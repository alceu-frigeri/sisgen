

<?php $thisform=$basepage.'?q=edits&sq=classes'; 
?>

<div class="row">
        <h2>Turmas</h2>
        <hr>


<?php 
	$can_class=($_SESSION['role'][$_POST['unitid']]['can_class'] | $_SESSION['role']['isadmin']) & !$readonly;
	$can_addclass=($_SESSION['role'][$_POST['unitid']]['can_addclass'] | $_SESSION['role']['isadmin']) & !$readonly;
//	$postedit = (($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit')) & !$readonly;
	$postedit = (($_POST['act'] == 'Edit')) & !$readonly;
	
	echo formpost($thisform);

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


	if (($_POST['act'] == 'Cancel') || ($_POST['act'] == 'Refresh')) {
		unset($_SESSION['segments']);
		unset($_SESSION['vacancies']);
		unset($_SESSION['classes']);
		unset($_SESSION['agreg']);
	}


	$q = "SELECT readonly FROM semester WHERE id = '" . $_POST['semid'] . "';";
	$result = $mysqli->dbquery($q);
	$sqlrow = $result->fetch_assoc();
	$readonly = $sqlrow['readonly'];


	if (($_POST['act'] == 'Submit') & !$readonly) {
		foreach ($_SESSION['segments'] as $segid => $segkey) {
			if (($_POST[$segkey.'delete'])) {
				$q = "DELETE FROM `classsegment` WHERE `id` = '" . $segid . "';";
			} else {
				$q = "UPDATE `classsegment` SET `day` = '" . $_POST[$segkey.'day']  . "' , `start` = '" . $_POST[$segkey.'start']  . 
					"' , `length` = '" . $_POST[$segkey.'length']  . "' , `room_id` = '" . $_POST[$segkey.'room']  . 
					"' , `prof_id` = '" . $_POST[$segkey.'prof']  . "' , `status_id` = '" . $_POST[$segkey.'status'] . "' WHERE `id` = '" . $segid  . "';";
			}
			$mysqli->dbquery($q);
		}
		foreach ($_SESSION['vacancies'] as $vacid => $vackey) {
			$q = "UPDATE `vacancies` SET `askednum` = '" . $_POST[$vackey.'asked']  . "' , `askedstatus_id`= '" . $_POST[$vackey.'askedstatusid'] . "' , `givennum`= '" . $_POST[$vackey.'given']  . "' , `givenstatus_id`= '" . $_POST[$vackey.'givenstatusid'] . "' , `comment`= '" . $_POST[$vackey.'comment'] . "' WHERE `id` = '" . $vacid  . "';";
			$mysqli->dbquery($q);
		}
		foreach ($_SESSION['classes'] as $classid => $classkey) {
			if (($_POST[$classkey.'delete'])) {
				$q = "DELETE FROM `class` WHERE `id` = '" . $classid . "';";
				$mysqli->dbquery($q);
			} else {
				if ($_POST[$classkey.'partof']) {
					$part = "`partof`= '" . $_POST[$classkey.'partof'] . "' ";
				} else {
					$part = "`partof`= NULL ";
				}
				$q = "UPDATE `class` SET `agreg`= '" . $_POST[$classkey.'agreg'] . "' , `status_id`= '" . $_POST[$classkey.'status'] . "' , `comment`= '" . $_POST[$classkey.'comment'] . "' , $part WHERE `id`= '" . $classid . "';";
				$mysqli->dbquery($q);
				if (!($_POST[$classkey.'agreg'])) {
					$q = "UPDATE `class` SET `partof` = NULL WHERE `partof` = '" . $classid . "';";
					$mysqli->dbquery($q);
				}
				if (($_POST[$classkey.'addsegment'])) {
					reset($_SESSION['deptprof'.$_POST['unitid']]);
					$q = "INSERT INTO `classsegment` (`day`,`start`,`length`,`room_id`,`prof_id`,`class_id`) VALUES ('2','7','2','1','" . key($_SESSION['deptprof'.$_POST['unitid']]) . "','" . $classid . "');";
					$mysqli->dbquery($q);
				}
			}
		}
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
		
	}


	if ($postedit) {
		echo formhiddenval('semid',$_POST['semid']);
		echo displaysqlitem('Semestre: ','semester',$_POST['semid'],'name');
		
		echo formhiddenval('unitid',$_POST['unitid']);
		echo displaysqlitem('&nbsp;&nbsp;&nbsp;&nbsp;Dept.: ','unit',$_POST['unitid'],'acronym');

		echo formhiddenval('discid',$_POST['discid']);
		echo '<br><b>';
		echo displaysqlitem('&nbsp;&nbsp;','discipline',$_POST['discid'],'code','name');
		echo '</b><br><span style="font-size:smaller;color:'.$commentcolor.';">';
		echo displaysqlitem('&nbsp;&nbsp;','discipline',$_POST['discid'],'comment');
		echo '</span></p>';
	} else {
		formselectsqlX($anytmp,"SELECT * FROM semester ORDER BY name;",'semid',$_POST['semid'],'id','name');
		formselectsqlX($anytmp,"SELECT * FROM unit  ORDER BY unit.iscourse DESC, unit.acronym ASC;",'unitid',$_POST['unitid'],'id','acronym');
		formselectsqlX($anytmp,"SELECT * FROM discipline WHERE discipline.dept_id = '".$_POST['unitid']."' ORDER BY name;",'discid',$_POST['discid'],'id','name');
	}


