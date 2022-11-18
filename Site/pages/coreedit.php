
<?php 


	if(!($_SESSION['rooms'])) {
		$q = "SELECT room.id , room.acronym AS room , room.capacity AS capacity , building.acronym AS building FROM room,building WHERE room.building_id = building.id ORDER BY building.acronym,room.acronym;";
		$sqlroom = $mysqli->dbquery($q);
		while ($roomrow = $sqlroom->fetch_assoc()) {
			if ($roomrow['capacity']){$cap = ' (cap.: '.$roomrow['capacity'] . ')';} else {$cap='';}
			$_SESSION['rooms'][$roomrow['id']] = $roomrow['building'].' - '.$roomrow['room'] . $cap;
		}
		
	}
	if(!($_SESSION['deptprof'.$_POST['unitid']])) {
		$q = "SELECT prof.id , prof.name FROM prof WHERE prof.dept_id = '".$_POST['unitid']."' ORDER BY name;";
		$sqlprof = $mysqli->dbquery($q);
		while ($profrow = $sqlprof->fetch_assoc()) {
			$_SESSION['deptprof'.$_POST['unitid']][$profrow['id']] = $profrow['name'];
		}
		$q = "SELECT prof.id , prof.name FROM prof,coursedept WHERE prof.dept_id = coursedept.dept_id AND coursedept.course_id ='".$_POST['unitid']."' ORDER BY name;";
		$sqlprof = $mysqli->dbquery($q);
		while ($profrow = $sqlprof->fetch_assoc()) {
			$_SESSION['deptprof'.$_POST['unitid']][$profrow['id']] = $profrow['name'];
		}
	}

	if(!($_SESSION['status'])) {
		$q = "SELECT * FROM status;";
		$sqlstatus = $mysqli->dbquery($q);
		while ($statusrow = $sqlstatus->fetch_assoc()) {
			$_SESSION['status'][$statusrow['id']] = $statusrow['status'];
			$_SESSION['statuscolor'][$statusrow['id']] = $statusrow['color'];
		}
		
	}

	
