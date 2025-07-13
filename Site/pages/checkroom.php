
<?php $thisform=$GBLbasepage.'?q=check&sq=room'; ?>

<div class="row">
        <h2>Verificação Salas p/prédio</h2>
        <hr>

<?php 
	$GBLmysqli->postsanitize();

	echo formpost($thisform);
        formretainvalues(array('semid','buildingid'));

	formselectsql($anytmp,"SELECT * FROM semester ORDER BY semester.name DESC;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM building WHERE mark = 1 ORDER BY acronym;",'buildingid',$_POST['buildingid'],'id','acronym');
	echo  '<br>';
	
	formsceneryselect();	
	
	echo '</form>';
   
	
//	$in = "'0'";
//	foreach ($_SESSION['sceneryselected'] as $scenid => $scenname) {
//		$in .= " , '".$scenid."'";
//	}
//	list($qscentbl,$qscensql) = scenery_sql($in);

	$inselected = inscenery_sessionlst('sceneryselected');
	list($qscentbl,$qscensql) = scenery_sql($inselected);


	$q = "SELECT room.* FROM room,building WHERE room.building_id = building.id AND building.id = '".$_POST['buildingid']."' ORDER BY room.acronym;";
	$roomsql = $GBLmysqli->dbquery($q);
	while ($roomrow = $roomsql->fetch_assoc()) {
		
		$q = "SELECT DISTINCT `discipline`.`name` AS `discname`, `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* " . 
		  "FROM `classsegment` , `class`, `semester`,`discipline`,`room`,`building` " . $qscentbl . " WHERE  " .  
		  "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " . 
		  "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`room_id` = `room`.`id` AND `room`.`building_id` = `building`.`id` AND " . 
		  "`semester`.`id` = '".$_POST['semid']."' AND " . 
		  "`room`.`id` = '".$roomrow['id']."' AND `building`.`id` = '".$_POST['buildingid'] . "' " . $qscensql . 
		 " ORDER BY `class`.`name`"
		 ;
		
	  echo '<br>';
	  echo hiddenroomform($_POST['semid'],$_POST['buildingid'],$roomrow['id'],'') . formsubmit('submit','go report') . spanformat('larger','',$roomrow['name']) . '</form>'  ;

	  
	  $flag =  checkweek($q);
	  if($flag['disc']) {echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Possível colisão de disciplina<br>';}
  	  if($flag['class']) {echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Possível colisão de turma<br>';}

	}
  

 ?>
    
 
</div> 