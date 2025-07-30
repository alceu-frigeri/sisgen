<?php
date_default_timezone_set('America/Sao_Paulo');

$timestamp = time();
$timestamp = date('Y-m-d H:i:s' , $timestamp);

list($microstamp , $sec) = explode(' ' , microtime(false));
list($nothing , $microstamp) = explode('.' , $microstamp);

$GBLdomainurl = 'https://www.ufrgs.br';
$GBLbaseurl = $GBLdomainurl.'/sisgen';
$GBLbasepage = '/sisgen/';
$GBLdebug = true;

$GBLtimeout = 2400 ; //if last check was 'that long ago', auto-logout
$GBLgracetime = 800; //if last check was over that (and less than timeout), auto-renew

// 
$sisgensetup = true; //to enable/disable 'initial' import/fix pages (admin)
$sisgenfullsetup = false; // this disable the "initial data imports"
$sisgenDBsetupHacks = false; // this disable whatever "DB import hack"
$sisgenimportCSV = true; //'new' simple way, direct from CSV file...


// some handy/aux values
$GBLcommentcolor = 'teal';
$GBLcommentpattern = '[a-zA-Z0-9à-äè-ëì-ïò-öù-üÀ-ÄÈ-ËÌ-ÏÒ-ÖÙ-ÜçÇ :\*\(\)\.\-\+]+';
$GBLdiscpattern = '[a-zA-Z0-9à-äè-ëì-ïò-öù-üÀ-ÄÈ-ËÌ-ÏÒ-ÖÙ-ÜçÇ \(\)\-]+';
$GBLnamepattern = '[a-zA-Z0-9à-äè-ëì-ïò-öù-üÀ-ÄÈ-ËÌ-ÏÒ-ÖÙ-ÜçÇ \'\-\.@_]+';
$GBLpasswdpattern = '(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$';
$GBLclasspattern = '[A-Z][A-Za-z0-9\*\-\+@]*';

// $pattern = '[a-zA-Z0-9 :\+\-\.\(\)]+';

  
$GBL_Dspc = '&nbsp;&nbsp;';
$GBL_Tspc = '&nbsp;&nbsp;&nbsp;&nbsp;';
$GBL_Qspc = '&nbsp;&nbsp;&nbsp;&nbsp;';
$GBLhighlightstyle = ' style="background-color:#E0FFE0;color:#8000B0;"';


include 'menu.php';

if(!$_SESSION['pagelnk']) {
        foreach ($menu as $key => $value) {
                if($value['id']) {
                        $_SESSION['pagelnk'][$value['id']] = $GBLbasepage .  "?q=${key}";
                }
                if($value['hasChildren']) {
                        foreach ($value['children'] as $ckey => $cvalue) {
                                if($cvalue['id']) {
                                        $_SESSION['pagelnk'][$cvalue['id']] = $GBLbasepage .  "?q=${key}&sq=${ckey}";
                                }
                        }
                }
        }
}

include 'dbconnect.php';

$GBLmysqli = myconnect();

////
////

function mymail($email , $subject , $msg) {
    $msg .= "\n\nAtt.\n sisgen\n$GBLbaseurl";
    $mailheaders  = 'MIME-Version: 1.0' . "\r\n";
    $mailheaders .= 'Content-Type: text/plain; charset=utf-8' . "\r\n";
    $mailheaders .= 'Content-Transfer-Encoding: base64' . "\r\n";
    $mailheaders .= 'From: sisgen@ufrgs.br' . "\r\n";
    $mailsubject .= '=?UTF-8?B?' . base64_encode("sisgen - $subject") . '?=';
    mail("$email" , $mailsubject , base64_encode($msg) , $mailheaders);
}

function myhtmlmail($from , $to , $subject , $msg) {
    
    $mailheaders  = 'MIME-Version: 1.0' . "\r\n";
    $mailheaders .= 'Content-Type: text/html; charset=utf-8' . "\r\n";
    $mailheaders .= 'Content-Transfer-Encoding: base64' . "\r\n";
    $mailheaders .= 'From: ' . $from . "\r\n";
    $mailheaders .= 'Cc: ' . $from . "\r\n";
    $mailsubject .= '=?UTF-8?B?' . base64_encode("sisgen - $subject") . '?=';
    mail($to , $mailsubject , base64_encode($msg) , $mailheaders);
}



function writeLogFile($msg) { 
    if (!$handle = @fopen('log.txt', 'a')) {
        echo "<br>ERR opening LOG file !!!</br>\n";
        exit;
    } else {
        if (@fwrite($handle , "$msg\r\n") === FALSE) {
            echo "<br>ERR writing to LOG file !!!</br>\n";
            exit;
        }
        @fclose($handle);
    }
}

