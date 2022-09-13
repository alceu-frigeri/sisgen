<?php include "proceedings-start.php"; ?>

<h2>Revisores</h2><hr>


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


    $ncols=4;
    if ($ncols == 3) {
	$coltype='col-sm-4';
    } else {
	$coltype='col-sm-3';
    }
    
    

    

    if (!($reviewersQ = $mysqli->query("SELECT DISTINCT name,email FROM reviewers ORDER BY name"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }

    
    $nlines = round(($reviewersQ->num_rows)/$ncols);
    //    echo "rows: ".$reviewersQ->num_rows." nlines: ".$nlines.'<br>';
    $cnt=0;
    echo "<div class='$coltype'>";
    while ($reviewer=$reviewersQ->fetch_assoc()) {
	if ($cnt == $nlines) {
	    echo "</div>
            <div class='$coltype'>";
	    $cnt=0;
	}
	$cnt++;
	
	//	echo utf8_decode($reviewer['name']).' :: '.utf8_decode($reviewer['email']).'<br>';
	echo utf8_decode($reviewer['name']).'<br>';
    }
    echo '</div>';


    ?>


    <?php include "proceedings-end.php"; ?>
