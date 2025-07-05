

<div class="row">
        <h2>Relatório de Ocupação p/ Disciplina </h2>
        <hr>

<?php
	$thisform=$GBLbasepage.'?q=reports&sq=enrollmentB'; 
	
	$GBLmysqli->postsanitize();

	formjavaprint(displaysqlitem('','unit',$_POST['deptid'],'acronym') . displaysqlitem(' - Encargos ','semester',$_POST['semid'],'name'));

	echo formpost($thisform);
//	formselectsql($anytmp,"SELECT * FROM `semester` ORDER BY `name`;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM `unit`  WHERE (`isdept` = '1' AND `mark` = '1') OR (`iscourse` = '1') ORDER BY `isdept` DESC, `acronym` ASC;",'deptid',$_POST['deptid'],'id','acronym');
	echo formsubmit('act','Refresh') . '<br>';
	
	formselectscenery('scen.acc.view');
	
	echo '</form>';

	
	echo "<button onclick=\"printContent('Encargos')\">Print</button>";

	$inselected = inscenery_sessionlst('sceneryselected');
	list($qscentbl,$qscensql) = scenery_sql($inselected);


	$q="SELECT * FROM discipline WHERE discipline.dept_id = '".$_POST['deptid']."' ORDER BY  `name`;";

	$discsql = $GBLmysqli->dbquery($q);
	
	echo '<hr><div id="Encargos">';
	
	echo '<h2>' . displaysqlitem('','unit',$_POST['deptid'],'acronym') . ' - Ocupação  </h2>';
	$output = fopen('php://output', 'w');
	fputcsv($output,array('Código','Disciplina','Semestre','Turma','Curso','Ofertadas','Ocupadas','Tipo'));
	echo '<br>';
	while ($discrow = $discsql->fetch_assoc()) {
//		echo '<br><b>'. spanformat('','darkblue',$discrow['code'].' -- '.$discrow['name']) .'</b><br>';
//  		list($qscentbl,$qscensql) = scenery_sql($in);

		$q = "SELECT DISTINCT class.* , `semester`.`name` as `sem_name` FROM  `class` , `semester` " . $qscentbl . "    WHERE `class`.`discipline_id` = '" . $discrow['id'] . "' AND " .  
		"`class`.`sem_id` = `semester`.`id` " . $qscensql . 
		 " ORDER BY `semester`.`name` ASC, `class`.`name` ASC; " ;
		 
		 //vardebug($q);
		$classsql = $GBLmysqli->dbquery($q);
		while($classrow = $classsql->fetch_assoc()) {
	//		 echo 'Turma: ' . $classrow['name'] . ' - ' . $classrow['sem_name'];
			 if ($classrow['agreg']) {
				 echo spanformat('','darkorange',' (agregadora)');
			 } else {
				 if($classrow['partof']) {
					 $q="SELECT `name` FROM `class` WHERE `id` = '".$classrow['partof']."'";
					 $partsql=$GBLmysqli->dbquery($q);
					 $partrow=$partsql->fetch_assoc();
					 echo spanformat('','darkorange',' (agregada à '.$partrow['name'].')');
				 }
			 }
			 
//			 echo '<br>';
//			 $q = "SELECT `seg`.* , `prof`.`nickname` , `prof`.`name`  FROM `classsegment` AS `seg` , `prof` WHERE " .
//				" `seg`.`prof_id` = `prof`.`id` AND  `seg`.`class_id` = '" . $classrow['id'] . "';";
//			 $segsql = $GBLmysqli->dbquery($q);
//			 while ($segrow = $segsql->fetch_assoc()) {
//				 if ($segrow['length']>1) { $p='s'; } else { $p=''; };
//				 echo '&nbsp;&nbsp;&nbsp;' . 
//					spanformat('','gray',$_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 ' . $segrow['length'] . ' Hora'.$p.'-Aula') . 
//					', ' . $segrow['name'] ;
//					echo '<br>'; 
//			 }
			 $q="SELECT `vac`.* , `unit`.`acronym`, `kind`.`code` AS `disckind` FROM `vacancies` AS `vac`,`unit` , `coursedisciplines` AS `grade` , `disciplinekind` AS `kind` WHERE " .
				"`vac`.`course_id` = `unit`.`id` AND `vac`.`course_id` = `grade`.`course_id` AND `grade`.`disciplinekind_id` = `kind`.`id` AND " .
				"`grade`.`discipline_id` = '" . $discrow['id'] . "' AND `vac`.`class_id` = '" . $classrow['id'] . "' ORDER BY `unit`.`acronym`";
			 $vacsql = $GBLmysqli->dbquery($q);
			 while ($vacrow = $vacsql->fetch_assoc()) {
			//	 if ($vacrow['givennum']==1) { $p=''; } else { $p='s'; };			 
//				 echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $vacrow['acronym'] . ' : ' . $vacrow['usednum'] . ' (' .$vacrow['givennum'] . ') Vaga'.$p. ' ('.$vacrow['disckind'] .')<br>';
				 //echo $discrow['code'] . ' , ' . $discrow['name'] . ' , ' . $classrow['sem_name'] . ' , ' . $classrow['name'] . ' , ' . $vacrow['acronym'] . ' , ' . $vacrow['givennum'] . ' , ' . $vacrow['usednum'] . ' , ' . $vacrow['disckind'] . '<br>';
				 fputcsv($output,array($discrow['code'] , $discrow['name'] , $classrow['sem_name'] , $classrow['name'] , $vacrow['acronym'] , $vacrow['givennum'] , $vacrow['usednum'] , $vacrow['disckind']));
				 	echo '<br>';

			 }
			 
		}
	}
	fclose($output);
	echo '</div>';
			
 ?>
    
 
</div>