?>


<?php 
	function formsegment($segrow) {
		global $mysqli;
		echo $_SESSION['weekday'][$segrow['day']] . " -- " . $segrow['start'] . ":30 -- " . $segrow['length'] . "H -- " . $_SESSION['rooms'][$segrow['room_id']] . " -- " . $_SESSION['deptprof'.$_POST['unitid']][$segrow['prof_id']] .
		 "&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:" . $_SESSION['statuscolor'][$segrow['status_id']] . "'>(" .  $_SESSION['status'][$segrow['status_id']] . ")</span>";
	}
    
	function formrangeselection($selectname,$initial,$final,$refval,$trail=null,$disparray=null) {
		echo "<select name='".$selectname."'>";
		for ($i=$initial;$i<$final;$i++) {
			if ($i == $refval) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			if($disparray) {$val = $disparray[$i];} else {$val = $i;}
			echo "<option value='$i'$selected>".$val.$trail."</option>";
		}	
		echo "</select>";
	}
	
	function formeditsegment($segrow) {
		global $mysqli;
		global $can_addclass;
		
		$segformid = 'seg'.$segrow['id'];
		$_SESSION['segments'][$segrow['id']] = $segformid;
//		formhiddenval('','')
		formrangeselection($segformid.'day',2,8,$segrow['day'],'',$_SESSION['weekday']);
		formrangeselection($segformid.'start',7,21,$segrow['start'],':30');
		formrangeselection($segformid.'length',1,6,$segrow['length']);
		formselectsessionX($segformid.'room','rooms',$segrow['room_id']);
		formselectsessionX($segformid.'prof','deptprof'.$_POST['unitid'],$segrow['prof_id']);
		formselectsessionX($segformid.'status','status',$segrow['status_id']);
		//formselectsessionX($segformid.'delete','bool',0);
		
		if ($can_addclass) {
			echo "&nbsp;&nbsp;&nbsp;remover:";
			formselectsessionX($segformid.'delete','bool',0);
		}
	}
?>


