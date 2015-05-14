<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');	
include('Crypt/RSA.php');

header('Content-Type: application/json');

$publickey = file_get_contents('keys/key.pub');
$privatekey = file_get_contents('keys/key.priv');

$rsa = new Crypt_RSA();
$rsa->loadKey($publickey); // public key

$plaintext = 'Hello!';

$rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
$ciphertext = $rsa->encrypt($plaintext);

$rsa->loadKey($privatekey); // private key
//echo $rsa->decrypt($ciphertext);

// Assinatura

$rsa->loadKey($privatekey); // private key

$rsa->setSignatureMode(CRYPT_RSA_SIGNATURE_PKCS1);
$signature = $rsa->sign($plaintext);

$rsa->loadKey($publickey); // public key
//echo $rsa->verify($plaintext, $signature) ? 'verified' : 'unverified';

$obj = new stdClass();
$obj->verification = $rsa->verify($plaintext, $signature) ? 'verified' : 'unverified';
$obj->message = $plaintext;
echo json_encode($obj);
?>