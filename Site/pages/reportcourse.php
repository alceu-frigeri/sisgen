<div class="row">
    
        <h2>Relatório p/Curso e Etapa </h2>
        <hr>
<?php

	echo formpost($thisform);
	formselectsqlX($anytmp,"SELECT * FROM semester ORDER BY name;",'semid',$_POST['semid'],'id','name');
	formselectsqlX($anytmp,"SELECT * FROM unit WHERE iscourse = 1 ORDER BY unit.name;",'courseid',$_POST['courseid'],'id','acronym');
	formselectsqlX($anytmp,"SELECT * FROM term ORDER BY term.name;",'termid',$_POST['termid'],'id','name');
	echo formsubmit('act','Refresh');
	echo '</form>';
	
// semester, course, term
	if ($_POST['termid']) {
		echo '<p>';
	  $q = "SELECT `discipline`.`name` AS `discname`, `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* FROM   `classsegment` , `class`, `term`, `semester` , `coursedisciplines`,`unit`,`discipline` WHERE " .
		"`coursedisciplines`.`course_id` = `unit`.`id` AND `coursedisciplines`.`term_id` = `term`.`id` AND `coursedisciplines`.`discipline_id` = `discipline`.`id` AND " .
		"`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " .
		"`classsegment`.`class_id` = `class`.`id` AND " .
		"`unit`.`id` = '".$_POST['courseid']."' AND `term`.`id` = '".$_POST['termid']."' AND " .
		"`semester`.`id` = '".$_POST['semid']."'";

	   dbweekmatrix($q,$_POST['courseid']);
    }

 ?>

    </div>
  


