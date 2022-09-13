<?php include "proceedings-start.php"; ?>
<h2>Artigos - SBAI 2017</h2><hr>






<?php


$single=0;
$first=1;
$div=0;
$noNUM=1; //to suppress >1000 numbers...
$noshow=0;

if ($_GET['ss']) {
    $noshow=1;
    
}


if($noshow) {
    $single=1;
}

function smark($txt,$color,$cnt) {
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
//    $prevplen=0;
    $blocks=array();
    if($single) {
	$blocks[$cnt] .= "<table id='proceedings'>";
    } else {
	if (!$div) {
	    $blocks[$cnt] = "<tr><th style='background-color:black'></th><th style='background-color:black'></th></tr>".
			    "<tr><th style='background-color:black'></th><th style='background-color:black'><font color='white' sytle='font-size:140%'><b>$weekdays[$weekday] - 10/1/2017</b></font></th></tr>";
	}
	
    }

    
    while ($session=$sessionQ->fetch_assoc()) {
	$session['session_date']=preg_replace('/10\/(\d{1})\/2017/','\1/10/2017',$session['session_date']);


	if($session['session_date'] != $sdate) {
	    $cnt++;
	    $addplenaria=0;
	    if ((!($single)) & $div) {
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
	} else {
	    $addplenaria=1;
//	    if ($prevplen) {
//		$cnt++;
//		$prevplen=0;
//	    }
	    
	}


	
	$sdate=$session['session_date'];

	$title=utf8_decode($session['session_title']);
	//	echo ":: $title :: ";
	$title = preg_replace('/Mesa Redonda.*/','Mesa Redonda',$title);
	$title = preg_replace('/Plen.ria.*/','Plenaria',$title);
	//echo "$title";
	switch ($title) {
	    case 'Coffee Break' :
		$cnt++;
		$blocks[$cnt] .=  smark("Coffee Break<br>$session[session_date] - $session[session_time]",'lightblue',$cnt);
		break;
	    case 'Almoço' :
	    case 'Jantar de Confraternização' :
	    case 'Coquetel de Abertura' :
		$cnt++;
		$blocks[$cnt] .=  smark(utf8_decode($session[session_title])."<br>$session[session_date] - $session[session_time]",'blue',$cnt);
		break;
	    case 'Mesa Redonda' :
	    case 'Encerramento' :
	    case 'Sessão de Abertura' :
	    case 'Assembleia SBA' :
		$cnt++;
		$style =  " style='background-color:green'";
		$blocks[$cnt] .=  "<tr><th $style></th><th $style><b>Sessão : <font color='black'><a name='session:$session[session_id]' style='color:black'>$session[session_id]</a></font></b></th></tr>";
		$chair='';
		if ($session['session_chair']) {
		    if($title == 'Mesa Redonda') {
			$chair="<br>Mediador: ".utf8_decode($session['session_chair']);
		    } else {
			$chair="<br>Chair: ".utf8_decode($session['session_chair']);
		    }
		    
		}
		$cochair='';
		if ($session['session_cochair']) {
		    $cochair="<br>Co-Chair: ".utf8_decode($session['session_cochair']);
		}
		
		$blocks[$cnt] .=  "<tr><th $style></th><th $style><font color='darkred'>".utf8_decode($session['session_title']).'</font>'.$chair.$cochair."<br>Local: <font color='black'>".utf8_decode($session['session_local'])."</font><br>(".utf8_decode($session['session_date'])." - ".utf8_decode($session['session_time']).")</th></tr>";
		break;
	    case 'Plenaria':
		$cnt += $addplenaria;
//		$prevplen=1;
	    default:
		$blocks[$cnt] .=  "<tr><th></th><th><b>Sessão : <font color='black'><a name='session:$session[session_id]' style='color:black'>$session[session_id]</a></font></b></th></tr>";
		$chair='';
		if ($session['session_chair']) {
		    $chair="<br>Chair: ".utf8_decode($session['session_chair']);
		}
		$cochair='';
		if ($session['session_cochair']) {
		    $cochair="<br>Co-Chair: ".utf8_decode($session['session_cochair']);
		}
		
		$blocks[$cnt] .=  "<tr><th></th><th><font color='darkred'>".utf8_decode($session['session_title']).'</font>'.$chair.$cochair."<br>Local: <font color='black'>".utf8_decode($session['session_local'])."</font><br>(".utf8_decode($session['session_date'])." - ".utf8_decode($session['session_time']).")</th></tr>";
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
	    
//	    if ($paper['noshow']) {
//	     $status.="<br><b><font color='red'>NO SHOW</font><b>";
//	    }


	    if (!($abstractsQ = $mysqli->query("SELECT * FROM abstracts WHERE paper_num = '$paper[paper_num]'"))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
	    }


	    
	    $abstract=$abstractsQ->fetch_assoc();

	    $paperref="<a target='_BLANK' href='papers/paper_$paper[paper_num].pdf'>#$paper[paper_num]</a>";
	    if($noNUM) {
	    	if ($paper[paper_num] > 1000) {
	    	    $paperref="";
	    	}
	    }
	    $NS = '';
	    if ($paper['noshow']) {
	    $NS = "<font color='red'>No Show</font>";
	    }
/*
	    if ($paper['noshow']) {
	    $blocks[$cnt] .=  "<tr><td></td><td><font color='red'>No Show</font>$paperref</td></tr>";
	    $blocks[$cnt] .=  "<tr><td></td><td></td></tr>";
	    } else {
	    $blocks[$cnt] .=  "<tr><td>$paperref</td><td>".utf8_decode(mb_convert_case($paper['paper_title'],MB_CASE_UPPER,'UTF-8'))."</td></tr>";
	    } 
*/

	    $blocks[$cnt] .=  "<tr><td>$paperref $NS</td><td>".utf8_decode(mb_convert_case($paper['paper_title'],MB_CASE_UPPER,'UTF-8'))."</td></tr>";


	    $blocks[$cnt] .=  "<tr><td>$status $NS</td><td>";
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
	    //	    $blocks[$cnt] .=  "<font sytle='font-size:70%'><i>".ucwords(mb_strtolower(utf8_decode($abstract['authors']),'iso-8859-1')).'</i></font>';
	    //	    $blocks[$cnt] .=  "<font sytle='font-size:70%'><i>".utf8_decode(str_ireplace(' and ',' e ',ucwords(mb_strtolower(($abstract['authors']),'UTF-8')))).'</i></font>';

/*
	    if ($paper['noshow']) {
	    $blocks[$cnt] .= "<font color='red' style='font-size:90%'>NO SHOW</font>";
	    } else {
	    $blocks[$cnt] .=  "<font sytle='font-size:70%'><i>".utf8_decode(str_ireplace(' and ',' e ',mb_convert_case($abstract['authors'],MB_CASE_TITLE,'UTF-8'))).'</i></font>';
	    };
*/
	    $blocks[$cnt] .=  "<font sytle='font-size:70%'><i>".utf8_decode(str_ireplace(' and ',' e ',mb_convert_case($abstract['authors'],MB_CASE_TITLE,'UTF-8'))).'</i></font> ';


	    if (($paper[paper_num] > 1000) && ($paper[paper_num] < 3000)) {
		$blocks[$cnt] .=  "<br><input type='checkbox' name='abstrack' id='abstract' value='$paper[paper_num]' onchange='setabstract(this,$paper[paper_num])'>abstract</input>";
		$blocks[$cnt] .=  "<div id='paper$paper[paper_num]' hidden>";
		$blocks[$cnt] .=  "<br><blockquote><font style='font-size:65%'><i>".utf8_decode($abstract['abstract'])."</i></font></blockquote>";
		$blocks[$cnt] .=  "</div>";
	    }
	    if($noshow AND ($paper[paper_num] < 1000)) {

		if (!($speakerQ = $mysqli->query("SELECT * FROM acc_papers,userdata WHERE acc_papers.acc_id = userdata.acc_id AND acc_papers.paper_num = '$paper[paper_num]'"))) {
		    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		    return NULL;
		}
		$speaker=$speakerQ->fetch_assoc();
		if($speaker['self']) {
		    $speakername=utf8_decode(str_ireplace(' and ',' e ',mb_convert_case($speaker['fullname'],MB_CASE_TITLE,'UTF-8')));
		} else {
		    $speakername=utf8_decode(str_ireplace(' and ',' e ',mb_convert_case($speaker['speaker'],MB_CASE_TITLE,'UTF-8')));
		};
		
		

		
		$blocks[$cnt] .= "<br>Apresentado por:<br><input type='checkbox' name='speak' id='speak'>$speakername</input><br><input type='checkbox' name='speak' id='speak'>Outra Pessoa:____________________________</input><br><input type='checkbox' name='noshow' id='noshow' value='$paper[paper_num]'>No Show</input><hr>";
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
	if ($div) {
	    $blocks[$cnt] .=  "</table></div>";
	    $i=1;
	    while ($i <= $cnt) {
		echo $blocks[$i];
		$i++;
	    }
	} else {
	    echo "<div class='col-sm-3'> 
                   <table id='proceedingsX'>$blocks[0]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[5]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[12]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[19]</table>
                  </div>";
	    echo "</div><div class='row'>";
	    echo "<div class='col-sm-3'>  
                   <table id='proceedingsX'>$blocks[0]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[6]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[13]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[20]</table>
                  </div>";
	    echo "</div><div class='row'>";
	    echo "<div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[1]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[7]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[14]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[21]</table>
                  </div>";
	    echo "</div><div class='row'>";
	    echo "<div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[2]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[8]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[15]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[22]</table>
                  </div>";
	    echo "</div><div class='row'>";
	    echo "<div class='col-sm-3'>

                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[9]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[16]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[23]</table>
                  </div>";
	    echo "</div><div class='row'>";
	    echo "<div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[3]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[10]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[17]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[24]</table>
                  </div>";
	    echo "</div><div class='row'>";
	    echo "<div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[4]</table>
                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[11]</table>
                  </div>
                  <div class='col-sm-3'>

                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[25]</table>
                  </div>";
	    echo "</div><div class='row'>";
	    echo "<div class='col-sm-3'>

                  </div>
                  <div class='col-sm-3'>

                  </div>
                  <div class='col-sm-3'>
                   <table id='proceedingsX'>$blocks[18]</table>
                  </div>
                  <div class='col-sm-3'>

                  </div>";
	    
	}
	
    }
    
    

}


?>

<?php include "proceedings-end.php"; ?>
