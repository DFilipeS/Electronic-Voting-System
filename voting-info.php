<?php
	header('Content-Type: application/json');
	
	$voting_info = new stdClass();
	
	$parties = array();
	$parties[md5('partido0_ID')] = 'Partido 0';
	$parties[md5('partido1_ID')] = 'Partido 1';
	$parties[md5('partido2_ID')] = 'Partido 2';
	$parties[md5('partido3_ID')] = 'Partido 3';
	
	$voting_info->nparties = sizeof($parties);
	$voting_info->parties = $parties;
	
	print json_encode($voting_info);
?>