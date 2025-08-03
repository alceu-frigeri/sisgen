
<?php 
include 'bailout.php';



$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['edclass'];
formretainvalues(array('semid' , 'unitid' , 'discid' , 'profnicks' , 'courseHL'));

include 'coreedit.php';
  
$class_edited = false;
$vac_edited = false;
        
//vardebug($_SESSION['pagelnk'],'page link');

$Query = "SELECT readonly FROM semester WHERE id =  '$_POST[semid]' ;";
$Queryresult = $GBLmysqli->dbquery($Query);
$sqlrow = $Queryresult->fetch_assoc();
$readonly = $sqlrow['readonly'];
$hiddenprofdeptid = null;
$hiddenroombuildingid = null;

$can_class = ($_SESSION['role'][$_POST['unitid']]['can_class'] || $_SESSION['role']['isadmin']) && !$readonly;
$can_addclass = ($_SESSION['role'][$_POST['unitid']]['can_addclass'] || $_SESSION['role']['isadmin']) && !$readonly;
  
$can_something = ($can_class || $can_addclass || $_SESSION['usercanscen']) && !$readonly ;


echo '<div class = "row">' .
    '<h2>Turmas</h2>' .
    '<hr>' ;


// TODO , TO BE REVIEWED !!! 


$postedit = false;

