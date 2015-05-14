<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');	
include('Crypt/RSA.php');

header('Content-Type: application/json');

$id = $_POST['voter_id'];
$publickey = getPublicKeyFromDB($id);

if (!is_null($publickey)) {
    echo getChallenge($publickey);
} else {
    echo getError();
}


function getPublicKeyFromDB($voterid) {
    $db = new mysqli('localhost', 'root', 'root', 'evs');

    if ($db->connect_errno > 0){
        die('Unable to connect to database [' . $db->connect_error . ']');
    }
    
    $statement = $db->prepare("SELECT `public_key` FROM `authentication` WHERE `id` = ?");
    
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

function getChallenge($key) {
    $rsa = new Crypt_RSA();
    $rsa->loadKey($key); // public key
    
    $plaintext = uniqid('', true);
    
    $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_PKCS1);
    $ciphertext = $rsa->encrypt($plaintext);
    
    $res = new stdClass();
    $res->secret = base64_encode($ciphertext);
    $res->cleartext = $plaintext;
    $res->privKey = file_get_contents('keys/key.priv');
    
    return json_encode($res);
} 

function getError() {
    $res = new stdClass();
    $res->error = "Invalid voter id";
    
    return json_encode($res);
}
?>