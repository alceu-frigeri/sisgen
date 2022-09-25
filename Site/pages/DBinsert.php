<?php

function DBinsertgrade($Course,$Term,$DiscCode,$DiscName,$DiscCred,$DiscKind) {
	// 1st: $DiscCode already exist ?
	// if NO 'create it' than go 2nd
	// if yes... go 2nd
	
	//2nd: $Course, $Disc association already exists ?
	// if YES 'flag warning'... END
	// if NO 'create association'... END
	
    global $mysqli;
    
	$DiscDeptCode = substr($DiscCode,0,5);
	
    if (!($DBquery = $mysqli->query("SELECT * FROM `discipline` WHERE `code` = '$DiscCode';"))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!($DiscDBline=$DBquery->fetch_assoc())) {
//		if (!($DBquery = $mysqli->query("SELECT * FROM `unit` WHERE `code` = '$DiscDeptCode';"))) {
//			echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
//		}
//		if (($DeptDBline=$DBquery->fetch_assoc())) {
	    if ($_SESSION['unitbycode'][$DiscDeptCode]) {
			$q="INSERT INTO `discipline` (`dept_id` , `code` , `long_name` , `Lcred` , `Tcred`) values ( '" . $_SESSION['unitbycode'][$DiscDeptCode]['id'] . "' , '" . $DiscCode . "' , '" . $DiscName . "' , '0' , '" . $DiscCred . "' );";
			if (!($result = $mysqli->query($q))) {
				echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
				echo "<br>" . $q . "<br>";
			}
			if (!($DBquery = $mysqli->query("SELECT * FROM `discipline` WHERE `code` = '$DiscCode';"))) {
				echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
			}
			if (!($DiscDBline=$DBquery->fetch_assoc())) {
			// ERR
			echo "<br>ERR !! discipline: $DiscCode still not there !<br>";
			}
		} else {
			// ERR
			echo "<br>ERR !! Dept.: $DiscDeptCode not there !<br>";
		}
	}
	// discipline already 'exists'... hopefuly lacking an association
	$q="INSERT INTO `coursedisciplines` (`course_id` , `term_id` , `discipline_id` , `disciplinekind_id`) VALUES ('" . $_SESSION['unitbycode'][$Course]['id'] . "' , '" . $_SESSION['termbycode'][$Term]['id'] . "' , '" . $DiscDBline['id'] . "' , '" . $_SESSION['disckindbycode'][$DiscKind]['id'] . "');";
	    if (!($DBquery = $mysqli->query($q))) {
				echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
				echo "<br>" . $q . "<br>";
		}
}

include 'gradesB.csv';
?>


