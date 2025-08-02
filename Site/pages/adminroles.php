
<?php
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['admrole'];

//vardebug($_SESSION);

        
$bfields = array('isadmin');
$canfields = array('edit' , 'dupsem' , 'class' , 'addclass' , 'scenery' , 'vacancies' , 'disciplines' , 'coursedisciplines' , 'prof' , 'room' , 'viewlog');

if(!$_SESSION['buildings']) {
    $Query = "SELECT * FROM `building` WHERE `mark` = 1 ORDER BY `acronym` ; ";
    $GBLmysqli->dbvaluesloop($Query,'buildings','id','acronym');
}

if ($_SESSION['role']['isadmin']) {
    switch($_POST['act']) {
    case 'Delete':
        if ($_POST['roledelete']) {
            $Query = 
                "DELETE FROM `role` " .
                "WHERE `id` =  '$_POST[roleid]' ; " ;
                vardebug($Query,'delete query');
            //$GBLmysqli->dbquery($Query);
            $_POST['roleid'] = null;
        }
        break;
    case 'Submit':
        $rolekey = 'role' . $_POST['roleid'] ;
        $compfields = array('rolename' , 'description' , 'unitid');
                foreach ($compfields as $field) {
                    $keypost[$field] = $_POST[$rolekey . $field] ;
                }
        
        foreach ($bfields as $key) {
                array_push ($compfields ,  $key) ;
                $keypost[$key] = $_POST[$rolekey . $key] ;
        }
        foreach ($canfields as $key) {
                array_push ($compfields , 'can' . $key) ;
                $keypost['can' . $key] = $_POST[$rolekey . 'can' . $key] ;
        }
        if (fieldscompare($rolekey , $compfields)) {
         //echo 'some changes <br/>';
        $Query = 
            "UPDATE `role` " .
            "SET `rolename` =  '$keypost[rolename]' , " .
            "`description` =  '$keypost[description]'  , " .
            "`unit_id` =  '$keypost[unitid]'  " ;
        foreach ($bfields as $key) {
            $Query  .=  " , `$key` = '$keypost[$key]' " ;
        }
        foreach ($canfields as $key) {
            $auxpostkey = $keypost['can' . $key] ; 
            $Query .= " , `can_$key` = '$auxpostkey' ";
        }
        $Query .= "WHERE `id` =  '$_POST[roleid]' ; " ;
//        vardebug($rolekey,'role key');
  //      vardebug($_POST,'post');
     //   vardebug($compfields,'comp fields');
     //   vardebug($keypost,'key post');
      //  vardebug($Query, 'query');
        $GBLmysqli->dbquery($Query);
        }
        //$_POST['roleid'] = null;
        break;
    case 'Add Role':
        if ($_POST['addrole']) {
            $Query = 
                "INSERT INTO `role` (`rolename` , `description` , `unit_id` " ;
            foreach ($bfields as $key) {
                $Query .=  " , `$key` " ;
            }
            foreach ($canfields as $key) {
                $Query .= " , `can_$key` " ;
            }
            $Query .= ") VALUES ( '$_POST[rolename]'  ,  '$_POST[description]'  ,  '$_POST[unitid]' ";
            foreach ($bfields as $key) {
                $Query .=  " , '$_POST[$key]' " ;
            }
            foreach ($canfields as $key) {
                $postkey = $_POST['can' . $key] ;
                $Query .= " , '$postkey' ";
            }
            $Query .= ') ; ' ;
            //vardebug($Query);
            $GBLmysqli->dbquery($Query);
        }
        $_POST['roleid'] =  $GBLmysqli->insert_id;
        $_POST['act'] = 'Submit';
        break;
            
    case 'Remove Scenery':
        if ($_POST['scenerydelete']) {
            $Query = 
                "DELETE FROM `sceneryrole` " .
                "WHERE `id` =  '$_POST[sceneryroleid]' ; " ;
            $GBLmysqli->dbquery($Query);
        }
        $_POST['sceneryroleid'] = null;
        //$_POST['roleid'] = null;
        $_POST['act'] = 'Submit';
        break;
    case 'Change Scenery':
        if(fieldscompare('' , array('newsceneryid'))){
            $Query = 
                "UPDATE `sceneryrole` " .
                "SET `scenery_id` =  '$_POST[newsceneryid]'  " .
                "WHERE `id` =  '$_POST[sceneryroleid]' ; " ;
            $GBLmysqli->dbquery($Query);
        }
        $_POST['sceneryroleid'] = null;
        //$_POST['roleid'] = null;
        $_POST['act'] = 'Submit';
        break;
    case 'Add Scenery':
        $Query = 
            "INSERT INTO `sceneryrole` (`role_id` , `scenery_id`) " .
            "VALUES ( '$_POST[roleid]'  ,  '$_POST[newsceneryid]' ) ; " ;
        $GBLmysqli->dbquery($Query);
        $_POST['sceneryroleid'] = null;
        //$_POST['roleid'] = null;
        $_POST['act'] = 'Submit';
        break;
    case 'Add Building':
        $Query = 
            "INSERT INTO `buildingrole` (`role_id` , `building_id`) " .
            "VALUES ( '$_POST[roleid]'  ,  '$_POST[newbuildingid]' ) ; " ;
        $GBLmysqli->dbquery($Query);
        $_POST['buildingroleid'] = null;
        //$_POST['roleid'] = null;
        $_POST['act'] = 'Submit';
        break;
    case 'Remove Building':
        if ($_POST['buildingdelete']) {
            $Query = 
                "DELETE FROM `buildingrole` " .
                "WHERE `id` =  '$_POST[buildingroleid]' ; " ;
            $GBLmysqli->dbquery($Query);
        }
        $_POST['buildingroleid'] = null;
        //$_POST['roleid'] = null;
        $_POST['act'] = 'Submit';
        break;


    }
}

