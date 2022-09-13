<?php

function pagesvalue($type,$sba,$mod,$npapers,$xpags,$minival) {
    global $DBVALS;
    $val = $minival;
    if(($mod == $DBVALS['DB_PROFESSIONAL']) && ($type == $DBVALS['DB_EARLY'])) {
	$val += 50*$xpags;
	if ($npapers > 2) {
      	    $val += 250*($npapers - 2);
	}
    }
    return $val;
}



function usrreg_updt($accdt,$usrdt,$admin=false) {
    global $mysqli;
    global $MAXPAPERS;
    global $debug;
    global $DBVALS;
    global $uploaddir;
    
    if(($_POST['type'] == $DBVALS['DB_LATE']) || ($_POST['mod'] == $DBVALS['DB_STUDENT'])) {
	$_POST['npapers']=0;
    };
    for ($i=1;$i<=$_POST['npapers'];$i++) {
    	$selffield = 'paper'.$i.'self';
    	$speakerfield = 'speaker'.$i;
	if ($_POST[$selffield]) {
	    $_POST[$speakerfield] = '';
	}
    }

    
    $xpags=0;
    $minival=0;
    $obs = utf8_encode($_POST['obs']);
    
    if ($usrdt) {
     	if (!($stmt = $mysqli->prepare("UPDATE `userdata` SET `type_id` = ?, `assoc_id` = ?, `mod_id` = ?, `num_papers` = ?, `acc_obs` = ?, `usrdata_admin` = ? WHERE `acc_id` = ?;"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
   	}
	//	echo "<br>type:$_POST[type]  sba:$_POST[sba] mod:$_POST[mod] npapers:$_POST[npapers] acc_id:$accdt[acc_id]<br>\n";
	if (!$stmt->bind_param('iiiisii',$_POST['type'],$_POST['sba'],$_POST['mod'],$_POST['npapers'],$obs,$admin,$accdt['acc_id'])) {
     	    echo "Binding parameters update failed: (" . $stmt->errno . ") " . $stmt->error;
   	};
	if(!$stmt->execute()) {
	    echo "Execute UPDATE failed: (" . $stmt->errno . ") " . $stmt->error;
	};

	if($_POST['sba'] != $DBVALS['DB_SBA']) {
	    if($usrdt['sba_file']) {
		unlink($uploaddir.$usrdt['sba_file']);
		if(!($mysqli->query("UPDATE `userdata` SET sba_file = '' WHERE `acc_id` = $accdt[acc_id];"))) {
		    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	    }
	}
	
	if($_POST['mod'] != $DBVALS['DB_STUDENT']) {
	    if($usrdt['student_file']) {
		unlink($uploaddir.$usrdt['student_file']);
		if(!($mysqli->query("UPDATE `userdata` SET student_file = '' WHERE `acc_id` = $accdt[acc_id];"))) {
		    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	    }
	} else {
	    if($usrdt['copy_file']) {
		unlink($uploaddir.$usrdt['copy_file']);
		if(!($mysqli->query("UPDATE `userdata` SET copy_file = '' WHERE `acc_id` = $accdt[acc_id];"))) {
		    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
	    }
	}
	



	

    } else {
	echo "INSERT<br>";
     	if (!($stmt = $mysqli->prepare("INSERT INTO `userdata` (`type_id`, `assoc_id`, `mod_id`, `num_papers`, `acc_obs`, `usrdata_admin`, `acc_id`) VALUES (?,?,?,?,?,?,?);"))) {
	    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
   	}
	if (!$stmt->bind_param('iiiisii',$_POST['type'],$_POST['sba'],$_POST['mod'],$_POST['npapers'],$obs,$admin,$accdt['acc_id'])) {
     	    echo "Binding parameters update failed: (" . $stmt->errno . ") " . $stmt->error;
   	};
	if(!$stmt->execute()) {
	    echo "Execute INSERT failed: (" . $stmt->errno . ") " . $stmt->error;
	};
    }
    $stmt->close();

    if(!($mysqli->query("UPDATE `account` SET can_files = 1 WHERE `acc_id` = $accdt[acc_id];"))) {
        echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    
    if(!($mysqli->query("DELETE FROM `acc_papers` WHERE `acc_id` = $accdt[acc_id];"))) {
        echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    if (!($stmt = $mysqli->prepare("INSERT INTO `acc_papers` (`acc_id`, `paper_num`, `xtra_pages`, `self`, `speaker`) VALUES (?,?,?,?,?);"))) {
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    for ($i=1;$i<=$_POST['npapers'];$i++) {
	$xtrafield='xtrapag'.$i;
	$selffield='paper'.$i.'self';
	$speakerfield='speaker'.$i;
	$paperfield='paperID'.$i;
	$speaker=utf8_encode($_POST[$speakerfield]);

	$xpags += $_POST[$xtrafield];

	//	echo "self:$selffield($_POST[$selffield]) xtra:$xtrafield($_POST[$xtrafield]) speaker:$speakerfield($_POST[$speakerfield])<br>\n";
	if (!$stmt->bind_param('iiiis',$accdt['acc_id'],$_POST[$paperfield],$_POST[$xtrafield],$_POST[$selffield],$speaker)) {
     	    echo "Binding parameters update failed: (" . $stmt->errno . ") " . $stmt->error;
	};
	if(!$stmt->execute()) {
	    echo "Execute INSERT paper list failed: (" . $stmt->errno . ") " . $stmt->error;
	};
	
    };
    $stmt->close();
    
    if (empty($_POST['mini'])) {
	if(!($mysqli->query("DELETE FROM `acc_mini` WHERE `acc_id`  = $accdt[acc_id];"))) {
            echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
    } else {
	if (!($result = $mysqli->query("SELECT * FROM `acc_mini` WHERE `acc_id` = '$accdt[acc_id]';"))) {
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    return NULL;
	}
	$miniorg=[];
	while($sqlrow = $result->fetch_assoc()) {
	    $org=false;
	    foreach ($_POST['mini'] as $miniID) {
		if ($miniID == $sqlrow['course_id']) {
		    $org=true;
		}
	    }
	    if(!$org) {
		//		echo "Deleting $sqlrow[course_id]<br>\n";
		if(!($mysqli->query("DELETE FROM `acc_mini` WHERE `acc_id`  = $accdt[acc_id] AND `course_id` = '$sqlrow[course_id]';"))) {
		    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		
	    }
	    
	}
	$result->close();
	
	foreach ($_POST['mini'] as $miniID) {
	    if (!($result = $mysqli->query("SELECT * FROM `acc_mini` WHERE `acc_id` = '$accdt[acc_id]' AND `course_id` = $miniID;"))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    }
	    if ($result->num_rows == 0) {
		//		echo "inserting $miniID<br>\n";
		if (!($stmt = $mysqli->prepare("INSERT INTO `acc_mini` (`acc_id`, `course_id`, `mini_stamp`, `mini_admin`) VALUES (?,?,NOW(),?);"))) {
		    echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
		}
		//		echo "Mini: $miniID<br>\n";
		$adminval = ($admin ? '1' : '0');
		if (!$stmt->bind_param('iii',$accdt['acc_id'],$miniID,$adminval)) {
     		    echo "Binding parameters update failed: (" . $stmt->errno . ") " . $stmt->error;
		};
		if(!$stmt->execute()) {
		    echo "Execute INSERT mini course failed: (" . $stmt->errno . ") " . $stmt->error;
		};

	    }
	    $result->close();
	}

	foreach ($_POST['mini'] as $miniID) {
	    if (!($result = $mysqli->query("SELECT * FROM `acc_mini`,`mini_courses` WHERE acc_mini.course_id = '$miniID' AND `acc_id` = '$accdt[acc_id]' AND mini_courses.course_id = acc_mini.course_id;"))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
	    }
	    $sqlrow=$result->fetch_assoc();
	    
	    if(!$sqlrow['reserved']) {
		if (!($admin)) {
		    
		    $slots=$sqlrow['course_slots'];
		    $result->close();
		    
		    //		echo "Locking for($miniID)...<br>\n";
		    if(!($mysqli->query("LOCK TABLES `acc_mini`  WRITE;"))) {
	    		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>\n";
		    }
		    
		    
		    //	    echo "about to sleep...<br>\n";
		    //	    	    sleep(15);
		    
		    if(!($result = $mysqli->query("SELECT COUNT(*) AS count FROM `acc_mini` WHERE `course_id` = '$miniID' AND `reserved` = '1';"))) {
			echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>\n"; 
		    }
		    $sqlcount = $result->fetch_assoc();
		    //		echo "count of $miniID : $sqlrow[count]<br>\n";
		    if ($sqlcount['count'] < $slots) {
			//		    echo "done<br>\n";
			if(!($mysqli->query("UPDATE `acc_mini`  SET reserved = '1', waitlist = '0' WHERE `acc_id` = '$accdt[acc_id]' AND `course_id` = '$miniID';"))) {
			    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>\n"; 
			}
			$minival += $sqlrow['course_val'];
			//		    echo 'new+<br>\n';
		    } else {
			if(!($mysqli->query("UPDATE `acc_mini`  SET waitlist = '1' WHERE `acc_id` = '$accdt[acc_id]' AND `course_id` = '$miniID';"))) {
			    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>\n"; 
			}
		    }
		    
		    //		echo "awake...<br>\n";
		    if(!($mysqli->query("UNLOCK TABLES;"))) {
	    		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>\n";
		    }
		    //		echo "UNLOCKED...<br>\n";
		} else {
		    if(!($mysqli->query("UPDATE `acc_mini`  SET reserved = '1', waitlist = '0', mini_admin = '1', overbook = '1'  WHERE `acc_id` = '$accdt[acc_id]' AND `course_id` = '$miniID';"))) {
			echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>\n"; 
		    }
		    $minival += $sqlrow['course_val'];
		}
		
	    } else {
		//		echo "already reserved $miniID<br>\n";
		$minival += $sqlrow['course_val'];
		//		echo 'old+<br>\n';
	    }
	}
    }

    /////////
    if (!($result = $mysqli->query("SELECT * FROM `baseprices` WHERE type_id = '$_POST[type]' AND assoc_id = '$_POST[sba]' AND mod_id = '$_POST[mod]';"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $sqlrow=$result->fetch_assoc();
    
    $value = $sqlrow['value']+pagesvalue($_POST['type'],$_POST['sba'],$_POST['mod'],$_POST['npapers'],$xpags,0);
    $total = $value + $minival;
    if (!($stmt = $mysqli->query("UPDATE `userdata` SET `subs_value` = '$value', `mini_value` = '$minival', `sec_value` =  '$total' WHERE `acc_id` = '$accdt[acc_id]';"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    


    
}

function usrreg_edit($accdt,$usrdt,$admin='') {
    global $mysqli;
    global $MAXPAPERS;
    global $ADMINMAXPAPERS;
    global $DBVALS;
    global $early;


    echo "<h2>Sistema de Inscrição</h2>
	<hr>
	<h3>Dados de Inscrição</h3>
	";

    regform('usrreg_updt','return validate()');
    
    echo "
	<table id='regform' style='width:70%'>
	<tr>
	<td style='text-align:right; width:30%'>Tipo Inscrição:</td><td style='text-align:left'>\n";

    if (!($result = $mysqli->query("SELECT * FROM `types`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $auxflag='';
    while (($sqlrow=$result->fetch_assoc())) {
	if (!$early && ($sqlrow['type_id'] == $DBVALS['DB_EARLY']) && !$admin) {
	    $auxflag='checked';
	    continue;
	}
	if ($sqlrow['type_id'] == $usrdt['type_id']) {
	    echo "<input type='radio' name='type' id='type' value='$sqlrow[type_id]' onclick=\"settype('$sqlrow[type_id]')\" checked>".utf8_decode($sqlrow[type_desc])."</input><br>\n";
	} else {
	    echo "<input type='radio' name='type' id='type' value='$sqlrow[type_id]' onclick=\"settype('$sqlrow[type_id]')\" $auxflag>".utf8_decode($sqlrow[type_desc])."</input><br>\n";
	}
	
    }
    $result->close();
    echo "</td></tr>
     <tr>
     <td style='text-align:right; width:30%'>Associação:</td><td style='text-align:left'>\n";

    if (!($result = $mysqli->query("SELECT * FROM `association`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
	if ($sqlrow['assoc_id'] == $usrdt['assoc_id']) {
	    echo "<input type='radio' name='sba' id='sba' value='$sqlrow[assoc_id]' onclick=\"setsba('$sqlrow[assoc_id]')\" checked>".utf8_decode($sqlrow[assoc_desc])."</input><br>\n";
	} else {
	    echo "<input type='radio' name='sba' id='sba' value='$sqlrow[assoc_id]' onclick=\"setsba('$sqlrow[assoc_id]')\">".utf8_decode($sqlrow[assoc_desc])."</input><br>\n";
	}

    }
    $result->close();
    echo "</td></tr>
     <tr>
     <td style='text-align:right; width:30%'>Modalidade:</td><td style='text-align:left'>\n";

    if (!($result = $mysqli->query("SELECT * FROM `modalities`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
	if ($sqlrow['mod_id'] == $usrdt['mod_id']) {
	    echo "<input type='radio' name='mod' id='mod' value='$sqlrow[mod_id]' onclick=\"setmod('$sqlrow[mod_id]')\" checked>".utf8_decode($sqlrow[mod_desc])."</input><br>\n";
	} else {
	    echo "<input type='radio' name='mod' id='mod' value='$sqlrow[mod_id]' onclick=\"setmod('$sqlrow[mod_id]')\">".utf8_decode($sqlrow[mod_desc])."</input><br>\n";
	}
    }
    $result->close();
    echo "</td></tr>\n";
    echo "</table>
	    <div id='papersdiv' hidden>
	    <table id='regform' style='width:70%'>
	    <tr>
	    <td style='text-align:right; width:30%'>Número de Artigos:</td><td style='text-align:left'>
	    <table id='regiform'>\n";

    $strpapers=array('Nenhum','Um Artigo','Dois Artigos','Três Artigos +R$250,00','Quatro Artigos +R$500,00','Cinco Artigos +R$750,00','Seis Artigos +R$1.000,00','Sete Artigos +R$1.250,00','Oito Artigos +R$1.500,00','Nove Artigos +R$1.750,00','Dez Artigos +R$2.000,00','Onze Artigos +R$2.250,00');

    if($admin == $DBVALS['DB_ADMIN']) {
	$MAXPAPERS=$ADMINMAXPAPERS;
	echo "<script type='text/javascript'>
        var MAXPAPERS = ${MAXPAPERS};
        </script>\n";
    }
    for ($i=0;$i<=$MAXPAPERS;$i+=3) {
	echo "<tr>";
	for ($j=$i;$j<($i+3) && $j<=$MAXPAPERS;$j++) {
	    //	    $sqlrow=$result->fetch_assoc();
	    if (($j == $usrdt['num_papers']) || ($MAXPAPERS == 0)) {
		echo "<td style='text-align:left'> <input type='radio' name='npapers' id='npapers' value='$j' checked onclick='numpapers($j)'>$strpapers[$j]</input><br></td>";
	    } else {
		echo "<td style='text-align:left'> <input type='radio' name='npapers' id='npapers' value='$j' onclick='numpapers($j)'>$strpapers[$j]</input><br></td>";
	    }
	}
	echo "</tr>";
    };
    echo "</table></td></tr></table>\n";

    echo "<br>
        <div style='display:none'> 
        <table id='regform' style='width:70%'>
        <tr>
        <th style='text-align:right; width:30%'>
	Valor da Incrição:</th><th style='text-align:left'> R$<input type='text' name='TOTAL' id='TOTAL' size='6' readonly style='color: #B80000;' value=''/></th>
        </tr>
        </table></div>\n";


    if (!($result = $mysqli->query("SELECT * FROM `acc_papers` WHERE acc_id = '$usrdt[acc_id]' ORDER BY `paper_num`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }

    for ($num=1; $num <= $MAXPAPERS; $num++) {
	$color = $num % 2;
	if ($color == 0) {
     	    $bgcolor='white';
     	    $color='green';
	} else {
	    $bgcolor='#F8F8FF';
	    $color='blue';
	};

	if ($num > $usrdt['num_papers']) {
	    $hidden='hidden';
	} else {
	    $hidden='';
	};
	$sqlrow=$result->fetch_assoc();
	$xtra = array('Nenhuma','Uma Página + R$50,00','Duas Páginas + R$100,00');
	echo "<div id='paper${num}div' $hidden>
	<table id='regform' style='width:70%'>
	<tr>
	<td style='text-align:right;background-color:${bgcolor}'><font color='$color'>Identificador do Artigo #${num}</font></td>
	<td style='text-align:left'><input type='number' size='6' name='paperID${num}' id='paperID${num}' onblur='papercheck($num)' onchange='papercheck($num)' oninput='papercheck($num)' value='$sqlrow[paper_num]'/>
	<span id='paper${num}color' style='color:white'>Ident. Inválido</span><span id='paper${num}dup' style='color:white'>(dup)</span>
	</td>
	</tr>
	<tr>
	<td style='text-align:right; width:30%;background-color:${bgcolor}'><font color='$color'>Páginas Extras do Artigo #${num}:</font></td><td style='text-align:left'>";

	for ($i=0;$i<3;$i++) {
	    if ($i == $sqlrow['xtra_pages']) {
		echo "<input type='radio' name='xtrapag${num}' id='xtrapag${num}' value='$i' checked onclick='xtrapag($num,$i)'>$xtra[$i]</input><br>";
	    } else {
		echo "<input type='radio' name='xtrapag${num}' id='xtrapag${num}' value='$i' onclick='xtrapag($num,$i)'>$xtra[$i]</input><br>";
	    }
	    
	}
	
	echo "</td></tr>
		<tr>
		<td style='text-align:right; width:30%;background-color:${bgcolor}'><font color='$color'>Apresentação do Artigo #${num}:</font></td><td style='text-align:left'>";
	if ($sqlrow['self'] == false) {
	    $hidden='';
	    $chkself='';
	    $chkother='checked';
	} else {
	    $hidden='hidden';
	    $chkself='checked';
	    $chkother='';
	}
	$txtpatternA='[a-zA-ZÀ-ü]';
	$txtpatternB='[a-zA-ZÀ-ü\. \-]';
	$txtpatternC='[a-zA-ZÀ-ü\. \-/]';

	echo "<input type='radio' name='paper${num}self' id='paper${num}self' value='1' $chkself onclick=\"setpaperself($num,'1')\">Eu mesmo vou apresentar</input><br>
      <input type='radio' name='paper${num}self' id='paper${num}self' value='0' $chkother onclick=\"setpaperself($num,'0')\">Outra pessoa vai apresentar</input><br>	
      </tr>
      </table>
     <div id='speaker${num}div' $hidden>
     <table id='regform' style='width:70%'>
     <tr>
     <td style='text-align:right; width:30%;background-color:${bgcolor}'><font color='$color'>Apresentador do Artigo #${num}:</font></td>
     <td style='text-align:left'><input type='text' name='speaker${num}' id='speaker${num}' size='30' pattern='${txtpatterB}+' title='somente letras! e .' value='".utf8_decode($sqlrow[speaker])."'/></td>
     </tr>
     </table>
     </div>
     </div>\n";



    }

    $result->close();

    echo "    </div>
    <hr><h3>Mini Cursos</h3>
    Marque os cursos que desejas realizar.
          <table id='regform' style='width:70%'>\n";


    if (!($result = $mysqli->query("SELECT * FROM `mini_courses`,`mini_shift` WHERE mini_courses.shift_id = mini_shift.shift_id ORDER BY course_id"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    $num=0;
    echo "     <script type='text/javascript'>
               var minishift =[];
               </script>";
    while ($sqlrow=$result->fetch_assoc()) {
	$color = $num % 2;
	$num++;
	if ($color == 0) {
     	    $bgcolor='white';
     	    $color='green';
	} else {
	    $bgcolor='#F8F8FF';
	    $color='blue';
	};

	echo "<tr>
               <td style='text-align:right; width:30%;background-color:${bgcolor}'>";
	
	if (!($resultB = $mysqli->query("SELECT * FROM `acc_mini` WHERE course_id = '$sqlrow[course_id]' AND `acc_id` = $accdt[acc_id]"))) {
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if ($resultB->num_rows) {
	    echo "<input type='checkbox' name='mini[]' id='mini_$num' value='$sqlrow[course_id]' onclick=\"minichoicecheck()\" checked/>";
	} else {
	    echo "<input type='checkbox' name='mini[]' id='mini_$num' value='$sqlrow[course_id]' onclick=\"minichoicecheck()\"/>";
	}
	$resultB->close();

	
	echo "               </td>
               <td style='text-align:left'>".utf8_decode($sqlrow[course_name])."<br>
               (".utf8_decode($sqlrow[shift_str1]).") + R$$sqlrow[course_val],00
               <span id='mini_${num}color' style='color:white'>Em Colisão</span>
               ";
	if ($sqlrow['course_desc']) {
	    echo "<br><b>Obs.:</b> ".utf8_decode($sqlrow['course_desc']);
	}
	echo "</td></tr>
               <script type='text/javascript'>
               minishift['$sqlrow[course_id]']=$sqlrow[shift_id];
               </script>
	";
    }
    echo "<input type='hidden' name='mini_num' value='$num' />
          </table>";
    echo "<script type='text/javascript'>
          function minichoicecheck() {
            var OK=true;
            var mini = document.getElementsByName('mini[]');
	    

            setTOTAL();

      	    for (j=0;j<mini.length;j++) {
                 var fieldBcolor=mini[j].id+'color';
                 document.getElementById(fieldBcolor).style.color='white';
            }
		    
            for (i=0;i<mini.length;i++) {
	      if (mini[i].checked == true) {
 	     	 var fieldAcolor=mini[i].id+'color';
	       	 for (j=i+1;j<mini.length;j++) {
		     if (mini[j].checked == true) {
		     	if (minishift[mini[i].value] == minishift[mini[j].value]) {
		     	   var fieldBcolor=mini[j].id+'color';
			   OK = false;
			   document.getElementById(fieldAcolor).style.color='red';
			   document.getElementById(fieldBcolor).style.color='red';
		     	}
		     }
		 }
	      }
            }
	    return OK;
          }
        function minival() {
            val = 0;
            minivals ={ ";

    $first=true;
    if (!($result = $mysqli->query("SELECT course_id,course_val FROM `mini_courses`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
	if ($first) {
	    $first = false;
	    echo "'$sqlrow[course_id]':$sqlrow[course_val] ";
	} else {
	    echo ",'$sqlrow[course_id]':$sqlrow[course_val] ";
	}
    }
    echo "};
    var mini = document.getElementsByName('mini[]');

    for (i=0;i<mini.length;i++) {
	if (mini[i].checked == true) {
//            alert('mini:: i:'+i+'  value:'+mini[i].value+'  val:'+minivals[mini[i].value]);
            val += minivals[mini[i].value];
        }
    }          
 //   alert('minival: '+val);
    return val;
}

function validate() {
    if(papercheck(1) && minichoicecheck()) {
	return true;
    } else {
  	alert('Revise os dados acima, em vermelho, antes de submeter');
	return false;
    }
}
</script>


<br>
<div style='display:none'>
<table id='regform' style='width:70%'>
<tr>
<th style='text-align:right; width:30%'>
Valor da Incrição:</th><th style='text-align:left'> R$<input type='text' name='TOTAL2' id='TOTAL2' size='6' readonly style='color: #B80000;' value=''/></th>
</tr>
</table></div>

<hr><h3>Observações</h3>

<table id='regform' style='width:70%'>
<tr>
<td style='text-align:right; width:30%'>
No caso de alguma excepcionalidade:</td><td style='text-align:left'> <textarea name='obs' id='obs' rows='6' cols='40'>".utf8_decode($usrdt[acc_obs])."</textarea></td>
</tr>
</table>

<input type='submit' value='Inserir'/>
</form>";


    echo "\n\n<script type='text/javascript'>
function regvalue(type,sba,mod,npapers,xpags,minival) {
    val=minival;
    ";
    if (!($result = $mysqli->query("SELECT * FROM `baseprices`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
	echo "if (((type == $sqlrow[type_id]) && (sba == $sqlrow[assoc_id]) && (mod == $sqlrow[mod_id]))) {
	    val += $sqlrow[value]; 
	}\n";
    }
    echo "	if((mod == DB_PROFESSIONAL) && (type == DB_EARLY)) {
	var i;
	for (i=1;i<=npapers;i++) {
      	    val += 50*xpags[i-1];
	}
	if (npapers > 2) {
      	    val += 250*(npapers - 2);
	}
    }

    return val;
}
</script>\n";

    echo "\n\n<script type='text/javascript'>

function settype(val) {
    document.getElementById('type').value=val;
    paperfields();
}

function setsba(val) {
    document.getElementById('sba').value=val;
    paperfields();
}

function setmod(val) {
    //	    console.log('setmod'+val);

    document.getElementById('mod').value=val;
    paperfields();
}

function numpapers(num) {
    document.getElementById('npapers').value=num;
    paperfields();
}


function xtrapag(pap,pag) {
    var field='xtrapag'+pap;
    document.getElementById(field).value=pag;
    paperfields();
}

function setpaperself(pap,speaker) {
    var field='paper'+pap+'self';
    document.getElementById(field).value=speaker;
    paperfields();
}


function initfield(field) {
    var list = document.getElementsByName(field);
    var i;
    for (i=0; i < list.length; i++) {
	if (list[i].checked) {
       	    document.getElementById(field).value=list[i].value;
	}
    }
}

function start(){
    initfield('type');
    initfield('mod');
    initfield('sba');
    initfield('npapers');
    var i;
    for (i=1;i<=MAXPAPERS;i++) {
	var field='paper'+i+'self';
	initfield(field);
	field='xtrapag'+i;
	initfield(field);
    }
    paperfields();
}

function paperfields(){
    var type = document.getElementById('type').value;
    var sba = document.getElementById('sba').value;
    var mod = document.getElementById('mod').value;
    var pap = document.getElementById('npapers').value;

    if ((mod == DB_STUDENT) || (type == DB_LATE) ) 
    {
	//	    console.log('here I am');
	document.getElementById('papersdiv').style.display='none';
	for (i=1;i<=MAXPAPERS;i++) {
     	    field = 'paper'+i+'div';
     	    document.getElementById(field).style.display='none';
            field = 'speaker'+i;
	    document.getElementById(field).required=false;
	    field = 'speaker'+i+'div';
	    document.getElementById(field).style.display='none';
	}
    } else {
	//	        console.log('here I am too');
	document.getElementById('papersdiv').style.display='block';

     	for (i=1;i<=MAXPAPERS;i++) {
	    //	        console.log('lets see'+i);
	    paperfield = 'paper'+i+'div';
	    
	    speakerfield='speaker'+i;
	    speakerdiv='speaker'+i+'div';
	    selffield='paper'+i+'self';
	    if (i<=pap) {
     		document.getElementById(paperfield).style.display='block';
		if (document.getElementById(selffield).value == 1) {
		    //	                console.log('false'+i);
   	       	    document.getElementById(speakerfield).required=false;
		    document.getElementById(speakerdiv).style.display='none';
		} else {
		    //	                console.log('required'+i);
   	       	    document.getElementById(speakerfield).required=true;
		    document.getElementById(speakerdiv).style.display='block';
		}
	    } else {
		//	            console.log('false'+i);
     		document.getElementById(paperfield).style.display='none';
   		document.getElementById(speakerfield).required=false;
	    }
	}

    }

    setTOTAL();
}

function setTOTAL() {
    var type = document.getElementById('type').value;
    var sba = document.getElementById('sba').value;
    var mod = document.getElementById('mod').value;
    var pap = document.getElementById('npapers').value;
    var xpags = [];
    var i;
    for (i=1;i<=pap;i++) {
	var field='xtrapag'+i;
    	xpags.push(document.getElementById(field).value);
    }

    val = regvalue(type,sba,mod,pap,xpags,minival());
    document.getElementById('TOTAL').value = val;
    document.getElementById('TOTAL2').value = val;

}

</script>\n";

    echo "\n\n<script type='text/javascript'>

function papercheck(pap) {

    var mod = document.getElementById('mod').value;
    if (mod == DB_STUDENT) {
      return true;
    }

    var paperlist = {";
    $first=true;
    if (!($result = $mysqli->query("SELECT paper_num FROM `papers`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($sqlrow=$result->fetch_assoc())) {
	if ($first) {
	    $first = false;
	    echo "'".$sqlrow['paper_num']."':true ";
	} else {
	    echo ",'".$sqlrow['paper_num']."':true ";
	}
    }
    echo "};

    var npap = document.getElementById('npapers').value;
    var i;

    var field;
    var paper;
    var OK = true;
    var found;
    var colorfield;
    for (i=1;i<=npap;i++) {
        field ='paperID'+i;
        paper = document.getElementById(field).value;
        found=false;
        found=paperlist[paper];
        colorfield = 'paper'+i+'color';

        if (found) {
	   document.getElementById(colorfield).style.color='white';
        } else {
	   document.getElementById(colorfield).style.color='red';
           OK = false;
        }
    }

    var dupA = 'paper'+pap+'dup';
    var dupcolor='white';

    for (i=1;i<=npap;i++) {
	var dupB = 'paper'+i+'dup';
	document.getElementById(dupB).style.color='white';
    }
    
    for (i=1;i<npap;i++) {
	field = 'paperID'+i;
	var paperB = document.getElementById(field).value;
	var dupB = 'paper'+i+'dup';
	for (j=i+1;j<=npap;j++) {
	    field = 'paperID'+j;
	    var paperC = document.getElementById(field).value;
	    var dupC = 'paper'+j+'dup';
	    if (paperC == paperB) {
       		OK=false;
		dupcolor='red';
		document.getElementById(dupB).style.color=dupcolor;
		document.getElementById(dupC).style.color=dupcolor;
	    }
	}
    }
    
    return OK;
}
</script>";

    echo "\n\n<script type='text/javascript'>
window.onload = start; //notice no parenthesis
</script>";



}


?>
