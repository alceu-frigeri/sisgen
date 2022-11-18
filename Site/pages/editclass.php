

<?php $thisform=$basepage.'?q=edits&sq=classes'; 
?>

<div class="row">
        <h2>Turmas</h2>
        <hr>



<?php 
    //vardebug($_POST);
	
	$q = "SELECT readonly FROM semester WHERE id = '" . $_POST['semid'] . "';";
	$result = $mysqli->dbquery($q);
	$sqlrow = $result->fetch_assoc();
	$readonly = $sqlrow['readonly'];

	$can_class=($_SESSION['role'][$_POST['unitid']]['can_class'] | $_SESSION['role']['isadmin']) & !$readonly;
	$can_addclass=($_SESSION['role'][$_POST['unitid']]['can_addclass'] | $_SESSION['role']['isadmin']) & !$readonly;
	$postedit = (($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit') | ($_POST['act'] == 'Add Class')) & !$readonly;
	
	$mysqli->postsanitize();

	include 'coreedit.php';

    $Gblvackind=array();
	$q="SELECT `kind`.`code` , `cd`.`course_id` , `term`.`code` AS `trm` FROM `disciplinekind` AS `kind` , `coursedisciplines` AS `cd` , `term` WHERE `cd`.`discipline_id` = '".$_POST['discid']."' AND `cd`.`disciplinekind_id` = `kind`.`id` AND `cd`.`term_id` = `term`.`id`;";
	$kindsql=$mysqli->dbquery($q);
	while($kindrow=$kindsql->fetch_assoc()) {
		$Gblvackind[$kindrow['course_id']]=$kindrow['code'].'-'.$kindrow['trm'];
	}

////

function agregupdt($aclassid) {
	global $mysqli;

	$q="SELECT `cd`.`course_id` FROM `coursedisciplines` AS `cd` WHERE `cd`.`discipline_id` = '".$_POST['discid']."' ";
	$aclasssql=$mysqli->dbquery($q);
	while($aclassrow=$aclasssql->fetch_assoc()) {
		$q = "SELECT  SUM(`vac`.`givennum`) AS `givensum`, SUM(`vac`.`askednum`) AS `askedsum` FROM `vacancies` AS `vac`,`class` " .
		"WHERE `vac`.`class_id` = `class`.`id` AND `class`.`partof` = '".$aclassid."' AND `vac`.`course_id` = '".$aclassrow['course_id']."';";
		$sumsql=$mysqli->dbquery($q);
		$sumrow=$sumsql->fetch_assoc();
		$q="UPDATE `vacancies`  SET `givennum` = '".$sumrow['givensum']."' , `askednum` = '".$sumrow['askedsum']."' WHERE `class_id` = '".$aclassid."' AND `course_id` = '".$aclassrow['course_id']."';";
		$mysqli->dbquery($q);
	}
}


/////



	//vardebug($_POST);
	if (!$readonly) {
		switch ($_POST['act']){
			case 'Submit':
				$mysqli->eventlog(array('level'=>'INFO','action'=>'class edit','str'=>displaysqlitem('','semester',$_POST['semid'],'name').displaysqlitem('','discipline',$_POST['discid'],'code'),'xtra'=>'editclass.php'));
				
				foreach ($_SESSION['segments'] as $segid => $segkey) {
					if (($_POST[$segkey.'delete'])) {
						$q = "DELETE FROM `classsegment` WHERE `id` = '" . $segid . "';";
						$mysqli->dbquery($q);
					} else {
						if (fieldscompare($segkey,array('day','start','length','room','prof','status'))) {
							$q = "UPDATE `classsegment` SET `day` = '" . $_POST[$segkey.'day']  . "' , `start` = '" . $_POST[$segkey.'start']  . 
								"' , `length` = '" . $_POST[$segkey.'length']  . "' , `room_id` = '" . $_POST[$segkey.'room']  . 
								"' , `prof_id` = '" . $_POST[$segkey.'prof']  . "' , `status_id` = '" . $_POST[$segkey.'status'] . "' WHERE `id` = '" . $segid  . "';";
							$mysqli->dbquery($q);
						}
					}
				}
				$anyone=0;
				foreach ($_SESSION['vacancies'] as $vacid => $vackey) {
					if (fieldscompare($vackey,array('asked','askedstatusid','given','givenstatusid','comment'))) {
						$anyone=1;
						$q = "UPDATE `vacancies` SET `askednum` = '" . $_POST[$vackey.'asked']  . "' , `askedstatus_id`= '" . $_POST[$vackey.'askedstatusid'] . "' , `givennum`= '" . $_POST[$vackey.'given']  . "' , `givenstatus_id`= '" . $_POST[$vackey.'givenstatusid'] . "' , `comment`= '" . $_POST[$vackey.'comment'] . "' WHERE `id` = '" . $vacid  . "';";
						$mysqli->dbquery($q);
					}
				}
				$segadded = 0;
				foreach ($_SESSION['classes'] as $classid => $classkey) {
					if (($_POST[$classkey.'delete'])) {
						$q = "DELETE FROM `class` WHERE `id` = '" . $classid . "';";
						$mysqli->dbquery($q);
					} else {
						if($_POST[$classkey.'agreg']) {$_POST[$classkey.'partof']=0;}
						if (fieldscompare($classkey,array('agreg','status','comment','partof'))) {
							if ($_POST[$classkey.'partof']) {
								$part = "`partof`= '" . $_POST[$classkey.'partof'] . "' ";
							} else {
								$part = "`partof`= NULL ";
							}
							$q = "UPDATE `class` SET `agreg`= '" . $_POST[$classkey.'agreg'] . "' , `status_id`= '" . $_POST[$classkey.'status'] . "' , `comment`= '" . $_POST[$classkey.'comment'] . "' , $part WHERE `id`= '" . $classid . "';";
							$mysqli->dbquery($q);
							if ($_SESSION['org'][$classkey.'agreg']  & !$_POST[$classkey.'agreg']) {
								$q = "UPDATE `class` SET `partof` = NULL WHERE `partof` = '" . $classid . "';";
								$mysqli->dbquery($q);
							}
							if (!$_SESSION['org'][$classkey.'agreg']  & $_POST[$classkey.'agreg']) {
								$q = "UPDATE `vacancies` SET `givennum` = '0' , `askednum` = '0' WHERE `class_id` = '" . $classid . "';";
								$mysqli->dbquery($q);
							}
						}
						if (($_POST[$classkey.'addsegment'])) {
							reset($_SESSION['deptprof'.$_POST['unitid']]);
							$q = "INSERT INTO `classsegment` (`day`,`start`,`length`,`room_id`,`prof_id`,`class_id`) VALUES ('2','7','2','1','" . key($_SESSION['deptprof'.$_POST['unitid']]) . "','" . $classid . "');";
							$mysqli->dbquery($q);
							$segadded = 1;
						}
					}
					if($_POST[$classkey.'delete'] ) {
						if($_SESSION['org'][$classkey.'partof']) {
								agregupdt($_SESSION['org'][$classkey.'partof']);
						}
					} else {
						if ($_POST[$classkey.'partof'] == $_SESSION['org'][$classkey.'partof']) {
							if($anyone) {
								agregupdt($_SESSION['org'][$classkey.'partof']);
							}
						} else {
							if($_POST[$classkey.'partof']) {
								// vacancie adjust ...'new' partof 
								agregupdt($_POST[$classkey.'partof']);
							}
							if($_SESSION['org'][$classkey.'partof']) {
								agregupdt($_SESSION['org'][$classkey.'partof']);
							}
						}
					}
				}
				if(!$segadded) {
					$_POST['classid']=null;
				}
			break;
			case 'Add Class':
				if ($_POST['addclass']) {
					$q = "INSERT INTO `class` (`name`,`sem_id`,`discipline_id`) VALUES ('".$_POST['newclassname']."','".$_POST['semid']."','".$_POST['discid']."');";
					if($mysqli->dbquery($q)) {
						$newclassid = $mysqli->insert_id;
						$q = "SELECT * FROM coursedisciplines WHERE discipline_id = '" . $_POST['discid'] . "';";
						$result = $mysqli->dbquery($q);
						while ($sqlrow = $result->fetch_assoc()) {
							$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) VALUES ('" . $newclassid . "','" . $sqlrow['course_id'] . "','0','0');";
							$mysqli->dbquery($q);
						}
					}
				}
			break;
		}
	}
	
	unset($_SESSION['segments']);
	unset($_SESSION['vacancies']);
	unset($_SESSION['classes']);
	unset($_SESSION['agreg']);
	unset($_SESSION['org']);




	if ($postedit) {
		thisformpost();
		echo displaysqlitem('Semestre: ','semester',$_POST['semid'],'name');
		echo displaysqlitem('&nbsp;&nbsp;&nbsp;&nbsp;Dept.: ','unit',$_POST['unitid'],'acronym');
		echo '<br><b>';
		echo displaysqlitem('&nbsp;&nbsp;','discipline',$_POST['discid'],'code','name');
		echo '</b>';
		echo formsubmit('act','Cancel');
		echo spanformat('smaller',$commentcolor, displaysqlitem('&nbsp;&nbsp;','discipline',$_POST['discid'],'comment')) ;
		echo  '</form>';
	} else {
		echo formpost($thisform);
		formselectsql($anytmp,"SELECT * FROM semester ORDER BY name;",'semid',$_POST['semid'],'id','name');
		formselectsql($anytmp,"SELECT * FROM unit  ORDER BY unit.iscourse DESC, unit.acronym ASC;",'unitid',$_POST['unitid'],'id','acronym');
		formselectsql($anytmp,"SELECT * FROM discipline WHERE discipline.dept_id = '".$_POST['unitid']."' ORDER BY name;",'discid',$_POST['discid'],'id','code','name');
	}
	echo '<hr>';


	$q = "SELECT class.* FROM class,discipline WHERE class.discipline_id = discipline.id AND class.discipline_id = '" . $_POST['discid'] . "' AND class.sem_id = '" . $_POST['semid'] . "' AND discipline.dept_id = '" . $_POST['unitid'] . "' AND class.agreg = '1' ORDER BY class.name;";
	$result = $mysqli->dbquery($q);
	unset($_SESSION['agreg']);
	while ($sqlrow = $result->fetch_assoc()) {
		$_SESSION['agreg'][$sqlrow['id']] = $sqlrow['name'];
	}	
	$q = "SELECT class.* FROM class,discipline WHERE class.discipline_id = discipline.id AND class.discipline_id = '" . $_POST['discid'] . "' AND class.sem_id = '" . $_POST['semid'] . "' AND discipline.dept_id = '" . $_POST['unitid'] . "' ORDER BY class.name;";
	$result = $mysqli->dbquery($q);
	
	$anyone = 0;
	while ($classrow = $result->fetch_assoc()) {
		$anyone = 1;
		if ($postedit) {
			thisformpost();
			echo formhiddenval('classid',$classrow['id']);
			if ($classrow['id'] == $_POST['classid']) {
				if($can_class) {
					formclassedit($classrow);
				} else {
					formclassdisplay($classrow,true);
				}
				echo formsubmit('act','Submit');
			} else {
				echo formsubmit('act','Edit');
				formclassdisplay($classrow);
			}
			echo '</form>';
		} else {
			formclassdisplay($classrow);
		}
		echo '<hr>';
	}
	echo '</p>';
	if ($postedit & $can_addclass) {
		thisformpost();
		echo 'Adicionar Turma:' . formpatterninput(3,1,'[A-Z][A-Z0-9]*','Nova Turma','newclassname','');
		formselectsession('addclass','bool',0);
		echo formsubmit('act','Add Class') . '</form>';
	}

	if (!$readonly) {
		if ($_POST['semid'] && $_POST['unitid'] && $_POST['discid']) {
			echo formsubmit('act','Refresh') . formsubmit('act','Edit') . '</form>';
		} else {
			echo formsubmit('act','Refresh') . '</form>';
		}
	} else {
		echo formsubmit('act','Refresh') . '</form>';

	}

?>




		
    
 
</div>