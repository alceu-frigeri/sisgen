<?php
$GBLmysqli->set_sessionXtravalues();

$GBLProfIDs = array();
$GBLClassIDs = array();
$GBLDiscIDs = array();
$GBLSegIDs = array();
$GBLVacIDs = array();
$GBLSemIDs = array();

	 $q = "SELECT * FROM `discipline`;";
	 $result = $GBLmysqli->dbquery($q);
	 while ($sqlrow = $result->fetch_assoc()) {
		 $GBLDiscIDs[$sqlrow['code']] = $sqlrow['id'];
	 }

	 $q = "SELECT * FROM `prof`;";
	 $result = $GBLmysqli->dbquery($q);
	 while ($sqlrow = $result->fetch_assoc()) {
		 $GBLProfIDs[$sqlrow['name']] = $sqlrow['id'];
	 }

	 $q = "SELECT * FROM `semester`;";
	 $result = $GBLmysqli->dbquery($q);
	 while ($sqlrow = $result->fetch_assoc()) {
		 $GBLSemIDs[$sqlrow['name']] = $sqlrow['id'];
	 }

	function fixvacancies(){
		global $GBLmysqli;
		
		$q = "INSERT INTO `vacancies` (`class_id` , `course_id`) " .
		"SELECT `class`.`id` , `cdisc`.`course_id` FROM `coursedisciplines` AS `cdisc` , `class` " . 
			"where `cdisc`.`discipline_id` = `class`.`discipline_id` AND " .
				"NOT EXISTS (SELECT * FROM `vacancies` AS `vac` WHERE `vac`.`class_id` = `class`.`id` AND `vac`.`course_id` = `cdisc`.`course_id`); ";
		$GBLmysqli->dbquery($q);
	}




	function ftablerestore($fname , $table) {
		global $GBLmysqli;
		
		$fhandler = fopen('csv/'.$fname , 'r');
		while ($line = fgetcsv($fhandler , 512 , ',' , '"' , '"')) {
			foreach ($line as &$val) {
				$val = $GBLmysqli->real_escape_string($val);
			}
			$q = "REPLACE INTO `".$table."` VALUES ('" . implode("','" , $line) . "');";
			//echo $q.'<br>';
			$GBLmysqli->dbquery($q);			
		}
		fclose($fhandler);		
	}

	function ftableexport($fname , $q) {
		global $GBLmysqli;
		
		$fhandler = fopen('csv/'.$fname , 'w');
		$sqlresult = $GBLmysqli->dbquery($q);
		while ($sqlrow = $sqlresult->fetch_assoc()) {
			fputcsv($fhandler , $sqlrow , ',' , '"' , '"');
		}
		fclose($fhandler);		
	}




$dbcntA = 0;
function DBinsertgrade($Course , $Term , $DiscCode , $DiscName , $DiscCred , $DiscKind) {

    global $GBLmysqli;
	global $dbcntA;
	
	global $GBLDiscIDs;
	
    $dbcntA +=1;
	echo "Acnt:$dbcntA $GBL_Tspc\n";

	$DiscDeptCode = substr($DiscCode , 0 , 5);
	
	if(!($discid = $GBLDiscIDs[$DiscCode])) {
	    if ($_SESSION['unitbycode'][$DiscDeptCode]) {
			$q = "INSERT INTO `discipline` (`dept_id` , `code` , `name` , `Lcred` , `Tcred`) values ( '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "' , '" . $DiscCode . "' , '" . $DiscName . "' , '0' , '" . $DiscCred . "' );";
			$GBLmysqli->dbquery($q);
			$discid = $GBLDiscIDs[$DiscCode] = $GBLmysqli->insert_id;
		} else {
			// TODO : ERR
			echo "<br>ERR !! Dept.: $DiscDeptCode not there !<br>";
		}
	}
		
	// discipline already 'exists'... hopefuly lacking an association
	$q = "INSERT INTO `coursedisciplines` (`course_id` , `term_id` , `discipline_id` , `disciplinekind_id`) VALUES ('" . $_SESSION['unitbycode'][$Course]['id'] . "' , '" . $_SESSION['termbycode'][$Term]['id'] . "' , '" . $discid . "' , '" . $_SESSION['disckindbycode'][$DiscKind]['id'] . "');";
	$GBLmysqli->dbquery($q);
}



