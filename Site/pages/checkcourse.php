<div class = "row">
    
        <h2>Verificação Curso p/semestre</h2>
        <hr>
<?php
 $thisform = $GBLbasepage . '?q=check&sq=course';

	$GBLmysqli->postsanitize();

	echo formpost($thisform);
        formretainvalues(array('semid' , 'courseid'));

	formselectsql($anytmp , "SELECT * FROM semester ORDER BY name DESC;" , 'semid' , $_POST['semid'] , 'id' , 'name');
	formselectsql($anytmp , "SELECT * FROM unit WHERE iscourse = 1 ORDER BY unit . name;" , 'courseid' , $_POST['courseid'] , 'id' , 'acronym');
	echo  '<br>';
	
	formsceneryselect();

	echo '</form>';
	
	$inselected = inscenery_sessionlst('sceneryselected');
	list($qscentbl , $qscensql) = scenery_sql($inselected);

	
// semester, course, term
    $q = "SELECT * FROM `term` ORDER BY `id`;";
	$termsql = $GBLmysqli->dbquery($q);
	
	while ($termrow = $termsql->fetch_assoc()) {
	  $q = "SELECT DISTINCT `discipline` . `name` AS `discname` , `discipline` . * , `class` . `id` AS `classid` , `class` . * , `classsegment` . * , `disciplinekind` . `code` AS `disckind` " . 
		"FROM   `classsegment` , `class` , `term` , `semester` , `coursedisciplines` , `unit` , `discipline` , `disciplinekind` " . $qscentbl . " WHERE "  . 
		"`coursedisciplines` . `course_id` = `unit` . `id` AND `coursedisciplines` . `term_id` = `term` . `id` AND `coursedisciplines` . `discipline_id` = `discipline` . `id` AND " . 
		"`coursedisciplines` . `disciplinekind_id` = `disciplinekind` . `id` AND "  . 
		"`class` . `discipline_id` = `discipline` . `id` AND `class` . `sem_id` = `semester` . `id` AND "  . 
		"`classsegment` . `class_id` = `class` . `id` AND "  . 
		"`unit` . `id` = '" . $_POST['courseid'] . "' AND `term` . `id` = '" . $termrow['id'] . "' AND "  . 
		"`semester` . `id` = '" . $_POST['semid'] . "' " . $qscensql . 
		 " ORDER BY `class` . `name`"
		 ;

  
  	  echo '<br>';
	  echo hiddencourseform($_POST['semid'] , $_POST['courseid'] , $termrow['id'] , '') . formsubmit('submit' , 'go report') . spanformat('larger' , '' , $termrow['name']) . '</form>'  ;

	  
	  $flag =  checkweek($q , null , $_POST['courseid'] , $termrow['id']);
	  if($flag['disc']) {echo $GBL_Qspc . 'Possível colisão de disciplina<br>';}
  	  if($flag['class']) {echo $GBL_Qspc . 'Possível colisão de turma<br>';}
  	  if($flag['ob']) {echo $GBL_Qspc . 'Disciplina ob/al não ofertada<br>';}

//	   dbweekmatrix($q , $inselected , $_POST['courseid']);
	}

 ?>

    </div>
  


