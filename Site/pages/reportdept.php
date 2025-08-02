
<?php
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['rpdept'];
formretainvalues(array('semid' , 'deptid'));
  
echo formjavaprint(displaysqlitem('' , 'unit' , $_POST['deptid'] , 'acronym') . displaysqlitem(' - Encargos ' , 'semester' , $_POST['semid'] , 'name'));       

echo '<div class = "row">' .
    '<h2>Relatório Depto. p/ Disciplina </h2>' .
    '<hr>' ;

echo formpost($thisform);

$Query = 
        "SELECT * " . 
        "FROM `semester` " . 
        "ORDER BY `name` DESC;" ; 
echo formselectsql($anytmp , $Query , 'semid' , $_POST['semid'] , 'id' , 'name');

$Query = 
        "SELECT * " . 
        "FROM `unit`  " . 
        "WHERE (`isdept` = '1' AND `mark` = '1') OR (`iscourse` = '1') " . 
        "ORDER BY `isdept` DESC, `acronym` ASC;" ;
echo formselectsql($anytmp , $Query , 'deptid' , $_POST['deptid'] , 'id' , 'acronym');

echo  '<br>';
echo formsceneryselect();
echo '</form>';

echo "<button onclick = \"printContent('Encargos')\">Print</button>";

$inselected = inscenery_sessionlst('sceneryselected');
list($qscentbl , $qscensql) = scenery_sql($inselected);

  
echo '<hr><div id = "Encargos">';

$Query = 
        "SELECT * " . 
        "FROM `unit`  " . 
        "WHERE ((`isdept` = '1' AND `mark` = '1') OR (`iscourse` = '1')) " . 
        "AND `id` = '$_POST[deptid]' ; " ;

if ( testpostsql(array('deptid' , 'semid') , $Query) ) {
        echo '<h2>' . 
            displaysqlitem('' , 'unit' , $_POST['deptid'] , 'acronym') . 
            displaysqlitem(' - Encargos p/ ' , 'semester' , $_POST['semid'] , 'name') . 
            '</h2>';
        
            $Query = 
                "SELECT DISTINCT `discipline` . * " .
                "FROM `discipline` , `unit` " .
                "WHERE `discipline` . `dept_id` = '$_POST[deptid]' " .
                "AND `discipline` . `dept_id` = `unit` . `id` " . 
        //        "AND ((`unit` . `isdept` = '1' AND `unit` . `mark` = '1') OR (`unit` . `iscourse` = '1')) " . 
                "ORDER BY  `name` ; " ;
                
            $discsql = $GBLmysqli->dbquery( $Query );
        
        while ($discrow = $discsql->fetch_assoc()) {
            echo '<br><b>' . 
                spanformat('' , 'darkblue' , $discrow['code'] . 
                           ' -- ' . 
                           $discrow['name'])  . 
                '</b><br>';
        
            //unset($Query);
            $Query = 
                "SELECT DISTINCT class . * " .
                "FROM  `class` " . $qscentbl .
                "WHERE `class` . `discipline_id` = '$discrow[id]' " . 
                        "AND `class` . `sem_id` = '$_POST[semid]' " . 
                        $qscensql .
                "ORDER BY `class` . `name` ; " ;       
             
            $classsql = $GBLmysqli->dbquery( $Query );
            while($classrow = $classsql->fetch_assoc()) {
                echo 'Turma: ' . $classrow['name'];
                if ($classrow['agreg']) {
                    echo spanformat('' , 'darkorange' , ' (agregadora)');
                } else {
                    if($classrow['partof']) {
                        $q = "SELECT `name` FROM `class` WHERE `id` = '$classrow[partof]' ;";
                        $partsql = $GBLmysqli->dbquery($q);
                        $partrow = $partsql->fetch_assoc();
                        echo spanformat('' , 'darkorange' , ' (agregada à ' . $partrow['name'] . ')');
                    }
                }       
                echo '<br>';
                
                $Query = 
                        "SELECT `seg` . * , `building` . `acronym` AS `buildingname` " . 
                                ", `room` . `acronym` AS `roomname` , " . 
                                "`room` . `capacity` AS `capacity` , " . 
                                "`prof` . `nickname` , " . 
                                "`prof` . `name`  " .
                        "FROM `classsegment` AS `seg` , `room` , `building` , `prof` "  .
                        "WHERE `seg` . `room_id` = `room` . `id` " . 
                                "AND `room` . `building_id` = `building` . `id`  " .  
                                "AND `seg` . `prof_id` = `prof` . `id` " . 
                                "AND  `seg` . `class_id` = '$classrow[id]' ; " ;
                                                                       
                $segsql = $GBLmysqli->dbquery( $Query );
                while ($segrow = $segsql->fetch_assoc()) {
                    if ($segrow['length']>1) { $p = 's'; } else { $p = ''; };
                    echo  $GBLspc['T']  . 
                        spanformat('' , 'gray' , $_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 ' . $segrow['length'] . ' Hora' . $p . '-Aula') . 
                        ', ' . $segrow['name'] . ',  ' . 
                        spanformat('' , 'gray' , 'Sala: ' . $segrow['roomname'] . ' (' . $segrow['buildingname'] . ')');
                    if ($segrow['capacity']) {
                        echo ' (cap . : ' . $segrow['capacity'] . ')';
                    }
                    echo '<br>'; 
                }
                
                $Query = 
                        "SELECT `vac` . * , " . 
                                "`unit` . `acronym` , " . 
                                "`kind` . `code` AS `disckind` " .
                        "FROM `vacancies` AS `vac` , " . 
                                "`unit` , " . 
                                "`coursedisciplines` AS `grade` , " . 
                                "`disciplinekind` AS `kind` " .
                        "WHERE `vac` . `course_id` = `unit` . `id` " . 
                                "AND `vac` . `course_id` = `grade` . `course_id` " . 
                                "AND `grade` . `disciplinekind_id` = `kind` . `id` " . 
                                "AND `grade` . `discipline_id` = '$discrow[id]' " . 
                                "AND `vac` . `class_id` = '$classrow[id]' " .
                        "ORDER BY `unit` . `acronym` ; ";
               
                $vacsql = $GBLmysqli->dbquery( $Query );
                while ($vacrow = $vacsql->fetch_assoc()) {
                    if (($vacrow['givennum'] + $vacrow['givenreservnum'])==1) 
                    { $p = ''; } 
                    else 
                    { $p = 's'; };       
                                                
                    echo $GBLspc['Q'] . $vacrow['acronym'] . ' : ' . 
                        ($vacrow['givennum'] + $vacrow['givenreservnum']) . 
                        ' Vaga' . $p . 
                        ' (' . $vacrow['disckind']  . ')';
                                                
                    if ($vacrow['givenreservnum'] > 0) {
                        echo spanformat('' , 'gray', "  das quais $vacrow[givenreservnum] p/calouros");
                    }
                    echo '<br>';
                }
               
            }
        }
    
}

echo '</div>';
echo '</div>';
      
?>
    
