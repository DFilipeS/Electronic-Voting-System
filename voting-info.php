<?php
	header('Content-Type: application/json');

	$voting_info = new stdClass();
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
		$parties[$row['hash_id']] = $row['name'];
	}

	$voting_info->nparties = sizeof($parties);
	$voting_info->parties = $parties;

	print json_encode($voting_info);
?>
