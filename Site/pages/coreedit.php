
<?php 


if(!($_SESSION['rooms'])) {
    $Query = 
        "SELECT room . id , " .
                "room . acronym AS room , " .
                "room . capacity AS capacity , " .
                "building . acronym AS building , " .
                "building . mark AS mark , " .
                "building . id AS buildingid " .
        "FROM room , building " .
        "WHERE room . building_id = building . id " .
        "ORDER BY building . acronym , room . acronym;";
    $sqlroom = $GBLmysqli->dbquery($Query);
    while ($roomrow = $sqlroom->fetch_assoc()) {
        if ($roomrow['capacity']){$cap = ' (cap.: ' . $roomrow['capacity'] . ')';} else {$cap = '';}
        $_SESSION['rooms'][$roomrow['id']]['txt'] = $roomrow['building'] . ' - ' . $roomrow['room'] . $cap;
        $_SESSION['rooms'][$roomrow['id']]['buildmark'] = $roomrow['mark'];
        $_SESSION['rooms'][$roomrow['id']]['buildingid'] = $roomrow['buildingid'];
        $_SESSION['roomsID'][$roomrow['id']] = $roomrow['building'] . ' - ' . $roomrow['room'];
        if ($roomrow['mark']) {
            $_SESSION['rooms'][$roomrow['id']]['txtlnk'] = hiddenformlnk(hiddenroomkey($_POST['semid'] , $roomrow['buildingid'] , $roomrow['id']) , $roomrow['building'] . ' - ' . $roomrow['room']) . $cap;
        } else {
            $_SESSION['rooms'][$roomrow['id']]['txtlnk'] = $roomrow['building'] . ' - ' . $roomrow['room'] . $cap;
        }
    }   
}

// That, just because 'semid' might/will change...
    $Query = 
        "SELECT room . id , " .
                "room . acronym AS room , " .
                "room . capacity AS capacity , " .
                "building . acronym AS building , " .
                "building . mark AS mark , " .
                "building . id AS buildingid " .
        "FROM room , building " .
        "WHERE room . building_id = building . id " .
                "AND mark = '1' " . 
        "ORDER BY building . acronym , room . acronym;";
    $sqlroom = $GBLmysqli->dbquery( $Query );
    while ($roomrow = $sqlroom->fetch_assoc()) {
            $_SESSION['rooms'][$roomrow['id']]['txtlnk'] = hiddenformlnk(hiddenroomkey($_POST['semid'] , $roomrow['buildingid'] , $roomrow['id']) , $roomrow['building'] . ' - ' . $roomrow['room']) . $cap;
    }


if(!($_SESSION['deptprof' . $_POST['unitid']])) {
    $Query = 
        "SELECT prof . id , prof . name " .
        "FROM prof " .
        "WHERE prof . dept_id =  '$_POST[unitid]'  " .
        "ORDER BY name;";
        
    $sqlprof = $GBLmysqli->dbquery($Query);
    while ($profrow = $sqlprof->fetch_assoc()) {
        $_SESSION['deptprof' . $_POST['unitid']][$profrow['id']] = $profrow['name'];
        $_SESSION['deptIDprof' . $_POST['unitid']][$profrow['id']] = $_POST['unitid'];
    }
    $Query = 
        "SELECT prof . id , prof . name , prof . dept_id " .
        "FROM prof , coursedept " .
        "WHERE prof . dept_id = coursedept . dept_id " . 
                "AND coursedept . course_id  =  '$_POST[unitid]'  " .
        "ORDER BY name;";
        
    $sqlprof = $GBLmysqli->dbquery($Query);
    while ($profrow = $sqlprof->fetch_assoc()) {
        $_SESSION['deptprof' . $_POST['unitid']][$profrow['id']] = $profrow['name'];
        $_SESSION['deptIDprof' . $_POST['unitid']][$profrow['id']] = $profrow['dept_id'];
    }
}

if(!($_SESSION['status'])) {
    $Query = 
        "SELECT * " .
        "FROM status;";
        
    $sqlstatus = $GBLmysqli->dbquery($Query);
    while ($statusrow = $sqlstatus->fetch_assoc()) {
        $_SESSION['status'][$statusrow['id']] = $statusrow['status'];
        $_SESSION['statuscolor'][$statusrow['id']] = $statusrow['color'];
    }
    
}

  
//// auxiliary functions....
function thisformpost($hash = null) {
    global $thisform;
    
    if ($hash) {
        echo formpost($thisform . '#' . $hash);
    } else {
        echo formpost($thisform);
    }
    echo formhiddenval('semid' , $_POST['semid']);
    echo formhiddenval('unitid' , $_POST['unitid']);
    echo formhiddenval('discid' , $_POST['discid']);
    echo formhiddenval('profnicks' , $_POST['profnicks']);
    echo formhiddenval('courseHL' , $_POST['courseHL']);
}


