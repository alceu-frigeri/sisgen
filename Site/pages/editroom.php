  
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['edroom'];
formretainvalues(array('buildingid'));
  
        
$can_room = $_SESSION['role']['isadmin'] || ($_SESSION['role']['building'][$_POST['buildingid']] && $_SESSION['role']['building'][$_POST['buildingid']]['can_room']);


$postedit = false;
if ( (($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit')) & $can_room) {
    $postedit = true;
} else {
    $_POST['act'] = 'Cancel';
}

echo '<div class = "row">' .   
    '<h2>Salas</h2>' .
    '<hr>' ;

echo formpost($thisform);
  
if (!($_SESSION['roomtype'])) {
    $result = $GBLmysqli->dbquery( "SELECT * FROM `roomtype` ; " );
    while ($sqlrow = $result->fetch_assoc()) {
        $_SESSION['roomtype'][$sqlrow['id']] = $sqlrow['name'];
    }
}
  
  
switch($_POST['act']) {
case 'Insert':
    break;
    
case 'Delete':
    if ($_POST['roomdelete']) {
        $Query = 
                "DELETE FROM `room` " . 
                "WHERE `id` = '$_POST[roomid]' ; " ; 
        $GBLmysqli->dbquery( $Query );
    }
    break;

case 'Submit':
    $roomkey = 'room' . $_POST['roomid'] ; 
    $compfields = array('roomtype' , 'capacity');
        if(fieldscompare( $roomkey , $compfields)) {
            foreach ($compfields as $field) {
                $keypost[$field] = $_POST[$roomkey . $field] ;
            }
    $Query = 
        "UPDATE `room` " .
        "SET `roomtype_id` = '$keypost[roomtype]' , " .
                "`capacity` = '$keypost[capacity]' " .
        "WHERE `id` = '$_POST[roomid]' ; " ;
        //vardebug($Query,'query');
        //vardebug($keypost,'key post');
        //vardebug($compfields,'comp fields');
        //vardebug($_POST,'post');
    $GBLmysqli->dbquery( $Query );
    } 
    //$_POST['roomid'] = null;
    break;
    
}

if ($postedit & $can_room) {
    echo formhiddenval('buildingid' , $_POST['buildingid']);
    echo displaysqlitem('' , 'building' , $_POST['buildingid'] , 'acronym' , 'name');
    echo formsubmit('act' , 'Cancel');
    echo '</form><br>';
} else {

    $Query = 
        "SELECT * " .
        "FROM building " .
        "WHERE `mark` = '1' " .
        "ORDER BY acronym ; " ;
    echo 'Prédio: ' . 
        formselectsql($anytmp , $Query , 'buildingid' , $_POST['buildingid'] , 'id' , 'acronym');
}

function roomdisplay($sqlrow) {
    global $GBLspc;
    echo formsubmit('act' , 'Edit');
    echo formhiddenval('roomid' , $sqlrow['id']);
    echo $sqlrow['name'] . $GBLspc['D'] . $_SESSION['roomtype'][$sqlrow['roomtype_id']] . $GBLspc['D'];
    if ($sqlrow['capacity']) {
        echo ' (cap . :' . $sqlrow['capacity'] . ')';
    }
    echo '<br>';      
}
 
// course, term
$Query = 
    "SELECT * " .
    "FROM   `room` " .
    "WHERE `building_id` = '$_POST[buildingid]' " .
    "ORDER BY `name` ; " ;

$result = $GBLmysqli->dbquery( $Query );

if ($postedit & $can_room) {
    while ($sqlrow = $result->fetch_assoc()) {
        echo formpost($thisform);
        echo formhiddenval('buildingid' , $_POST['buildingid']);
        if ($_POST['roomid'] == $sqlrow['id']) {
            if($_POST['act'] == 'Submit') {
            echo HLbegin();
            roomdisplay($sqlrow);
            echo HLend();
            } else {
            $roomkey = 'room' . $sqlrow['id'] ; 
            echo HLbegin();
                    echo formhiddenval('roomid' , $sqlrow['id']);
                    echo $GBLspc['D'] . ' ' . $sqlrow['name'];
                    echo formselectsession($roomkey . 'roomtype' , 'roomtype' , $sqlrow['roomtype_id']);
                    echo '  Capacidade:  ' . formpatterninput(3 , 1 , '[0-9]+' , 'Capacidade' , $roomkey . 'capacity' , $sqlrow['capacity']);
                    echo formsubmit('act' , 'Submit');
            echo HLend();
            } 
        } else {
            roomdisplay($sqlrow);
        }
        echo '</form>';
    }
    echo '<br>';
 

} else {
    $firstofmany = true;
    while ($sqlrow = $result->fetch_assoc()) {
        if ($firstofmany) {
            $firstofmany = false;
            if ($can_room) {
                echo formsubmit('act' , 'Edit') . '</form><br>' ;
            } else {
                echo  '</form><br>' ;
            }
        }
        echo $sqlrow['name']. $GBLspc['D'] . $_SESSION['roomtype'][$sqlrow['roomtype_id']] . $GBLspc['D'];
        if ($sqlrow['capacity']) {
            echo ' (cap.:' . $sqlrow['capacity'] . ')';
        }
        echo '<br>';      
    }
}

  
echo '</div>';

?>
  


