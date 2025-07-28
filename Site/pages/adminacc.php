
<?php 
include 'bailout.php';

$thisform = $GBLbasepage . '?q=admin&sq=adminacc'; 
$GBLmysqli->postsanitize();

unset($_SESSION['rolelist']);
$Query = 
        "SELECT * " . 
        "FROM `role` " . 
        "ORDER BY `rolename` ; " ;
$result = $GBLmysqli->dbquery($Query);
while ($sqlrow = $result->fetch_assoc()) {
    $_SESSION['roleslist'][$sqlrow['id']] = $sqlrow['rolename']  . '  /  ' . $sqlrow['description'];
}

if($_SESSION['role']['isadmin']) {
    switch ($_POST['act']) {
    case 'Submit':
        if($_POST['resetpasswd']) {
            $pass = " , `password` = 'abc123xyz' , `chgpasswd` = '1' ";
        } else {
            $pass = ' ' ;
        }
        $Query = 
                "UPDATE `account` " .
                "SET `activ` = '$_POST[activ]' $pass " .
                "WHERE `id` = '$_POST[usrid]' ; ";
        $GBLmysqli->dbquery( $Query );
        
        break;
    case 'Delete User':
        if ($_POST['userdelete']) {
            $Query = 
                "DELETE FROM `account` " .
                "WHERE `id` =  '$_POST[usrid]' ; " ;
            $GBLmysqli->dbquery( $Query );
            $_POST['accroleid'] = null;
        }
        break;
    case 'Delete Role':
        if ($_POST['roledelete']) {
            $Query = 
                "DELETE FROM `accrole` " .
                "WHERE `id` =  '$_POST[accroleid]' ; ";
            $GBLmysqli->dbquery( $Query );
        }
        $_POST['accroleid'] = null;
        break;
    case 'Change Role':
        if(fieldscompare('' , array('newroleid'))){
            $Query = 
                "UPDATE `accrole` " .
                "SET `role_id` =  '$_POST[newroleid]' " .
                "WHERE `id` =  '$_POST[accroleid]' ; " ; 
            $GBLmysqli->dbquery(  $Query  );
        }
        $_POST['accroleid'] = null;
        break;
    case 'Add Role':
        $Query = 
                "INSERT INTO `accrole` (`account_id` , `role_id`) " .
                "VALUES ( '$_POST[usrid]'  ,  '$_POST[newroleid]' ) ; " ;
        $GBLmysqli->dbquery( $Query );
        $_POST['accroleid'] = null;
        break;    
    }
}

$GBLmysqli->set_scenerysessionvalues();
$GBLmysqli->set_rolesessionvalues();

echo '<div class = "row">' .
    '<h2>Contas de Usuários</h2>' .
    '<hr>' ;



if($_SESSION['role']['isadmin']) {
    
    $Query = 
        "SELECT * " .
        "FROM `account` " .
        "ORDER BY `name` ; " ;
  $sqlusers = $GBLmysqli->dbquery( $Query );
  while ($usrrow = $sqlusers->fetch_assoc()) {
        echo hiddendivkey('acc' , $usrrow['id']);

    echo formpost($thisform . targetdivkey('acc' , $usrrow['id']));
    echo formhiddenval('usrid' , $usrrow['id']);
    if ($usrrow['id'] == $_POST['usrid']) {
      echo highlightbegin();
    }
    echo spanformat('' , 'darkblue' , 'User: <b>' . $usrrow['displayname'] . ' / ' . $usrrow['name']  . ' ( ' . $usrrow['email'] . ' )</b>');
    echo $GBL_Dspc . ' Reset passwd?';
    echo formselectsession('resetpasswd' , 'bool' , 0);
    echo 'chgpasswd:';
  if ($usrrow['chgpasswd']) {
            echo spanformat('' , 'red' , ':T ');
        } else {
            echo spanformat('' , 'blue' , ':F ');
        }
    echo $GBL_Dspc . ' Activ?';
    echo formselectsession('activ' , 'bool' , $usrrow['activ']);  
    echo formsubmit('act' , 'Submit') . '<br>';
    echo spanformat('' , 'red' , $GBL_Dspc . 'Delete?' , null , true);
    echo formselectsession('userdelete' , 'bool' , 0);
    echo spanformat('' , 'red' , formsubmit('act' , 'Delete User') . '<br>' , null , true);
    echo '</form>'. '<br>';
        
        $Query = 
                "SELECT `role` . * , " . 
                        "`accrole` . `id` AS `accroleid` " .
                "FROM `accrole` , `role` " .
                "WHERE `accrole` . `role_id` = `role` . `id` " . 
                        "AND `accrole` . `account_id` = '$usrrow[id]' " .
                "ORDER BY `role` . `rolename` ; " ;
  $sqlrole = $GBLmysqli->dbquery( $Query );
  
  while ($rolerow = $sqlrole->fetch_assoc()) {
            echo formpost($thisform . targetdivkey('acc' , $usrrow['id']));
      echo formhiddenval('accroleid' , $rolerow['accroleid']);
      echo formhiddenval('usrid' , $usrrow['id']);
      if ($rolerow['accroleid'] == $_POST['accroleid']) {
        echo formselectsession('newroleid' , 'roleslist' , $rolerow['id']);
        echo formsubmit('act' , 'Change Role');
      } else {
        echo formsubmit('act' , 'Edit Role');
        echo $GBL_Dspc . ' ' . $rolerow['rolename'] . ' / ' . $rolerow['description'];
        echo spanformat('' , 'red' , $GBL_Dspc . 'Delete?');
        echo formselectsession('roledelete' , 'bool' , 0);
        echo spanformat('' , 'red' , formsubmit('act' , 'Delete Role')  . '<br>');
      }
      echo '</form>';
    }
        echo formpost($thisform . targetdivkey('acc' , $usrrow['id']));
    echo formhiddenval('usrid' , $usrrow['id']);
        echo '<br>';
    echo formselectsession('newroleid' , 'roleslist' , 15);
    echo  formsubmit('act' , 'Add Role');
    echo '</form>';  
    if ($usrrow['id'] == $_POST['usrid']) {
      echo highlightend();
    }
  
    
  }
}
  
echo '</div>' ;
  
?>

    
 
