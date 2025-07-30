
<?php
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['comgrad'];
formretainvalues(array('semid' , 'courseid' , 'deptid'));
  

echo '<div class = "row">' .
    '<h2>Demandas COMGRAD por Departamento </h2>' .
    '<hr>';

echo formpost($thisform);

echo formselectsql($anytmp , 
              "SELECT * FROM semester ORDER BY semester . name DESC;" , 
              'semid' , 
              $_POST['semid'] , 
              'id' , 
              'name');
  
echo 'Curso:'; 
echo formselectsql($anytmp , 
              "SELECT * FROM unit WHERE `iscourse` = '1' ORDER BY unit . acronym;" , 
              'courseid' , 
              $_POST['courseid'] , 
              'id' , 
              'acronym');
  
$Query = "SELECT DISTINCT `disc` . `dept_id` , `unit` . `acronym` , `unit` . `name`  " .
        "FROM `coursedisciplines` AS `grade` , `discipline` AS `disc` , `unit` " .
        "WHERE `grade` . `discipline_id` = `disc` . `id` " . 
                "AND `disc` . `dept_id` = `unit` . `id` " . 
                "AND `grade` . `course_id` = '$_POST[courseid]' " .
        "ORDER BY `unit` . `acronym` ; " ;
       
echo $GBLspc['T'] . 'Dept.:';
echo formselectsql($anytmp , 
              $Query  , 
              'deptid' , 
              $_POST['deptid'] , 
              'dept_id' , 
              'acronym');
  
echo  '<br>';
  
echo formsceneryselect();
echo '</form>';


$inselected = inscenery_sessionlst('sceneryselected');
list($qscentbl , $qscensql) = scenery_sql($inselected);


$Query = 
        "SELECT acronym , " .  
                "name " .  
        "FROM unit " .  
        "WHERE id = '$_POST[deptid]' ; ";

$result = $GBLmysqli->dbquery($Query);
$sqlrow = $result->fetch_assoc();

$emailbody = '';
$emailbodyhdr = '';
if($sqlrow['acronym']) {
    $temp = '<h4>' . $sqlrow['acronym'] . ' -- ' . $sqlrow['name'] . '</h4>';
      
    $emailbodyhdr .= $temp;
}

