<?php include "proceedings-start.php"; ?>

<h2>Autores</h2><hr>

<table id="proceedings">



    <?php
    $mysqli = mysqli_init();

    if (!$mysqli) {
	die('mysqli_init failed');
    }
    if (!$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
	die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
    }

    if (!$mysqli->real_connect('bdlivre.ufrgs.br', 'sbai17', 'rkefBq4HQTMH', 'sbai17')) {
	die('Connect Error (' . mysqli_connect_errno() . ') '
	  . mysqli_connect_error());
    }

    $mysqli->autocommit(TRUE);


    $str = "   <script type='text/javascript'>
    function setabstract(obj,num) {
      var spanfield = 'abstract'+num;
      if(obj.checked) {
        document.getElementById(spanfield).style.display='block';
      } else {
        document.getElementById(spanfield).style.display='none';
      }
    }

    </script>";
    echo $str;


    $num=0;
    if (!($sessionQ = $mysqli->query("SELECT * FROM sessions"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $last='';
    while ($session=$sessionQ->fetch_assoc()) {


	$session_begin = substr($session['session_time'],0,5);
	
	if (!($stmt = $mysqli->query("UPDATE sessions SET session_begin = '$session_begin' WHERE session_id = '$session[session_id]'"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}

    }


    ?>

</table>

<?php include "proceedings-end.php"; ?>
