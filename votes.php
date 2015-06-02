<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include('Crypt/RSA.php');
include ('db-config.php');

if (isset($_POST['token']) && isset($_POST['votes'])) {
    $token = $_POST['token'];
    $sets = $_POST['votes'];

    if (checkSetsSize($sets)) {
        $chosenSet = rand(0, sizeof($sets)-1);

        $ser = base64_encode(serialize($sets));

        $dsn = "mysql:host=" . $dbhost . ";dbname=" . $dbname . ";charset=utf8";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $pdo = new PDO($dsn, $dbuser, $dbpass, $opt);

        $query = $pdo->prepare("UPDATE sets SET data=?, chosen=? WHERE token LIKE ?");
        $query->execute(array($ser, $chosenSet, $token));

        $res = new stdClass();
        $res->set = $chosenSet;
        echo json_encode($res);
    } else {
        $res = new stdClass();
        $res->error = "ERROR: Invalid set size.";
        echo json_encode($res);
    }
} else if (isset($_POST['token']) && isset($_POST['votesSets']) && isset($_POST['passSets'])) {
    $token = $_POST['token'];
    $votesSets = $_POST['votesSets'];
    $passSets = $_POST['passSets'];

    $dsn = "mysql:host=" . $dbhost . ";dbname=" . $dbname . ";charset=utf8";
    $opt = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    $pdo = new PDO($dsn, $dbuser, $dbpass, $opt);

    $query = $pdo->prepare("SELECT * FROM sets WHERE token LIKE ?");
    $query->execute(array($token));
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($query->rowCount() == 1) {
        foreach ($result as $row) {
            $hashedVotesSets = unserialize(base64_decode($row['data']));
            if (verifySetsIntegrity($votesSets, $hashedVotesSets, $passSets)) {
                $signatures = array();

                $rsa = new Crypt_RSA();
                $rsa->loadKey(file_get_contents('keys/ce.priv'));
                $rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);

                foreach ($hashedVotesSets[$row['chosen']] as $voteHash) {
                    $signatures[] = base64_encode($rsa->sign($voteHash));
                }

                $res = new stdClass();
                $res->ok = "INFO: Votes integrity is ok.";
                $res->signatures = $signatures;

                echo json_encode($res);
            } else {
                $res = new stdClass();
                $res->error = "ERROR: Something went wrong, refresh and try again.";
                echo json_encode($res);
            }
        }
    } else {
        $res = new stdClass();
        $res->error = "ERROR: Something went wrong, refresh and try again.";
        echo json_encode($res);
    }
} else if (isset($_POST['vote'])) {
    $vote = $_POST['vote'];
    $rsa = new Crypt_RSA();
    $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    $rsa->loadKey(file_get_contents('keys/ce.priv'));

    foreach ($vote as &$part) {
        $part = $rsa->decrypt(base64_decode($part));
        $part = rtrim($part, "\0");
    }

    $vote = implode("", $vote);
    $vote = str_replace('""', "", $vote);
    $vote = str_replace('\\', "", $vote);
    $vote = substr($vote, 1, -1);
    $vote = json_decode($vote);

    $dsn = "mysql:host=" . $dbhost . ";dbname=" . $dbname . ";charset=utf8";
    $opt = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    $pdo = new PDO($dsn, $dbuser, $dbpass, $opt);

    $query = $pdo->prepare("INSERT INTO votes (token, vote_id, party_hash) VALUES (?, ?, ?)");
    $query->execute(array($vote->token, $vote->vote->id, $vote->vote->party));

    $res = new stdClass();
    if ($query->rowCount() != 1) {
        $res->error = "Something went wrong. Try again!";
        return json_encode($res);
    }

    echo json_encode($res);
}

function checkSetsSize($sets) {
    if (sizeof($sets) > 0) {
        $size = sizeof($sets[0]);

        foreach ($sets as $set) {
            if (sizeof($set) != $size) {
                return false;
            }
        }

        return true;
    }

    return false;
}

function verifySetsIntegrity($sets, $hashedSets, $passwords) {
    include('db-config.php');
    $parties = array();

    $dsn = "mysql:host=" . $dbhost . ";dbname=" . $dbname . ";charset=utf8";
    $opt = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    $pdo = new PDO($dsn, $dbuser, $dbpass, $opt);

    $query = $pdo->prepare("SELECT * FROM parties");
    $query->execute();
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

	foreach ($result as $row) {
		$parties[] = $row['hash_id'];
	}

    for ($j = 0; $j < sizeof($sets); $j++) {
        for ($i = 0; $i < sizeof($parties); $i++) {
            if (isset($sets[$j])) {
                if ($sets[$j][$i]['party'] != $parties[$i]) {
                    return false;
                }

                $storedVoteHash = hash_hmac('md5', json_encode($sets[$j][$i]), $passwords[$j][$i]);
                if ($storedVoteHash != $hashedSets[$j][$i]) {
                    return false;
                }
            }
        }
    }

    return true;
}
?>
