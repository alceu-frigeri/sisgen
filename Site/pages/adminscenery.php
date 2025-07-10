
<?php $thisform=$GBLbasepage.'?q=admin&sq=scenery'; ?>

<?php

	$GBLmysqli->postsanitize();

	if ($_SESSION['role']['isadmin']) {
		switch($_POST['act']) {
			case 'Delete':
				if ($_POST['scenerydelete']) {
					$q = "DELETE FROM `scenery` WHERE `id` = '" . $_POST['sceneryid'] . "';";
					$GBLmysqli->dbquery($q);
					$_POST['sceneryid']=null;
				}
			break;
			case 'Submit':
					$q = "UPDATE `scenery` SET `name` = '".$_POST['sceneryname'] ."', `desc` = '".$_POST['scenerydesc'] ."' , `hide` = '".$_POST['sceneryhide'] ."' ";
					$q = $q .  " WHERE `id` = '".$_POST['sceneryid']."';";
					$GBLmysqli->dbquery($q);
				$_POST['sceneryid'] = null;
			break;
			case 'Add Scenery':
				if ($_POST['addscenery']) {
					$q = "INSERT INTO `scenery` (`name`, `desc`,`hide`) VALUES ('"   . $_POST['sceneryname'] . "' , '" . $_POST['scenerydesc'] . "' , '" . $_POST['sceneryhide'] . "');";					
					$GBLmysqli->dbquery($q);
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
//  $pattern = '[a-zA-Z0-9 :\+\-\.\(\)]+';
  if($_SESSION['role']['isadmin']) {
	$q = "SELECT * FROM `scenery` ORDER BY `name`;";
	$sqlsceneries = $GBLmysqli->dbquery($q);
	while ($sceneryrow = $sqlsceneries->fetch_assoc()) {
		
		if ($sceneryrow['id'] == $_POST['sceneryid']) {
			echo hiddendivkey('scen',$sceneryrow['id']);
			highlightbegin();
			echo formpost($thisform) . formhiddenval('sceneryid',$sceneryrow['id']);
			echo 'Nome:' . formpatterninput(32,8,$GBLnamepattern,'scenery name','sceneryname',$sceneryrow['name']) .
			 'Descrição:' . formpatterninput(64,32,$GBLcommentpattern,'scenery description','scenerydesc',$sceneryrow['desc']);
			echo 'hide scenery? ';
     		formselectsession('sceneryhide','bool',$sceneryrow['hide']);
			echo '<br>' . formsubmit('act','Submit');
			echo '</form>';			
			highlightend();
			echo formpost($thisform) . formhiddenval('sceneryid',$sceneryrow['id']);
			echo spanformat('','red','Delete scenery &lt;'.$sceneryrow['name'] .'&gt;?',null,true);
			formselectsession('scenerydelete','bool',0);
			echo spanformat('','red',formsubmit('act','Delete'),null,true);
			echo '</form><br><hr>';			
		} else {
//			echo formpost($thisform.'#scen'.$sceneryrow['id'].'div') . formhiddenval('sceneryid',$sceneryrow['id']) . formsubmit('act','Edit');
			echo formpost($thisform.targetdivkey('scen',$sceneryrow['id'])) . formhiddenval('sceneryid',$sceneryrow['id']) . formsubmit('act','Edit');
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
		echo formpost($thisform);
		echo 'Nome:' . formpatterninput(32,8,$GBLnamepattern,'scenery name','sceneryname','!') .
		 'Descriçao:' . formpatterninput(64,32,$GBLcommentpattern,'scenery name','scenerydesc','!');
			echo '  hidden? ';
			formselectsession('sceneryhide','bool',0);
		echo '<br> Add scenery? ';
		formselectsession('addscenery','bool',0);
		echo  formsubmit('act','Add Scenery');
		echo '</form><br>';			

  }
	
?>

		
 
</div> 