<?php
$mysqli->set_sessionXtravalues();

$GblProfIDs = array();
$GblClassIDs = array();
$GblDiscIDs = array();
$GblSegIDs = array();
$GblVacIDs = array();


	 $q = "SELECT * FROM `discipline`;";
	 $result = $mysqli->dbquery($q);
	 while ($sqlrow=$result->fetch_assoc()) {
		 $GblDiscIDs[$sqlrow['code']] = $sqlrow['id'];
	 }


$dbcntA = 0;
function DBinsertgrade($Course,$Term,$DiscCode,$DiscName,$DiscCred,$DiscKind) {

    global $mysqli;
	global $dbcntA;
	
	global $GblDiscIDs;
	
    $dbcntA +=1;
	echo "Acnt:$dbcntA &nbsp;&nbsp;&nbsp;\n";

	$DiscDeptCode = substr($DiscCode,0,5);
	
	if(!($discid = $GblDiscIDs[$DiscCode])) {
	    if ($_SESSION['unitbycode'][$DiscDeptCode]) {
			$q="INSERT INTO `discipline` (`dept_id` , `code` , `name` , `Lcred` , `Tcred`) values ( '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "' , '" . $DiscCode . "' , '" . $DiscName . "' , '0' , '" . $DiscCred . "' );";
			$mysqli->dbquery($q);
			$discid = $GblDiscIDs[$DiscCode] = $mysqli->insert_id;
		} else {
			// ERR
			echo "<br>ERR !! Dept.: $DiscDeptCode not there !<br>";
		}
	}
		
	// discipline already 'exists'... hopefuly lacking an association
	$q="INSERT INTO `coursedisciplines` (`course_id` , `term_id` , `discipline_id` , `disciplinekind_id`) VALUES ('" . $_SESSION['unitbycode'][$Course]['id'] . "' , '" . $_SESSION['termbycode'][$Term]['id'] . "' , '" . $discid . "' , '" . $_SESSION['disckindbycode'][$DiscKind]['id'] . "');";
	$mysqli->dbquery($q);
}



$dbcntB = 0;
function DBinsertdept($grpAcro,$sem,$DiscCode,$DiscName,$DiscCred,$Class,$ClassVac,$ClassDay,$ClassStart,$ClassDur,$ClassProf,$Campus,$Building,$Room) {
	global $dbcntB;
    global $mysqli;
	
	global $GblProfIDs;
	global $GblClassIDs;
	global $GblDiscIDs;
	global $GblSegIDs;
	global $GblVacIDs;
	
	$PreSem = '';
	$PreDiscCode = '';
	$PreClass = '';
    $dbcntB +=1;
	echo "Bcnt:$dbcntB &nbsp;&nbsp;&nbsp;\n";
	
	if (!($_SESSION['sem'][$sem])) {
		$q="INSERT INTO `semester` (`name`) VALUES ('" . $sem . "');"; 
		$mysqli->dbquery($q);
		$_SESSION['sem'][$sem]=$mysqli->insert_id;
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
			$mysqli->dbquery($q);
			$_SESSION['buildingbyacronym'][$Building]['roombyacronym'][$Room] = $mysqli->insert_id;
		}
		$RoomID = $_SESSION['buildingbyacronym'][$Building]['roombyacronym'][$Room];
	}
	/// repeat for $Room
	
	$DiscDeptCode = substr($DiscCode,0,5);
	
	if ($_SESSION['unitbycode'][$DiscDeptCode]['mark']) { 
		/// this is one of the few...
		$aux = preg_split('/\s+/', $ClassProf);
		$ClassProfnickname = $aux[0];
	} else {
		/// 'not relevant'...
		$ClassProf = "Prof. $DiscDeptCode";
		$ClassProfnickname = $ClassProf;
	}
	if (!($ClassProfID = $GblProfIDs[$ClassProf])) {
		$q = "INSERT INTO `prof` (`nickname`,`name`,`dept_id`,`profkind_id`) VALUES ('" . $ClassProfnickname . "' , '" . $ClassProf . "' , '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "' , '1');";
		$mysqli->dbquery($q);
		$ClassProfID = $GblProfIDs[$ClassProf] = $mysqli->insert_id;
	} 
	
	 if (!($DiscID = $GblDiscIDs[$DiscCode])) {
		 $q="INSERT INTO `discipline` (`dept_id` , `code` , `name` , `Lcred` , `Tcred`) values ( '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "' , '" . $DiscCode . "' , '" . $DiscName . "' , '0' , '" . $DiscCred . "' );";
		 $mysqli->dbquery($q);
		 $DiscID = $GblDiscIDs[$DiscCode] = $mysqli->insert_id;
	 }

	
	/// find out if class already 'inserted'
	if (!($ClassID = $GblClassIDs[$semID][$DiscID][$Class])) {
   		$q = "INSERT INTO `class` (`name`,`sem_id`,`discipline_id`) VALUES ('" . $Class . "' , '" . $semID . "' , '" . $DiscID . "');";
		$mysqli->dbquery($q);
		$ClassID = $GblClassIDs[$semID][$DiscID][$Class] = $mysqli->insert_id;	
	}
	
	// find out if vacancies already 'inserted' as well...
	$grpID = $_SESSION['unitbyacronym'][$grpAcro]['id'];
	if(($_SESSION['unitbyacronym'][$grpAcro]['iscourse'])) {
		
		if(!($GblVacIDs[$ClassID][$grpID])) {
			$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) VALUE ( '" .$ClassID . "' , '" . $grpID . "' , '" . $ClassVac . "' , '" . $ClassVac . "')";
			$mysqli->dbquery($q);
			$GblVacIDs[$ClassID][$grpID] = $mysqli->insert_id;
		}
	}
	
	if(!($GblSegIDs[$ClassID][$ClassDay][$ClassStart])) {
		$q = "INSERT INTO `classsegment` (`day`,`start`,`length`,`room_id`,`class_id`,`prof_id`) VALUES ('" . $_SESSION['weekday'][$ClassDay] . "' , '" . $ClassStart . "' , '" . $ClassDur . "' , '" . $RoomID . "' , '" . $ClassID . "' , '" . $ClassProfID . "');";	
		$result = $mysqli->dbquery($q);
		$GblSegIDs[$ClassID][$ClassDay][$ClassStart] = $mysqli->insert_id;
	}

}	
	
	
	function roomset($roomtypeid,$roomcap,$buildingid,$roomacronym) {
		global $mysqli;
		$q = "UPDATE `room` SET `roomtype_id` = '".$roomtypeid."' , `capacity` = '".$roomcap."' WHERE `building_id` = '".$buildingid."' AND `acronym` = '".$roomacronym."' ";
		$mysqli->dbquery($q);
	}



?>