function vardebug($var , $name = null) {
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
    
    $GBLmysqli->postsanitize();

    $emailhash = md5($_POST['emailA']);
    $today = date('Y-m-d');

    $Query = "SELECT email , password , activ FROM `account` WHERE `email` = '$_POST[emailA]' ; " ;
    $result = $GBLmysqli->dbquery($Query) ;
   

    if ($sqlrow = $result->fetch_assoc()) {
        if($sqlrow['activ']) {
            echo '<h3>email já cadastrado!</h3> Acabamos de lhe re-enviar um Email com a sua senha de acesso.<br>';
            mymail($sqlrow['email'] , 'Senha de Acesso' , "Prezado(a)\n Sua senha é: $sqlrow[password]");
        } else {
            echo '<h3>email já cadastrado!</h3> Acabamos de lhe re-enviar um Email de ativação da sua conta.<br>';
            $msg = "Obrigado por criar uma conta. \nPor favor, acesse o link abaixo para ativar a mesma\n\n".
                "${GBLbaseurl}?st=validate&h=$emailhash\n\n";
            mymail($sqlrow['email'] , 'Confirmação de Email' , $msg);
        }
        $result->close();
    } else {
        $result->close();

        $sql = 
                "INSERT INTO `account` (`email` , `password` , `name` , `displayname` , `valhash`) " .
                "VALUES ( '$_POST[emailA]' , '$_POST[passA]' , '$_POST[name]  $_POST[familyname]' , '$_POST[name]' , '$emailhash' ) ; " ;
        $result = $GBLmysqli->dbquery($sql);

        echo '<h4>Obrigado por criar uma conta.</h4><br>
Você estará recebendo, em breve, um Email com instruções para ativar a sua conta.<br>';

        $msg = "Obrigado por criar uma conta. \nPor favor, acesse o link abaixo para ativar a mesma\n\n".
            "${GBLbaseurl}?st=validate&h=$emailhash\n\nAtt. sisgen";
        mymail($_POST['emailA'] , 'Confirmação de Email' , $msg);
        
        $msg  = "P/Registro:\n\nConta registrada: $email\n";
        mymail('alceu.frigeri@ufrgs.br' , 'Conta Nova - sisgen' , $msg);

    } 
}


function regacc_validate($gethash) {
    global $GBLmysqli;
    global $regpage;
    
    echo '<h2>Confirmação de Email</h2><hr>';
    $sql = 
        "SELECT * " . 
        "FROM `account` " . 
        "WHERE `account`.`valhash` = '$gethash' ; " ;
    $result = $GBLmysqli->dbquery($sql);
    if ($result->num_rows) {
        echo 'Obrigado por confirmar seu Email<br>';
        $sql  = 
                "UPDATE `sisgen`.`account` " .
                "SET `activ` = '1' " .
                "WHERE `account`.`valhash` = '$gethash' ; " ;
        $result = $GBLmysqli->dbquery($sql);
        echo 'Agora você já pode se logar no sistema !<br>';
    } else {
        echo '<b>' . spanformat('' , 'red' , 'Link Inválido ou Expirado') . '</b><br>';
    }
}


function getacc_byemail($email) {
    global $GBLmysqli;
  
    $email = $GBLmysqli->real_escape_string($email);
    $Query = 
        "SELECT * " . 
        "FROM `account` " . 
        "WHERE account.email = '$email' ; " ;
    if(!($result = $GBLmysqli->dbquery($Query))) {
        return NULL;
    }
    return $result->fetch_assoc();
}



function regacc_passrecovery() {
    $accdt = getacc_byemail($_POST['emailA']);
    if ($accdt && $accdt['activ']) {
        mymail($accdt['email'] , 'Senha de Acesso' , "Prezado(a)\n Sua senha é: $accdt[password]");
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
        mymail($email , 'Confirmação de Email' , $msg);
    }
}


function duplicatecourse($courseid , $acronym , $code , $name , $comment) {
    global $GBLmysqli;
  
    $Query = 
        "INSERT INTO `unit` (`acronym` , `code` , `name` , `iscourse` , `isdept`) " . 
        "VALUES ('$acronym' , '$code' , '$name' , '1' , '1') ; " ;
    $result = $GBLmysqli->dbquery($Query);
    $newid = $GBLmysqli->insert_id;
  
    $Query = 
        "INSERT INTO `coursedisciplines` (`course_id` , `term_id` , `discipline_id` , `disciplinekind_id`) " . 
                "SELECT '$newid' , `cd`.`term_id` , `cd`.`discipline_id`  , `cd`.`disciplinekind_id` " . 
                "FROM `coursedisciplines` AS `cd` " . 
                "WHERE `course_id` = '$courseid' ; " ;
    $GBLmysqli->dbquery($Query);
  
    $Query = 
        "SELECT `id` " . 
        "FROM `status` " . 
        "WHERE `status` = 'dup' ; " ;
    $result = $GBLmysqli->dbquery($Query);
    $strow = $result->fetch_assoc();
  
    $Query = 
        "INSERT INTO `vacancies` (`course_id` , `class_id` , `askednum` , `askedreservnum` , `givennum` , `givenreservnum` , `comment` , `askedstatus_id` , `givenstatus_id`) " . 
                "SELECT '$newid' , `vc`.`class_id` , `vc`.`askednum`  , `vc`.`askedreservnum`  , `vc`.`givennum` , `vc`.`givenreservnum` ,  '$comment' , '$strow[id]' , '$strow[id]' " . 
                "FROM `vacancies` AS `vc` " . 
                "WHERE `course_id` = '$courseid' ; " ;
    $GBLmysqli->dbquery($Query);
}