$dbcntB = 0;
function DBinsertdept($grpAcro , $sem , $DiscCode , $DiscName , $DiscCred , $Class , $ClassVac , $ClassUsed , $ClassDay , $ClassStart , $ClassDur , $ClassProf , $Campus , $Building , $Room) {
	global $dbcntB;
    global $GBLmysqli;
	
	global $GBLProfIDs;
	global $GBLClassIDs;
	global $GBLDiscIDs;
	global $GBLSegIDs;
	global $GBLVacIDs;
	global $GBLSemIDs;
	
	$PreSem = '';
	$PreDiscCode = '';
	$PreClass = '';
    $dbcntB +=1;
	echo "Bcnt:$dbcntB $GBL_Tspc\n";
	 
//	vardebug($_SESSION['sem']);
//	vardebug($_SESSION['termbycode']);
	if (!($GBLSemIDs[$sem])) {
		$q = "INSERT INTO `semester` (`name` , `imported`) VALUES ('" . $sem . "' , '1');"; 
		$GBLmysqli->dbquery($q);
		$GBLSemIDs[$sem] = $GBLmysqli->insert_id;
	}
	$semID = $GBLSemIDs[$sem];
	
	
	/// verify that $Building already exists ...
	if (!($_SESSION['buildingbyacronym'][$Building])) {
		echo "<br>ERR !! building: $Building not there !<br>";
	} else {
		$BuildingID = $_SESSION['buildingbyacronym'][$Building]['id'];
		if (!($_SESSION['buildingbyacronym'][$Building]['roombyacronym'][$Room])) {
			// room not  there yet...
			$q = "INSERT INTO room (`acronym` , `name` , `building_id`) VALUES ('" . $Room . "' , 'Sala " . $Room . "' , '" . $BuildingID . "');";
			$GBLmysqli->dbquery($q);
			$_SESSION['buildingbyacronym'][$Building]['roombyacronym'][$Room] = $GBLmysqli->insert_id;
		}
		$RoomID = $_SESSION['buildingbyacronym'][$Building]['roombyacronym'][$Room];
	}
	/// repeat for $Room
	
	$DiscDeptCode = substr($DiscCode , 0 , 5);
	
	if ($_SESSION['unitbycode'][$DiscDeptCode]['mark']) { 
		/// this is one of the few...
		$aux = preg_split('/\s+/', $ClassProf);
		$ClassProfnickname = $aux[0];
	} else {
		/// 'not relevant'...
		$ClassProf = "Prof. $DiscDeptCode";
		$ClassProfnickname = $ClassProf;
	}
	if (!($ClassProfID = $GBLProfIDs[$ClassProf])) {
		$q = "INSERT INTO `prof` (`nickname` , `name` , `dept_id` , `profkind_id`) VALUES ('" . $ClassProfnickname . "' , '" . $ClassProf . "' , '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "' , '1');";
		$GBLmysqli->dbquery($q);
		$ClassProfID = $GBLProfIDs[$ClassProf] = $GBLmysqli->insert_id;
	} 
	
	 if (!($DiscID = $GBLDiscIDs[$DiscCode])) {
		 $q = "INSERT INTO `discipline` (`dept_id` , `code` , `name` , `Lcred` , `Tcred`) values ( '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "' , '" . $DiscCode . "' , '" . $DiscName . "' , '0' , '" . $DiscCred . "' );";
		 $GBLmysqli->dbquery($q);
		 $DiscID = $GBLDiscIDs[$DiscCode] = $GBLmysqli->insert_id;
	 }

	
	/// find out if class already 'inserted'
	if (!($ClassID = $GBLClassIDs[$semID][$DiscID][$Class])) {
   		$q = "INSERT INTO `class` (`name` , `sem_id` , `discipline_id`) VALUES ('" . $Class . "' , '" . $semID . "' , '" . $DiscID . "');";
		$GBLmysqli->dbquery($q);
		$ClassID = $GBLClassIDs[$semID][$DiscID][$Class] = $GBLmysqli->insert_id;	
	}
	
	// find out if vacancies already 'inserted' as well...
	$grpID = $_SESSION['unitbyacronym'][$grpAcro]['id'];
	if(($_SESSION['unitbyacronym'][$grpAcro]['iscourse'])) {
		
		if(!($GBLVacIDs[$ClassID][$grpID])) {
			$q = "INSERT INTO `vacancies` (`class_id` , `course_id` , `askednum` , `givennum` , `usednum`) VALUE ( '" .$ClassID . "' , '" . $grpID . "' , '" . $ClassVac . "' , '" . $ClassVac . "' , '" . $ClassUsed . "')";
			$GBLmysqli->dbquery($q);
			$GBLVacIDs[$ClassID][$grpID] = $GBLmysqli->insert_id;
		}
	}
	
	if(!($GBLSegIDs[$ClassID][$ClassDay][$ClassStart])) {
		$q = "INSERT INTO `classsegment` (`day` , `start` , `length` , `room_id` , `class_id` , `prof_id`) VALUES ('" . $_SESSION['weekday'][$ClassDay] . "' , '" . $ClassStart . "' , '" . $ClassDur . "' , '" . $RoomID . "' , '" . $ClassID . "' , '" . $ClassProfID . "');";	
		$result = $GBLmysqli->dbquery($q);
		$GBLSegIDs[$ClassID][$ClassDay][$ClassStart] = $GBLmysqli->insert_id;
	}

}	
	
	
function roomset($roomtypeid , $roomcap , $buildingid , $roomacronym) {
	global $GBLmysqli;
	$q = "UPDATE `room` SET `roomtype_id` = '".$roomtypeid."' , `capacity` = '".$roomcap."' WHERE `building_id` = '".$buildingid."' AND `acronym` = '".$roomacronym."' ";
	$GBLmysqli->dbquery($q);
}


