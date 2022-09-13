<?php



function usr_submit($accdt,$usrdt) {
    global $mysqli;

    if (!($result = $mysqli->query("UPDATE `account` SET `submitted` = '1', `can_edit` = '0', `can_files` = '0' WHERE acc_id = '$accdt[acc_id]'"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }


}



?>