function duplicatesem($currsemid , $newsemname) {
    global $GBLmysqli;
  
    $newsem = $GBLmysqli->real_escape_string($newsemname);
  
    $q = 
        "SELECT * " .
        "FROM semester " .
        "WHERE `name` = '$newsem' ; " ;
        
    $result = $GBLmysqli->dbquery($q);
    if ($sqlrow = $result->fetch_assoc()) {
        echo "ERR: semestre já existente ! </br>";
    } else {
  
        $q = 
            "INSERT INTO `semester` (`name`) " .
            "VALUES ('$newsem') ; " ;
        $GBLmysqli->dbquery($q);
        $newsemid = $GBLmysqli->insert_id;


        $qnewclass = 
            "INSERT INTO `class` (`name` , `agreg` , `partof` , `sem_id` , `discipline_id` , `scenery`) " . 
                    "SELECT `cl`.`name` , `cl`.`agreg` , `cl`.`partof` , '$newsemid' , `cl`.`discipline_id` , `cl`.`scenery` " . 
                    "FROM `class` AS `cl` " .
                    "WHERE  `cl`.`sem_id` = '$currsemid' ; " ;
        //echo "<br> $qnewclass";
        $GBLmysqli->dbquery($qnewclass);

        $qsegment = 
            "INSERT INTO `classsegment` (`class_id` , `day` , `start` , `length` , `room_id` , `prof_id`) " . 
                    "SELECT `new`.`id` , `cs`.`day` , `cs`.`start` , `cs`.`length` , `cs`.`room_id` , `cs`.`prof_id` " . 
                    "FROM `class` AS `org` , `class` AS `new` , `classsegment` AS `cs` " . 
                    "WHERE `cs`.`class_id` = `org`.`id` " . 
                            "AND `org`.`sem_id` = '$currsemid' " . 
                            "AND `new`.`name` = `org`.`name` " . 
                            "AND `new`.`discipline_id` = `org`.`discipline_id` " . 
                            "AND `new`.`sem_id` = '$newsemid' ; " ;
        //echo "<br> $qsegment";
        $GBLmysqli->dbquery($qsegment);


        $qvacancy = 
            "INSERT INTO `vacancies` (`class_id` , `course_id` , `askednum` , `askedreservnum` , `givennum` , `givenreservnum` , `usednum` , `usedreservnum`) " . 
                    "SELECT `new`.`id` , `vc`.`course_id` , `vc`.`askednum` , `vc`.`askedreservnum` , `vc`.`givennum` , `vc`.`givenreservnum` , `vc`.`usednum` , `vc`.`usedreservnum`  " .
                    "FROM `class` AS `org` , `class` AS `new` , `vacancies` AS `vc` " . 
                    "WHERE `vc`.`class_id` = `org`.`id` " . 
                            "AND `org`.`sem_id` = '$currsemid' " . 
                            "AND `new`.`name` = `org`.`name` " . 
                            "AND `new`.`discipline_id` = `org`.`discipline_id` " . 
                            "AND `new`.`sem_id` = '$newsemid' ; " ;
        //echo "<br> $qvacancy";
        $GBLmysqli->dbquery($qvacancy);
    
        $qscenery = 
            "INSERT INTO `sceneryclass` (`class_id` , `scenery_id`) " . 
                    "SELECT `new`.`id` , `sc`.`scenery_id` " .
                    "FROM `class` AS `org` , `class` AS `new` , `sceneryclass` AS `sc` " . 
                    "WHERE `sc`.`class_id` = `org`.`id` " . 
                            "AND `org`.`sem_id` = '$currsemid' " . 
                            "AND `new`.`name` = `org`.`name` " . 
                            "AND `new`.`discipline_id` = `org`.`discipline_id` " . 
                            "AND `new`.`sem_id` = '$newsemid' ; " ;
        //echo "<br> $qscenery";
        $GBLmysqli->dbquery($qscenery);
    }
  
}




///// other 'help' functions


function checkweek($q , $qscen = null , $courseid = null , $termid = null) {
    global $GBLmysqli;
    $flag = array();

    $result = $GBLmysqli->dbquery($q);
    while ($sqlrow = $result->fetch_assoc()) {
        $disccodes[$sqlrow['code']] = $sqlrow['code'];
        $disc[$sqlrow['code']] = $sqlrow['discname'];

        if (!$vac[$sqlrow['code'] . ' - ' . $sqlrow['name']]) {
            if($courseid) {
                $q = 
                    "SELECT `askednum` AS `totalA` , " .
                            "`askedreservnum`  AS `totalB` " .
                    "FROM `vacancies` " .
                    "WHERE `class_id` = '$sqlrow[classid]' " .
                    "AND `course_id` = '$courseid' ; " ;
            } else {
                $q = 
                    "SELECT SUM(givennum) AS `totalA` , " .
                            "SUM(givenreservnum) AS `totalB` " .
                    "FROM `vacancies` " .
                    "WHERE `class_id` = '$sqlrow[classid]' ; " ;
            }
            $vacresult = $GBLmysqli->dbquery($q);
            $vacrow = $vacresult->fetch_assoc();
            $vac[$sqlrow['code'] . ' - ' . $sqlrow['name']] = $vacrow['totalA'] + $vacrow['totalB'];
        }
        if ($vac[$sqlrow['code'] . ' - ' . $sqlrow['name']]) {
            for ($i = 0; $i < $sqlrow['length']; $i++) {
                if($sqlrow['disckind']) {$kind = ' ('.$sqlrow['disckind'].') ';} else {$kind = '';};
                $start = $sqlrow['start'];
                $day = $sqlrow['day'];
                $discweek[$day][$start+$i][$sqlrow['code']] += 1;
                $discflag[$sqlrow['code']] = 1;
            }
        }
    }
  
    if($termid) {
        $q = 
            "SELECT `discipline`.`code` " .
            "FROM `discipline` , `coursedisciplines` , `disciplinekind` " .
            "WHERE `coursedisciplines`.`course_id` = '$courseid' " .
                    "AND `coursedisciplines`.`term_id` = '$termid' " .
                    "AND `coursedisciplines`.`disciplinekind_id` = `disciplinekind`.`id` " .
                    "AND `coursedisciplines`.`discipline_id` = `discipline`.`id` " .
                    "AND (`disciplinekind`.`code` = 'OB' OR `disciplinekind`.`code` = 'AL') ; " ;
        $termsql = $GBLmysqli->dbquery($q);
        while ($termrow = $termsql->fetch_assoc()) {
            if(!$discflag[$termrow['code']]) {$flag['ob'] = 1;};
        }
    }
    for ($j = 7;$j<22;$j++) {
        for ($i = 2;$i<8;$i++) {
            if (count($discweek[$i][$j]) > 1) {
                $flag['disc'] = 1;
            } else {
                if(max($discweek[$i][$j]) > 1) {
                    $flag['class'] = 1;
                }
            }
        }
    }
    return($flag);
}



