
<?php $thisform=$basepage.'?q=admin&sq=adminroles'; ?>

<?php
	$bfields = array('isadmin');
	$canfields = array('edit','dupsem','class','addclass','vacancies','disciplines','coursedisciplines','prof','room','viewlog');
	$mysqli->postsanitize();

	if ($_SESSION['role']['isadmin']) {
		switch($_POST['act']) {
			case 'Delete':
				if ($_POST['roledelete']) {
					$q = "DELETE FROM `role` WHERE `id` = '" . $_POST['roleid'] . "';";
					$mysqli->dbquery($q);
					$_POST['roleid']=null;
				}
			break;
			case 'Submit':
					$q = "UPDATE `role` SET `rolename` = '".$_POST['rolename'] ."', `description` = '".$_POST['description'] ."' , `unit_id` = '".$_POST['unitid'] ."' ";
					foreach ($bfields as $key) {
						$q = $q .  " , `" . $key . "` = '" . $_POST[$key] . "'";
					}
					foreach ($canfields as $key) {
						$q = $q  . " , `can_" . $key . "` = '" . $_POST['can'.$key] . "'";
					}
					$q = $q .  " WHERE `id` = '".$_POST['roleid']."';";
					//echo $q.'<br>';
			//		$q = "UPDATE `accrole` SET `role_id` = '" . $_POST['newroleid'] . "' WHERE `id` = '" . $_POST['accroleid'] . "';";
					$mysqli->dbquery($q);
				$_POST['roleid'] = null;
			break;
			case 'Add Role':
				if ($_POST['addrole']) {
					$q = "INSERT INTO `role` (`rolename`, `description`,`unit_id`";
					foreach ($bfields as $key) {
						$q = $q .  " , `" . $key . "`";
					}
					foreach ($canfields as $key) {
						$q = $q  . " , `can_" . $key . "`";
					}
					$q = $q . ") VALUES ('" . $_POST['rolename'] . "' , '" . $_POST['description'] . "' , '" . $_POST['unitid'] . "'";
					foreach ($bfields as $key) {
						$q = $q .  " , '" . $_POST[$key] . "'";
					}
					foreach ($canfields as $key) {
						$q = $q  . " , '" . $_POST['can'.$key] . "'";
					}
					$q = $q . ');';
					
					$mysqli->dbquery($q);
				}
				$_POST['roleid'] = null;
			break;
		}
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
			formselectsql($anytmp,'SELECT * FROM `unit`;','unitid',$rolerow['unit_id'],'id','acronym');
			echo '<br>';
			foreach ($bfields as $key) {
				echo '  '.$key.': ';
				formselectsession($key,'bool',$rolerow[$key]);
			}
			echo '<br>';
			foreach ($canfields as $key) {
				echo '  '.$key.': ';
				formselectsession('can'.$key,'bool',$rolerow['can_'.$key]);
			}
			echo '<br>' . formsubmit('act','Submit');
			echo '</form>';			
			echo formpost($thisform) . formhiddenval('roleid',$rolerow['id']);
			echo 'Delete Role &lt;'.$rolerow['rolename'] .'&gt;?';
			formselectsession('roledelete','bool',0);
			echo formsubmit('act','Delete');
			echo '</form><br><hr>';			
			
		} else {
			echo formpost($thisform) . formhiddenval('roleid',$rolerow['id']) . formsubmit('act','Edit');
			echo $rolerow['rolename'] . ' ( ' . $rolerow['description'] . ' ) :: ' . $rolerow['acronym'] . ' <br>';
			foreach ($bfields as $key) {
				if ($rolerow[$key]) {
					echo spanformat('','red',$key . ':T ');
				} else {
					echo spanformat('','blue',$key . ':F ');
				}
			}
			echo '<br>';
			foreach ($canfields as $key) {
				if ($rolerow['can_'.$key]) {
					echo spanformat('','red',$key . ':T ');
				} else {
					echo spanformat('','blue',$key . ':F ');
				}
			}
			echo '</form><hr>';
		}
	}
		echo '<br>'.formpost($thisform);
		echo 'Nome:' . formpatterninput(32,8,$pattern,'role name','rolename','!') .
		 'Descriçao:' . formpatterninput(64,32,$pattern,'role name','description','!');
		formselectsql($anytmp,'SELECT * FROM `unit`;','unitid',0,'id','acronym');
		echo '<br>';
		foreach ($bfields as $key) {
			echo '  '.$key.': ';
			formselectsession($key,'bool',0);
		}
		echo '<br>';
		foreach ($canfields as $key) {
			echo '  '.$key.': ';
			formselectsession('can'.$key,'bool',0);
		}
		echo '<br> Add Role? ';
		formselectsession('addrole','bool',0);
		echo  formsubmit('act','Add Role');
		echo '</form><br>';			
	
	
	
  }
	
?>

		
 
</div>