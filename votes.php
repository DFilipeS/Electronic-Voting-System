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

?>
