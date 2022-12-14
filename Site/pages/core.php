<?php
date_default_timezone_set('America/Sao_Paulo');

$timestamp=time();
$timestamp=date('Y-m-d H:i:s',$timestamp);

list($microstamp,$sec) = explode(' ',microtime(false));
list($nothing,$microstamp) = explode('.',$microstamp);

$domainurl='https://www.ufrgs.br';
$baseurl=$domainurl.'/sisgen';
$basepage='/sisgen/';
$debug=true;

$sisgensetup=true; //to enable/disable 'initial' import/fix pages (admin)
$sisgenimportCSV=true; //'new' simple way, direct from CSV file...

// some handy/aux values
	$commentcolor='teal';
//	$commentpattern='[a-zA-Z0-9 \'\"\?\!\.\-]+';
	$commentpattern="[a-zA-Z0-9 \.\-]+";
	$discpattern='[a-zA-Z0-8à-äè-ëì-ïò-öù-üÀ-ÄÈ-ËÌ-ÏÒ-ÖÙ-ÜçÇ \(\)\-]+';
	$namepattern='[a-zA-Z0-8à-äè-ëì-ïò-öù-üÀ-ÄÈ-ËÌ-ÏÒ-ÖÙ-ÜçÇ \'\-\.@]+';
	$passwdpattern='(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$';
	
	$Gblspc='&nbsp;&nbsp;';
	$GblDspc=$Gblspc.$Gblspc;
	

include 'dbconnect.php';

$mysqli = myconnect();


function mymail($email,$subject,$msg) {
    $msg .= "\n\nAtt.\n sisgen\n$baseurl";
	$mailheaders  = 'MIME-Version: 1.0' . "\r\n";
	$mailheaders .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
	$mailheaders .= 'Content-Transfer-Encoding: base64' . "\r\n";
	$mailheaders .= 'From: sisgen@ufrgs.br' . "\r\n";
	$mailsubject .= '=?UTF-8?B?' . base64_encode("sisgen - $subject") . '?=';
    mail("$email",$mailsubject,base64_encode($msg),$mailheaders);
}


function writeLogFile($msg) { 
     if (!$handle = @fopen('log.txt', 'a')) {
          echo "<br>ERR opening LOG file !!!</br>\n";
          exit;
     } else {
          if (@fwrite($handle,"$msg\r\n") === FALSE) {
			echo "<br>ERR writing to LOG file !!!</br>\n";
            exit;
          }
          @fclose($handle);
     }
}

function vardebug($var) {
	global $debug;
	if($debug) {
		echo '<pre>';
		var_dump($var);
		echo '</pre>';
	}
}

