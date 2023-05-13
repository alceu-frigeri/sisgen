
<?php $thisform=$basepage.'?q=edits&sq=Prof'; ?>
<div class="row">
    
        <h2>Professores</h2>
        <hr>


	
<?php 
    $can_prof = $_SESSION['role']['isadmin'] | ($_SESSION['role'][$_POST['unitid']] & $_SESSION['role'][$_POST['unitid']]['can_prof']) ;
	$postedit = (($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit') | ($_POST['act'] == 'Delete') | ($_POST['act'] == 'Insert'));

	$mysqli->postsanitize();

	echo formpost($thisform);
	
	if (!($_SESSION['profkind'])) {
		$q = "SELECT * FROM `profkind`; ";
		$result = $mysqli->dbquery($q);
		while ($sqlrow = $result->fetch_assoc()) {
			$_SESSION['profkind'][$sqlrow['id']] = $sqlrow['acronym'];
		}
	}
	
	switch ($_POST['act']) {
		case 'Insert':
			$q = "INSERT INTO `prof` (`dept_id`,`profkind_id`,`name`,`nickname`) VALUES ('" . $_POST['unitid'] . "','" . $_POST['profkind'] . "','" . $_POST['profname'] . "','" . $_POST['profnickname']  . "');";
			$mysqli->dbquery($q);
			$_POST['profid'] = null;
		break;
		case 'Submit':
			$q = "UPDATE `prof` SET `profkind_id` = '" . $_POST['profkind'] . "' , `name` = '" . $_POST['profname']  . "' , `nickname` = '" . $_POST['profnickname'] . "'  WHERE `id` = '" . $_POST['profid'] . "'";
			$mysqli->dbquery($q);
			$_POST['profid'] = null;
		break;
		case 'Delete':
			if ($_POST['profdelete']) {
				$q = "DELETE FROM `prof` WHERE `id` = '" . $_POST['profid'] . "';";
				$mysqli->dbquery($q);
			}
			$_POST['profid'] = null;
		break;
	}

	if ($postedit & $can_prof) {
		echo formhiddenval('unitid',$_POST['unitid']);
		echo displaysqlitem('','unit',$_POST['unitid'],'acronym','name');
		echo formsubmit('act','Cancel');
		echo '</form>';
	} else {
		formselectsql($anytmp,"SELECT * FROM unit WHERE `isdept` = '1' AND `mark` = '1' ORDER BY unit.acronym;",'unitid',$_POST['unitid'],'id','acronym');
	}
	
	
		
	?>
	
<br>



		
<?php


// course, term
  $q = "SELECT * FROM   `prof` WHERE `dept_id` = '".$_POST['unitid']."' ORDER BY `profkind_id`,`name`;";

  $result=$mysqli->dbquery($q);
  $anyone = 0;
  if ($postedit & $can_prof) {
	  while ($sqlrow=$result->fetch_assoc()) {
	  	echo formpost($thisform);
		echo formhiddenval('unitid',$_POST['unitid']);
		if ($_POST['profid'] == $sqlrow['id']) {
			echo formhiddenval('profid',$sqlrow['id']);
			formselectsession('profkind','profkind',$sqlrow['profkind_id']);
            echo formpatterninput(120,64,$namepattern,'Nome completo','profname',$sqlrow['name']);
			echo '&nbsp;&nbsp; ' . formpatterninput(64,32,$namepattern,'Nome abreviado','profnickname',$sqlrow['nickname']);
			echo formsubmit('act','Submit');
			echo '</form>';
			echo formpost($thisform);
			echo formhiddenval('unitid',$_POST['unitid']);
			echo formhiddenval('profid',$sqlrow['id']);
			echo '  &nbsp;&nbsp;&nbsp;remover: ';
			formselectsession('profdelete','bool',0);
			echo formsubmit('act','Delete');
		} else {
			echo formsubmit('act','Edit');
			echo formhiddenval('profid',$sqlrow['id']);
			echo $_SESSION['profkind'][$sqlrow['profkind_id']].'&nbsp;&nbsp;'.$sqlrow['name'].'&nbsp;&nbsp; ('.$sqlrow['nickname'].')<br>';
		}
		echo '</form>';
	  }
	  echo '<br>';
	  echo '<i>inserção</i>';


	  	echo formpost($thisform);
		echo formhiddenval('unitid',$_POST['unitid']);
		formselectsession('profkind','profkind',1);
        echo formpatterninput(120,64,$namepattern,'Nome completo','profname','-');
		echo '&nbsp;&nbsp; ' . formpatterninput(64,32,$namepattern,'Nome abreviado','profnickname','-');
		echo formsubmit('act','Insert') ;
		echo '</form>';
	  

  } else {
	  while ($sqlrow=$result->fetch_assoc()) {
		  $anyone = 1;
		  echo $_SESSION['profkind'][$sqlrow['profkind_id']].'&nbsp;&nbsp;'.$sqlrow['name'].'&nbsp;&nbsp; ('.$sqlrow['nickname'].')<br>';
	  }
  }



  if ($postedit & $can_prof) {
  } else {
  	if ($anyone & $can_prof) {
		echo formsubmit('act','Edit');
	}
	echo formsubmit('act','Refresh');
  }

	echo '</form>';
	

 ?>

    </div>
  


