
<?php 

	$thisform=$basepage.'?q=admin&sq=adminacc'; 

	$mysqli->postsanitize();

	unset($_SESSION['rolelist']);
		$q = "SELECT * FROM `role`;";
		$result = $mysqli->dbquery($q);
		while ($sqlrow = $result->fetch_assoc()) {
			$_SESSION['roleslist'][$sqlrow['id']] = $sqlrow['rolename'] .'  /  '.$sqlrow['description'];
		}


	
	if($_SESSION['role']['isadmin']) {
		switch ($_POST['act']) {
			case 'Submit':
				if(_POST['resetpasswd']) {
					$pass = " , `password` = 'abc123xyz' , `chgpasswd` = '1' ";
				} else {
					$pass='';
				}
					$q = "UPDATE `account` SET `activ` = '" . $_POST['activ'] . "' $pass WHERE `id` = '" . $_POST['usrid'] . "';";
					$mysqli->dbquery($q);
			break;
			case 'Delete User':
				if ($_POST['userdelete']) {
					$q = "DELETE FROM `account` WHERE `id` = '" . $_POST['usrid'] . "';";
					$mysqli->dbquery($q);
					$_POST['accroleid'] = null;
				}
			break;
			case 'Delete Role':
				if ($_POST['roledelete']) {
					$q = "DELETE FROM `accrole` WHERE `id` = '" . $_POST['accroleid'] . "';";
					$mysqli->dbquery($q);
				}
				$_POST['accroleid'] = null;
			break;
			case 'Change Role':
				if(fieldscompare('',array('newroleid'))){
					$q = "UPDATE `accrole` SET `role_id` = '" . $_POST['newroleid'] . "' WHERE `id` = '" . $_POST['accroleid'] . "';";
					$mysqli->dbquery($q);
				}
				$_POST['accroleid'] = null;
			break;
			case 'Add Role':
				$q = "INSERT INTO `accrole` (`account_id`, `role_id`) VALUES ('" . $_POST['usrid'] . "' , '" . $_POST['newroleid'] . "');";
				$mysqli->dbquery($q);
				$_POST['accroleid'] = null;
			break;		
		}
	}

	$mysqli->set_scenerysessionvalues();
	$mysqli->set_rolesessionvalues();

?>



<div class="row">
        <h2>Contas de Usuários</h2>
        <hr>


<?php
  if($_SESSION['role']['isadmin']) {
	$q = "SELECT * FROM `account` ORDER BY `name`;";
	$sqlusers = $mysqli->dbquery($q);
	while ($usrrow = $sqlusers->fetch_assoc()) {
		echo '<div id="acc'.$usrrow['id'].'div">&nbsp;<br></div><br><br>';

		echo formpost($thisform.'#acc'.$usrrow['id'].'div');
		echo formhiddenval('usrid',$usrrow['id']);
		echo spanformat('','darkblue','User: <b>' . $usrrow['name'] . ' / ' . $usrrow['email'] . '</b>');
		echo '&nbsp;&nbsp;&nbsp;&nbsp; Reset passwd?';
		formselectsession('resetpasswd','bool',0);
		echo 'chgpasswd:';
		if ($usrrow['chgpasswd']) {
					echo spanformat('','red',':T ');
				} else {
					echo spanformat('','blue',':F ');
				}
		echo '&nbsp;&nbsp;&nbsp;&nbsp; Activ?';
		formselectsession('activ','bool',$usrrow['activ']);	
		echo formsubmit('act','Submit') . '<br>';
		echo '</form>';
		echo formpost($thisform);
		echo formhiddenval('usrid',$usrrow['id']);
		echo ' &nbsp;&nbsp;Delete?';
		formselectsession('userdelete','bool',0);
		echo formsubmit('act','Delete User') . '<br>';
		echo '</form>';
		$q = "SELECT `role`.* , `accrole`.`id` AS `accroleid` FROM `accrole`,`role` WHERE `accrole`.`role_id` = `role`.`id` AND `accrole`.`account_id` = '" . $usrrow['id'] . "' ;";
		$sqlrole = $mysqli->dbquery($q);
		while ($rolerow = $sqlrole->fetch_assoc()) {
			echo formpost($thisform.'#acc'.$usrrow['id'].'div');
			echo formhiddenval('accroleid',$rolerow['accroleid']);
			if ($rolerow['accroleid'] == $_POST['accroleid']) {
				formselectsession('newroleid','roleslist',$rolerow['id']);
				echo formsubmit('act','Change Role');
			} else {
				echo formsubmit('act','Edit Role');
				echo '&nbsp;&nbsp;&nbsp;&nbsp; ' . $rolerow['rolename'] . ' / ' . $rolerow['description'];
				echo ' &nbsp;&nbsp;Delete?';
				formselectsession('roledelete','bool',0);
				echo formsubmit('act','Delete Role') .'<br>';
			}
			echo '</form>';
		}
		echo formpost($thisform.'#acc'.$usrrow['id'].'div');
		echo formhiddenval('usrid',$usrrow['id']);
		formselectsession('newroleid','roleslist',15);
		echo formsubmit('act','Add Role');
		echo '</form><p>';	
		
	}
  }
	
?>

		
 
</div>