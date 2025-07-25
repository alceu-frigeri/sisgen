


<?php 
include 'bailout.php';

$thisform = $GBLbasepage . '?q=reports&sq=assignment'; 
$GBLmysqli->postsanitize();
formretainvalues(array('semid' , 'deptid'));

formjavaprint(displaysqlitem('' , 'unit' , $_POST['deptid'] , 'acronym') . displaysqlitem(' - Encargos ' , 'semester' , $_POST['semid'] , 'name'));
  

echo '<div class = "row">' .
    '<h2>Relatório Depto. p/ Prof.</h2>' .
    '<hr>' ;

echo formpost($thisform);
        
formselectsql($anytmp , 
              "SELECT * FROM semester ORDER BY semester . name DESC;" , 
              'semid' , 
              $_POST['semid'] , 
              'id' , 
              'name');
formselectsql($anytmp , 
              "SELECT * FROM unit WHERE isdept = 1 AND mark = 1 ORDER BY unit . name;" , 
              'deptid' , 
              $_POST['deptid'] , 
              'id' , 
              'acronym');
echo  '<br>';
  
formsceneryselect();  
echo '</form>';

echo "<button onclick=\"printContent('Encargos')\">Print</button>";


$inselected = inscenery_sessionlst('sceneryselected');
list($qscentbl , $qscensql) = scenery_sql($inselected);


echo '<hr><div id="Encargos">';
  
echo '<h2>' . displaysqlitem('' , 'unit' , $_POST['deptid'] , 'acronym') . displaysqlitem(' - Encargos p/ ' , 'semester' , $_POST['semid'] , 'name') . '</h2>';

$Query = 
        "SELECT prof.* " .
        "FROM prof , unit " .
        "WHERE prof . dept_id = unit . id " . 
                "AND unit . id = '$_POST[deptid]' " . 
                "AND prof . profkind_id != '5' " .
        "ORDER BY `prof` . `name` ; " ;

$profsql = $GBLmysqli->dbquery( $Query );
while ($profrow = $profsql->fetch_assoc()) {
    echo '<br>';  

    //unset( $Query );
    $Query = 
        "SELECT DISTINCT `discipline` . `name` AS `discname` , " . 
                "`discipline` . * ,  " . 
                "`discipline` . `id` AS `discid` , " . 
                "`class` . `id` AS `classid` , " . 
                "`class` . * , " . 
                "`classsegment` . * , " . 
                "`unit` . `id` AS `discdeptid` " .
        " FROM `classsegment` , `class` , `semester` , `unit` , `discipline` , `prof` " . 
                $qscentbl .
        "WHERE `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " . 
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `classsegment` . `prof_id` = `prof` . `id` " . 
                "AND `unit` . `id` = '$_POST[deptid]' " . 
                "AND `semester` . `id` = '$_POST[semid]' " . 
                "AND `prof` . `id` = '$profrow[id]' " . 
                $qscensql .
        " ORDER BY `class` . `name` ; " ;
                
    echo   spanformat('larger' , '' , $profrow['name'])  ;
    //    $flag =  checkweek( $Query );    
    
    dbweekmatrix( $Query  , $inselected , null , null , false);

}
echo '</div>' . 
    '</div>';
  

?>

