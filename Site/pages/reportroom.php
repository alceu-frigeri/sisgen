
<?php $thisform=$basepage.'?q=reports&sq=room'; ?>

<div class="row">
        <h2>Relatório p/Salas </h2>
        <hr>

<?php 
	$mysqli->postsanitize();

	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY semester.name;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM building WHERE mark = 1 ORDER BY acronym;",'buildingid',$_POST['buildingid'],'id','acronym');
	formselectsql($anytmp,"SELECT room.* FROM room,building WHERE room.building_id = building.id AND building.id = '".$_POST['buildingid']."' ORDER BY room.acronym;",'roomid',$_POST['roomid'],'id','acronym');
	echo formsubmit('act','Refresh');
	echo '</form>';
   

  // semester, building, room
  if ($_POST['roomid']) {
	  $q = "SELECT `room`.*, `roomtype`.`name` AS `type` , `building`.`name` AS `buildingname`  FROM `room`,`roomtype`,`building` WHERE `room`.`roomtype_id` = `roomtype`.`id` AND `room`.`building_id` = `building`.`id` AND `room`.`id` = '". $_POST['roomid'] ."';";
	  $result = $mysqli->dbquery($q);
	  $sqlrow = $result->fetch_assoc();
	  
	  echo '<br>'.$sqlrow['buildingname'] . ' - ' . $sqlrow['name'] . ' : ' . $sqlrow['type'];
	  if ($sqlrow['capacity']) {
	  echo ' (cap.: '. $sqlrow['capacity'] .' vagas)';
	  }
	  echo '<p>';
	  
    $q = "SELECT `discipline`.`name` AS `discname` ,  `discipline`.`id` AS `discid`, `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `discdept`.`id` AS `discdeptid` " . 
	  "FROM `classsegment` , `class`, `semester`,`discipline`,`room`,`building`, `unit` AS `discdept` WHERE  " .  
	  "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " . 
	  "`discipline`.`dept_id` = `discdept`.`id` AND " .
	  "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`room_id` = `room`.`id` AND `room`.`building_id` = `building`.`id` AND " . 
	  "`semester`.`id` = '".$_POST['semid']."' AND " . 
	  "`room`.`id` = '".$_POST['roomid']."' AND `building`.`id` = '".$_POST['buildingid']."';";


 //vardebug($q);
 
   dbweekmatrix($q);
  }

 ?>
    
 
</div>