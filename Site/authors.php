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
//    if (!($authorQ = $mysqli->query("SELECT DISTINCT author,author_id FROM authors  WHERE EXISTS (SELECT 1 FROM papers WHERE papers.paper_num = authors.paper_num AND papers.session_id != '0' AND papers.noshow = '0') ORDER BY authors.author"))) {

    if (!($authorQ = $mysqli->query("SELECT DISTINCT author,author_id FROM authors  WHERE EXISTS (SELECT 1 FROM papers WHERE papers.paper_num = authors.paper_num AND papers.session_id != '0') ORDER BY authors.author"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while ($author=$authorQ->fetch_assoc()) {
	
	if ($author['author'] == $last) {
	    continue;
	    echo "<font color='red'>  dup</font>  ";
	};

	
	//	echo "<tr><td><a href='mailto:$author[email]'>".utf8_decode($author['author']).'</a></td><td><ul>';
	echo "<tr><td><a name='author:$author[author_id]'>".utf8_decode(mb_convert_case($author['author'],MB_CASE_TITLE,'UTF-8')).'</a></td><td><ul>';



//	if (!($papersQ = $mysqli->query("SELECT * FROM authors,papers,abstracts  WHERE papers.paper_num = authors.paper_num AND papers.paper_num = abstracts.paper_num AND authors.author_id = '$author[author_id]' AND papers.session_id != '0' AND papers.noshow = '0' ORDER BY papers.paper_title"))) {
	if (!($papersQ = $mysqli->query("SELECT * FROM authors,papers,abstracts  WHERE papers.paper_num = authors.paper_num AND papers.paper_num = abstracts.paper_num AND authors.author_id = '$author[author_id]' AND papers.session_id != '0'ORDER BY papers.paper_title"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	
	while ($paper = $papersQ->fetch_assoc()) {
	    $num++;
	    $NS='';
	    if($paper['noshow']) { $NS='<font color="red"> - No Show</font>'; }
	    $paperref="<a target='_BLANK' href='papers/paper_$paper[paper_num].pdf'>#$paper[paper_num]</a>";
	    if ($paper['paper_num'] > 1000) {
		$paperref='Plenária';
		if ($paper['paper_num'] > 2000) {
		    $paperref='Mini-curso';
		}
		
	    }
	    
	    echo "<li>$paperref - ".utf8_decode(mb_convert_case($paper['paper_title'],MB_CASE_UPPER,'UTF-8'));


	    
	    echo " (sessão: <a href='program.php#session:$paper[session_id]'>$paper[session_id]</a>)$NS<br>";
	    /*
	       if (!($coauthorsQ = $mysqli->query("SELECT * FROM bacalhau WHERE paper_num = '$paper[paper_num]' ORDER BY author"))) {
	       echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	       return NULL;
	       }
	       $comma='';
	       while ($coauthor=$coauthorsQ->fetch_assoc()) {
	       echo $comma."<a href='authors.php#author:$coauthor[author_id]'>".utf8_decode($coauthor['author'])."</a>";
	       if(!($comma)) {
	       $comma=', ';
	       }
	       
	       }
	     */
	    echo "<font style='font-size:85%'><i>".utf8_decode(str_ireplace(' and ',' e ',mb_convert_case($paper['authors'],MB_CASE_TITLE,'UTF-8')))."</i></font>";

	    if ($paper['paper_num'] > 1000) {
		echo "<br><input type='checkbox' name='abstrack' id='abstract' value='$paper[paper_num]' onchange='setabstract(this,$num)'>abstract</input>";
		echo "<div id='abstract$num' hidden>";
		echo "<br><blockquote><font style='font-size:65%'><i>".utf8_decode($paper['abstract'])."</i></font></blockquote>";
		echo "</div>";
	    }
	    
	    
	}
	echo '</ul></td></tr>';
	


	$last=$author['author'];

    }


    ?>

</table>

<?php include "proceedings-end.php"; ?>
