
<?php 

	$thisform=$GBLbasepage.'?q=admin&sq=adminacc'; 

	$GBLmysqli->postsanitize();

	unset($_SESSION['rolelist']);
		$q = "SELECT * FROM `role`;";
		$result = $GBLmysqli->dbquery($q);
		while ($sqlrow = $result->fetch_assoc()) {
			$_SESSION['roleslist'][$sqlrow['id']] = $sqlrow['rolename'] .'  /  '.$sqlrow['description'];
		}


	
	if($_SESSION['role']['isadmin']) {
		switch ($_POST['act']) {
			case 'Submit':
				if($_POST['resetpasswd']) {
					$pass = " , `password` = 'abc123xyz' , `chgpasswd` = '1' ";
					$q = "UPDATE `account` SET `activ` = '" . $_POST['activ'] . "' $pass WHERE `id` = '" . $_POST['usrid'] . "';";
					$GBLmysqli->dbquery($q);
				}
			break;
			case 'Delete User':
				if ($_POST['userdelete']) {
					$q = "DELETE FROM `account` WHERE `id` = '" . $_POST['usrid'] . "';";
					$GBLmysqli->dbquery($q);
					$_POST['accroleid'] = null;
				}
			break;
			case 'Delete Role':
				if ($_POST['roledelete']) {
					$q = "DELETE FROM `accrole` WHERE `id` = '" . $_POST['accroleid'] . "';";
					$GBLmysqli->dbquery($q);
				}
				$_POST['accroleid'] = null;
			break;
			case 'Change Role':
				if(fieldscompare('',array('newroleid'))){
					$q = "UPDATE `accrole` SET `role_id` = '" . $_POST['newroleid'] . "' WHERE `id` = '" . $_POST['accroleid'] . "';";
					$GBLmysqli->dbquery($q);
				}
				$_POST['accroleid'] = null;
			break;
			case 'Add Role':
				$q = "INSERT INTO `accrole` (`account_id`, `role_id`) VALUES ('" . $_POST['usrid'] . "' , '" . $_POST['newroleid'] . "');";
				$GBLmysqli->dbquery($q);
				$_POST['accroleid'] = null;
			break;		
		}
	}

	$GBLmysqli->set_scenerysessionvalues();
	$GBLmysqli->set_rolesessionvalues();

?>



<div class="row">
        <h2>Contas de Usuários</h2>
        <hr>


<?php
  if($_SESSION['role']['isadmin']) {
	$q = "SELECT * FROM `account` ORDER BY `name`;";
	$sqlusers = $GBLmysqli->dbquery($q);
	while ($usrrow = $sqlusers->fetch_assoc()) {
                echo hiddendivkey('acc',$usrrow['id']);

		echo formpost($thisform.targetdivkey('acc',$usrrow['id']));
		echo formhiddenval('usrid',$usrrow['id']);
		if ($usrrow['id'] == $_POST['usrid']) {
			highlightbegin();
		}
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
		echo spanformat('','red',' &nbsp;&nbsp;Delete?',null,true);
		formselectsession('userdelete','bool',0);
		echo spanformat('','red',formsubmit('act','Delete User') . '<br>',null,true);
		echo '</form>';
		$q = "SELECT `role`.* , `accrole`.`id` AS `accroleid` FROM `accrole`,`role` WHERE `accrole`.`role_id` = `role`.`id` AND `accrole`.`account_id` = '" . $usrrow['id'] . "' ;";
		$sqlrole = $GBLmysqli->dbquery($q);
		while ($rolerow = $sqlrole->fetch_assoc()) {
                        echo formpost($thisform.targetdivkey('acc',$usrrow['id']));
			echo formhiddenval('accroleid',$rolerow['accroleid']);
			echo formhiddenval('usrid',$usrrow['id']);
			if ($rolerow['accroleid'] == $_POST['accroleid']) {
				formselectsession('newroleid','roleslist',$rolerow['id']);
				echo formsubmit('act','Change Role');
			} else {
				echo formsubmit('act','Edit Role');
				echo '&nbsp;&nbsp;&nbsp;&nbsp; ' . $rolerow['rolename'] . ' / ' . $rolerow['description'];
				echo spanformat('','red',' &nbsp;&nbsp;Delete?');
				formselectsession('roledelete','bool',0);
				echo spanformat('','red',formsubmit('act','Delete Role') .'<br>');
			}
			echo '</form>';
		}
                echo formpost($thisform.targetdivkey('acc',$usrrow['id']));
		echo formhiddenval('usrid',$usrrow['id']);
		formselectsession('newroleid','roleslist',15);
		echo formsubmit('act','Add Role');
		echo '</form>';	
		if ($usrrow['id'] == $_POST['usrid']) {
			highlightend();
		}
	
		
	}
  }
	
?>

		
 
</div> 