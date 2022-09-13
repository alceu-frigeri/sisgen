<?php include "proceedings-start.php"; ?>
<h2>Proceedings - SBAI 2017</h2><hr>






<?php

$single=0;
$first=1;

function smark($txt,$color) {
    $str = "<tr><th style='background-color:$color'></th><th style='background-color:$color'></th></tr>
       <tr><th style='background-color:$color'></th><th style='background-color:$color'><font color='black' sytle='font-size:140%'><b>$txt</b></font></th></tr>";
    return $str;
}


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
      var spanfield = 'paper'+num;
      if(obj.checked) {
        document.getElementById(spanfield).style.display='block';
      } else {
        document.getElementById(spanfield).style.display='none';
      }
    }

    </script>";
echo $str;


if (!($trackQ = $mysqli->query("SELECT * FROM `tracks` WHERE proceedings = '1' ORDER BY track_seq"))) {
    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    return NULL;
}
while ($track=$trackQ->fetch_assoc()) {

    //	echo "<tr style=\"background-color:grey;\"><td></td><td>".utf8_decode($track['track_title'])."<br>Chair: ".utf8_decode($track['track_chair'])."</td></tr>";


    if (!($sessionQ = $mysqli->query("SELECT * FROM `sessions` WHERE track_id = $track[track_id] ORDER BY session_date, session_begin, session_id"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }

    
    $sdate='';
    $weekdays = array( 1 => 'Domingo',2 =>'Segunda',3 =>'Terça',4 =>'Quarta',5 =>'Quinta',6 =>'Sexta',7 =>'Sábado');
    $weekday=1;

    $cnt=0;
    $blocks=array();
    if($single) {
	$blocks[$cnt] .= "<table id='proceedings'>";
    }

    while ($session=$sessionQ->fetch_assoc()) {
	if($session['session_date'] != $sdate) {
	    $cnt++;
	    if (!($single)) {
		$protab="
            <div class='col-sm-3'>

            <table id='proceedingsX'>";
		if ($first) {
		    $blocks[$cnt] .= $protab;
		    $first = 0;
		} else {
		    $blocks[$cnt] .=  '</table></div>
                         '.$protab;
		}
	    }
	    
	    $blocks[$cnt] .=  "<tr><th style='background-color:black'></th><th style='background-color:black'></th></tr>";
	    $blocks[$cnt] .=  "<tr><th style='background-color:black'></th><th style='background-color:black'><font color='white' sytle='font-size:140%'><b>$weekdays[$weekday] - $session[session_date]</b></font></th></tr>";
	    $weekday++;
	}
	$sdate=$session['session_date'];

	switch (utf8_decode($session['session_title'])) {
	    case 'Coffee Break' :
		$blocks[$cnt] .=  smark("Coffee Break<br>$session[session_date] - $session[session_time]",'lightblue');
		break;
	    case 'Almoço' :
	    case 'Jantar de Confraternização' :
	    case 'Coquetel de Abertura' :
		$blocks[$cnt] .=  smark(utf8_decode($session[session_title])."<br>$session[session_date] - $session[session_time]",'blue');
		break;
	    case 'Mesa Redonda 01' :
	    case 'Mesa Redonda 02' :
	    case 'Mesa Redonda 03' :
	    case 'Encerramento' :
	    case 'Sessão de Abertura' :
	    case 'Assembleia SBA' :
		$style =  " style='background-color:green'";
		$blocks[$cnt] .=  "<tr><th $style></th><th $style><b>Sessão : <font style='font-color:black'><a name='session:$session[session_id]' style='color:black'>$session[session_id]</a></font></b></th></tr>";
		$chair='';
		if ($session['session_chair']) {
		    $chair="<br>Chair: ".utf8_decode($session['session_chair']);
		}
		$cochair='';
		if ($session['session_cochair']) {
		    $cochair="<br>Co-Chair: ".utf8_decode($session['session_cochair']);
		}
		
		$blocks[$cnt] .=  "<tr><th $style></th><th $style>".utf8_decode($session['session_title']).$chair.$cochair."<br>Local: ".utf8_decode($session['session_local'])."<br>(".utf8_decode($session['session_date'])." - ".utf8_decode($session['session_time']).")</th></tr>";
		break;
		
	    default:
		$blocks[$cnt] .=  "<tr><th></th><th><b>Sessão : <font style='font-color:black'><a name='session:$session[session_id]' style='color:black'>$session[session_id]</a></font></b></th></tr>";
		$chair='';
		if ($session['session_chair']) {
		    $chair="<br>Chair: ".utf8_decode($session['session_chair']);
		}
		$cochair='';
		if ($session['session_cochair']) {
		    $cochair="<br>Co-Chair: ".utf8_decode($session['session_cochair']);
		}
		
		$blocks[$cnt] .=  "<tr><th></th><th>".utf8_decode($session['session_title']).$chair.$cochair."<br>Local: ".utf8_decode($session['session_local'])."<br>(".utf8_decode($session['session_date'])." - ".utf8_decode($session['session_time']).")</th></tr>";
		break;
		
	}
	
	

	if (!($paperQ = $mysqli->query("SELECT * FROM papers WHERE papers.session_id = '$session[session_id]' ORDER BY paper_time, paper_title"))) {
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    return NULL;
	}
	while ($paper=$paperQ->fetch_assoc()) {
	    if (!($accountQ = $mysqli->query("SELECT * FROM acc_papers,account WHERE acc_papers.paper_num = '$paper[paper_num]' AND acc_papers.acc_id = account.acc_id"))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
	    }
	    if ($accountQ->num_rows) {
		$account=$accountQ->fetch_assoc();
		if ($account['confirmed']) {
		    $status=$paper['paper_time'];
		} else {
		    if ($account['submitted']) {
			$status='pending';
		    } else {
			$status="<b><font color='red'>não sub.</font><b>";
		    }
		    
		    
		}
		
		
	    } else {
		$status="<b><font color='red'>NONE</font><b>";
	    }


	    if (!($abstractsQ = $mysqli->query("SELECT * FROM abstracts WHERE paper_num = '$paper[paper_num]'"))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
	    }


	    
	    $abstract=$abstractsQ->fetch_assoc();

	    $paperref="<a target='_BLANK' href='papers/paper_$paper[paper_num].pdf'>#$paper[paper_num]</a>";
	    //		if ($paper[paper_num] > 1000) {
	    //		    $paperref="";
	    //		}
	    
	    $blocks[$cnt] .=  "<tr><td>$paperref</td><td>".ucwords(mb_strtolower(utf8_decode($paper[paper_title]),'iso-8859-1'))."</td></tr>";
	    

	    $blocks[$cnt] .=  "<tr><td>$status</td><td>";
	    /*
	       if (!($authorsQ = $mysqli->query("SELECT * FROM bacalhau WHERE paper_num = '$paper[paper_num]' ORDER BY author"))) {
	       echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	       return NULL;
	       }
	       $comma='';
	       while ($author=$authorsQ->fetch_assoc()) {
	       echo $comma."<a href='authors.php#author:$author[author_id]'>".utf8_decode($author['author'])."</a>";
	       if(!($comma)) {
	       $comma=', ';
	       }
	       
	       }
	     */
	    $blocks[$cnt] .=  "<font sytle='font-size:70%'><i>".utf8_decode($abstract['authors']).'</i></font>';
	    if ($paper[paper_num] > 1000 ) {
		$blocks[$cnt] .=  "<br><input type='checkbox' name='abstrack' id='abstract' value='$paper[paper_num]' onchange='setabstract(this,$paper[paper_num])'>abstract</input>";
		$blocks[$cnt] .=  "<div id='paper$paper[paper_num]' hidden>";
		$blocks[$cnt] .=  "<br><blockquote><font style='font-size:65%'><i>".utf8_decode($abstract['abstract'])."</i></font></blockquote>";
		$blocks[$cnt] .=  "</div>";
	    }
	    
	    $blocks[$cnt] .=  "</td></tr>";
	    
	    
	}
	

	
	
    }
    if ($single) {
	$blocks[$cnt] .= "</table>";
	$i=0;
	while ($i <= $cnt) {
	    echo $blocks[$i];
	    $i++;
	}
	
    } else {
	$blocks[$cnt] .=  "</table></div>";
	$i=1;
	while ($i <= $cnt) {
	    echo $blocks[$i];
	    $i++;
	}
    }
    
    

}


?>

<?php include "proceedings-end.php"; ?>
