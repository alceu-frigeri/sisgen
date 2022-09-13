<?php

function trX($queue,$bgcolor,$color,$style='') {
    echo "<tr>";
    tdX($queue,$bgcolor,$color,$style);
    echo "</tr>";
}
function tdX($queue,$bgcolor,$color,$style='') {
    foreach ($queue as $tag) {
	echo "<td style=\"$style background-color:$bgcolor; font-size:11px\"><font color='$color'>$tag</font></td>\n";
    }
    
}

function maillink($email,$subject,$body) {
    $trans=array(' ' => '%20', "\n" => '%0D%0A');
    $body .= "\n\nAtt.\n Secretaria SBAI17\n 1-4 de Outubro de 2017\n https://www.ufrgs.br/sbai17";
    $str = "<a href='mailto:$email?subject=".strtr($subject,$trans)."&body=".strtr($body,$trans)."'><font color='darkred'><b>$email</b></font></a>";
    return $str;

}


function javascripts() {
    $str = "   <script type='text/javascript'>
    function setsent(obj,num) {
      var spanfield = 'div_'+num;
      var txtfield = 'sec_'+num;
      if(obj.checked) {
        document.getElementById(spanfield).style.display='block';
        <!-- document.getElementById(txtfield).required=true;-->
      } else {
        document.getElementById(spanfield).style.display='none';
        <!-- document.getElementById(txtfield).required=false;-->
      }
    }
    function setmailing(obj) {
      var spanfield = 'div_mailing';
      var txtfield = 'mailing';
      if(obj.checked) {
        document.getElementById(spanfield).style.display='block';
        <!-- document.getElementById(txtfield).required=true;-->
      } else {
        document.getElementById(spanfield).style.display='none';
        <!-- document.getElementById(txtfield).required=false;-->
      }
    }

    </script>";
    return $str;
}

function sec_menu($admin) {
    global $DBVALS;

    
    $menu ="<div style='width:1920px;'><div class='col-sm-12'>".
           '<h2>Opções</h2><table><tr>'.
	   '<td>'.strform('all_accounts','').strsubmit('submit','Todas as contas').'</form></td>'.
	   '<td>'.strform('waiting_accounts','').strsubmit('submit','Aguardanto confirmação').'</form></td>'.
     	   '<td>'.strform('pending_accounts','').strsubmit('submit','Contas pendentes').'</form></td>'.
	   '</tr>';
    if ($admin == $DBVALS['DB_ADMIN'])
    {
	$menu .=  '<tr><td>'.strform('mini_waitlist','').strsubmit('submit','mini cursos (Espera)').'</form></td>'.
		  '<td>'.strform('mini_list','').strsubmit('submit','mini cursos').'</form></td>';
	'</tr>';
    }
    
    $menu .='<tr><td>'.strform('csv_confirmed','').strsubmit('submit','Confirmados').'</form></td></tr>';
    if ($admin == $DBVALS['DB_ADMIN'])
    {
	
	$menu .= '<tr><td>'.strform('csv_sessions','').strsubmit('submit','Sessões').'</form></td>'.
		 '<td>'.strform('papers_hanging','').strsubmit('submit','Artigos/pendencias').'</form></td>'.
		 '</tr>';
	$menu .= '<tr>'.
		 '<td>'.strform('sba_list','').strsubmit('submit','SBA').'</form></td>'.
		 '</tr>';
    }
	//	$menu .= '<tr>'.
	//		 '<td>'.strform('bac_1','').strsubmit('submit','Bac I').'</form></td>'.
	//		 '<td>'.strform('bac_2','').strsubmit('submit','Bac II').'</form></td>'.
	//		 '<td>'.strform('bac_3','').strsubmit('submit','Bac III').'</form></td>'.
	//		 '<td>'.strform('bac_email','').strsubmit('submit','Bac Email').'</form></td>'.		 '</tr>';
	$menu .='<tr><td>'.strform('usr_select','')."ID #<input name='acc_id' id='acc_id' size='10'/>".strsubmit('submit','select').'</form></td>'.
		'<td>'.strform('usr_select','')."Name<input name='name' id='name' size='10'/>".strsubmit('submit','select').'</form></td>'.
		'<td>'.strform('usr_select','')."Email<input name='email' id='email' size='10'/>".strsubmit('submit','select').'</form></td>'.
		'<td>'.strform('paper_select','')."Paper #<input name='paper_num' id='paper_num' size='10'/>".strsubmit('submit','select').'</form></td>'.
		'</tr>';
	
	
    

    $menu .= '</table>';
    if ($admin == $DBVALS['DB_ADMIN']) {
	$menu .= "<input type='checkbox' name='mailing' id='mailing' value='true' onchange='setmailing(this)'>Mailing</input>\n".
		 "<div id='div_mailing' hidden><hr>".
		 strform('mailing','').
		 "<input type='checkbox' name='test' id='test' checked value='true' '><b>TEST</b></input>".

		 "<input type='checkbox' name='confirmed' id='confirmed' value='true' '>Confirmados</input>".
		 "<input type='checkbox' name='nonconfirmed' id='nonconfirmed' value='true' '>Aguardando Confirmação</input>\n".
		 "<input type='checkbox' name='paycheck' id='paycheck' value='true' '>valor OK</input>".
		 "<input type='checkbox' name='nonpaycheck' id='nonpaycheck' value='true' '>valor Não OK</input>".
		 "<input type='checkbox' name='pendingwithpaper' id='pendingwithpaper' value='true' '>pendentes com artigos associados</input>".
		 "<input type='checkbox' name='pending' id='pending' value='true' '>Cinza Claro</input>\n".
		 "<input type='checkbox' name='filespending' id='filespending' value='true' '>Amarelo</input><br>\n".
		 "<textarea name='mailing_sbj' id='mailing_sbj' rows=1 cols=120 required>SBAI17 - </textarea><br>".
		 "<textarea name='mailing_txt' id='mailing_txt' rows=12 cols=120 required><tratamento> <titulo> <nome>,
seguimos aguardando algo...


  Att.
  Secretaria SBAI17
  1-4 de Outubro de 2017
  https://www.ufrgs.br/sbai17
</textarea>".strsubmit('mailing','Mailing').
		 "</form><hr></div>";
    }
    

    return $menu;
}

function sec_end() {
    $str = '</div></div>';
    return $str;
}


function sec_buttonA($num,$phase,$offset,$usrdt,$files,$confirm) {
    $str = strform('sec_submit','').strhiddenval('phase2',$phase).strhiddenval('offset',$offset).
	   "<table style='width:150px'><tr>
	<td><input type='radio' name='action' id='action' value='edit'>Liberar&nbsp;Edição</input></td></tr>\n";
    if ($files) {
	$str .= "<tr><td><input type='radio' name='action' id='action' value='files'>Liberar&nbsp;Arquivos</input></td></tr>\n";
	if ($confirm) {
	    $str .= "<tr><td><input type='radio' name='action' id='action' value='confirm'>Confirmar</input></td></tr>\n";
	} else {
	    $str .= "<tr><td><input type='radio' name='action' id='action' value='unconfirm'>Desconfirmar</input></td></tr>\n";
	}
    }
    $str .= "<tr><td><input type='checkbox' name='sentemail' id='sentemail' value='true' onchange='setsent(this,$num)'>Enviar Email</input></td></tr>\n";
    $str .= "</table>
        <div id='div_$num' hidden><textarea name='sec_txt' id='sec_$num' rows=6 cols=40 required></textarea></div>
	<input type='submit' value='Alterar'/></form>";
    if (!$files) {
	$str .= "<br>usuário ainda não submeteu 2";
    }

    return $str;
}


function sec_buttonB($num,$phase,$offset,$usrdt) {
    $value=$usrdt['sec_value'];
    $str = strform('sec_data','').strhiddenval('phase2',$phase).strhiddenval('offset',$offset)."
	<br>
	Valor Efetivo:<input type='text' name='sec_value' id='sec_value' value='$value' required></input><br>
	Observações (para registro da secretaria)<br>
	<textarea name='secobs_txt' id='sec_obs$num' rows=6 cols=40 required>".($usrdt['sec_obs'])."</textarea>
	<input type='submit' value='Anotar'/></form>";
    return $str;
}