//// auxiliary functions....
	function thisformpost() {
		global $thisform;
		
		echo formpost($thisform);
		echo formhiddenval('semid',$_POST['semid']);
		echo formhiddenval('unitid',$_POST['unitid']);
		echo formhiddenval('discid',$_POST['discid']);
	}

	function formsegmentdisplay($segrow) {
		global $mysqli;
		echo $_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 -- ' . $segrow['length'] . 'H -- ' . $_SESSION['rooms'][$segrow['room_id']] . ' -- ' . $_SESSION['deptprof'.$_POST['unitid']][$segrow['prof_id']] .
		 '&nbsp;&nbsp;&nbsp;&nbsp;'. spanformat('', $_SESSION['statuscolor'][$segrow['status_id']]  ,  '(' .  $_SESSION['status'][$segrow['status_id']] . ')');
	}
    
	
	function formsegmentedit($segrow) {
		global $mysqli;
		global $can_addclass;
		
		$segformid = 'seg'.$segrow['id'];
		$_SESSION['segments'][$segrow['id']] = $segformid;
//		formhiddenval('','')
		formselectrange($segformid.'day',2,8,$segrow['day'],'',$_SESSION['weekday']);
		formselectrange($segformid.'start',7,21,$segrow['start'],':30');
		formselectrange($segformid.'length',1,6,$segrow['length']);
		formselectsession($segformid.'room','rooms',$segrow['room_id']);
		formselectsession($segformid.'prof','deptprof'.$_POST['unitid'],$segrow['prof_id']);
		formselectsession($segformid.'status','status',$segrow['status_id']);
		
		if ($can_addclass) {
			echo '&nbsp;&nbsp;&nbsp;remover:';
			formselectsession($segformid.'delete','bool',0);
		}
	}
	
	function formclassedit ($classrow) {
		global $mysqli;
		global $can_class;
		global $can_addclass;
		global $postedit;
		global $commentpattern;

		//thisformpost();
		
		echo 'Turma:<b>' . $classrow['name'] . '</b>&nbsp;&nbsp;&nbsp;&nbsp;agregadora:';

		$classid = 'class'.$classrow['id'];
		$_SESSION['classes'][$classrow['id']] = $classid;

		formselectsession($classid.'agreg','bool',$classrow['agreg']);
		if($_SESSION['agreg']) {
			echo '  agregada à:';
			formselectsession($classid.'partof','agreg',$classrow['partof'],true);
		}
		echo '&nbsp;&nbsp;status:';
		formselectsession($classid.'status','status',$classrow['status_id']);
		echo '&nbsp;&nbsp;&nbsp;remover:';
		formselectsession($classid.'delete','bool',0);
		echo '</br>';
		echo 'Obs.: ' . formpatterninput(48,16,$commentpattern,'Obs.',$classid.'comment',$classrow['comment']).'<br>';
		
				
		$q = "SELECT * FROM classsegment WHERE classsegment.class_id = '" . $classrow['id'] . "' ORDER BY day,start;";
		$segresult = $mysqli->dbquery($q);
		while ($segrow = $segresult->fetch_assoc()) {
			formsegmentedit($segrow);
			echo '<br>';
		}
		if ($postedit & $can_addclass) {
			echo 'Adicionar segmento:';
			formselectsession($classid.'addsegment','bool',0);
		}
		
		$q = "SELECT vacancies.* , unit.acronym , unit.id AS courseid FROM vacancies,unit WHERE vacancies.course_id = unit.id AND vacancies.class_id = '". $classrow['id'] . "';";
		$vacsql = $mysqli->dbquery($q);
		if($classrow['agreg']) {
			formvacdisplay($vacsql);
		} else {
			formvacedit($vacsql);
		}
	}
	
	function formclassdisplay ($classrow,$vacedit=false) {
		global $mysqli;
		global $can_class;
		global $can_addclass;
		global $postedit;
		global $commentcolor;

		echo 'Turma:<b>' . $classrow['name'] . '</b>&nbsp;&nbsp;&nbsp;&nbsp;agregadora:';
		echo $_SESSION['bool'][$classrow['agreg']];
		if ($classrow['partof']) {
			echo '&nbsp;&nbsp;&nbsp;&nbsp;agregada à: Turma <b>'.$_SESSION['agreg'][$classrow['partof']].'</b>';
		}
		echo '&nbsp;&nbsp;&nbsp;&nbsp;' . spanformat('', $_SESSION['statuscolor'][$classrow['status_id']] , '(' .  $_SESSION['status'][$classrow['status_id']] . ')');
		echo '</br>';

		if ($classrow['comment']) {
			echo '&nbsp;&nbsp;&nbsp;' .spanformat('smaller',$commentcolor ,$classrow['comment']) . '<br>';
		}
		
		$q = "SELECT * FROM classsegment WHERE classsegment.class_id = '" . $classrow['id'] . "' ORDER BY day,start;";
		$segresult = $mysqli->dbquery($q);
		while ($segrow = $segresult->fetch_assoc()) {
			formsegmentdisplay($segrow);
			echo '<br>';
		}
		
		$q = "SELECT vacancies.* , unit.acronym , unit.id AS courseid FROM vacancies,unit WHERE vacancies.course_id = unit.id AND vacancies.class_id = '". $classrow['id'] . "';";
		$vacsql = $mysqli->dbquery($q);
		if ($vacedit && !$classrow['agreg']) {
			formvacedit($vacsql);
		} else {
			formvacdisplay($vacsql);
		}
	}
	
	function formvacedit ($vacsql) {
		global $mysqli;
		global $can_class;
		global $can_addclass;
		global $postedit;
		global $readonly;
		global $commentpattern;
		global $commentcolor;
		global $Gblvackind;
		
		
		echo '<table>';
		while ($vacrow = $vacsql->fetch_assoc()) {
			echo '<tr>';
			$vacid = 'vac'.$vacrow['id'];
			if (($_SESSION['role'][$vacrow['course_id']]['can_vacancies'] | $_SESSION['role']['isadmin'])  & !$readonly) {
				$_SESSION['vacancies'][$vacrow['id']] = $vacid;
				echo '<td>Vagas solicitadas ' . $vacrow['acronym'] . ': ' . formpatterninput(3,1,'[0-9]+','Núm.',$vacid.'asked',$vacrow['askednum']) . '&nbsp;&nbsp;' .spanformat('','',$Gblvackind[$vacrow['courseid']]) . '&nbsp;&nbsp;';
				formselectsession($vacid.'askedstatusid','status',$vacrow['askedstatus_id']);
				echo '</td>';
			} else {
				echo formhiddenval($vacid.'asked',$vacrow['askednum']);
				echo formhiddenval($vacid.'askedstatusid',$vacrow['askedstatus_id']);
				echo '<td>Vagas solicitadas ' . $vacrow['acronym'] . ': ' .  $vacrow['askednum'] . '<sub>'.spanformat('smaller','',$Gblvackind[$vacrow['courseid']]). '</sub>' . '&nbsp;&nbsp;&nbsp;&nbsp;' . spanformat('', $_SESSION['statuscolor'][$vacrow['askedstatus_id']] , '(' .  $_SESSION['status'][$vacrow['askedstatus_id']] . ')') . '</td>';
			}
			if (($_SESSION['role'][$_POST['unitid']]['can_vacancies'] | $_SESSION['role']['isadmin']) & !$readonly) {
				$_SESSION['vacancies'][$vacrow['id']] = $vacid;
				echo '<td>&nbsp;&nbsp;Vagas concedidas: ' .	formpatterninput(3,1,'[0-9]+','Núm.',$vacid.'given',$vacrow['givennum']);
				formselectsession($vacid.'givenstatusid','status',$vacrow['givenstatus_id']);
				echo '</td>';
			} else {
				echo formhiddenval($vacid.'given',$vacrow['givennum']);
				echo formhiddenval($vacid.'givenstatusid',$vacrow['givenstatus_id']);
				echo '<td>&nbsp;&nbsp;Vagas concedidas: ' . $vacrow['givennum'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . spanformat('', $_SESSION['statuscolor'][$vacrow['givenstatus_id']]  , '(' .  $_SESSION['status'][$vacrow['givenstatus_id']] . ')') . '</td>';
			}
			echo '</tr>';
			if (($_SESSION['role'][$vacrow['course_id']]['can_vacancies'] | $_SESSION['role']['isadmin'])  & !$readonly) {
				$_SESSION['vacancies'][$vacrow['id']] = $vacid;
				echo '<tr>';
				echo '<td>Obs.: ' . formpatterninput(48,16,$commentpattern,'Obs.',$vacid.'comment',$vacrow['comment']).'</td>';
				echo '<td></td></tr>';
			} else {
				echo formhiddenval($vacid.'comment',$vacrow['comment']);
				echo '<tr><td>'.'&nbsp;&nbsp;&nbsp;'.spanformat('smaller',$commentcolor,$vacrow['comment']) . '</td><td></td></tr>';
			}
			
		}
		echo '</table>';
	}
	
	function formvacdisplay ($vacsql) {
		global $mysqli;
		global $can_class;
		global $can_addclass;
		global $postedit;
		global $commentcolor;
		global $Gblvackind;
		
		echo '<table>';
		$totalasked = 0;
		$totalgiven = 0;
		while ($vacrow = $vacsql->fetch_assoc()) {
			echo '<tr>';
			echo '<td>Vagas solicitadas ' . $vacrow['acronym'] . ': ' .  $vacrow['askednum'] . '<sub>'.spanformat('smaller','',$Gblvackind[$vacrow['courseid']]). '</sub>'. '&nbsp;&nbsp;&nbsp;&nbsp;' . spanformat('',$_SESSION['statuscolor'][$vacrow['askedstatus_id']], '(' .  $_SESSION['status'][$vacrow['askedstatus_id']] . ')') . '</td>';
			echo '<td>&nbsp;&nbsp;Vagas concedidas: ' . $vacrow['givennum'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . spanformat('', $_SESSION['statuscolor'][$vacrow['givenstatus_id']] , '(' .  $_SESSION['status'][$vacrow['givenstatus_id']] . ')') .  '</td>';
			echo '</tr>';
			if(!(($_SESSION['status'][$vacrow['askedstatus_id']] == 'dup') | ($_SESSION['status'][$vacrow['givenstatus_id']] == 'dup'))) {
				$totalasked += $vacrow['askednum'];
				$totalgiven += $vacrow['givennum'];			
			}
			if ($vacrow['comment']) {
				echo '<tr><td>'.'&nbsp;&nbsp;&nbsp;'.spanformat('smaller',$commentcolor,$vacrow['comment']) . '</td><td></td></tr>';
			}
		}
		echo '<tr><td>' . spanformat('','darkblue','Total: ' . $totalasked) . '</td><td>&nbsp;&nbsp;' . spanformat('','darkblue','Total: ' . $totalgiven) . '</td></tr>';
		echo '</table>';
		
	}
	


?>
