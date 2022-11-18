<div class="row">
        <h2>Relatório p/Departamento </h2>
        <hr>

<?php 
	$mysqli->postsanitize();

	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY semester.name;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM unit WHERE isdept = 1 AND mark = 1 ORDER BY unit.name;",'deptid',$_POST['deptid'],'id','acronym');
	formselectsql($anytmp,"SELECT prof.* FROM prof,unit WHERE prof.dept_id = unit.id AND unit.id = '".$_POST['deptid']."' ORDER BY prof.name;",'profid',$_POST['profid'],'id','name');
	echo formsubmit('act','Refresh');
	echo '</form>';
   
   if ($_POST['profid']) {
	   		echo '<p>';
	   $q = "SELECT `discipline`.`name` AS `discname` ,  `discipline`.`id` AS `discid` , `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `discdept`.`id` AS `discdeptid`" .
		 " FROM `classsegment` , `class`, `semester`,`unit`,`discipline`,`prof` , `unit` AS `discdept` WHERE " . 
		 "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " . 
		 "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`prof_id` = `prof`.`id` AND " .
		 "`discipline`.`dept_id` = `discdept`.`id` AND " .
		 "`unit`.`id` = '".$_POST['deptid']."' AND `semester`.`id` = '".$_POST['semid']."' AND " . 
		 "`prof`.`id` = '".$_POST['profid']."'";

//vardebug($q);

	   dbweekmatrix($q);
   
   }

 ?>
    
 
</div>

