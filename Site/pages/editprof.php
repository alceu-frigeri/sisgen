  
<?php 
include 'bailout.php';


$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['edprof'];
formretainvalues(array('deptid'));

$can_prof = $_SESSION['role']['isadmin'] || ($_SESSION['role'][$_POST['deptid']] && $_SESSION['role'][$_POST['deptid']]['can_prof']) ;
  
        


$postedit = false;
if ( (($_POST['act'] == 'Edit') || ($_POST['act'] == 'Submit') || ($_POST['act'] == 'Delete') || ($_POST['act'] == 'Insert')) && $can_prof) {
    $postedit = true;
} else {
    $_POST['act'] = 'Cancel';
}

echo '<div class = "row">' .    
    '<h2>Professores</h2>' .
    '<hr>' ;

echo formpost($thisform);
  
if (!($_SESSION['profkind'])) {
    $Queryresult = $GBLmysqli->dbquery( "SELECT * FROM `profkind`; " );
    while ($sqlrow = $Queryresult->fetch_assoc()) {
        $_SESSION['profkind'][$sqlrow['id']] = $sqlrow['acronym'];
    }
}
  
switch ($_POST['act']) {
case 'Insert':
    $Query = 
        "INSERT INTO `prof` (`dept_id` , `profkind_id` , `name` , `nickname`) " .
        "VALUES ('$_POST[deptid]' , '$_POST[profkind]' , '$_POST[profname]' , '$_POST[profnickname]') ; " ;
    $GBLmysqli->dbquery( $Query );
    $_POST['profid'] = $GBLmysqli->insert_id;
    $_POST['act'] = 'Submit';
    break;
case 'Submit':
        $profkey = 'prof' . $_POST['profid'] ;
        $compfields = array('profkind' , 'profname' , 'profnickname');
        if(fieldscompare( $profkey , $compfields)) {
            foreach ($compfields as $field) {
                $keypost[$field] = $_POST[$profkey . $field] ;
            }
            $Query = 
                "UPDATE `prof` " .
                "SET `profkind_id` = '$keypost[profkind]' , " .
                "`name` = '$keypost[profname]' , " .
                "`nickname` = '$keypost[profnickname]'  " .
                "WHERE `id` = '$_POST[profid]' ; " ;
            $GBLmysqli->dbquery( $Query );            
        }
    break;
case 'Delete':
    if ($_POST['profdelete']) {
        $Query = 
                "DELETE FROM `prof` " .  
                "WHERE `id` = '$_POST[profid]' ; " ;
        $GBLmysqli->dbquery($Query);
    }
    $_POST['profid'] = null;
    break;
}

if ($postedit & $can_prof) {
    echo formhiddenval('deptid' , $_POST['deptid']);
    echo displaysqlitem('' , 'unit' , $_POST['deptid'] , 'acronym' , 'name');
    echo formsubmit('act' , 'Cancel');
    echo '</form><br>';
} else {
    $Query = 
        "SELECT * " . 
        "FROM unit " . 
        "WHERE `isdept` = '1' " . 
                "AND `mark` = '1' " . 
        "ORDER BY unit . acronym ; " ;
    echo 'Dept.: ' . formselectsql($anytmp , $Query , 'deptid' , $_POST['deptid'] , 'id' ,  'acronym');
}
  
// course, term
$Query = 
        "SELECT `prof` . * " .
        "FROM   `prof` ,  `unit`" .
        "WHERE `dept_id` = '$_POST[deptid]' " .
                "AND `unit` . `id` = `dept_id` " . 
                "AND `unit` . `mark` = '1' " . 
        "ORDER BY `profkind_id` , `name` ; " ;

$Queryresult = $GBLmysqli->dbquery( $Query );

function profdisplay($sqlrow) {
      global $GBLspc;
      echo formsubmit('act' , 'Edit');
      echo formhiddenval('profid' , $sqlrow['id']);
      echo $_SESSION['profkind'][$sqlrow['profkind_id']] . $GBLspc['D'] . $sqlrow['name'] . $GBLspc['D'] . ' (' . $sqlrow['nickname'] . ')<br>';
}

$firstofmany = true;
if ($postedit & $can_prof) {
    while ($sqlrow = $Queryresult->fetch_assoc()) {
      echo formpost($thisform . targetdivkey('prof' , $sqlrow['id']));
    echo formhiddenval('deptid' , $_POST['deptid']);
    if ($_POST['profid'] == $sqlrow['id']) {
      if($_POST['act'] == 'Submit') {
          echo HLbegin();
          profdisplay($sqlrow);
          echo HLend(); 
      } else {
      $profkey = 'prof' . $sqlrow['id'] ;
      echo hiddendivkey('prof' , $sqlrow['id']);
      echo HLbegin();
      echo formhiddenval('profid' , $sqlrow['id']);
      echo formselectsession($profkey . 'profkind' , 'profkind' , $sqlrow['profkind_id']);
            echo formpatterninput(120 , 64 , $GBLpattern['name'] , 'Nome completo' , $profkey .  'profname' , $sqlrow['name']);
      echo '<br>' . $GBLspc['Q'] .  formpatterninput(64 , 32 , $GBLpattern['name'] , 'Nome abreviado' , $profkey .  'profnickname' , $sqlrow['nickname']);
      echo formsubmit('act' , 'Submit');
      echo '</form>';
      echo HLend();
      echo formpost($thisform);
      echo formhiddenval('deptid' , $_POST['deptid']);
      echo formhiddenval('profid' , $sqlrow['id']);
      echo spanfmtbegin('' , 'red' , null , true) . '  ' . $GBLspc['T'] . 'remover: ' ;
      echo formselectsession('profdelete' , 'bool' , 0);
      echo formsubmit('act' , 'Delete') . spanfmtend() ; 
      }
    } else {
      profdisplay($sqlrow);
    }
    echo '</form>';
    }
    echo '<br>';
    echo '<i>inserção</i>';


    echo formpost($thisform);
    echo formhiddenval('deptid' , $_POST['deptid']);
    echo formselectsession('profkind' , 'profkind' , 1);
    echo formpatterninput(120 , 64 , $GBLpattern['name'] , 'Nome completo' , 'profname' , '-');
    echo $GBLspc['D'] . ' ' . formpatterninput(64 , 32 , $GBLpattern['name'] , 'Nome abreviado' , 'profnickname' , '-');
    echo formsubmit('act' , 'Insert') ;
    echo '</form>';
    

} else {
    while ($sqlrow = $Queryresult->fetch_assoc()) {
        if ($firstofmany) {
            if ($can_prof) {
                echo $GBLspc['D'] .  formsubmit('act' , 'Edit') . '</form>';
            } else {
                echo  '</form>';
            }
            echo '<br>';
            $firstofmany = false;
        } 
        echo $_SESSION['profkind'][$sqlrow['profkind_id']] . $GBLspc['D'] . $sqlrow['name'] . $GBLspc['D'] . ' (' . $sqlrow['nickname'] . ')<br>';
    }
}

echo '</div>';
  
?>



