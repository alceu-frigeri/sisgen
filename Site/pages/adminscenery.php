
<?php $thisform=$basepage.'?q=admin&sq=scenery'; ?>

<?php

	$mysqli->postsanitize();

	if ($_SESSION['role']['isadmin']) {
		switch($_POST['act']) {
			case 'Delete':
				if ($_POST['scenerydelete']) {
					$q = "DELETE FROM `scenery` WHERE `id` = '" . $_POST['sceneryid'] . "';";
					$mysqli->dbquery($q);
					$_POST['sceneryid']=null;
				}
			break;
			case 'Submit':
					$q = "UPDATE `scenery` SET `name` = '".$_POST['sceneryname'] ."', `desc` = '".$_POST['scenerydesc'] ."' , `hide` = '".$_POST['sceneryhide'] ."' ";
					$q = $q .  " WHERE `id` = '".$_POST['sceneryid']."';";
					$mysqli->dbquery($q);
				$_POST['roleid'] = null;
			break;
			case 'Add Scenery':
				if ($_POST['addscenery']) {
					$q = "INSERT INTO `scenery` (`name`, `desc`,`hide`) VALUES ('"   . $_POST['sceneryname'] . "' , '" . $_POST['scenerydesc'] . "' , '" . $_POST['sceneryhide'] . "');";					
					$mysqli->dbquery($q);
				}
				$_POST['sceneryid'] = null;
			break;
		}
	}
?>

<div class="row">
        <h2>Sceneries</h2>
        <hr>


<?php
  $pattern = '[a-zA-Z \-\.\(\)]+';
  if($_SESSION['role']['isadmin']) {
	$q = "SELECT * FROM `scenery` ORDER BY `name`;";
	$sqlsceneries = $mysqli->dbquery($q);
	while ($sceneryrow = $sqlsceneries->fetch_assoc()) {
		if ($sceneryrow['id'] == $_POST['sceneryid']) {
			echo '<br>'.formpost($thisform) . formhiddenval('sceneryid',$sceneryrow['id']);
			echo 'Nome:' . formpatterninput(32,8,$pattern,'scenery name','sceneryname',$sceneryrow['name']) .
			 'Descrição:' . formpatterninput(64,32,$pattern,'scenery description','scenerydesc',$sceneryrow['desc']);
			echo 'hide scenery? ';
     		formselectsession('sceneryhide','bool',$sceneryrow['hide']);
			echo '<br>' . formsubmit('act','Submit');
			echo '</form>';			
			echo formpost($thisform) . formhiddenval('sceneryid',$sceneryrow['id']);
			echo 'Delete scenery &lt;'.$sceneryrow['name'] .'&gt;?';
			formselectsession('scenerydelete','bool',0);
			echo formsubmit('act','Delete');
			echo '</form><br><hr>';			
			
		} else {
			echo formpost($thisform) . formhiddenval('sceneryid',$sceneryrow['id']) . formsubmit('act','Edit');
			echo $sceneryrow['name'] . ' ( ' . $sceneryrow['desc'] . ' )' . ' <br>';
				if ($sceneryrow['hide']) {
					echo spanformat('','red','hidden: T ');
				} else {
					echo spanformat('','blue','hidden: F ');
				}
			echo '<br>';
			echo '</form><hr>';
		}
	}
		echo '<br>'.formpost($thisform);
		echo 'Nome:' . formpatterninput(32,8,$pattern,'scenery name','sceneryname','!') .
		 'Descriçao:' . formpatterninput(64,32,$pattern,'scenery name','scenerydesc','!');
			echo '  hidden? ';
			formselectsession('sceneryhide','bool',0);
		echo '<br> Add scenery? ';
		formselectsession('addscenery','bool',0);
		echo  formsubmit('act','Add Scenery');
		echo '</form><br>';			

  }
	
?>

		
 
</div>