
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['prof'];
formretainvalues(array('semid' , 'deptid' , 'profid'));

echo '<div class = "row">' . 
    '<h2>Grade p/ Prof. </h2>' . 
    '<hr>';

echo formpost($thisform);

$Query = 
        "SELECT * " . 
        "FROM semester " . 
        "ORDER BY semester . name DESC ; " ; 
echo formselectsql($anytmp , $Query , 'semid' , $_POST['semid'] , 'id' , 'name');

$Query = 
        "SELECT * " . 
        "FROM unit " . 
        "WHERE isdept = 1 " . 
        "AND mark = 1 " . 
        "ORDER BY unit . name ; " ; 
echo formselectsql($anytmp , $Query , 'deptid' , $_POST['deptid'] , 'id' , 'acronym');
              
$Query = 
        "SELECT prof . * " . 
        "FROM prof , unit , profkind " . 
        "WHERE prof . dept_id = unit . id " . 
                "AND prof . profkind_id = profkind . id " . 
                "AND profkind . acronym <> '-none-' " . 
                "AND unit . id = '$_POST[deptid]' " . 
                "AND unit . isdept = 1 " . 
                "AND unit . mark = 1 " . 
        "ORDER BY prof . name ; " ; 
echo formselectsql($anytmp , $Query  , 'profid' , $_POST['profid'] , 'id' , 'name');
echo  '<br>';
  
echo formsceneryselect();
echo '</form>';

   
$Query = 
        "SELECT * " . 
        "FROM prof , unit " . 
        "WHERE prof . id = '$_POST[profid]'  "  .
        "AND prof . dept_id = '$_POST[deptid]' " .
        "AND prof . dept_id = unit . id " .
        "AND unit . isdept = 1 " .
        "AND unit . mark = 1 ; " ; 

if ( testpostsql( array('profid','deptid') , $Query) ) {
    echo '<p>';
     
    $inselected = inscenery_sessionlst('sceneryselected');
    list($qscentbl , $qscensql) = scenery_sql($inselected);
    
    $Query = 
        "SELECT DISTINCT `discipline` . `name` AS `discname` ,  " . 
                "`discipline` . `id` AS `discid` , " . 
                "`discipline` . * , " . 
                "`class` . `id` AS `classid` , " . 
                "`class` . * , " . 
                "`classsegment` . * , " . 
                "`discdept` . `id` AS `discdeptid` " .
        "FROM `classsegment` , `class` , `semester` , `unit` , `discipline` , `prof` , `unit` AS `discdept`  " . 
                $qscentbl .
        "WHERE `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " . 
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `classsegment` . `prof_id` = `prof` . `id` " . 
                "AND `discipline` . `dept_id` = `discdept` . `id` " . 
                "AND `unit` . `isdept` = '1' " . 
                "AND `unit` . `mark` = '1' " .
                "AND `unit` . `id` = '$_POST[deptid]' " . 
                "AND `semester` . `id` = '$_POST[semid]' " . 
                "AND `prof` . `id` = '$_POST[profid]' " . 
                $qscensql .
        "ORDER BY `discipline` . `name` , `class` . `name` ; " ;

    echo dbweekmatrix(  $Query  , $inselected);
   
}
echo '</div>';


 ?>

