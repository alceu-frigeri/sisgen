<?php include "FLAGS.php" ?>

<?php
include "dbconnect.php";

$mysqli = myconnect();
?>


<?php
date_default_timezone_set('America/Sao_Paulo');
$timestamp=time();
$timestamp=date('Y-m-d H:i:s',$timestamp);

list($microstamp,$sec) = explode(' ',microtime(false));
list($nothing,$microstamp) = explode('.',$microstamp);

function mymail($email,$subject,$msg) {
    $msg .= "\n\nAtt.\n sisgen\nhttps://www.ufrgs.br/sisgen";
	$mailheaders  = 'MIME-Version: 1.0' . "\r\n";
	$mailheaders .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
	$mailheaders .= 'Content-Transfer-Encoding: base64' . "\r\n";
	$mailheaders .= "From: sisgen@ufrgs.br" . "\r\n";
	$mailsubject .= '=?UTF-8?B?' . base64_encode("sisgen - $subject") . '?=';
    mail("$email",$mailsubject,base64_encode($msg),$mailheaders);
}






function eventlog($level,$action,$usrid,$logstr,$logxtra) {
    global $DBVALS;
    global $mysqli;
    global $debug;
    
    $level='LOG_'.$level;

    $trace=debug_backtrace();
    $caller=$trace[1];  // who called us (0 is us)
      
    if (!($stmt = $mysqli->prepare("INSERT INTO `log` (loglevel_id,user_id,browserIP,browseragent,callersfunction,action,logline,logxtra) VALUES (?,?,?,?,?,?,?,?);"))) {
	echo "LOG Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!$stmt->bind_param('iissssss',$DBVALS[$level],$usrid,$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],$caller['function'],$action,$logstr,$logxtra)) {
	echo "LOG Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    };
    if (!$stmt->execute()) {
	echo "LOG execute failed: (" . $stmt->errno . ") " . $stmt->error;
    };
    $mysqli->commit();
}




function regacc_create() {
    global $mysqli;
    global $regpage;
    global $baseurl;
    global $microstamp;
    
    //    echo "<h2>Sistema de Inscrição</h2><hr>\n";
    //    echo "Dados submetidos:<br>\n";

    $email=$_POST['emailA'];
    $passwd=$_POST['passA'];
    $emailhash=md5($email);
    $hashadmin=md5("$email-$microstamp");
    $today = date('Y-m-d');
    //    echo "email: $email<br>\n";
    //    echo "password: $passwd<br>\n";
    //    echo "md5(email):$emailhash<br>\n";

    $email = $mysqli->real_escape_string($email);
    $passwd = $mysqli->real_escape_string($passwd);


    
    if (!($stmt = $mysqli->prepare("SELECT email,password,activ FROM `account` WHERE `email` = ?;"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!$stmt->bind_param('s',$email)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    };
    $result  = $stmt->execute();
    $stmt->bind_result($mail2,$pass2,$activ2);

    if ($stmt->fetch()) {
	if($activ2) {
	    echo "<h3>email já cadastrado!</h3> Acabamos de lhe re-enviar um Email com a sua senha de acesso.<br>\n";
	    mymail($mail2,"Senha de Acesso","Prezado(a)\n Sua senha é: $pass2");
	} else {
	    echo "<h3>email já cadastrado!</h3> Acabamos de lhe re-enviar um Email de ativação da sua conta.<br>\n";
	    $msg = "Obrigado por criar uma conta. \nPor favor, acesse o link abaixo para ativar a mesma\n\n".
   		   "${baseurl}?st=validate&h=$emailhash\n\n";
	    mymail($email,"Confirmação de Email",$msg);
	}
	
	
	$stmt->close();
    } else {
	$stmt->close();
	$type = T_CONG;


	$sql = "INSERT INTO `account` (`email`, `password`, `valhash`, `hashdate`) VALUES ('$email','$passwd','$emailhash','$today');";
	if (!($result=$mysqli->query($sql))) {
	    printf("<br>Error: %s<br>\n", $mysqli->error);
	}

	//     $mysqli->commit();
	echo "<h4>Obrigado por criar uma conta.</h4><br>
Você estará recebendo, em breve, um Email com instruções para ativar a sua conta.<br>";


	$msg = "Obrigado por criar uma conta. \nPor favor, acesse o link abaixo para ativar a mesma\n\n".
   	       "${baseurl}?st=validate&h=$emailhash\n\nAtt. sisgen";
	mymail($email,"Confirmação de Email",$msg);

    } 
}


function regacc_validate($gethash) {
    global $mysqli;
    global $regpage;
    
    echo "<h2>Confirmação de Email</h2><hr>\n";
    $sql = "SELECT * FROM `account` WHERE `account`.`valhash` = '$gethash'";
    if(!($result = $mysqli->query($sql))) {
	printf("Error: %s<br>\n", $mysqli->error);
    }
    if ($result->num_rows) {
	echo "Obrigado por confirmar seu Email<br>\n";
	$sql ="UPDATE `sisgen`.`account` SET `activ` = '1' WHERE `account`.`valhash` = '$gethash'";
	$result = $mysqli->query($sql);
	if (!$result) {printf("Error: %s<br>\n", $mysqli->error);};
	echo "Agora você já pode se logar no sistema !<br>\n";
    } else {
	echo "<font color='red'><b>Link Inválido ou Expirado</b></font><br>\n";
    }
}


function getacc_byemail($email) {
    global $mysqli;

    if (!($result = $mysqli->query("SELECT * FROM `account` WHERE account.email = '${email}'"))) {
	$err= "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	eventlog('DBERROR','',"($email) $err") ;
	return NULL;
    }
    return $result->fetch_assoc();
}



function regacc_passrecovery() {
    $accdt = getacc_byemail($_POST['emailA']);
    if ($accdt && $accdt['activ']) {
	mymail($accdt['email'],"Senha de Acesso","Prezado(a)\n Sua senha é: $accdt[password]");
    }
}


function regacc_valresend() {
    global $baseurl;
    $accdt = getacc_byemail($_POST['emailA']);
    if ($accdt && !$accdt['activ']) {
	$msg = "
Obrigado por criar uma conta.
Por favor, acesse o link abaixo para ativar a mesma

      ${baseurl}?st=validade&h=$accdt[valhash]

Att.
sisgen";
	mymail($email,"Confirmação de Email",$msg);
    }
}


//eventlog($level,$action,$usrid,$logstr,$logxtra)

function set_sessionvalues($userid,$userhash) {
	global $mysqli;
	$_SESSION['userid'] = $userid;
	$_SESSION['sessionhash'] = $userhash;
	
	if (!($result = $mysqli->query("SELECT rr.* , cd.acronym, cd.code, cd.iscourse, cd.isdept FROM role rr, unit cd , accrole accr , account acc  WHERE rr.unit_id = cd.id AND rr.id = accr.role_id AND acc.id = accr.account_id AND acc.id = $userid;"))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	$kinds = array('isadmin','isdept','iscourse');
	$boolkeys  = array('can_edit','can_dupsem','chg_vacancies','chg_class','can_viewlog','chg_disciplines','chg_coursedisciplines');
	$txtkeys = array('rolename','description');
	while ($sqlrow = $result->fetch_assoc()) {
		foreach ($kinds as $kind) {
			if($sqlrow[$kind]) {
				if ($_SESSION[$kind][$sqlrow['unit_id']]) {
					foreach ($boolkeys as $key) {
						$_SESSION[$kind][$sqlrow['unit_id']][$key] = $_SESSION[$kind][$sqlrow['unit_id']][$key] || $sqlrow[$key];
					}
					foreach ($txtkeys as $key) {
						$_SESSION[$kind][$sqlrow['unit_id']][$key] = $_SESSION[$kind][$sqlrow['unit_id']][$key] . ' / ' . $sqlrow[$key];
					}
				} else {
					$_SESSION[$kind][$sqlrow['unit_id']] = $sqlrow;
				}
			}
		}
	}
	$result->close();
	if (!($result = $mysqli->query("SELECT *  FROM unit;"))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	while ($sqlrow = $result->fetch_assoc()) {
		$_SESSION['unitbycode'][$sqlrow['code']] = $sqlrow;
		$_SESSION['unitbyacronym'][$sqlrow['acronym']] = $sqlrow;
	}
	$result->close();
	if (!($result = $mysqli->query("SELECT *  FROM disciplinekind;"))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	while ($sqlrow = $result->fetch_assoc()) {
		$_SESSION['disckindbycode'][$sqlrow['code']] = $sqlrow;
	}
	$result->close();
	if (!($result = $mysqli->query("SELECT *  FROM term;"))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	while ($sqlrow = $result->fetch_assoc()) {
		$_SESSION['termbycode'][$sqlrow['code']] = $sqlrow;
	}
	$result->close();
	if (!($result = $mysqli->query("SELECT *  FROM building;"))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	while ($sqlrow = $result->fetch_assoc()) {
		$_SESSION['buildingbyacronym'][$sqlrow['acronym']] = $sqlrow;
		if (!($roomquery = $mysqli->query("SELECT room.*  FROM room , building WHERE room.building_id = building.id;"))) {
			echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		while ($sqlroom = $roomquery->fetch_assoc()) {
			$_SESSION['buildingbyacronym'][$sqlrow['acronym']]['roombyacronym'][$sqlroom['acronym']] = $sqlroom;
		}
		$roomquery->close();
	}
	$result->close();
	$q = "select * from weekdays;";
	if (!($result = $mysqli->query($q))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	while ($sqlrow = $result->fetch_assoc()){
		$_SESSION['weekday'][$sqlrow['name']] = $sqlrow;
	}
	
	
	
}


function regacc_maillogincheck($email,$passwd) {
    global $mysqli;
    global $microstamp;
    
    if (!($result = $mysqli->query("SELECT * FROM `account` WHERE `email` = '$email';"))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (($sqlrow=$result->fetch_assoc())) {
	//	echo "hash ORG: $sqlrow[valhash]<br>\n";
	if (($passwd == $sqlrow['password'] && $sqlrow['activ'])) {
	    $newhash=md5("$email - $microstamp");
		set_sessionvalues($sqlrow['id'],$newhash);
	    $result->close();
	    if (!($mysqli->query("UPDATE  `account` SET `sessionhash` = '$newhash' WHERE `email` = '$email';"))) {
		$err = "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		eventlog('DBERROR','login(binding)',NULL,$err,'core.php') ;
	    }
	    //            return array($newhash,$sqlrow['can_edit'],$sqlrow['type_id']);
	    $sqlrow['valhash']=$newhash;
	    //	    echo "hash NEW: $sqlrow[valhash]<br>\n";
	    eventlog('LOGIN','login',$sqlrow['id'],"user $sqlrow[email] login","core.php") ;
	    return TRUE;
	}
    }
    $result->close();
    //    return array(false,false,false);
    return FALSE;
}

function regacc_hashcheck() {
    global $mysqli;
    global $microstamp;
    
    if (!($result = $mysqli->query("SELECT * FROM `account` WHERE `sessionhash` = '$_SESSION[sessionhash]' AND `id` = '$_SESSION[userid]';"))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (($sqlrow=$result->fetch_assoc())) {
		return TRUE;
    } else {
	    eventlog('WARNING','session check',$_SESSION['userid'],"hashcheck failed !","core.php") ;
	}
    $result->close();
    //    return array(false,false,false);
    return FALSE;
}


?>