function regacc_create() {
    global $mysqli;
    global $regpage;
    global $baseurl;
    global $microstamp;
    
    //    echo '<h2>Sistema de Inscrição</h2><hr>\n';
    //    echo 'Dados submetidos:<br>\n';

    $email=$_POST['emailA'];
    $passwd=$_POST['passA'];
    $emailhash=md5($email);
    $today = date('Y-m-d');
    //    echo 'email: $email<br>\n';
    //    echo 'password: $passwd<br>\n';
    //    echo 'md5(email):$emailhash<br>\n';

    $email = $mysqli->real_escape_string($email);
    $passwd = $mysqli->real_escape_string($passwd);
    list($usrname,$usrdomain) = explode('@',$email,2);

    
    if (!($stmt = $mysqli->prepare("SELECT email,password,activ FROM `account` WHERE `email` = ?;"))) {
		echo 'Prepare failed: (' . $mysqli->errno . ') ' . $mysqli->error;
    }
    if (!$stmt->bind_param('s',$email)) {
		echo 'Binding parameters failed: (' . $stmt->errno . ') ' . $stmt->error;
    };
    $result  = $stmt->execute();
    $stmt->bind_result($mail2,$pass2,$activ2);

    if ($stmt->fetch()) {
		if($activ2) {
			echo '<h3>email já cadastrado!</h3> Acabamos de lhe re-enviar um Email com a sua senha de acesso.<br>\n';
			mymail($mail2,'Senha de Acesso','Prezado(a)\n Sua senha é:'. $pass2);
		} else {
			echo '<h3>email já cadastrado!</h3> Acabamos de lhe re-enviar um Email de ativação da sua conta.<br>\n';
			$msg = "Obrigado por criar uma conta. \nPor favor, acesse o link abaixo para ativar a mesma\n\n".
				"${baseurl}?st=validate&h=$emailhash\n\n";
			mymail($email,'Confirmação de Email',$msg);
		}
		$stmt->close();
    } else {
		$stmt->close();

		$sql = "INSERT INTO `account` (`email`, `password`, `name` , `displayname` , `valhash`) VALUES ('$email','$passwd','$email','$usrname','$emailhash');";
		$result=$mysqli->dbquery($sql);

		echo '<h4>Obrigado por criar uma conta.</h4><br>
Você estará recebendo, em breve, um Email com instruções para ativar a sua conta.<br>';

		$msg = "Obrigado por criar uma conta. \nPor favor, acesse o link abaixo para ativar a mesma\n\n".
   	       "${baseurl}?st=validate&h=$emailhash\n\nAtt. sisgen";
		mymail($email,'Confirmação de Email',$msg);
		$msg ="P/Registro:\n\nConta registrada: $email\n";
		mymail('alceu.frigeri@ufrgs.br','Conta Nova - sisgen',$msg);

    } 
}


function regacc_validate($gethash) {
    global $mysqli;
    global $regpage;
    
    echo '<h2>Confirmação de Email</h2><hr>';
    $sql = "SELECT * FROM `account` WHERE `account`.`valhash` = '$gethash'";
	$result = $mysqli->dbquery($sql);
    if ($result->num_rows) {
		echo 'Obrigado por confirmar seu Email<br>';
		$sql ="UPDATE `sisgen`.`account` SET `activ` = '1' WHERE `account`.`valhash` = '$gethash'";
		$result = $mysqli->dbquery($sql);
		echo 'Agora você já pode se logar no sistema !<br>';
	} else {
		echo '<b>'.spanformat('','red','Link Inválido ou Expirado').'</b><br>';
	}
}


function getacc_byemail($email) {
    global $mysqli;
	
    $email = $mysqli->real_escape_string($email);
	$q = "SELECT * FROM `account` WHERE account.email = '".$email."'";
	if(!($result = $mysqli->dbquery($q))) {
		return NULL;
    }
    return $result->fetch_assoc();
}



function regacc_passrecovery() {
    $accdt = getacc_byemail($_POST['emailA']);
    if ($accdt && $accdt['activ']) {
		mymail($accdt['email'],'Senha de Acesso',"Prezado(a)\n Sua senha é: $accdt[password]");
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
	mymail($email,'Confirmação de Email',$msg);
    }
}


function duplicatecourse($courseid,$acronym,$code,$name,$comment) {
	global $mysqli;
	
	$q = "INSERT INTO `unit` (`acronym`,`code`,`name`,`iscourse`,`isdept`) VALUES ('".$acronym."','".$code."','".$name."','1','1')";
	$result = $mysqli->dbquery($q);
	$newid = $mysqli->insert_id;
	$q = "INSERT INTO `coursedisciplines` (`course_id`,`term_id`,`discipline_id`,`disciplinekind_id`) SELECT '".$newid."' , `cd`.`term_id` , `cd`.`discipline_id`  , `cd`.`disciplinekind_id` FROM `coursedisciplines` AS `cd` WHERE `course_id` = '".$courseid."'";
	$mysqli->dbquery($q);
	$q= "SELECT `id` FROM `status` WHERE `status` = 'dup';";
	$result = $mysqli->dbquery($q);
	$strow=$result->fetch_assoc();
	$q = "INSERT INTO `vacancies` (`course_id`,`class_id`,`askednum`,`givennum`,`comment`,`askedstatus_id`,`givenstatus_id`) SELECT '".$newid."' , `vc`.`class_id` , `vc`.`askednum`  , `vc`.`givennum` , '".$comment."' , '".$strow['id']."' , '".$strow['id']."' FROM `vacancies` AS `vc` WHERE `course_id` = '".$courseid."'";
	$mysqli->dbquery($q);
}