$termsql = $GBLmysqli->dbquery( "SELECT * FROM `term` ORDER BY `id`" );
while ($termrow = $termsql->fetch_assoc()) {
    
    $Query = 
        "SELECT discipline . * " .
        "FROM coursedisciplines , discipline " .
        "WHERE coursedisciplines . course_id = '$_POST[courseid]' " . 
                "AND coursedisciplines . discipline_id = discipline . id " . 
                "AND discipline . dept_id = '$_POST[deptid]' " . 
                "AND `coursedisciplines` . `term_id` = '$termrow[id]' " .
        "ORDER BY  discipline . name ; " ;
                        
    $discsql = $GBLmysqli->dbquery( $Query );
    if($discsql->num_rows) {
        $temp = '<hr><b>' . $termrow['name'] . '</b><br>';

        $emailbody .= $temp;
    }
    while ($discrow = $discsql->fetch_assoc()) {
        $flag = 0;
        $temp = '<br><b>'. spanformat('' , 'darkblue' , $discrow['code'] . ' -- ' . $discrow['name'])  . '</b><br>';

        $emailbody .= $temp;
        
        //unset( $Query );

        $Query = 
                "SELECT DISTINCT class . * , " . 
                        "(`vac` . `askednum` + `vac` . `askedreservnum` ) as `askednum`  , " . 
                        "`vac` . `askedreservnum` " .
                "FROM  `class` , `vacancies` AS `vac` " . 
                        $qscentbl .
                "WHERE `class` . `discipline_id` = '$discrow[id]' " . 
                        "AND `class` . `sem_id` = '$_POST[semid]' " . 
                        "AND `vac` . `class_id` = `class` . `id` " . 
                        "AND (`vac` . `askednum` > '0' OR `vac` . `askedreservnum` > '0') " . 
                        "AND `vac` . `course_id` = '$_POST[courseid]' " . 
                        $qscensql .
                "ORDER BY `class` . `name`" ;
        
        $classsql = $GBLmysqli->dbquery( $Query );
        while($classrow = $classsql->fetch_assoc()) {
            if ($classrow['askednum']>1) { $p = 's'; } else { $p = ''; };
            $flag = 1;
            $temp = 'Turma: ' . $classrow['name'] . ' ('. $classrow['askednum'] . ' vaga' . $p . ')';

            $emailbody .= $temp;
            if ($classrow['askedreservnum'] > 0) {
                $temp = ' das quais ' . $classrow['askedreservnum'] . ' serão para calouros. ';
                $emailbody .= $temp;
            }
            if ($classrow['agreg']) {
                $temp = spanformat('' , 'darkorange' , ' (agregadora)');

                $emailbody .= $temp;
            } else {
                if($classrow['partof']) {
                    $Query = 
                        "SELECT `name` " .  
                        "FROM `class` " .  
                        "WHERE `id` = '$classrow[partof]' ";
                        
                    $partsql = $GBLmysqli->dbquery($Query);
                    $partrow = $partsql->fetch_assoc();
                    $temp = spanformat('' , 'darkorange' , ' (agregada à ' . $partrow['name'] . ')');

                    $emailbody .= $temp;
                }
            }           
            $temp = '<br>';

            $emailbody .= $temp;
            
            $Query = "SELECT * " .
                "FROM `classsegment` AS `seg` " .
                "WHERE `seg` . `class_id` = '$classrow[id]' ; " ;

            $segsql = $GBLmysqli->dbquery( $Query );
            while ($segrow = $segsql->fetch_assoc()) {
                if ($segrow['length']>1) { $p = 's'; } else { $p = ''; };
                $temp =  $GBLspc['T']  . spanformat('' , 'gray' , $_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 ' . $segrow['length'] . ' Hora' . $p . '-Aula<br>'); 

                $emailbody .= $temp;
            }
        }
        if(!$flag) {
            $temp =  $GBLspc['T']  . spanformat('' , 'gray' , ' -- sem demandas --<br>'); 

            $emailbody .= $temp;
        }
    }
      
}
if ($emailbody) {
    $Query = 
        "SELECT * " .  
        "FROM `unit`  " .  
        "WHERE `id` = '$_POST[courseid]' ; " ;
    $coursesql = $GBLmysqli->dbquery($Query);
    $courserow = $coursesql->fetch_assoc();

    $Query = 
        "SELECT * " .  
        "FROM `unit`  " .  
        "WHERE `id` = '$_POST[deptid]' ; " ;
    $deptsql = $GBLmysqli->dbquery($Query);
    $deptrow = $deptsql->fetch_assoc();

    $Query = 
        "SELECT * " .  
        "FROM `semester`  " .  
        "WHERE `id` = '$_POST[semid]' ; " ; 
    $semsql = $GBLmysqli->dbquery($Query);
    $semrow = $semsql->fetch_assoc();
      
    if (($_POST['act'] == 'Send Email') & ($_POST['trulysend'])) {
        myhtmlmail($_POST['emailfrom'] , $_POST['emailto'] , $_POST['emailsubject'] , str_replace('\r\n' , '<br>' , $_POST['emailtext']) . '<p><hr><h4>Ao</h4>' . $emailbodyhdr . '<h5>Necessidades de Vagas p/ o Curso em ' . $courserow['name'] . '</h5>' . $emailbody);
    }
  
    echo formpost($thisform);  
    echo formhiddenval('semid' , $_POST['semid']);
    echo formhiddenval('courseid' , $_POST['courseid']);
    echo formhiddenval('deptid' , $_POST['deptid']);
    echo '<hr>';
    echo $GBLspc['D'] . 'from:' . formpatterninput(64 , 16 , '[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+' , 'from:' , 'emailfrom' , $courserow['contactemail']) . '<br>';
    echo $GBLspc['Q'] . 'to:' . formpatterninput(64 , 16 , '[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+' , 'to:' , 'emailto' , $deptrow['contactemail']) . '<br>';
    echo 'subject:' . formpatterninput(128 , 64 , '[a-zA-Z0-9\._- ]+' , 'subject:' , 'emailsubject' , 'Demandas COMGRAD/' . $courserow['acronym'] . ' para o semestre ' . $semrow['name'] . ' (' . $deptrow['acronym'] . ')') . '<br>';
    echo 'body:<textarea name="emailtext" rows="10" cols="64"> Prezado(a) ' . $deptrow['contactname'] . " , \n Seguem abaixo as nossas necessidades de turmas/vagas para o Semestre " . 
        $semrow['name'] . '.'  . 
        "\n\nColocamo-nos, desde já, a disposição para sanar quaisquer dúvidas."  . 
        "\n\nAtenciosamente, \n". $courserow['contactname'] . "\nCOMGRAD/"  . $courserow['acronym']  . "\n". $courserow['name'] . "\n\n</textarea><br>";
    echo 'Really Send it:'; 
    echo formselectsession('trulysend' , 'bool' , 0);
    echo formsubmit('act' , 'Send Email');
    echo '</form>';
    echo '<p><hr>' . $emailbodyhdr . $emailbody; 
    //$scapedbody = htmlspecialchars($emailbody);
    // $scapedbody = htmlspecialchars('thats a " b test');
    //echo "<a href='mailto:" . 'email@test.br' . '?body=' . $scapedbody . "'> EMAIL </a>";
    //      mymailX('comgrad_cca@ufrgs.br' , 'alceu.frigeri@ufrgs.br' , 'test report email' , $emailbody);
}
    
echo '</div>' ;
?>
    
 
