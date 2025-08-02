
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['room'];
formretainvalues(array('semid' , 'buildingid' , 'roomid','profnicks'));
  

echo '<div class = "row">' .
    '<h2>Grade p/Salas </h2>' .
    '<hr>' ;
        
echo formpost($thisform);
        
$Query = 
        "SELECT * " . 
        "FROM semester " . 
        "ORDER BY semester . name DESC;" ;
echo formselectsql($anytmp , $Query , 'semid' , $_POST['semid'] , 'id' , 'name');
              
$Query = 
        "SELECT * " . 
        "FROM building " . 
        "WHERE mark = 1 " . 
        "ORDER BY acronym ; " ;  
echo formselectsql($anytmp , $Query , 'buildingid' , $_POST['buildingid'] , 'id' , 'acronym');

$Query = 
        "SELECT room . * " . 
        "FROM room , building " . 
        "WHERE room . building_id = building . id " . 
                "AND building . id = '$_POST[buildingid]' " . 
        "ORDER BY room . acronym ; " ; 
echo formselectsql($anytmp , $Query , 'roomid' , $_POST['roomid'] , 'id' , 'acronym');

echo $GBLspc['D'] . "Nome Profs ? ";
echo formselectsession('profnicks' , 'bool' , $_POST['profnicks'] , false , true);
echo  '<br>';
  
echo formsceneryselect();
echo '</form>';
   
$Query = 
        "SELECT room . * " . 
        "FROM room , building " . 
        "WHERE room . id = $_POST[roomid] " .
        "AND room . building_id = $_POST[buildingid] ; " ; 

// semester, building, room
if ( testpostsql( array('semid','buildingid','roomid') , $Query ) ) {
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
    
 
    echo dbweekmatrix(  $Query  , $inselected);
}

echo  '</div>';

?>
