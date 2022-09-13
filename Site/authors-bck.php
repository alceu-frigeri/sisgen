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
    if (!($authorQ = $mysqli->query("SELECT * FROM bacalhau  WHERE EXISTS (SELECT 1 FROM papers WHERE papers.paper_num = bacalhau.paper_num AND papers.session_id != '0') ORDER BY bacalhau.author"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while ($author=$authorQ->fetch_assoc()) {
	
	if ($author['author'] == $last) {
	    continue;
	    echo "<font color='red'>  dup</font>  ";
	};

	
	echo "<tr><td><a href='mailto:$author[email]'>".utf8_decode($author['author']).'</a></td><td><ul>';



	if (!($stmt = $mysqli->prepare("SELECT papers.paper_title,papers.paper_num,abstracts.abstract FROM bacalhau,papers,abstracts  WHERE papers.paper_num = bacalhau.paper_num AND papers.paper_num = abstracts.paper_num AND bacalhau.author = ? ORDER BY papers.paper_title"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if (!$stmt->bind_param('s',$author['author'])) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	};
	$result  = $stmt->execute();
//	$stmt->store_results();
	$stmt->bind_result($papertitle,$papernum,$paperabstract);

	//	$stmt->close();
	
	while ($stmt->fetch()) {
	    $num++;
	    echo "<li><a href='papers/paper_$papernum.pdf'>#$papernum</a> - ".utf8_decode($papertitle);


	    /*	    
	       if (!($authorsQ = $mysqli->query("SELECT * FROM bacalhau WHERE paper_num = '$papernum' ORDER BY author"))) {
	       echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	       return NULL;
	       }
	       echo "<br>";
	       $comma='';
	       while ($author=$authorsQ->fetch_assoc()) {
	       echo $comma."<a href='mailto:".$author['email']."'>".utf8_decode($author['author'])."</a>";
	       if(!($comma)) {
	       $comma=', ';
	       }
	       
	       }
	       //	    $authorsQ->close();
	     */

	    
	    echo "<br><input type='checkbox' name='abstrack' id='abstract' value='$paper[paper_num]' onchange='setabstract(this,$num)'>abstract</input>";
	    echo "<div id='abstract$num' hidden>";
	    echo "<br><blockquote><font style='font-size:65%'><i>".utf8_decode($paperabstract)."</i></font></blockquote>";
	    echo "</div>";
	    
	}
	echo '</ul></td></tr>';
	


	$last=$author['author'];

    }


    ?>

</table>
    
<?php include "proceedings-end.php"; ?>
