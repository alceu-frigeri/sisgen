<div class="row">
        <h2>Verificação Professores p/departamento </h2>
        <hr>

<?php 

	$thisform=$basepage.'?q=check&sq=prof'; 

	$mysqli->postsanitize();

	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY semester.name;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM unit WHERE isdept = 1 AND mark = 1 ORDER BY unit.name;",'deptid',$_POST['deptid'],'id','acronym');
	echo formsubmit('act','Check') . '<br>';
	
	formselectscenery('scen.acc.view');	
	echo '</form>';

	
//	$in = "'0'";
//	foreach ($_SESSION['sceneryselected'] as $scenid => $scenname) {
//		$in .= " , '".$scenid."'";
//	}
//	list($qscentbl,$qscensql) = scenery_sql($in);

	$inselected = inscenery_sessionlst('sceneryselected');
	list($qscentbl,$qscensql) = scenery_sql($inselected);


	$q = "SELECT prof.* FROM prof,unit WHERE prof.dept_id = unit.id AND unit.id = '".$_POST['deptid']."' ORDER BY prof.name;";
	$profsql = $mysqli->dbquery($q);
	while ($profrow = $profsql->fetch_assoc()) {
	   echo '<br>';	
	   $q = "SELECT DISTINCT `discipline`.`name` AS `discname`, `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* " . 
		 " FROM `classsegment` , `class`, `semester`,`unit`,`discipline`,`prof` " . $qscentbl . " WHERE " . 
		 "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " . 
		 "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`prof_id` = `prof`.`id` AND " . 
		 "`unit`.`id` = '".$_POST['deptid']."' AND `semester`.`id` = '".$_POST['semid']."' AND " . 
		 "`prof`.`id` = '".$profrow['id'] . "' " . $qscensql . 
		 " ORDER BY `class`.`name`"
		 ;
		
	  echo  formpost($basepage.'?q=reports&sq=prof','profhid'.$_POST['deptid'].'-'.$profrow['id'],'profhid'.$_POST['deptid'].'-'.$profrow['id']) . //'profhid'.$_POST['deptid'].'-'.$profrow['id']
		formhiddenval('semid',$_POST['semid']) . formhiddenval('deptid',$_POST['deptid']) . 
		formhiddenval('profid',$profrow['id']) . formhiddenval('act',$_POST['Refresh']) . 
		formsubmit('submit','go report') . spanformat('larger','',$profrow['name']) . '</form>'  ;
	  $flag =  checkweek($q);
	  if($flag['disc']) {echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Possível colisão de disciplina<br>';}
  	  if($flag['class']) {echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Possível colisão de turma<br>';}

	}
  

 ?>
    
 
</div>

