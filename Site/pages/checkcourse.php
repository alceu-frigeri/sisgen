
<?php
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $GBLbasepage . '?q=check&sq=course';
formretainvalues(array('semid' , 'courseid'));

echo '<div class = "row">' .
    '<h2>Verificação Curso p/semestre</h2>' .
    '<hr>' ;

echo formpost($thisform);

echo formselectsql($anytmp , 
              "SELECT * FROM semester ORDER BY name DESC;" , 
              'semid' , 
              $_POST['semid'] , 
              'id' , 
              'name');
echo formselectsql($anytmp , 
              "SELECT * FROM unit WHERE iscourse = 1 ORDER BY unit . name;" , 
              'courseid' , 
              $_POST['courseid'] , 
              'id' , 
              'acronym');
echo  '<br>';
  
echo formsceneryselect();

echo '</form>';
  
$inselected = inscenery_sessionlst('sceneryselected');
list($qscentbl , $qscensql) = scenery_sql($inselected);

  
// semester, course, term
$Query = 
        "SELECT * " .  
        "FROM `term` " .  
        "ORDER BY `id` ; " ;
$termsql = $GBLmysqli->dbquery($Query);
  
while ($termrow = $termsql->fetch_assoc()) {
    
    $Query = 
        "SELECT DISTINCT `discipline` . `name` AS `discname` , " . 
                "`discipline` . * , " . 
                "`class` . `id` AS `classid` , " . 
                "`class` . * , " . 
                "`classsegment` . * , " . 
                "`disciplinekind` . `code` AS `disckind` " .
        "FROM   `classsegment` , `class` , `term` , `semester` , `coursedisciplines` , `unit` , `discipline` , `disciplinekind` " . 
                $qscentbl .
        "WHERE `coursedisciplines` . `course_id` = `unit` . `id` " .  
                "AND `coursedisciplines` . `term_id` = `term` . `id` " . 
                "AND `coursedisciplines` . `discipline_id` = `discipline` . `id` " . 
                "AND `coursedisciplines` . `disciplinekind_id` = `disciplinekind` . `id` " . 
                "AND `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " . 
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `unit` . `id` = '$_POST[courseid]' " . 
                "AND `term` . `id` = '$termrow[id]' " . 
                "AND `semester` . `id` = '$_POST[semid]' " . 
                $qscensql .
        "ORDER BY `class` . `name` ; " ;
  
    echo '<br>';
    echo hiddencourseform($_POST['semid'] , $_POST['courseid'] , $termrow['id'] , '') . formsubmit('submit' , 'go report') . spanformat('larger' , '' , $termrow['name']) . '</form>'  ;
    
    $flag =  checkweek( $Query , null , $_POST['courseid'] , $termrow['id']);

    if($flag['disc']) {
        echo spanformat('','orange',$GBL_Qspc . 'Possível colisão de disciplina<br>');
    }
    if($flag['class']) {
        echo spanformat('','brown',$GBL_Qspc . 'Possível colisão de turma<br>');
    }
    if($flag['ob']) {
        echo spanformat('','',$GBL_Qspc . 'Disciplina ob/al não ofertada<br>',null,true);
    }

    //     dbweekmatrix(  implode( ' ' , $Query)  , $inselected , $_POST['courseid']);
}

echo '</div>' ;
?>
