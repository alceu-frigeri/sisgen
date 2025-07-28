
<?php
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $GBLbasepage . '?q=admin&sq=scenery';

if ($_SESSION['role']['isadmin']) {
    switch($_POST['act']) {
    case 'Delete':
        if ($_POST['scenerydelete']) {
            $Query  = 
                "DELETE FROM `scenery` " .
                "WHERE `id` =  '$_POST[sceneryid]' ; " ;
            $GBLmysqli->dbquery( $Query );
            $_POST['sceneryid'] = null;
        }
        break;
    case 'Submit':
        $Query = 
                "UPDATE `scenery` " .
                "SET `name` =  '$_POST[sceneryname]' , " . 
                        "`desc` =  '$_POST[scenerydesc]'  , " . 
                        "`hide` =  '$_POST[sceneryhide]'  ".
                "WHERE `id` =  '$_POST[sceneryid]' ; " ;
        $GBLmysqli->dbquery( $Query );
        //$_POST['sceneryid'] = null;
        break;
    case 'Add Scenery':
        if ($_POST['addscenery']) {
            $Query = 
                "INSERT INTO `scenery` (`name` , `desc` , `hide`) " .
                "VALUES ( '$_POST[sceneryname]'  ,  '$_POST[scenerydesc]'  ,  '$_POST[sceneryhide]' );";          
            $GBLmysqli->dbquery( $Query );
        }
        $_POST['sceneryid'] = null;
        break;
    }
}

echo '<div class = "row">' .
    '<h2>Sceneries</h2>' .
    '<hr>' ;

if($_SESSION['role']['isadmin']) {
    $Query = 
        "SELECT * " . 
        "FROM `scenery` " . 
        "ORDER BY `name`;";
    $sqlsceneries = $GBLmysqli->dbquery( $Query );
    while ($sceneryrow = $sqlsceneries->fetch_assoc()) {
    
        if ($sceneryrow['id'] == $_POST['sceneryid']) {
            if($_POST['act'] == 'Submit') {
                echo highlightbegin();
                    echo formpost($thisform . targetdivkey('scen' , $sceneryrow['id'])) . formhiddenval('sceneryid' , $sceneryrow['id']) . formsubmit('act' , 'Edit');
                    echo $sceneryrow['name'] . ' ( ' . $sceneryrow['desc'] . ' )' . ' <br>';
                    if ($sceneryrow['hide']) {
                        echo spanformat('' , 'red' , 'hidden: T ');
                    } else {
                        echo spanformat('' , 'blue' , 'hidden: F ');
                    }
                    echo '<br>';
                echo highlightend();
                    echo '</form><hr>';
                    
            } else {
            echo hiddendivkey('scen' , $sceneryrow['id']);
            echo highlightbegin();
            echo formpost($thisform) . formhiddenval('sceneryid' , $sceneryrow['id']);
            echo 'Nome:' . formpatterninput(32 , 8 , $GBLnamepattern , 'scenery name' , 'sceneryname' , $sceneryrow['name'])  . 
                'Descrição:' . formpatterninput(64 , 32 , $GBLcommentpattern , 'scenery description' , 'scenerydesc' , $sceneryrow['desc']);
            echo 'hide scenery? ';
            echo formselectsession('sceneryhide' , 'bool' , $sceneryrow['hide']);
            echo '<br>' . formsubmit('act' , 'Submit');
            echo '</form>';      
            echo highlightend();
            echo formpost($thisform) . formhiddenval('sceneryid' , $sceneryrow['id']);
            echo spanformatstart('','red',null,true);
            echo 'Delete scenery &lt;' . $sceneryrow['name']  . '&gt;?';
            echo formselectsession('scenerydelete' , 'bool' , 0);
            echo formsubmit('act' , 'Delete');
            echo spanformatend();
            echo '</form><br><hr>';      
            }
        } else {
            echo formpost($thisform . targetdivkey('scen' , $sceneryrow['id'])) . formhiddenval('sceneryid' , $sceneryrow['id']) . formsubmit('act' , 'Edit');
            echo $sceneryrow['name'] . ' ( ' . $sceneryrow['desc'] . ' )' . ' <br>';
            if ($sceneryrow['hide']) {
                echo spanformat('' , 'red' , 'hidden: T ');
            } else {
                echo spanformat('' , 'blue' , 'hidden: F ');
            }
            echo '<br>';
            echo '</form><hr>';
        }
    }
    echo formpost($thisform);
    echo 'Nome:' . formpatterninput(32 , 8 , $GBLnamepattern , 'scenery name' , 'sceneryname' , '!')  . 
        'Descriçao:' . formpatterninput(64 , 32 , $GBLcommentpattern , 'scenery name' , 'scenerydesc' , '!');
    echo '  hidden? ';
    echo formselectsession('sceneryhide' , 'bool' , 0);
    echo '<br> Add scenery? ';
    echo formselectsession('addscenery' , 'bool' , 0);
    echo  formsubmit('act' , 'Add Scenery');
    echo '</form><br>';      

}
  
echo '</div>' ;
  
?> 