$dbcntE = 0;
function DBinsertreserv($grpAcro , $sem , $DiscCode , $DiscName , $Class , $ClassReserv , $ClassUsedReserv) {
	global $dbcntE;
    global $GBLmysqli;
	
	global $GBLClassIDs;
	global $GBLDiscIDs;
	global $GBLVacIDs;
	global $GBLSemIDs;
	
	$PreSem = '';
	$PreDiscCode = '';
	$PreClass = '';
    $dbcntE +=1;
	echo "Ecnt:$dbcntE $GBL_Tspc\n";
	 
        if ( ($ClassReserv == 0) & ($ClassUsedReserv == 0)) {
                echo '(skipping) ';
                return;
        }
	
	if (!($semID  = $GBLSemIDs[$sem])) {
                echo "<br>ERR !! Term: $sem not there ! skipping<br>";
                return;
	}
        
	if (!($discID  = $GBLDiscIDs[$DiscCode])) {
                echo "<br>ERR !! Discipline: $DiscCode not there ! skipping<br>";
                return;
	}

        if (!($classID = $GBLClassIDs[$semID][$discID][$Class])) {
                $result = $GBLmysqli->dbquery("SELECT `class`.`id` FROM `class` WHERE `discipline_id` = '$discID' AND `sem_id` = '$semID' AND `name` = '$Class' ");
                $classrow = $result->fetch_assoc();
                if(!($classID = $classrow['id'])) {
                        echo "<br>ERR !! Class: $Class not there ! skipping<br>";
                        return;
                };
                $GBLClassIDs[$semID][$discID][$Class] = $classID;
        }
        
	if (!($grpID = $_SESSION['unitbyacronym'][$grpAcro]['id'])) {
                        echo "<br>ERR !! Unit: $grpAcro not there ! skipping<br>";
                        return;
        } elseif ($_SESSION['unitbyacronym'][$grpAcro]['iscourse']) {
			$GBLmysqli->dbquery("UPDATE `vacancies` SET `askedreservnum` = '$ClassReserv' , `givenreservnum` = '$ClassReserv' , `usedreservnum` = '$ClassUsedReserv' WHERE `class_id` = '$classID' AND `course_id` = '$grpID'; ");
                        echo "$sem($semID) $grpAcro($grpID) $DiscCode($discID) $Class($classID) $ClassReserv $ClassUsedReserv <br>";
        } else {
                        echo "<br>ERR !! Unit: $grpAcro isn't a course ! skipping<br>";
                        return;
        }        
}



$dbcntC = 0;
function courseupdt($coursecode , $disccode , $kindcode , $termcode) {
	global $GBLmysqli;
	global $dbcntC;
	
    $dbcntC +=1;
	echo "Ccnt:$dbcntC (".$disccode.")$GBL_Tspc\n";

	$q = "SELECT `cd`.`id` FROM `coursedisciplines` AS `cd` , `discipline` AS `disc` WHERE `cd`.`discipline_id` = `disc`.`id` and `cd`.`course_id` = '".$_SESSION['unitbycode'][$coursecode]['id']."' and `disc`.`code` = '".$disccode."'";
	$result = $GBLmysqli->dbquery($q);
	$cdrow = $result->fetch_assoc();
	if ($kindcode) {
		if ($termcode) {
			$q = "UPDATE `coursedisciplines` SET `term_id` = '".$_SESSION['termbycode'][$termcode]['id']."' , `disciplinekind_id` = '".$_SESSION['disckindbycode'][$kindcode]['id']."' WHERE `id` = '".$cdrow['id']."';";
		} else {
			$q = "UPDATE `coursedisciplines` SET  `disciplinekind_id` = '".$_SESSION['disckindbycode'][$kindcode]['id']."' WHERE `id` = '".$cdrow['id']."';";
		}
	} else {
		$q = "UPDATE `coursedisciplines` SET `term_id` = '".$_SESSION['termbycode'][$termcode]['id']."' WHERE `id` = '".$cdrow['id']."';";
	}
	$GBLmysqli->dbquery($q);
}

$dbcntD = 0;
function disccourserem($disclist , $courseid) {
	global $GBLmysqli;
	global $dbcntD;
	
    $dbcntD +=1;
	echo "Dcnt:$dbcntD rem(".$disccode.")$GBL_Tspc\n";
	
	foreach ($disclist as $code) {
		$dbcntD +=1;
		echo "Dcnt:$dbcntD (".$code.")$GBL_Tspc\n";
		$q = "SELECT `id` FROM `discipline` WHERE `code` = '".$code."'";
		$result = $GBLmysqli->dbquery($q);
		$discrow = $result->fetch_assoc();
		$q = "DELETE FROM `coursedisciplines` WHERE `course_id` = '".$courseid."' AND `discipline_id` = '".$discrow['id']."';";
		$GBLmysqli->dbquery($q);
		$q = "DELETE FROM `vacancies` WHERE `vacancies`.`course_id` = '".$courseid."' AND `vacancies`.`class_id` IN (SELECT `class`.`id` FROM `class` , `discipline` AS `disc` WHERE `class`.`discipline_id` = `disc`.`id` AND `disc`.`id` = '".$discrow['id']."');";
		$GBLmysqli->dbquery($q);
		
	}
}

?> 