////
////
////
////

function dbweekmatrix($q , $qscen = null , $courseid = null , $termid = null , $edit = true , $matrixonly = false , $courseHL = null) {
    global $GBLmysqli;
    global $GBL_Dspc, $GBL_Tspc, $GBL_Qspc;
    
    $rtnmatrix = '';
  
    //  $basevals = array('DA' , 'A0' , '68' , '40' , '00');
    //  $basevals = array('D8' , 'A0' , '80' , '50' , '00');
    //  $basevals = array('E6' , '9B' , '5A' , '00');
    $basevals = array('C8' , '90' , '5A' , '00');
    $numcolors = 0;
    foreach ($basevals as $red) {
        foreach ($basevals as $green) {
            foreach ($basevals as $blue) {
                $colors[$numcolors] = '#'.$red.$green.$blue;
                $numcolors++;          
                //          $rtnmatrix .=  spanformat('smaller' , '#'.$red.$green.$blue , '<b>#'.$red.$green.$blue.'</b>&nbsp;');
            }
            //        $rtnmatrix .=  '<br>';
        }
        //      $rtnmatrix .=  '<br>';
    }


    $hiddenclasskeys = null;
    $hiddenprofdeptid = null;
    $result = $GBLmysqli->dbquery($q);
    while ($sqlrow = $result->fetch_assoc()) {
        $courseHLquery = $sqlrow['courseid'];
        $disccodes[$sqlrow['code']] = $sqlrow['code'];
        $disc[$sqlrow['code']] = $sqlrow['discname'];
        $discid[$sqlrow['code']] = $sqlrow['discid'];
        $discbgcolor[$sqlrow['code']] = 0x0;
        $profnicks = 0;

        if (!isset($scen[$sqlrow['classid']])) {
            if($sqlrow['scenery']) {
                if(isset($qscen)) {
                    $qx = 
                        "SELECT `scen`.`id` , `scen`.`name` " .
                        "FROM `sceneryclass` AS `sc` , " .
                                "`scenery` AS `scen` " .
                        "WHERE `sc`.`class_id` = '$sqlrow[classid]' " .
                                "AND `sc`.`scenery_id` IN ( $qscen ) " .
                                "AND `sc`.`scenery_id` = `scen`.`id` ; " ;
                    $qxresult = $GBLmysqli->dbquery($qx);
                    while ($qxrow = $qxresult->fetch_assoc()) {
                        $scen[$sqlrow['classid']] .= $qxrow['name'];
                    }
                } else {
                    $scen[$sqlrow['classid']] = '';          
                }
            } else {
                $scen[$sqlrow['classid']] = '';
            }
        }
    
        $classindex = $sqlrow['code'] . ' - ' . $sqlrow['name'];

        if (!$vac[$classindex]) {
            if($courseid) {
                $q = 
                    "SELECT `askednum` AS `totalA` , " .
                            "`askedreservnum`  AS `totalB` " .
                    "FROM `vacancies` " .
                    "WHERE `class_id` = '$sqlrow[classid]' " .
                    "AND `course_id` = '$courseid' ; " ;
            } else {
                $q = 
                    "SELECT SUM(givennum) AS `totalA` , " .
                            "SUM(givenreservnum) AS `totalB` " .
                    "FROM `vacancies` " .
                    "WHERE `class_id` = '$sqlrow[classid]' ; " ;
            }
            $vacresult = $GBLmysqli->dbquery($q);
            $vacrow = $vacresult->fetch_assoc();
            $vac[$classindex] = $vacrow['totalA'] + $vacrow['totalB'];  ;  
        }
        if(!$vacHL[$classindex] && $courseHL) {
            $q = 
                "SELECT (`askednum` + `askedreservnum` + `givennum` + `givenreservnum`) AS `total` " .
                "FROM `vacancies` " .
                "WHERE `class_id` = '$sqlrow[classid]' " .
                        "AND `course_id` = '$courseHL' ; " ;
            $vacresult = $GBLmysqli->dbquery($q);
            $vacrow = $vacresult->fetch_assoc();
            $vacHL[$classindex] = $vacrow['total'];  
        }
    
        if ($vac[$classindex]) {
            if($sqlrow['disckind']) {$kind = ' ('.$sqlrow['disckind'].') ';} else {$kind = '';};
            $start = $sqlrow['start'];
            $day = $sqlrow['day'];
        
            $classhiddenkey = hiddenclasskey($_POST['semid'] , $sqlrow['discdeptid'] , $sqlrow['discid'] , $sqlrow['classid']) ;
            $hiddenclasskeys[$sqlrow['discdeptid']][$sqlrow['discid']][$sqlrow['classid']] = $classhiddenkey;
        
            $d  = $sqlrow['code'] . $kind . ' - ' . hiddenformlnk($classhiddenkey , $sqlrow['name']) . ' (' . $vac[$sqlrow['code'] . ' - ' . $sqlrow['name']] . ')';
            if($vacHL[$classindex]) {
                $d .= ' **';
            }
        
            if ($sqlrow['profnick']) {
                $profnicks = 1;
                $hiddenprofdeptid[$sqlrow['profid']] = $sqlrow['profdeptid'];
                $d .= '<p style="margin:0;border:0;line-height:50%;"><sup>'. spanformat('75%' , 'MidnightBlue' , $scen[$sqlrow['classid']] , null);
                $d .= hiddenformlnk(hiddenprofkey($_POST['semid'] , $sqlrow['profdeptid'] , $sqlrow['profid']) , spanformat('75%' , 'red' , '('.$sqlrow['profnick'].')')) . '</sup></p>'; 
            } else {
                $d .= '<p style="margin:0;border:0;line-height:50%;"><sup>'. spanformat('75%' , 'MidnightBlue' , $scen[$sqlrow['classid']] , null).'</sup></p>';
            } 
            $seg[$d] = $sqlrow['code'];
            $discflag[$sqlrow['code']] = 1;

            for ($i = 0; $i < $sqlrow['length']; $i++) {
                $week[$day][$start+$i][] = $d;
                $discweek[$day][$start+$i][$sqlrow['code']] += 1;
            }
        }
        $discdept[$sqlrow['code']] = $sqlrow['discdeptid'];
        $discid[$sqlrow['code']] = $sqlrow['discid'];
    }
    $i = 0;
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
                    $rtnmatrix .=  hiddenclassform($_POST['semid'] , $Hdeptid , $Hdiscid , $Hclassid , 'name' , $profnicks , $courseHL);
                }
            }
        }
    
    }
  

    if($hiddenprofdeptid){
        foreach ($hiddenprofdeptid as $Hprofid => $Hdeptid) {
            $rtnmatrix .=  hiddenprofform($_POST['semid'] , $Hdeptid , $Hprofid);
        }
    }


    $rtnmatrix .=  '<table>';
    $rtnmatrix .=  '<tr style="border-bottom:1px solid black"><th style="width:50px">Hora</th>';
    for ($i = 2; $i <8; $i++) {
        $rtnmatrix .=  "<th style='width:155px'> " . $_SESSION['weekday'][$i] . '</th>';
    }
    $rtnmatrix .=  '</tr>';
    for ($j = 7;$j<22;$j++) {
        $rtnmatrix .=  '<tr style="border-bottom:1px solid black"><td>' . $j . ':30 ' . $GBL_Dspc . ' </td>';
        for ($i = 2;$i<8;$i++) {
            $td = '<td>';
            if (count($discweek[$i][$j]) > 1) {
                $td = '<td style="background:#FFF2F2;">';
                foreach ($discweek[$i][$j] as $xID => $xcnt) {
                    $discbgcolor[$xID] |= 0xFF0000;
                }
            } else {
                if(max($discweek[$i][$j]) > 1) {
                    $td = '<td style="background:#F2FFF2;">';
                    foreach ($discweek[$i][$j] as $xID => $xcnt) {
                        $discbgcolor[$xID] |= 0x00FF00;
                    }
                }
            }
            $rtnmatrix .=  $td;
            foreach ($week[$i][$j] as $d) {
                $rtnmatrix .=  '<p style="margin:0;border:0;">' . spanformat(null , $disccolor[$seg[$d]], '<b>'.$d.'</b>');
            }
            $rtnmatrix .=  '</td>';
        }
        $rtnmatrix .=  '</tr>';
    }
    $rtnmatrix .=  '</table>';
  
    if($matrixonly) {}
    else {
        $hiddencoursekeys = null;
        foreach ($disccodes as $d) {
            if($courseid){
                $q = 
                    "SELECT `kind`.`code` " .
                    "FROM `disciplinekind` AS `kind` , " .
                            "`coursedisciplines` AS `cd` " .
                    "WHERE `cd`.`course_id` = '$courseid' " .
                            "AND `cd`.`discipline_id` = '$discid[$d]' " .
                            "AND `cd`.`disciplinekind_id`= `kind`.`id` ; " ;
                $result = $GBLmysqli->dbquery($q);
                $sqlrow = $result->fetch_assoc();
                $kind = '<sub>'.spanformat('smaller' , '' , $sqlrow['code']).'</sub>';
            } else {
                $kind = null;
                $q = 
                    "SELECT `kind`.`code` AS kcode , " .
                            "`course`.`acronym` AS acro, " .
                            "`course`.`id` AS courseid , " .
                            "`term`.`code`  AS tcode , " .
                            "`term`.`id`  AS termid ".
                    "FROM `disciplinekind` AS `kind` , " .
                            "`coursedisciplines` AS `cd` , " .
                            "`unit` AS `course` , term " . 
                    "WHERE  `cd`.`discipline_id` = '$discid[$d]' " .
                            "AND `cd`.`course_id`= `course`.`id` " .
                            "AND `cd`.`term_id`= `term`.`id`  " .
                            "AND `cd`.`disciplinekind_id`= `kind`.`id` ; " ;
                $result = $GBLmysqli->dbquery($q);
                while ($sqlrow = $result->fetch_assoc()) {
                    $hiddencoursekeys[$sqlrow['courseid']][$sqlrow['termid']] = hiddencoursekey($_POST['semid'] , $sqlrow['courseid'] , $sqlrow['termid']);
                    if (($sqlrow['kcode'] == 'OB') || ($sqlrow['kcode'] == 'AL')) {$bold = true;$tcolor = '#0000A0';} else {$bold = false;$tcolor = null;}
                    $kind .= hiddenformlnk($hiddencoursekeys[$sqlrow['courseid']][$sqlrow['termid']] , spanformat (null , $tcolor , ' ' . $sqlrow['acro'] . ' - ' . $sqlrow['tcode'] . spanformat('smaller' , null , '('.$sqlrow['kcode'] .')') . $GBL_Tspc . ' ' , null , $bold));
                }
                if ($kind) {
                    $kind = '<sub>'.spanformat('smaller' , '' , $kind).'</sub>';
                }
            }
            if($discbgcolor[$d]) {
                $bgcolor = '#' . dechex($discbgcolor[$d] | 0xECECE8);
            } else {
                $bgcolor = null;
            };

            if ($edit) {
                $rtnmatrix .=  hiddendiscform($_POST['semid'] , $discdept[$d] , $discid[$d] , $profnicks , $courseHL , '') . formsubmit('submit' , 'go edit');      
                $rtnmatrix .=   spanformat('' , $disccolor[$d], $d . ' - ' . $disc[$d]  , $bgcolor , true) . $kind;
                $rtnmatrix .=   '</form>'   ;
            } else {
                $rtnmatrix .=   spanformat('' , $disccolor[$d], $d . ' - ' . $disc[$d]  , $bgcolor , true) . $kind . '<br>';
            }
      
        }
        if ($hiddencoursekeys) {
            foreach ($hiddencoursekeys as $cid => $acid) {
                foreach ($acid as $tid => $atid) {
                    $rtnmatrix .=  hiddencourseform($_POST['semid'] , $cid , $tid);
                }
            }
        }
        if($termid) {
            $q = 
                "SELECT `disc`.`code` , " .
                        "`disc`.`name` , " .
                        "`disc`.`id` AS `discid` , " .
                        "`discdept`.`id` AS `discdeptid` , " .
                        "`kind`.`code` AS `kindcode`" .
                "FROM `discipline` AS `disc`  , " .
                        "`coursedisciplines` AS `cd`  , " .
                        "`disciplinekind` AS `kind` , " .
                        "`unit` AS `discdept`" .
                "WHERE `cd`.`course_id` = '$courseid' " .
                        "AND `disc`.`dept_id` = `discdept`.`id` " .
                        "AND `cd`.`term_id` = '$termid' " .
                        "AND `cd`.`disciplinekind_id` = `kind`.`id` " .
                        "AND `cd`.`discipline_id` = `disc`.`id`; " ;
            //        "AND (`kind`.`code` = 'OB' OR `kind`.`code` = 'AL');";
            $termsql = $GBLmysqli->dbquery($q);
            $title = '<h5><b>Disciplina(s) não ofertada(s)</b></h5>';
            while ($termrow = $termsql->fetch_assoc()) {
                if(!$discflag[$termrow['code']]) {
                    if($title) {
                        $rtnmatrix .=  $title;
                        $title = '';
                    }
                    $rtnmatrix .=  hiddendiscform($_POST['semid'] , $termrow['discdeptid'] , $termrow['discid'] , $profnicks , $courseHL , null) . 
                        formsubmit('submit' , 'go edit') . 
                        $termrow['code'] . ' - ' . $termrow['name']  . 
                        '<sub>' . 
                                spanformat('smaller' , '' , $termrow['kindcode']).
                        '</sub>' . 
                        '</form>'  ;
                };
            }
        }
    }
    
    return $rtnmatrix;
}


