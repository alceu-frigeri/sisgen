
<?php $thisform=$basepage.'?q=reports&sq=comgrad'; ?>

<div class="row">
        <h2>Relatório COMGRAD por Departamento </h2>
        <hr>

<?php

	echo formpost($thisform);
	formselectsqlX($anytmp,"SELECT * FROM semester ORDER BY semester.name;",'semid',$_POST['semid'],'id','name');
	
	echo 'Curso:'; formselectsqlX($anytmp,"SELECT * FROM unit WHERE `iscourse` = '1' ORDER BY unit.acronym;",'courseid',$_POST['courseid'],'id','acronym');
	
    $q = "SELECT DISTINCT `disc`.`dept_id` , `unit`.`acronym` , `unit`.`name`  FROM `coursedisciplines` AS `grade` , `discipline` AS `disc` , `unit` " . 
	"WHERE `grade`.`discipline_id` = `disc`.`id` AND `disc`.`dept_id` = `unit`.`id` AND " .
	"`grade`.`course_id` = '". $_POST['courseid']."' ORDER BY `unit`.`acronym`";

    echo '&nbsp;&nbsp;&nbsp;Dept.:';
	formselectsqlX($anytmp,$q,'deptid',$_POST['deptid'],'dept_id','acronym');
	
	echo formsubmit('act','Refresh');
	echo '</form>';


//	$result = $mysqli->dbquery($q);
//	while ($sqlrow = $result->fetch_assoc()) {
	    $q = "SELECT acronym,name FROM unit WHERE id='".$_POST['deptid']."'";
		$result=$mysqli->dbquery($q);
		$sqlrow=$result->fetch_assoc();
		if($sqlrow['acronym']) {
			echo '<p><hr><h4>'.$sqlrow['acronym'] . ' -- ' . $sqlrow['name'] . '</h4>';
		}
		$q = "SELECT * FROM `term` ORDER BY `id`";
		$termsql = $mysqli->dbquery($q);
		while ($termrow = $termsql->fetch_assoc()) {
			$q = "SELECT discipline.* FROM coursedisciplines , discipline WHERE coursedisciplines.course_id = '".$_POST['courseid']."' AND coursedisciplines.discipline_id = discipline.id AND discipline.dept_id = '".$_POST['deptid']."' AND `coursedisciplines`.`term_id` = '".$termrow['id']."' ORDER BY  discipline.name ";
			$discsql = $mysqli->dbquery($q);
			if($discsql->num_rows) {
				echo '<hr><b>'.$termrow['name'].'</b><br>';
			}
			while ($discrow = $discsql->fetch_assoc()) {
				echo '<br><b>'. spanformat('color:darkblue;',$discrow['code'].' -- '.$discrow['name']) .'</b><br>';
				$q = "SELECT class.* , `vac`.`askednum` FROM  `class` , `vacancies` AS `vac` WHERE `class`.`discipline_id` = '" . $discrow['id'] . "' AND " .
				"`class`.`sem_id` = '" . $_POST['semid'] . "' AND `vac`.`class_id` = `class`.`id` AND `vac`.`askednum` > '0' AND `vac`.`course_id` = '" . $_POST['courseid'] . "'";
				$classsql = $mysqli->dbquery($q);
				while($classrow = $classsql->fetch_assoc()) {
					if ($classrow['askednum']>1) { $p='s'; } else { $p=''; };
					 echo 'Turma: ' . $classrow['name'] . ' ('. $classrow['askednum'] . ' vaga'.$p.')<br>';
					 $q = "SELECT * FROM `classsegment` AS `seg` WHERE `seg`.`class_id` = '" . $classrow['id'] . "';";
					 $segsql = $mysqli->dbquery($q);
					 while ($segrow = $segsql->fetch_assoc()) {
						 if ($segrow['length']>1) { $p='s'; } else { $p=''; };
						 echo '&nbsp;&nbsp;&nbsp;' . spanformat('color:gray;',$_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 ' . $segrow['length'] . ' Hora'.$p.'-Aula<br>'); 
					 }
				}
			}
			
		}
		
		
		
//	}
 ?>
    
 
</div>