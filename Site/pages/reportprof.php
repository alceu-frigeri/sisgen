<div class="row">
        <h2>Relatório p/ Prof. </h2>
        <hr>

<?php 
	$GBLmysqli->postsanitize();

	echo formpost($thisform);
        formretainvalues(array('semid','deptid','profid'));
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY semester.name DESC;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM unit WHERE isdept = 1 AND mark = 1 ORDER BY unit.name;",'deptid',$_POST['deptid'],'id','acronym');
	formselectsql($anytmp,"SELECT prof.* FROM prof,unit,profkind WHERE prof.dept_id = unit.id AND prof.profkind_id = profkind.id AND profkind.acronym <> '-none-' AND unit.id = '".$_POST['deptid']."' ORDER BY prof.name;",'profid',$_POST['profid'],'id','name');
	echo  '<br>';
	
	formsceneryselect();
	echo '</form>';
   
   if ($_POST['profid']) {
		echo '<p>';
//		$in = "'0'";
//		foreach ($_SESSION['sceneryselected'] as $scenid => $scenname) {
//			$in .= " , '".$scenid."'";
//		}
//			$in = "'0' , '1' , '2' , '4'";
       //$in = '0';
	   
	   	$inselected = inscenery_sessionlst('sceneryselected');
   		list($qscentbl,$qscensql) = scenery_sql($inselected);
		
	   $q = "SELECT DISTINCT `discipline`.`name` AS `discname` ,  `discipline`.`id` AS `discid` , `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `discdept`.`id` AS `discdeptid`" .
		 " FROM `classsegment` , `class`, `semester`,`unit`,`discipline`,`prof` , `unit` AS `discdept`  " . $qscentbl . " WHERE " . 
		 "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " . 
		 "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`prof_id` = `prof`.`id` AND " .
		 "`discipline`.`dept_id` = `discdept`.`id` AND " .
		 "`unit`.`id` = '".$_POST['deptid']."' AND `semester`.`id` = '".$_POST['semid']."' AND " . 
		 "`prof`.`id` = '".$_POST['profid'] . "' " . $qscensql . " ORDER BY `discipline`.`name` , `class`.`name`";

	   dbweekmatrix($q,$inselected);
   
   }

 ?>
    
 
</div>


<?php

// SELECT `discipline`.`name` AS `discname` ,  `discipline`.`id` AS `discid` , `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , 
// `discdept`.`id` AS `discdeptid` , case when `class`.`scenery` = '1' then `sceneryclass`.`scenery_id`  else '0'  END AS `sceneryid` 
// FROM `classsegment` , `class`, `semester`,`unit`,`discipline`,`prof` , `unit` AS `discdept` , `sceneryclass` 
// WHERE `class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND `classsegment`.`class_id` = `class`.`id` AND `classsegment`.`prof_id` = `prof`.`id` 
// AND `discipline`.`dept_id` = `discdept`.`id` AND `unit`.`id` = '3' AND `semester`.`id` = '11' AND `prof`.`id` = '129' 
// AND ((`class`.`scenery` = 0) OR (`class`.`scenery` = 1 AND `sceneryclass`.`class_id` = `class`.`id` AND `sceneryclass`.`scenery_id` IN ('1' , '3' , '5' ,'2')))



// EFECTIV
// SELECT DISTINCT `discipline`.`name` AS `discname` , `discipline`.`id` AS `discid` , `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `discdept`.`id` AS `discdeptid` 
// FROM `classsegment` , `class`, `semester`,`unit`,`discipline`,`prof` , `unit` AS `discdept` , `sceneryclass` 
// WHERE `class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND `classsegment`.`class_id` = `class`.`id` AND `classsegment`.`prof_id` = `prof`.`id` 
// AND `discipline`.`dept_id` = `discdept`.`id` AND `unit`.`id` = '3' AND `semester`.`id` = '11' AND `prof`.`id` = '129' 
// AND ((`class`.`scenery` = 0 ) OR (`class`.`scenery` = 1 AND `sceneryclass`.`class_id` = `class`.`id` AND `sceneryclass`.`scenery_id` IN ('1' , '3' , '5' ,'2')))



?> 