echo '<div class = "row">' .
    '<h2>Roles</h2>' .
    '<hr>' ;


function admroledisplay($bfields,$canfields,$rolerow) {
            echo formpost($thisform . '#role' . $rolerow['id'] . 'div') . formhiddenval('roleid' , $rolerow['id']) . formsubmit('act' , 'Edit');
            echo $rolerow['rolename'] . ' ( ' . $rolerow['description'] . ' ) :: ' . $rolerow['acronym'] . ' <br>';
            foreach ($bfields as $key) {
                if ($rolerow[$key]) {
                    echo spanformat('' , 'red' , $key . ':T ');
                } else {
                    echo spanformat('' , 'blue' , $key . ':F ');
                }
            }
            echo '<br>';
            foreach ($canfields as $key) {
                if ($rolerow['can_' . $key]) {
                    echo spanformat('' , 'red' , $key . ':T ');
                } else {
                    echo spanformat('' , 'blue' , $key . ':F ');
                }
            }
            echo '</form>';
}

$pattern = '[a-zA-Z \-\.\(\)]+';
if($_SESSION['role']['isadmin']) {
    $Query = 
        "SELECT `role` . *, " .
        "`unit` . `acronym` " .
        "FROM `role` , `unit` " .
        "WHERE `role` . `unit_id` = `unit` . `id` " .
        "ORDER BY `acronym` , `rolename` ; " ;
    $sqlroles = $GBLmysqli->dbquery($Query);
    while ($rolerow = $sqlroles->fetch_assoc()) {
    
        echo hiddendivkey('role' , $rolerow['id']);

        if ($rolerow['id'] == $_POST['roleid']) {
            if(($_POST['act'] == 'Submit') || ($_POST['act'] == 'Edit Scenery')) {
            echo HLbegin();
            admroledisplay($bfields,$canfields,$rolerow);
            echo HLend();
            } else {
            echo '<br>' . formpost($thisform . targetdivkey('role' , $rolerow['id'])) . formhiddenval('roleid' , $rolerow['id']);
            echo HLbegin();
            $rolekey = 'role' . $rolerow['id'] ;
            //echo '<table style = "background-color:#E0FFE0;color:#8000B0;"><tr><td>';
            echo 'Nome:' . formpatterninput(32 , 8 , $pattern , 'role name' , $rolekey . 'rolename' , $rolerow['rolename'])  . 
                'Descriçao:' . formpatterninput(64 , 32 , $pattern , 'role name' , $rolekey . 'description' , $rolerow['description']);

            $Query = "SELECT * FROM `unit`;" ;
            echo formselectsql($anytmp , $Query , $rolekey . 'unitid' , $rolerow['unit_id'] , 'id' , 'acronym');
            echo '<br>';
            foreach ($bfields as $key) {
                echo '  ' . $key . ': ';
                echo formselectsession($rolekey . $key , 'bool' , $rolerow[$key]);
            }
            echo '<br>';
            foreach ($canfields as $key) {
                echo '  ' . $key . ': ';
                echo formselectsession($rolekey . 'can' . $key , 'bool' , $rolerow['can_' . $key]);
            }
            echo '<br>' . formsubmit('act' , 'Submit');
            echo '</form>';      
    
            echo formpost($thisform) . formhiddenval('roleid' , $rolerow['id']);
            echo spanfmtbegin('' , 'red' , '' , true);
            echo 'Delete Role &lt;' . $rolerow['rolename']  . '&gt;?' ;
            echo formselectsession('roledelete' , 'bool' , 0);
            echo formsubmit('act' , 'Delete');
            echo spanfmtend();
            echo '</form><br>';      
            echo HLend();
            //echo '</td></tr></table>';
            }
        } else {
            admroledisplay($bfields,$canfields,$rolerow);
        }
    
      
        $Query = 
            "SELECT `scenery` . * , " .
            "`sceneryrole` . `id` AS `sceneryroleid` " .
            "FROM `sceneryrole` , `scenery` " .
            "WHERE `sceneryrole` . `scenery_id` = `scenery` . `id` " .
            "AND `sceneryrole` . `role_id` = '$rolerow[id]' ; " ;
        $sqlscenery = $GBLmysqli->dbquery($Query);
        while ($sceneryrow = $sqlscenery->fetch_assoc()) {
            echo formpost($thisform . '#role' . $rolerow['id'] . 'div');
            echo formhiddenval('roleid' , $rolerow['id']);
            echo formhiddenval('sceneryroleid' , $sceneryrow['sceneryroleid']);
            if ($sceneryrow['sceneryroleid'] == $_POST['sceneryroleid']) {
                echo formselectsession('newsceneryid' , 'scen.all' , $sceneryrow['id']);
                echo formsubmit('act' , 'Change Scenery');
            } else {
                echo formsubmit('act' , 'Edit Scenery');
                echo $GBLspc['D'] . ' ' . $sceneryrow['name'] . ' / ' . $sceneryrow['desc'];
                echo $GBLspc['D'] . 'Remove?';
                echo formselectsession('scenerydelete' , 'bool' , 0);
                echo formsubmit('act' , 'Remove Scenery')  . '<br>';
            }
            echo '</form>';
        }
        echo formpost($thisform . '#role' . $rolerow['id'] . 'div');
        echo formhiddenval('roleid' , $rolerow['id']);
        echo formselectsession('newsceneryid' , 'scen.all' , 15);
        echo formsubmit('act' , 'Add Scenery');
        echo '</form><p><p>';
    

        $Query = 
            "SELECT `building` . * , " .
            "`buildingrole` . `id` AS `buildingroleid` " .
            "FROM `buildingrole` , `building` " .
            "WHERE `buildingrole` . `building_id` = `building` . `id` " .
            "AND `buildingrole` . `role_id` = '$rolerow[id]' ; " ;
        $sqlbuilding = $GBLmysqli->dbquery($Query);
        while ($buildingrow = $sqlbuilding->fetch_assoc()) {
            echo formpost($thisform . '#role' . $rolerow['id'] . 'div');
            echo formhiddenval('roleid' , $rolerow['id']);
            echo formhiddenval('buildingroleid' , $buildingrow['buildingroleid']);
                echo $GBLspc['D'] . ' ' . $buildingrow['acronym'] . ' / ' . $buildingrow['name'];
                echo $GBLspc['D'] . 'Remove?';
                echo formselectsession('buildingdelete' , 'bool' , 0);
                echo formsubmit('act' , 'Remove Building')  . '<br>';
            echo '</form>';
        }
        echo formpost($thisform . '#role' . $rolerow['id'] . 'div');
        echo formhiddenval('roleid' , $rolerow['id']);
        echo formselectsession('newbuildingid' , 'buildings' , 0);
        echo formsubmit('act' , 'Add Building');
        echo '</form><p><p>';



    }
  
    
    echo '<br>' . formpost($thisform);
    echo 'Nome:' . formpatterninput(32 , 8 , $pattern , 'role name' , 'rolename' , '!')  . 
        'Descriçao:' . formpatterninput(64 , 32 , $pattern , 'role name' , 'description' , '!');

    $Query = "SELECT * FROM `unit`; " ; 
    echo formselectsql($anytmp , $Query , 'unitid' , 0 , 'id' , 'acronym' , null , false);
    echo '<br>';
    foreach ($bfields as $key) {
        echo '  ' . $key . ': ';
        echo formselectsession($key , 'bool' , 0);
    }
    echo '<br>';
    foreach ($canfields as $key) {
        echo '  ' . $key . ': ';
        echo formselectsession('can' . $key , 'bool' , 0);
    }
    echo '<br> Add Role? ';
    echo formselectsession('addrole' , 'bool' , 0);
    echo  formsubmit('act' , 'Add Role');
    echo '</form><br>';      
  
  
  
}
  
echo '</div>' ;
  
?>

    
 
