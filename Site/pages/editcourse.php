
<?php $thisform=$basepage.'?q=edits&sq=Course'; ?>
<div class="row">
    
        <h2>Grades Curriculares</h2>
        <hr>


	
<?php 
	$can_coursedisciplines = ($_SESSION['role']['isadmin'] | ($_SESSION['role'][$_POST['courseid']] & $_SESSION['role'][$_POST['courseid']]['can_coursedisciplines']));
	$postedit = (($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit') | ($_POST['act'] == 'Delete') | ($_POST['act'] == 'Insert') | ($_POST['act'] == 'Reload'));
	
	$mysqli->postsanitize();
	
	echo formpost($thisform);
	
	if(!($_SESSION['disckind'])) {
		$q = "SELECT * FROM `disciplinekind`";
		$result = $mysqli->dbquery($q);
		while ($sqlrow=$result->fetch_assoc()) {
			$_SESSION['disckind'][$sqlrow['id']] = $sqlrow['code'];
		}
	}
	if(!($_SESSION['term'])) {
		$q = "SELECT * FROM `term`";
		$result = $mysqli->dbquery($q);
		while ($sqlrow=$result->fetch_assoc()) {
			$_SESSION['term'][$sqlrow['id']] = $sqlrow['code'];
		}
	}
	
	switch ($_POST['act']) {
		case 'Insert':
			if ($can_coursedisciplines) {
				$q = "INSERT INTO `coursedisciplines` (`course_id`,`term_id`,`discipline_id`,`disciplinekind_id`) VALUES ('" . $_POST['courseid'] . "','" . $_POST['termid'] . "','" . $_POST['discid'] . "','" . $_POST['newdisckind'] . "');";
				$mysqli->dbquery($q);
				$_POST['coursediscid'] = null;
			}
		break;
		case 'Submit':
			if ($can_coursedisciplines) {
				if(fieldscompare('',array('newterm','newkind'))) {
					$q = "UPDATE `coursedisciplines` SET `term_id` = '" . $_POST['newterm'] . "' , `disciplinekind_id` = '" . $_POST['newkind'] . "' WHERE `id` = '" . $_POST['coursediscid'] . "'";
					$mysqli->dbquery($q);
				}
				$_POST['coursediscid'] = null;
			}
		break;
		case 'Delete':
			if ($can_coursedisciplines & $_POST['discdelete']) {
				$q = "DELETE FROM `coursedisciplines` WHERE `id` = '" . $_POST['coursediscid'] . "';";
				$mysqli->dbquery($q);
				$q = "DELETE FROM `vacancies` WHERE `vacancies`.`course_id` = '".$_POST['courseid']."' AND `vacancies`.`class_id` IN (SELECT `class`.`id` FROM `class` , `discipline` AS `disc` WHERE `class`.`discipline_id` = `disc`.`id` AND `disc`.`id` = '".$_POST['coursediscid']."');";
				$mysqli->dbquery($q);
			}
		break;
	}
	


	if($postedit & $can_coursedisciplines) {
		echo formhiddenval('courseid',$_POST['courseid']);
		echo displaysqlitem('','unit',$_POST['courseid'],'acronym');
		echo formhiddenval('termid',$_POST['termid']);
		echo displaysqlitem('-- ','term',$_POST['termid'],'name');
		echo formsubmit('act','Cancel');
		echo '</form>';
	} else {
		formselectsql($anytmp,"SELECT * FROM unit WHERE iscourse = 1 ORDER BY unit.name;",'courseid',$_POST['courseid'],'id','acronym');
		formselectsql($anytmp,"SELECT * FROM term ORDER BY term.name;",'termid',$_POST['termid'],'id','name');
	}
	
	
		
	?>
	
<br>



		
<?php



// course, term
  $q = "SELECT `discipline`.`code` , `discipline`.`name` , `disciplinekind`.`code` AS disckindcode , `disciplinekind`.`id` AS disckindid , `coursedisciplines`.`id` FROM   `term`, `coursedisciplines`,`discipline`,`disciplinekind` WHERE " .
	"`coursedisciplines`.`course_id` = '".$_POST['courseid']."' AND `coursedisciplines`.`term_id` = `term`.`id` AND " . 
	"`coursedisciplines`.`discipline_id` = `discipline`.`id` AND `coursedisciplines`.`disciplinekind_id` = `disciplinekind`.`id` AND " .
	"`term`.`id` = '".$_POST['termid']."'";

  $result=$mysqli->dbquery($q);
  $anyone = 0;
  if ($postedit & $can_coursedisciplines) {
	  while ($sqlrow=$result->fetch_assoc()) {
	  	echo formpost($thisform);
		echo formhiddenval('courseid',$_POST['courseid']);
		echo formhiddenval('termid',$_POST['termid']);
		echo formhiddenval('coursediscid',$sqlrow['id']);
		if ($_POST['coursediscid'] == $sqlrow['id']) {
			echo $sqlrow['code'].' -- '.$sqlrow['name'].'  ';
			formselectsession('newterm','term',$_POST['termid']);
			formselectsession('newkind','disckind',$sqlrow['disckindid']);
			echo formsubmit('act','Submit') . '</form>';
			echo formpost($thisform);
			echo '  &nbsp;&nbsp;&nbsp;remover: ';
			formselectsession('discdelete','bool',0);
			echo formhiddenval('courseid',$_POST['courseid']);
			echo formhiddenval('termid',$_POST['termid']);
			echo formhiddenval('coursediscid',$sqlrow['id']);
			echo formsubmit('act','Delete') . '<br>';
		} else {
			echo formsubmit('act','Edit');
			echo $sqlrow['code'].' -- '.$sqlrow['name']. ' ('.$sqlrow['disckindcode'].')<br>';
		}
		echo '</form>';
	  }
	  echo '<br>';
	  echo '<i>inserção</i>';
	  echo formpost($thisform);
		echo formhiddenval('courseid',$_POST['courseid']);
		echo formhiddenval('termid',$_POST['termid']);
		echo formhiddenval('coursediscid',$sqlrow['id']);

		$q = "SELECT * FROM `unit` ORDER BY `acronym`";
		formselectsql($anytmp,$q,'unitid',$_POST['unitid'],'id','acronym');

		$q = "SELECT * FROM `discipline` WHERE `dept_id` = '" . $_POST['unitid'] . "' ORDER BY `name`";
		formselectsql($anyone,$q,'discid',$_POST['discid'],'id','code','name');
		
		formselectsession('newdisckind','disckind',1);
		echo formsubmit('act','Reload');
		if ($anyone) {
			echo formsubmit('act','Insert');
		}
	  echo '</form>';
	  

  } else {
	  while ($sqlrow=$result->fetch_assoc()) {
		  $anyone = 1;
		  echo $sqlrow['code'].'  '.$sqlrow['name']. ' ('.$sqlrow['disckindcode'].')<br>';
	  }
  }



	if ($postedit & $can_coursedisciplines) {
	} else {
	if ($anyone & $can_coursedisciplines) {
		echo formsubmit('act','Edit');
	}
	echo formsubmit('act','Refresh');
	}

	echo '</form>';
	

 ?>

    </div>
  


