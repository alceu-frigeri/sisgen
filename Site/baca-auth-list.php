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


    function insert_author($author,$paper_num) {
	global $mysqli;

	if (!($stmt = $mysqli->prepare("INSERT INTO authors (author, paper_num) VALUES (?,?)"))) {
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    return NULL;
	}
	if (!$stmt->bind_param('si',$author,$paper_num)) {
     	    echo "Binding parameters update failed: (" . $stmt->errno . ") " . $stmt->error;
   	};
	if(!$stmt->execute()) {
	    echo "Execute INSERT failed: (" . $stmt->errno . ") " . $stmt->error;
	};
	
	$stmt->close();


    }

    $create = 0;
    if ($create) {

	if (!($mysqli->query("DELETE FROM authors WHERE 1"))) {
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    return NULL;
	}

	
	
	if (!($paperQ = $mysqli->query("SELECT * FROM papers,abstracts WHERE papers.session_id != '0' AND papers.paper_num = abstracts.paper_num"))) {
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    return NULL;
	}

	while ($paper=$paperQ->fetch_assoc()) {
	    //	echo 'list:'.utf8_decode($paper['authors']).' : ';
	    $authors=explode(',',$paper['authors']);
	    foreach($authors as $author) {
		//	    echo '('.$author.')';
		if(preg_match('/\band\b/i',$author)) {
		    //		echo ' :: ';
		    $authors2=explode(' and ',$author);
		    foreach ($authors2 as $author2) {
			//		    echo '['.$author2.']';
			insert_author(trim($author2),$paper['paper_num']);
		    }
		} else {
		    insert_author(trim($author),$paper['paper_num']);
		}
		
	    }
	    //	echo '<br>';
	    
	}
    }
    

    
    $renumerate=0;
    $num=0;
    if (!($authorQ = $mysqli->query("SELECT * FROM authors  ORDER BY author"))) {
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
	echo $num." - ".utf8_decode($author['author']).$dup.'<br>'; //.'(paper:'.$author['paper_num'].')<br>';

	if($renumerate) {

	    if (!($stmt = $mysqli->prepare("UPDATE authors SET author_id = ? WHERE author = ?"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	    if (!$stmt->bind_param('is',$num,$author['author'])) {
		echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	    };
	    $result  = $stmt->execute();
	    //	$stmt->store_results();
	    $stmt->close();
	}
	$last=$author['author'];
	
    }


    ?>

</table>

<?php include "proceedings-end.php"; ?>
