
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $GBLbasepage . '?q=grids&sq=room';
formretainvalues(array('semid' , 'buildingid' , 'roomid'));
  

echo '<div class = "row">' .
    '<h2>Grade p/Salas </h2>' .
    '<hr>' ;
        
echo formpost($thisform);
        
echo formselectsql($anytmp , 
              "SELECT * FROM semester ORDER BY semester . name DESC;" , 
              'semid' , 
              $_POST['semid'] , 
              'id' , 
              'name');
echo formselectsql($anytmp ,
              "SELECT * FROM building WHERE mark = 1 ORDER BY acronym;" , 
              'buildingid' , 
              $_POST['buildingid'] , 
              'id' , 
              'acronym');
echo formselectsql($anytmp , 
              "SELECT room . * FROM room , building WHERE room . building_id = building . id AND building . id = '" . 
              $_POST['buildingid'] .  "' ORDER BY room . acronym;" , 
              'roomid' , 
              $_POST['roomid'] , 
              'id' , 
              'acronym');
echo "Nome Profs ? ";
echo formselectsession('profnicks' , 'bool' , $_POST['profnicks'] , false , true);
echo  '<br>';
  
echo formsceneryselect();
echo '</form>';
   

// semester, building, room
if ($_POST['roomid']) {
    $Query = 
        "SELECT DISTINCT `room` . *, " . 
                "`roomtype` . `name` AS `type` , " . 
                "`building` . `name` AS `buildingname`" .
        "FROM `room` , `roomtype` , `building`" .
        "WHERE `room` . `roomtype_id` = `roomtype` . `id` " . 
                "AND `room` . `building_id` = `building` . `id` " . 
                "AND `room` . `id` = '$_POST[roomid]' ; " ;

    $result = $GBLmysqli->dbquery( $Query );
    $sqlrow = $result->fetch_assoc();
    
    echo '<br>' . $sqlrow['buildingname'] . ' - ' . $sqlrow['name'] . ' : ' . $sqlrow['type'];
    if ($sqlrow['capacity']) {
        echo ' (cap . : '. $sqlrow['capacity']  . ' vagas)';
    }
    echo '<p>';

    $inselected = inscenery_sessionlst('sceneryselected');
    list($qscentbl , $qscensql) = scenery_sql($inselected);

    if($_POST['profnicks']) {
        $Qnicks = " , `prof` . `nickname` AS `profnick` , `prof` . `id` AS `profid` , `prof` . `dept_id` AS `profdeptid`  ";
    } else {
        $Qnicks = '';
    }
    
    $Query = 
        "SELECT DISTINCT `discipline` . `name` AS `discname` ,  " . 
                "`discipline` . `id` AS `discid` , " . 
                "`discipline` . * , `class` . `id` AS `classid` , " . 
                "`class` . * , " . 
                "`classsegment` . * , " . 
                "`discdept` . `id` AS `discdeptid` "  .
                $Qnicks .
        "FROM `classsegment` , `class` , `semester` , `discipline` , `room` , `building` , `unit` AS `discdept` , `prof` " . 
                $qscentbl .
        " WHERE  `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " .
                "AND `discipline` . `dept_id` = `discdept` . `id` " .  
                "AND  `classsegment` . `prof_id` = `prof` . `id` " .
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `classsegment` . `room_id` = `room` . `id` " . 
                "AND `room` . `building_id` = `building` . `id` " .
                "AND `semester` . `id` = '$_POST[semid]' " . 
                "AND `room` . `id` = '$_POST[roomid]' " . 
                "AND `building` . `id` = '$_POST[buildingid]' " . 
                $qscensql .
        "ORDER BY `discipline` . `name` , `class` . `name` ;";
    
 
    dbweekmatrix(  $Query  , $inselected);
}

echo  '</div>';

?>