function duplicatesem($currsemid,$newsemname) {
	global $mysqli;
	
	$newsem = $mysqli->real_escape_string($newsemname);
	$q = "SELECT * FROM semester WHERE `name` = '" . $newsem . "';";
	$result = $mysqli->dbquery($q);
	if ($sqlrow = $result->fetch_assoc()) {
		echo "ERR: semestre já existente ! </br>";
	} else {
		$q = "INSERT INTO `semester` (`name`) VALUES ('" . $newsem . "');";
		$mysqli->dbquery($q);
		$newsemid = $mysqli->insert_id;


		$qnewclass = "INSERT INTO `class` (`name`,`agreg`,`partof`,`sem_id`,`discipline_id`) SELECT `cl`.`name`, `cl`.`agreg` , `cl`.`partof`, '".$newsemid."' , `cl`.`discipline_id` FROM `class` AS `cl` WHERE  `cl`.`sem_id` = '".$currsemid."';";
		//echo "<br> $qnewclass";
		$mysqli->dbquery($qnewclass);

		$qsegment = "INSERT INTO `classsegment` (`class_id`,`day`,`start`,`length`,`room_id`,`prof_id`) SELECT `new`.`id` , `cs`.`day` , `cs`.`start` , `cs`.`length` , `cs`.`room_id` , `cs`.`prof_id` " . 
		"FROM `class` AS `org`, `class` AS `new` , `classsegment` AS `cs` " . 
		"WHERE `cs`.`class_id` = `org`.`id` AND `org`.`sem_id` = '".$currsemid."' AND `new`.`name` = `org`.`name` AND `new`.`discipline_id` = `org`.`discipline_id` AND `new`.`sem_id` = '".$newsemid."';";
		//echo "<br> $qsegment";
		$mysqli->dbquery($qsegment);


		$qvacancy = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`givennum`) SELECT `new`.`id` , `vc`.`course_id` , `vc`.`askednum` , `vc`.`givennum`  " .
		"FROM `class` AS `org`, `class` AS `new` , `vacancies` AS `vc` " . 
		"WHERE `vc`.`class_id` = `org`.`id` AND `org`.`sem_id` = '".$currsemid."' AND `new`.`name` = `org`.`name` AND `new`.`discipline_id` = `org`.`discipline_id` AND `new`.`sem_id` = '".$newsemid."';";
		//echo "<br> $qvacancy";
		$mysqli->dbquery($qvacancy);

	
	}
	
}




///// other 'help' functions


