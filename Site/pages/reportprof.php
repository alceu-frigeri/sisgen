<div class="row">
        <h2>Relatório p/Departamento </h2>
        <hr>

<?php 
	echo formpost($thisform);
	formselectsqlX($anytmp,"SELECT * FROM semester ORDER BY semester.name;",'semid',$_POST['semid'],'id','name');
	formselectsqlX($anytmp,"SELECT * FROM unit WHERE isdept = 1 AND mark = 1 ORDER BY unit.name;",'deptid',$_POST['deptid'],'id','acronym');
	formselectsqlX($anytmp,"SELECT prof.* FROM prof,unit WHERE prof.dept_id = unit.id AND unit.id = '".$_POST['deptid']."' ORDER BY prof.name;",'profid',$_POST['profid'],'id','name');
	echo formsubmit('act','Refresh');
	echo '</form>';
   
   if ($_POST['profid']) {
	   		echo '<p>';
	   $q = "SELECT `discipline`.`name` AS `discname`, `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* FROM `classsegment` , `class`, `semester`,`unit`,`discipline`,`prof` WHERE " . 
		 "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " . 
		 "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`prof_id` = `prof`.`id` AND " . 
		 "`unit`.`id` = '".$_POST['deptid']."' AND `semester`.`id` = '".$_POST['semid']."' AND " . 
		 "`prof`.`id` = '".$_POST['profid']."'";

	   dbweekmatrix($q);
   
   }

 ?>
    
 
</div>

