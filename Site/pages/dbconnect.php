<?php

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
        $this->set_charset('utf8');
    }


    public function dbquery($Query , $logOK = null) {
        global $GBLspc;

        if ($result = $this->query($Query)) {
            if ($logOK) {
                $this->eventlog($logOK);
            }
        } else {
            $err = 'Query <' . $Query . '> failed: (' . $this->errno . ') ' . $this->error;
            echo '<br><b>Query' . $GBLspc['D'] . '</b>' . spanformat('smaller' , '', htmlspecialchars($Query , ENT_QUOTES))  . '<b>' . $GBLspc['T'] . 'FAILED' . spanformat('' , 'red',  htmlspecialchars($this->error , ENT_QUOTES)) . '</b></p>';
            $err = $this->real_escape_string($err);
            $logOK['action'] = $this->real_escape_string($logOK['action']);
            $this->eventlog(array('level'=>'DBERROR' ,   'action'=> $logOK['action'] . '(dbquery)', 'str' => $err, 'xtra' => 'dbconnect.php'));
        }
        return $result;
    }

    public function trydbquery($Query , $logOK = null) {
        if ($result = $this->query($Query)) {
            if ($logOK) {
                $this->eventlog($logOK);
            }
        }
        return $result;
    }


    public function postsanitize(){
        foreach ($_POST as &$val) {
            $val = $this->real_escape_string($val);
        }
    }

    //LOG array
    //$log['level'] INFO/LOGIN/...
    //$log['action'] action being executed, to be recorded, like login , dbupdate...
    //$log['str'] 
    //$log['xtra']


    public function eventlog($logdata) {
        $trace = debug_backtrace();
        $callerA = $trace[1]['function'];  // who called us (we are 0)
        $callerB = $trace[2]['function'];  // who called us (we are 0)
        $callerC = $trace[3]['function'];  // who called us (we are 0)
      
        // just in case something fails...
        $logline = 'LOG:' . $logdata['level'] . ' :: userID:' . $_SESSION['userid'] . '('. $_SESSION['useremail']  . ') remote:' . $_SERVER['REMOTE_ADDR'] . ' ' . $_SERVER['HTTP_USER_AGENT']. 'Callers: <' . 
            $callerA['function'] . '><' . $callerB['function'] . '><' . $callerC['function'] . '>  action:' . $logdata['action'] . '==>' . $logdata['str'] . '(' . $logdata['xtra'] . ')';
        //
        
        $loglevel =  $_SESSION['log'][$logdata['level']];
        $Query = 
                "INSERT INTO `log` ( " . 
                        "loglevel_id , " . 
                        "user_id , " . 
                        "browserIP , " . 
                        "browseragent , " . 
                        "callerA , " . 
                        "callerB , " . 
                        "callerC , " . 
                        "action , " . 
                        "logline , " . 
                        "logxtra , " . 
                        "dataorg , " . 
                        "datanew ) " . 
                "VALUES (" . 
                        " '$loglevel'  , " . 
                        " '$_SESSION[userid]'  , " . 
                        " '$_SERVER[REMOTE_ADDR]'  , " . 
                        " '$_SERVER[HTTP_USER_AGENT]'  , " . 
                        " '$callerA'  , " . 
                        " '$callerB'  , " . 
                        " '$callerC'  , " . 
                        " '$logdata[action]'  , " . 
                        " '$logdata[str]'  , " . 
                        " '$logdata[xtra]'  , " . 
                        " '$logdata[dataorg]'  , " . 
                        " '$logdata[datanew]'  ) ; " ;
        
        $result = $this->dbquery( $Query );
        if(!$result) {
            $str = "<br>LOG execute failed: ( $result->errno ) $result->error "; 
            echo $str;
            writeLogFile("$str \n $logline \n"); // last resort !!!    
        }    
    }


    public function maillogincheck($email , $passwd) {
        list($microstamp , $sec) = explode(' ' , microtime(false));
        list($nothing , $microstamp) = explode('.' , $microstamp);

        $Query = 
            "SELECT * " . 
            "FROM `account` " . 
            "WHERE `email` = '" . $email . "';";
        $result = $this->dbquery( $Query );
    
        if (($sqlrow = $result->fetch_assoc())) {
            if (($passwd == $sqlrow['password'] && $sqlrow['activ'])) {
                $newhash = md5("$email - $microstamp");
                $this->set_sessionvalues($sqlrow , $newhash);
                $result->close();
                $Query = 
                    "UPDATE  `account` " . 
                    "SET `sessionhash` = '$newhash' " . 
                    "WHERE `email` = '$email' ; " ;
                $this->dbquery($Query , array('level'=>'LOGIN' , 'action'=>'login' , 'str'=>"user $sqlrow[email] login" , 'xtra'=>'core.php'));
                $_SESSION['last_hashcheck'] = time() ;
                return TRUE;
            }
        }
        $result->close();
        return FALSE;
    }

    public function hashcheck() {
        global $GBLtimeout , $GBLgracetime ;
        
        $Query = 
            "SELECT * " . 
            "FROM `account` " . 
            "WHERE `sessionhash` = '$_SESSION[sessionhash]' " . 
                    "AND `id` = '$_SESSION[userid]' ; " ;
        $result = $this->dbquery( $Query );
        if (($sqlrow = $result->fetch_assoc())) {
            $now = time() ; 
            $diff = $now - $_SESSION['last_hashcheck'] ; 
            if ($diff > $GBLtimeout) {
                // timeout
                return FALSE;
            } elseif ($diff > $GBLgracetime)  {
                $_SESSION['last_hashcheck'] = $now ;
            }
            //vardebug($diff);
            return TRUE;
        } else {
            $log = array('level'=>'WARNING' , 'action'=>'session check' , 'str'=>'hashcheck failed !' , 'xtra'=>'core.php');
            $this->eventlog($log) ;
        }
        $result->close();
        return FALSE;
    }


    public function hashpasswdcheck($passwd) {
        $Query = 
            "SELECT * " . 
            "FROM `account` " . 
            "WHERE `sessionhash` = '$_SESSION[sessionhash]' " . 
            "AND `password` = '${passwd}' ; " ;
        $result = $this->dbquery( $Query );
        if (($sqlrow = $result->fetch_assoc())) {
            return TRUE;
        } else {
            $log = array('level'=>'WARNING' , 'action'=>'passwd check' , 'str'=>'hash/passwd check failed !' , 'xtra'=>'core.php');
            $this->eventlog($log) ;
        }
        $result->close();
        return FALSE;
    }



    public function dbvaluesloop($Query , $sessionkeyA , $sqlkeyA , $sqlvalA , $sessionkeyB = null , $sqlkeyB = null , $sqlvalB = null) {
        $result = $this->dbquery($Query);
        while ($sqlrow = $result->fetch_assoc()) {
            $_SESSION[$sessionkeyA][$sqlrow[$sqlkeyA]] = $sqlrow[$sqlvalA];
            if ($sqlkeyB) {
                $_SESSION[$sessionkeyB][$sqlrow[$sqlkeyB]] = $sqlrow[$sqlvalB];
            }
        }
        $result->close();
    }


    public function set_sessionvalues($usrsql , $userhash) {
        $_SESSION['userid'] = $usrsql['id'];
        $_SESSION['useremail'] = $usrsql['email'];
        $_SESSION['username'] = $usrsql['name'];
        $_SESSION['userchgpasswd'] = $usrsql['chgpasswd'];
        $_SESSION['userdisplayname'] = $usrsql['displayname'];
    
        $_SESSION['sessionhash'] = $userhash;
  
  
        $this->set_rolesessionvalues();
    
        $this->set_scenerysessionvalues();

        $Query = "select * from weekdays;";
        $this->dbvaluesloop($Query , 'weekday' , 'abrv' , 'id' , 'weekday' , 'id' , 'abrv');
    
        $Query = "SELECT *  FROM `loglevel`;";
        $this->dbvaluesloop($Query , 'log' , 'level' , 'id');
    
        $_SESSION['bool'][0] = 'Não';
        $_SESSION['bool'][1] = 'Sim';

        $_SESSION['ADDsegments'][0] = 'Não';
        $_SESSION['ADDsegments'][1] = '1';
        $_SESSION['ADDsegments'][2] = '2';
        $_SESSION['ADDsegments'][3] = '3';

    
        $_SESSION['orderby'][0] = 'Nome';
        $_SESSION['orderby'][1] = 'Código';
    
    }

    public function set_rolesessionvalues() {
        unset($_SESSION['role']);

        $Query = 
            "SELECT DISTINCT role . * , " . 
                    "unit . acronym, " . 
                    "unit . code, " . 
                    "unit . iscourse, " . 
                    "unit . isdept " . 
            "FROM role , unit  , accrole accr , account  " . 
            "WHERE role . unit_id = unit . id " . 
                    "AND role . id = accr . role_id " . 
                    "AND account . id = accr . account_id " . 
                    "AND account . id = '$_SESSION[userid]' ; " ;
        $result = $this->dbquery( $Query );
        $boolkeys  = array('can_edit' , 'can_dupsem' , 'can_vacancies' , 'can_class' , 'can_addclass' , 'can_disciplines' , 'can_coursedisciplines' , 'can_prof' , 'can_viewlog' , 'can_scenery' , 'can_room');
        $txtkeys = array('rolename' , 'description');
        $_SESSION['usercanedit'] = '0';
        $_SESSION['usercanscen'] = '0';
        while ($sqlrow = $result->fetch_assoc()) {
            $_SESSION['role']['isadmin'] |= $sqlrow['isadmin'];
            $_SESSION['usercanedit'] |= $sqlrow['can_edit'];
            $_SESSION['usercanscen'] |= $sqlrow['can_scenery'];
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
//            if($sqlrow['can_scenery'] == '1') {
//                $_SESSION['sceneryroles'][$sqlrow['id']] = $sqlrow['description'];
//            }
            if($sqlrow['can_room'] == '1') {
                $BQuery = 
                        "SELECT DISTINCT `building` . * " .
                        "FROM `building` , `buildingrole` "  .
                        "WHERE `building`.`id` = `buildingrole`. `building_id` " .
                                "AND `buildingrole` . `role_id` = $sqlrow[id] ; " ;
                $buildresult = $this->dbquery( $BQuery );
                while ($sqlbuilding = $buildresult->fetch_assoc()) {
                        $_SESSION['role']['building'][$sqlbuilding['id']]['can_room'] = true ;
                }
                $buildresult->close();
            }
            
        }
        $result->close();

    }
  
    public function set_scenerysessionvalues() {
        unset($_SESSION['scen.all']);
        unset($_SESSION['scen.acc.view']);
        unset($_SESSION['scen.acc.edit']);
        unset($_SESSION['sceneryroles']);

        $Query = 
            "SELECT * " . 
            "FROM `scenery` " . 
            "WHERE '1' " . 
            "ORDER BY `name`;";
        $result = $this->dbquery( $Query );
        while ($sqlrow = $result->fetch_assoc()) {
            $_SESSION['scen.desc'][$sqlrow['id']] = $sqlrow['desc'] ; 
            if ($_SESSION['role']['isadmin']) {
                $_SESSION['scen.acc.view'][$sqlrow['id']] = $sqlrow['name'] ; 
                $_SESSION['scen.acc.edit'][$sqlrow['id']] = $sqlrow['name'] ; 
            }

            if ($sqlrow['hide']) {
                $_SESSION['scen.all'][$sqlrow['id']] = $sqlrow['name'] ; // all  
                if ($_SESSION['role']['isadmin']) {
                    $_SESSION['scen.acc.view'][$sqlrow['id']] = $sqlrow['name'] ; 
                }
            } else {
                $_SESSION['scen.acc.view'][$sqlrow['id']] = $sqlrow['name'] ; // all accounts can view.... used @ editclass.php
                $_SESSION['scen.all'][$sqlrow['id']] = $sqlrow['name'] ; // all 
            }
        }
        $result->close();

        $Query = 
            "SELECT DISTINCT scenery . * , " .
                    "role . can_scenery ,  " .
                    "sceneryrole . role_id " .
            "FROM `scenery` , `sceneryrole` , `accrole`  , `role` " . 
            "WHERE `scenery` . `id` = `sceneryrole` . `scenery_id` " .
                    "AND `sceneryrole` . `role_id` = `accrole` . `role_id` " .
                    "AND `accrole` . `role_id` = `role` . `id` " .
                    "AND  `accrole` . `account_id` = '$_SESSION[userid]'  " .
            "ORDER BY `name` ; " ; 

        $result = $this->dbquery( $Query );
        while ($sqlrow = $result->fetch_assoc()) {
            $_SESSION['scen.acc.edit'][$sqlrow['id']] = $sqlrow['name'] ; // all users can add/remove classes to/from => that's for editclass
            $_SESSION['scen.acc.view'][$sqlrow['id']] = $sqlrow['name'] ;

            $_SESSION['role.scen'][$sqlrow['role_id']][$sqlrow['id']] = $sqlrow['can_scenery'];
            $_SESSION['role.scen'][$sqlrow['role_id']]['can_scenery'] = $sqlrow['can_scenery'];
            if ($sqlrow['can_scenery']) {
                $_SESSION['scen.role'][$sqlrow['id']] = $sqlrow['can_scenery'];
                //$_SESSION['scen.byroles'][$sqlrow['role_id']][$sqlrow['id']] = $sqlrow['name'];
            }
        }
        $result->close();


        $Query = 
            "SELECT * " . 
            "FROM `role` " . 
            "WHERE `role` . `can_scenery` = '1' " . 
                    "AND `role` . `isadmin` = '0' " . 
            "ORDER BY `description` ; " ;
        $result = $this->dbquery( $Query );
        while ($sqlrow = $result->fetch_assoc()) {
            $_SESSION['scen.editroles'][$sqlrow['id']] = $sqlrow['description'];
              
            $Query =  
                "SELECT DISTINCT scenery . * " . 
                "FROM `scenery` , `sceneryrole` " . 
                "WHERE `sceneryrole` . `role_id` = '$sqlrow[id]' " . 
                        "AND `sceneryrole` . `scenery_id` = `scenery` . `id` " . 
                        "AND `scenery` . `hide` = '0' " . 
                "ORDER BY `name` ; " ;
            $scenresult = $this->dbquery( $Query );
            while ($sceneryrow = $scenresult->fetch_assoc()) {
                $_SESSION['scen.byroles'][$sqlrow['id']][$sceneryrow['id']] = $sceneryrow['name'];
            }                  
        }
        $result->close();

        $Query = 
            "SELECT DISTINCT r . * " . 
            "FROM role , role AS r , accrole " . 
            "WHERE `role` . `id` = `accrole` . `role_id` " . 
                    "AND `accrole` . `account_id` = $_SESSION[userid] " . 
                    "AND `role` . `unit_id` = `r` . `unit_id` " . 
                    "AND `r` . `can_scenery` = '1' " .
                    "AND `r` . `isadmin` = '0' ; "    ;
        $result = $this->dbquery( $Query );
        while ($sqlrow = $result->fetch_assoc()) {
            $_SESSION['sceneryroles'][$sqlrow['id']] = $sqlrow['description'];
        }
    }
  
  
  
    public function dbvaluesloopX($Query , $keyA , $sqlkeyA , $keyB = null , $sqlkeyB = null) {
        $result = $this->dbquery($Query);
        while ($sqlrow = $result->fetch_assoc()) {
            $_SESSION[$keyA][$sqlrow[$sqlkeyA]] = $sqlrow;
            if ($keyB) {
                $_SESSION[$keyB][$sqlrow[$sqlkeyB]] = $sqlrow;
            }
        }
        $result->close();
    }
  
    public function set_sessionXtravalues() {
        $Query = "SELECT *  FROM disciplinekind; " ;
        $this->dbvaluesloopX($Query , 'disckindbycode' , 'code');

        $Query = "SELECT *  FROM unit; " ;
        $this->dbvaluesloopX($Query , 'unitbycode' , 'code' , 'unitbyacronym' , 'acronym');
    
        $Query = "SELECT *  FROM term;";
        $this->dbvaluesloopX($Query , 'termbycode' , 'code');

        $Query = "SELECT *  FROM building;" ; 
        $result = $this->dbquery( $Query );
        while ($sqlrow = $result->fetch_assoc()) {
            $_SESSION['buildingbyacronym'][$sqlrow['acronym']] = $sqlrow;
      
            $Query = 
                "SELECT `room` . *  " . 
                "FROM `room` , `building` " . 
                "WHERE `room` . `building_id` = `building` . `id` " . 
                        "AND `building` . `id` = '$sqlrow[id]' ; " ;
            $roomquery = $this->dbquery( $Query );
            while ($sqlroom = $roomquery->fetch_assoc()) {
                $_SESSION['buildingbyacronym'][$sqlrow['acronym']]['roombyacronym'][$sqlroom['acronym']] = $sqlroom['id'];
            }
            $roomquery->close();
        }
        $result->close();
    
    }

    public function scenclass_test() {
        $Query = 
            "SELECT * FROM `sceneryclass`;";
        $result = $this->dbquery( $Query );
        return ($result->num_rows);     
    }


}

/////
/////
/////

include 'pages/coreconnect.php';



?>


