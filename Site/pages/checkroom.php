
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $GBLbasepage . '?q=check&sq=room';
formretainvalues(array('semid' , 'buildingid'));

echo '<div class = "row">' . 
    '<h2>Verificação Salas p/prédio</h2>' . 
    '<hr>' ;

echo formpost($thisform);

formselectsql($anytmp , 
              "SELECT * FROM semester ORDER BY semester . name DESC;" , 
              'semid' , 
              $_POST['semid'] , 
              'id' , 
              'name');
formselectsql($anytmp , 
              "SELECT * FROM building WHERE mark = 1 ORDER BY acronym;" , 
              'buildingid' , 
              $_POST['buildingid'] , 
              'id' , 
              'acronym');
echo  '<br>';
  
formsceneryselect();  
  
echo '</form>';
   
$inselected = inscenery_sessionlst('sceneryselected');
list($qscentbl , $qscensql) = scenery_sql($inselected);

$Query = 
        "SELECT room . * " .
        "FROM room , building " .
        "WHERE room . building_id = building . id " . 
                "AND building . id = '$_POST[buildingid]' " .
        "ORDER BY room . acronym ; " ;
        

$roomsql = $GBLmysqli->dbquery( $Query );
while ($roomrow = $roomsql->fetch_assoc()) {
    
    $Query = 
        "SELECT DISTINCT `discipline` . `name` AS `discname` , " . 
                "`discipline` . * , " . 
                "`class` . `id` AS `classid` , " . 
                "`class` . * , " . 
                "`classsegment` . * " .
        "FROM `classsegment` , `class` , `semester` , `discipline` , `room` , `building` " . 
                $qscentbl .
        "WHERE  `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " . 
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `classsegment` . `room_id` = `room` . `id` " . 
                "AND `room` . `building_id` = `building` . `id` " . 
                "AND `semester` . `id` = '$_POST[semid]' " . 
                "AND `room` . `id` = '$roomrow[id]' " . 
                "AND `building` . `id` = '$_POST[buildingid]' " . 
                $qscensql .
        "ORDER BY `class` . `name` ; " ;
    
    echo '<br>';
    echo hiddenroomform($_POST['semid'] , $_POST['buildingid'] , $roomrow['id'] , '') . formsubmit('submit' , 'go report') . spanformat('larger' , '' , $roomrow['name']) . '</form>'  ;

    
    $flag =  checkweek( $Query );

    if($flag['disc']) {
        echo spanformat('','orange',$GBL_Qspc . 'Possível colisão de disciplina<br>');
    }
    if($flag['class']) {
        echo spanformat('','brown',$GBL_Qspc . 'Possível colisão de turma<br>');
    }
    
}

echo '</div>' ;
  

?>