if ((($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit') )  && $can_something ) {
    $postedit = true;
} else {
    if ((($_POST['act'] == 'Add Class') | ($_POST['act'] == 'Replicate Class') ) & $can_addclass) {
        $postedit = true;
    } else {
        if ((($_POST['act'] == 'Add Class in Scenery') | ($_POST['act'] == 'Replicate Class in Scenery') ) && $can_something ) {
            $postedit = true;
        } else {
            $_POST['act'] = 'Cancel';
        }
    }
}

  

//$GBLpattern['class'] = '[A-Z][A-Za-z0-9\*]*';

$GBLvackind = array();
$Query = 
        "SELECT `kind` . `code` , " .
                "`cd` . `course_id` , " .
                "`term` . `code` AS `trm` , " .
                "`term` . `id` AS `termid` " .
        "FROM `disciplinekind` AS `kind` , " .
                "`coursedisciplines` AS `cd` , " .
                "`term` " .
        "WHERE `cd` . `discipline_id` = '$_POST[discid]' " .
                "AND `cd` . `disciplinekind_id` = `kind` . `id` " .
                "AND `cd` . `term_id` = `term` . `id` ; " ;

$kindsql = $GBLmysqli->dbquery($Query);
while($kindrow = $kindsql->fetch_assoc()) {
    if ($kindrow['code'] == 'OB') {
        $kba = '<b>';
        $kbb = '</b>';
    } else {
        $kba = null;
        $kbb = null;
    }
    $GBLvackind[$kindrow['course_id']] = $kba . hiddenformlnk(hiddencoursekey($_POST['semid'] , $kindrow['course_id'] , $kindrow['termid']) , $kindrow['code'] . '-' . $kindrow['trm']) . $kbb;
    
    echo hiddencourseform($_POST['semid'] , $kindrow['course_id'] , $kindrow['termid']);
}
  
$incanview = inscenery_sessionlst('scen.acc.view');
list($qscentbl , $qscensql) = scenery_sql($incanview);   //TODO HERE... acc.view

$incanedit = inscenery_sessionlst('scen.acc.edit');


if (!$readonly) {
    $classlogaction = 'class edit (' . $_POST['act'] . ') ';
    $classlog = array('level'=>'INFO' , 'action'=> $classlogaction , 'str'=>displaysqlitem('' , 'semester' , $_POST['semid'] , 'name') . displaysqlitem('' , 'discipline' , $_POST['discid'] , 'code') , 'xtra'=>'editclass.php');
    switch ($_POST['act']){
    case 'Submit':
        classsubmit($classlog) ;
        break;

    case 'Add Class':
        addclass($classlog) ;
        break;

    case 'Replicate Class':
        repclass($classlog) ;
        break;

    case 'Add Class in Scenery':
        addclassinscen($classlog) ;
        break;
      
    case 'Replicate Class in Scenery':
        repclassinscen($classlog);
        break;
    }
}
  
unset($_SESSION['segments']);
unset($_SESSION['vacancies']);
unset($_SESSION['classes']);
unset($_SESSION['agreg']);
unset($_SESSION['org']);



if ($postedit) {
    thisformpost();

    echo displaysqlitem('Semestre: ' , 'semester' , $_POST['semid'] , 'name');
    echo displaysqlitem($GBLspc['D'] . 'Dept . : ' , 'unit' , $_POST['unitid'] , 'acronym');
    echo '<br><b>';
    echo displaysqlitem($GBLspc['D'] , 'discipline' , $_POST['discid'] , 'code' , 'name');
    echo '</b>';
    echo formsubmit('act' , 'Cancel');
    echo spanformat('smaller' , $GBLcommentcolor, displaysqlitem($GBLspc['D'] , 'discipline' , $_POST['discid'] , 'comment')) ;

    echo formhiddenval('profnicks' , $_POST['profnicks']);
    echo formhiddenval('courseHL' , $_POST['courseHL']);


    [$SCinselected , $SCquery] = sceneryclasshack($_POST['profnicks'] , false);
    if($_POST['courseHL']) {
        echo displaysqlitem('<br>Realçando: ' , 'unit' , $_POST['courseHL'] , 'name');
    }

    echo  '</form>';
} else {
    echo formpost($thisform);

    $Query = 
        "SELECT * " .
        "FROM semester " .
        "ORDER BY name DESC;" ; 
    echo formselectsql($anytmp , $Query , 'semid' , $_POST['semid'] , 'id' , 'name');
    
    $Query = 
        "SELECT * " .
        "FROM unit  " .
        "ORDER BY unit . mark DESC , unit . iscourse ASC , unit . acronym ASC;" ;
    echo formselectsql($anytmp , $Query , 'unitid' , $_POST['unitid'] , 'id' , 'acronym');
    
    $Query = 
        "SELECT * " .
        "FROM discipline " .
        "WHERE discipline . dept_id =  '$_POST[unitid]'  " .
        "ORDER BY name;" ;
    echo formselectsql($anytmp , $Query , 'discid' , $_POST['discid'] , 'id' , 'code' , 'name');
                  
    [$SCinselected , $SCquery] = sceneryclasshack($_POST['profnicks']);

    
    echo "Nome Profs? ";
    echo formselectsession('profnicks' , 'bool' , $_POST['profnicks'] , false , true);

    $Query = 
        "SELECT `course` . * " .
        "FROM `unit` AS `course` , " .
                "coursedisciplines AS `cdisc` " .
        "WHERE `cdisc` . `discipline_id` =  '$_POST[discid]'  " .
                "AND `cdisc` . `course_id` = `course` . `id` " .
        "ORDER BY name;" ; 
    echo $GBLspc['D'] . "Realçar curso: ";
    echo formselectsql($anytmp , $Query , 'courseHL' , $_POST['courseHL'] , 'id' , 'name');
  
    $Query = 
        "SELECT * " .
        "FROM `discipline` " .
        "WHERE `dept_id` = '$_POST[unitid]' " .
                "AND `id` = '$_POST[discid]' ; " ;
// 
    if (!$readonly) {
        if (testpostsql( array('semid' , 'unitid' ) , $Query) && $can_something) {
            echo  $GBLspc['D'] . formsubmit('act' , 'Edit') . '</form>';
        } else {
            echo  '</form>';
        }
    } else {
        echo  '</form>';

    }

}

$Tquery = 
        "SELECT * " .
        "FROM discipline " . 
        "WHERE id =  '$_POST[discid]'  " .
                "AND dept_id =  '$_POST[unitid]'  ; " ;
if(testpostsql( array('semid' , 'unitid' , 'discid') , $Tquery)) {
       
        echo dbweekmatrix($SCquery , $SCinselected , null , null , false , true , $_POST['courseHL']);
        echo '<hr>';
        
        $Query = 
                "SELECT class . * " .
                "FROM class , discipline " .
                "WHERE class . discipline_id = discipline . id " .
                        "AND class . discipline_id =  '$_POST[discid]'  " .
                        "AND class . sem_id =  '$_POST[semid]'  " .
                        "AND discipline . dept_id =  '$_POST[unitid]'  " .
                        "AND class . agreg = '1' " .
                "ORDER BY class . name; " ;
        
        $Queryresult = $GBLmysqli->dbquery($Query);
        
        unset($_SESSION['agreg']);
        while ($sqlrow = $Queryresult->fetch_assoc()) {
            $_SESSION['agreg'][$sqlrow['id']] = $sqlrow['name'];
        }  
                
        if ($can_class) {
            $Query = 
                "SELECT DISTINCT class . * " .
                "FROM `class` , `discipline` " . 
                "WHERE class . discipline_id = discipline . id " .
                        "AND class . discipline_id =  '$_POST[discid]'  " . 
                        "AND class . sem_id =  '$_POST[semid]'  " . 
                        "AND discipline . dept_id =  '$_POST[unitid]' " . 
                "ORDER BY  `class` . `name` ; "  ;
        } else {
            $Query = 
                "SELECT DISTINCT class . * " .
                "FROM `class` , `discipline` " . 
                        $qscentbl . 
                "WHERE class . discipline_id = discipline . id " . 
                        "AND class . discipline_id =  '$_POST[discid]'  " . 
                        "AND class . sem_id =  '$_POST[semid]'  " . 
                        "AND discipline . dept_id =  '$_POST[unitid]'  " . 
                        $qscensql . " ; " ;
        }
        $Queryresult = $GBLmysqli->dbquery($Query);
          
        while ($classrow = $Queryresult->fetch_assoc()) {
            if ($postedit) {
                echo hiddendivkey('class' , $classrow['id']);
                if($classrow['id'] == $newclassid) {
                    echo hiddendivkey('class' , 'NEW');
                }
                thisformpost('class' . $classrow['id'] . 'div');
                echo formhiddenval('classid' , $classrow['id']);
                echo formhiddenval('classname' , $classrow['name']);
                if ( $classrow['id'] == $_POST['classid'] ) {
                
                    if ($_POST['act'] == 'SubDisplay') {
                            echo HLbegin();
                            echo formsubmit('act' , 'Cancel');
                            echo formsubmit('act' , 'Edit');
                            formclassdisplay($classrow);
                            echo HLend();
                    } else {
                            if($can_class) {
                                formclassedit($classrow , $incanedit);
                            } else {
                                // TODO: verify if can edit scenery
                                $qtestsql = 
                                        "SELECT * " .
                                        "FROM `sceneryclass` " .
                                        "WHERE `sceneryclass` . `class_id` = '$classrow[id]' " . 
                                        "AND `sceneryclass` . `scenery_id` IN ( $incanedit ) ; " ;
                                $qtestresult = $GBLmysqli->dbquery($qtestsql);
                                if ($qtestresult->num_rows) {
                                    formclassedit($classrow , $incanedit , true);
                                } else {
                                    formclassdisplay($classrow , true);
                                }
                            }
                            if ($class_edited || $vac_edited) {
                            echo formsubmit('act' , 'Submit');
                            echo formsubmit('act' , 'Cancel');
                            }
                    }
                } else {    
                    echo formsubmit('act' , 'Cancel');
                    echo formsubmit('act' , 'Edit');
                    formclassdisplay($classrow);
                }
                echo '</form>';
              
                if ($can_addclass) {
                    thisformpost('classNEWdiv');
                    echo '<p style="line-height:0px;"></p>';
                                        
                    echo spanfmtbegin('' , 'green' , null , true) . 
                        'Replicar esta Turma como:' . 
                        formpatterninput(3 , 1 , $GBLpattern['class'] , 'Nova Turma' , 'newclassname' , '!') .
                        formhiddenval('classid' , $classrow['id']) .
                        formhiddenval('orgclassname' , $classrow['name']) .
                        formselectsession('addclass' , 'bool' , 0) .
                        formsubmit('act' , 'Replicate Class') . 
                        '</form>' . 
                        spanfmtend();
                } else {
                    if ($incanedit) {
                        thisformpost('classNEWdiv');
                        echo '<p style="line-height:0px;"></p>';
                        echo spanfmtbegin('' , 'green' , null , true) . 
                                'Replicar esta Turma como:' . 
                                formpatterninput(3 , 1 , $GBLpattern['class'] , 'Nova Turma' , 'newclassname' , '!') .
                                formhiddenval('classid' , $classrow['id']) .
                                formhiddenval('orgclassname' , $classrow['name']) .
                                formselectsession('addscenery' , 'scen.acc.edit' , 0) .
                                formselectsession('addclass' , 'bool' , 0) .
                                formsubmit('act' , 'Replicate Class in Scenery') . 
                                '</form>' . 
                                spanfmtend();        
                    }
                }
              
            } else {
                formclassdisplay($classrow);
            }
            echo '<hr>';
        }
        echo '</p>';
        
        if ($hiddenprofdeptid) {
            foreach ($hiddenprofdeptid as $profid => $hidprofdeptid ) { // '_blank'
                echo hiddenprofform($_POST['semid'] , $hidprofdeptid , $profid);  
            }
        }
        
        if ($hiddenroombuildingid) {
            foreach ($hiddenroombuildingid as $roomid => $buildingid) {
                echo hiddenroomform($_POST['semid'] , $buildingid , $roomid);
            }
        }
        
        if ($postedit) {
            if ($can_addclass) {
                thisformpost('classNEWdiv');
                echo spanfmtbegin('' , 'brown' , null , true) .  
                        'Adicionar Turma:' . 
                        formpatterninput(3 , 1 , $GBLpattern['class'] , 'Nova Turma' , 'newclassname' , '!') .
                        formselectsession('addclass' , 'bool' , 0) .
                        formsubmit('act' , 'Add Class') . 
                        '</form>' . 
                        spanfmtend();
            } else {
                if ($incanedit) {
                    thisformpost('classNEWdiv');
                    echo spanfmtbegin('' , 'brown' , null , true) . 
                        'Adicionar Turma:' . 
                        formpatterninput(3 , 1 , $GBLpattern['class'] , 'Nova Turma' , 'newclassname' , '!') .
                        formselectsession('addscenery' , 'scen.acc.edit' , 0) .
                        formselectsession('addclass' , 'bool' , 0) .
                        formsubmit('act' , 'Add Class in Scenery') . 
                        '</form>' . 
                        spanfmtend() ;        
                }
            }
        }

} 

 
echo '</div>' ; 

?>
