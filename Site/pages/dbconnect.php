<?php

$DBVALS = [];

class DBclass extends mysqli {
    public function __construct($host, $user, $pass, $db) {
        parent::init();

        if (!parent::options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = TRUE')) {
            die('Setting MYSQLI_INIT_COMMAND failed');
        }

        if (!parent::options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
            die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
        }

        if (!parent::real_connect($host, $user, $pass, $db)) {
            die('Connect Error (' . mysqli_connect_errno() . ') '
                    . mysqli_connect_error());
        }
		$this->set_charset("utf8");
    }


	public function dbquery($q,$logOK=null) {
		if ($result = $this->query($q)) {
			if ($logOK) {
				$this->eventlog($logOK);
			}
		} else {
			$err = "Query <" . $q . "> failed: (" . $this->errno . ") " . $this->error;
			echo '<br><b>Query&nbsp;&nbsp;</b><span style="font-size:smaller;">' . htmlspecialchars($q,ENT_QUOTES) . '</span><b>&nbsp;&nbsp;&nbsp;FAILED <span style="color:red;">' . htmlspecialchars($this->error,ENT_QUOTES) . '</span></b></p>';
			$this->eventlog(array('level'=>'DBERROR',	'action'=> $logOK['action'].'(dbquery)', 'str' => $err, 'xtra' => 'dbconnect.php'));
		}
		return $result;
	}


//LOG array
//$log['level'] INFO/LOGIN/...
//$log['action'] action being executed, to be recorded, like login,dbupdate...
//$log['str'] 
//$log['xtra']
	public function eventlog($logdata) {
		$trace=debug_backtrace();
		$callerA=$trace[1]['function'];  // who called us (we are 0)
		$callerB=$trace[2]['function'];  // who called us (we are 0)
		$callerC=$trace[3]['function'];  // who called us (we are 0)
      
	    // just in case something fails...
		$logline = "LOG:".$logdata['level']." :: userID:".$_SESSION['userid'] . "(". $_SESSION['useremail'] .") remote:".$_SERVER['REMOTE_ADDR']." ".$_SERVER['HTTP_USER_AGENT']. "Callers: <".
			$callerA['function']."><".$callerB['function']."><".$callerC['function'].">  action:".$logdata['action']."==>".$logdata['str']."(".$logdata['xtra'].")";
		//
		if (!($stmt = $this->prepare("INSERT INTO `log` (loglevel_id,user_id,browserIP,browseragent,callerA,callerB,callerC,action,logline,logxtra) VALUES (?,?,?,?,?,?,?,?,?,?);"))) {
			$str = "<br>LOG Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
			echo $str;
			writeLogFile("$str \n $logline \n"); // last resort !!!
		}
		if (!$stmt->bind_param('iissssssss',$_SESSION['log'][$logdata['level']],$_SESSION['userid'],$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],
		    $callerA,$callerB,$callerC,$logdata['action'],$logdata['str'],$logdata['xtra'])) {
			$str = "<br>LOG Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
			echo $str;
			writeLogFile("$str \n $logline \n"); // last resort !!!		
		};
		if (!$stmt->execute()) {
			$str = "<br>LOG execute failed: (" . $stmt->errno . ") " . $stmt->error;
			echo $str;
			writeLogFile("$str \n $logline \n"); // last resort !!!		
		};
		$this->commit();
	}


	public function maillogincheck($email,$passwd) {
		list($microstamp,$sec) = explode(' ',microtime(false));
		list($nothing,$microstamp) = explode('.',$microstamp);

		$q = "SELECT * FROM `account` WHERE `email` = '" . $email . "';";
		$result = $this->dbquery($q);
		
		if (($sqlrow=$result->fetch_assoc())) {
			if (($passwd == $sqlrow['password'] && $sqlrow['activ'])) {
				$newhash=md5("$email - $microstamp");
				$this->set_sessionvalues($sqlrow,$newhash);
				$result->close();
				$q = "UPDATE  `account` SET `sessionhash` = '".$newhash."' WHERE `email` = '".$email."';";
				$this->dbquery($q,array('level'=>'LOGIN','action'=>'login','str'=>"user $sqlrow[email] login",'xtra'=>'core.php'));
				return TRUE;
			}
		}
		$result->close();
		return FALSE;
	}

	public function hashcheck() {
		$q = "SELECT * FROM `account` WHERE `sessionhash` = '$_SESSION[sessionhash]' AND `id` = '$_SESSION[userid]';";
		$result = $this->dbquery($q);
		if (($sqlrow=$result->fetch_assoc())) {
			return TRUE;
		} else {
			$log=array('level'=>'WARNING','action'=>'session check','str'=>"hashcheck failed !",'xtra'=>'core.php');
			$this->eventlog($log) ;
		}
		$result->close();
		return FALSE;
	}


	public function hashpasswdcheck($passwd) {
		$q = "SELECT * FROM `account` WHERE `sessionhash` = '$_SESSION[sessionhash]' AND `password` = '${passwd}';";
		$result = $this->dbquery($q);
		if (($sqlrow=$result->fetch_assoc())) {
			return TRUE;
		} else {
			$log=array('level'=>'WARNING','action'=>'passwd check','str'=>"hash/passwd check failed !",'xtra'=>'core.php');
			$this->eventlog($log) ;
		}
		$result->close();
		return FALSE;
	}



	public function dbvaluesloop($q,$sessionkeyA,$sqlkeyA,$sqlvalA,$sessionkeyB=null,$sqlkeyB=null,$sqlvalB=null) {
		$result = $this->dbquery($q);
		while ($sqlrow = $result->fetch_assoc()) {
			$_SESSION[$sessionkeyA][$sqlrow[$sqlkeyA]] = $sqlrow[$sqlvalA];
			if ($sqlkeyB) {
				$_SESSION[$sessionkeyB][$sqlrow[$sqlkeyB]] = $sqlrow[$sqlvalB];
			}
		}
		$result->close();
	}


	public function set_sessionvalues($usrsql,$userhash) {
		$_SESSION['userid'] = $usrsql['id'];
		$_SESSION['useremail'] = $usrsql['email'];
		$_SESSION['username'] = $usrsql['name'];
		$_SESSION['userchgpasswd'] = $usrsql['chgpasswd'];
		$_SESSION['userdisplayname'] = $usrsql['displayname'];
		
		$_SESSION['sessionhash'] = $userhash;
	
		$_SESSION['usercanedit'] = '1';
	
		$q = "SELECT role.* , unit.acronym, unit.code, unit.iscourse, unit.isdept FROM role , unit  , accrole accr , account  WHERE " . 
			"role.unit_id = unit.id AND role.id = accr.role_id AND account.id = accr.account_id AND account.id = '".$_SESSION['userid']."';";
		$result = $this->dbquery($q);
		$boolkeys  = array('can_edit','can_dupsem','can_vacancies','can_class','can_addclass','can_disciplines','can_coursedisciplines','can_prof','can_viewlog');
		$txtkeys = array('rolename','description');
		while ($sqlrow = $result->fetch_assoc()) {
			$_SESSION['role']['isadmin'] = $_SESSION['role']['isadmin']  || $sqlrow['isadmin'];
			$_SESSION['usercanedit'] = $_SESSION['usercanedit'] & $sqlrow['can_edit'];
			if ($_SESSION['role'][$sqlrow['unit_id']]) {
				foreach ($boolkeys as $key) {
					$_SESSION['role'][$sqlrow['unit_id']][$key] = $_SESSION['role'][$sqlrow['unit_id']][$key] || $sqlrow[$key];
				}
				foreach ($txtkeys as $key) {
					$_SESSION['role'][$sqlrow['unit_id']][$key] = $_SESSION['role'][$sqlrow['unit_id']][$key] . ' / ' . $sqlrow[$key];
				}
			} else {
				$_SESSION['role'][$sqlrow['unit_id']] = $sqlrow;
			}
		}
		$result->close();

		$q = "select * from weekdays;";
		$this->dbvaluesloop($q,'weekday','abrv','id','weekday','id','abrv');
		
		$q="SELECT *  FROM `loglevel`;";
		$this->dbvaluesloop($q,'log','level','id');
		
		$_SESSION['bool'][0] = 'Não';
		$_SESSION['bool'][1] = 'Sim';

		
	}
	
	
	public function dbvaluesloopX($q,$keyA,$sqlkeyA,$keyB=null,$sqlkeyB=null) {
		$result = $this->dbquery($q);
		while ($sqlrow = $result->fetch_assoc()) {
			$_SESSION[$keyA][$sqlrow[$sqlkeyA]] = $sqlrow;
			if ($keyB) {
				$_SESSION[$keyB][$sqlrow[$sqlkeyB]] = $sqlrow;
			}
		}
		$result->close();
	}
	
	public function set_sessionXtravalues() {
		$q="SELECT *  FROM disciplinekind;";
		$this->dbvaluesloopX($q,'disckindbycode','code');

		$q="SELECT *  FROM unit;";
		$this->dbvaluesloopX($q,'unitbycode','code','unitbyacronym','acronym');
		
		$q="SELECT *  FROM term;";
		$this->dbvaluesloopX($q,'termbycode','code');

		$result = $this->dbquery("SELECT *  FROM building;");
		while ($sqlrow = $result->fetch_assoc()) {
			$_SESSION['buildingbyacronym'][$sqlrow['acronym']] = $sqlrow;
			$roomquery = $this->dbquery("SELECT room.*  FROM room , building WHERE room.building_id = building.id;");
			while ($sqlroom = $roomquery->fetch_assoc()) {
				$_SESSION['buildingbyacronym'][$sqlrow['acronym']]['roombyacronym'][$sqlroom['acronym']] = $sqlroom['id'];
			}
			$roomquery->close();
		}
		$result->close();
		
	}




}



/////
/////
/////


function myconnect() {
	$mysqli = new DBclass('bdlivre.ufrgs.br', 'sisgen', 'SKRqgFBASnUS', 'sisgen');
	
    $mysqli->autocommit(TRUE);

	//$mysqli->set_charset('utf8mb4');
	//$mysqli->query("SET NAMES utf8mb4 COLLATE unicode_520_ci");

    echo "<script type='text/javascript'>\n";

//    if (!($result = $mysqli->query("SELECT * FROM `types`"))) {
//	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
//	return NULL;
 //   }
//    while (($sqlrow=$result->fetch_assoc())) {
//    	$var = 'DB_'.strtoupper(utf8_decode($sqlrow['type_str']));
//	echo "  var $var = $sqlrow[type_id];\n";
//	$DBVALS[$var] = $sqlrow[type_id];
 //   }

    
    echo "</script>\n";

    
    return $mysqli;

}


// 'replicating' some DB tables into SESSION 
// to reduce the number of DB calls (inside a SESSION)
function dbsessionset() {
}





?>


