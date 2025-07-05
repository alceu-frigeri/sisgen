

<?php $thisform=$GBLbasepage.'?q=edits&sq=classes'; 
?>

<div class="row">
        <h2>Turmas</h2>
        <hr>



<?php 
    //vardebug($_POST);

				//vardebug($_SESSION['scenery']);
				//vardebug($_SESSION['org'][sceneryusr]);
				//vardebug($_SESSION['org'][sceneryclass]);
				//vardebug($_POST);
//				vardebug($_POST['class7387scenlst']);



	
	$q = "SELECT readonly FROM semester WHERE id = '" . $_POST['semid'] . "';";
	$result = $GBLmysqli->dbquery($q);
	$sqlrow = $result->fetch_assoc();
	$readonly = $sqlrow['readonly'];
	$hiddenprofdeptid = null;
	$hiddenroombuildingid = null;

	$can_class=($_SESSION['role'][$_POST['unitid']]['can_class'] | $_SESSION['role']['isadmin']) & !$readonly;
	$can_addclass=($_SESSION['role'][$_POST['unitid']]['can_addclass'] | $_SESSION['role']['isadmin']) & !$readonly;
	
	$GBLmysqli->postsanitize();
				//vardebug($_POST);

	$postedit = false;
	if ((($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit') ) & $can_class ) {
		$postedit = true;
	} else {
		if ((($_POST['act'] == 'Add Class') | ($_POST['act'] == 'Replicate Class') ) & $can_addclass) {
			$postedit = true;
		} else {
			$_POST['act'] = 'Cancel';
		}
	}

	

	include 'coreedit.php';
	//$GBLclasspattern = '[A-Z][A-Za-z0-9\*]*';

    $GBLvackind=array();
	$q="SELECT `kind`.`code` , `cd`.`course_id` , `term`.`code` AS `trm` , `term`.`id` AS `termid` FROM `disciplinekind` AS `kind` , `coursedisciplines` AS `cd` , `term` WHERE `cd`.`discipline_id` = '".$_POST['discid']."' AND `cd`.`disciplinekind_id` = `kind`.`id` AND `cd`.`term_id` = `term`.`id`;";
	$kindsql=$GBLmysqli->dbquery($q);
	while($kindrow=$kindsql->fetch_assoc()) {
		if ($kindrow['code'] == 'OB') {
			$kba = '<b>';
			$kbb = '</b>';
		} else {
			$kba=null;
			$kbb=null;
		}
		$GBLvackind[$kindrow['course_id']]=$kba . hiddenformlnk(hiddencoursekey($_POST['semid'],$kindrow['course_id'],$kindrow['termid']) , $kindrow['code'].'-'.$kindrow['trm']) . $kbb;
		
		echo hiddencourseform($_POST['semid'],$kindrow['course_id'],$kindrow['termid']);
		

	}
	
//	$incanview = "'0'";
//	foreach ($_SESSION['scen.acc.view'] as $scenid => $scenname) {
//		$incanview .= " , '".$scenid."'";
//	}
	$incanview = inscenery_sessionlst('scen.acc.view');
	list($qscentbl,$qscensql) = scenery_sql($incanview);


//	$incanedit = "'0'";
//	foreach ($_SESSION['scen.acc.edit'] as $scenid => $scenname) {
//		$incanedit .= " , '".$scenid."'";
//	}
	$incanedit = inscenery_sessionlst('scen.acc.edit');

////

function agregupdt($aclassid) {
	global $GBLmysqli;

	$q="SELECT `cd`.`course_id` FROM `coursedisciplines` AS `cd` WHERE `cd`.`discipline_id` = '".$_POST['discid']."' ";
	$aclasssql=$GBLmysqli->dbquery($q);
	while($aclassrow=$aclasssql->fetch_assoc()) {
		$q = "SELECT  SUM(`vac`.`givennum`) AS `givensum`, SUM(`vac`.`askednum`) AS `askedsum` FROM `vacancies` AS `vac`,`class` " .
		"WHERE `vac`.`class_id` = `class`.`id` AND `class`.`partof` = '".$aclassid."' AND `vac`.`course_id` = '".$aclassrow['course_id']."';";
		$sumsql=$GBLmysqli->dbquery($q);
		$sumrow=$sumsql->fetch_assoc();
		$q="UPDATE `vacancies`  SET `givennum` = '".$sumrow['givensum']."' , `askednum` = '".$sumrow['askedsum']."' WHERE `class_id` = '".$aclassid."' AND `course_id` = '".$aclassrow['course_id']."';";
		$GBLmysqli->dbquery($q);
	}
}


/////



	//vardebug($_POST);
	if (!$readonly) {
		$classlogaction = 'class edit ('.$_POST['act'].') ';
		$classlog = array('level'=>'INFO','action'=> $classlogaction,'str'=>displaysqlitem('','semester',$_POST['semid'],'name').displaysqlitem('','discipline',$_POST['discid'],'code'),'xtra'=>'editclass.php');
		switch ($_POST['act']){
			case 'Submit':
//				$GBLmysqli->eventlog(array('level'=>'INFO','action'=>'class edit','str'=>displaysqlitem('','semester',$_POST['semid'],'name').displaysqlitem('','discipline',$_POST['discid'],'code'),'xtra'=>'editclass.php'));
				//vardebug($_POST);
				foreach ($_SESSION['segments'] as $segid => $segkey) {
					if (($_POST[$segkey.'delete'])) {
						$q = "DELETE FROM `classsegment` WHERE `id` = '" . $segid . "';";
						$classlog['action'] = $classlogaction . 'segment DELETE';
							$classlog['dataorg'] = $_SESSION['weekday'][$_SESSION['org'][$segkey.'day']] . ' ' . 
													$_SESSION['org'][$segkey.'start'] . ':30 (' . $_SESSION['org'][$segkey.'length'] . ') sala: ' . 
													$_SESSION['rooms'][$_SESSION['org'][$segkey.'room']]['txt'] . ' - ' . 
													$_SESSION['deptprof'.$_POST['unitid']][$_SESSION['org'][$segkey.'prof']] . ' (' . 
													$_SESSION['status'][$_SESSION['org'][$segkey.'status']] .')';
							$classlog['datanew']='';
						$GBLmysqli->dbquery($q,$classlog);
					} else {
						if (fieldscompare($segkey,array('day','start','length','room','prof','status'))) {
							$q = "UPDATE `classsegment` SET `day` = '" . $_POST[$segkey.'day']  . "' , `start` = '" . $_POST[$segkey.'start']  . 
								"' , `length` = '" . $_POST[$segkey.'length']  . "' , `room_id` = '" . $_POST[$segkey.'room']  . 
								"' , `prof_id` = '" . $_POST[$segkey.'prof']  . "' , `status_id` = '" . $_POST[$segkey.'status'] . "' WHERE `id` = '" . $segid  . "';";
							$classlog['action'] = $classlogaction . 'segment UPDATE';
							$classlog['dataorg'] = $_SESSION['weekday'][$_SESSION['org'][$segkey.'day']] . ' ' . 
													$_SESSION['org'][$segkey.'start'] . ':30 (' . $_SESSION['org'][$segkey.'length'] . ') sala: ' . 
													$_SESSION['rooms'][$_SESSION['org'][$segkey.'room']]['txt'] . ' - ' . 
													$_SESSION['deptprof'.$_POST['unitid']][$_SESSION['org'][$segkey.'prof']] . ' (' . 
													$_SESSION['status'][$_SESSION['org'][$segkey.'status']] .')';
							$classlog['datanew'] = $_SESSION['weekday'][$_POST[$segkey.'day']] . ' ' . 
													$_POST[$segkey.'start'] . ':30 (' . 
													$_POST[$segkey.'length'] . ') sala: ' . 
													$_SESSION['rooms'][$_POST[$segkey.'room']]['txt'] . ' - ' . 
													$_SESSION['deptprof'.$_POST['unitid']][$_POST[$segkey.'prof']] . ' (' . 
													$_SESSION['status'][$_POST[$segkey.'status']] .')';
							$GBLmysqli->dbquery($q,$classlog);
						}
					}
				}
				$anyone=0;
				foreach ($_SESSION['vacancies'] as $vacid => $vackey) {
					if (fieldscompare($vackey,array('asked','askedstatusid','given','givenstatusid','comment'))) {
						$anyone=1;
						$q = "UPDATE `vacancies` SET `askednum` = '" . $_POST[$vackey.'asked']  . "' , `askedstatus_id`= '" . $_POST[$vackey.'askedstatusid'] . "' , `givennum`= '" . $_POST[$vackey.'given']  . "' , `givenstatus_id`= '" . $_POST[$vackey.'givenstatusid'] . "' , `comment`= '" . $_POST[$vackey.'comment'] . "' WHERE `id` = '" . $vacid  . "';";
						$classlog['action'] = $classlogaction . 'vac UPDATE';
							$classlog['dataorg'] = 'asked: ' . $_SESSION['org'][$vackey.'asked'] . ' (' . $_SESSION['status'][$_SESSION['org'][$vackey.'askedstatusid']] . ') ' .
													'given: ' . $_SESSION['org'][$vackey.'given'] . ' (' . $_SESSION['status'][$_SESSION['org'][$vackey.'givenstatusid']] . ') ' .
													$_SESSION['org'][$vackey.'comment'] ;
							$classlog['datanew'] = 'asked: ' . $_POST[$vackey.'asked'] . ' (' . $_SESSION['status'][$_POST[$vackey.'askedstatusid']] . ') ' .
													'given: ' . $_POST[$vackey.'given'] . ' (' . $_SESSION['status'][$_POST[$vackey.'givenstatusid']] . ') ' .
													$_POST[$vackey.'comment'] ;
						$GBLmysqli->dbquery($q,$classlog);
					}
				}
				$segadded = 0;
				//vardebug($_SESSION['classes']);
				//vardebug($_POST['class7387scenlst']);
				foreach ($_SESSION['classes'] as $classid => $classkey) {
					if (($_POST[$classkey.'delete'])) {
						$q = "DELETE FROM `class` WHERE `id` = '" . $classid . "';";
						$classlog['action'] = $classlogaction . 'class DELETE';
						  $classlog['dataorg'] = 'Turma : ' . $_POST['classname'] . ' deleted';
						  $classlog['datanew'] = '';
						$GBLmysqli->dbquery($q,$classlog);
					} else {
						if($_POST[$classkey.'agreg']) {$_POST[$classkey.'partof']=0;}
						if (fieldscompare($classkey,array('classname'))) {
							$q = "UPDATE `class` SET `name`= '" . $_POST[$classkey.'classname'] . "' WHERE `id`= '" . $classid . "';";
							$GBLmysqli->dbquery($q);
						}
						if (fieldscompare($classkey,array('agreg','status','comment','partof','scenerybool'))) {
							//echo 'tjam<br>';
							if ($_POST[$classkey.'partof']) {
								$part = "`partof`= '" . $_POST[$classkey.'partof'] . "' ";
							} else {
								$part = "`partof`= NULL ";
							}
							$q = "UPDATE `class` SET `agreg`= '" . $_POST[$classkey.'agreg'] . "' , `status_id`= '" . $_POST[$classkey.'status'] . "' , `comment`= '" . $_POST[$classkey.'comment'] . "' , `scenery`= '" . $_POST[$classkey.'scenerybool'] . "' , $part WHERE `id`= '" . $classid . "';";
							$classlog['action'] = $classlogaction . 'class agreg UPDATE';
							$classlog['dataorg'] = 'agreg: ' . $_SESSION['bool'][$_SESSION['org'][$classkey.'agreg']] . ' (' . $_SESSION['status'][$_SESSION['org'][$classkey.'statusid']] . ') ' .
													$_SESSION['org'][$classkey.'comment'] ;
							$classlog['datanew'] = 'agreg: ' . $_SESSION['bool'][$_POST[$classkey.'agreg']] . ' (' . $_SESSION['status'][$_POST[$classkey.'statusid']] . ') ' .
													$_POST[$classkey.'comment'] ;
		
							$GBLmysqli->dbquery($q,$classlog);
							if ($_SESSION['org'][$classkey.'agreg']  & !$_POST[$classkey.'agreg']) {
								$q = "UPDATE `class` SET `partof` = NULL WHERE `partof` = '" . $classid . "';";
								$classlog['action'] = $classlogaction . 'class partof UPDATE';
								$classlog['dataorg'] = '';
								$classlog['datanew'] = 'vac cleanup';								
								$GBLmysqli->dbquery($q,$classlog);
							}
							if (!$_SESSION['org'][$classkey.'agreg']  & $_POST[$classkey.'agreg']) {
								$q = "UPDATE `vacancies` SET `givennum` = '0' , `askednum` = '0' WHERE `class_id` = '" . $classid . "';";
								$classlog['action'] = $classlogaction . 'vac UPDATE';
								$classlog['dataorg'] = '';
								$classlog['datanew'] = 'vac cleanup';								
								$GBLmysqli->dbquery($q,$classlog);
							}
						}
						//echo 'and...'.$classkey.'<br>';
						//vardebug($_POST);
						//vardebug($_SESSION['org']);
						if ($_POST[$classkey.'scenerybool']) {
							//echo 'here<br>';
							foreach ($_SESSION['org']['sceneryusr'] as $session_sceneryid => $session_sceneryclassid) {
								
								$postscenery = $_POST[$classkey.'scenery'.$session_sceneryid];
								//echo 'id:' . $session_sceneryid . 'clsid:' . $session_sceneryclassid . 'post:' . $postscenery. '<br>';
								if ($session_sceneryclassid && !$postscenery) {
									//echo 'delete<br>';
									$q = "DELETE FROM `sceneryclass` WHERE `id` = '".$session_sceneryclassid."';";
									$GBLmysqli->dbquery($q,$classlog);
									//remove
								}
								if (!$session_sceneryclassid && $postscenery) {
									//echo 'insert<br>';
									$q = "INSERT INTO `sceneryclass` (`class_id`,`scenery_id`) VALUES ('" . $classid  . "' , '" . $postscenery . "');";
									$GBLmysqli->dbquery($q,$classlog);
									// insert 
								}
							}
						} else {
							//echo '...<br>';
							if ($_SESSION['org'][$classkey.'scenerybool']) {
								//echo $classid.'<br>';
								$q = "DELETE FROM `sceneryclass` WHERE class_id = '".$classid."';";
								$GBLmysqli->dbquery($q,$classlog);
							}
						}
						//vardebug($q);
						//vardebug($_POST);
						if (($_POST[$classkey.'addsegment'])) {
							reset($_SESSION['deptprof'.$_POST['unitid']]);
							$q = "INSERT INTO `classsegment` (`day`,`start`,`length`,`room_id`,`prof_id`,`class_id`) VALUES ('2','7','2','1','" . key($_SESSION['deptprof'.$_POST['unitid']]) . "','" . $classid . "');";
							$classlog['action'] = $classlogaction . 'segment ADD';
							$classlog['dataorg'] = '';
							$classlog['datanew'] = 'default segment added';								
							$GBLmysqli->dbquery($q,$classlog);
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
					if ($_POST['newclassname']) {
						$q = "INSERT INTO `class` (`name`,`sem_id`,`discipline_id`) VALUES ('".$_POST['newclassname']."','".$_POST['semid']."','".$_POST['discid']."');";
						$classlog['action'] = $classlogaction . 'class ADD';
						$classlog['datanew'] = 'Turma : ' . $_POST['newclassname']  . ' added';
						if($GBLmysqli->dbquery($q)) {
							$newclassid = $GBLmysqli->insert_id;
							$GBLmysqli->eventlog($classlog);
							$q = "SELECT * FROM coursedisciplines WHERE discipline_id = '" . $_POST['discid'] . "';";
							$result = $GBLmysqli->dbquery($q);
							while ($sqlrow = $result->fetch_assoc()) {
								$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) VALUES ('" . $newclassid . "','" . $sqlrow['course_id'] . "','0','0');";
								$classlog['action'] = $classlogaction . 'vac UPDATE/ADD';
								$GBLmysqli->dbquery($q,$classlog);
							}
						}
					}
				}
			break;
			case 'Replicate Class':
				if ($_POST['addclass']) {
					if ($_POST['newclassname']) {
						$q = "INSERT INTO `class` (`name`,`sem_id`,`discipline_id`) VALUES ('".$_POST['newclassname']."','".$_POST['semid']."','".$_POST['discid']."');";
						$classlog['action'] = $classlogaction . 'class ADD';
						$classlog['datanew'] = 'Turma : ' . $_POST['newclassname']  . ' added';
						if($GBLmysqli->dbquery($q)) {
							$newclassid = $GBLmysqli->insert_id;
							$GBLmysqli->eventlog($classlog);
							
							$q = "INSERT INTO `classsegment` (`class_id`,`day`,`start`,`length`,`room_id`,`prof_id`) " . 
								"SELECT '".$newclassid."' , `cs`.`day` , `cs`.`start` , `cs`.`length` , `cs`.`room_id` , `cs`.`prof_id` " . 
								"FROM  `classsegment` AS `cs` " . 
								"WHERE `cs`.`class_id` = '" . $_POST['classid'] . "' ;";
							$GBLmysqli->dbquery($q);
							
							$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) " . 
								"SELECT '".$newclassid."' , `vc`.`course_id` , `vc`.`askednum` , `vc`.`givennum`  " .
								"FROM  `vacancies` AS `vc` " . 
								"WHERE `vc`.`class_id` = '" . $_POST['classid'] . "';";
							$GBLmysqli->dbquery($q);
							
						}
					}
				}
			break;

			case 'Add Class in Scenery':
				if ($_POST['addclass']) {
					if ($_POST['newclassname']) {
						$q = "INSERT INTO `class` (`name`,`sem_id`,`discipline_id`,`scenery`) VALUES ('".$_POST['newclassname']."','".$_POST['semid']."','".$_POST['discid']."','1');";
						$classlog['action'] = $classlogaction . 'class ADD';
						$classlog['datanew'] = 'Turma : ' . $_POST['newclassname']  . ' added';
						if($GBLmysqli->dbquery($q)) {
							$newclassid = $GBLmysqli->insert_id;
							$GBLmysqli->eventlog($classlog);
							$q = "SELECT * FROM coursedisciplines WHERE discipline_id = '" . $_POST['discid'] . "';";
							$result = $GBLmysqli->dbquery($q);
							while ($sqlrow = $result->fetch_assoc()) {
								$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) VALUES ('" . $newclassid . "','" . $sqlrow['course_id'] . "','0','0');";
								$classlog['action'] = $classlogaction . 'vac UPDATE/ADD';
								$GBLmysqli->dbquery($q,$classlog);
							}
							$q = "INSERT INTO `sceneryclass` (`class_id`,`scenery_id`) VALUES ('".$newclassid."','".$_POST['addscenery']."');";
							$GBLmysqli->dbquery($q);
						}
					}
				}

			break;
			
			case 'Replicate Class in Scenery':
				if ($_POST['addclass']) {
					if ($_POST['newclassname']) {
						$q = "INSERT INTO `class` (`name`,`sem_id`,`discipline_id`,`scenery`) VALUES ('".$_POST['newclassname']."','".$_POST['semid']."','".$_POST['discid']."','1');";
						$classlog['action'] = $classlogaction . 'class ADD';
						$classlog['datanew'] = 'Turma : ' . $_POST['newclassname']  . ' added';
						if($GBLmysqli->dbquery($q)) {
							$newclassid = $GBLmysqli->insert_id;
							$GBLmysqli->eventlog($classlog);
							
							$q = "INSERT INTO `classsegment` (`class_id`,`day`,`start`,`length`,`room_id`,`prof_id`) " . 
								"SELECT '".$newclassid."' , `cs`.`day` , `cs`.`start` , `cs`.`length` , `cs`.`room_id` , `cs`.`prof_id` " . 
								"FROM  `classsegment` AS `cs` " . 
								"WHERE `cs`.`class_id` = '" . $_POST['classid'] . "' ;";
							$GBLmysqli->dbquery($q);
							
							$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) " . 
								"SELECT '".$newclassid."' , `vc`.`course_id` , `vc`.`askednum` , `vc`.`givennum`  " .
								"FROM  `vacancies` AS `vc` " . 
								"WHERE `vc`.`class_id` = '" . $_POST['classid'] . "';";
							$GBLmysqli->dbquery($q);
							
							$q = "INSERT INTO `sceneryclass` (`class_id`,`scenery_id`) VALUES ('".$newclassid."','".$_POST['addscenery']."');";
							$GBLmysqli->dbquery($q);
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

function sceneryclasshack($profnicks,$inselect=true) {
	if($inselect) {
		formselectscenery('scen.acc.view',formsubmit('act','Refresh'));
	} else {
		echo '<br>';
		displayscenerysession();
	}
	
	$inselected = inscenery_sessionlst('sceneryselected');
	list($qqscentbl,$qqscensql) = scenery_sql($inselected);

	if($profnicks) {
		$qnicks = " , `prof`.`nickname` AS `profnick`, `prof`.`id` AS `profid`, `prof`.`dept_id` AS `profdeptid`  ";
	} else {
		$qnicks='';
	}

//	$qqscentbl = ""; //nothing by now
	$qq = "SELECT DISTINCT `discipline`.`name` AS `discname` ,  `discipline`.`id` AS `discid` , `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `discdept`.`id` AS `discdeptid` " . $qnicks .
		 " FROM `classsegment` , `class`, `semester`,`unit`,`discipline`,`prof` , `unit` AS `discdept`  " . $qqscentbl . " WHERE " . 
		 "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " . 
		 "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`prof_id` = `prof`.`id` AND " .
		 "`discipline`.`dept_id` = `discdept`.`id` AND " .
		 "`unit`.`id` = '".$_POST['unitid']."' AND `semester`.`id` = '".$_POST['semid']."' AND " . 
		 "`discipline`.`id` = '".$_POST['discid'] . "' " . $qqscensql . " ORDER BY `discipline`.`name` , `class`.`name`";

	return [$inselected,$qq];
		//echo '<details>';
		//echo '<summary>&nbsp;&nbsp;&nbsp;<b>&rArr;</b> Matriz Horários</summary>';
		//dbweekmatrix($qq,$inselected,null,null,false,true);
		//echo '</details>';
}



	if ($postedit) {
		thisformpost();
		echo displaysqlitem('Semestre: ','semester',$_POST['semid'],'name');
		echo displaysqlitem('&nbsp;&nbsp;&nbsp;&nbsp;Dept.: ','unit',$_POST['unitid'],'acronym');
		echo '<br><b>';
		echo displaysqlitem('&nbsp;&nbsp;','discipline',$_POST['discid'],'code','name');
		echo '</b>';
		echo formsubmit('act','Cancel');
		echo spanformat('smaller',$GBLcommentcolor, displaysqlitem('&nbsp;&nbsp;','discipline',$_POST['discid'],'comment')) ;
		//formselectscenery('scen.acc.view');
		echo formhiddenval('profnicks',$_POST['profnicks']);
		echo formhiddenval('courseHL',$_POST['courseHL']);
//		echo formhiddenval('orderby',$_POST['orderby']);

		[$SCinselected,$SCquery] = sceneryclasshack($_POST['profnicks'],false);
		if($_POST['courseHL']) {
			echo displaysqlitem('<br>Realçando: ','unit',$_POST['courseHL'],'name');
		}

		echo  '</form>';
	} else {
		echo formpost($thisform);
		formselectsql($anytmp,"SELECT * FROM semester ORDER BY name;",'semid',$_POST['semid'],'id','name');
		formselectsql($anytmp,"SELECT * FROM unit  ORDER BY unit.mark DESC , unit.iscourse ASC, unit.acronym ASC;",'unitid',$_POST['unitid'],'id','acronym');
		formselectsql($anytmp,"SELECT * FROM discipline WHERE discipline.dept_id = '".$_POST['unitid']."' ORDER BY name;",'discid',$_POST['discid'],'id','code','name');
		//formselectscenery('scen.acc.view');
		[$SCinselected,$SCquery] = sceneryclasshack($_POST['profnicks']);
		//echo '<br>';
//		echo "Ordenado por:  ";
//		formselectsession('orderby','orderby',$_POST['orderby']);
		
	echo "Nome Profs? ";
	formselectsession('profnicks','bool',$_POST['profnicks'],false,true);
	echo "Realçar curso: ";
	formselectsql($anytmp,"SELECT `course`.* FROM `unit` AS `course`, coursedisciplines AS `cdisc` WHERE `cdisc`.`discipline_id` = '".$_POST['discid']."' AND `cdisc`.`course_id` = `course`.`id` ORDER BY name;",'courseHL',$_POST['courseHL'],'id','name');
	
		if (!$readonly) {
			if ($_POST['semid'] && $_POST['unitid'] && $_POST['discid']) {
				echo  formsubmit('act','Edit') . '</form>';
			} else {
				echo  '</form>';
			}
		} else {
			echo  '</form>';

		}

	}
	dbweekmatrix($SCquery,$SCinselected,null,null,false,true,$_POST['courseHL']);
	echo '<hr>';
   	$inselected = inscenery_sessionlst('sceneryselected');
	list($qqscentbl,$qqscensql) = scenery_sql($inselected);




	$q = "SELECT class.* FROM class,discipline WHERE class.discipline_id = discipline.id AND class.discipline_id = '" . $_POST['discid'] . "' AND class.sem_id = '" . $_POST['semid'] . "' AND discipline.dept_id = '" . $_POST['unitid'] . "' AND class.agreg = '1' ORDER BY class.name;";
	$result = $GBLmysqli->dbquery($q);
	unset($_SESSION['agreg']);
	while ($sqlrow = $result->fetch_assoc()) {
		$_SESSION['agreg'][$sqlrow['id']] = $sqlrow['name'];
	}	
	if ($can_class) {
		$q = "SELECT DISTINCT class.* FROM `class` , `discipline` " . 
			"WHERE class.discipline_id = discipline.id AND class.discipline_id = '" . $_POST['discid'] . "' AND class.sem_id = '" . $_POST['semid'] . "' AND " . 
			"discipline.dept_id = '" . $_POST['unitid'] . "'" . 
			 " ORDER BY  `class`.`name`"
			 ;
	} else {
		$q = "SELECT DISTINCT class.* FROM `class` , `discipline` " . $qscentbl . 
			"WHERE class.discipline_id = discipline.id AND class.discipline_id = '" . $_POST['discid'] . "' AND class.sem_id = '" . $_POST['semid'] . "' AND " . 
			"discipline.dept_id = '" . $_POST['unitid'] . "' " . $qscensql ;
	}
	$result = $GBLmysqli->dbquery($q);
	
	$anyone = 0;
				//vardebug($_SESSION['scenery']);
				//vardebug($_SESSION['org'][sceneryusr]);
				//vardebug($_SESSION['org'][sceneryclass]);
				//vardebug($_POST);
				//vardebug($_POST['class7387scenlst']);

	while ($classrow = $result->fetch_assoc()) {
		$anyone = 1;
		if ($postedit) {
			echo '<div id="class'.$classrow['id'].'div">&nbsp;</div>';
			echo '<br><br>';
			thisformpost('class'.$classrow['id'].'div');
			echo formhiddenval('classid',$classrow['id']);
			echo formhiddenval('classname',$classrow['name']);
			if ($classrow['id'] == $_POST['classid']) {
				if($can_class) {
					formclassedit($classrow,$incanedit);
				} else {
					// TODO: verify if 'can edit scenery
					$qtestsql = "SELECT * FROM `sceneryclass` WHERE `sceneryclass`.`class_id` = '".$classrow['id']."' AND `sceneryclass`.`scenery_id` IN (".$incanedit.");";
					$qtestresult = $GBLmysqli->dbquery($qtestsql);
					if ($qtestresult->num_rows) {
						formclassedit($classrow,$incanedit,true);
					} else {
						formclassdisplay($classrow,true);
					}
				}
				echo formsubmit('act','Submit');
				echo formsubmit('act','Cancel');
			} else {
				echo formsubmit('act','Cancel');
				echo formsubmit('act','Edit');
				formclassdisplay($classrow);
			}
			echo '</form>';
			
			if ($can_addclass) {
				thisformpost();
				echo 'Replicar Turma:' . formpatterninput(3,1,$GBLclasspattern,'Nova Turma','newclassname','!');
				echo formhiddenval('classid',$classrow['id']);
				formselectsession('addclass','bool',0);
				echo formsubmit('act','Replicate Class') . '</form>';
			} else {
				if ($incanedit) {
					thisformpost();
					echo 'Replicar Turma:' . formpatterninput(3,1,$GBLclasspattern,'Nova Turma','newclassname','!');
					echo formhiddenval('classid',$classrow['id']);
					formselectsession('addscenery','scen.acc.edit',0);
					formselectsession('addclass','bool',0);
					echo formsubmit('act','Replicate Class in Scenery') . '</form>';				
				}
			}
			
		} else {
			formclassdisplay($classrow);
		}
		echo '<hr>';
	}
	echo '</p>';
	if ($hiddenprofdeptid) {
		foreach ($hiddenprofdeptid as $profid => $hidprofdeptid ) { // '_blank'
			echo hiddenprofform($_POST['semid'],$hidprofdeptid,$profid);	
		}
	}
	if ($hiddenroombuildingid) {
		foreach ($hiddenroombuildingid as $roomid => $buildingid) {
			echo hiddenroomform($_POST['semid'],$buildingid,$roomid);
		}
	}
	if ($postedit) {
		if ($can_addclass) {
			thisformpost();
			echo 'Adicionar Turma:' . formpatterninput(3,1,$GBLclasspattern,'Nova Turma','newclassname','!');
			formselectsession('addclass','bool',0);
			echo formsubmit('act','Add Class') . '</form>';
		} else {
			if ($incanedit) {
				thisformpost();
				echo 'Adicionar Turma:' . formpatterninput(3,1,$GBLclasspattern,'Nova Turma','newclassname','!');
				formselectsession('addscenery','scen.acc.edit',0);
				formselectsession('addclass','bool',0);
				echo formsubmit('act','Add Class in Scenery') . '</form>';				
			}
		}
	}
?>




		
    
 
</div>