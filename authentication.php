<?php

/*************************** Main ****************************/

set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');
include('Crypt/RSA.php');
header('Content-Type: application/json');

$id = $_POST['voter_id'];

$publickey = getPublicKeyFromDB($id);
if (!is_null($publickey)) {
    echo getChallenge($id, $publickey);
} else {
    echo getError();
}

/************************* Functions *************************/

function getPublicKeyFromDB($voterid) {
    include('db-config.php');
    $db = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

    if ($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }

    $statement = $db->prepare("SELECT `public_key` FROM `authentication` WHERE `id` = ? AND `token` = 0");

    $statement->bind_param('i', $voterid);
    $statement->execute();
    $statement->bind_result($returned_name);

    while ($statement->fetch()) {
        $key = $returned_name;
        break;
    }

    $statement->free_result();

    if (isset($key)) {
        return $key;
    } else {
        return null;
    }
}

function getChallenge($voterId, $key) {
    $rsa = new Crypt_RSA();
    $rsa->loadKey($key); // public key

    $plaintext = uniqid('', true);
    setToken($voterId, $plaintext);

    $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    $ciphertext = $rsa->encrypt($plaintext);

    $res = new stdClass();
    $res->secret = base64_encode($ciphertext);

    return json_encode($res);
}

function getError() {
    $res = new stdClass();
    $res->error = "Invalid voter id";

    return json_encode($res);
}

function setToken($voterId, $token) {
    include('db-config.php');
    $dsn = "mysql:host=" . $dbhost . ";dbname=" . $dbname . ";charset=utf8";
    $opt = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    $pdo = new PDO($dsn, $dbuser, $dbpass, $opt);

    $query = $pdo->prepare("UPDATE authentication SET token = 1 WHERE id = ?");
    $query->execute(array($voterId));

    $query = $pdo->prepare("INSERT INTO sets (token, data, chosen) VALUES (?, ?, ?)");
    $query->execute(array($token, NULL, NULL));

    $res = new stdClass();
    if ($query->rowCount() != 1) {
        $res->error = "Token not set, something went wrong. Try again!";
        return json_encode($res);
    }

    return json_encode($res);
}
?>
