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
    if (!($authorQ = $mysqli->query("SELECT * FROM bacalhau  ORDER BY author"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $last='';
    while ($author=$authorQ->fetch_assoc()) {


	if ($author['author'] == $last) {
	    $dup = "<font color='red'>  dup</font>";
	} else {
	    $dup = '';
	    $num++;
	};
	echo $num." - ".utf8_decode($author['author']).$dup.'<br>';

	if (!($stmt = $mysqli->prepare("UPDATE bacalhau SET author_id = ? WHERE author = ?"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if (!$stmt->bind_param('is',$num,$author['author'])) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	};
	$result  = $stmt->execute();
	//	$stmt->store_results();
	$stmt->close();

	$last=$author['author'];

    }


    ?>

</table>

<?php include "proceedings-end.php"; ?>