function sec_buttonD($num,$phase,$offset,$usrdt,$files,$confirm) {
    $str = strform('sec_info','').strhiddenval('phase2',$phase).strhiddenval('offset',$offset).
	   "<table style='width:150px'><tr>";
    $recibo='';
    $nf='';
    if ($usrdt['receipt_type']) {
	$recibo='checked';
    } else {
	$nf='checked';
    }
    $str .= "<td><input type='radio' name='receipt' id='receipt' value='1' $recibo>Recibo</input></td><td><input type='radio' name='receipt' id='receipt' value='0' $nf>NF</input></td></tr>\n";


    $exception='';
    $regular='';
    if ($usrdt['exception']) {
	$exception='checked';
    } else {
	$regular='checked';
    }
    $str .= "<td><input type='radio' name='exception' id='exception' value='0' $regular>Regular</input></td><td><input type='radio' name='exception' id='exception' value='1' $exception>Exceção</input></td></tr>\n";

    $deposito='';
    $boleto='';
    $cartao='';
    $empenho='';
    switch ($usrdt['payment']) {
	case 0:
	    $deposito='checked';
	    break;
	case 1:
	    $boleto='checked';
	    break;
	case 2:
	    $cartao='checked';
	    break;
	case 3:
	    $empenho='checked';
	    break;
	    
    }
    $str .= "<td><input type='radio' name='payment' id='payment' value='0' $deposito>Deposito</input></td><td><input type='radio' name='payment' id='payment' value='1' $boleto>Boleto</input></td></tr>
	<tr><td><input type='radio' name='payment' id='payment' value='2' $cartao>Cartão</input></td><td><input type='radio' name='payment' id='payment' value='3' $empenho>Empenho</input></td></tr>\n";

    $paymentOK='';
    $paypending='';
    if ($usrdt['paycheck']) {
	$paymentOK='checked';
    } else {
	$paypending='checked';
    }
    $str .= "<td><input type='radio' name='paycheck' id='paycheck' value='0' $paypending>valor pendente</input></td><td><input type='radio' name='paycheck' id='paycheck' value='1' $paymentOK>valor OK</input></td></tr>\n";

    $str .= "</table><br><input type='submit' value='Registrar'/></form>";

    return $str;
}



function sec_buttonC($num,$phase,$offset,$usrdt,$course,$files,$confirm,$txt) {
    $str = strform('sec_overbook','').strhiddenval('phase2',$phase).strhiddenval('offset',$offset).strhiddenval('course',$course).
	   "<table style='width:150px'>";
    $str .= "<tr><td><input type='checkbox' name='sentemail' id='sentemail' value='true' onchange='setsent(this,$num)'>Enviar Email</input></td></tr>\n";
    $str .= "</table>
	<div id='div_$num' hidden><textarea name='sec_txt' id='sec_$num' rows=6 cols=40 required></textarea></div>
	<input type='submit' value='Overbook'/></form><br>$txt";
    if (!$files) {
	$str .= "<br>usuário ainda não submeteu 2";
    }

    return $str;
}


function sec_submit($accdt,$usrdt,$admin) {
    global $mysqli;
    global $loginadmin;
    
    
    $subject='Alteração de Status inscrição : ';
    $msg = "$usrdt[treat_str1] $usrdt[treat_str2] $usrdt[familyname]\n";

    switch ($_POST['action']) {
	case 'edit':
	    $can_edit=1;
	    $can_files=1;
	    $submitted=0;
	    $confirmed=0;
	    $subject .= "Edição Liberada";
	    $msg .= "Detectamos as seguintes pendências na sua inscrição e que requerem a sua atenção:\n\n$_POST[sec_txt]\n.\n\n";
	    $msg .= "Por favor acesse\n https://www.ufrgs.br/sbai17/?q=Register\ne efetive as correções solicitadas.\n";
	    break;
	case 'files':
	    $can_edit=0;
	    $can_files=1;
	    $submitted=0;
	    $confirmed=0;
	    $subject .= "Submissão Comprovantes Liberada";
	    $msg .= "Detectamos as seguintes pendências na sua inscrição e que requerem a sua atenção:\n\n$_POST[sec_txt]\n.\n\n";
	    $msg .= "Por favor acesse\n https://www.ufrgs.br/sbai17/?q=Register\ne efetive as correções solicitadas.\n";
	    $msg .= "\n\n";
	    break;
	case 'confirm':
	    $can_edit=0;
	    $can_files=0;
	    $submitted=1;
	    $confirmed=1;
	    $subject = "Confirmação de Inscrição";
	    $msg .= "Confirmamos a sua inscrição no SBAI17, conforme dados abaixo!\n\n
     Tipo: $usrdt[type_desc2]
     Associação: $usrdt[assoc_desc]
     Modalidade: $usrdt[mod_desc]
     # Artigos: $usrdt[num_papers]
";



	    if (!($result = $mysqli->query("SELECT * FROM `acc_papers`,`papers`,`pag_str` WHERE acc_id = '$usrdt[acc_id]'  AND papers.paper_num = acc_papers.paper_num AND acc_papers.xtra_pages = pag_str.xtra_pages ORDER BY acc_papers.`paper_num` "))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
	    }

	    $xtrapag=0;
	    for ($num=1; $num <= $usrdt['num_papers']; $num++) {
		$sqlrow=$result->fetch_assoc();
		$msg .= "
        ${num}o. Artigo # $sqlrow[paper_num]
        Título : ".utf8_decode($sqlrow['paper_title'])."
        Núm. de Pág. Extras : ".utf8_decode($sqlrow['npag_str2']);
		if ($sqlrow['self']) {
		    $msg .= "
        A ser apresentado por eu mesmo\n";
		} else {
		    $msg .= "
        A ser apresentado por ".utf8_decode($sqlrow['speaker'])."\n";
		}

	    };



	    if (!($result = $mysqli->query("SELECT * FROM `acc_mini`,`mini_courses`,`mini_shift` WHERE `acc_id` = '$accdt[acc_id]' AND mini_courses.course_id = acc_mini.course_id AND mini_courses.shift_id = mini_shift.shift_id;"))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
	    }
	    $minival=0;
	    while($sqlrow = $result->fetch_assoc()) {
		$num++;
		if($sqlrow['reserved']) {
		    $msg .= "
       Você está inscrito no minicurso: ".utf8_decode($sqlrow['course_name'])."\n";
		} else {
		    $msg .= "
       Você está na lista de espera do minicurso: ".utf8_decode($sqlrow['course_name'])."\n";
		}

	    }		
	    $msg .= "\n
     Você pode, a qualquer momento, verificar os dados da inscrição realizada em
     https://www.ufrgs.br/sbai17/?q=Register
     Informações adicionais:\n\n$_POST[sec_txt]\n";
	    break;
	case 'unconfirm':
	    $can_edit=0;
	    $can_files=0;
	    $submitted=1;
	    $confirmed=0;
	    $subject .= "Pendências na Inscrição";
	    $msg .= "Detectamos as seguintes pendências na sua inscrição (previamente confirmada) e que requerem a sua atenção:\n\n$_POST[sec_txt]\n";
	    break;
	case 'Email' :
	    $subject .= "Comunicado/Atenção";
	    $msg .= "Prezado(a)\n\n$_POST[sec_txt]\n";
	    eventlog('SECRETARY','Email',"sec:$loginadmin[email] user:$usrdt[name]");

	    mymail($accdt['email'],$subject,$msg);
	    return;
	    break;
	default;
	    eventlog('SECRETARY','Data Updt',"NOTHING !! sec:$loginadmin[email] user:$usrdt[name]");
	    return;
	    break;
    }
    if(!$mysqli->query("UPDATE account SET can_edit = '$can_edit', can_files = '$can_files', submitted = '$submitted', confirmed = '$confirmed' WHERE acc_id = '$accdt[acc_id]';")) {
	$err= "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	eventlog('DBERROR','',$err) ;
    }
    eventlog('SECRETARY','Data Updt',"sec:$loginadmin[email] user:$usrdt[name]");

    mymail($accdt['email'],$subject,$msg);
}


function sec_data($accdt,$usrdt,$admin) {
    global $mysqli;
    global $loginadmin;
    if(!($stmt = $mysqli->prepare("UPDATE userdata SET sec_value = '$_POST[sec_value]', sec_obs = ?  WHERE acc_id = '$accdt[acc_id]';"))) {
	$err= "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	eventlog('DBERROR','',$err) ;
    }
    $_POST['secobs_txt'] = utf8_encode($_POST['secobs_txt']);
    if (!$stmt->bind_param('s',$_POST['secobs_txt'])) {
	$err =  "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	eventlog('DBERROR','',$err) ;

    };
    if (!$stmt->execute()) {
	$err = "execute failed: (" . $stmt->errno . ") " . $stmt->error;
	eventlog('DBERROR','',$err) ;

    };
    eventlog('SECRETARY','Data comm. Updt',"sec:$loginadmin[email] user:$usrdt[name]");
}


