
<?php
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['course'];
formretainvalues(array('semid' , 'courseid' , 'termid' , 'profnicks' , 'reqonly'));

echo  '<div class = "row">' .
    '<h2>Grade p/Curso e Etapa </h2>' .
    '<hr>' ;

echo formpost($thisform);
   
$Query = 
    "SELECT * " .
    "FROM semester " .
    "ORDER BY name DESC ; " ;
echo formselectsql($anytmp , $Query ,  'semid' ,  $_POST['semid'] ,  'id' ,  'name');
              
$Query = 
    "SELECT * " . 
    "FROM unit " . 
    "WHERE iscourse = 1 " . 
    "ORDER BY unit . name ; " ; 
echo formselectsql($anytmp , $Query , 'courseid' , $_POST['courseid'] , 'id' , 'acronym');

$Query = 
    "SELECT * " . 
    "FROM term " . 
    "ORDER BY term . name ; " ;
echo formselectsql($anytmp , $Query , 'termid' , $_POST['termid'] , 'id' , 'name');

if ($_POST['reqonly'] == '1') {
        echo spanfmtbegin('','darkgreen',null,true);
        $fmtend=spanfmtend();
} else {
        $fmtend=' ';
}
echo $GBLspc['D'] . "Somente OB/AL ? ";
echo formselectsession('reqonly' , 'bool' , $_POST['reqonly'] , false , true);
echo $fmtend;

echo $GBLspc['D'] . "Nome Profs ? ";
echo formselectsession('profnicks' , 'bool' , $_POST['profnicks'] , false , true);
echo  '<br>';  
echo formsceneryselect(); 
echo '</form>';
  
// semester, course, term

if ( testpostsql( array('semid','termid','courseid') ) ) {
    echo '<p>';

    $inselected = inscenery_sessionlst('sceneryselected');
    list($qscentbl , $qscensql) = scenery_sql($inselected);

    if($_POST['reqonly']) {
        $qextra = "AND ( `coursedisciplines` . `disciplinekind_id` = '1' OR `coursedisciplines` . `disciplinekind_id` = '3' ) ";
    } else {
        $qextra = '';
    }

    if($_POST['profnicks']) {
        $Qnicks = " , `prof` . `nickname` AS `profnick` , `prof` . `id` AS `profid` , `prof` . `dept_id` AS `profdeptid`  ";
    } else {
        $Qnicks = '';
    }
        
    $Query = 
        "SELECT DISTINCT `discipline` . `name` AS `discname` , "  . 
                "`discipline` . `id` AS `discid` , " . 
                "`discipline` . * , `class` . `id` AS `classid` , " . 
                "`class` . * , `classsegment` . * , " . 
                "`discdept` . `id` AS `discdeptid` , " . 
                "`unit` . `id` AS `courseid` " . 
                $Qnicks  .
        "FROM  `classsegment` , `class` , `term` , `semester` , `coursedisciplines` " . 
                ", `unit` , `discipline` , `unit` AS `discdept` , `prof` " . 
                $qscentbl .
        "WHERE `coursedisciplines` . `course_id` = `unit` . `id` " . 
                "AND `coursedisciplines` . `term_id` = `term` . `id` " . 
                "AND `coursedisciplines` . `discipline_id` = `discipline` . `id` " . 
                "AND `discipline` . `dept_id` = `discdept` . `id` " . 
                "AND `classsegment` . `prof_id` = `prof` . `id` " . 
                "AND `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " . 
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `unit` . `id` = '$_POST[courseid]' " . 
                "AND `term` . `id` = '$_POST[termid]' " . 
                "AND `semester` . `id` = '$_POST[semid]' " . 
                $qscensql . $qextra . 
        "ORDER BY `discipline` . `name` , `class` . `name` ; " ;

    echo dbweekmatrix(  $Query , $inselected , $_POST['courseid'] , $_POST['termid']);
}

echo '</div>' ;

?>

  


