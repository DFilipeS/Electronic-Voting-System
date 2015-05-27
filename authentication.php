<?php
set_include_path(get_include_path() . PATH_SEPARATOR . 'phpseclib');	
include('Crypt/RSA.php');

header('Content-Type: application/json');

$id = $_POST['voter_id'];

if (isset($_POST['token'])) {
    echo setToken($id, $_POST['token']);
} else {
    $publickey = getPublicKeyFromDB($id);
    if (!is_null($publickey)) {
        echo getChallenge($publickey);
    } else {
        echo getError();
    }   
}

function setToken($voterId, $token) {
    $dsn = "mysql:host=localhost;dbname=evs;charset=utf8";
    $opt = array(
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    );
    $pdo = new PDO($dsn, 'root', 'root', $opt);
    
    $query = $pdo->prepare("UPDATE authentication SET token = ? WHERE id = ?");
    $query->execute(array($token, $voterId));
    
    $res = new stdClass();
    if ($query->rowCount() != 1) {
        $res->error = "Token not set, something went wrong. Try again!";
        return json_encode($res);
    }
    
    return json_encode($res);
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
    
    return json_encode($res);
} 

function getError() {
    $res = new stdClass();
    $res->error = "Invalid voter id";
    
    return json_encode($res);
}
?>