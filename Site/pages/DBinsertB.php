<?php

$dbcnt = 0;
function DBinsertdept($grpAcro,$sem,$DiscCode,$DiscName,$DiscCred,$Class,$ClassVac,$ClassDay,$ClassStart,$ClassDur,$ClassProf,$Campus,$Building,$Room) {
	// 1st: $DiscCode already exist ?
	// if NO 'create it' than go 2nd
	// if yes... go 2nd
	
	//2nd: $Course, $Disc association already exists ?
	// if YES 'flag warning'... END
	// if NO 'create association'... END
	global $dbcnt;
    global $mysqli;
	$PreSem = '';
	$PreDiscCode = '';
	$PreClass = '';
    $dbcnt +=1;
	echo "<br>cnt:$dbcnt<br>\n";
	
	if (!($_SESSION['sem'][$sem])) {
		$q="INSERT INTO `semester` (`name`) VALUES ('" . $sem . "');"; 
		if (!($result = $mysqli->query($q))) {
			echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
			echo "<br>" . $q . "<br>";
		}
		$q="SELECT `id` FROM `semester`  WHERE `name` = '" . $sem . "';"; 
		if (!($result = $mysqli->query($q))) {
			echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
			echo "<br>" . $q . "<br>";
		}
		if (!($DBsem=$result->fetch_assoc())) {
		// ERR
			echo "<br>ERR !! semester: $sem still not there !<br>";
		}
		$_SESSION['sem'][$sem]=$DBsem['id'];
	}
	$semID = $_SESSION['sem'][$sem];
	
	
	/// verify that $Building already exists ...
	if (!($_SESSION['buildingbyacronym'][$Building])) {
		echo "<br>ERR !! building: $Building not there !<br>";
	} else {
		$BuildingID = $_SESSION['buildingbyacronym'][$Building]['id'];
		if (!($_SESSION['buildingbyacronym'][$Building]['roombyacronym'][$Room])) {
			// room not  there yet...
			$q = "INSERT INTO room (`acronym`,`name`,`building_id`) VALUES ('" . $Room . "' , 'Sala " . $Room . "' , '" . $BuildingID . "');";
			if (!($result = $mysqli->query($q))) {
				echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
				echo "<br>" . $q . "<br>";
			}
			$q = "SELECT * FROM `room` WHERE `acronym` = '" . $Room . "' AND `building_id` = '" . $BuildingID . "';";
			
			if (!($roomquery = $mysqli->query($q))) {
				echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
				echo "<br>" . $q . "<br>";
			}
			$_SESSION['buildingbyacronym'][$Building]['roombyacronym'][$Room] = $roomquery->fetch_assoc();
		}
		$RoomID = $_SESSION['buildingbyacronym'][$Building]['roombyacronym'][$Room]['id'];
	}
	/// repeat for $Room
	
	$DiscDeptCode = substr($DiscCode,0,5);
	
	if ($_SESSION['unitbycode'][$DiscDeptCode]['mark']) { 
		/// this is one of the few...
		$aux = preg_split('/\s+/', $ClassProf);
		$ClassProfshortname = $aux[0];
	} else {
		/// 'not relevant'...
		$ClassProf = "Prof. $DiscDeptCode";
		$ClassProfshortname = $ClassProf;
	}
	$q = "SELECT * FROM `prof` WHERE `name` = '" . $ClassProf . "';";
	$result = $mysqli->dbquery($q);
	if (($aux = $result->fetch_assoc())) {
		$ClassProfID = $aux['id'];
	} else {
		$q = "INSERT INTO `prof` (`shortname`,`name`,`dept_id`,`profkind_id`) VALUES ('" . $ClassProfshortname . "' , '" . $ClassProf . "' , '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "' , '1');";
		if (!($result = $mysqli->query($q))) {
			echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
			echo "<br>" . $q . "<br>";
		}
		$q = "SELECT * FROM prof WHERE name = '" . $ClassProf . "' AND `dept_id` = '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "';";
		if (!($result = $mysqli->query($q))) {
			echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
			echo "<br>" . $q . "<br>";
		}
		$aux = $result->fetch_assoc();
		$ClassProfID = $aux['id'];
	}
	
	

	$q = "SELECT * FROM `discipline` WHERE `code` = '" . $DiscCode . "';";
	$result = $mysqli->dbquery($q);
	
	/// NEED to verify if the discipline already exists, if not INSERT IT ! (case in point: department discipline for 'other courses'!! )
	if (($aux = $result->fetch_assoc())) {
		$DiscID=$aux['id'];
	} else {
		$q="INSERT INTO `discipline` (`dept_id` , `code` , `long_name` , `Lcred` , `Tcred`) values ( '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "' , '" . $DiscCode . "' , '" . $DiscName . "' , '0' , '" . $DiscCred . "' );";
		$mysqli->dbquery($q);
		$DiscID = $mysqli->insert_id;
	}
	
	/// find out if class already 'inserted'
	$q = "SELECT * FROM `class` WHERE `name` = '". $Class . "' AND `discipline_id` = '" . $DiscID . "' AND `sem_id` = '" . $semID . "';";
	$result = $mysqli->dbquery($q);
	if(($aux = $result->fetch_assoc())) {
		$ClassID = $aux['id'];
	} else {
   		$q = "INSERT INTO `class` (`name`,`sem_id`,`discipline_id`) VALUES ('" . $Class . "' , '" . $semID . "' , '" . $DiscID . "');";
		$mysqli->dbquery($q);
		$ClassID = $mysqli->insert_id;
	};
	
	// find out if vacancies already 'inserted' as well...
	$grpID = $_SESSION['unitbyacronym'][$grpAcro]['id'];
	if(($_SESSION['unitbyacronym'][$grpAcro]['iscourse'])) {
		$q = "SELECT * FROM `vacancies` WHERE `class_id` = '" . $ClassID . "' AND `course_id` = '" . $grpID . "'";
		$result = $mysqli->dbquery($q);
		if(!($result->fetch_assoc())) {
			$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) VALUE ( '" .$ClassID . "' , '" . $grpID . "' , '" . $ClassVac . "' , '" . $ClassVac . "')";
			$mysqli->dbquery($q);
		}
	}


    $q = "INSERT INTO `classsegment` (`day`,`start`,`length`,`room_id`,`class_id`,`prof_id`) VALUES ('" . $_SESSION['weekday'][$ClassDay]['id'] . "' , '" . $ClassStart . "' , '" . $ClassDur . "' , '" . $RoomID . "' , '" . $ClassID . "' , '" . $ClassProfID . "');";	
	$result = $mysqli->dbquery($q);
}	
	
	/// verify that $DiscCode alread exists !!!
	
	/// $Class is already defined for said $DiscCode. if not, insert into DB 'copy code into $classTEMP' (including 'Vacancy' entry)
	/// => create ClassSEGMENT for current 'call'
	
	

include 'semestresB.csv';
?>


