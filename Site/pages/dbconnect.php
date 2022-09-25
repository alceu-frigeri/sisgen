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
	
	public function dbquery($q) {
		if (!($result = $this->query($q))) {
			echo "<br>Query failed: (" . $this->errno . ") " . $this->error;
			echo "\n<br>ERR !!" . $q . "<br>\n";
		}
		return $result;
	}
	

}

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


    if (!($result = $mysqli->query("SELECT * FROM `loglevel`"))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
    }
	while (($sqlrow=$result->fetch_assoc())) {
		$var = 'LOG_'.strtoupper($sqlrow['level']);
		$DBVALS[$var] = $sqlrow['id'];
    }

    
    echo "</script>\n";

    
    return $mysqli;

}

function myXconnect() {
    global $DBVALS;

    $mysqli = mysqli_init();
    
    if (!$mysqli) {
	die('mysqli_init failed');
    }
    //   if (!$mysqli->options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
    //       die('Setting MYSQLI_INIT_COMMAND failed');
    //   }
    if (!$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
	die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
    }

    if (!$mysqli->real_connect('bdlivre.ufrgs.br', 'sisgen', 'SKRqgFBASnUS', 'sisgen')) {
	die('Connect Error (' . mysqli_connect_errno() . ') '
          . mysqli_connect_error());
    }

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


    if (!($result = $mysqli->query("SELECT * FROM `loglevel`"))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
    }
	while (($sqlrow=$result->fetch_assoc())) {
		$var = 'LOG_'.strtoupper($sqlrow['level']);
		$DBVALS[$var] = $sqlrow['id'];
    }

    
    echo "</script>\n";

    
    return $mysqli;
};

// 'replicating' some DB tables into SESSION 
// to reduce the number of DB calls (inside a SESSION)
function dbsessionset() {
}





?>


