
<?php $thisform=$basepage.'?q=admin&sq=adminroles'; ?>

<?php
	$bfields = array('isadmin');
	$canfields = array('edit','dupsem','class','addclass','vacancies','disciplines','coursedisciplines','prof','room','viewlog');

	if (($_POST['act'] == 'Delete') & $_SESSION['role']['isadmin']) {
		if ($_POST['roledelete']) {
			$q = "DELETE FROM `role` WHERE `id` = '" . $_POST['roleid'] . "';";
			$mysqli->dbquery($q);
		}
	}
	if (($_POST['act'] == 'Submit') & $_SESSION['role']['isadmin']) {
		$q = "UPDATE `role` SET `rolename` = '".$_POST['rolename'] ."', `description` = '".$_POST['description'] ."' , `unit_id` = '".$_POST['unitid'] ."' ";
		foreach ($bfields as $key) {
			$q = $q .  " , `" . $key . "` = '" . $_POST[$key] . "'";
		}
		foreach ($canfields as $key) {
			$q = $q  . " , `can_" . $key . "` = '" . $_POST['can'.$key] . "'";
		}
		$q = $q .  " WHERE `id` = '".$_POST[roleid]."';";
		//echo $q.'<br>';
//		$q = "UPDATE `accrole` SET `role_id` = '" . $_POST['newroleid'] . "' WHERE `id` = '" . $_POST['accroleid'] . "';";
		$mysqli->dbquery($q);
		$_POST['roleid'] = null;
	}
	if (($_POST['act'] == 'Add Role') & $_SESSION['role']['isadmin']) {
		$q = "INSERT INTO `role` (`account_id`, `role_id`) VALUES ('" . $_POST['usrid'] . "' , '" . $_POST['newroleid'] . "');";
		$mysqli->dbquery($q);
		$_POST['roleid'] = null;
	}
?>

<div class="row">
        <h2>Roles</h2>
        <hr>


<?php
  $pattern = '[a-zA-Z \-\.\(\)]+';
  if($_SESSION['role']['isadmin']) {
	$q = "SELECT `role`.*, `unit`.`acronym` FROM `role`,`unit` WHERE `role`.`unit_id` = `unit`.`id` ORDER BY `rolename`;";
	$sqlroles = $mysqli->dbquery($q);
	while ($rolerow = $sqlroles->fetch_assoc()) {
		if ($rolerow['id'] == $_POST['roleid']) {
			echo '<br>'.formpost($thisform) . formhiddenval('roleid',$rolerow['id']);
			echo 'Nome:' . formpatterninput(32,8,$pattern,'role name','rolename',$rolerow['rolename']) .
			 'Descriçao:' . formpatterninput(64,32,$pattern,'role name','description',$rolerow['description']);
			formselectsqlX($anytmp,'SELECT * FROM `unit`;','unitid',$rolerow['unit_id'],'id','acronym');
			echo '<br>';
			foreach ($bfields as $key) {
				echo '  '.$key.': ';
				formselectsessionX($key,'bool',$rolerow[$key]);
			}
			echo '<br>';
			foreach ($canfields as $key) {
				echo '  '.$key.': ';
				formselectsessionX('can'.$key,'bool',$rolerow['can_'.$key]);
			}
			echo '<br>' . formsubmit('act','Submit');
			echo '</form>';			
			echo formpost($thisform) . formhiddenval('roleid',$rolerow['id']);
			echo 'Delete Role &lt;'.$rolerow['rolename'] .'&gt;?';
			formselectsessionX('roledelete','bool',0);
			echo formsubmit('act','Delete');
			echo '</form><br><hr>';			
			
		} else {
			echo formpost($thisform) . formhiddenval('roleid',$rolerow['id']) . formsubmit('act','Edit');
			echo $rolerow['rolename'] . ' ( ' . $rolerow['description'] . ' ) :: ' . $rolerow['acronym'] . ' <br>';
			foreach ($bfields as $key) {
				if ($rolerow[$key]) {
					echo spanformat('color:red;',$key . ':T ');
				} else {
					echo spanformat('color:blue;',$key . ':F ');
				}
			}
			echo '<br>';
			foreach ($canfields as $key) {
				if ($rolerow['can_'.$key]) {
					echo spanformat('color:red;',$key . ':T ');
				} else {
					echo spanformat('color:blue;',$key . ':F ');
				}
			}
			echo '</form><hr>';
		}
	}
  }
	
?>

		
 
</div>