function sec_info($accdt,$usrdt,$admin) {
    global $mysqli;
    global $loginadmin;
    if(!($stmt = $mysqli->query("UPDATE userdata SET receipt_type = '$_POST[receipt]', exception = '$_POST[exception]', payment = '$_POST[payment]', paycheck = '$_POST[paycheck]' WHERE acc_id = '$accdt[acc_id]';"))) {
	$err= "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	eventlog('DBERROR','',$err) ;
    }
    eventlog('SECRETARY','Data Info Updt',"sec:$loginadmin[email] user:$usrdt[name]");
}



function sec_overbook($accdt,$usrdt,$admin) {
    global $mysqli;
    global $loginadmin;


    if(!($mysqli->query("UPDATE `acc_mini`  SET reserved = '1', waitlist = '0', overbook = '1'  WHERE `acc_id` = '$accdt[acc_id]' AND `course_id` = '$_POST[course]';"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>\n"; 
    }
    if(!($result = $mysqli->query("SELECT * FROM mini_courses  WHERE `course_id` = '$POST_[course]';"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error . "<br>\n"; 
    }

    $course=$result->fetch_assoc();

    eventlog('SECRETARY','Overbook',"sec:$loginadmin[email] user:$usrdt[name]");
    $subject = "Confirmação de Inscrição Mini-Curso";
    $msg .= "Confirmamos a sua inscrição no Mini-Curso:\n\n
     
     $course[course_name]
     
     Você pode, a qualquer momento, verificar os dados da inscrição realizada em
     https://www.ufrgs.br/sbai17/?q=Register
     Informações adicionais:\n\n$_POST[sec_txt]\n";
    mymail($accdt['email'],$subject,$msg);

}

function sba_list($admin) {
    echo sec_menu($admin);
    $queryA="SELECT COUNT(*) as count FROM account, userdata WHERE account.acc_id = userdata.acc_id AND assoc_id = '1';";
    $queryB="SELECT * FROM account, userdata WHERE account.acc_id = userdata.acc_id AND assoc_id = '1' ORDER BY `email`;";
    csv_accounts('sba_list',$queryA,$queryB);
    echo sec_end();
}


function csv_confirmed($admin) {
    echo sec_menu($admin);
    $queryA="SELECT COUNT(*) as count FROM `account` WHERE confirmed = '1';";
    $queryB="SELECT * FROM `account` WHERE confirmed = '1' ORDER BY `email`;";
    csv_accounts('csv_confirmed',$queryA,$queryB);
    echo sec_end();
}

function csv_sessions($admin) {
    echo sec_menu($admin);
    sessions('csv_sessions',true);
    echo sec_end();
}

function all_account($admin) {
    echo 'not done yet<br>';
}

function papers_hanging($admin) {
    echo sec_menu($admin);
    papers('papers_hanging',false);
    echo sec_end();
}

function paper_select($admin) {
    echo sec_menu($admin);
    papers('papers_select',false,"paper_num = '$_POST[paper_num]'");
    echo sec_end();
}

function pending_accounts($admin) {

    echo sec_menu($admin);
    accounts_list('Lista de Contas (não submetidas)','pending_accounts',"submitted = '0'  AND type_id = '3'");
    echo sec_end();
}

function waiting_accounts($admin) {

    echo sec_menu($admin);
    accounts_list('Lista de Contas (aguardando confirmação)','waiting_accounts',"submitted = '1' AND confirmed = '0'");
    //    echo "aqui<br>\n";
    echo sec_end();
}

function mini_waitlist($admin) {

    echo sec_menu($admin);
    //    waitlist('mini_waitlist');
    
    waitlist('Lista de Espera Mini-Cursos','mini_waitlist','wait_queryA','stdfieldsnav','stdfieldshead','waitfields');
    echo sec_end();
}


function mini_list($admin) {

    echo sec_menu($admin);
    //    waitlist('mini_waitlist');

    
    waitlist('Lista  Mini-Cursos','mini_waitlist','wait_queryB','stdfieldsnav','minihead','minifields');
    echo sec_end();
}

function wait_queryA($course) {
    $query = "SELECT * from acc_mini,account WHERE acc_mini.acc_id = account.acc_id AND acc_mini.course_id = '$course' AND acc_mini.submitted = '1' AND acc_mini.reserved = '0' ORDER BY acc_mini.mini_stamp";
    return $query;
}


function wait_queryB($course) {
    $query = "SELECT * from acc_mini,account WHERE acc_mini.acc_id = account.acc_id AND acc_mini.course_id = '$course' AND acc_mini.submitted = '1' ORDER BY acc_mini.mini_stamp";
    return $query;
}



function sec_main($admin) {
    echo sec_menu($admin);
    accounts_list('Lista de Contas (todas)','all_accounts',"type_id = '3'");
    echo sec_end();
}


function usr_select($admin) {
    echo sec_menu($admin);
    if ($_POST['acc_id']) {
	accounts_list('Conta selecionada','usr_select',"acc_id = $_POST[acc_id]");
    } else {
	if ($_POST['name']) {
	    $name = $_POST['name'];
	    $where = "name LIKE '%$name%' OR familyname LIKE '%$name%' OR fullname LIKE '%$name%' OR fullnamereceipt LIKE '%$name%'";
	    $queryA="SELECT * FROM account,userdata where account.acc_id = userdata.acc_id AND ( $where ) ";
	    $queryB="SELECT COUNT(*) as count FROM account,userdata where account.acc_id = userdata.acc_id AND ( $where ) ";
	    
	    dtlist('Conta(s) selecionada(s)','usr_select',$queryA,$queryB,'stdfieldsnav','stdfieldshead','stdfields');
	} else {
	    $email = $_POST['email'];
	    $where = "email LIKE '%$email%'";
	    $queryA="SELECT * FROM account,userdata where account.acc_id = userdata.acc_id AND ( $where ) ";
	    $queryB="SELECT COUNT(*) as count FROM account,userdata where account.acc_id = userdata.acc_id AND ( $where ) ";
	    
	    dtlist('Conta(s) selecionada(s)','usr_select',$queryA,$queryB,'stdfieldsnav','stdfieldshead','stdfields');
	}
	
	
    }
    
    echo sec_end();
}


function all_accounts($admin) {

    echo sec_menu($admin);
    accounts_list('Lista de Contas (todas)','all_accounts',"type_id = '3'");
    echo sec_end();
}

function accounts_list($list_title,$phase,$filter='') {
    if ($filter) {
	$filter = "WHERE $filter";
    };
    $queryA="SELECT * FROM `account` $filter ORDER BY `email`";
    $queryB="SELECT COUNT(*) as count FROM `account` $filter";
    
    //    accounts($list_title,$phase,$queryA,$queryB);
    dtlist($list_title,$phase,$queryA,$queryB,'stdfieldsnav','stdfieldshead','stdfields');
}

//#############################
//######################


////////////////
////////////////
///////////////

function dtlist($list_title,$phase,$queryA,$queryB,$Nav,$Header,$Std,$csv='') {
    global $microstamp;
    global $mysqli;
    global $DBVALS;
    global $MAXSIZE;
    global $uploadurl;
    global $loginacc;
    global $loginadmin;
    global $NRECORDS;


    
    echo javascripts();

    
    if ($_POST['offset']) {
	$offset=$_POST['offset'];
    } else {
	$offset=0;
    }

    //    echo "query: $queryB<br>\n";
    if (!($result = $mysqli->query($queryB))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $sqlrow = $result->fetch_assoc();
    if($offset > 0) {
	$first=strform($phase,'').strhiddenval('offset',$offset-$NRECORDS).strsubmit('submit','Previous').'</form>';
    } else {
	$first='';
    }
    if (($offset+$NRECORDS) < $sqlrow['count']) {
	$last=strform($phase,'').strhiddenval('offset',$offset+$NRECORDS).strsubmit('submit','Next').'</form>';
    } else {
	$last='';
    }
    $navline=$Nav($first,$last,$csv);

    $result->close();
    
    $last=$offset+$NRECORDS;
    if ($last > $sqlrow['count']) {
	$last=$sqlrow['count'];
    }
    
    echo "<h2>$list_title</h2><h4> (".(${offset}+1)." à $last de $sqlrow[count])</h4>
	    <div class='row'><div class='col-sm-12'>
	    <table id='regform' style='width=120%'>\n";

    
    //    echo "queryA: $queryA<br>\n";
    if (!($result = $mysqli->query("$queryA LIMIT $offset,$NRECORDS;"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }

    echo "<tr>";
    $fields = $Header($csv);
    trX($navline,'lightblue','darkyellow');
    
    //    tdX($fields,'green','black');
    //    $fields=[];
    //    $fields[]='Ação';
    tdX($fields,'green','black');
    echo "</tr>";

    $num=0;
    while ($account=$result->fetch_assoc()) {
	$num++;
	$sqlrow=getuserdata(true,$account['hashadmin']);
	$loginacc = getaccdata(true,$account['hashadmin']);
	
	list ($fields,$bgcolor) = $Std($loginacc,$sqlrow,$num,$phase,$offset,$csv,$offset+$num);

	trX($fields,$bgcolor,'black');
    }
    $result->close();
    trX($navline,'lightblue','darkyellow');
    echo "</table>";

    echo "</div></div>\n";
}



function waitlist($list_title,$phase,$query,$Nav,$Header,$Std,$csv='') {
    global $microstamp;
    global $mysqli;
    global $DBVALS;
    global $MAXSIZE;
    global $uploadurl;
    global $loginacc;
    global $loginadmin;
    global $NRECORDS;


    
    echo javascripts();

    echo "<h2>$list_title</h2>
	    <div class='row'><div class='col-sm-12'>
	    <table id='regform' style='width=120%'>\n";


    $queryB = "SELECT * from mini_courses ORDER BY course_id;";
    if (!($result = $mysqli->query($queryB))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }

    $navline=$Nav($first,$last,$csv);


    $fields = $Header($csv);

    trX($navline,'lightblue','darkyellow');
    trX($fields,'green','black');

    $num=0;
    while ($mini=$result->fetch_assoc()) {
	//	$queryA = "SELECT * from acc_mini,account WHERE acc_mini.acc_id = account.acc_id AND acc_mini.course_id = '$mini[course_id]' AND acc_mini.submitted = '1' AND acc_mini.reserved = '0' ORDER BY acc_mini.mini_stamp";

	$queryA = $query($mini['course_id']);
	$num++;
	echo "<tr><td colspan='10'>".utf8_decode($mini[course_name])."</td></tr>\n";

	if (!($result2 = $mysqli->query($queryA))) {
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    return NULL;
	}
        $seq=0;
	while ($accmini=$result2->fetch_assoc()) {
	    $seq++;
	    $sqlrow=getuserdata(true,$accmini['hashadmin']);
	    $loginacc = getaccdata(true,$accmini['hashadmin']);
	    
	    list ($fields,$bgcolor) = $Std($loginacc,$sqlrow,$num,$phase,$offset,$mini['course_id'],$accmini,$mini,$csv,$seq);

	    trX($fields,$bgcolor,'black');
	}
    }
    $result->close();
    trX($navline,'lightblue','darkyellow');
    echo "</table>";

    echo "</div></div>\n";
}







//##########
//############
//#########

function sessionssnav($first,$last,$csv) {

    $navline=[];
    $navline[]='Título Sessão';
    $navline[]='Chair';
    $navline[]='Horário';
    $navline[]='Local';
    $navline[]='# Artigo';
    $navline[]='Título';
    $navline[]='Apresentador';
    $navline[]='Inscrição';
    if(!$csv) {
	$navline[]='Autor de Contato';
	$navline[]='Email de Contato';
    }
    return $navline;
}

function sessionshead($csv) {
    $fields=[];
    $fields[]='Título Sessão';
    $fields[]='Chair';
    $fields[]='Horário';
    $fields[]='Local';
    $fields[]='# Artigo';
    $fields[]='Título';
    $fields[]='Apresentador';
    $fields[]='Inscrição';
    if(!$csv) {
	$fields[]='Autor de Contato';
	$fields[]='Email de Contato';
    }

    return $fields;
}

function sessionsstd($account,$sqlrow,$num,$phase,$offset,$csv) {
    global $uploadurl;
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'inactive'  => 'grey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );

}

function stdfieldsnav($first,$last,$csv) {


    $navline=[];
    $navline[]=$first;
    //    $navline[]='';
    //    $navline[]='';
    $navline[]='';
    $navline[]='';
    //    $navline[]='';
    //    $navline[]='';
    //    $navline[]='';
    //    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    //    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]=$last;
    return $navline;
}

function stdfieldshead($csv) {
    $fields=[];
    $fields[]='#';
    //    $fields[]='Acc ID';
    //    $fields[]='Detalhes';
    $fields[]='Acc ID/Detalhes/Email/Nome';
    //    $fields[]='Nome';
    $fields[]='Tipo/Assoc/Mod/Valor';
    //    $fields[]='Associação';
    //    $fields[]='Modalidade';
    //    $fields[]='Valor';
    $fields[]='Recibo/Estudante/Assoc.';
    //    $fields[]='Estudante'; 
    //    $fields[]='Assoc.';
    $fields[]='# Artigos';
    $fields[]='Copyright';
    $fields[]='Observações';
    $fields[]='Editado (Admin)';
    $fields[]='Pagamento';
    $fields[]='Anotações';
    $fields[]='Ação';

    return $fields;
}

function stdfields($account,$sqlrow,$num,$phase,$offset,$csv,$seq) {
    global $uploadurl;
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'editing2'   => '#989898',
	'inactive'  => 'dimgrey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );
    

    $fields=[];
    $fields[]=$seq;
    $fields[]=$account['acc_id'].'<br>'.
	      strform('usr_data','',true)."<input type='submit' value='Detalhes'/></form><br>".
	      maillink($account['email'],'SBAI17 - ','')."<br>$sqlrow[name] $sqlrow[familyname]";
    //    $fields[]=($sqlrow[name]).' '.($sqlrow[familyname]);
    $fields[]="$sqlrow[type_str]<br>$sqlrow[assoc_str]<br>$sqlrow[mod_str]<br>".
	      ($sqlrow['subs_value']+$sqlrow['mini_value'])." ($sqlrow[subs_value]+$sqlrow[mini_value])";
    //    $fields[]=$sqlrow['type_str'];
    //    $fields[]=$sqlrow['assoc_str'];
    //    $fields[]=$sqlrow['mod_str'];
    // $fields[]=$sqlrow['subs_value']+$sqlrow['mini_value']." ($sqlrow[subs_value]+$sqlrow[mini_value])";
    $fields[]="<a href='$uploadurl$sqlrow[receipt_file]' target='_BLANK'>$sqlrow[receipt_file]</a><br><hr>".
	      "<a href='$uploadurl$sqlrow[student_file]' target='_BLANK'>$sqlrow[student_file]</a><br><hr>".
	      "<a href='$uploadurl$sqlrow[sba_file]' target='_BLANK'>$sqlrow[sba_file]</a>";

    $fields[]="<b><font color='darkred'>$sqlrow[num_papers]</font></b>";
    $fields[]="<a href='$uploadurl$sqlrow[copy_file]' target='_BLANK'>$sqlrow[copy_file]</a>";

    $fields[]=($sqlrow['acc_obs']);
    if($sqlrow['usrdata_admin']) {
	$fields[]="<b><font color='red'>Editado</font></b>";
    } else {
	$fields[]="<b>Original</b>";
    }
    $fields[] = sec_buttonD($num,$phase,$offset,$sqlrow);
    

    $fields[] = sec_buttonB($num,$phase,$offset,$sqlrow);
    if($account['submitted']) {
	if($account['confirmed']) {
	    $fields[] = sec_buttonA($num,$phase,$offset,$sqlrow,true,false);
	    $bgcolor=$usrcolor['confirmed'];
	} else {
	    $fields[] = sec_buttonA($num,$phase,$offset,$sqlrow,true,true);
	    $bgcolor=$usrcolor['submitted'];
	}
    } else {
	if($account['activ']) {
	    if($account['can_edit']) {
		$fields[]='usuário ainda não submeteu';
		if($account['can_files']) {
		    $bgcolor=$usrcolor['editing'];
		} else {
		    $bgcolor=$usrcolor['editing2'];
		}
		
	    } else {
		$fields[] = sec_buttonA($num,$phase,$offset,$sqlrow,false,true);
		$bgcolor=$usrcolor['files'];
	    }
	} else {
	    $fields[]='conta não ativada';
	    $bgcolor=$usrcolor['inactive'];
	}
    }

    return array($fields,$bgcolor);
}


function minihead($csv) {
    $fields=[];
    $fields[]='#';
    $fields[]='Acc ID';
    $fields[]='Mini Curso';
    $fields[]='Reservado';
    $fields[]='Editado (Admin)'; 
    $fields[]='Overbook';
    $fields[]='Email';
    $fields[]='Tratamento';
    $fields[]='Título';
    $fields[]='Nome Completo';
    $fields[]='Nome Crachá';
    $fields[]='Sobrenome Crachá';
    $fields[]='Afiliação Crachá';
    $fields[]='Afiliação Completa';
    $fields[]='Telefone';
    $fields[]='País';
    $fields[]='Estado';
    $fields[]='Status';


    return $fields;
}

function minifields($account,$sqlrow,$num,$phase,$offset,$course,$accmini,$mini,$csv,$seq) {
    global $uploadurl;
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'editing2'   => '#989898',
	'inactive'  => 'grey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );
    

    $fields=[];
    $fields[]=$seq;
    $fields[]=$account['acc_id'];
    $fields[] = utf8_decode($mini['course_name']);
    if($accmini['reserved']) {
	$fields[]='reservado';
    } else {
	$fields[]="<font color='blue'>Lista de Espera</font>";
    }
    if($accmini['mini_admin']) {
	$fields[]="<font color='red'>editado</font>";
    } else {
	$fields[]='org';
    }
    if($accmini['overbook']) {
	$fields[]="<font color='red'>Overbook</font>";
    } else {
	$fields[]='normal';
    }

    $fields[]=$sqlrow['email'];
    $fields[]=$sqlrow['treat_str1'];
    $fields[]=$sqlrow['treat_str2'];
    $fields[]=$sqlrow['fullname'];
    $fields[]=$sqlrow['name'];
    $fields[]=$sqlrow['familyname'];
    $fields[]=$sqlrow['affiliation'];
    $fields[]=$sqlrow['fullaffiliation'];
    $fields[]=$sqlrow['phone'];
    $fields[]=$sqlrow['country_name'];
    $fields[]=$sqlrow['state_name'];

    if($account['submitted']) {
	if($account['confirmed']) {
	    $fields[] = 'Inscrição Confirmada';
	    $bgcolor=$usrcolor['confirmed'];
	} else {
	    $fields[] = 'Inscrição Não Confirmada';
	    $bgcolor=$usrcolor['submitted'];
	}
    } else {
	if($account['activ']) {
	    if($account['can_edit']) {
		$fields[]='usuário ainda não submeteu';
		if($account['can_files']) {
		    $bgcolor=$usrcolor['editing'];
		} else {
		    $bgcolor=$usrcolor['editing2'];
		}
	    } else {
		$fields[] = 'conta aguardando comprovantes';
		$bgcolor=$usrcolor['files'];
	    }
	} else {
	    $fields[]='conta inativa';
	    $bgcolor=$usrcolor['inactive'];
	}
    }

    return array($fields,$bgcolor);
}


function waitfields($account,$sqlrow,$num,$phase,$offset,$course,$accmini,$mini,$csv,$seq) {
    global $uploadurl;
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'editing2'   => '#989898',
	'inactive'  => 'grey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );
    

    $fields=[];
    $fields[]=$seq;
    $fields[]=$account['acc_id'];
    $fields[] = strform('usr_data','',true)."<input type='submit' value='Detalhes'/></form>";
    $fields[]=maillink($account['email'],'SBAI17 - ','')."<br>$sqlrow[name] $sqlrow[familyname]";
    //    $fields[]=($sqlrow[name]).' '.($sqlrow[familyname]);
    $fields[]="$sqlrow[type_str]<br>$sqlrow[assoc_str]<br>$sqlrow[mod_str]";
    //    $fields[]=$sqlrow['type_str'];
    //    $fields[]=$sqlrow['assoc_str'];
    //    $fields[]=$sqlrow['mod_str'];
    $fields[]=$sqlrow['subs_value']+$sqlrow['mini_value']." ($sqlrow[subs_value]+$sqlrow[mini_value])";
    $fields[]="<a href='$uploadurl$sqlrow[receipt_file]' target='_BLANK'>$sqlrow[receipt_file]</a>";
    $fields[]="<a href='$uploadurl$sqlrow[student_file]' target='_BLANK'>$sqlrow[student_file]</a>";
    $fields[]="<a href='$uploadurl$sqlrow[sba_file]' target='_BLANK'>$sqlrow[sba_file]</a>";

    $fields[]=utf8_decode($sqlrow['acc_obs']);
    if($sqlrow['usrdata_admin']) {
	$fields[]="<b><font color='red'>Editado</font></b>";
    } else {
	$fields[]="<b>Original</b>";
    }
    $fields[] = sec_buttonB($num,$phase,$offset,$sqlrow,$course);
    if($account['submitted']) {
	if($account['confirmed']) {
	    $fields[] = sec_buttonC($num,$phase,$offset,$sqlrow,$course,true,false,'conta confirmada');
	    $bgcolor=$usrcolor['confirmed'];
	} else {
	    $fields[] = sec_buttonC($num,$phase,$offset,$sqlrow,$course,true,true,'conta não confirmada');
	    $bgcolor=$usrcolor['submitted'];
	}
    } else {
	if($account['activ']) {
	    if($account['can_edit']) {
		$fields[]='usuário ainda não submeteu';
		if($account['can_files']) {
		    $bgcolor=$usrcolor['editing'];
		} else {
		    $bgcolor=$usrcolor['editing2'];
		}
	    } else {
		$fields[] = sec_buttonC($num,$phase,$offset,$sqlrow,$course,false,true,'conta aguardando comprovantes');
		$bgcolor=$usrcolor['files'];
	    }
	} else {
	    $fields[]='conta inativa';
	    $bgcolor=$usrcolor['inactive'];
	}
    }

    return array($fields,$bgcolor);
}


function csv_accounts($phase,$queryA,$queryB) {
    global $microstamp;
    global $mysqli;
    global $DBVALS;
    global $MAXSIZE;
    global $uploadurl;
    global $loginacc;
    global $loginadmin;
    //    global $NRECORDS;
    $NRECORDS = 1000;
    
    if ($filter) {
	$filter = "WHERE $filter";
    };
    
    
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'inactive'  => 'grey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );
    

    if ($_POST['offset']) {
	$offset=$_POST['offset'];
    } else {
	$offset=0;
    }


    if (!($result = $mysqli->query($queryA))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $sqlrow = $result->fetch_assoc();
    $navline=[];
    if($offset > 0) {
	$navline[]=strform($phase,'').strhiddenval('offset',$offset-$NRECORDS).strsubmit('submit','Previous').'</form>';
    } else {
	$navline[]='';
    }
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]=''; 
    $navline[]='';
    $navline[]=''; 
    $navline[]=''; 
    $navline[]=''; 
    $navline[]='';
    $navline[]=''; 
    $navline[]='';
    $navline[]='';
    $navline[]=''; 
    $navline[]='';
    $navline[]='';
    
    
    if (($offset+$NRECORDS) < $sqlrow['count']) {
	$navline[]=strform($phase,'').strhiddenval('offset',$offset+$NRECORDS).strsubmit('submit','Next').'</form>';
    } else {
	$navline[]='';
    }


    $result->close();
    
    $last=$offset+$NRECORDS;
    if ($last > $sqlrow['count']) {
	$last=$sqlrow['count'];
    }
    
    echo "<h2>Lista Confirmados</h2><h4> (".(${offset}+1)." à $last)</h4>
    <div class='row'><div class='col-sm-12'>
    <table id='regform' style='width=120%'>\n";

    
    //    if (!($result = $mysqli->query("SELECT * FROM `account` $filter ORDER BY `email` LIMIT $offset,$NRECORDS;"))) {
    if (!($result = $mysqli->query($queryB))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    



    echo "<tr>";
    $fields=[];
    $fields[]='#';
    $fields[]='Acc ID';
    $fields[]='Status';
    $fields[]='Email';
    $fields[]='Tratamento';
    $fields[]='Título';
    $fields[]='Nome Completo Comprovante';
    $fields[]='Nome Completo Recibo';
    $fields[]='Dados Recibo';
    $fields[]='Nome Crachá';
    $fields[]='Sobrenome Crachá';
    $fields[]='Afiliação Crachá';
    $fields[]='Afiliação Completa';
    $fields[]='Telefone';
    $fields[]='País';
    $fields[]='Estado';
    $fields[]='Tipo';
    $fields[]='Associação';
    $fields[]='Modalidade';
    $fields[]='# Papers';
    $fields[]='Valor'; 
    $fields[]='Valor (sec)'; 
    $fields[]='Editado (admin)'; 
    $fields[]='Rec/NF'; 
    $fields[]='Forma Pag.'; 
    $fields[]='regular?'; 
    $fields[]='Obs '; 
    $fields[]='Obs (sec)'; 
    tdX($fields,'green','black','width:600px;');
    echo "</tr>";

    $num=0;
    while ($account=$result->fetch_assoc()) {

	$num++;
	$sqlrow=getuserdata(true,$account['hashadmin']);
	$fields=[];
	$fields[]=$num;
	$fields[]=$sqlrow['acc_id'];

	if ($sqlrow['confirmed']) {
	    $fields[]='confirmado';
	} else {
	    if ($sqlrow['submitted']) {
		$fields[]='<b>Submetido</b>';
	    } else {
		$fields[]="<font color='red'><b>Pendente</b></font>";
	    }
	    
	}
	
	$fields[]=$sqlrow['email'];
	$fields[]=$sqlrow['treat_str1'];
	$fields[]=$sqlrow['treat_str2'];
	$fields[]=$sqlrow['fullname'];
	$fields[]=$sqlrow['fullnamereceipt'];
	$fields[]=$sqlrow['receipt_data'];
	$fields[]=$sqlrow['name'];
	$fields[]=$sqlrow['familyname'];
	$fields[]=$sqlrow['affiliation'];
	$fields[]=$sqlrow['fullaffiliation'];
	$fields[]=$sqlrow['phone'];
	$fields[]=$sqlrow['country_name'];
	$fields[]=$sqlrow['state_name'];
	$fields[]=$sqlrow['type_desc2'];
	$fields[]=$sqlrow['assoc_desc'];
	$fields[]=$sqlrow['mod_desc'];
	$fields[]=$sqlrow['num_papers'];
	$fields[]=$sqlrow['subs_value']+$sqlrow['mini_value']; 
	$fields[]=$sqlrow['sec_value'];
	if($sqlrow['usrdata_admin']) {
	    $fields[]="<b><font color='red'>Editado</font></b>";
	} else {
	    $fields[]="<b>Original</b>";
	}
	if($sqlrow['receipt_type']) {
	    $fields[]='Recibo';
	} else {
	    $fields[]='NF';
	}
	if($sqlrow['exception']) {
	    $fields[]="<font color='red'><b>Exceção</b></font>";
	} else {
	    $fields[]='Regular';
	}
	switch($sqlrow['payment']) {
	    case 0:
		$fields[]='Depósito';
		break;
	    case 1:
		$fields[]='Boleto';
		break;
	    case 2:
		$fields[]='Cartão';
		break;
	    case 3:
		$fields[]='Empenho';
		break;
	}

	
	$fields[]=$sqlrow['acc_obs'];
	$fields[]=$sqlrow['sec_obs'];

	trX($fields,$bgcolor,'black');
    }
    $result->close();
    trX($navline,'lightblue','darkyellow');
    echo "</table>";

    echo "</div></div>\n";
}




//###########
//###########
function bac_1($admin){
    echo sec_menu($admin);
    $queryA="SELECT COUNT(*) as count FROM bacalhau;";
    $queryB="SELECT * FROM bacalhau  ORDER BY status,paper_num,author;";
    bacalhau('bac_1',$queryA,$queryB,"TODOS");
    echo sec_end();
}


function bac_2($admin){
    echo sec_menu($admin);
    $filter="WHERE (status = 'Ninguem Associado') OR (status LIKE '%submetido')";
    $queryA="SELECT COUNT(*) as count FROM bacalhau $filter;";
    $queryB="SELECT * FROM bacalhau  $filter ORDER BY status,paper_num,author;";
    bacalhau('bac_2',$queryA,$queryB,"não assoc/submetido");
    echo sec_end();
}


function bac_3($admin){
    echo sec_menu($admin);
    $filter="WHERE (status = 'Ninguem Associado') OR (status LIKE '%submetido' AND annotation = '0')";
    $queryA="SELECT COUNT(*) as count FROM bacalhau $filter;";
    $queryB="SELECT * FROM bacalhau  $filter ORDER BY status,paper_num,author;";
    bacalhau('bac_3',$queryA,$queryB,"lista final");
    echo sec_end();
}


function bac_email($admin){
    echo sec_menu($admin);
    echo "<b>NOT DONE YET</b><br>";
    $filter="WHERE (status = 'Ninguem Associado') OR (status LIKE '%submetido' AND annotation = '0')";
    $queryA="SELECT COUNT(*) as count FROM bacalhau $filter;";
    $queryB="SELECT * FROM bacalhau  $filter ORDER BY status,paper_num,author;";
    bacmail('bac_email',$queryA,$queryB,"placeholder");
    echo sec_end();
}


function bacalhau($phase,$queryA,$queryB,$str) {
    global $microstamp;
    global $mysqli;
    global $DBVALS;
    global $MAXSIZE;
    global $loginacc;
    global $loginadmin;
    $NRECORDS = 1000;
    
    
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'inactive'  => 'grey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );
    
    if (!($result = $mysqli->query($queryA))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $sqlrow = $result->fetch_assoc();
    $navline=[];
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    $navline[]='';
    

    $result->close();
    
    $last=$sqlrow['count'];
    
    echo "<h2>Bacalhau: $str</h2><h4> (1 à $last)</h4>
    <div class='row'><div class='col-sm-12'>
    <table id='regform' style='width=120%'>\n";

    
    if (!($result = $mysqli->query($queryB))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    



    echo "<tr>";
    $fields=[];
    $fields[]='#';
    $fields[]='paper #';
    $fields[]='status';
    $fields[]='anotação';
    $fields[]='autor';
    $fields[]='email';
    $fields[]='título';
    tdX($fields,'green','black','width:600px;');
    echo "</tr>";

    $num=0;
    while ($sqlrow=$result->fetch_assoc()) {

	$num++;
	$fields=[];
	$fields[]=$num;
	$fields[]=$sqlrow['paper_num'];
	$fields[]=utf8_decode($sqlrow['status']);
	$fields[]=utf8_decode($sqlrow['annotation']);
	$fields[]=utf8_decode($sqlrow['author']);
	$fields[]=utf8_decode($sqlrow['email']);
	$fields[]=utf8_decode($sqlrow['paper_title']);

	trX($fields,$bgcolor,'black');
    }
    $result->close();
    echo "</table>";

    echo "</div></div>\n";
}



function bacmail($phase,$queryA,$queryB,$str) {
    global $microstamp;
    global $mysqli;
    global $DBVALS;
    global $MAXSIZE;
    global $loginacc;
    global $loginadmin;
    $NRECORDS = 1000;
    
    
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'inactive'  => 'grey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );
    
    if (!($result = $mysqli->query($queryA))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $sqlrow = $result->fetch_assoc();
    

    $result->close();
    
    $last=$sqlrow['count'];
    
    echo "<h2>Bacalhau Emails: $str</h2><h4> (1 à $last)</h4>
    <div class='row'><div class='col-sm-12'>
    \n";

    
    if (!($result = $mysqli->query($queryB))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    




    $num=0;
    while ($sqlrow=$result->fetch_assoc()) {

	$num++;
	$str=
	    "Prezado(a) ".utf8_decode($sqlrow['author']).",

Detectamos que não houve inscrição profissional associada a seu artigo
Número: ".utf8_decode($sqlrow['paper_num'])."
Título: ".utf8_decode($sqlrow['paper_title'])."

Infelizmente, informamos que, conforme alertado reiteradas vezes, seu artigo será removido do programa do evento.

Att.,

Prof. João Manoel Gomes da Silva Jr.
Prof. Carlos Eduardo Pereira
\n\n SBAI17\n 1-4 de Outubro de 2017\nhttps://www.ufrgs.br/sbai17
";
	echo strtr($str,array("\n"=> '<br>'))."<hr>\n";
	//mail($sqlrow['email'],"SBAI17 - Exclusão de Artigo do Programa",$str,"From: sbai17@ufrgs.br");
	echo "enviado para, ".utf8_decode($sqlrow['author']).", paper: ,".$sqlrow['paper_num'].', Título: ,"'.utf8_decode($sqlrow['paper_title']).'"<br>';

	
    }
    $result->close();

    echo "</div></div>\n";
}



//##############
function mailing($admin){
    echo sec_menu($admin);
    $filter="";
    $first=1;
    $str="";
    if ($_POST['confirmed']) {
	$rule = " (confirmed = '1')";
	$str .= "confirmados, ";
	if ($first) {
	    $first = 0;
	    $filter .= $rule;
	} else {
	    $filter .= " OR $rule";
	}
    }
    if ($_POST['nonconfirmed']) {
	$rule = " (confirmed = '0' AND submitted = '1')";
	$str .= "aguardando confirmação, ";
	if ($first) {
	    $first = 0;
	    $filter .= $rule;
	} else {
	    $filter .= " OR $rule";
	}
    }
    if ($_POST['paycheck']) {
	$rule = " (paycheck = '1')";
	$str .= "valor OK, ";

	if ($first) {
	    $first = 0;
	    $filter .= $rule;
	} else {
	    $filter .= " OR $rule";
	}
    }
    if ($_POST['nonpaycheck']) {
	$rule = " (paycheck = '0' AND can_files = '1')";
	$str .= "valor Não OK, ";

	if ($first) {
	    $first = 0;
	    $filter .= $rule;
	} else {
	    $filter .= " OR $rule";
	}
    }
    if ($_POST['pending']) {
	$rule = " (can_files = '1' AND submitted = '0')";
	$str .= "Cinza Claro, ";

	if ($first) {
	    $first = 0;
	    $filter .= $rule;
	} else {
	    $filter .= " OR $rule";
	}
    }
    if ($_POST['filespending']) {
	$rule = " (can_files = '1' AND submitted = '0' AND can_edit = '0')";
	$str .= "Amarelo, ";

	if ($first) {
	    $first = 0;
	    $filter .= $rule;
	} else {
	    $filter .= " OR $rule";
	}
    }
    if ($_POST['pendingwithpaper']) {
	$rule = " (submitted = '0' AND num_papers > '0')";
	$str .= "Pendentes com Artigos, ";

	if ($first) {
	    $first = 0;
	    $filter .= $rule;
	} else {
	    $filter .= " OR $rule";
	}
    }



    $filter = "WHERE account.acc_id = userdata.acc_id AND userdata.treat_id = treatment.treat_id AND ($filter)";
    $queryA="SELECT COUNT(*) as count FROM account,userdata,treatment $filter;";
    $queryB="SELECT * FROM account,userdata,treatment $filter ORDER BY email;";
    sendmailing('mailing',$queryA,$queryB,$str,$_POST['mailing_txt'],$_POST['test']);
    echo sec_end();
}

function sendmailing($phase,$queryA,$queryB,$str,$msg,$test) {
    global $microstamp;
    global $mysqli;
    global $DBVALS;
    global $MAXSIZE;
    global $loginacc;
    global $loginadmin;
    $NRECORDS = 1000;
    
    echo javascripts();
    
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'inactive'  => 'grey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );

    

    
    if (!($result = $mysqli->query($queryA))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $sqlrow = $result->fetch_assoc();
    

    $result->close();
    
    $last=$sqlrow['count'];
    
    echo "<h2>Bacalhau Mailing List: $str</h2><h4> (1 à $last)</h4>
    <div class='row'><div class='col-sm-12'>
    \n";

    
    if (!($result = $mysqli->query($queryB))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    




    $num=0;
    while ($sqlrow=$result->fetch_assoc()) {

	$num++;
	$name=utf8_decode($sqlrow['name'])." ".utf8_decode($sqlrow['familyname']);
	$mesg = strtr($msg,array("<nome>"=> $name,"<titulo>"=>"$sqlrow[treat_str2]","<tratamento>"=>"$sqlrow[treat_str1]"));

	if ($test) {
	    
	    echo "<hr>".
		 "Assunto: $_POST[mailing_sbj]<br>".strtr($mesg,array("\n"=> '<br>'))."<br>\n";
	} else {
	    mail($sqlrow['email'],$_POST['mailing_sbj'],$mesg,"From: sbai17@ufrgs.br");
	}
	echo "enviado para, ".utf8_decode($sqlrow['name'])." ".$sqlrow['familyname'].', email: ,"'.utf8_decode($sqlrow['email']).'"<br>';

	
    }
    $result->close();

    echo "</div></div>\n";
}



//###############
//###############

function sessions($phase,$csv,$filter='') {
    global $microstamp;
    global $mysqli;
    global $DBVALS;
    global $MAXSIZE;
    global $uploadurl;
    global $loginacc;
    global $loginadmin;
    global $NRECORDS;

    if ($filter) {
	$filter = "WHERE $filter";
    };
    
    
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'inactive'  => 'grey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );
    

    
    echo "<h2>Lista Sessões</h2>
    <div class='row'><div class='col-sm-12'>
    <table id='regform' style='width=120%'>\n";

    
    if (!($result = $mysqli->query("SELECT * FROM `sessions` $filter ORDER BY session_date, session_begin, session_id;"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    echo "<tr>";
    $fields=[];
    $fields[]='# Sessão';
    $fields[]='Título Sessão';
    $fields[]='Chair';
    $fields[]='Co-chair';
    
    $fields[]='Dia';
    $fields[]='Horário (sessão)';
    $fields[]='Horário (paper)';
    $fields[]='Local';
    $fields[]='# Artigo';
    $fields[]='Título';
    $fields[]='Autores';
    $fields[]='Apresentador';
    $fields[]='Inscrição';
    if(!$csv) {
	$fields[]='Autor de Contato';
	$fields[]='Email de Contato';
    }
    
    tdX($fields,'green','black','width:600px;');
    echo "</tr>";

    $num=0;
    while ($session=$result->fetch_assoc()) {

	$num++;
	if (!($result2 = $mysqli->query("SELECT * FROM `papers`,abstracts WHERE session_id = '$session[session_id]' AND papers.paper_num = abstracts.paper_num ORDER BY paper_time;"))) {
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    return NULL;
	}
	$num2=0;
	$fields=[];
	$fields[]=($session['session_id']);
	$fields[]=utf8_decode($session['session_title']);
	$fields[]=utf8_decode($session['session_chair']);
	$fields[]=utf8_decode($session['session_cochair']);
	$session['session_date']=preg_replace('/10\/(\d{1})\/2017/','\1/10/2017',$session['session_date']);
	$fields[]=$session['session_date'];

	$fields[]=utf8_decode($session['session_time']);
	$fields[]='';
	$fields[]=utf8_decode($session['session_local']);
	trX($fields,'cyan','black');
	
	while($paper=$result2->fetch_assoc()) {
	    $num2++;
	    
	    if (!($result3 = $mysqli->query("SELECT * FROM `account`,`userdata`,acc_papers WHERE account.acc_id = userdata.acc_id AND acc_papers.acc_id = account.acc_id AND acc_papers.paper_num = '$paper[paper_num]';"))) {
		echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
		return NULL;
	    }
	    if ($result3->num_rows) {
		
		while($speaker=$result3->fetch_assoc()) {
		    $fields=[];
		    $fields[]=($session['session_id']);
		    $fields[]=utf8_decode($session['session_title']);
		    $fields[]=utf8_decode($session['session_chair']);
		    $fields[]=utf8_decode($session['session_cochair']);
		    $fields[]=$session['session_date'];
		    $fields[]=utf8_decode($session['session_time']);
		    $fields[]=utf8_decode($paper['paper_time']);
		    $fields[]=utf8_decode($session['session_local']);
		    $fields[]=$paper['paper_num'];
		    $fields[]=utf8_decode(mb_convert_case($paper['paper_title'],MB_CASE_UPPER,'UTF-8'));
		    $fields[]=utf8_decode(str_ireplace(' and ',' e ',mb_convert_case($paper['authors'],MB_CASE_TITLE,'UTF-8')));
		    if($speaker['self']) {
			$fields[]=utf8_decode($speaker['fullname']);
		    } else {
			$fields[]=utf8_decode($speaker['speaker']);
		    }
		    if($speaker['confirmed']) {
			$fields[]='inscrição confirmada';
			$bgcolor='lightred';
		    } else {
			$fields[]='não confirmado';
			$bgcolor='lightblue';
		    }
		    if(!$csv){
			$fields[]=utf8_decode($paper['paper_author']);
			$fields[]=maillink($paper['paper_email'],'SBAI17 - Artigo não associado a uma Inscrição',"Prezado ".utf8_decode($paper[paper_author])."\n\nAté o momento não identificamos nenhuma inscrição associada ao seu Artigo #$paper[paper_num]:\n\n    ".utf8_decode($paper[paper_title])."\n\nPor favor entre em contato com a secretaria o mais rapidamente possível.");
		    }


		    trX($fields,$bgcolor,'black');
		}

	    } else {
		$fields=[];
		$fields[]=($session['session_id']);
		$fields[]=utf8_decode($session['session_title']);
		$fields[]=utf8_decode($session['session_chair']);
		$fields[]=utf8_decode($session['session_cochair']);
		$fields[]=$session['session_date'];
		$fields[]=utf8_decode($session['session_time']);
		$fields[]=utf8_decode($session['paper_time']);
		$fields[]=utf8_decode($session['session_local']);
		$fields[]=$paper['paper_num'];
		$fields[]=utf8_decode($paper['paper_title']);
		$fields[]=utf8_decode(str_ireplace(' and ',' e ',mb_convert_case($paper['authors'],MB_CASE_TITLE,'UTF-8')));
		$fields[]='Ninguem Associado';
		$fields[]='Ninguem Associado';
		if(!$csv){
		    $fields[]=utf8_decode($paper['paper_author']);
		    $fields[]=maillink($paper['paper_email'],'SBAI17 - Artigo não associado a uma Inscrição',"Prezado ".utf8_decode($paper[paper_author])."\n\nAté o momento não identificamos nenhuma inscrição associada ao seu Artigo #$paper[paper_num]:\n\n    ".utf8_decode($paper[paper_title])."\n\nPor favor entre em contato com a secretaria o mais rapidamente possível.");
		}

		trX($fields,'yellow','darkred');
	    }

	}
    }
    $result->close();
    trX($navline,'lightblue','darkyellow');
    echo "</table>";

    echo "</div></div>\n";
}


/////////////////
/////////////////
function papers($phase,$csv,$filter='') {
    global $microstamp;
    global $mysqli;
    global $DBVALS;
    global $MAXSIZE;
    global $uploadurl;
    global $loginacc;
    global $loginadmin;
    global $NRECORDS;

    if ($filter) {
	$filter = "WHERE $filter";
    };
    
    
    $usrcolor= array(
	'confirmed' => 'lightgreen',
	'submitted'  => 'lightyellow',
	'editing'   => 'lightgrey',
	'inactive'  => 'grey',
	'files'     => 'yellow',
	'secretary' => 'lightblue',
	'admin'     => 'blue'
    );
    

    
    echo "<h2>Lista Artigos</h2>
	    <div class='row'><div class='col-sm-12'>
	    <table id='regform' style='width=120%'>\n";

    
    if (!($result = $mysqli->query("SELECT * FROM `papers` $filter ORDER BY `paper_num`;"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    echo "<tr>";
    $fields=[];
    $fields[]='#';
    $fields[]='# Artigo';
    $fields[]='# Páginas';
    $fields[]='--Acc ID--';
    $fields[]='Sessão';
    $fields[]='Horário';
    $fields[]='Título';
    $fields[]='Apresentador';
    $fields[]='Inscrição';
    if(!$csv) {
	$fields[]='Autor de Contato';
	$fields[]='Email de Contato';
	$fields[]='Anotações';
    }

    
    tdX($fields,'green','black','width:600px;');
    echo "</tr>";

    $num=0;
    $totconfirm=0;
    $totnonassoc=0;
    $totnonconfirm=0;
    $totnonsubmitted=0;
    $Dconfirm=0;
    $Dnonassoc=0;
    $Dnonconfirm=0;
    $Dnonsubmitted=0;
    $totdup=0;
    $totxtra=0;
    
    while ($paper=$result->fetch_assoc()) {

	$num++;
	if (!($result2 = $mysqli->query("SELECT * FROM `acc_papers`,`account`,`userdata` WHERE acc_papers.paper_num = '$paper[paper_num]' AND acc_papers.acc_id=account.acc_id AND account.acc_id = userdata.acc_id;"))) {
	    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	    return NULL;
	}
	if ($result2->num_rows) {
	    $txtcolor='black';
	    if($result2->num_rows > 1) {
		$txtcolor='red';
	    };
	    
	    
	    while($speaker=$result2->fetch_assoc()) {
		$fields=[];
		$fields[]=$num;
		$fields[]=$paper['paper_num'];
		$fields[]=$speaker['xtra_pages']+6;
		$totxtra += $speaker['xtra_pages'];
		$fields[]=$speaker['acc_id'];
		$fields[]=$paper['session_id'];
		$fields[]=$paper['paper_time'];
		$fields[]=utf8_decode($paper['paper_title']);
		if($speaker['self']) {
		    $fields[]=utf8_decode($speaker['fullname']);
		} else {
		    $fields[]=utf8_decode($speaker['speaker']);
		}
		if($speaker['confirmed']) {
		    $fields[]='inscrição confirmada';
		    $bgcolor='lightred';
		    $totconfirm+=1;
		    if($result2->num_rows >1) {
			$totdup+=1;
			$Dconfirm+=1;
		    }
		    
		} else {
		    if($speaker['submitted']) {
			$fields[]='não confirmado';
			$bgcolor='lightgreen';
			$totnonconfirm+=1;
			if($result2->num_rows >1) {
			    $totdup+=1;
			    $Dnonconfirm+=1;
			}
		    } else {
			$fields[]='não submetido';
			$bgcolor='lightblue';
			$totnonsubmitted+=1;
			if($result2->num_rows >1) {
			    $totdup+=1;
			    $Dnonsubmitted+=1;
			}
		    }
		    
		    
		}
		if(!$csv){
		    $fields[]=utf8_decode($paper['paper_author']);
		    $fields[]=maillink($paper['paper_email'],'SBAI17 - Artigo não associado a uma Inscrição',"Prezado ".utf8_decode($paper[paper_author])."\n\nAté o momento não identificamos nenhuma inscrição associada ao seu Artigo #$paper[paper_num]:\n\n    ".utf8_decode($paper[paper_title])."\n\nPor favor entre em contato com a secretaria o mais rapidamente possível.");
		    $fields[]=utf8_decode($speaker['sec_obs']);
		}
		if ($result2->num_rows > 1) {
		    $fields[]='DUP';
		}
		

		trX($fields,$bgcolor,$txtcolor);
	    }
	    
	} else {
	    $fields=[];
	    $fields[]=$num;
	    $fields[]=$paper['paper_num'];
	    $fields[]='';
	    $fields[]='';
	    $fields[]=utf8_decode($paper['paper_title']);
	    $fields[]='Ninguem Associado';
	    $fields[]='Ninguem Associado';
	    if(!$csv){
		$fields[]=utf8_decode($paper['paper_author']);
		$fields[]=maillink($paper['paper_email'],'SBAI17 - Artigo não associado a uma Inscrição',"Prezado ".utf8_decode($paper[paper_author])."\n\nAté o momento não identificamos nenhuma inscrição associada ao seu Artigo #$paper[paper_num]:\n\n    ".utf8_decode($paper[paper_title])."\n\nPor favor entre em contato com a secretaria o mais rapidamente possível.");
	    }
	    $fields[]='';
	    trX($fields,'yellow','darkred');
	    $totnonassoc+=1;

	}
	
	
    }
    $result->close();
    trX($navline,'lightblue','darkyellow');
    $fields=[];
    trX($fields,'grey','black');
    $fields[]='Tot. Confirmados';
    $fields[]='Tot. Não Confirmados';
    $fields[]='Tot. Não Submetidos';
    $fields[]='Tot. Não Associados';
    $fields[]='Tot. Duplicados';
    $fields[]='Tot. Páginas Extras';
    trX($fields,'green','black');
    $fields=[];
    $fields[]=$totconfirm.'(dups:'.$Dconfirm.')';
    $fields[]=$totnonconfirm.'(dups:'.$Dnonconfirm.')';
    $fields[]=$totnonsubmitted.'(dups:'.$Dnonsubmitted.')';
    $fields[]=$totnonassoc;
    $fields[]=$totdup;
    $fields[]=$totxtra;
    trX($fields,'lightgreen','black');

    echo "</table>";

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




