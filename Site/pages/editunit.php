  
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['edunit'];
        
$can_edit = $_SESSION['role']['isadmin'] || ($_SESSION['usercanedit']);

$postedit = false;
if ((($_POST['act'] == 'Edit') || ($_POST['act'] == 'Submit')) && $can_edit) {
    $postedit = true;
} else {
    $_POST['act'] = 'Cancel';
}

echo '<div class = "row">' .
    '<h2>Departamentos/Cursos</h2>' .
    '<hr>' . '<br>' ;

echo formpost($thisform);

switch($_POST['act']) {
case 'Insert':
    break;
    
case 'Delete':
    break;
    
case 'Submit':
    $unitkey  = 'unit' . $_POST['unitid'];
    $compfields = array('contactname' , 'contactemail' , 'contactphone');
    if(fieldscompare( $unitkey , $compfields)) {
        foreach ($compfields as $field) {
            $keypost[$field] = $_POST[$unitkey . $field] ;
        }
        $Query = 
            "UPDATE `unit` " .
            "SET `contactname` = '$keypost[contactname]' , " . 
                "`contactemail` = '$keypost[contactemail]' , " . 
                "`contactphone` = '$keypost[contactphone]' " .
            "WHERE `id` = '$_POST[unitid]' ; " ;
        $GBLmysqli->dbquery( $Query );
    } 
        
    //$_POST['unitid'] = null;
    break;
    
}


function unitdisplay($sqlrow,$submit='') {
    $GBLspc;
    echo '<td>' . $submit . '</td>';        
    echo '<td>' . $sqlrow['acronym'] . $GBLspc['T'] . '</td><td>' . $sqlrow['name'] . $GBLspc['T'] . '</td>';
    echo '<td>Contato:' . $sqlrow['contactname']  . $GBLspc['T'] . '</td>' . 
        '<td>Email:'. $sqlrow['contactemail'] . $GBLspc['T'] . '</td>'  . 
        ' <td>Fone:'. $sqlrow['contactphone'] . $GBLspc['T'] . '</td><td></td>';
}

// course, term
$Queryresult = $GBLmysqli->dbquery( "SELECT * FROM   `unit`  ORDER BY `iscourse` DESC, `name` ASC;" );
  
if ($postedit & $can_edit) {
    echo formsubmit('act' , 'Cancel') . '</form><br>';
} else {
    if ($can_edit) {
        echo formsubmit('act' , 'Edit') . '</form><br>';
    }
}



echo '<table>';
while ($sqlrow = $Queryresult->fetch_assoc()) {
    if ($postedit & $can_edit) {
        echo formpost($thisform . targetdivkey('unit' , $sqlrow['id']));
        $unitkey = 'unit' . $sqlrow['id'] ; 
        if ($_POST['unitid'] == $sqlrow['id']) {
            if ($_POST['act'] == 'Submit') {
                echo '<tr  ' . $GBLhighlightstyle . '>';
                unitdisplay($sqlrow,formsubmit('act' , 'Edit') . formhiddenval('unitid' , $sqlrow['id']) . $GBLspc['T']);
                echo '</tr>';        
            } else {
                echo '<tr ' . iddivkey('unit' , $sqlrow['id']) . '><td>&nbsp;<td></tr>';
                echo '<tr><td>&nbsp;</td></tr>';
                echo '<tr><td>&nbsp;</td></tr>';
                echo '<tr  ' . $GBLhighlightstyle . '>';

                echo '<td>' . 
                    formsubmit('act' , 'Edit') . 
                    formhiddenval('unitid' , $sqlrow['id']) . 
                    $GBLspc['T'] . '</td>';
                echo "<td>$sqlrow[acronym]$GBLspc[T]</td><td>$sqlrow[name]$GBLspc[T]</td>";
                echo 
                    "<td>Contato:" . 
                    formpatterninput(64 , 20 , $GBLpattern['name'] , 'Contato' , $unitkey . 'contactname' , $sqlrow['contactname'])  . 
                    $GBLspc['T'] . 
                    '</td>' . 
                    "<td>Email:". 
                    formpatterninput(64 , 20 , $GBLpattern['name'] , 'Email' , $unitkey . 'contactemail' , $sqlrow['contactemail']) . 
                    $GBLspc['T'] . 
                    '</td>'  . 
                    "<td>Fone:". 
                    formpatterninput(16 , 9 , '[0-9\.]+' , 'Telefone' , $unitkey . 'contactphone' , $sqlrow['contactphone']) .  
                    $GBLspc['T']  . 
                    '</td></tr>' ;
                echo '<tr><td></td><td></td><td></td><td>'  .  formsubmit('act' , 'Submit') . '</td>' . '</tr>';
            }
        } else {
            echo '<tr>';
            unitdisplay($sqlrow,formsubmit('act' , 'Edit') . formhiddenval('unitid' , $sqlrow['id']) . $GBLspc['T']);
            echo '</tr>';
        }
        echo "</form>";
    } else {
        echo '<tr>';
        unitdisplay($sqlrow,$GBLspc['Q']);
        echo '</tr>';
    }
}
echo '</table>';

echo '</div>';
  

?>
  


