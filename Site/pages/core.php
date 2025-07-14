<?php
date_default_timezone_set('America/Sao_Paulo');

$timestamp=time();
$timestamp=date('Y-m-d H:i:s',$timestamp);

list($microstamp,$sec) = explode(' ',microtime(false));
list($nothing,$microstamp) = explode('.',$microstamp);

$GBLdomainurl='https://www.ufrgs.br';
$GBLbaseurl=$GBLdomainurl.'/sisgen';
$GBLbasepage='/sisgen/';
$GBLdebug=true;

$sisgensetup=true; //to enable/disable 'initial' import/fix pages (admin)
$sisgenfullsetup=false; // this disable the "initial data imports"
$sisgenDBsetupHacks=false; // this disable whatever "DB import hack"
$sisgenimportCSV=true; //'new' simple way, direct from CSV file...


// some handy/aux values
	$GBLcommentcolor='teal';
	$GBLcommentpattern='[a-zA-Z0-9à-äè-ëì-ïò-öù-üÀ-ÄÈ-ËÌ-ÏÒ-ÖÙ-ÜçÇ :\*\(\)\.\-\+]+';
	$GBLdiscpattern='[a-zA-Z0-9à-äè-ëì-ïò-öù-üÀ-ÄÈ-ËÌ-ÏÒ-ÖÙ-ÜçÇ \(\)\-]+';
	$GBLnamepattern='[a-zA-Z0-9à-äè-ëì-ïò-öù-üÀ-ÄÈ-ËÌ-ÏÒ-ÖÙ-ÜçÇ \'\-\.@_]+';
	$GBLpasswdpattern='(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$';
	$GBLclasspattern = '[A-Z][A-Za-z0-9\*\-\+@]*';

// $pattern = '[a-zA-Z0-9 :\+\-\.\(\)]+';

	
	$GBLspc='&nbsp;&nbsp;';
	$GBLDspc=$GBLspc.$GBLspc;
	$GBLhighlightstyle=' style="background-color:#E0FFE0;color:#8000B0;"';

include 'dbconnect.php';

$GBLmysqli = myconnect();


function mymail($email,$subject,$msg) {
    $msg .= "\n\nAtt.\n sisgen\n$GBLbaseurl";
	$mailheaders  = 'MIME-Version: 1.0' . "\r\n";
	$mailheaders .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
	$mailheaders .= 'Content-Transfer-Encoding: base64' . "\r\n";
	$mailheaders .= 'From: sisgen@ufrgs.br' . "\r\n";
	$mailsubject .= '=?UTF-8?B?' . base64_encode("sisgen - $subject") . '?=';
    mail("$email",$mailsubject,base64_encode($msg),$mailheaders);
}

function myhtmlmail($from,$to,$subject,$msg) {
    
	$mailheaders  = 'MIME-Version: 1.0' . "\r\n";
	$mailheaders .= 'Content-Type: text/html; charset=utf-8' . "\r\n";
	$mailheaders .= 'Content-Transfer-Encoding: base64' . "\r\n";
	$mailheaders .= 'From: ' . $from . "\r\n";
	$mailheaders .= 'Cc: ' . $from . "\r\n";
	$mailsubject .= '=?UTF-8?B?' . base64_encode("sisgen - $subject") . '?=';
    mail($to,$mailsubject,base64_encode($msg),$mailheaders);
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

function vardebug($var,$name=null) {
	global $GBLdebug;
	if($GBLdebug) {
		echo '<pre>';
                if($name){echo $name.': ';}
		var_dump($var);
		echo '</pre>';
	}
}

function regacc_create() {
    global $GBLmysqli;
    global $regpage;
    global $GBLbaseurl;
    global $microstamp;
    

    $email=$_POST['emailA'];
    $passwd=$_POST['passA'];
    $emailhash=md5($email);
    $today = date('Y-m-d');

    $email = $GBLmysqli->real_escape_string($email);
    $passwd = $GBLmysqli->real_escape_string($passwd);
    list($usrname,$usrdomain) = explode('@',$email,2);

    
    if (!($stmt = $GBLmysqli->prepare("SELECT email,password,activ FROM `account` WHERE `email` = ?;"))) {
		echo 'Prepare failed: (' . $GBLmysqli->errno . ') ' . $GBLmysqli->error;
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
				"${GBLbaseurl}?st=validate&h=$emailhash\n\n";
			mymail($email,'Confirmação de Email',$msg);
		}
		$stmt->close();
    } else {
		$stmt->close();

		$sql = "INSERT INTO `account` (`email`, `password`, `name` , `displayname` , `valhash`) VALUES ('$email','$passwd','$email','$usrname','$emailhash');";
		$result=$GBLmysqli->dbquery($sql);

		echo '<h4>Obrigado por criar uma conta.</h4><br>
Você estará recebendo, em breve, um Email com instruções para ativar a sua conta.<br>';

		$msg = "Obrigado por criar uma conta. \nPor favor, acesse o link abaixo para ativar a mesma\n\n".
   	       "${GBLbaseurl}?st=validate&h=$emailhash\n\nAtt. sisgen";
		mymail($email,'Confirmação de Email',$msg);
		$msg ="P/Registro:\n\nConta registrada: $email\n";
		mymail('alceu.frigeri@ufrgs.br','Conta Nova - sisgen',$msg);

    } 
}