function checkweek($q,$courseid=null,$termid=null) {
	global $mysqli;
	$flag=array();

	$result = $mysqli->dbquery($q);
	while ($sqlrow = $result->fetch_assoc()) {
		$disccodes[$sqlrow['code']] = $sqlrow['code'];
		$disc[$sqlrow['code']] = $sqlrow['discname'];
		if (!$vac[$sqlrow['code'] . ' - ' . $sqlrow['name']]) {
			if($courseid) {
				$q = "SELECT `askednum`  AS `total` FROM `vacancies` WHERE `class_id` = '".$sqlrow['classid']."' AND `course_id` = '".$courseid."';";
			} else {
				$q = "SELECT SUM(givennum) AS `total` FROM `vacancies` WHERE `class_id` = '".$sqlrow['classid']."';";
			}
			$vacresult = $mysqli->dbquery($q);
			$vacrow = $vacresult->fetch_assoc();
			$vac[$sqlrow['code'] . ' - ' . $sqlrow['name']] = $vacrow['total'];
		}
		if ($vac[$sqlrow['code'] . ' - ' . $sqlrow['name']]) {
			for ($i=0; $i < $sqlrow['length']; $i++) {
				if($sqlrow['disckind']) {$kind=' ('.$sqlrow['disckind'].') ';} else {$kind='';};
				$start=$sqlrow['start'];
				$day=$sqlrow['day'];
				$discweek[$day][$start+$i][$sqlrow['code']] += 1;
				$discflag[$sqlrow['code']] = 1;
			}
		}
	}
	if($termid) {
		$q = "SELECT `discipline`.`code` FROM `discipline`,`coursedisciplines`,`disciplinekind` WHERE `coursedisciplines`.`course_id` = '".$courseid."' AND " . 
			"`coursedisciplines`.`term_id` = '".$termid."' AND `coursedisciplines`.`disciplinekind_id` = `disciplinekind`.`id` AND " . 
			"`coursedisciplines`.`discipline_id` = `discipline`.`id` AND (`disciplinekind`.`code` = 'OB' OR `disciplinekind`.`code` = 'AL');";
		$termsql = $mysqli->dbquery($q);
		while ($termrow = $termsql->fetch_assoc()) {
			if(!$discflag[$termrow['code']]) {$flag['ob']=1;};
			//echo $termrow['code'].'&nbsp;&nbsp;';
		}
	}
	for ($j=7;$j<22;$j++) {
		for ($i=2;$i<8;$i++) {
			if (count($discweek[$i][$j]) > 1) {
				$flag['disc']=1;
			} else {
				if(max($discweek[$i][$j]) > 1) {
					$flag['class']=1;
				}
			}
		}
	}
	return($flag);
}






