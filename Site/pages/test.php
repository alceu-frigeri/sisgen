<?php include "FLAGS.php" ?>

<?php

$mysqli = mysqli_init();
if (!$mysqli) {
    die('mysqli_init failed');
}

if (!$mysqli->options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
    die('Setting MYSQLI_INIT_COMMAND failed');
}

if (!$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
    die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
}

if (!$mysqli->real_connect('bdlivre.ufrgs.br', 'sbai17', 'rkefBq4HQTMH', 'sbai17')) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
            . mysqli_connect_error());
}

echo 'Success... ' . $mysqli->host_info . "<br>\n";


$sql = "SELECT * FROM `account`, `account_type` WHERE `account`.`type` = `account_type`.`type`";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while($row = $result->fetch_assoc()) {
        echo "acc_id:$row[acc_id] email:$row[email] str:$row[str] desc:$row[description]<br>\n";
    }
} else {
    echo "0 results";
}

echo "md5(admin)".md5('alceu.frigeri@live.com admin')."<br>\n";
echo "md5(sec)".md5('sbai17@ufrgs.br sec')."<br>\n";
echo "md5(admin)".md5('admsbai17')."<br>\n";
echo "md5(sec)".md5('sec-sbai17')."<br>\n";

$mysqli->close();
?>


<?php
// the message
$msg = "First line of text\nSecond line of text";

// use wordwrap() if lines are longer than 70 characters
$msg = wordwrap($msg,70);

$headers = "From: jmgomes@ufrgs.br" . "\r\n" .
"CC: alceu.frigeri@live.com";

// send email
//mail("alceu.frigeri@ufrgs.br","My test subject",$msg,$headers);
?>