function regacc_validate($gethash) {
    global $GBLmysqli;
    global $regpage;
    
    echo '<h2>Confirmação de Email</h2><hr>';
    $sql = "SELECT * FROM `account` WHERE `account`.`valhash` = '$gethash'";
	$result = $GBLmysqli->dbquery($sql);
    if ($result->num_rows) {
		echo 'Obrigado por confirmar seu Email<br>';
		$sql ="UPDATE `sisgen`.`account` SET `activ` = '1' WHERE `account`.`valhash` = '$gethash'";
		$result = $GBLmysqli->dbquery($sql);
		echo 'Agora você já pode se logar no sistema !<br>';
	} else {
		echo '<b>'.spanformat('','red','Link Inválido ou Expirado').'</b><br>';
	}
}


function getacc_byemail($email) {
    global $GBLmysqli;
	
    $email = $GBLmysqli->real_escape_string($email);
	$q = "SELECT * FROM `account` WHERE account.email = '".$email."'";
	if(!($result = $GBLmysqli->dbquery($q))) {
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
    global $GBLbaseurl;
    $accdt = getacc_byemail($_POST['emailA']);
    if ($accdt && !$accdt['activ']) {
	$msg = "
Obrigado por criar uma conta.
Por favor, acesse o link abaixo para ativar a mesma

      ${GBLbaseurl}?st=validade&h=$accdt[valhash]

Att.
sisgen";
	mymail($email,'Confirmação de Email',$msg);
    }
}


function duplicatecourse($courseid,$acronym,$code,$name,$comment) {
	global $GBLmysqli;
	
	$q = "INSERT INTO `unit` (`acronym`,`code`,`name`,`iscourse`,`isdept`) VALUES ('".$acronym."','".$code."','".$name."','1','1')";
	$result = $GBLmysqli->dbquery($q);
	$newid = $GBLmysqli->insert_id;
	$q = "INSERT INTO `coursedisciplines` (`course_id`,`term_id`,`discipline_id`,`disciplinekind_id`) SELECT '".$newid."' , `cd`.`term_id` , `cd`.`discipline_id`  , `cd`.`disciplinekind_id` FROM `coursedisciplines` AS `cd` WHERE `course_id` = '".$courseid."'";
	$GBLmysqli->dbquery($q);
	$q= "SELECT `id` FROM `status` WHERE `status` = 'dup';";
	$result = $GBLmysqli->dbquery($q);
	$strow=$result->fetch_assoc();
	$q = "INSERT INTO `vacancies` (`course_id`,`class_id`,`askednum`,`askedreservnum`,`givennum`,`givenreservnum`,`comment`,`askedstatus_id`,`givenstatus_id`) SELECT '".$newid."' , `vc`.`class_id` , `vc`.`askednum`  , `vc`.`askedreservnum`  , `vc`.`givennum` , `vc`.`givenreservnum` ,  '".$comment."' , '".$strow['id']."' , '".$strow['id']."' FROM `vacancies` AS `vc` WHERE `course_id` = '".$courseid."'";
	$GBLmysqli->dbquery($q);
}



function duplicatesem($currsemid,$newsemname) {
	global $GBLmysqli;
	
	$newsem = $GBLmysqli->real_escape_string($newsemname);
	$q = "SELECT * FROM semester WHERE `name` = '" . $newsem . "';";
	$result = $GBLmysqli->dbquery($q);
	if ($sqlrow = $result->fetch_assoc()) {
		echo "ERR: semestre já existente ! </br>";
	} else {
		$q = "INSERT INTO `semester` (`name`) VALUES ('" . $newsem . "');";
		$GBLmysqli->dbquery($q);
		$newsemid = $GBLmysqli->insert_id;


		$qnewclass = "INSERT INTO `class` (`name`,`agreg`,`partof`,`sem_id`,`discipline_id`,`scenery`) " . 
				"SELECT `cl`.`name`, `cl`.`agreg` , `cl`.`partof`, '".$newsemid."' , `cl`.`discipline_id` , `cl`.`scenery` " . 
				"FROM `class` AS `cl` WHERE  `cl`.`sem_id` = '".$currsemid."';";
		//echo "<br> $qnewclass";
		$GBLmysqli->dbquery($qnewclass);

		$qsegment = "INSERT INTO `classsegment` (`class_id`,`day`,`start`,`length`,`room_id`,`prof_id`) " . 
				"SELECT `new`.`id` , `cs`.`day` , `cs`.`start` , `cs`.`length` , `cs`.`room_id` , `cs`.`prof_id` " . 
				"FROM `class` AS `org`, `class` AS `new` , `classsegment` AS `cs` " . 
				"WHERE `cs`.`class_id` = `org`.`id` AND `org`.`sem_id` = '".$currsemid."' AND `new`.`name` = `org`.`name` AND `new`.`discipline_id` = `org`.`discipline_id` AND `new`.`sem_id` = '".$newsemid."';";
		//echo "<br> $qsegment";
		$GBLmysqli->dbquery($qsegment);


		$qvacancy = "INSERT INTO `vacancies` (`class_id`,`course_id`,`askednum`,`askedreservnum`,`givennum`,`givenreservnum`,`usednum`,`usedreservnum`) " . 
				"SELECT `new`.`id` , `vc`.`course_id` , `vc`.`askednum` , `vc`.`askedreservnum` , `vc`.`givennum` , `vc`.`givenreservnum`, `vc`.`usednum` , `vc`.`usedreservnum`  " .
				"FROM `class` AS `org`, `class` AS `new` , `vacancies` AS `vc` " . 
				"WHERE `vc`.`class_id` = `org`.`id` AND `org`.`sem_id` = '".$currsemid."' AND `new`.`name` = `org`.`name` AND `new`.`discipline_id` = `org`.`discipline_id` AND `new`.`sem_id` = '".$newsemid."';";
		//echo "<br> $qvacancy";
		$GBLmysqli->dbquery($qvacancy);
		
		$qscenery = "INSERT INTO `sceneryclass` (`class_id`,`scenery_id`) " . 
				"SELECT `new`.`id` , `sc`.`scenery_id` " .
				"FROM `class` AS `org`, `class` AS `new` , `sceneryclass` AS `sc` " . 
				"WHERE `sc`.`class_id` = `org`.`id` AND `org`.`sem_id` = '".$currsemid."' AND `new`.`name` = `org`.`name` AND `new`.`discipline_id` = `org`.`discipline_id` AND `new`.`sem_id` = '".$newsemid."';";
		//echo "<br> $qscenery";
		$GBLmysqli->dbquery($qscenery);
				

	
	}
	
}




///// other 'help' functions


function checkweek($q,$qscen=null,$courseid=null,$termid=null) {
	global $GBLmysqli;
	$flag=array();

	$result = $GBLmysqli->dbquery($q);
	while ($sqlrow = $result->fetch_assoc()) {
		$disccodes[$sqlrow['code']] = $sqlrow['code'];
		$disc[$sqlrow['code']] = $sqlrow['discname'];

		if (!$vac[$sqlrow['code'] . ' - ' . $sqlrow['name']]) {
			if($courseid) {
				$q = "SELECT `askednum` AS `totalA` , `askedreservnum`  AS `totalB` FROM `vacancies` WHERE `class_id` = '".$sqlrow['classid']."' AND `course_id` = '".$courseid."';";
			} else {
				$q = "SELECT SUM(givennum) AS `totalA` , SUM(givenreservnum) AS `totalB` FROM `vacancies` WHERE `class_id` = '".$sqlrow['classid']."';";
			}
			$vacresult = $GBLmysqli->dbquery($q);
			$vacrow = $vacresult->fetch_assoc();
			$vac[$sqlrow['code'] . ' - ' . $sqlrow['name']] = $vacrow['totalA'] + $vacrow['totalB'];
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
		$termsql = $GBLmysqli->dbquery($q);
		while ($termrow = $termsql->fetch_assoc()) {
			if(!$discflag[$termrow['code']]) {$flag['ob']=1;};
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





function dbweekmatrix($q,$qscen=null,$courseid=null,$termid=null,$edit=true,$matrixonly=false,$courseHL=null) {
	global $GBLmysqli;
	
//	$basevals = array('DA','A0','68','40','00');
//	$basevals = array('D8','A0','80','50','00');
//	$basevals = array('E6','9B','5A','00');
	$basevals = array('C8','90','5A','00');
	$numcolors=0;
	  foreach ($basevals as $red) {
		  foreach ($basevals as $green) {
			  foreach ($basevals as $blue) {
				  $colors[$numcolors] = '#'.$red.$green.$blue;
				  $numcolors++;				  
//				  echo spanformat('smaller','#'.$red.$green.$blue,'<b>#'.$red.$green.$blue.'</b>&nbsp;');
			  }
//			  echo '<br>';
		  }
//		  echo '<br>';
	  }


	$hiddenclasskeys=null;
	$hiddenprofdeptid=null;
	$result = $GBLmysqli->dbquery($q);
	while ($sqlrow = $result->fetch_assoc()) {
		$courseHLquery = $sqlrow['courseid'];
		$disccodes[$sqlrow['code']] = $sqlrow['code'];
		$disc[$sqlrow['code']] = $sqlrow['discname'];
		$discid[$sqlrow['code']] = $sqlrow['discid'];
		$discbgcolor[$sqlrow['code']] = 0x0;
		$profnicks=0;

		if (!isset($scen[$sqlrow['classid']])) {
			if($sqlrow['scenery']) {
				if(isset($qscen)) {
					$qx = "SELECT `scen`.`id`, `scen`.`name` FROM `sceneryclass` AS `sc` , `scenery` AS `scen` WHERE `sc`.`class_id` = '".$sqlrow['classid']."' AND `sc`.`scenery_id` IN (".$qscen.") AND `sc`.`scenery_id` = `scen`.`id`;";
					$qxresult = $GBLmysqli->dbquery($qx);
					while ($qxrow = $qxresult->fetch_assoc()) {
						$scen[$sqlrow['classid']] .= $qxrow['name'];
					}
				} else {
					$scen[$sqlrow['classid']]='';					
				}
			} else {
				$scen[$sqlrow['classid']]='';
			}
		}
		
		$classindex=$sqlrow['code'] . ' - ' . $sqlrow['name'];

		if (!$vac[$classindex]) {
			if($courseid) {
				$q = "SELECT `askednum` AS `totalA` , `askedreservnum`  AS `totalB` FROM `vacancies` WHERE `class_id` = '".$sqlrow['classid']."' AND `course_id` = '".$courseid."';";
			} else {
				$q = "SELECT SUM(givennum) AS `totalA` , SUM(givenreservnum) AS `totalB` FROM `vacancies` WHERE `class_id` = '".$sqlrow['classid']."';";
			}
			$vacresult = $GBLmysqli->dbquery($q);
			$vacrow = $vacresult->fetch_assoc();
			$vac[$classindex] = $vacrow['totalA'] + $vacrow['totalB'];	;	
		}
		if(!$vacHL[$classindex] && $courseHL) {
			$q = "SELECT (`askednum` + `askedreservnum` + `givennum` + `givenreservnum`) AS `total` FROM `vacancies` WHERE `class_id` = '".$sqlrow['classid']."' AND `course_id` = '".$courseHL."';";
			$vacresult = $GBLmysqli->dbquery($q);
			$vacrow = $vacresult->fetch_assoc();
			$vacHL[$classindex] = $vacrow['total'];	
		}
		
		if ($vac[$classindex]) {
				if($sqlrow['disckind']) {$kind=' ('.$sqlrow['disckind'].') ';} else {$kind='';};
				$start=$sqlrow['start'];
				$day=$sqlrow['day'];
				
				$classhiddenkey = hiddenclasskey($_POST['semid'],$sqlrow['discdeptid'],$sqlrow['discid'],$sqlrow['classid']) ;
				$hiddenclasskeys[$sqlrow['discdeptid']][$sqlrow['discid']][$sqlrow['classid']] = $classhiddenkey;
				
				$d  = $sqlrow['code'] . $kind . ' - ' . hiddenformlnk($classhiddenkey,$sqlrow['name']) . ' (' . $vac[$sqlrow['code'] . ' - ' . $sqlrow['name']] . ')';
				if($vacHL[$classindex]) {
					$d .= ' **';
				}
				
				if ($sqlrow['profnick']) {
					$profnicks=1;
					$hiddenprofdeptid[$sqlrow['profid']]=$sqlrow['profdeptid'];
					$d .= '<p style="margin:0;border:0;line-height:50%;"><sup>'. spanformat('75%','MidnightBlue',$scen[$sqlrow['classid']],null);
					$d .= hiddenformlnk(hiddenprofkey($_POST['semid'],$sqlrow['profdeptid'],$sqlrow['profid']) , spanformat('75%','red','('.$sqlrow['profnick'].')')) . '</sup></p>'; 
				} else {
					$d .= '<p style="margin:0;border:0;line-height:50%;"><sup>'. spanformat('75%','MidnightBlue',$scen[$sqlrow['classid']],null).'</sup></p>';
				} 
				$seg[$d] = $sqlrow['code'];
				$discflag[$sqlrow['code']] = 1;

			for ($i=0; $i < $sqlrow['length']; $i++) {
				$week[$day][$start+$i][] = $d;
				$discweek[$day][$start+$i][$sqlrow['code']] += 1;
			}
		}
		$discdept[$sqlrow['code']]=$sqlrow['discdeptid'];
		$discid[$sqlrow['code']]=$sqlrow['discid'];
	}
	$i=0;
	if($color) {
		foreach ($disccodes as $d) {
			$disccolor[$d] = $color;
		}
	} else {
		foreach ($disccodes as $d) {
			$disccolor[$d] = $colors[((((($discid[$d] * 13 ) % 97 ) * 1 ) % 83 ) * 3 ) % $numcolors];
		}
	}
	
	if($hiddenclasskeys){
		foreach ($hiddenclasskeys as $Hdeptid => $HdeptX) {
			foreach ($HdeptX as $Hdiscid => $HdiscX) {
				foreach ($HdiscX as $Hclassid => $HclassX) {
					if($courseHLquery) {
						$courseHL = $courseHLquery;
					}
					echo hiddenclassform($_POST['semid'],$Hdeptid,$Hdiscid,$Hclassid,'name',$profnicks,$courseHL);
				}
			}
		}
		
	}
	

	if($hiddenprofdeptid){
		foreach ($hiddenprofdeptid as $Hprofid => $Hdeptid) {
			echo hiddenprofform($_POST['semid'],$Hdeptid,$Hprofid);
		}
	}


	echo '<table>';
	echo '<tr style="border-bottom:1px solid black"><th>Hora</th>';
	for ($i=2; $i <8; $i++) {
		echo "<th style='width:155px'> " . $_SESSION['weekday'][$i] . '</th>';
	}
	echo '</tr>';
	for ($j=7;$j<22;$j++) {
		echo '<tr style="border-bottom:1px solid black"><td>' . $j . ':30&nbsp;&nbsp;</td>';
		for ($i=2;$i<8;$i++) {
			$td='<td>';
			if (count($discweek[$i][$j]) > 1) {
				$td='<td style="background:#FFF2F2;">';
				foreach ($discweek[$i][$j] as $xID => $xcnt) {
					$discbgcolor[$xID] |= 0xFF0000;
				}
			} else {
				if(max($discweek[$i][$j]) > 1) {
					$td='<td style="background:#F2FFF2;">';
					foreach ($discweek[$i][$j] as $xID => $xcnt) {
						$discbgcolor[$xID] |= 0x00FF00;
					}
				}
			}
			echo $td;
			foreach ($week[$i][$j] as $d) {
				echo '<p style="margin:0;border:0;">' . spanformat(null,$disccolor[$seg[$d]], '<b>'.$d.'</b>');
			}
			echo '</td>';
		}
		echo '</tr>';
	}
	echo '</table>';
	
	if($matrixonly) {}
	else {
		$hiddencoursekeys=null;
		foreach ($disccodes as $d) {
			if($courseid){
				$q = "SELECT `kind`.`code` FROM `disciplinekind` AS `kind` , `coursedisciplines` AS `cd` WHERE `cd`.`course_id` = '".$courseid."' AND `cd`.`discipline_id` = '".$discid[$d]."' AND `cd`.`disciplinekind_id`= `kind`.`id`;";
				$result = $GBLmysqli->dbquery($q);
				$sqlrow=$result->fetch_assoc();
				$kind = '<sub>'.spanformat('smaller','',$sqlrow['code']).'</sub>';
			} else {
				$kind=null;
				$q = "SELECT `kind`.`code` AS kcode , `course`.`acronym` AS acro, `course`.`id` AS courseid , `term`.`code`  AS tcode , `term`.`id`  AS termid ".
					"FROM `disciplinekind` AS `kind` , `coursedisciplines` AS `cd` , `unit` AS `course` , term " . 
					"WHERE  `cd`.`discipline_id` = '".$discid[$d]."' AND `cd`.`course_id`= `course`.`id` AND `cd`.`term_id`= `term`.`id`  AND `cd`.`disciplinekind_id`= `kind`.`id`;";
				$result = $GBLmysqli->dbquery($q);
				while ($sqlrow=$result->fetch_assoc()) {
					$hiddencoursekeys[$sqlrow['courseid']][$sqlrow['termid']] = hiddencoursekey($_POST['semid'],$sqlrow['courseid'],$sqlrow['termid']);
					if (($sqlrow['kcode'] == 'OB') || ($sqlrow['kcode'] == 'AL')) {$bold = true;$tcolor='#0000A0';} else {$bold=false;$tcolor=null;}
					$kind .= hiddenformlnk($hiddencoursekeys[$sqlrow['courseid']][$sqlrow['termid']] , spanformat (null,$tcolor,' ' . $sqlrow['acro'] . ' - ' . $sqlrow['tcode'] . spanformat('smaller',null,'('.$sqlrow['kcode'] .')') . '&nbsp;&nbsp; ',null,$bold));
				}
				if ($kind) {
					$kind = '<sub>'.spanformat('smaller','',$kind).'</sub>';
				}
			}
			if($discbgcolor[$d]) {
				$bgcolor = '#' . dechex($discbgcolor[$d] | 0xECECE8);
			} else {
				$bgcolor = null;
			};

			if ($edit) {
				echo hiddendiscform($_POST['semid'],$discdept[$d],$discid[$d],$profnicks,$courseHL,'') . formsubmit('submit','go edit');			
				echo	spanformat('',$disccolor[$d], $d . ' - ' . $disc[$d] ,$bgcolor,true) . $kind;
				echo  '</form>'   ;
			} else {
				echo	spanformat('',$disccolor[$d], $d . ' - ' . $disc[$d] ,$bgcolor,true) . $kind . '<br>';
			}
			
		}
		if ($hiddencoursekeys) {
			foreach ($hiddencoursekeys as $cid => $acid) {
				foreach ($acid as $tid => $atid) {
					echo hiddencourseform($_POST['semid'],$cid,$tid);
				}
			}
		}
		if($termid) {
			$q = "SELECT `disc`.`code` , `disc`.`name` , `disc`.`id` AS `discid` , `discdept`.`id` AS `discdeptid` , `kind`.`code` AS `kindcode`" .
				"FROM `discipline` AS `disc` ,`coursedisciplines` AS `cd` ,`disciplinekind` AS `kind`,`unit` AS `discdept`" .
				"WHERE `cd`.`course_id` = '".$courseid."' AND " . 
				"`disc`.`dept_id` = `discdept`.`id` AND " .
				"`cd`.`term_id` = '".$termid."' AND `cd`.`disciplinekind_id` = `kind`.`id` AND " . 
				"`cd`.`discipline_id` = `disc`.`id`; " ;
//				"AND (`kind`.`code` = 'OB' OR `kind`.`code` = 'AL');";
			$termsql = $GBLmysqli->dbquery($q);
			$title = '<h5><b>Disciplina(s) não ofertada(s)</b></h5>';
			while ($termrow = $termsql->fetch_assoc()) {
				if(!$discflag[$termrow['code']]) {
					if($title) {
						echo $title;
						$title='';
					}
					echo hiddendiscform($_POST['semid'],$termrow['discdeptid'],$termrow['discid'],$profnicks,$courseHL,null) . 
						formsubmit('submit','go edit') . $termrow['code'].' - '.$termrow['name']  . '<sub>'.spanformat('smaller','',$termrow['kindcode']).'</sub>' . '</form>'  ;
				};
			}
		}
	}
}



	function spanformat($size,$color,$text,$bgcolor=null,$bold=null,$height=null) {
		$style='';
		if($size) {$style .= 'font-size:'.$size.';';}
		if($color) {$style .= 'color:'.$color.';';}
		if($bgcolor) {$style .= 'background:'.$bgcolor.';';}
		if($bold) {$style .= 'font-weight:bold;';}
		if($height) {$style .= 'line-height:'.$height.';';}
		return '<span style="'.$style.'">' . $text . '</span>';
	}
	
	function pagereload($page) {
		return "<script type=\"text/javascript\">
			setInterval('location.replace(\"".$page."\")', 250);
			</script>";
	}

	function formpost($action,$target=null,$formname=null) {
		if($target) {$target = ' target="'.$target.'"';};
		if($formname) {$formname = ' name="'.$formname.'"';};
		return '<form method="post" enctype="multipart/form-data" action="' . $action . '"'.$target . $formname . '>';
	}
	
	
	function hiddenformlnk($formkey,$textlink) {
		return '<a href="javascript:document.forms['."'". $formkey .  "'" . '].submit()">' . $textlink .'</a>';
	}
	
	function hiddenprofform($semid,$deptid,$profid,$closing='</form>') {
		global $GBLbasepage;
		$lnk = join('_',array('profhid',$semid,$deptid,$profid));
		return formpost($GBLbasepage.'?q=reports&sq=prof', $lnk, $lnk ) . 
			formhiddenval('semid',$semid) . formhiddenval('deptid',$deptid) . 
			formhiddenval('profid',$profid) . formhiddenval('act','Refresh') . $closing;
	}
	function hiddenprofkey($semid,$deptid,$profid) {
		return join('_',array('profhid',$semid,$deptid,$profid));
	}
	
	function hiddenroomform($semid,$buildingid,$roomid,$closing='</form>') {
		global $GBLbasepage;
		$lnk = join('_',array('roomhid',$semid,$buildingid,$roomid));
		return formpost($GBLbasepage.'?q=reports&sq=room', $lnk, $lnk ) . 
			formhiddenval('semid',$semid) . formhiddenval('buildingid',$buildingid) . 
			formhiddenval('roomid',$roomid) . formhiddenval('act','Refresh') . $closing;
	}
	function hiddenroomkey($semid,$buildingid,$roomid) {
		return join('_',array('roomhid',$semid,$buildingid,$roomid));
	}

	function hiddencourseform($semid,$courseid,$termid,$closing='</form>') {
		global $GBLbasepage;
		$lnk = join('_',array('coursehid',$semid,$courseid,$termid));
		return formpost($GBLbasepage.'?q=reports&sq=course', $lnk, $lnk ) . 
			formhiddenval('semid',$semid) . formhiddenval('courseid',$courseid) . 
			formhiddenval('termid',$termid) . formhiddenval('act','Refresh') . $closing;
	}
	function hiddencoursekey($semid,$courseid,$termid) {
		return join('_',array('coursehid',$semid,$courseid,$termid));
	}
	
	function hiddendiscform($semid,$deptid,$discid,$profnicks='0',$courseHL='',$closing='</form>') {
		global $GBLbasepage;
		$lnk = join('_',array('dischid',$semid,$deptid,$discid));
		return formpost($GBLbasepage.'?q=edits&sq=classes', $lnk, $lnk ) . 
			formhiddenval('semid',$semid) . formhiddenval('unitid',$deptid) . 
			formhiddenval('discid',$discid) . 
			formhiddenval('profnicks',$profnicks) . 
			formhiddenval('courseHL',$courseHL) . 
			formhiddenval('act','Refresh') . $closing;
		}
	function hiddendisckey($semid,$deptid,$discid) {
		return join('_',array('dischid',$semid,$deptid,$discid));
	}
	
	function hiddenclassform($semid,$deptid,$discid,$classid,$classname,$profnicks='0',$courseHL='',$closing='</form>') {
		global $GBLbasepage;
		$pagelnk = join('_',array('dischid',$semid,$deptid,$discid));
		$formlnk = join('_',array('classhid',$semid,$deptid,$discid,$classid));
		return formpost($GBLbasepage.'?q=edits&sq=classes#class'.$classid.'div', $pagelnk, $formlnk ) . 
			formhiddenval('semid',$semid) . formhiddenval('unitid',$deptid) . 
			formhiddenval('discid',$discid) . 
			formhiddenval('classid',$classid) . formhiddenval('classname',$classname) . 
			formhiddenval('profnicks',$profnicks) . 
			formhiddenval('courseHL',$courseHL) . 
			formhiddenval('act','Edit') . $closing;
		}
	function hiddenclasskey($semid,$deptid,$discid,$classid) {
		return join('_',array('classhid',$semid,$deptid,$discid,$classid));
	}		
		
	function iddivkey($key,$val) {
		return ' id="' . $key . $val . 'div"';
	}
	
	function hiddendivkey($key,$val) {
		return '<div id="' . $key . $val . 'div">&nbsp;</div><br><br>';
	}
	
	function targetdivkey($key,$val) {
		return '#' . $key . $val . 'div';
	}

	function formpatterninput($max,$size,$pattern,$title,$fieldname,$fieldval) {
		$_SESSION['org'][$fieldname]=$fieldval;
		return '<input type="text" maxlength="'.$max.'" size="'.$size.'" pattern="'.$pattern.'" title="'.$title.'" name="'.$fieldname.'" value="'.htmlentities($fieldval,ENT_QUOTES).'"\>';
	}

	function formhiddenval($field,$val) {
		return "<input type='hidden' name='$field' id='$field' value='$val' />\n";
	}

        function formretainvalues($fields) {
                foreach ($fields as $field) {
                        if ($_POST[$field]) {
                                $_SESSION['retain'][$field] = $_POST[$field];
                        } elseif ($_SESSION['retain'][$field]) {
                                $_POST[$field] = $_SESSION['retain'][$field];
                        }
                }
        }
                

	function formsubmit($field,$val) {
		return "<input type='submit' name='$field' value='$val' />\n";
	}


	function displaysqlitem($str,$sqltable,$sqlid,$sqlitem,$sqlitemB=null) {
		global $GBLmysqli;
		if($sqlitemB) {$b=' , `'.$sqlitemB.'`';} else {$b='';};
		$q = "SELECT `".$sqlitem."`$b FROM `".$sqltable."` WHERE `id` = '" . $sqlid . "';";
		$result = $GBLmysqli->dbquery($q);
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





// auxiliary scenery functions
function inscenery_sessionlst ($sessionlst) {
	$in = "0";
	foreach ($_SESSION[$sessionlst] as $scenid => $scenname) {
		$in .= " , '".$scenid."'";
	}
	return $in;
}	

function scenery_sql($inscenery) {
	global $GBLmysqli;
	
   if ($GBLmysqli->scenclass_test()) {
	   $tbl=' , `sceneryclass` ';
	   $sql=" AND ( (`class`.`scenery` = '0') OR " .
		 " (`class`.`scenery` = '1' AND `sceneryclass`.`class_id` = `class`.`id` AND `sceneryclass`.`scenery_id` IN (" .$inscenery. ")) ) "  ;
	   return (array($tbl,$sql));
   } else {
	   return (array(''," AND `class`.`scenery` = '0' " ));
   }
}




        function formsessionselectinit($fieldname,$fieldlist) {
		if ($_POST[$fieldname]) {
			unset($_SESSION[$fieldname]);
			foreach ($_SESSION[$fieldlist] as $selectid => $selectname) {
				if ($_POST[$fieldname.$selectid]) {
					$_SESSION[$fieldname][$selectid] = $selectname;
				}
			}	
		}
		echo formhiddenval($fieldname,'true');
        }





        function formsessionselect($session,$fieldname,&$cnt,$desc=null) {
		foreach ($session as $selectid => $selectname) {
			$checked='';
			$style='';
			if ($_SESSION[$fieldname][$selectid]) {
				$checked=' checked';
				$style=';background-color: lightgray';
			};
			$cnt++;
			if ($cnt == 7) {
				$cnt = 1;
				echo '</tr><tr>';
			}
                        if ($desc) {
                               echo '<td style="width:170px' . $style . '"><b>' . $selectname .':</b> '. $_SESSION[$desc][$selectid].'</td>';
                        } else {
        			echo '<th style="width:170px' . $style . '">' . 
                                        '<input type="checkbox" name="' . 
                                        $fieldname . $selectid . 
                                        '" value="' . $selectid . 
                                        '"' . $checked . 
                                        ' > <label for="' . 
                                        $fieldname . $selectid .
                                        '">' . 
                                        $selectname . 
                                        '</label></th>';
                        }

		}
        }

	
	function formsceneryselect() {
                formsessionselectinit('sceneryselected','scen.all');
                formsessionselectinit('sceneryroles','scen.editroles');
                		
		echo '<details>';
        		echo '<summary>&nbsp;&nbsp;&nbsp;<b>&rArr;</b> ';
        		displaysessionselected('Cenário(s)','sceneryselected');
        		echo '</summary>';
                		$cnt = 0;
                		echo '<table><tr>';
                                foreach ($_SESSION['sceneryroles'] as $roleid => $roledesc) {
                                        formsessionselect($_SESSION['scen.byroles'][$roleid],'sceneryselected',$cnt);
                                }
                		echo '</tr></table>';        
        
                        
        		echo '<details>';
                		echo '<summary>&nbsp;&nbsp;&nbsp;<b>&rArr;</b> Legenda: ';
                                echo '</summary>';
                        		$cnt = 0;
                        		echo '<table><tr>';
                                        foreach ($_SESSION['sceneryroles'] as $roleid => $roledesc) {
                                                formsessionselect($_SESSION['scen.byroles'][$roleid],'sceneryselected',$cnt,'scen.desc');
                                        }
                        		echo '</tr></table>';        
        		echo '</details>';       

        		echo '<details>';
        		echo '<summary>&nbsp;&nbsp;&nbsp;<b>&rArr;</b> ';
                        displaysessionselected('Perfil(is)','sceneryroles');
        		echo '</summary>';
                        		$cnt = 0;
                        		echo '<table><tr>';
                                        formsessionselect($_SESSION['scen.editroles'],'sceneryroles',$cnt);
                        		echo '</tr></table>';        
        		echo '</details>';       
                        
                        echo formsubmit('act','Refresh');
        	echo '</details>';
	}
	
	function displaysessionselected($label,$fieldname){
                echo $label.': ';
		$comma='';
		foreach ($_SESSION[$fieldname] as $id => $name) {
			echo $comma . '<b> ' . $name . '</b>';
			$comma = ', ';
		}
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


	function formselectsession($selectname,$sessionkey,$refval,$nulloption=false,$onchange=false) {
		$_SESSION['org'][$selectname]=$refval;
		if ($onchange) {
			echo "<select name='".$selectname."' onchange='this.form.submit(".$submit.")'>";
		} else {
			echo "<select name='".$selectname."'>";
		}
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


	function formselectsql(&$any,$q,$selectname,$refval,$idkey,$valAkey,$valBkey=null,$onchange=true) {
		global $GBLmysqli;
		
		$_SESSION['org'][$selectname]=$refval;
		$result = $GBLmysqli->dbquery($q);
		if ($onchange) {
			echo "<select name='".$selectname."' onchange='this.form.submit(".$submit.")'>";
			echo "<option value='0'>---</option>";
		} else {
			echo "<select name='".$selectname."'>";
			echo "<option value='0'>---</option>";
		};
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


	function highlightbegin() {
		global $GBLhighlightstyle;
		echo '<table '.$GBLhighlightstyle.'><tr><td>';
	}
	
	function highlightend() {
		echo '</td></tr></table>';
	}
	
	function formjavaprint($title) {

		echo 
			"<script type=\"text/javascript\">
				function printContent(id){
					str=document.getElementById(id).innerHTML
					newwin=window.open('','printwin','left=100,top=100,width=1100,height=1000')
					newwin.document.write('<HTML><HEAD>')
					newwin.document.write('<TITLE>" . $title . "</TITLE>')
					newwin.document.write('<script>')
					newwin.document.write('function chkstate(){')
					newwin.document.write('if(document.readyState==\"complete\"){')
					newwin.document.write('window.close()')
					newwin.document.write('}')
					newwin.document.write('else{')
					newwin.document.write('setTimeout(\"chkstate()\",2000)')
					newwin.document.write('}')
					newwin.document.write('}')
					newwin.document.write('function print_win(){')
					newwin.document.write('window.print();')
					newwin.document.write('chkstate();')
					newwin.document.write('}')
					newwin.document.write('<\/script>')
					newwin.document.write('</HEAD>')
					newwin.document.write('<BODY onload=\"print_win()\">')
					newwin.document.write(str)
					newwin.document.write('</BODY>')
					newwin.document.write('</HTML>')
					newwin.document.close()
				}
			</script>";
	}






?>


