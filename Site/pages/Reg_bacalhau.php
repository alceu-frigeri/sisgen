<?php

<?php include "FLAGS.php" ?>

<?php
include "dbconnect.php";

$mysqli = myconnect();
	 ?>

<?php

date_default_timezone_set('America/Sao_Paulo');
$timestamp=time();
$timestamp=date('Y-m-d H:i:s',$timestamp);

list($microstamp,$sec) = explode(' ',microtime(false));
list($nothing,$microstamp) = explode('.',$microstamp);

    if (!($result = $mysqli->query("SELECT * FROM `account`;"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    
    
    //    if (!($result = $mysqli->query("SELECT * FROM `account`,`types`,`association`,`modalities`,`country`,`treatment`,`userdata`,`baseprices`,`pap_str` WHERE account.acc_id = userdata.acc_id AND userdata.country_id = country.country_id AND userdata.treat_id = treatment.treat_id AND userdata.mod_id = modalities.mod_id AND userdata.assoc_id = association.assoc_id AND userdata.type_id = types.type_id AND types.type_id = baseprices.type_id AND association.assoc_id = baseprices.assoc_id AND modalities.mod_id = baseprices.mod_id AND userdata.num_papers = pap_str.num_papers;"))) {
    //	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    //	return NULL;
    //    }


    echo "<tr>";
    $fields=[];
    $fields[]='Detalhes';
    $fields[]='Email';
    $fields[]='Nome';
    $fields[]='Tipo';
    $fields[]='Associação';
    $fields[]='Modalidade';
    $fields[]='Valor';
    $fields[]='Recibo';
    $fields[]='Assoc.';
    $fields[]='Estudante';
    tdX($fields,'green','black');
    $fields=[];
    $fields[]='Ação';
    tdX($fields,'green','black','width:600px;');
    echo "</tr>";

    $num=0;
    while ($account=$result->fetch_assoc()) {
	$num++;
	$sqlrow=getuserdata($account['hashval']);
	$fields=[];

	$loginacc = $sqlrow;
	
	$fields[] = strform('usr_data','',true)."<input type='submit' value='Detalhes'/></form>";
	$fields[]="<a href='mailto:$sqlrow[email]'><font color='darkred'><b>$account[email]</b></font></a>";
	$fields[]=($sqlrow[name]).' '.($sqlrow[familyname]);
	$fields[]=$sqlrow['type_str'];
	$fields[]=$sqlrow['assoc_str'];
	$fields[]=$sqlrow['mod_str'];
	$fields[]=$sqlrow['subs_value']+$sqlrow['mini_value']." ($sqlrow[subs_value]+$sqlrow[mini_value])";
	$fields[]="<a href='$uploadurl$sqlrow[receipt_file]' target='_BLANK'>$sqlrow[receipt_file]</a>";
	$fields[]="<a href='$uploadurl$sqlrow[student_file]' target='_BLANK'>$sqlrow[student_file]</a>";
	$fields[]="<a href='$uploadurl$sqlrow[sba_file]' target='_BLANK'>$sqlrow[sba_file]</a>";

	if($sqlrow['submitted']) {
	    if($sqlrow['confirmed']) {
		$fields[] = sec_button($num,true,false);
		$bgcolor=$usrcolor['confirmed'];
	    } else {
		$fields[] = sec_button($num,true,true);
		$bgcolor=$usrcolor['submitted'];
	    }
	} else {
	    if($sqlrow['activ']) {
		if($sqlrow['can_edit']) {
		    $fields[]='usuário ainda não submeteu';
		    $bgcolor=$usrcolor['editing'];
		} else {
		    $fields[] = sec_button($num,false,true);
		    $bgcolor=$usrcolor['files'];
		}
	    } else {
		$fields[]='conta inativa';
		$bgcolor=$usrcolor['inactive'];
	    }
	}
	
	




	trX($fields,$bgcolor,'black');
    }
    echo "</div></div>\n";


}


/******************
   <form name=f1 method=post action=test5.html>
   <input type=text name=name value='plus2net'>
   <input type='submit' value='Open default action file test5.php' 
   onclick="this.form.target='_blank';return true;">
   </form>
 **********************/


			    ?>




