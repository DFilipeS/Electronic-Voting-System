<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');	
include('Crypt/RSA.php');

header('Content-Type: application/json');

// Vem do AJAX
$id = 1;

$db = new mysqli('localhost', 'root', 'root', 'evs');

if ($db->connect_errno > 0){
    die('Unable to connect to database [' . $db->connect_error . ']');
}

$statement = $db->prepare("SELECT `public_key` FROM `authentication` WHERE `id` = ?");

$statement->bind_param('i', $id);
$statement->execute();
$statement->bind_result($returned_name);

while ($statement->fetch()) {
    $publickey = $returned_name;
    break;
}

$statement->free_result();

$rsa = new Crypt_RSA();
$rsa->loadKey($publickey); // public key

$plaintext = uniqid('', true);

$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
$ciphertext = $rsa->encrypt($plaintext);

$res = new stdClass();
$res->secret = base64_encode($ciphertext);
$res->cleartext = $plaintext;
$res->privKey = file_get_contents('keys/key.priv');

echo json_encode($res);
?>