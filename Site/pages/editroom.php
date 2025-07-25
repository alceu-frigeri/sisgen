  
<?php 
// TODO: LOL error in logic. no unitid...
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $GBLbasepage . '?q=edits&sq=rooms';
formretainvalues(array('buildingid'));
  
        
$can_room = $_SESSION['role']['isadmin'] || ($_SESSION['role'][$_POST['unitid']] && $_SESSION['role'][$_POST['unitid']]['can_room']);


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
    $Query = 
        "UPDATE `room` " .
        "SET `roomtype_id` = '$_POST[roomtype]' , " .
                "`capacity` = '$_POST[capacity]' " .
        "WHERE `id` = '$_POST[roomid]' ; " ;
    $GBLmysqli->dbquery( $Query );
    $_POST['roomid'] = null;
    break;
    
}

if ($postedit & $can_room) {
    echo formhiddenval('buildingid' , $_POST['buildingid']);
    echo displaysqlitem('' , 'building' , $_POST['buildingid'] , 'acronym' , 'name');
    echo formsubmit('act' , 'Cancel');
    echo '</form>';
} else {

    formselectsql($anytmp , 
                  "SELECT * FROM building WHERE `mark` = '1' ORDER BY acronym;" , 
                  'buildingid' , 
                  $_POST['buildingid'] , 
                  'id' , 
                  'acronym');
}

 
// course, term
$Query = 
    "SELECT * " .
    "FROM   `room` " .
    "WHERE `building_id` = '$_POST[buildingid]' " .
    "ORDER BY `name` ; " ;

$result = $GBLmysqli->dbquery( $Query );
$anyone = 0;
if ($postedit & $can_room) {
    while ($sqlrow = $result->fetch_assoc()) {
        echo formpost($thisform);
        echo formhiddenval('buildingid' , $_POST['buildingid']);
        if ($_POST['roomid'] == $sqlrow['id']) {
            echo formhiddenval('roomid' , $sqlrow['id']);
            echo $GBL_Dspc . ' ' . $sqlrow['name'];
            formselectsession('roomtype' , 'roomtype' , $sqlrow['roomtype_id']);
            echo '  Capacidade:  ' . formpatterninput(3 , 1 , '[0-9]+' , 'Capacidade' , 'capacity' , $sqlrow['capacity']);
            echo formsubmit('act' , 'Submit');
        } else {
            echo formsubmit('act' , 'Edit');
            echo formhiddenval('roomid' , $sqlrow['id']);
            echo $sqlrow['name'] . $GBL_Dspc . $_SESSION['roomtype'][$sqlrow['roomtype_id']] . $GBL_Dspc;
            if ($sqlrow['capacity']) {
                echo ' (cap . :' . $sqlrow['capacity'] . ')';
            }
            echo '<br>';      
        }
        echo '</form>';
    }
    echo '<br>';
 

} else {
    $firstofmany = true;
    while ($sqlrow = $result->fetch_assoc()) {
        $anyone = 1;
        if ($firstofmany) {
            $firstofmany = false;
            if ($can_room) {
                echo formsubmit('act' , 'Edit') ;
            }
            echo '<br>';
        }
        echo $sqlrow['name']. $GBL_Dspc . $_SESSION['roomtype'][$sqlrow['roomtype_id']] . $GBL_Dspc;
        if ($sqlrow['capacity']) {
            echo ' (cap.:' . $sqlrow['capacity'] . ')';
        }
        echo '<br>';      
    }
}

echo '</form>';
  
echo '</div>';

?>
  