function spanformat($size , $color , $text , $bgcolor = null , $bold = null , $height = null) {
    return spanfmtbegin($size , $color , $bgcolor , $bold , $height) . $text . spanfmtend();
}

function spanfmtbegin($size , $color , $bgcolor = null , $bold = null , $height = null) {
    $style = '';
    if($size) {$style .= 'font-size:'.$size.';';}
    if($color) {$style .= 'color:'.$color.';';}
    if($bgcolor) {$style .= 'background:'.$bgcolor.';';}
    if($bold) {$style .= 'font-weight:bold;';}
    if($height) {$style .= 'line-height:'.$height.';';}
    return '<span style = "'.$style.'">' ;
}
function spanfmtend() {
    return '</span>';
}
  
function pagereload($page) {
    return "<script type=\"text/javascript\">
      setInterval('location.replace(\"" . $page . "\")', 200);
      </script>";
}

function formpost($action , $target = null , $formname = null) {
    if($target) {$target = ' target = "'.$target.'"';};
    if($formname) {$formname = ' name="'.$formname.'"';};
    return '<form method="post" enctype="multipart/form-data" action="' . $action . '"'.$target . $formname . '>';
}
  
  
function hiddenformlnk($formkey , $textlink) {
    return '<a href = "javascript:document.forms['."'" .  $formkey .  "'" . '].submit()">' . $textlink .'</a>';
}
  
