
<?php $thisform=$GBLbasepage.'?q=admin&sq=adminroles'; ?>

<?php
	$bfields = array('isadmin');
	$canfields = array('edit','dupsem','class','addclass','scenery','vacancies','disciplines','coursedisciplines','prof','room','viewlog');
	$GBLmysqli->postsanitize();

	if ($_SESSION['role']['isadmin']) {
		switch($_POST['act']) {
			case 'Delete':
				if ($_POST['roledelete']) {
					$q = "DELETE FROM `role` WHERE `id` = '" . $_POST['roleid'] . "';";
					$GBLmysqli->dbquery($q);
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
					$GBLmysqli->dbquery($q);
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
					
					$GBLmysqli->dbquery($q);
				}
				$_POST['roleid'] = null;
			break;
						
			case 'Delete Scenery':
				if ($_POST['scenerydelete']) {
					$q = "DELETE FROM `sceneryrole` WHERE `id` = '" . $_POST['sceneryroleid'] . "';";
					$GBLmysqli->dbquery($q);
				}
				$_POST['sceneryroleid'] = null;
				$_POST['roleid'] = null;
			break;
			case 'Change Scenery':
				if(fieldscompare('',array('newsceneryid'))){
					$q = "UPDATE `sceneryrole` SET `scenery_id` = '" . $_POST['newsceneryid'] . "' WHERE `id` = '" . $_POST['sceneryroleid'] . "';";
					$GBLmysqli->dbquery($q);
				}
				$_POST['sceneryroleid'] = null;
				$_POST['roleid'] = null;
			break;
			case 'Add Scenery':
				$q = "INSERT INTO `sceneryrole` (`role_id`, `scenery_id`) VALUES ('" . $_POST['roleid'] . "' , '" . $_POST['newsceneryid'] . "');";
				$GBLmysqli->dbquery($q);
				$_POST['sceneryroleid'] = null;
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
	$sqlroles = $GBLmysqli->dbquery($q);
	while ($rolerow = $sqlroles->fetch_assoc()) {
		
                echo hiddendivkey('role',$rolerow['id']);

		if ($rolerow['id'] == $_POST['roleid']) {
			echo '<br>'.formpost($thisform.targetdivkey('role',$rolerow['id'])) . formhiddenval('roleid',$rolerow['id']);
			echo '<table style="background-color:#E0FFE0;color:#8000B0;"><tr><td>';
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
			echo spanformat('','red','Delete Role &lt;'.$rolerow['rolename'] .'&gt;?','',true);
			formselectsession('roledelete','bool',0);
			echo spanformat('','red',formsubmit('act','Delete'),'',true);
			echo '</form><br>';			
			echo '</td></tr></table>';
		} else {
			echo formpost($thisform.'#role'.$rolerow['id'].'div') . formhiddenval('roleid',$rolerow['id']) . formsubmit('act','Edit');
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
			echo '</form>';
		}
		
			
		$q = "SELECT `scenery`.* , `sceneryrole`.`id` AS `sceneryroleid` FROM `sceneryrole`,`scenery` WHERE `sceneryrole`.`scenery_id` = `scenery`.`id` AND `sceneryrole`.`role_id` = '" . $rolerow['id'] . "' ;";
		$sqlscenery = $GBLmysqli->dbquery($q);
		while ($sceneryrow = $sqlscenery->fetch_assoc()) {
			echo formpost($thisform.'#role'.$rolerow['id'].'div');
			echo formhiddenval('sceneryroleid',$sceneryrow['sceneryroleid']);
			if ($sceneryrow['sceneryroleid'] == $_POST['sceneryroleid']) {
				formselectsession('newsceneryid','scen.all',$sceneryrow['id']);
				echo formsubmit('act','Change Scenery');
			} else {
				echo formsubmit('act','Edit Scenery');
				echo '&nbsp;&nbsp;&nbsp;&nbsp; ' . $sceneryrow['name'] . ' / ' . $sceneryrow['desc'];
				echo ' &nbsp;&nbsp;Delete?';
				formselectsession('scenerydelete','bool',0);
				echo formsubmit('act','Delete Scenery') .'<br>';
			}
			echo '</form>';
		}
		echo formpost($thisform.'#role'.$rolerow['id'].'div');
		echo formhiddenval('roleid',$rolerow['id']);
		formselectsession('newsceneryid','scen.all',15);
		echo formsubmit('act','Add Scenery');
		echo '</form><p><p>';

			
		
		
	}
	
	
	
	
		echo '<br>'.formpost($thisform);
		echo 'Nome:' . formpatterninput(32,8,$pattern,'role name','rolename','!') .
		 'Descriçao:' . formpatterninput(64,32,$pattern,'role name','description','!');
		formselectsql($anytmp,'SELECT * FROM `unit`;','unitid',0,'id','acronym',null,false);
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