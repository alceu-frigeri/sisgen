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

function strform($phase,$validate,$target='') {
    global $regpage;
    global $MAXSIZE;
    global $loginacc;
    global $loginadmin;
    $str;
    if ($target) {
	$target="target='_blank'";
    };
    
    if ($validate) {
	$str = "<form method='post' onsubmit='$validate'  $target enctype='multipart/form-data' action='/sbai17/?q=$regpage'>\n";
    } else {
	$str = "<form method='post' $target enctype='multipart/form-data' action='/sbai17/?q=$regpage'>\n";
    }
    $str .= "<input type='hidden' name='phase' value='$phase' />\n";
    $str .= "<input type='hidden' name='MAX_FILE_SIZE' value='${MAXSIZE}' />\n";
    if ($loginacc) {
	$str .= "<input type='hidden' name='hash' value='$loginacc[valhash]' />\n";
    } 
    if ($loginadmin) {
	$str .= "<input type='hidden' name='hash2' value='$loginadmin[valhash]' />\n";
	$str .= "<input type='hidden' name='hash3' value='$loginacc[hashadmin]' />\n";
    }
    if ($phane == 'usr_select') {
	if ($_POST['acc_id']) {
	    $str .= "<input type='hidden' name='acc_id' value='$_POST[acc_id]' />\n";
	}
	if ($_POST['name']) {
	    $str .= "<input type='hidden' name='name' value='$_POST[name]' />\n";
	}
	if ($_POST['email']) {
	    $str .= "<input type='hidden' name='email' value='$_POST[email]' />\n";
	}
    }
    

    return $str;
}

function regform($phase,$validate) {
    echo strform($phase,$validate);
}

function strhiddenval($field,$val) {
    return "<input type='hidden' name='$field' id='$field' value=\"$val\" />\n";
}

function strsubmit($field,$val) {
    return "<input type='submit' value='$val' />\n";
}

function hiddenfield($field) {
    echo "<input type='hidden' name='$field' id='$field' value=\"" .$_POST[$field]. "\" />\n";
}

function tablineB($txt,$txtB,$color,$colorB) {
    echo "<tr><td style =\"text-align:right; width:30%; background-color:$color\">${txt}</td><td style=\"text-align:left; background-color:$color\"><font color='$colorB'><b>${txtB}</b></font></td></tr>\n";
}


function tablineC($txt,$txtB,$color,$colorB) {
    echo "<tr><td style =\"text-align:right; width:30%; background-color:$color\">${txt}</td><td style=\"text-align:left\"><font color='$colorB'><b>${txtB}</b></font></td></tr>\n";
}

function tabline($txt,$field,$color) {
    echo "<tr><td style =\"text-align:right; width:30%; background-color:$color\">${txt}</td><td style=\"text-align:left\"><b>" .$_POST[$field]. "</b></td></tr>\n";
}

function tabfile($txt,$field) {
    echo "<tr><td style='text-align:right; width:30%'>${txt}</td><td style='text-align:left'><input type='file' name='${field}' id='${field}' required/></td></tr>\n";
}

