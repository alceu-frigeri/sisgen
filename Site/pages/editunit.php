  
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['edunit'];
        
$can_edit = $_SESSION['role']['isadmin'] || ($_SESSION['usercanedit']);

$postedit = false;
if ((($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit')) & $can_edit) {
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


// course, term
$result = $GBLmysqli->dbquery( "SELECT * FROM   `unit`  ORDER BY `iscourse` DESC, `name` ASC;" );
$anyone = 0;
  
if ($postedit & $can_edit) {
  echo formsubmit('act' , 'Cancel');
} else {
    if ($can_edit) {
    echo formsubmit('act' , 'Edit');
  }
}



echo '<table>';
while ($sqlrow = $result->fetch_assoc()) {
    if ($postedit & $can_edit) {
        echo formpost($thisform . targetdivkey('unit' , $sqlrow['id']));
        $unitkey = 'unit' . $sqlrow['id'] ; 
        if ($_POST['unitid'] == $sqlrow['id']) {
            if ($_POST['act'] == 'Submit') {
            echo '<tr  ' . $GBLhighlightstyle . '>';
            echo '<td>' . formsubmit('act' , 'Edit') . formhiddenval('unitid' , $sqlrow['id']) . $GBL_Tspc . '</td>';        
            echo '<td>' . $sqlrow['acronym'] . $GBL_Tspc . '</td><td>' . $sqlrow['name'] . $GBL_Tspc . '</td>';
            echo '<td>Contato:' . $sqlrow['contactname']  . $GBL_Tspc . '</td>' . 
        '<td>Email:'. $sqlrow['contactemail'] . $GBL_Tspc . '</td>'  . 
        ' <td>Fone:'. $sqlrow['contactphone'] . $GBL_Tspc . '</td><td></td>';
        echo '</tr>';
        
            } else {
            //echo '<tr>';
            echo '<tr ' . iddivkey('unit' , $sqlrow['id']) . '><td>&nbsp;<td></tr>';
            echo '<tr><td>&nbsp;</td></tr>';
            echo '<tr><td>&nbsp;</td></tr>';
            echo '<tr  ' . $GBLhighlightstyle . '>';
            //echo hiddendivkey('unit' , $sqlrow['id']); 
            echo '<td>' . formsubmit('act' , 'Edit') . formhiddenval('unitid' , $sqlrow['id']) . $GBL_Tspc . '</td>';
            echo '<td>' . $sqlrow['acronym'] . $GBL_Tspc . '</td><td>' . $sqlrow['name'] . $GBL_Tspc . '</td>';
            echo '<td>Contato:' . formpatterninput(64 , 20 , $GBLnamepattern , 'Contato' , $unitkey . 'contactname' , $sqlrow['contactname'])  . $GBL_Tspc . '</td>' . 
        '<td>Email:'. formpatterninput(64 , 20 , $GBLnamepattern , 'Email' , $unitkey . 'contactemail' , $sqlrow['contactemail']) . $GBL_Tspc . '</td>'  . 
        ' <td>Fone:'. formpatterninput(16 , 9 , '[0-9\.]+' , 'Telefone' , $unitkey . 'contactphone' , $sqlrow['contactphone']) .  $GBL_Tspc  . '</td></tr><tr><td></td><td></td><td></td><td>'  . formsubmit('act' , 'Submit') . '</td>';
        echo '</tr>';
        }
        } else {
            echo '<tr>';
            echo '<td>' . formsubmit('act' , 'Edit') . formhiddenval('unitid' , $sqlrow['id']) . $GBL_Tspc . '</td>';        
            echo '<td>' . $sqlrow['acronym'] . $GBL_Tspc . '</td><td>' . $sqlrow['name'] . $GBL_Tspc . '</td>';
            echo '<td>Contato:' . $sqlrow['contactname']  . $GBL_Tspc . '</td>' . 
        '<td>Email:'. $sqlrow['contactemail'] . $GBL_Tspc . '</td>'  . 
        ' <td>Fone:'. $sqlrow['contactphone'] . $GBL_Tspc . '</td><td></td>';
        echo '</tr>';
        }
//        echo '</tr>';
        echo "</form>";
    } else {
    echo '<tr>';
        echo '<td>' .  '</td>';
        echo '<td>' . $sqlrow['acronym'] . $GBL_Tspc . '</td><td>' . $sqlrow['name'] . $GBL_Tspc . '</td>';
        echo '<td>Contato:' . $sqlrow['contactname']  . $GBL_Tspc . '</td>' . 
            '<td>Email:'. $sqlrow['contactemail'] . $GBL_Tspc . '</td>'  . 
            ' <td>Fone:'. $sqlrow['contactphone'] . $GBL_Tspc . '</td>';
    echo '</tr>';
    }
    //echo '</tr>';
}
echo '</table>';

echo '</form>';
echo '</div>';
  

?>
  


