
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['admsem'];

echo '<div class = "row">' .
    '<h2>Edição de Semestres</h2>' .
    '<hr>' ;



if($_SESSION['role']['isadmin']) {
    switch($_POST['act']) {
    case 'Submit':
        if (fieldscompare('' , array('readonly'))) {
            $Query = 
                "UPDATE `semester` " .
                "SET `readonly` =  '$_POST[readonly]'  " .
                "WHERE `id` =  '$_POST[semid]' ; " ;
            $GBLmysqli->dbquery( $Query );
        }
        break;
        
    case 'Delete':
        if($_POST['act'] == 'Delete') {
            if ($_POST['delete']) {
                $Query = 
                        "DELETE FROM `semester` " .
                        "WHERE `id` =  '$_POST[semid]' ; ";
                $GBLmysqli->dbquery( $Query );
            }
        }
        break;
        
    case 'Duplicate as':
        duplicatesem($_POST['semid'] , $_POST['newsem']);
        break;
    }

    $Query =
        "SELECT * " .
        "FROM `semester` " .
        "ORDER BY `name` DESC ; " ;
        
    $semsql = $GBLmysqli->dbquery( $Query );
    while ($semrow = $semsql->fetch_assoc()) {
        echo formpost($thisform) . formhiddenval('semid' , $semrow['id']);
        if (($_POST['semid'] == $semrow['id']) & (($_POST['act'] == 'Edit'))) {
            echo HLbegin();
            echo $semrow['name'] . $GBLspc['D'] . 'readonly:';
            echo formselectsession('readonly' , 'bool' , $semrow['readonly']);
            echo formsubmit('act' , 'Submit');
            echo spanfmtbegin('','red',null,true);
            echo '  Delete? ';
            echo formselectsession('delete' , 'bool' , 0);
            echo formsubmit('act' , 'Delete');
            echo spanfmtend();
            echo '</form>';
            echo HLend();
            echo formpost($thisform) . formhiddenval('semid' , $semrow['id']);
            echo formpatterninput(10 , 5 , '[0-9a-zA-Z \-]+' , 'novo semestre' , 'newsem' , '!');
            echo formsubmit('act' , 'Duplicate as');
        } else {
            if ($_POST['semid'] == $semrow['id']) { 
                echo HLbegin();
                $HLend = HLend();
            } else {
                $HLend = ' ';
            }
            echo formsubmit('act' , 'Edit') . $semrow['name'] . $GBLspc['Q'];
            if ($semrow['readonly']) {
                echo '(readonly)';
            } else {
                echo '(read/write)';
            }    
            echo $HLend;
        }
        echo '</form>';
    }
}

echo '</div>';

?>
  
  