function hiddenprofform($semid , $deptid , $profid , $closing = '</form>') {
    global $GBLbasepage;
    $lnk = join('_' , array('profhid' , $semid , $deptid , $profid));
    
    return formpost( $_SESSION['pagelnk']['prof'] , $lnk , $lnk ) . 
        formhiddenval('semid' , $semid) . formhiddenval('deptid' , $deptid) . 
        formhiddenval('profid' , $profid) . formhiddenval('act' , 'Refresh') . $closing;
}
function hiddenprofkey($semid , $deptid , $profid) {
    return join('_' , array('profhid' , $semid , $deptid , $profid));
}
  
function hiddenroomform($semid , $buildingid , $roomid , $closing = '</form>') {
    global $GBLbasepage;
    $lnk = join('_' , array('roomhid' , $semid , $buildingid , $roomid));

    return formpost( $_SESSION['pagelnk']['room'] , $lnk, $lnk ) . 
        formhiddenval('semid' , $semid) . formhiddenval('buildingid' , $buildingid) . 
        formhiddenval('roomid' , $roomid) . formhiddenval('act' , 'Refresh') . $closing;
}
function hiddenroomkey($semid , $buildingid , $roomid) {
    return join('_' , array('roomhid' , $semid , $buildingid , $roomid));
}

function hiddencourseform($semid , $courseid , $termid , $closing = '</form>') {
    global $GBLbasepage;
    $lnk = join('_' , array('coursehid' , $semid , $courseid , $termid));
    
    return formpost( $_SESSION['pagelnk']['course'] , $lnk, $lnk ) . 
        formhiddenval('semid' , $semid) . formhiddenval('courseid' , $courseid) . 
        formhiddenval('termid' , $termid) . formhiddenval('act' , 'Refresh') . $closing;
}
function hiddencoursekey($semid , $courseid , $termid) {
    return join('_' , array('coursehid' , $semid , $courseid , $termid));
}
  
