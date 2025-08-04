    
<?php
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['admdb'];
  


include 'coreX.php';

echo '<div class = "row">' .
    '<h2>DB setup/import/export </h2>' .
    '<hr>' ; 

if($_SESSION['role']['isadmin'] & $GBLsisgensetup) {
    switch($_POST['act']) {
    case 'Import Initial Data':
        DBimportInitialData();
        break;
    
    case 'Import Term Data':
        DBimportTermData();
        break;

    case 'Import Term Reserv Data':
        DBimportTermReservData();
        break;

    case 'Import Course Data':
        DBimportCourseData();
        break;

    case 'Fix Vacancies':
        if($_POST['fixvacancies']) {
            echo '<h3>Fixing Vacancies...</h3>';
            fixvacancies();
            echo 'done<br>';
        }
        break;

    case 'Export acc/unit tables':
        DBexportTables();
        break;

    case 'Restore acc/unit tables':
        DBrestoreTables();
        break;
        
    case 'Courses Adjust':
        //DBcourseAdjust();
        break;
    default:
        break;
    }
    
    if ($GBLsisgenfullsetup) {
        echo formpost($thisform);
        echo formselectsession('importdata' , 'bool' , 0);
        echo formsubmit('act' , 'Import Initial Data') . '</form><p>';
    }

    echo formpost($thisform);
    echo formselectsession('fixvacancies' , 'bool' , 0);
    echo formsubmit('act' , 'Fix Vacancies') . '</form><p>';

    echo formpost($thisform);
    echo formselectsession('exporttables' , 'bool' , 0);
    echo formsubmit('act' , 'Export acc/unit tables') . '</form><p>';

    if ($GBLsisgenfullsetup) {
        echo formpost($thisform);
        echo formselectsession('restoretables' , 'bool' , 0);
        echo formsubmit('act' , 'Restore acc/unit tables') . '</form><p>';  
    }

    if ($GBLsisgenDBsetupHacks) {
        echo formpost($thisform);
        echo formselectsession('courseadjust' , 'bool' , 0);
        echo formsubmit('act' , 'Courses Adjust') . '</form><p>';  
    }

    echo formpost($thisform);
    echo formselectsession('termdata' , 'bool' , 0);
    echo formsubmit('act' , 'Import Term Data') . '</form><p>';

    echo formpost($thisform);
    echo formselectsession('termreservdata' , 'bool' , 0);
    echo formsubmit('act' , 'Import Term Reserv Data') . '</form><p>';

// has yet to be implemented.
/*
    echo formpost($thisform);
    echo formselectsession('expreservdata' , 'bool' , 0);
    echo formselectsql($anytmp,  "SELECT * FROM `semester`  ORDER BY `name` DESC;"   , 'semid' , $_POST['semid'] , 'id' , 'name');
    echo formsubmit('act' , 'Export Reserv Data') . '</form><p>';

    echo formpost($thisform);
    echo formselectsession('impreservdata' , 'bool' , 0);
    echo formselectsql($anytmp,  "SELECT * FROM `semester` WHERE `readonly` = '0' ORDER BY `name` DESC;"   , 'semid' , $_POST['semid'] , 'id' , 'name');
    echo formsubmit('act' , 'Import Reserv Data') . '</form><p>';
*/


    echo formpost($thisform);
    echo formselectsession('coursedata' , 'bool' , 0);
    echo formsubmit('act' , 'Import Course Data') . '</form><p>';

}
echo '</div>' ;

  
?>
    
 