function dbweekmatrix($q,$courseid=null,$termid=null) {
	global $mysqli;
	
	  $colors = array(
				'#CF0000',
				'#00CF00',
				'#0000CF',
				'#9F0000',
				'#009F00',
				'#00009F',
				'#9F9F00',
				'#009F9F',
				'#9F009F',
				'#9F9F4F',
				'#4F9F9F',
				'#9F4F9F');

	$result = $mysqli->dbquery($q);
	while ($sqlrow = $result->fetch_assoc()) {
		$disccodes[$sqlrow['code']] = $sqlrow['code'];
		$disc[$sqlrow['code']] = $sqlrow['discname'];
		if (!$vac[$sqlrow['code'] . ' - ' . $sqlrow['name']]) {
			if($courseid) {
				$q = "SELECT `askednum`  AS `total` FROM `vacancies` WHERE `class_id` = '".$sqlrow['classid']."' AND `course_id` = '".$courseid."';";
			} else {
				$q = "SELECT SUM(givennum) AS `total` FROM `vacancies` WHERE `class_id` = '".$sqlrow['classid']."';";
			}
			$vacresult = $mysqli->dbquery($q);
			$vacrow = $vacresult->fetch_assoc();
			$vac[$sqlrow['code'] . ' - ' . $sqlrow['name']] = $vacrow['total'];
		}
		if ($vac[$sqlrow['code'] . ' - ' . $sqlrow['name']]) {
			for ($i=0; $i < $sqlrow['length']; $i++) {
				if($sqlrow['disckind']) {$kind=' ('.$sqlrow['disckind'].') ';} else {$kind='';};
				$start=$sqlrow['start'];
				$day=$sqlrow['day'];
				$d = $sqlrow['code'] . $kind . ' - ' . $sqlrow['name'] . ' (' . $vac[$sqlrow['code'] . ' - ' . $sqlrow['name']] . ')';
				$week[$day][$start+$i][] = $d;
				$discweek[$day][$start+$i][$sqlrow['code']] += 1;
				$seg[$d] = $sqlrow['code'];
				$discflag[$sqlrow['code']] = 1;
			}
		}
		$discdept[$sqlrow['code']]=$sqlrow['discdeptid'];
		$discid[$sqlrow['code']]=$sqlrow['discid'];
	}
	$i=0;
	foreach ($disccodes as $d) {
		$disccolor[$d] = $colors[$i];
		$i++;
	}

	echo '<table>';
	echo '<tr style="border-bottom:1px solid black"><td>Hora</td>';
	for ($i=2; $i <8; $i++) {
		echo "<td> " . $_SESSION['weekday'][$i] . '&nbsp;&nbsp;</td>';
	}
	echo '</tr>';
	for ($j=7;$j<22;$j++) {
		echo '<tr style="border-bottom:1px solid black"><td>' . $j . ':30&nbsp;&nbsp;</td>';
		for ($i=2;$i<8;$i++) {
			$td='<td>';
			if (count($discweek[$i][$j]) > 1) {
				$td='<td style="background:#FFEBEB;">';
			} else {
				if(max($discweek[$i][$j]) > 1) {
					$td='<td style="background:#EBFFEB;">';
				}
			}
			echo $td;
			$b='';
			foreach ($week[$i][$j] as $d) {
				echo $b . spanformat('',$disccolor[$seg[$d]], '&nbsp;&nbsp;'.$d.'&nbsp;&nbsp;&nbsp;&nbsp;');
				$b='<br>';
			}
			echo '&nbsp;&nbsp;</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
	foreach ($disccodes as $d) {
		if($courseid){
			$q = "SELECT `kind`.`code` FROM `disciplinekind` AS `kind` , `coursedisciplines` AS `cd` WHERE `cd`.`course_id` = '".$courseid."' AND `cd`.`discipline_id` = '".$discid[$d]."' AND `cd`.`disciplinekind_id`= `kind`.`id`;";
			$result = $mysqli->dbquery($q);
			$sqlrow=$result->fetch_assoc();
			$kind = '<sub>'.spanformat('smaller','',$sqlrow['code']).'</sub>';
		} else {
			$kind='';
		}
	  echo  formpost($basepage.'?q=edits&sq=classes','_blank') . 
		formhiddenval('semid',$_POST['semid']) . formhiddenval('unitid',$discdept[$d]) . 
		formhiddenval('discid',$discid[$d]) . formhiddenval('act','Refresh') . 
		formsubmit('submit','go edit') . spanformat('',$disccolor[$d], $d . ' - ' . $disc[$d])  . $kind.'</form>'  ;
		//echo '</br>';
	}
	if($termid) {
		$q = "SELECT `disc`.`code` , `disc`.`name` , `disc`.`id` AS `discid` , `discdept`.`id` AS `discdeptid` , `kind`.`code` AS `kindcode`" .
		    "FROM `discipline` AS `disc` ,`coursedisciplines` AS `cd` ,`disciplinekind` AS `kind`,`unit` AS `discdept`" .
		    "WHERE `cd`.`course_id` = '".$courseid."' AND " . 
			"`disc`.`dept_id` = `discdept`.`id` AND " .
			"`cd`.`term_id` = '".$termid."' AND `cd`.`disciplinekind_id` = `kind`.`id` AND " . 
			"`cd`.`discipline_id` = `disc`.`id`; " ;
			//"AND (`kind`.`code` = 'OB' OR `kind`.`code` = 'AL');";
		$termsql = $mysqli->dbquery($q);
		$title = '<h5><b>Disciplina(s) não ofertada(s)</b></h5>';
		while ($termrow = $termsql->fetch_assoc()) {
			if(!$discflag[$termrow['code']]) {
				if($title) {
					echo $title;
					$title='';
				}
				echo  formpost($basepage.'?q=edits&sq=classes','_blank') . 
					formhiddenval('semid',$_POST['semid']) . formhiddenval('unitid',$termrow['discdeptid']) . 
					formhiddenval('discid',$termrow['discid']) . formhiddenval('act','Refresh') . 
					formsubmit('submit','go edit') . $termrow['code'].' - '.$termrow['name']  . '<sub>'.spanformat('smaller','',$termrow['kindcode']).'</sub>' . '</form>'  ;

			};
			//echo $termrow['code'].'&nbsp;&nbsp;';
		}
	}

}




	// function spanformat($style,$text) {
		// return '<span style="'.$style.'">' . $text . '</span>';
	// }

	function spanformat($size,$color,$text,$bgcolor=null) {
		$style='';
		if($size) {$style .= 'font-size:'.$size.';';}
		if($color) {$style .= 'color:'.$color.';';}
		if($bgcolor) {$style .= 'background:'.$bgcolor.';';}
		return '<span style="'.$style.'">' . $text . '</span>';
	}
	
	function pagereload($page) {
		return "<script type=\"text/javascript\">
			setInterval('location.replace(\"".$page."\")', 100);
			</script>";
	}

	function formpost($action,$target=null) {
		if($target) {$target = ' target="'.$target.'"';};
		return '<form method="post" enctype="multipart/form-data" action="' . $action . '"'.$target.'>';
	}

	function formpatterninput($max,$size,$pattern,$title,$fieldname,$fieldval) {
		$_SESSION['org'][$fieldname]=$fieldval;
		return '<input type="text" maxlength="'.$max.'" size="'.$size.'" pattern="'.$pattern.'" title="'.$title.'" name="'.$fieldname.'" value="'.htmlentities($fieldval,ENT_QUOTES).'"\>';
	}

	function formhiddenval($field,$val) {
		return "<input type='hidden' name='$field' id='$field' value='$val' />\n";
	}

	function formsubmit($field,$val) {
		return "<input type='submit' name='$field' value='$val' />\n";
	}


	function displaysqlitem($str,$sqltable,$sqlid,$sqlitem,$sqlitemB=null) {
		global $mysqli;
		if($sqlitemB) {$b=' , `'.$sqlitemB.'`';} else {$b='';};
		$q = "SELECT `".$sqlitem."`$b FROM `".$sqltable."` WHERE `id` = '" . $sqlid . "';";
		$result = $mysqli->dbquery($q);
		$sqlrow = $result->fetch_assoc();
		if($sqlitemB) {
			return $str . $sqlrow[$sqlitem] . ' -- ' . $sqlrow[$sqlitemB] .'   ';
		} else {
			return $str . $sqlrow[$sqlitem] . '   ';
		}
	}
	
	
	function fieldscompare($key,$fields) {
		foreach ($fields as $field) {
			if ($_POST[$key.$field] != $_SESSION['org'][$key.$field]) {return 1;}
		}
		return 0;
	}

	
	function formselectrange($selectname,$initial,$final,$refval,$trail=null,$disparray=null) {
		$_SESSION['org'][$selectname]=$refval;
		echo "<select name='".$selectname."'>";
		for ($i=$initial;$i<$final;$i++) {
			if ($i == $refval) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			if($disparray) {$val = $disparray[$i];} else {$val = $i;}
			echo "<option value='$i'$selected>".$val.$trail.'</option>';
		}	
		echo '</select>';
	}


	function formselectsession($selectname,$sessionkey,$refval,$nulloption=false) {
		$_SESSION['org'][$selectname]=$refval;
		echo "<select name='$selectname'>";
		if($nulloption) { echo "<option value='0'>--</option>";	}
		foreach ($_SESSION[$sessionkey] as $id => $val) {
			if ($id == $refval) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			echo "<option value='$id'$selected>".$val."</option>";
		}	
		echo '</select>';
	}


	function formselectsql(&$any,$q,$selectname,$refval,$idkey,$valAkey,$valBkey=null) {
		global $mysqli;
		
		$_SESSION['org'][$selectname]=$refval;
		$result = $mysqli->dbquery($q);
		echo "<select name='".$selectname."'>";
		echo "<option value='0'>---</option>";
		$any=0;
		while ($sqlrow = $result->fetch_assoc()) {
			$any=1;
			if ($sqlrow[$idkey] == $refval) {
				$selected = ' selected="selected"';
			} else {
				$selected = '';
			}
			if($valBkey) {
				$val = $sqlrow[$valAkey]. " - ".$sqlrow[$valBkey];
			} else {
				$val = $sqlrow[$valAkey];
			}
			echo "<option value='".$sqlrow[$idkey]."'$selected>".$val."</option>";
		}
		echo '</select>';
	}
	




?>