function tabfileB($txt,$txt2,$txt3,$field,$color) {
    global $MAXSIZE;
    
    echo "<tr><td style='text-align:right; width:30%'>${txt}</td><td style='text-align:left'>
    <font color='$color'><b>${txt2}</b></font>\n";
    
    regform('usrfile_updt',"return validate_${field}()");

    echo "<input type='file' name='${field}' id='${field}'  required/>
    <input type='submit' value='Inserir Comprovante $txt3'/>
    </form>
    </td></tr>\n";
    echo "<script type='text/javascript'>
          $('#${field}').bind('change', function() {
           if (this.files[0].size > $MAXSIZE) {
              alert(\"Arquivo muito grande!\\n Reduza o mesmo, por favor.\");
           }
          });
          function validate_${field}() {
            if (document.getElementById('${field}').files[0].size > $MAXSIZE) {
                alert(\"Arquivo comprovante $txt3 muito grande !\\n Reduza o mesmo, por favor.\");
	        return false;
            } else {
	      return true;
            }
	  }
         </script>
";
    
}


function eventlog($level,$action,$str) {
    global $DBVALS;
    global $mysqli;
    global $loginacc;
    global $loginadmin;
    global $debug;
    
    $level='DB_'.$level;

    $trace=debug_backtrace();
    $caller=$trace[1];
    
    if ($loginadmin) {
	$acc_id=$loginadmin['acc_id'];
    } else {
	if ($loginacc) {
	    $acc_id=$loginacc['acc_id'];
	} else {
	    $acc_id=0;
	}
    }
    
    if (!($stmt = $mysqli->prepare("INSERT INTO `log` (level_id,log_date,log_action,log_caller,acc_id,log_IP,log_agent,log_str) VALUES ($DBVALS[$level],NOW(),?,?,'$acc_id',?,?,?);"))) {
	echo "LOG Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!$stmt->bind_param('sssss',$action,$caller['function'],$_SERVER['REMOTE_ADDR'],$_SERVER['HTTP_USER_AGENT'],$str)) {
	echo "LOG Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    };
    if (!$stmt->execute()) {
	echo "LOG execute failed: (" . $stmt->errno . ") " . $stmt->error;
    };
    $mysqli->commit();
}


function getaccdata($admin=false,$hash2 = '') {
    global $mysqli;
    global $loginacc;
    if($admin) {
	$hashfield='hashadmin';
    } else {
	$hashfield='valhash';
    }
    if ($hash2) {
	$hash=$hash2;
    } else {
	$hash=$loginacc['valhash'];
    }

    if (!($result = $mysqli->query("SELECT * FROM `account` WHERE account.$hashfield = '$hash'"))) {
	$err= "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	eventlog('DBERROR','',$err) ;
	return NULL;
    }


    if ($accdt = $result->fetch_assoc()) {
	if($debug)
	{
	    echo "something found<br>\n";
       	    var_dump($accdt);
	};
    }


    return $accdt;
}

function getemailaccdata($email) {
    global $mysqli;

    if (!($result = $mysqli->query("SELECT * FROM `account` WHERE account.email = '${email}'"))) {
	$err= "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	eventlog('DBERROR','',"($email) $err") ;
	return NULL;
    }
    return $result->fetch_assoc();
}



function getuserdata($admin=false,$hash2 = '') {
    global $mysqli;
    global $loginacc;
    global $debug;
    
    if($admin) {
	$hashfield='hashadmin';
    } else {
	$hashfield='valhash';
    }
    
    if ($hash2) {
	$hash=$hash2;
    } else {
	$hash=$loginacc['valhash'];
    }

    $query="SELECT * FROM `account`,`types`,`association`,`modalities`,`country`,`states`,`treatment`,`userdata`,`baseprices`,`pap_str` WHERE account.$hashfield = '$hash' AND account.acc_id = userdata.acc_id AND userdata.country_id = country.country_id AND userdata.treat_id = treatment.treat_id AND userdata.mod_id = modalities.mod_id AND userdata.assoc_id = association.assoc_id AND userdata.type_id = types.type_id AND types.type_id = baseprices.type_id AND association.assoc_id = baseprices.assoc_id AND modalities.mod_id = baseprices.mod_id AND userdata.num_papers = pap_str.num_papers AND userdata.state_id = states.state_id;";
    //    echo "<b>$query</b><br>\n";
    if (!($result = $mysqli->query($query))) {
	$err= "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	eventlog('DBERROR','',$err) ;
	return NULL;
    }
    if($debug)
    { echo "hash:$hash<br>\n";};
    
    
    if ($usrdt = $result->fetch_assoc()) {
	if($debug)
	{
	    echo "something found<br>\n";
       	    var_dump($usrdt);
	};
	$fields=array('name','familyname','fullname','fullnamereceipt','receipt_data','affiliation','fullaffiliation','type_desc','mod_desc','assoc_desc','state_name','acc_obs','sec_obs');
	foreach ($fields as $field) {
       	    $usrdt[$field] = utf8_decode($usrdt[$field]);
	}
    }
    return $usrdt;
}


///////////
//////////
///////////

function mail_logincheck($email,$passwd) {
    global $mysqli;
    global $microstamp;
    
    if (!($result = $mysqli->query("SELECT * FROM `account` WHERE `email` = '$email';"))) {
        echo "Prepare query: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (($sqlrow=$result->fetch_assoc())) {
	//	echo "hash ORG: $sqlrow[valhash]<br>\n";
	if (($passwd == $sqlrow['password'] && $sqlrow['activ'])) {
	    $newhash=md5("$email - $microstamp");
	    $result->close();
	    if (!($mysqli->query("UPDATE  `account` SET `valhash` = '$newhash' WHERE `email` = '$email';"))) {
		$err = "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		eventlog('DBERROR','',$err) ;
	    }
	    //            return array($newhash,$sqlrow['can_edit'],$sqlrow['type_id']);
	    $sqlrow['valhash']=$newhash;
	    //	    echo "hash NEW: $sqlrow[valhash]<br>\n";
	    eventlog('INFO','login',"user $sqlrow[email] login") ;
	    return $sqlrow;
	}
    }
    $result->close();
    //    return array(false,false,false);
    return NULL;
}

function hash_logincheck($hash) {
    global $mysqli;
    global $microstamp;
    
    if (!($result = $mysqli->query("SELECT * FROM `account` WHERE `valhash` = '$hash';"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    //    echo "posthash: $hash<br>\n";
    if (($sqlrow=$result->fetch_assoc())) {
	//	echo "hash ORG: $sqlrow[valhash]<br>\n";
	if ($sqlrow['activ']) {
	    $newhash=md5("$sqlrow[email] - $microstamp");
	    
	    // changing hash only at first login...	    
	    //	    $result->close();
	    //	    if (!($mysqli->query("UPDATE  `account` SET `valhash` = '$newhash' WHERE `valhash` = '$hash';"))) {
	    //		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    //	    }
	    //	    $sqlrow['valhash']=$newhash;
	    $result->close();
	    if (!($mysqli->query("UPDATE  `account` SET `last_action` = NOW() WHERE `valhash` = '$hash';"))) {
	    	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    }
	    return $sqlrow;
	}
    }
    //    return array(false,false);
    return NULL;
}

function logincheck($login,$passwd,$field) {
    global $mysqli;
    
    if (!($stmt = $mysqli->prepare("SELECT password FROM `account` WHERE `$field` = ?;"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!$stmt->bind_param('s',$login)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    };
    $result  = $stmt->execute();
    $stmt->bind_result($pass2);

    if ($stmt->fetch()) {
	if ($passwd == $pass2) {
	    return true;
	}
    }
    return false;
}











include 'Reg_usrpage.php';
include 'Reg_login.php';
include 'Reg_usrreg.php';
include 'Reg_usrdata.php';
include 'Reg_usrfiles.php';
include 'Reg_usrsubmit.php';

include 'Reg_secpage.php';



$gethash=$_GET['h'];
$posthash=$_POST['hash'];
$posthash2=$_POST['hash2'];
$loginacc='';
$loginadmin='';
if ($gethash != '') {
    reg_confirm($gethash); 
} else {
    switch($_POST['phase']) {
	case 'subscribe':
     	    reg_subscribe();
	    reg_default();
	    break;
	case 'forgot':
	    reg_forgot();
	    reg_default();
	    break;
	case 're-send':
	    reg_resend();
	    reg_default();
	    break;
	case 'login':
	    $loginacc = mail_logincheck($_POST['emailA'],$_POST['passA']);
	    //eventlog('DEBUG','debug',"user:$_POST[emailA] type: $loginacc[type_id] (admin: $DBVALS[DB_ADMIN])");
	    if ($loginacc){
		if (($loginacc['type_id'] == $DBVALS['DB_ADMIN']) || ($loginacc['type_id'] == $DBVALS['DB_SEC']) ) {
		    $loginadmin=$loginacc;
		    sec_main($loginadmin['type_id']);
		} else {
		    
		    $accdt = getaccdata();
		    $usrdt = getuserdata();
		    usr_main($accdt,$usrdt);
		}
	    } else {
		echo "<h3><font color='red'>Email/Password Inválidos</font></h3>";
		reg_default();
     	    }
	    break;
	default:
	    if ($_POST['hash2']) {
		$loginadmin = hash_logincheck($_POST['hash2']);
		$loginacc = hash_logincheck($_POST['hash']);
		if (!$loginadmin) { reg_default(); return; };
		if (($loginadmin['type_id'] == $DBVALS['DB_ADMIN']) || ($loginadmin['type_id'] == $DBVALS['DB_SEC']) ) {
		    switch($_POST['phase']) {
			case 'bac_1':
			    bac_1($loginadmin['type_id']);
			    break;
			case 'bac_2':
			    bac_2($loginadmin['type_id']);
			    break;
			case 'bac_3':
			    bac_3($loginadmin['type_id']);
			    break;
			case 'bac_email':
			    bac_email($loginadmin['type_id']);
			    break;
       			case 'mailing':
			    mailing($loginadmin['type_id']);
			    break;

			case 'usr_main':
       			case 'sec_main':
			    sec_main($loginadmin['type_id']);
			    break;
			case 'all_accounts':
			    all_accounts($loginadmin['type_id']);
			    break;
			case 'csv_confirmed':
			    csv_confirmed($loginadmin['type_id']);
			    break;
			case 'sba_list':
			    sba_list($loginadmin['type_id']);
			    break;
			case 'csv_sessions':
			    csv_sessions($loginadmin['type_id']);
			    break;
			case 'papers_hanging':
			    papers_hanging($loginadmin['type_id']);
			    break;
			case 'pending_accounts':
			    pending_accounts($loginadmin['type_id']);
			    break;
			case 'waiting_accounts':
			    waiting_accounts($loginadmin['type_id']);
			    break;
			case 'mini_waitlist':
			    mini_waitlist($loginadmin['type_id']);
			    break;
			case 'mini_list':
			    mini_list($loginadmin['type_id']);
			    break;
			case 'usr_select':
			    usr_select($loginadmin['type_id']);
			    break;
			case 'paper_select':
			    paper_select($loginadmin['type_id']);
			    break;
       			case 'usr_data':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    $loginacc = $accdt;
			    usr_main($accdt,$usrdt,$loginadmin['type_id']);
			    break;
       			case 'usrdata_edit':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    $loginacc = $accdt;
			    usrdata_edit($accdt,$usrdt);
			    break;
       			case 'usrdata_updt':
			    echo "here<br>\n";
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    usrdata_updt($accdt,$usrdt,$loginadmin['type_id']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    $loginacc = $accdt;
			    usr_main($accdt,$usrdt,$loginadmin['type_id']);
			    break;
      			case 'usrreg_edit':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    $loginacc = $accdt;
			    //			    echo 'admin here<br>';
			    usrreg_edit($accdt,$usrdt,$loginadmin['type_id']);
			    break;
       			case 'usrreg_updt':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    $loginacc = $accdt;
			    usrreg_updt($accdt,$usrdt,true,$loginadmin['type_id']);
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    usr_main($accdt,$usrdt,$loginadmin['type_id']);
			    break;
       			case 'usrfile_updt':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    $loginacc = $accdt;
			    usrfiles_updt($accdt,$usrdt,$loginadmin['type_id']);
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    $loginacc = $accdt;
			    usr_main($accdt,$usrdt,$loginadmin['type_id']);
			    break;

       			case 'usr_submit':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    $loginacc = $accdt;
			    usr_submit($accdt,$usrdt);
			    $accdt = getaccdata();
			    $usrdt = getuserdata();
			    usr_main($accdt,$usrdt);
			    break;

			    

			case 'sec_submit':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    sec_submit($accdt,$usrdt,$loginadmin['type_id']);
			    break;
       			case 'sec_data':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    sec_data($accdt,$usrdt,$loginadmin['type_id']);
			    break;
       			case 'sec_info':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    sec_info($accdt,$usrdt,$loginadmin['type_id']);
			    break;
       			case 'sec_overbook':
			    $accdt = getaccdata(true,$_POST['hash3']);
			    $usrdt = getuserdata(true,$_POST['hash3']);
			    sec_overbook($accdt,$usrdt,$loginadmin['type_id']);
			    break;
		    }
		    switch ($_POST['phase']) {
			case 'sec_submit':
			case 'sec_data':
			case 'sec_overbook':
			case 'sec_info':
			    switch ($_POST['phase2']) {
       				case 'usr_main':
       				case 'sec_main':
				    sec_main();
				    break;
				case 'all_accounts':
				    all_accounts($loginadmin['type_id']);
				    break;
				case 'csv_confirmed':
				    csv_confirmed($loginadmin['type_id']);
				    break;
				case 'usr_select':
				    usr_select($loginadmin['type_id']);
				    break;
				case 'csv_sessions':
				    csv_sessions($loginadmin['type_id']);
				    break;
				case 'papers_hanging':
				    papers_hanging($loginadmin['type_id']);
				    break;
				case 'pending_accounts':
				    pending_accounts($loginadmin['type_id']);
				    break;
				case 'waiting_accounts':
				    waiting_accounts($loginadmin['type_id']);
				    break;
				case 'mini_waitlist':
				    mini_waitlist($loginadmin['type_id']);
				    break;
				case 'mini_list':
				    mini_list($loginadmin['type_id']);
				    break;

			    }
			    
		    }
		    

		};

	    } else {
		$loginacc = hash_logincheck($_POST['hash']);
		if (!$loginacc) { reg_default(); return; };
		$accdt = getaccdata();
		$usrdt = getuserdata();
		switch($_POST['phase']) {
       		    case 'usr_main':
			usr_main($accdt,$usrdt);
			break;
       		    case 'usr_submit':
			usr_submit($accdt,$usrdt);
			$accdt = getaccdata();
			$usrdt = getuserdata();
			usr_main($accdt,$usrdt);
			break;
		    default:
			if ($loginacc['can_edit']) {
			    switch($_POST['phase']) {
       				case 'usrdata_edit':
				    usrdata_edit($accdt,$usrdt);
				    break;
       				case 'usrdata_updt':
				    usrdata_updt($accdt,$usrdt);
				    $usrdt = getuserdata();
				    $accdt = getaccdata();
				    usr_main($accdt,$usrdt);
				    break;
      				case 'usrreg_edit':
				    //				    echo 'here<br>';
				    usrreg_edit($accdt,$usrdt);
				    break;
       				case 'usrreg_updt':
				    usrreg_updt($accdt,$usrdt);
				    $usrdt = getuserdata();
				    $accdt = getaccdata();
				    usr_main($accdt,$usrdt);
				    break;
       				case 'usrfile_updt':
				    usrfiles_updt($accdt,$usrdt);
				    $usrdt = getuserdata();
				    //				    $accdt = getaccdata();
				    usr_main($accdt,$usrdt);
				    break;
				default:
				    reg_default();
				    break;
			    }
			} else {
			    if ($loginacc['can_files']) {
				switch($_POST['phase']) {
				    case 'usrfile_updt':
					usrfiles_updt($accdt,$usrdt);
					$usrdt = getuserdata();
					usr_main($accdt,$usrdt);
					break;
				    default:
					reg_default();
					break;
				}
			    } else {
				reg_default();
			    }
			    
			}
			break;
		}
	    }
    }
    
}

// To locate orphan records !!!
//  SELECT * FROM `account` WHERE not EXISTS (SELECT 1 FROM userdata WHERE account.acc_id = userdata.acc_id)

///
// select papers.paper_num, papers.paper_title FROM papers,account,acc_papers WHERE account.acc_id = acc_papers.acc_id AND acc_papers.paper_num = papers.paper_num AND account.confirmed = '0'


///
// 
?>

<?php $mysqli->close(); ?>






