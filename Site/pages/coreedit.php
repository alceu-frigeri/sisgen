
<?php 


	if(!($_SESSION['rooms'])) {
		$q = "SELECT room.id , room.acronym AS room , room.capacity AS capacity , building.acronym AS building , building.mark AS mark , building.id AS buildingid FROM room,building WHERE room.building_id = building.id ORDER BY building.acronym,room.acronym;";
		$sqlroom = $GBLmysqli->dbquery($q);
		while ($roomrow = $sqlroom->fetch_assoc()) {
			if ($roomrow['capacity']){$cap = ' (cap.: '.$roomrow['capacity'] . ')';} else {$cap='';}
			$_SESSION['rooms'][$roomrow['id']]['txt'] = $roomrow['building'].' - '.$roomrow['room'] . $cap;
			$_SESSION['rooms'][$roomrow['id']]['buildmark'] = $roomrow['mark'];
			$_SESSION['rooms'][$roomrow['id']]['buildingid'] = $roomrow['buildingid'];
			$_SESSION['roomsID'][$roomrow['id']] = $roomrow['building'] . ' - ' . $roomrow['room'];
			if ($roomrow['mark']) {
				$_SESSION['rooms'][$roomrow['id']]['txtlnk'] = hiddenformlnk(hiddenroomkey($_POST['semid'],$roomrow['buildingid'],$roomrow['id']) , $roomrow['building'].' - '.$roomrow['room']) . $cap;
			} else {
				$_SESSION['rooms'][$roomrow['id']]['txtlnk'] = $roomrow['building'].' - '.$roomrow['room'] . $cap;
			}
		}
		
	}
	if(!($_SESSION['deptprof'.$_POST['unitid']])) {
		$q = "SELECT prof.id , prof.name FROM prof WHERE prof.dept_id = '".$_POST['unitid']."' ORDER BY name;";
		$sqlprof = $GBLmysqli->dbquery($q);
		while ($profrow = $sqlprof->fetch_assoc()) {
			$_SESSION['deptprof'.$_POST['unitid']][$profrow['id']] = $profrow['name'];
			$_SESSION['deptIDprof'.$_POST['unitid']][$profrow['id']] = $_POST['unitid'];
		}
		$q = "SELECT prof.id , prof.name , prof.dept_id FROM prof,coursedept WHERE prof.dept_id = coursedept.dept_id AND coursedept.course_id ='".$_POST['unitid']."' ORDER BY name;";
		$sqlprof = $GBLmysqli->dbquery($q);
		while ($profrow = $sqlprof->fetch_assoc()) {
			$_SESSION['deptprof'.$_POST['unitid']][$profrow['id']] = $profrow['name'];
			$_SESSION['deptIDprof'.$_POST['unitid']][$profrow['id']] = $profrow['dept_id'];
		}
	}

	if(!($_SESSION['status'])) {
		$q = "SELECT * FROM status;";
		$sqlstatus = $GBLmysqli->dbquery($q);
		while ($statusrow = $sqlstatus->fetch_assoc()) {
			$_SESSION['status'][$statusrow['id']] = $statusrow['status'];
			$_SESSION['statuscolor'][$statusrow['id']] = $statusrow['color'];
		}
		
	}

	
