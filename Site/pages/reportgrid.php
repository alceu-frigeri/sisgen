
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['rpgrid'];
formretainvalues(array('semid' , 'deptid'));
  

echo '<div class = "row">' . 
    '<h2>Grade Departamental </h2>' . 
    '<hr>';

echo formpost($thisform);
echo formselectsql($anytmp , 
              "SELECT * FROM semester ORDER BY semester . name DESC;" , 
              'semid' , 
              $_POST['semid'] , 
              'id' , 
              'name');
echo formselectsql($anytmp , 
              "SELECT * FROM unit WHERE isdept = 1 AND mark = 1 ORDER BY unit . name;" , 
              'deptid' , 
              $_POST['deptid'] , 
              'id' , 
              'acronym');
echo "Nome Profs ? ";
echo formselectsession('profnicks' , 
                  'bool' , 
                  $_POST['profnicks'] , 
                  false , 
                  true);
echo  '<br>';
  
echo formsceneryselect();
echo '</form>';
   
   
if ($_POST['semid'] <> '' & $_POST['deptid'] <> '') {
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
                "`discipline` . * , " . 
                "`class` . `id` AS `classid` , " . 
                "`class` . * , " . 
                "`classsegment` . * , " . 
                "`discdept` . `id` AS `discdeptid`"  . 
                $Qnicks .
        " FROM `classsegment` , `class` , `semester` , `unit` , `discipline` , `prof` , `unit` AS `discdept`  " . 
                $qscentbl .
        "WHERE `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " . 
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `classsegment` . `prof_id` = `prof` . `id` " . 
                "AND `discipline` . `dept_id` = `discdept` . `id` " . 
                "AND `unit` . `id` = '$_POST[deptid]' " . 
                "AND `semester` . `id` = '$_POST[semid]'  " . 
                "AND `unit` . `id` = `prof` .  `dept_id` " .
                $qscensql .
        "ORDER BY `discipline` . `name` , `class` . `name`;" ;
    
    echo dbweekmatrix( $Query  , $inselected);
   
}
echo '</div>';


?>

