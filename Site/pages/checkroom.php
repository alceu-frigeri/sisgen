
<?php $thisform=$basepage.'?q=check&sq=room'; ?>

<div class="row">
        <h2>Verificação Salas p/prédio</h2>
        <hr>

<?php 
	$mysqli->postsanitize();

	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY semester.name;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM building WHERE mark = 1 ORDER BY acronym;",'buildingid',$_POST['buildingid'],'id','acronym');
	echo formsubmit('act','Check');
	echo '</form>';
   


	$q = "SELECT room.* FROM room,building WHERE room.building_id = building.id AND building.id = '".$_POST['buildingid']."' ORDER BY room.acronym;";
	$roomsql = $mysqli->dbquery($q);
	while ($roomrow = $roomsql->fetch_assoc()) {
		
		$q = "SELECT `discipline`.`name` AS `discname`, `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* FROM `classsegment` , `class`, `semester`,`discipline`,`room`,`building` WHERE  " .  
		  "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " . 
		  "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`room_id` = `room`.`id` AND `room`.`building_id` = `building`.`id` AND " . 
		  "`semester`.`id` = '".$_POST['semid']."' AND " . 
		  "`room`.`id` = '".$roomrow['id']."' AND `building`.`id` = '".$_POST['buildingid']."';";
		
	  echo '<br>';
	  echo  formpost($basepage.'?q=reports&sq=room','_blank') . 
		formhiddenval('semid',$_POST['semid']) . formhiddenval('buildingid',$_POST['buildingid']) . 
		formhiddenval('roomid',$roomrow['id']) . formhiddenval('act',$_POST['Refresh']) . 
		formsubmit('submit','go report') . spanformat('larger','',$roomrow['name']) . '</form>'  ;

	  
	  $flag =  checkweek($q);
	  if($flag['disc']) {echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Possível colisão de disciplina<br>';}
  	  if($flag['class']) {echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Possível colisão de turma<br>';}

	}
  

 ?>
    
 
</div>