//// auxiliary functions....
	function thisformpost($hash=null) {
		global $thisform;
		
		if ($hash) {
			echo formpost($thisform.'#'.$hash);
		} else {
			echo formpost($thisform);
		}
		echo formhiddenval('semid',$_POST['semid']);
		echo formhiddenval('unitid',$_POST['unitid']);
		echo formhiddenval('discid',$_POST['discid']);
		echo formhiddenval('profnicks',$_POST['profnicks']);
		echo formhiddenval('courseHL',$_POST['courseHL']);
	}

	function formsegmentdisplay($segrow) {
		global $GBLmysqli;
		global $hiddenprofdeptid;
		global $hiddenroombuildingid;
		
		$hiddenprofdeptid[$segrow['prof_id']] = $_SESSION['deptIDprof'.$_POST['unitid']][$segrow['prof_id']];
		$hiddenroombuildingid[$segrow['room_id']] = $_SESSION['rooms'][$segrow['room_id']]['buildingid'];
		echo $_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 -- ' . $segrow['length'] . 'H -- ' . $_SESSION['rooms'][$segrow['room_id']]['txtlnk'] . ' -- ' . 
			hiddenformlnk(hiddenprofkey($_POST['semid'],$_SESSION['deptIDprof'.$_POST['unitid']][$segrow['prof_id']],$segrow['prof_id']) , $_SESSION['deptprof'.$_POST['unitid']][$segrow['prof_id']]) .
			'&nbsp;&nbsp;&nbsp;&nbsp;'. spanformat('', $_SESSION['statuscolor'][$segrow['status_id']]  ,  '(' .  $_SESSION['status'][$segrow['status_id']] . ')');
	}
    
	
	function formsegmentedit($segrow) {
		global $GBLmysqli;
		global $can_addclass;
		
		$segformid = 'seg'.$segrow['id'];
		$_SESSION['segments'][$segrow['id']] = $segformid;
//		formhiddenval('','')
		formselectrange($segformid.'day',2,8,$segrow['day'],'',$_SESSION['weekday']);
		formselectrange($segformid.'start',7,21,$segrow['start'],':30');
		formselectrange($segformid.'length',1,6,$segrow['length']);
		formselectsession($segformid.'room','roomsID',$segrow['room_id']);
		formselectsession($segformid.'prof','deptprof'.$_POST['unitid'],$segrow['prof_id']);
		formselectsession($segformid.'status','status',$segrow['status_id']);
		
		if ($can_addclass) {
			echo '&nbsp;&nbsp;&nbsp;remover:';
			formselectsession($segformid.'delete','bool',0);
		}
	}
	
	function formclassedit ($classrow,$incanedit,$canbyscenery=false) {
		global $GBLmysqli;
		global $can_class;
		global $can_addclass;
		global $postedit;
		global $GBLcommentpattern;
		global $GBLclasspattern;

		//thisformpost();
		$classkey = 'class'.$classrow['id'];
		$_SESSION['classes'][$classrow['id']] = $classkey;
		
		//echo 'Turma:<b>' . $classrow['name'] . '</b>&nbsp;&nbsp;&nbsp;&nbsp;agregadora:';
		//echo '<span style="color:#8000B0;">';
		echo '<table style="background-color:#E0FFE0;color:#8000B0;"><tr><td>';
		echo 'Turma:<b>' . formpatterninput(3,1,$GBLclasspattern,' Turma',$classkey.'classname',$classrow['name']) . '</b>&nbsp;&nbsp;&nbsp;&nbsp;agregadora:';


		formselectsession($classkey.'agreg','bool',$classrow['agreg']);
		if($_SESSION['agreg']) {
			echo '  agregada à:';
			formselectsession($classkey.'partof','agreg',$classrow['partof'],true);
		}
		echo '&nbsp;&nbsp;status:';
		formselectsession($classkey.'status','status',$classrow['status_id']);
		echo '&nbsp;&nbsp;&nbsp;remover:';
		formselectsession($classkey.'delete','bool',0);
		echo '</br>';
		echo 'Obs.: ' . formpatterninput(48,16,$GBLcommentpattern,'Obs.',$classkey.'comment',$classrow['comment']).'<br>';
		
				
		$q = "SELECT * FROM classsegment WHERE classsegment.class_id = '" . $classrow['id'] . "' ORDER BY day,start;";
		$segresult = $GBLmysqli->dbquery($q);
		while ($segrow = $segresult->fetch_assoc()) {
			formsegmentedit($segrow);
			echo '<br>';
		}
		if ($postedit && ($can_addclass || $canbyscenery)) {
			echo 'Adicionar segmento:';
			formselectsession($classkey.'addsegment','bool',0);
		}
		echo '<br>';
		
		if ($postedit && ($can_addclass || $canbyscenery)) {
 			if($can_addclass) {
				echo 'Cenários?:';
				formselectsession($classkey.'scenerybool','bool',$classrow['scenery']);
			} else {
				echo 'Cenários:';
				echo formhiddenval($classkey.'scenerybool',$classrow['scenery']);
				$_SESSION['org'][$classkey.'scenerybool'] = $classrow['scenery'];				
			}
			
//			if ($classrow['scenery']) {
				unset ($_SESSION['org']['sceneryclass']);
				unset ($_SESSION['org']['sceneryusr']);
				echo '&nbsp;&nbsp;&nbsp;&nbsp;';
				$q = "SELECT * FROM sceneryclass WHERE class_id = '" . $classrow['id'] .  "';"; // classkey X classid !!!!
				$scentmpsql = $GBLmysqli->dbquery($q);
				while ($scentmprow = $scentmpsql->fetch_assoc()) {
					$_SESSION['org']['sceneryclass'][$scentmprow['scenery_id']] = $scentmprow['id'];
				}
				echo '<table><tr>';
				$cnt=0;
				foreach ($_SESSION['scen.acc.edit'] as $id => $name) {
					$_SESSION['org']['sceneryusr'][$id] = $_SESSION['org']['sceneryclass'][$id];
					$checked='';
					if ($_SESSION['org']['sceneryclass'][$id]) {
						$checked=' checked';
					};
					$cnt++;
					if ($cnt == 9) {
						$cnt = 1;
						echo '</tr><tr>';
					}
					echo '<th style="width:110px">' . '<input type="checkbox" name="' . $classkey. 'scenery' . $id . '" id="scenery' . $id .   '" value="'. $id .'"' .$checked. '> <label for="scenery'. $id .'">'. $name .'</label></th>';
				}
				echo '</tr></table>';

				//vardebug($_SESSION['scen.acc.edit']);
				//vardebug($_SESSION['org']['sceneryusr']);
				//vardebug($_SESSION['org']['sceneryclass']);
	//		}
		}

		$q = "SELECT vacancies.* , unit.acronym , unit.id AS courseid FROM vacancies,unit WHERE vacancies.course_id = unit.id AND vacancies.class_id = '". $classrow['id'] . "' ORDER BY unit.acronym;";
		$vacsql = $GBLmysqli->dbquery($q);
		if($classrow['agreg']) {
			formvacdisplay($vacsql);
		} else {
			formvacedit($vacsql);
		}
		
		//echo '</span>';
		echo '</td></tr></table>';
	}
	
	function formclassdisplay ($classrow,$vacedit=false) {
		global $GBLmysqli;
		global $can_class;
		global $can_addclass;
		global $postedit;
		global $GBLcommentcolor;
		
		$courseHL=false;
		if ($_POST['courseHL']) {
			$q = "SELECT (`askednum` + `askedreservnum`+ `givennum` + `givenreservnum`) AS `total` FROM `vacancies` WHERE `class_id` = '".$classrow['id']."' AND `course_id` = '".$_POST['courseHL']."';";
			$xresult = $GBLmysqli->dbquery($q);
			$xrow = $xresult->fetch_assoc();
			if($xrow['total']) {
				$courseHL=true;
			}
		}
		

		echo 'Turma:<b>' . $classrow['name'] . '</b>&nbsp;&nbsp;&nbsp;&nbsp;agregadora:';
		echo $_SESSION['bool'][$classrow['agreg']];
		if ($classrow['partof']) {
			echo '&nbsp;&nbsp;&nbsp;&nbsp;agregada à: Turma <b>'.$_SESSION['agreg'][$classrow['partof']].'</b>';
		}
		echo '&nbsp;&nbsp;&nbsp;&nbsp;' . spanformat('', $_SESSION['statuscolor'][$classrow['status_id']] , '(' .  $_SESSION['status'][$classrow['status_id']] . ')');
		echo '</br>';

		if ($classrow['comment']) {
			echo '&nbsp;&nbsp;&nbsp;' .spanformat('smaller',$GBLcommentcolor ,$classrow['comment']) . '<br>';
		}
		
		$q = "SELECT * FROM classsegment WHERE classsegment.class_id = '" . $classrow['id'] . "' ORDER BY day,start;";
		$segresult = $GBLmysqli->dbquery($q);
		while ($segrow = $segresult->fetch_assoc()) {
			formsegmentdisplay($segrow);
			echo '<br>';
		}
		
		echo '<table>';
		echo '<tr style="visibility:collapse"><td>----</td><td>---</td><td>---</td><td>---</td></tr>';
		if ($classrow['scenery']) {
			echo '<tr><td></td><td><b style="color:MidnightBlue;">Cenário(s):</b></td><td></td><td></td></tr>';
			$q = "SELECT scenery.* FROM scenery , sceneryclass WHERE scenery.id = sceneryclass.scenery_id AND sceneryclass.class_id = '" . $classrow['id'] . "' ORDER BY scenery.name;";
			$scensql = $GBLmysqli->dbquery($q);
			while ($scenrow = $scensql->fetch_assoc()) {
				echo '<tr><td></td><td></td><td></td><td><b style="color:MidnightBlue;">' . $scenrow['name'] . '</b></td></tr>';
			}
		} else {
			echo '<tr><td></td><td>Cenário:</td><td></td><td>Default</td></tr>';
		}
		echo '</table>';

		if($courseHL){
			echo '<table style="background-color:#FAFAF4;color:#4080A0;"><tr><td>';
		}
		
		$q = "SELECT vacancies.* , unit.acronym , unit.id AS courseid FROM vacancies,unit WHERE vacancies.course_id = unit.id AND vacancies.class_id = '". $classrow['id'] . "' ORDER BY unit.acronym;";
		$vacsql = $GBLmysqli->dbquery($q);
		if ($vacedit && !$classrow['agreg']) {
    		echo '<table style="background-color:#E0FFE0;color:#8000B0;"><tr><td>';
			formvacedit($vacsql);
		echo '</td></tr></table>';
		} else {
			formvacdisplay($vacsql);
		}
		
		if($courseHL){
			echo '</td></tr></table>';
		}

		
	}
	
	
	function formvacedit ($vacsql) {
		global $GBLmysqli;
		global $can_class;
		global $can_addclass;
		global $postedit;
		global $readonly;
		global $GBLcommentpattern;
		global $GBLcommentcolor;
		global $GBLvackind;
		
		
		echo '<table>';
		while ($vacrow = $vacsql->fetch_assoc()) {
			echo '<tr>';
			$vacid = 'vac'.$vacrow['id'];
			if (($_SESSION['role'][$vacrow['course_id']]['can_vacancies'] | $_SESSION['role']['isadmin'])  & !$readonly) {
				$_SESSION['vacancies'][$vacrow['id']] = $vacid;
				echo '<td>Vagas solicitadas ' . $vacrow['acronym'] . ': ' . 
                                        formpatterninput(3,1,'[0-9]+','Núm.',$vacid.'asked',$vacrow['askednum']) ;
                                echo '&nbsp;reserv: ' . 
                                        formpatterninput(3,1,'[0-9]+','Núm.',$vacid.'askedreserv',$vacrow['askedreservnum']) . 
                                        '&nbsp;&nbsp;' .
                                        spanformat('','',$GBLvackind[$vacrow['courseid']]) . '&nbsp;&nbsp;';
				formselectsession($vacid.'askedstatusid','status',$vacrow['askedstatus_id']);
				echo '</td>';
			} else {
				echo formhiddenval($vacid.'asked',$vacrow['askednum']);
                                echo formhiddenval($vacid.'askedreserv',$vacrow['askedreservnum']);
				echo formhiddenval($vacid.'askedstatusid',$vacrow['askedstatus_id']);
				echo '<td>Vagas solicitadas ' . $vacrow['acronym'] . ': ' .  $vacrow['askednum'] . 
                                        ' (+' . $vacrow[askedreservnum] . ') ' .
                                        '<sub>'.
                                                spanformat('smaller','',$GBLvackind[$vacrow['courseid']]). 
                                        '</sub>' . 
                                        '&nbsp;&nbsp;&nbsp;&nbsp;' . 
                                        spanformat('', $_SESSION['statuscolor'][$vacrow['askedstatus_id']] , '(' .  $_SESSION['status'][$vacrow['askedstatus_id']] . ')') . 
                                        '</td>';
			}
			if (($_SESSION['role'][$_POST['unitid']]['can_vacancies'] | $_SESSION['role']['isadmin']) & !$readonly) {
				$_SESSION['vacancies'][$vacrow['id']] = $vacid;
				echo '<td>&nbsp;&nbsp;Vagas concedidas: ' .	formpatterninput(3,1,'[0-9]+','Núm.',$vacid.'given',$vacrow['givennum']);
                                echo '&nbsp;reserv: ' . formpatterninput(3,1,'[0-9]+','Núm.',$vacid.'givenreserv',$vacrow['givenreservnum']);
				formselectsession($vacid.'givenstatusid','status',$vacrow['givenstatus_id']);
				echo '</td>';
			} else {
				echo formhiddenval($vacid.'given',$vacrow['givennum']);
                                echo formhiddenval($vacid.'givenreserv',$vacrow['givenreservnum']);
				echo formhiddenval($vacid.'givenstatusid',$vacrow['givenstatus_id']);
				echo '<td>&nbsp;&nbsp;Vagas concedidas: ' . 
                                        $vacrow['givennum'] . ' (+' . $vacrow[givenreservnum] . ') ' . 
                                        '&nbsp;&nbsp;&nbsp;&nbsp;' . 
                                        spanformat('', $_SESSION['statuscolor'][$vacrow['givenstatus_id']]  , '(' .  
                                                $_SESSION['status'][$vacrow['givenstatus_id']] . ')') . 
                                        '</td>';
			}
			echo '<td>&nbsp;&nbsp;Vagas Ocupadas: ' . $vacrow['usednum'] . ' (+' . $vacrow['usedreservnum'] . ')</td>';
			echo '</tr>';
			if (($_SESSION['role'][$vacrow['course_id']]['can_vacancies'] | $_SESSION['role']['isadmin'])  & !$readonly) {
				$_SESSION['vacancies'][$vacrow['id']] = $vacid;
				echo '<tr>';
				echo '<td>Obs.: ' . formpatterninput(48,16,$GBLcommentpattern,'Obs.',$vacid.'comment',$vacrow['comment']).'</td>';
				echo '<td></td></tr>';
			} else {
				echo formhiddenval($vacid.'comment',$vacrow['comment']);
				echo '<tr><td>'.'&nbsp;&nbsp;&nbsp;'.spanformat('smaller',$GBLcommentcolor,$vacrow['comment']) . '</td><td></td></tr>';
			}
			
		}
		echo '</table>';
	}
	
	function formvacdisplay ($vacsql) {
		global $GBLmysqli;
		global $can_class;
		global $can_addclass;
		global $postedit;
		global $GBLcommentcolor;
		global $GBLvackind;
		
		echo '<table>';
		$totalasked = 0;
                $totalreserv = 0;
		$totalgiven = 0;
		$totalgivenreserv = 0;
		$totalused = 0;
		$totalusedreserv = 0;
		while ($vacrow = $vacsql->fetch_assoc()) {
			echo '<tr>';
			if ($GBLvackind[$vacrow['courseid']] == 'OB') {
				$coursecolor='#0000F0';
				$coursebold=true;
			} else {
				$coursecolor=null;
				$coursebold=false;
			}
			echo '<td>Vagas solicitadas ' . $vacrow['acronym'] . ': ' .  
                                $vacrow['askednum'] . ' (+' . $vacrow['askedreservnum'] . ') ' . '<sub>'.spanformat('smaller',$coursecolor,$GBLvackind[$vacrow['courseid']],null,$coursebold). '</sub>'. 
                                '&nbsp;&nbsp;&nbsp;&nbsp;' . 
                                spanformat('',$_SESSION['statuscolor'][$vacrow['askedstatus_id']], '(' .  
                                        $_SESSION['status'][$vacrow['askedstatus_id']] . ')') . 
                                '</td>';
			echo '<td>&nbsp;&nbsp;Vagas concedidas: ' . 
                                $vacrow['givennum'] .  ' (+' . $vacrow['givenreservnum'] . ') ' . 
                                '&nbsp;&nbsp;&nbsp;&nbsp;' . 
                                spanformat('', $_SESSION['statuscolor'][$vacrow['givenstatus_id']] , '(' .  
                                        $_SESSION['status'][$vacrow['givenstatus_id']] . ')') .  
                                '</td>';
                                
			echo '<td>Vagas Ocupadas: ' . $vacrow['usednum'] .  ' (+' . $vacrow['usedreservnum'] . ') ' . '</td>';

			echo '</tr>';
			if(!(($_SESSION['status'][$vacrow['askedstatus_id']] == 'dup') | ($_SESSION['status'][$vacrow['givenstatus_id']] == 'dup'))) {
				$totalasked += $vacrow['askednum'];
                                $totalreserv += $vacrow['askedreservnum'];
				$totalgiven += $vacrow['givennum'];			
				$totalgivenreserv += $vacrow['givenreservnum'];			
				$totalused += $vacrow['usednum'];			
				$totalusedreserv += $vacrow['usedreservnum'];			
			}
			if ($vacrow['comment']) {
				echo '<tr><td>'.'&nbsp;&nbsp;&nbsp;'.spanformat('smaller',$GBLcommentcolor,$vacrow['comment']) . '</td><td></td></tr>';
			}
		}
		echo '<tr><td>' . spanformat('','darkblue','Total: ' . $totalasked .' (+' . $totalreserv . ')') . '</td><td>&nbsp;&nbsp;' . spanformat('','darkblue','Total: ' . $totalgiven.' (+' . $totalgivenreserv . ')') . '</td><td>' . spanformat('','darkblue','Total: ' . $totalused.' (+' . $totalusedreserv . ')') . '</td></tr>';
		echo '</table>';
		
	}
	


?>
