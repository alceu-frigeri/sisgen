
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $GBLbasepage . '?q=check&sq=prof'; 
formretainvalues(array('semid' , 'deptid'));

echo '<div class = "row">' .
    '<h2>Verificação Professores p/departamento </h2>' .
    '<hr>' ;

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
echo  '<br>';
  
echo formsceneryselect();  
echo '</form>';

  
$inselected = inscenery_sessionlst('sceneryselected');
list($qscentbl , $qscensql) = scenery_sql($inselected);

$Query = 
        "SELECT DISTINCT prof . * " .
        "FROM prof , unit , profkind " .
        "WHERE prof . dept_id = unit . id  " . 
                "AND prof . profkind_id = profkind . id " . 
                "AND profkind . acronym <> '-none-' " . 
                "AND unit . id = '$_POST[deptid]' " .
        "ORDER BY prof . name ; " ;

$profsql = $GBLmysqli->dbquery( $Query  );
while ($profrow = $profsql->fetch_assoc()) {
    echo '<br>';  
    
    $Query = 
        "SELECT DISTINCT `discipline` . `name` AS `discname` , " . 
                "`discipline` . * , " . 
                "`class` . `id` AS `classid` , " . 
                "`class` . * , " . 
                "`classsegment` . * " .
        "FROM `classsegment` , `class` , `semester` , `unit` , `discipline` , `prof` " . 
                $qscentbl  .
        "WHERE `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " . 
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `classsegment` . `prof_id` = `prof` . `id` " . 
                "AND `unit` . `id` = '$_POST[deptid]' " . 
                "AND `semester` . `id` = '$_POST[semid]' " . 
                "AND `prof` . `id` = '$profrow[id]' " . 
                $qscensql .
        " ORDER BY `class` . `name` ; " ;
    
    echo hiddenprofform($_POST['semid'] , $_POST['deptid'] , $profrow['id'] , '') . formsubmit('submit' , 'go report') . spanformat('larger' , '' , $profrow['name']) . '</form>'  ;
    $flag =  checkweek( $Query  );
    
    if($flag['disc']) {
        echo spanformat('','orange',$GBL_Qspc . 'Possível colisão de disciplina<br>');
    }
    if($flag['class']) {
        echo spanformat('','brown',$GBL_Qspc . 'Possível colisão de turma<br>');
    }

}
  
echo '</div>' ; 

?>
    
 

