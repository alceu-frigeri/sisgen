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


	function fixvacancies(){
		global $mysqli;
		
		$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) " .
		"SELECT `class`.`id` , `cdisc`.`course_id`, '0' , '0'  FROM `coursedisciplines` AS `cdisc`,`class` " . 
			"where `cdisc`.`discipline_id` = `class`.`discipline_id` AND " .
				"NOT EXISTS (SELECT * FROM `vacancies` AS `vac` WHERE `vac`.`class_id` = `class`.`id` AND `vac`.`course_id` = `cdisc`.`course_id`); ";
		$mysqli->dbquery($q);
	}




	function ftablerestore($fname,$table) {
		global $mysqli;
		
		$fhandler = fopen('csv/'.$fname,'r');
		while ($line = fgetcsv($fhandler,512,',','"','"')) {
			foreach ($line as &$val) {
				$val = $mysqli->real_escape_string($val);
			}
			$q = "REPLACE INTO `".$table."` VALUES ('" . implode("','",$line) . "');";
			//echo $q.'<br>';
			$mysqli->dbquery($q);			
		}
		fclose($fhandler);		
	}

	function ftabledump($fname,$q) {
		global $mysqli;
		
		$fhandler = fopen('csv/'.$fname,'w');
		$sqlresult = $mysqli->dbquery($q);
		while ($sqlrow = $sqlresult->fetch_assoc()) {
			fputcsv($fhandler,$sqlrow,',','"','"');
		}
		fclose($fhandler);		
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
function DBinsertdept($grpAcro,$sem,$DiscCode,$DiscName,$DiscCred,$Class,$ClassVac,$ClassUsed,$ClassDay,$ClassStart,$ClassDur,$ClassProf,$Campus,$Building,$Room) {
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
			$q = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`,`usednum`) VALUE ( '" .$ClassID . "' , '" . $grpID . "' , '" . $ClassVac . "' , '" . $ClassVac . "' , '" . $ClassUsed . "')";
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



$dbcntC = 0;
function courseupdt($coursecode,$disccode,$kindcode,$termcode) {
	global $mysqli;
	global $dbcntC;
	
    $dbcntC +=1;
	echo "Ccnt:$dbcntC (".$disccode.")&nbsp;&nbsp;&nbsp;\n";

	$q = "SELECT `cd`.`id` FROM `coursedisciplines` AS `cd`, `discipline` AS `disc` WHERE `cd`.`discipline_id` = `disc`.`id` and `cd`.`course_id` = '".$_SESSION['unitbycode'][$coursecode]['id']."' and `disc`.`code` = '".$disccode."'";
	$result = $mysqli->dbquery($q);
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
	$mysqli->dbquery($q);
}

$dbcntD = 0;
function disccourserem($disclist,$courseid) {
	global $mysqli;
	global $dbcntD;
	
    $dbcntD +=1;
	echo "Dcnt:$dbcntD rem(".$disccode.")&nbsp;&nbsp;&nbsp;\n";
	
	foreach ($disclist as $code) {
		$dbcntD +=1;
		echo "Dcnt:$dbcntD (".$code.")&nbsp;&nbsp;&nbsp;\n";
		$q = "SELECT `id` FROM `discipline` WHERE `code` = '".$code."'";
		$result = $mysqli->dbquery($q);
		$discrow = $result->fetch_assoc();
		$q = "DELETE FROM `coursedisciplines` WHERE `course_id` = '".$courseid."' AND `discipline_id` = '".$discrow['id']."';";
		$mysqli->dbquery($q);
		$q = "DELETE FROM `vacancies` WHERE `vacancies`.`course_id` = '".$courseid."' AND `vacancies`.`class_id` IN (SELECT `class`.`id` FROM `class` , `discipline` AS `disc` WHERE `class`.`discipline_id` = `disc`.`id` AND `disc`.`id` = '".$discrow['id']."');";
		$mysqli->dbquery($q);
		
	}
}

// 07041 -> 33
// 857 995 1056 2835
?> 


