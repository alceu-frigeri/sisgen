
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $GBLbasepage . '?q=grids&sq=scenery';
formretainvalues(array('semid' , 'allscenery' , 'sceneryid'));
      
echo '<div class = "row">' .
    '<h2>Grade de Disciplinas p/ Cenário </h2>' .
    '<hr>' ;
        
echo formpost($thisform);
echo formselectsql($anytmp , 
              "SELECT * FROM semester ORDER BY semester . name DESC;" , 
              'semid' , 
              $_POST['semid'] , 
              'id' , 
              'name');
echo " Todos ? ";
echo formselectsession('allscenery' , 'bool' , $_POST['allscenery'] , false , true);        

if($_SESSION['role']['isadmin']) {
    echo formselectsql($anytmp , 
                  "SELECT DISTINCT scen . * FROM scenery scen ORDER BY name;" , 
                  'sceneryid' , 
                  $_POST['sceneryid'] , 
                  'id' , 
                  'name');
} else {
    if($_POST['allscenery']) {
        $Qallscen = "OR ( scen . hide = '0') ";
    } else {
        $Qallscen = '';
    }
    echo formselectsql($anytmp , 
                  "SELECT DISTINCT scen . * FROM scenery scen  ,  sceneryrole scenrole ,   accrole " . 
                  "WHERE ( scen . id = scenrole . scenery_id " . 
                        "AND scenrole . role_id = accrole . role_id " . 
                        "AND accrole . account_id = '$_SESSION[userid]' ) " . 
                  $Qallscen . 
                  "ORDER BY name; " , 
                  'sceneryid' , 
                  $_POST['sceneryid'] , 
                  'id' , 
                  'name');
}

echo "Nome Profs ? ";
echo formselectsession('profnicks' , 'bool' , $_POST['profnicks'] , false , true);
echo  '<br>';

echo '</form>';

if (($_POST['semid'] != 0 )& ($_POST['sceneryid'] != 0 )) {
    echo '<h3>' . 
        $_SESSION['scen.all'][$_POST['sceneryid']] . 
        ' ( ' . $_SESSION['scen.desc'][$_POST['sceneryid']] . ' ) </h3>';
        

    if($_POST['profnicks']) {
        $Qnicks = 
                " , `prof` . `nickname` AS `profnick` " . 
                " , `prof` . `id` AS `profid` " . 
                " , `prof` . `dept_id` AS `profdeptid`  " ;
    } else {
        $Qnicks = ' ' ;
    }
    $inselected = $_POST['sceneryid'];
        
    $Query = 
        "SELECT DISTINCT `discipline` . `name` AS `discname` ,  " . 
                "`discipline` . `id` AS `discid` , " . 
                "`discipline` . * , `class` . `id` AS `classid` , " . 
                "`class` . * , " . 
                "`classsegment` . * , " . 
                "`discdept` . `id` AS `discdeptid`" . 
                $Qnicks .
        "FROM `classsegment` , `class` , `semester` , `discipline` , `prof` , `unit` AS `discdept` ,  sceneryclass " .
        "WHERE `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " . 
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `classsegment` . `prof_id` = `prof` . `id` " . 
                "AND `discipline` . `dept_id` = `discdept` . `id` " . 
                "AND `semester` . `id` = '$_POST[semid]' " . 
                "AND `class` . `scenery` = '1' " . 
                "AND `sceneryclass` . `class_id` = `class` . `id` " . 
                "AND `sceneryclass` . `scenery_id` = '$_POST[sceneryid]' " .
        "ORDER BY `discipline` . `name` , `class` . `name` ;" ;
                 
    dbweekmatrix( $Query , $inselected);
}

echo '</div> ';
?>
    
 
