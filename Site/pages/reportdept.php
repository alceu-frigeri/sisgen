

<div class="row">
        <h2>Relatório Depto. p/ Disciplina </h2>
        <hr>

<?php
	$thisform=$basepage.'?q=reports&sq=dept'; 
	
	$mysqli->postsanitize();

	formjavaprint(displaysqlitem('','unit',$_POST['deptid'],'acronym') . displaysqlitem(' - Encargos ','semester',$_POST['semid'],'name'));

	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM `semester` ORDER BY `name`;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM `unit`  WHERE (`isdept` = '1' AND `mark` = '1') OR (`iscourse` = '1') ORDER BY `isdept` DESC, `acronym` ASC;",'deptid',$_POST['deptid'],'id','acronym');
	echo formsubmit('act','Refresh') . '<br>';
	
	formselectscenery('scen.acc.view');
	
	echo '</form>';

	
	echo "<button onclick=\"printContent('Encargos')\">Print</button>";

	$inselected = inscenery_sessionlst('sceneryselected');
	list($qscentbl,$qscensql) = scenery_sql($inselected);


	$q="SELECT * FROM discipline WHERE discipline.dept_id = '".$_POST['deptid']."' ORDER BY  `name`;";

	$discsql = $mysqli->dbquery($q);
	
	echo '<hr><div id="Encargos">';
	
	echo '<h2>' . displaysqlitem('','unit',$_POST['deptid'],'acronym') . displaysqlitem(' - Encargos p/ ','semester',$_POST['semid'],'name') . '</h2>';
	while ($discrow = $discsql->fetch_assoc()) {
		echo '<br><b>'. spanformat('','darkblue',$discrow['code'].' -- '.$discrow['name']) .'</b><br>';
//  		list($qscentbl,$qscensql) = scenery_sql($in);

		$q = "SELECT DISTINCT class.*  FROM  `class` " . $qscentbl . "  WHERE `class`.`discipline_id` = '" . $discrow['id'] . "' AND " .  
		"`class`.`sem_id` = '" . $_POST['semid'] . "' " . $qscensql . 
		 " ORDER BY `class`.`name`" ;
		 
		$classsql = $mysqli->dbquery($q);
		while($classrow = $classsql->fetch_assoc()) {
			 echo 'Turma: ' . $classrow['name'];
			 if ($classrow['agreg']) {
				 echo spanformat('','darkorange',' (agregadora)');
			 } else {
				 if($classrow['partof']) {
					 $q="SELECT `name` FROM `class` WHERE `id` = '".$classrow['partof']."'";
					 $partsql=$mysqli->dbquery($q);
					 $partrow=$partsql->fetch_assoc();
					 echo spanformat('','darkorange',' (agregada à '.$partrow['name'].')');
				 }
			 }
			 
			 echo '<br>';
			 $q = "SELECT `seg`.* , `building`.`acronym` AS `buildingname` , `room`.`acronym` AS `roomname` , `room`.`capacity` AS `capacity` , `prof`.`nickname` , `prof`.`name`  FROM `classsegment` AS `seg` , `room` , `building`, `prof` WHERE " .
				"`seg`.`room_id` = `room`.`id` AND `room`.`building_id` = `building`.`id`  AND `seg`.`prof_id` = `prof`.`id` AND  `seg`.`class_id` = '" . $classrow['id'] . "';";
			 $segsql = $mysqli->dbquery($q);
			 while ($segrow = $segsql->fetch_assoc()) {
				 if ($segrow['length']>1) { $p='s'; } else { $p=''; };
				 echo '&nbsp;&nbsp;&nbsp;' . 
					spanformat('','gray',$_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 ' . $segrow['length'] . ' Hora'.$p.'-Aula') . 
					', ' . $segrow['name'] . ',  ' . 
					spanformat('','gray','Sala: ' . $segrow['roomname'] . ' (' . $segrow['buildingname'] . ')');
					if ($segrow['capacity']) {
						echo ' (cap.: ' . $segrow['capacity'] . ')';
					}
					echo '<br>'; 
			 }
			 $q="SELECT `vac`.* , `unit`.`acronym`, `kind`.`code` AS `disckind` FROM `vacancies` AS `vac`,`unit` , `coursedisciplines` AS `grade` , `disciplinekind` AS `kind` WHERE " .
				"`vac`.`course_id` = `unit`.`id` AND `vac`.`course_id` = `grade`.`course_id` AND `grade`.`disciplinekind_id` = `kind`.`id` AND " .
				"`grade`.`discipline_id` = '" . $discrow['id'] . "' AND `vac`.`class_id` = '" . $classrow['id'] . "' ORDER BY `unit`.`acronym`";
			 $vacsql = $mysqli->dbquery($q);
			 while ($vacrow = $vacsql->fetch_assoc()) {
				 if ($vacrow['givennum']==1) { $p=''; } else { $p='s'; };			 
				 echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $vacrow['acronym'] . ' : ' . $vacrow['givennum'] . ' Vaga'.$p. ' ('.$vacrow['disckind'] .')<br>';
			 }
			 
		}
	}
	echo '</div>';
			
 ?>
    
 
</div>