function hiddendiscform($semid , $deptid , $discid , $profnicks = '0' , $courseHL = '' , $closing = '</form>') {
    global $GBLbasepage;
    $lnk = join('_' , array('dischid' , $semid , $deptid , $discid));
    
    return formpost( $_SESSION['pagelnk']['edclass'] ,  $lnk ,  $lnk ) . 
        formhiddenval('semid' , $semid) . formhiddenval('unitid' , $deptid) . 
        formhiddenval('discid' , $discid) . 
        formhiddenval('profnicks' , $profnicks) . 
        formhiddenval('courseHL' , $courseHL) . 
        formhiddenval('act' , 'Refresh') . $closing;
}
function hiddendisckey($semid , $deptid , $discid) {
    return join('_' , array('dischid' , $semid , $deptid , $discid));
}
  
function hiddenclassform($semid , $deptid , $discid , $classid , $classname , $profnicks = '0' , $courseHL = '' , $closing = '</form>') {
    global $GBLbasepage;
    $pagelnk = join('_' , array('dischid' , $semid , $deptid , $discid));
    $formlnk = join('_' , array('classhid' , $semid , $deptid , $discid , $classid));
    
    return formpost( $_SESSION['pagelnk']['edclass'] . '#class'.$classid.'div', $pagelnk, $formlnk ) . 
        formhiddenval('semid' , $semid) . formhiddenval('unitid' , $deptid) . 
        formhiddenval('discid' , $discid) . 
        formhiddenval('classid' , $classid) . formhiddenval('classname' , $classname) . 
        formhiddenval('profnicks' , $profnicks) . 
        formhiddenval('courseHL' , $courseHL) . 
        formhiddenval('act' , 'Edit') . $closing;
}
function hiddenclasskey($semid , $deptid , $discid , $classid) {
    return join('_' , array('classhid' , $semid , $deptid , $discid , $classid));
}    
    
function iddivkey($key , $val) {
    return ' id="' . $key . $val . 'div"';
}
  
function hiddendivkey($key , $val) {
    return "<div id='${key}${val}div'>  </div><br><br><br>";
}
  
function targetdivkey($key , $val) {
    return '#' . $key . $val . 'div';
}

function formpatterninput($max , $size , $pattern , $title , $fieldname , $fieldval) {
    $_SESSION['org'][$fieldname] = $fieldval;
    return '<input type="text" maxlength="'.$max.'" size="'.$size.'" pattern="'.$pattern.'" title="'.$title.'" name="'.$fieldname.'" value="'.htmlentities($fieldval , ENT_QUOTES).'"\>';
}

