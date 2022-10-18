<?php

    include 'coreX.php';
	
if($_SESSION['role']['isadmin'] & $sisgensetup) {
	echo '<h3>Inserting Courses Curricula</h3>';
	include 'gradesB.csv';
	echo '<p>';
	echo '<h3>Inserting Classes</h3>';
	include 'semestresB.csv';
	echo '<p>';


	echo '<h3>Fixing Rooms Info</h3>';
	$q = "UPDATE `semester` SET `readonly` = '1';";
	$mysqli->dbquery($q);
	$q = "SELECT `id` FROM `building` WHERE `acronym` = 'Eletro';";
	$result = $mysqli->dbquery($q);
	$sqlrow=$result->fetch_assoc();

	roomset('2','18',$sqlrow['id'],'200');
	roomset('2','18',$sqlrow['id'],'201');
	roomset('2','18',$sqlrow['id'],'202');
	roomset('2','20',$sqlrow['id'],'110');
	roomset('1','40',$sqlrow['id'],'303');
	roomset('1','40',$sqlrow['id'],'304');
	roomset('3','20',$sqlrow['id'],'305');
	roomset('3','20',$sqlrow['id'],'301');
	roomset('1','60',$sqlrow['id'],'306');
	
	include 'DBfixvac.php';
		
}

?>


