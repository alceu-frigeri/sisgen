<?php

const T_ADMIN = 1;
const T_SEC = 2;
const T_CONG = 3;
$DBVALS = [];
function myconnect() {
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

    if (!$mysqli->real_connect('bdlivre.ufrgs.br', 'sbai17', 'rkefBq4HQTMH', 'sbai17')) {
	die('Connect Error (' . mysqli_connect_errno() . ') '
          . mysqli_connect_error());
    }

    $mysqli->autocommit(TRUE);


    echo "<script type='text/javascript'>\n";

    if (!($result = $mysqli->query("SELECT * FROM `types`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
    	$var = 'DB_'.strtoupper(utf8_decode($sqlrow['type_str']));
	echo "  var $var = $sqlrow[type_id];\n";
	$DBVALS[$var] = $sqlrow[type_id];
    }

    if (!($result = $mysqli->query("SELECT * FROM `modalities`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
	$var = 'DB_'.strtoupper(utf8_decode($sqlrow['mod_str']));
	echo "  var $var = $sqlrow[mod_id];\n";
	$DBVALS[$var] = $sqlrow[mod_id];
    }

    if (!($result = $mysqli->query("SELECT * FROM `association`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
	$var = 'DB_'.strtoupper(utf8_decode($sqlrow['assoc_str']));
	echo "  var $var = $sqlrow[assoc_id];\n";
	$DBVALS[$var] = $sqlrow[assoc_id];
    }


    if (!($result = $mysqli->query("SELECT * FROM `account_type`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
	$var = 'DB_'.strtoupper(utf8_decode($sqlrow['type_str']));
	$DBVALS[$var] = $sqlrow[type_id];
    }


    if (!($result = $mysqli->query("SELECT * FROM `loglevel`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
	$var = 'DB_'.strtoupper(utf8_decode($sqlrow['level_label']));
	$DBVALS[$var] = $sqlrow[level_id];
    }

    
    echo "</script>\n";

    
    return $mysqli;
};








?>