<?php 
	$q = "SELECT class.* FROM class,discipline WHERE class.discipline_id = discipline.id AND class.discipline_id = '" . $_POST['discid'] . "' AND class.sem_id = '" . $_POST['semid'] . "' AND discipline.dept_id = '" . $_POST['unitid'] . "' AND class.agreg = '1' ORDER BY class.name;";
	$result = $mysqli->dbquery($q);
	unset($_SESSION['agreg']);
	while ($sqlrow = $result->fetch_assoc()) {
		$_SESSION['agreg'][$sqlrow['id']] = $sqlrow['name'];
	}	
	$q = "SELECT class.* FROM class,discipline WHERE class.discipline_id = discipline.id AND class.discipline_id = '" . $_POST['discid'] . "' AND class.sem_id = '" . $_POST['semid'] . "' AND discipline.dept_id = '" . $_POST['unitid'] . "' ORDER BY class.name;";
	$result = $mysqli->dbquery($q);
	
	$anyclass = 0;
	while ($sqlrow = $result->fetch_assoc()) {
		$anyclass = 1;
		$classid = 'class'.$sqlrow['id'];
		$_SESSION['classes'][$sqlrow['id']] = $classid;

		echo "<br>Turma:<b>" . $sqlrow['name'] . "</b>&nbsp;&nbsp;&nbsp;&nbsp;agregadora:";
		if ($postedit & $can_addclass) {
			formselectsessionX($classid.'agreg','bool',$sqlrow['agreg']);
			if($_SESSION['agreg']) {
				formselectsessionX($classid.'partof','agreg',$sqlrow['partof'],true);
			}
			echo "&nbsp;&nbsp;status:";
			formselectsessionX($classid.'status','status',$sqlrow['status_id']);
			echo "&nbsp;&nbsp;&nbsp;remover:";
			formselectsessionX($classid.'delete','bool',0);
		} else {
			echo $_SESSION['bool'][$sqlrow['agreg']];
			if ($sqlrow['partof']) {
				echo "&nbsp;&nbsp;&nbsp;&nbsp;agregada à: ".$_SESSION['agreg'][$sqlrow['partof']];
			}
			echo "&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:" . $_SESSION['statuscolor'][$sqlrow['status_id']] . "'>(" .  $_SESSION['status'][$sqlrow['status_id']] . ")</span>";
		}
		echo "</br>";
		if ($postedit & $can_class) {
			echo 'Obs.: ' . formpatterninput(48,16,$commentpattern,'Obs.',$classid.'comment',$sqlrow['comment']).'<br>';
		} else {
			if ($sqlrow['comment']) {
				echo '&nbsp;&nbsp;&nbsp;' .spanformat('font-size:smaller;color:'.$commentcolor.';',$sqlrow['comment']) . '<br>';
			}
		} 
		
		$q = "SELECT * FROM classsegment WHERE classsegment.class_id = '" . $sqlrow['id'] . "' ORDER BY day,start;";
		$segresult = $mysqli->dbquery($q);
		while ($segrow = $segresult->fetch_assoc()) {
			if ($postedit & $can_class) {
				formeditsegment($segrow);
			} else {
				formsegment($segrow);
			}
			echo "<br>";
		}
		if ($postedit & $can_addclass) {
			echo "Adicionar segmento:";
			formselectsessionX($classid.'addsegment','bool',0);
		}
		$q = "SELECT vacancies.* , unit.acronym FROM vacancies,unit WHERE vacancies.course_id = unit.id AND vacancies.class_id = '". $sqlrow['id'] . "';";
		$vacresult = $mysqli->dbquery($q);
		echo "<table>";
		if ($postedit) {
			while ($vacrow = $vacresult->fetch_assoc()) {
				echo "<tr>";
				$vacid = 'vac'.$vacrow['id'];
				$_SESSION['vacancies'][$vacrow['id']] = $vacid;
				if (($_SESSION['role'][$vacrow['course_id']]['can_vacancies'] | $_SESSION['role']['isadmin'])  & !$readonly) {
					echo "<td>Vagas solicitadas " . $vacrow['acronym'] . ": " . formpatterninput(3,1,'[0-9]+','Núm.',$vacid.'asked',$vacrow['askednum']);
					formselectsessionX($vacid.'askedstatusid','status',$vacrow['askedstatus_id']);
					echo "</td>";
				} else {
					echo formhiddenval($vacid.'asked',$vacrow['askednum']);
					echo "<td>Vagas solicitadas " . $vacrow['acronym'] . ": " .  $vacrow['askednum'] . "&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:" . $_SESSION['statuscolor'][$vacrow['askedstatus_id']] . "'>(" .  $_SESSION['status'][$vacrow['askedstatus_id']] . ")</span></td>";
				}
				if (($_SESSION['role'][$_POST['unitid']]['can_vacancies'] | $_SESSION['role']['isadmin']) & !$readonly) {
					echo "<td>&nbsp;&nbsp;Vagas concedidas: " .	formpatterninput(3,1,'[0-9]+','Núm.',$vacid.'given',$vacrow['givennum']);
					formselectsessionX($vacid.'givenstatusid','status',$vacrow['givenstatus_id']);
					echo "</td>";
				} else {
					echo formhiddenval($vacid.'given',$vacrow['givennum']);
					echo "<td>&nbsp;&nbsp;Vagas concedidas: " . $vacrow['givennum'] . "&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:" . $_SESSION['statuscolor'][$vacrow['givenstatus_id']] . "'>(" .  $_SESSION['status'][$vacrow['givenstatus_id']] . ")</span>" . "</td>";
				}
				echo "</tr>";
				if (($_SESSION['role'][$vacrow['course_id']]['can_vacancies'] | $_SESSION['role']['isadmin'])  & !$readonly) {
					echo '<tr>';
					echo "<td>Obs.: " . formpatterninput(48,16,$commentpattern,'Obs.',$vacid.'comment',$vacrow['comment']).'</td>';
					echo '<td></td></tr>';
				} else {
					echo formhiddenval($vacid.'comment',$vacrow['comment']);
					echo '<tr><td>'.'&nbsp;&nbsp;&nbsp;'.spanformat('font-size:smaller;color:',$commentcolor.';',$vacrow['comment']) . '</td><td></td></tr>';
				}
				
			}
		} else {
			$totalasked = 0;
			$totalgiven = 0;
			while ($vacrow = $vacresult->fetch_assoc()) {
				echo "<tr>";
				echo "<td>Vagas solicitadas " . $vacrow['acronym'] . ": " .  $vacrow['askednum'] . "&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:" . $_SESSION['statuscolor'][$vacrow['askedstatus_id']] . "'>(" .  $_SESSION['status'][$vacrow['askedstatus_id']] . ")</span></td>";
				echo "<td>&nbsp;&nbsp;Vagas concedidas: " . $vacrow['givennum'] . "&nbsp;&nbsp;&nbsp;&nbsp;<span style='color:" . $_SESSION['statuscolor'][$vacrow['givenstatus_id']] . "'>(" .  $_SESSION['status'][$vacrow['givenstatus_id']] . ")</span>" . "</td>";
				echo "</tr>";
				$totalasked += $vacrow['askednum'];
				$totalgiven += $vacrow['givennum'];			
				if ($vacrow['comment']) {
					echo '<tr><td>'.'&nbsp;&nbsp;&nbsp;'.spanformat('font-size:smaller;color:teal;',$vacrow['comment']) . '</td><td></td></tr>';
				}
			}
			echo '<tr><td>' . spanformat ('color:darkblue;','Total: ' . $totalasked) . '</td><td>&nbsp;&nbsp;' . spanformat('color:darkblue;','Total: ' . $totalgiven) . '</td></tr>';
		}
		echo "</table>";

	
	}
	echo "</p>";
	if ($postedit & $can_addclass) {
		echo "Adicionar Turma:<input type='text'  maxlength='3' size='1' pattern='[A-Z][A-Z0-9]*' name='newclassname'\>";
		formselectsessionX('addclass','bool',0);
	}

	if ($anyclass & !$readonly) {
		if ($postedit) {
			echo formsubmit('act','Cancel') . formsubmit('act','Submit');
		} else {
			echo formsubmit('act','Refresh') . formsubmit('act','Edit');
		}
	} else {
		echo formsubmit('act','Refresh');
	}
	




?>



</form>



		
    
 
</div>