function formhiddenval($field , $val) {
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
                

function formsubmit($field , $val) {
    return "<input type='submit' name='$field' value='$val' />\n";
}


function displaysqlitem($str , $sqltable , $sqlid , $sqlitem , $sqlitemB = null) {
    global $GBLmysqli;
    if($sqlitemB) {$b = ' , `'.$sqlitemB.'`';} else {$b = '';};
    $q = 
        "SELECT `$sqlitem`$b " .
        "FROM `$sqltable` " .
        "WHERE `id` = '$sqlid' ; " ;
    $result = $GBLmysqli->dbquery($q);
    $sqlrow = $result->fetch_assoc();
    if($sqlitemB) {
        return $str . $sqlrow[$sqlitem] . ' -- ' . $sqlrow[$sqlitemB] .'   ';
    } else {
        return $str . $sqlrow[$sqlitem] . '   ';
    }
}
  
  
function fieldscompare($key , $fields) {
    foreach ($fields as $field) {
//        vardebug($_POST[$key.$field],$key.$field);
//        vardebug($_SESSION['org'][$key.$field],'session '.$key.$field);
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
        $tbl = ' , `sceneryclass` ';
        $sql = "AND ( (`class`.`scenery` = '0') OR " .
            " (`class`.`scenery` = '1' AND `sceneryclass`.`class_id` = `class`.`id` AND `sceneryclass`.`scenery_id` IN ( $inscenery )) ) "  ;
        return (array($tbl , $sql));
    } else {
        return (array(' ' , "AND `class`.`scenery` = '0' " ));
    }
}




function formsessionselectinit($fieldname , $fieldlist) {
    if ($_POST[$fieldname]) {
        unset($_SESSION[$fieldname]);
        foreach ($_SESSION[$fieldlist] as $selectid => $selectname) {
            if ($_POST[$fieldname.$selectid]) {
                $_SESSION[$fieldname][$selectid] = $selectname;
            }
        }  
    }
    return formhiddenval($fieldname , 'true');
}





function formsessionselect($session , $fieldname , &$cnt , $desc = null) {
    $rtntext = '';
    foreach ($session as $selectid => $selectname) {
        $checked = '';
        $style = '';
        if ($_SESSION[$fieldname][$selectid]) {
            $checked = ' checked';
            $style = ';background-color: lightgray';
        };
        $cnt++;
        if ($cnt == 7) {
            $cnt = 1;
            $rtntext .= '</tr><tr>';
            //echo '</tr><tr>';
        }
        if ($desc) {
            $rtntext .= '<td style="width:170px' . $style . '"><b>' . $selectname .':</b> '. $_SESSION[$desc][$selectid].'</td>';
            //echo '<td style="width:170px' . $style . '"><b>' . $selectname .':</b> '. $_SESSION[$desc][$selectid].'</td>';
        } else {
            
            $rtntext .=  '<th style="width:170px' . $style . '">' . 
                '<input type="checkbox" name="' . 
                $fieldname . $selectid . 
                '" value="' . $selectid . 
                '"' . $checked . 
                ' > <label for="' . 
                $fieldname . $selectid .
                '">' . 
                $selectname . 
                '</label></th>';
                /*
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
                */
        }

    }
    return $rtntext;
}

  
function formsceneryselect() {
    global $GBL_Dspc, $GBL_Tspc, $GBL_Qspc;
        
    $rtntext = '';

    $rtntext .=  formsessionselectinit('sceneryselected' , 'scen.acc.view');
    $rtntext .=  formsessionselectinit('sceneryroles' , 'scen.editroles');
                    
    $rtntext .=  '<details>';
    $rtntext .=  '<summary>' . $GBL_Tspc . '<b>&rArr;</b> ';
    $rtntext .=  displaysessionselected('Cenário(s)' , 'sceneryselected');
    $rtntext .=  '</summary>';
    $cnt = 0;
    $rtntext .=  '<table><tr>';
    foreach ($_SESSION['sceneryroles'] as $roleid => $roledesc) {
        $rtntext .=  formsessionselect($_SESSION['scen.byroles'][$roleid] , 'sceneryselected' , $cnt);
    }
    $rtntext .=  '</tr></table>';        
        
                        
    $rtntext .=  '<details>';
    $rtntext .=  '<summary>' . $GBL_Tspc . '<b>&rArr;</b> Legenda: ';
    $rtntext .=  '</summary>';
    $cnt = 0;
    $rtntext .=  '<table><tr>';
    foreach ($_SESSION['sceneryroles'] as $roleid => $roledesc) {
        $rtntext .=  formsessionselect($_SESSION['scen.byroles'][$roleid] , 'sceneryselected' , $cnt , 'scen.desc');
    }
    $rtntext .=  '</tr></table>';        
    $rtntext .=  '</details>';       
    $rtntext .=  '<p style="line-height:0px;"></p>';
    $rtntext .=  '<details>';
    $rtntext .=  '<summary>' . $GBL_Tspc . '<b>&rArr;</b> ';
    $rtntext .=  displaysessionselected('Perfil(is)' , 'sceneryroles');
    $rtntext .=  '</summary>';
    $cnt = 0;
    $rtntext .=  '<table><tr>';
    $rtntext .=  formsessionselect($_SESSION['scen.editroles'] , 'sceneryroles' , $cnt);
    $rtntext .=  '</tr></table>';        
    $rtntext .=  '</details>';       
                        
    $rtntext .=  formsubmit('act' , 'Refresh');
    $rtntext .=  '</details>';
    return $rtntext;
}
  
function displaysessionselected($label , $fieldname){
    $rtntext = $label.': ' ;
    $comma = '';
    foreach ($_SESSION[$fieldname] as $id => $name) {
        $rtntext .= $comma . '<b> ' . $name . '</b>';
        $comma = ', ';
    }
    return $rtntext;
}
  
function formselectrange($selectname , $initial , $final , $refval , $trail = null , $disparray = null) {
    $_SESSION['org'][$selectname] = $refval;
    $rtntext = "<select name='".$selectname."'>";
    for ($i = $initial;$i<$final;$i++) {
        if ($i == $refval) {
            $selected = ' selected="selected"';
        } else {
            $selected = '';
        }
        if($disparray) {$val = $disparray[$i];} else {$val = $i;}
        $rtntext .= "<option value='$i'$selected>".$val.$trail.'</option>';
    }  
    $rtntext .= '</select>';
    return $rtntext;
}


function formselectsession($selectname , $sessionkey , $refval , $nulloption = false , $onchange = false) {
    $_SESSION['org'][$selectname] = $refval;
    if ($onchange) {
        $rtntext = "<select name='".$selectname."' onchange='this.form.submit(".$submit.")'>";
    } else {
        $rtntext = "<select name='".$selectname."'>";
    }
    if($nulloption) { echo "<option value='0'>--</option>";  }
    foreach ($_SESSION[$sessionkey] as $id => $val) {
        if ($id == $refval) {
            $selected = ' selected="selected"';
        } else {
            $selected = '';
        }
        $rtntext .= "<option value='$id'$selected>".$val."</option>";
    }  
    $rtntext .= '</select>';
    return $rtntext;
}


function formselectsql(&$any , $q , $selectname , $refval , $idkey , $valAkey , $valBkey = null , $onchange = true) {
    global $GBLmysqli;
    
    $_SESSION['org'][$selectname] = $refval;
    $result = $GBLmysqli->dbquery($q);
    if ($onchange) {
        $rtntext = "<select name='".$selectname."' onchange='this.form.submit(".$submit.")'>";
        $rtntext .= "<option value='0'>---</option>";
    } else {
        $rtntext = "<select name='".$selectname."'>";
        $rtntext .= "<option value='0'>---</option>";
    };
    $any = 0;
    while ($sqlrow = $result->fetch_assoc()) {
        $any = 1;
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
        $rtntext .= "<option value='".$sqlrow[$idkey]."'$selected>".$val."</option>";
    }
    $rtntext .= '</select>';
    return $rtntext;
}


function HLbegin() {
    global $GBLhighlightstyle;
    return "<table $GBLhighlightstyle ><tr><td>";
}
  
function HLend() {
    return '</td></tr></table>';
}
  
function formjavaprint($title) {

    return
        "<script type=\"text/javascript\">
        function printContent(id){
          str = document.getElementById(id).innerHTML
          newwin = window.open('' , 'printwin' , 'left=100 , top=100 , width=1100 , height=1000')
          newwin.document.write('<HTML><HEAD>')
          newwin.document.write('<TITLE>" . $title . "</TITLE>')
          newwin.document.write('<script>')
          newwin.document.write('function chkstate(){')
          newwin.document.write('if(document.readyState==\"complete\"){')
          newwin.document.write('window.close()')
          newwin.document.write('}')
          newwin.document.write('else{')
          newwin.document.write('setTimeout(\"chkstate()\" , 2000)')
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


