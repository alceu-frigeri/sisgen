
<?php
	if($_SESSION['role']['isadmin'] & $sisgensetup) {
		echo "<h3>Fixing Vacancies...</h3>";
		$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) " .
			"SELECT `class`.`id` , `cdisc`.`course_id`, '0' , '0'  FROM `coursedisciplines` AS `cdisc`,`class` " . 
				"where `cdisc`.`discipline_id` = `class`.`discipline_id` AND " .
					"NOT EXISTS (SELECT * FROM `vacancies` AS `vac` WHERE `vac`.`class_id` = `class`.`id` AND `vac`.`course_id` = `cdisc`.`course_id`); ";
		$mysqli->dbquery($q);
		echo "done<br>";
	}
 ?>

