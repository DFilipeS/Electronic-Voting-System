<?php

if (isset($_POST['token']) && isset($_POST['votes'])) {
    $token = $_POST['token'];
    $sets = $_POST['votes'];

    if (checkSetsSize($sets)) {
        $chosenSet = rand(0, sizeof($sets)-1);

        $ser = base64_encode(serialize($sets));

        $dsn = "mysql:host=localhost;dbname=evs;charset=utf8";
        $opt = array(
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        );
        $pdo = new PDO($dsn, 'root', 'root', $opt);

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

    $dsn = "mysql:host=localhost;dbname=evs;charset=utf8";
    $opt = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    $pdo = new PDO($dsn, 'root', 'root', $opt);

    $query = $pdo->prepare("SELECT * FROM sets WHERE token LIKE ?");
    $query->execute(array($token));
    $result = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($query->rowCount() == 1) {
        foreach ($result as $row) {
            $hashedVotesSets = unserialize(base64_decode($row['data']));
            if (verifySetsIntegrity($votesSets, $hashedVotesSets, $passSets)) {
                echo 'OK';
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
    $parties = array();

	$dsn = "mysql:host=localhost;dbname=evs;charset=utf8";
    $opt = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    $pdo = new PDO($dsn, 'root', 'root', $opt);

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
