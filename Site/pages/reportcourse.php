<div class="row">
    
        <h2>Relatório p/Curso e Etapa </h2>
        <hr>
<?php
	$mysqli->postsanitize();

	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY name;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM unit WHERE iscourse = 1 ORDER BY unit.name;",'courseid',$_POST['courseid'],'id','acronym');
	formselectsql($anytmp,"SELECT * FROM term ORDER BY term.name;",'termid',$_POST['termid'],'id','name');
	echo formsubmit('act','Refresh') . '<br>';
	
	formselectscenery('scen.acc.view');
	echo '</form>';
	
// semester, course, term
	if ($_POST['termid']) {
		echo '<p>';
//		$in = "'0'";
//		foreach ($_SESSION['sceneryselected'] as $scenid => $scenname) {
//			$in .= " , '".$scenid."'";
//		}

	$inselected = inscenery_sessionlst('sceneryselected');
	list($qscentbl,$qscensql) = scenery_sql($inselected);

	  $q = "SELECT DISTINCT `discipline`.`name` AS `discname` ,  `discipline`.`id` AS `discid` , `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `discdept`.`id` AS `discdeptid` " . 
	    "FROM  `classsegment` , `class`, `term`, `semester` , `coursedisciplines`,`unit`,`discipline` , `unit` AS `discdept` " . $qscentbl . " WHERE " .
		"`coursedisciplines`.`course_id` = `unit`.`id` AND `coursedisciplines`.`term_id` = `term`.`id` AND `coursedisciplines`.`discipline_id` = `discipline`.`id` AND " .
		"`discipline`.`dept_id` = `discdept`.`id` AND " .
		"`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " .
		"`classsegment`.`class_id` = `class`.`id` AND " .
		"`unit`.`id` = '".$_POST['courseid']."' AND `term`.`id` = '".$_POST['termid']."' AND " .
		"`semester`.`id` = '".$_POST['semid']."' " . $qscensql . " ORDER BY `discipline`.`name` , `class`.`name`";

	   dbweekmatrix($q,$inselected,$_POST['courseid'],$_POST['termid']);
    }

 ?>

    </div>
  


