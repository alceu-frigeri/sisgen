<div class="row">
    
        <h2>Verificação Curso p/semestre</h2>
        <hr>
<?php
 $thisform=$basepage.'?q=check&sq=course';

	$mysqli->postsanitize();

	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY name;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM unit WHERE iscourse = 1 ORDER BY unit.name;",'courseid',$_POST['courseid'],'id','acronym');
	echo formsubmit('act','Check');
	echo '</form>';
	
// semester, course, term
    $q = "SELECT * FROM `term` ORDER BY `id`;";
	$termsql = $mysqli->dbquery($q);
	while ($termrow = $termsql->fetch_assoc()) {
	  $q = "SELECT `discipline`.`name` AS `discname`, `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `disciplinekind`.`code` AS `disckind` " . 
		"FROM   `classsegment` , `class`, `term`, `semester` , `coursedisciplines`,`unit`,`discipline` , `disciplinekind` WHERE " .
		"`coursedisciplines`.`course_id` = `unit`.`id` AND `coursedisciplines`.`term_id` = `term`.`id` AND `coursedisciplines`.`discipline_id` = `discipline`.`id` AND " .
		"`coursedisciplines`.`disciplinekind_id` = `disciplinekind`.`id` AND " .
		"`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " .
		"`classsegment`.`class_id` = `class`.`id` AND " .
		"`unit`.`id` = '".$_POST['courseid']."' AND `term`.`id` = '".$termrow['id']."' AND " .
		"`semester`.`id` = '".$_POST['semid']."'";

  
  	  echo '<br>';
	  echo  formpost($basepage.'?q=reports&sq=course','_blank') . 
		formhiddenval('semid',$_POST['semid']) . formhiddenval('courseid',$_POST['courseid']) . 
		formhiddenval('termid',$termrow['id']) . formhiddenval('act',$_POST['Refresh']) . 
		formsubmit('submit','go report') . spanformat('larger','',$termrow['name']) . '</form>'  ;

	  
	  $flag =  checkweek($q,$_POST['courseid'],$termrow['id']);
	  if($flag['disc']) {echo 'Possível colisão de disciplina<br>';}
  	  if($flag['class']) {echo 'Possível colisão de turma<br>';}
  	  if($flag['ob']) {echo 'Disciplina ob/al não ofertada<br>';}

//	   dbweekmatrix($q,$_POST['courseid']);
	}

 ?>

    </div>
  