function sceneryclasshack($profnicks , $inselect = true) {
    if($inselect) {
        echo formsceneryselect();
    } else {
        echo '<br>';
        echo displaysessionselected('Cenário(s)' , 'sceneryselected');
    }
  
    $inselected = inscenery_sessionlst('sceneryselected');
    list($qqscentbl , $qqscensql) = scenery_sql($inselected);

    if($profnicks) {
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
                "`discdept` . `id` AS `discdeptid` " . 
                $Qnicks .
        "FROM `classsegment` , `class` , `semester` , `unit` , `discipline` , `prof` , `unit` AS `discdept`  " . 
                $qqscentbl .
        "WHERE `class` . `discipline_id` = `discipline` . `id` " . 
                "AND `class` . `sem_id` = `semester` . `id` " . 
                "AND `classsegment` . `class_id` = `class` . `id` " . 
                "AND `classsegment` . `prof_id` = `prof` . `id` " . 
                "AND `discipline` . `dept_id` = `discdept` . `id` " . 
                "AND `unit` . `id` =  '$_POST[unitid]'  " . 
                "AND `semester` . `id` =  '$_POST[semid]'  " . 
                "AND `discipline` . `id` =  '$_POST[discid]'  " . 
                $qqscensql .
        "ORDER BY `discipline` . `name` , `class` . `name` ; " ;
       

    return [$inselected ,  $Query  ];

}





function formsegmentdisplay($segrow) {
    global $GBLmysqli;
    global $hiddenprofdeptid;
    global $hiddenroombuildingid;
    
    $hiddenprofdeptid[$segrow['prof_id']] = $_SESSION['deptIDprof' . $_POST['unitid']][$segrow['prof_id']];
    $hiddenroombuildingid[$segrow['room_id']] = $_SESSION['rooms'][$segrow['room_id']]['buildingid'];
    echo $_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 -- ' . $segrow['length'] . 'H -- ' . $_SESSION['rooms'][$segrow['room_id']]['txtlnk'] . ' -- ' . 
        hiddenformlnk(hiddenprofkey($_POST['semid'] , $_SESSION['deptIDprof' . $_POST['unitid']][$segrow['prof_id']] , $segrow['prof_id']) , $_SESSION['deptprof' . $_POST['unitid']][$segrow['prof_id']])  . 
        $GBLspc['Q']. spanformat('', $_SESSION['statuscolor'][$segrow['status_id']]  ,  '(' .  $_SESSION['status'][$segrow['status_id']] . ')');
}
    
  
function formsegmentedit($segrow) {
    global $GBLmysqli;
    global $can_addclass;
    
    $segformid = 'seg' . $segrow['id'];
    $_SESSION['segments'][$segrow['id']] = $segformid;

    echo formselectrange($segformid . 'day' , 2 , 8 , $segrow['day'] , '' , $_SESSION['weekday']);
    echo formselectrange($segformid . 'start' , 7 , 21 , $segrow['start'] , ':30');
    echo formselectrange($segformid . 'length' , 1 , 6 , $segrow['length']);
    echo formselectsession($segformid . 'room' , 'roomsID' , $segrow['room_id']);
    echo formselectsession($segformid . 'prof' , 'deptprof' . $_POST['unitid'] , $segrow['prof_id']);
    echo formselectsession($segformid . 'status' , 'status' , $segrow['status_id']);
    
    if ($can_addclass) {
        echo spanfmtbegin('' , 'red' , null , true) . $GBLspc['T'] . 'remover:';
        echo formselectsession($segformid . 'delete' , 'bool' , 0);
        echo spanfmtend();
    }
}
  
function formclassedit ($classrow , $incanedit , $canbyscenery = false) {
    global $GBLmysqli;
    global $can_class;
    global $can_addclass;
    global $postedit;
    global $GBLpattern;
    global $GBLspc;
    global $class_edited;
    
    $class_edited = true;
    $classkey = 'class' . $classrow['id'];
    $_SESSION['classes'][$classrow['id']] = $classkey;
    
    echo HLbegin();
    echo 'Turma:<b>' . formpatterninput(3 , 1 , $GBLpattern['class'] , ' Turma' , $classkey . 'classname' , $classrow['name']) . '</b>' . $GBLspc['Q'] . 'agregadora:';


    echo formselectsession($classkey . 'agreg' , 'bool' , $classrow['agreg']);
    if($_SESSION['agreg']) {
        echo '  agregada à:';
        echo formselectsession($classkey . 'partof' , 'agreg' , $classrow['partof'] , true);
    }
    echo $GBLspc['T'] . 'status:';
    echo formselectsession($classkey . 'status' , 'status' , $classrow['status_id']);

    //echo spanformat('' , 'red' , $GBLspc['T'] . 'remover:' , null , true);
    echo spanfmtbegin('' , 'red' , null , true) . $GBLspc['T'] . 'remover:';
    echo formselectsession($classkey . 'delete' , 'bool' , 0);
    echo spanfmtend();

    echo '</br>';
    echo 'Obs.: ' . formpatterninput(48 , 16 , $GBLpattern['comment'] , 'Obs.' , $classkey . 'comment' , $classrow['comment']) . '<br>';
    
        
    $Query = 
        "SELECT * " .
        "FROM classsegment " .
        "WHERE classsegment . class_id = '$classrow[id]' " .
        "ORDER BY day , start ; " ;
        
    $segresult = $GBLmysqli->dbquery($Query);
    while ($segrow = $segresult->fetch_assoc()) {
        formsegmentedit($segrow);
        echo '<br>';
    }
    if ($postedit && ($can_addclass || $canbyscenery)) {
        echo 'Adicionar segmento:';
        echo formselectsession($classkey . 'addsegment' , 'ADDsegments' , 0);
    }
    echo '<br>';
    
    if ($postedit && ($can_addclass || $canbyscenery)) {
        if($can_addclass) {
            echo 'Cenários?:';
            echo formselectsession($classkey . 'scenerybool' , 'bool' , $classrow['scenery']);
        } else {
            echo 'Cenários:';
            echo formhiddenval($classkey . 'scenerybool' , $classrow['scenery']);
            $_SESSION['org'][$classkey . 'scenerybool'] = $classrow['scenery'];        
        }
      
        unset ($_SESSION['org']['sceneryclass']);
        unset ($_SESSION['org']['sceneryusr']);
        echo $GBLspc['Q'];
        $Query = 
                "SELECT * " .
                "FROM sceneryclass " .
                "WHERE class_id = '$classrow[id]' ; " ; // classkey X classid !!!!
        
        $scentmpsql = $GBLmysqli->dbquery($Query);
        while ($scentmprow = $scentmpsql->fetch_assoc()) {
            $_SESSION['org']['sceneryclass'][$scentmprow['scenery_id']] = $scentmprow['id'];
        }
        echo '<table><tr>';
        $cnt = 0;
        foreach ($_SESSION['scen.acc.edit'] as $id => $name) {
            $_SESSION['org']['sceneryusr'][$id] = $_SESSION['org']['sceneryclass'][$id];
            $checked = '';
            if ($_SESSION['org']['sceneryclass'][$id]) {
                $checked = ' checked';
            };
            $cnt++;
            if ($cnt == 9) {
                $cnt = 1;
                echo '</tr><tr>';
            }
            echo '<th style="width:110px">' . '<input type="checkbox" name="' . $classkey. 'scenery' . $id . '" id="scenery' . $id .   '" value="'. $id  . '"'  . $checked. '> <label for="scenery'. $id  . '">'. $name  . '</label></th>';
        }
        echo '</tr></table>';
    }

    $Query = 
        "SELECT vacancies . * , " .
                "unit . acronym , " .
                "unit . id AS courseid " .
        "FROM vacancies , unit " .
        "WHERE vacancies . course_id = unit . id " .
        "AND vacancies . class_id = '$classrow[id]' " .
        "ORDER BY unit . acronym ; " ;
        
    $vacsql = $GBLmysqli->dbquery($Query);
    if($classrow['agreg']) {
        formvacdisplay($vacsql);
    } else {
        formvacedit($vacsql);
    }
    echo HLend();
}
  
function formclassdisplay ($classrow , $vacedit = false) {
    global $GBLmysqli;
    global $can_class;
    global $can_addclass;
    global $postedit;
    global $GBLcommentcolor;
    global $GBLspc;
    
    $courseHL = false;
    if ($_POST['courseHL'] == 1) {
        $Query = 
                "SELECT (`askednum` + `askedreservnum`+ `givennum` + `givenreservnum`) AS `total` " .
                "FROM `vacancies` " .
                "WHERE `class_id` = '$classrow[id]' " .
                "AND `course_id` =  '$_POST[courseHL]' ; " ;
                
        $xresult = $GBLmysqli->dbquery($Query);
        $xrow = $xresult->fetch_assoc();
        if($xrow['total']) {
            $courseHL = true;
        }
    }
    
    echo 'Turma:<b>' . $classrow['name'] . '</b>' . $GBLspc['Q'] . 'agregadora:';
    echo $_SESSION['bool'][$classrow['agreg']];
    if ($classrow['partof']) {
        echo $GBLspc['D'] . 'agregada à: Turma <b>' . $_SESSION['agreg'][$classrow['partof']] . '</b>';
    }
    echo $GBLspc['Q'] . spanformat('', $_SESSION['statuscolor'][$classrow['status_id']] , '(' .  $_SESSION['status'][$classrow['status_id']] . ')');
    echo '</br>';

    if ($classrow['comment']) {
        echo  $GBLspc['T']   . spanformat('smaller' , $GBLcommentcolor  , $classrow['comment']) . '<br>';
    }
    
    $Query = 
        "SELECT * " .
        "FROM classsegment " .
        "WHERE classsegment . class_id = '$classrow[id]' " .
        "ORDER BY day , start ; " ;
        
    $segresult = $GBLmysqli->dbquery($Query);
    while ($segrow = $segresult->fetch_assoc()) {
        formsegmentdisplay($segrow);
        echo '<br>';
    }
    
    echo '<table>';
    echo '<tr style="visibility:collapse"><td>----</td><td>---</td><td>---</td><td>---</td></tr>';
    if ($classrow['scenery']) {
        echo '<tr><td></td><td><b style="color:MidnightBlue;">Cenário(s):</b></td><td></td><td></td></tr>';
        $Query = 
                "SELECT scenery . * " .
                "FROM scenery , sceneryclass " .
                "WHERE scenery . id = sceneryclass . scenery_id " .
                        "AND sceneryclass . class_id = '$classrow[id]' " .
                "ORDER BY scenery . name;";
        
        $scensql = $GBLmysqli->dbquery($Query);
        while ($scenrow = $scensql->fetch_assoc()) {
            echo '<tr><td></td><td></td><td></td><td><b style="color:MidnightBlue;">' . $scenrow['name'] . '</b></td></tr>';
        }
    } else {
        echo '<tr><td></td><td>Cenário:</td><td></td><td>Default</td></tr>';
    }
    echo '</table>';

    if($courseHL){
        echo '<table style="background-color:#FAFAF4;color:#4080A0;"><tr><td>';
    }
    
    $Query = 
        "SELECT vacancies . * , " .
                "unit . acronym , " .
                "unit . id AS courseid " .
        "FROM vacancies , unit " .
        "WHERE vacancies . course_id = unit . id " .
        "AND vacancies . class_id = '$classrow[id]' " .
        "ORDER BY unit . acronym ; " ;
    
    $vacsql = $GBLmysqli->dbquery($Query);
    if ($vacedit && !$classrow['agreg']) {
        echo '<table style="background-color:#E0FFE0;color:#8000B0;"><tr><td>';
        formvacedit($vacsql);
        echo '</td></tr></table>';
    } else {
        formvacdisplay($vacsql);
    }
    
    if($courseHL){
        echo '</td></tr></table>';
    }

    
}
  
  
function formvacedit ($vacsql) {
    global $GBLmysqli;
    global $can_class;
    global $can_addclass;
    global $postedit;
    global $readonly;
    global $GBLpattern;
    global $GBLcommentcolor;
    global $GBLvackind;
    global $GBLspc;
    global $vac_edited;
    
    
    
    echo '<table>';
    while ($vacrow = $vacsql->fetch_assoc()) {
        echo '<tr>';
        $vacid = 'vac' . $vacrow['id'];
        if (($_SESSION['role'][$vacrow['course_id']]['can_vacancies'] | $_SESSION['role']['isadmin'])  & !$readonly) {
            $_SESSION['vacancies'][$vacrow['id']] = $vacid;
            echo '<td>Vagas solicitadas ' . $vacrow['acronym'] . ': ' . 
                formpatterninput(3 , 1 , '[0-9]+' , 'Núm . ' , $vacid . 'asked' , $vacrow['askednum']) ;
            echo $GBLspc['D'] . 'reserv: ' . 
                formpatterninput(3 , 1 , '[0-9]+' , 'Núm . ' , $vacid . 'askedreserv' , $vacrow['askedreservnum']) . 
                $GBLspc['D']  . 
                spanformat('' , '' , $GBLvackind[$vacrow['courseid']]) . $GBLspc['D'];
            echo formselectsession($vacid . 'askedstatusid' , 'status' , $vacrow['askedstatus_id']);
            echo '</td>';
        } else {
            echo formhiddenval($vacid . 'asked' , $vacrow['askednum']);
            echo formhiddenval($vacid . 'askedreserv' , $vacrow['askedreservnum']);
            echo formhiddenval($vacid . 'askedstatusid' , $vacrow['askedstatus_id']);
            echo '<td>Vagas solicitadas ' . $vacrow['acronym'] . ': ' .  $vacrow['askednum'] . 
                ' (+' . $vacrow[askedreservnum] . ') '  . 
                '<sub>' . 
                spanformat('smaller' , '' , $GBLvackind[$vacrow['courseid']]). 
                '</sub>' . 
                $GBLspc['Q'] . 
                spanformat('', $_SESSION['statuscolor'][$vacrow['askedstatus_id']] , '(' .  $_SESSION['status'][$vacrow['askedstatus_id']] . ')') . 
                '</td>';
        }
        if (($_SESSION['role'][$_POST['unitid']]['can_vacancies'] | $_SESSION['role']['isadmin']) & !$readonly) {
            $vac_edited = true;
            $_SESSION['vacancies'][$vacrow['id']] = $vacid;
            echo '<td>' . $GBLspc['D'] . 'Vagas concedidas: '  .   formpatterninput(3 , 1 , '[0-9]+' , 'Núm.' , $vacid . 'given' , $vacrow['givennum']);
            echo $GBLspc['D'] . 'reserv: ' . formpatterninput(3 , 1 , '[0-9]+' , 'Núm.' , $vacid . 'givenreserv' , $vacrow['givenreservnum']);
            echo formselectsession($vacid . 'givenstatusid' , 'status' , $vacrow['givenstatus_id']);
            echo '</td>';
        } else {
            echo formhiddenval($vacid . 'given' , $vacrow['givennum']);
            echo formhiddenval($vacid . 'givenreserv' , $vacrow['givenreservnum']);
            echo formhiddenval($vacid . 'givenstatusid' , $vacrow['givenstatus_id']);
            echo '<td>' . $GBLspc['D'] . 'Vagas concedidas: ' . 
                $vacrow['givennum'] . ' (+' . $vacrow[givenreservnum] . ') ' . 
                $GBLspc['Q'] . 
                spanformat('', $_SESSION['statuscolor'][$vacrow['givenstatus_id']]  , '(' .  
                           $_SESSION['status'][$vacrow['givenstatus_id']] . ')') . 
                '</td>';
        }
        echo '<td>' . $GBLspc['D'] . 'Vagas Ocupadas: ' . $vacrow['usednum'] . ' (+' . $vacrow['usedreservnum'] . ')</td>';
        echo '</tr>';
        if (($_SESSION['role'][$vacrow['course_id']]['can_vacancies'] | $_SESSION['role']['isadmin'])  & !$readonly) {
            $vac_edited = true;
            $_SESSION['vacancies'][$vacrow['id']] = $vacid;
            echo '<tr>';
            echo '<td>Obs.: ' . formpatterninput(48 , 16 , $GBLpattern['comment'] , 'Obs.' , $vacid . 'comment' , $vacrow['comment']) . '</td>';
            echo '<td></td></tr>';
        } else {
            echo formhiddenval($vacid . 'comment' , $vacrow['comment']);
            echo '<tr><td>' .  $GBLspc['T']  . spanformat('smaller' , $GBLcommentcolor , $vacrow['comment']) . '</td><td></td></tr>';
        }
      
    }
    echo '</table>';
}
  
function formvacdisplay ($vacsql) {
    global $GBLmysqli;
    global $can_class;
    global $can_addclass;
    global $postedit;
    global $GBLcommentcolor;
    global $GBLvackind;
    global $GBLspc;
    
    echo '<table>';
    $totalasked = 0;
    $totalreserv = 0;
    $totalgiven = 0;
    $totalgivenreserv = 0;
    $totalused = 0;
    $totalusedreserv = 0;
    while ($vacrow = $vacsql->fetch_assoc()) {
        echo '<tr>';
        if ($GBLvackind[$vacrow['courseid']] == 'OB') {
            $coursecolor = '#0000F0';
            $coursebold = true;
        } else {
            $coursecolor = null;
            $coursebold = false;
        }
        echo '<td>Vagas solicitadas ' . $vacrow['acronym'] . ': ' .  
            $vacrow['askednum'] . ' (+' . $vacrow['askedreservnum'] . ') ' . '<sub>' . spanformat('smaller' , $coursecolor , $GBLvackind[$vacrow['courseid']] , null , $coursebold). '</sub>'. 
            $GBLspc['Q'] . 
            spanformat('' , $_SESSION['statuscolor'][$vacrow['askedstatus_id']], '(' .  
                       $_SESSION['status'][$vacrow['askedstatus_id']] . ')') . 
            '</td>';
        echo '<td>' . $GBLspc['D'] . 'Vagas concedidas: ' . 
            $vacrow['givennum'] .  ' (+' . $vacrow['givenreservnum'] . ') ' . 
            $GBLspc['Q'] . 
            spanformat('', $_SESSION['statuscolor'][$vacrow['givenstatus_id']] , '(' .  
                       $_SESSION['status'][$vacrow['givenstatus_id']] . ')') .  
            '</td>';
                                
        echo '<td>Vagas Ocupadas: ' . $vacrow['usednum'] .  ' (+' . $vacrow['usedreservnum'] . ') ' . '</td>';

        echo '</tr>';
        if(!(($_SESSION['status'][$vacrow['askedstatus_id']] == 'dup') | ($_SESSION['status'][$vacrow['givenstatus_id']] == 'dup'))) {
            $totalasked += $vacrow['askednum'];
            $totalreserv += $vacrow['askedreservnum'];
            $totalgiven += $vacrow['givennum'];      
            $totalgivenreserv += $vacrow['givenreservnum'];      
            $totalused += $vacrow['usednum'];      
            $totalusedreserv += $vacrow['usedreservnum'];      
        }
        if ($vacrow['comment']) {
            echo '<tr><td>' .  $GBLspc['T']  . spanformat('smaller' , $GBLcommentcolor , $vacrow['comment']) . '</td><td></td></tr>';
        }
    }
    echo '<tr><td>' . spanformat('' , 'darkblue' , 'Total: ' . $totalasked  . ' (+' . $totalreserv . ')') . '</td><td>' . $GBLspc['D'] . '' . spanformat('' , 'darkblue' , 'Total: ' . $totalgiven . ' (+' . $totalgivenreserv . ')') . '</td><td>' . spanformat('' , 'darkblue' , 'Total: ' . $totalused . ' (+' . $totalusedreserv . ')') . '</td></tr>';
    echo '</table>';
    
}
  


function agregupdt($aclassid) {
    global $GBLmysqli;

    $Query = 
        "SELECT `cd` . `course_id` " .
        "FROM `coursedisciplines` AS `cd` " .
        "WHERE `cd` . `discipline_id` =  '$_POST[discid]'  ; " ;
        
    $aclasssql = $GBLmysqli->dbquery($Query);
    while($aclassrow = $aclasssql->fetch_assoc()) {
        $Query = 
                "SELECT  SUM(`vac` . `givennum`) AS `givensum` , " .
                        "SUM(`vac` . `givenreservnum`) AS `givenreservsum` , " .
                        "SUM(`vac` . `askednum`) AS `askedsum` , " .
                        "SUM(`vac` . `askedreservnum`) AS `askedreservsum` " .
                "FROM `vacancies` AS `vac` , `class` "  . 
                "WHERE `vac` . `class_id` = `class` . `id` " .
                        "AND `class` . `partof` = '$aclassid' " .
                        "AND `vac` . `course_id` = '$aclassrow[course_id]' ; " ;
                        
        $sumsql = $GBLmysqli->dbquery($Query);
        $sumrow = $sumsql->fetch_assoc();
        $Query = 
                "UPDATE `vacancies`  " .
                "SET `givennum` = '$sumrow[givensum]' , " .
                        "`givenreservnum` = '$sumrow[givenreservsum]' , " .
                        "`askednum` = '$sumrow[askedsum]' , " .
                        "`askedreservnum` = '$sumrow[askedreservsum]' " .
                "WHERE `class_id` = '$aclassid' " .
                "AND `course_id` = '$aclassrow[course_id]' ; " ;
                
        $GBLmysqli->dbquery($Query);
    }
}


// Main DB functions
function repclassinscen ($classlog) {
    global $GBLmysqli;
    global $newclassid;
        
    if ($_POST['addclass'] == 1) {
        if ($_POST['newclassname']) {
            $Query = 
                "INSERT INTO `class` (`name` , `sem_id` , `discipline_id` , `scenery` , `comment`) " .
                "VALUES ( '$_POST[newclassname]'  ,  '$_POST[semid]'  ,  '$_POST[discid]'  , '1' , 'cópia de $_POST[orgclassname]' ) ; " ;
                
            $classlog['action'] = $classlogaction . 'class ADD';
            $classlog['datanew'] = 'Turma : ' . $_POST['newclassname']  . ' added';
            if($GBLmysqli->dbquery($Query)) {
                $newclassid = $GBLmysqli->insert_id;
                $GBLmysqli->eventlog($classlog);
              
                $Query = 
                        "INSERT INTO `classsegment` (`class_id` , `day` , `start` , `length` , `room_id` , `prof_id`) " . 
                        "SELECT '$newclassid' , `cs` . `day` , `cs` . `start` , `cs` . `length` , `cs` . `room_id` , `cs` . `prof_id` " . 
                                "FROM  `classsegment` AS `cs` " . 
                                "WHERE `cs` . `class_id` =  '$_POST[classid]'  ; " ;
                $GBLmysqli->dbquery($Query);
              
                $Query = 
                "INSERT INTO `vacancies` (`class_id` , `course_id` , `askednum` , `askedreservnum` , `givennum` , `givenreservnum`) " . 
                    "SELECT '$newclassid' , `vc` . `course_id` , `vc` . `askednum` , `vc` . `askedreservnum` , `vc` . `givennum` , `vc` . `givenreservnum`  " . 
                    "FROM  `vacancies` AS `vc` " . 
                    "WHERE `vc` . `class_id` =  '$_POST[classid]' ; " ;
                $GBLmysqli->dbquery($Query);
              
                $Query = 
                        "INSERT INTO `sceneryclass` (`class_id` , `scenery_id`) " .
                        "VALUES ('$newclassid' ,  '$_POST[addscenery]' ) ; " ;
                $GBLmysqli->dbquery($Query);
                $_POST['classid'] = $newclassid;
            }
        }
    }
 
}
 
function addclassinscen ($classlog) {
    global $GBLmysqli;
    global $newclassid;
    if ($_POST['addclass'] == 1) {
        if ($_POST['newclassname']) {
            $Query = 
                "INSERT INTO `class` (`name` , `sem_id` , `discipline_id` , `scenery`) " .
                "VALUES ( '$_POST[newclassname]'  ,  '$_POST[semid]'  ,  '$_POST[discid]'  , '1' ) ; " ;
                
            $classlog['action'] = $classlogaction . 'class ADD';
            $classlog['datanew'] = 'Turma : ' . $_POST['newclassname']  . ' added';
            if($GBLmysqli->dbquery($Query)) {
                $newclassid = $GBLmysqli->insert_id;
                $GBLmysqli->eventlog($classlog);
                $Query = 
                        "SELECT * " .
                        "FROM coursedisciplines " .
                        "WHERE discipline_id =  '$_POST[discid]' ; " ;
                        
                $Queryresult = $GBLmysqli->dbquery($Query);
                while ($sqlrow = $Queryresult->fetch_assoc()) {
                    $Query = 
                        "INSERT INTO `vacancies` (`class_id` , `course_id`) " .
                        "VALUES ('$newclassid' , '$sqlrow[course_id]' ) ; " ; // defaults are zero . 
                        
                    $classlog['action'] = $classlogaction . 'vac UPDATE/ADD';
                    $GBLmysqli->dbquery($Query , $classlog);
                }
                $Query = 
                        "INSERT INTO `sceneryclass` (`class_id` , `scenery_id`) " .
                        "VALUES ('$newclassid' ,  '$_POST[addscenery]' ) ; " ;
                        
                $GBLmysqli->dbquery($Query);
                $_POST['classid'] = $newclassid;
            }
        }
    }

}

function addclass ($classlog) {
    global $GBLmysqli;
    global $newclassid;

    if ($_POST['addclass'] == 1) {
        if ($_POST['newclassname']) {
            $Query = 
                "INSERT INTO `class` (`name` , `sem_id` , `discipline_id`) " .
                "VALUES ( '$_POST[newclassname]'  ,  '$_POST[semid]'  ,  '$_POST[discid]' ) ; " ;
                
            $classlog['action'] = $classlogaction . 'class ADD';
            $classlog['datanew'] = 'Turma : ' . $_POST['newclassname']  . ' added';
            if($GBLmysqli->dbquery($Query)) {
                $newclassid = $GBLmysqli->insert_id;
                $GBLmysqli->eventlog($classlog);
                $Query = 
                        "SELECT * " .
                        "FROM coursedisciplines " .
                        "WHERE discipline_id =  '$_POST[discid]' ; " ;
                        
                $Queryresult = $GBLmysqli->dbquery($Query);
                while ($sqlrow = $Queryresult->fetch_assoc()) {
                    $Query = 
                        "INSERT INTO `vacancies` (`class_id` , `course_id`) " .
                        "VALUES ('$newclassid' , '$sqlrow[course_id]') ; " ;
                        
                    $classlog['action'] = $classlogaction . 'vac UPDATE/ADD';
                    $GBLmysqli->dbquery($Query , $classlog);
                    $_POST['classid'] = $newclassid;
                }
            }
        }
    }
}


function repclass ($classlog) {
    global $GBLmysqli;
    global $newclassid;
    if ($_POST['addclass'] == 1) {
        if ($_POST['newclassname']) {
            $Query = 
                "INSERT INTO `class` (`name` , `sem_id` , `discipline_id` , `comment`) " .
                "VALUES ( '$_POST[newclassname]'  ,  '$_POST[semid]'  ,  '$_POST[discid]'  , 'cópia de $_POST[orgclassname]') ; " ;
                
            $classlog['action'] = $classlogaction . 'class ADD';
            $classlog['datanew'] = 'Turma : ' . $_POST['newclassname']  . ' added';
            if($GBLmysqli->dbquery($Query)) {
                $newclassid = $GBLmysqli->insert_id;
                $GBLmysqli->eventlog($classlog);
              
                $Query =         
                        "INSERT INTO `classsegment` (`class_id` , `day` , `start` , `length` , `room_id` , `prof_id`) " . 
                                "SELECT '$newclassid' , `cs` . `day` , `cs` . `start` , `cs` . `length` , `cs` . `room_id` , `cs` . `prof_id` " . 
                                "FROM  `classsegment` AS `cs` " . 
                                "WHERE `cs` . `class_id` =  '$_POST[classid]'  ; " ;
                                
                $GBLmysqli->dbquery($Query);
              
                $Query = 
                        "INSERT INTO `vacancies` (`class_id` , `course_id` , `askednum` , `askedreservnum` , `givennum` , `givenreservnum`) " . 
                                "SELECT '$newclassid' , `vc` . `course_id` , `vc` . `askednum` , `vc` . `askedreservnum` , `vc` . `givennum`  , `vc` . `givenreservnum` "  . 
                                "FROM  `vacancies` AS `vc` " . 
                                "WHERE `vc` . `class_id` =  '$_POST[classid]' ; " ;
                                
                $GBLmysqli->dbquery($Query);
                $_POST['classid'] = $newclassid;
            }
        }
    }

}


function classsubmit ($classlog) {
    global $GBLmysqli;

    //        $GBLmysqli->eventlog(array('level'=>'INFO' , 'action'=>'class edit' , 'str'=>displaysqlitem('' , 'semester' , $_POST['semid'] , 'name') . displaysqlitem('' , 'discipline' , $_POST['discid'] , 'code') , 'xtra'=>'editclass.php'));
    //vardebug($_POST);

    foreach ($_SESSION['segments'] as $segid => $segkey) {
        if (($_POST[$segkey . 'delete'])) {
            $Query = 
                "DELETE FROM `classsegment` " .
                "WHERE `id` = '$segid' ; " ;
            $classlog['action'] = $classlogaction . 'segment DELETE';
            $classlog['dataorg'] = $_SESSION['weekday'][$_SESSION['org'][$segkey . 'day']] . ' ' . 
                $_SESSION['org'][$segkey . 'start'] . ':30 (' . $_SESSION['org'][$segkey . 'length'] . ') sala: ' . 
                $_SESSION['rooms'][$_SESSION['org'][$segkey . 'room']]['txt'] . ' - ' . 
                $_SESSION['deptprof' . $_POST['unitid']][$_SESSION['org'][$segkey . 'prof']] . ' (' . 
                $_SESSION['status'][$_SESSION['org'][$segkey . 'status']]  . ')';
            $classlog['datanew'] = '';
            $GBLmysqli->dbquery($Query , $classlog);
        } else {
            $compfields = array('day' , 'start' , 'length' , 'room' , 'prof' , 'status') ;
            if (fieldscompare($segkey , $compfields)) {
                foreach ($compfields as $field) {
                    $keypost[$field] = $_POST[$segkey . $field] ;
                }
                $Query = 
                        "UPDATE `classsegment` " .
                        "SET `day` = '$keypost[day]' , " . 
                                "`start` = '$keypost[start]'  , " .
                                "`length` = '$keypost[length]' , " .
                                "`room_id` = '$keypost[room]' , " .
                                "`prof_id` = '$keypost[prof]' , " .
                                "`status_id` = '$keypost[status]' " .
                        "WHERE `id` = '$segid' ; " ;
                        
                $classlog['action'] = $classlogaction . 'segment UPDATE';
                $classlog['dataorg'] = $_SESSION['weekday'][$_SESSION['org'][$segkey . 'day']] . ' ' . 
                    $_SESSION['org'][$segkey . 'start'] . ':30 (' . $_SESSION['org'][$segkey . 'length'] . ') sala: ' . 
                    $_SESSION['rooms'][$_SESSION['org'][$segkey . 'room']]['txt'] . ' - ' . 
                    $_SESSION['deptprof' . $_POST['unitid']][$_SESSION['org'][$segkey . 'prof']] . ' (' . 
                    $_SESSION['status'][$_SESSION['org'][$segkey . 'status']]  . ')';
                $classlog['datanew'] = $_SESSION['weekday'][$_POST[$segkey . 'day']] . ' ' . 
                    $_POST[$segkey . 'start'] . ':30 (' . 
                    $_POST[$segkey . 'length'] . ') sala: ' . 
                    $_SESSION['rooms'][$_POST[$segkey . 'room']]['txt'] . ' - ' . 
                    $_SESSION['deptprof' . $_POST['unitid']][$_POST[$segkey . 'prof']] . ' (' . 
                    $_SESSION['status'][$_POST[$segkey . 'status']]  . ')';
                $GBLmysqli->dbquery($Query , $classlog);
            }
        }
    }
    $anyone = 0;
    foreach ($_SESSION['vacancies'] as $vacid => $vackey) {
        $compfields = array('asked' , 'askedreserv' , 'askedstatusid' , 'given' , 'givenreserv' , 'givenstatusid' , 'comment') ;
        if (fieldscompare($vackey , $compfields)) {
            $anyone = 1;
            foreach ($compfields as $field) {
                $keypost[$field] = $_POST[$vackey . $field] ;
            }
            $Query = 
                "UPDATE `vacancies` " . 
                "SET `askednum` = '$keypost[asked]' , " . 
                        "`askedreservnum`= '$keypost[askedreserv]' , " . 
                        "`askedstatus_id`= '$keypost[askedstatusid]' , " . 
                        "`givennum`= '$keypost[given]' , " . 
                        "`givenreservnum`= '$keypost[givenreserv]' , " . 
                        "`givenstatus_id`= '$keypost[givenstatusid]' , " . 
                        "`comment`= '$keypost[comment]'   " .
                "WHERE `id` = '$vacid' ; " ;
                
            $classlog['action'] = $classlogaction . 'vac UPDATE';
            $classlog['dataorg'] = 'asked: ' . $_SESSION['org'][$vackey . 'asked'] . ' (' . $_SESSION['status'][$_SESSION['org'][$vackey . 'askedstatusid']] . ') '  . 
                'given: ' . $_SESSION['org'][$vackey . 'given'] . ' (' . $_SESSION['status'][$_SESSION['org'][$vackey . 'givenstatusid']] . ') '  . 
                $_SESSION['org'][$vackey . 'comment'] ;
            $classlog['datanew'] = 'asked: ' . $_POST[$vackey . 'asked'] . ' (' . $_SESSION['status'][$_POST[$vackey . 'askedstatusid']] . ') '  . 
                'given: ' . $_POST[$vackey . 'given'] . ' (' . $_SESSION['status'][$_POST[$vackey . 'givenstatusid']] . ') '  . 
                $_POST[$vackey . 'comment'] ;
            $GBLmysqli->dbquery($Query , $classlog);
        }
    }
    $segadded = 0;

    foreach ($_SESSION['classes'] as $classid => $classkey) {
        if (($_POST[$classkey . 'delete'])) {
            $Query = 
                "DELETE FROM `class` " .
                "WHERE `id` = '$classid' ; " ;
                
            $classlog['action'] = $classlogaction . 'class DELETE';
            $classlog['dataorg'] = 'Turma : ' . $_POST['classname'] . ' deleted';
            $classlog['datanew'] = '';
            $GBLmysqli->dbquery($Query , $classlog);
        } else {
            if ($_POST[$classkey . 'agreg']) {
                $_POST[$classkey . 'partof'] = 0;
            }
            
            $compfields = array('classname');
            if (fieldscompare($classkey , $compfields)) {
                foreach ($compfields as $field) {
                    $keypost[$field] = $_POST[$classkey . $field] ;
                }
                $Query = 
                        "UPDATE `class` " .
                        "SET `name`= '$keypost[classname]' " .
                        "WHERE `id`= '$classid' ; " ;
                $GBLmysqli->dbquery($Query);
            }
            $compfields = array('agreg' , 'status' , 'comment' , 'partof' , 'scenerybool');
            if (fieldscompare($classkey , $compfields)) {
                foreach ($compfields as $field) {
                    $keypost[$field] = $_POST[$classkey . $field] ;
                }
                
                //echo 'tjam<br>';
                if ($_POST[$classkey . 'partof']) {
                    $part = "`partof`= '" . $_POST[$classkey . 'partof'] . "' ";
                } else {
                    $part = "`partof`= NULL ";
                }
                $Query = 
                        "UPDATE `class` " . 
                        "SET `agreg`= '$keypost[agreg]' , " . 
                                "`status_id`= '$keypost[status]' , " . 
                                "`comment`= '$keypost[comment]' , " . 
                                "`scenery`= '$keypost[scenerybool]' , " . 
                                $part . 
                                "WHERE `id`= '$classid' ; " ;
                                
                $classlog['action'] = $classlogaction . 'class agreg UPDATE';
                $classlog['dataorg'] = 'agreg: ' . $_SESSION['bool'][$_SESSION['org'][$classkey . 'agreg']] . ' (' . $_SESSION['status'][$_SESSION['org'][$classkey . 'statusid']] . ') '  . 
                    $_SESSION['org'][$classkey . 'comment'] ;
                $classlog['datanew'] = 'agreg: ' . $_SESSION['bool'][$_POST[$classkey . 'agreg']] . ' (' . $_SESSION['status'][$_POST[$classkey . 'statusid']] . ') '  . 
                    $_POST[$classkey . 'comment'] ;
    
                $GBLmysqli->dbquery($Query , $classlog);
                if ($_SESSION['org'][$classkey . 'agreg']  & !$_POST[$classkey . 'agreg']) {
                    $Query = 
                        "UPDATE `class` " . 
                        "SET `partof` = NULL " . 
                        "WHERE `partof` = '$classid' ; " ;
                        
                    $classlog['action'] = $classlogaction . 'class partof UPDATE';
                    $classlog['dataorg'] = '';
                    $classlog['datanew'] = 'vac cleanup';                
                    $GBLmysqli->dbquery($Query , $classlog);
                }
                // TODO : REVIEW THIS... might be better to also zero reservs ...
                if (!$_SESSION['org'][$classkey . 'agreg']  & $_POST[$classkey . 'agreg']) {
                    $Query = 
                        "UPDATE `vacancies` " . 
                        "SET `givennum` = '0' , " . 
                        "`askednum` = '0' " . 
                        "WHERE `class_id` = '$classid' ; " ;
                        
                    $classlog['action'] = $classlogaction . 'vac UPDATE';
                    $classlog['dataorg'] = '';
                    $classlog['datanew'] = 'vac cleanup';                
                    $GBLmysqli->dbquery($Query , $classlog);
                }
            }

            if ($_POST[$classkey . 'scenerybool']) {
                //echo 'here<br>';
                foreach ($_SESSION['org']['sceneryusr'] as $session_sceneryid => $session_sceneryclassid) {
                
                    $postscenery = $_POST[$classkey . 'scenery' . $session_sceneryid];
                    //echo 'id:' . $session_sceneryid . 'clsid:' . $session_sceneryclassid . 'post:' . $postscenery. '<br>';
                    if ($session_sceneryclassid && !$postscenery) {
                        //echo 'delete<br>';
                        $Query = 
                                "DELETE FROM `sceneryclass` " . 
                                "WHERE `id` = '$session_sceneryclassid' ; " ;
                        $GBLmysqli->dbquery($Query , $classlog);
                        //remove
                    }
                    if (!$session_sceneryclassid && $postscenery) {
                        //echo 'insert<br>';
                        $Query = 
                                "INSERT INTO `sceneryclass` (`class_id` , `scenery_id`) " . 
                                "VALUES ('$classid' , '$postscenery') ; " ;
                        $GBLmysqli->dbquery($Query , $classlog);
                        // insert 
                    }
                }
            } else {
                //echo '...<br>';
                if ($_SESSION['org'][$classkey . 'scenerybool']) {
                    //echo $classid . '<br>';
                    $Query = 
                        "DELETE FROM `sceneryclass` " . 
                        "WHERE class_id = '$classid' ; " ;
                    $GBLmysqli->dbquery($Query , $classlog);
                }
            }

            if (($_POST[$classkey . 'addsegment'])) {
                reset($_SESSION['deptprof' . $_POST['unitid']]);
                $classlog['action'] = $classlogaction . 'segment ADD';
                $classlog['dataorg'] = '';
                $classlog['datanew'] = 'default segment added';                
                $segday = 2;
                $segstart = 7;
                $addsegcnt = $_POST[$classkey . 'addsegment'];
                while ($addsegcnt > 0 ) {
                    do {
                        $Query = 
                                "INSERT INTO `classsegment` (`day` , `start` , `length` , `room_id` , `prof_id` , `class_id`) " .
                                "VALUES ('$segday' , '$segstart' , '2' , '1' , '" . key($_SESSION['deptprof' . $_POST['unitid']]) . "' , '$classid') ; " ;
                        $segstart += 1;
                    } while (!  $GBLmysqli->trydbquery($Query , $classlog));
                    $segday++;
                    $segstart = 7;
                    //echo ' cnt: ' . $addsegcnt;
                    $addsegcnt--;
                };
                $segadded = 1;
            }
        }
        if($_POST[$classkey . 'delete'] ) {
            if($_SESSION['org'][$classkey . 'partof']) {
                agregupdt($_SESSION['org'][$classkey . 'partof']);
            }
        } else {
            if ($_POST[$classkey . 'partof'] == $_SESSION['org'][$classkey . 'partof']) {
                if($anyone) {
                    agregupdt($_SESSION['org'][$classkey . 'partof']);
                }
            } else {
                if($_POST[$classkey . 'partof']) {
                    // vacancie adjust ...'new' partof 
                    agregupdt($_POST[$classkey . 'partof']);
                }
                if($_SESSION['org'][$classkey . 'partof']) {
                    agregupdt($_SESSION['org'][$classkey . 'partof']);
                }
            }
        }
    }
    if(!$segadded) {
//        $_POST['classid'] = null;
        $_POST['act'] = 'SubDisplay';
    }

}

?>
