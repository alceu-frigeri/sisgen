<?php

function pagesvalueXX($type,$sba,$mod,$npapers,$xpags,$minival) {
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



function usr_main($accdt,$usrdt,$admin=NULL) {

    global $microstamp;
    global $mysqli;
    global $DBVALS;
    global $MAXSIZE;
    global $uploadurl;
    global $receiptdir;
    global $receipturl;
    global $earlytime;
    global $paperstime;
    global $sysclosed;

    $now=time();

    $cansubmit=true;

    echo "<table bgcolor='#ffffff'>
    <tbody>
    <tr>
    <p align='center'><span class='style14'></span></p>
    <table width='75%' border='1' align='center'>
    <tr>
    <td>";
    include "RegDescription.php";
    echo "</td>
    </tr></tbody>
    </table>";
    
    echo "<div class='row'><div class='col-sm-14'>
    <h2>Instruções</h2><hr>
    <table bgcolor='#ffffff'>
    <tr>
    <p align='center'><span class='style14'></span></p>
    <table width='75%' border='1' align='center'>
    <tr>
    <td>
    <b><font color='red'>IMPORTANTE:</font></b> Uma vez submetida a inscrição, você não poderá mais editar seus dados.<br><br>
    <b>1o.</b> Preencha os dados pessoais.<br>
    <b>2o.</b> Preencha os detalhes de inscrição.<br>
    <b>3o.</b> (se pertinente) Faça o Download do documento de transferencia de Copyright.<br>
    <b>4o.</b> Efetue o depósito bancário no valor indicado.<br>
    <b>5o.</b> Insira os comprovantes (Pagamento/Associação/Estudante/Copyright).<br>
    <b>6o.</b> <font color='red'>Revise tudo.</font><br>
    <b>7o.</b> Submeta a sua inscrição.<br><br>
    <b>8o.</b> Em alguns dias (após a submissão dos dados) você deverá receber um Email da secretaria confirmando sua inscrição.<br>
    </td>
    </tr>
    </table>

    ";

    echo "<h2>1o. Dados Pessoais</h2><hr>";

    echo "
    <table id='regform' style='width=70%'>\n";
    tablineB('Tratamento:',$usrdt['treat_str2'],'white','black');
    tablineB('Nome Completo (comprovantes):',($usrdt['fullname']),'white','black');
    tablineB('Nome Completo (recibo):',($usrdt['fullnamereceipt']),'white','black');
    tablineB('Dados adicionais (recibo):',($usrdt['receipt_data']),'white','black');
    tablineB('Afiliação (recibo):',($usrdt['fullaffiliation']),'white','black');
    tablineB('Nome (Crachá):',($usrdt['name']),'white','black');
    tablineB('Sobrenome (Crachá):',($usrdt['familyname']),'white','black');
    tablineB('Sigla Afiliação (Crachá):',($usrdt['affiliation']),'white','black');
    tablineB('Telefone:',$usrdt['phone'],'white','black');
    tablineB('Pais:',$usrdt['country_name'],'white','black');
    tablineB('Estado:',$usrdt['state_name'],'white','black');
    echo "</table>\n";
    if(($accdt['can_edit'] && !$admin && !$sysclosed) || ($admin == $DBVALS['DB_ADMIN'])) {
    	regform('usrdata_edit','return validate()');
	echo "
   <input type='submit' value='Editar Dados Pessoais'/>
    </form>\n";
    }

    echo "<h2>2o. Detalhes de Inscrição</h2><hr>";
    echo "
    <table id='regform' style='width=70%'>\n";

    if (($earlytime < $now) && (!$accdt['submitted']) && ($usrdt['type_id'] == $DBVALS['DB_EARLY']  )) {
	tablineB('Tipo:',$usrdt['type_desc'].' (prazo esgotado)','white','red');
	$cansubmit=false;
    } else {
	tablineB('Tipo:',$usrdt['type_desc'],'white','black');
    }
    

    
    tablineB('Associação:',$usrdt['assoc_desc'],'white','black');
    tablineB('Modalidade:',$usrdt['mod_desc'],'white','black');

    if( (!($accdt['submitted'])) && ($paperstime < $now) && ($usrdt['num_papers'] != 0)) {
	tablineB('Núm. de Artigos:',utf8_decode($usrdt[npap_str1]).' (prazo esgotado)','white','red');
	$cansubmit=false;
    } else {
	tablineB('Núm. de Artigos:',utf8_decode($usrdt[npap_str1]),'white','black');
    }

    tablineB('','','white','white');

    if (!($result = $mysqli->query("SELECT * FROM `acc_papers`,`papers`,`pag_str` WHERE acc_id = '$usrdt[acc_id]'  AND papers.paper_num = acc_papers.paper_num AND acc_papers.xtra_pages = pag_str.xtra_pages ORDER BY acc_papers.`paper_num` "))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }

    $xtrapag=0;
    for ($num=1; $num <= $usrdt['num_papers']; $num++) {
	$color = $num % 2;
	if ($color == 0) {
     	    $bgcolor='white';
     	    $color='green';
	} else {
	    $bgcolor='#F4F4F8';
	    $color='blue';
	};
	$sqlrow=$result->fetch_assoc();
	if (!($result2 = $mysqli->query("SELECT * FROM `acc_papers`,`userdata`,`treatment`,account WHERE acc_papers.acc_id != '$usrdt[acc_id]' AND paper_num = '$sqlrow[paper_num]' AND acc_papers.acc_id = userdata.acc_id AND userdata.treat_id = treatment.treat_id AND acc_papers.acc_id = account.acc_id;"))) {
	    echo "Query2 failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if(($sqlrow2=$result2->fetch_assoc())) {
	    tablineB("${num}o. Artigo # ","$sqlrow[paper_num] (<a href='mailto:$sqlrow2[email]'>$sqlrow2[treat_str2] ".utf8_decode($sqlrow2[name])." ".utf8_decode($sqlrow2[familyname])."</a> também registrou este)",$bgcolor,'red');
	    $cansubmit=false;
	} else {
	    tablineB("${num}o. Artigo # ",$sqlrow['paper_num'],$bgcolor,'black');
	}
	tablineB("Título",utf8_decode($sqlrow['paper_title']),$bgcolor,'black');
	
	tablineB("Núm. de Pág. Extras ",utf8_decode($sqlrow['npag_str1']),$bgcolor,'black');
	$xtrapag += $sqlrow['xtra_pages'];
	if ($sqlrow['self']) {
	    tablineB("A ser apresentado por","eu mesmo",$bgcolor,'black');
	} else {
	    tablineB("A ser apresentado por",utf8_decode($sqlrow['speaker']),$bgcolor,'black');
	}

    };

    tablineB('','','white','white');
    if (!($result = $mysqli->query("SELECT * FROM `acc_mini`,`mini_courses`,`mini_shift` WHERE `acc_id` = '$accdt[acc_id]' AND mini_courses.course_id = acc_mini.course_id AND mini_courses.shift_id = mini_shift.shift_id;"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    $minival=0;
    while($sqlrow = $result->fetch_assoc()) {
	$color = $num % 2;
	$num++;
	if ($color == 0) {
     	    $bgcolor='white';
     	    $color='green';
	    $lcolor='lightgreen';
	} else {
	    $bgcolor='#F4F4F8';
	    $color='blue';
	    $lcolor='lightblue';
	};
	
	if($sqlrow['reserved']) {
	    tablineB('Mini Curso',utf8_decode($sqlrow['course_name'])." + R$".utf8_decode($sqlrow['course_val']).",00",$bgcolor,$color);
	} else {
	    $color='gray';	
	    tablineB('Mini Curso',utf8_decode($sqlrow['course_name'])." + R$0,00",$bgcolor,$color);
	}
	
	if($sqlrow['reserved']) {
	    tablineB('Reserva','Reservado<br> (OBS: Garantia de vaga somente após confirmação da inscrição)',$bgcolor,$lcolor);
	} else {
	    tablineB('Reserva','Você esta na Lista de espera; caso hajam desistências entraremos em contato.', $bgcolor,'#cfa10a');
	    //	    $cansubmit=false;
	}
	
	tablineB('Ministrante',utf8_decode($sqlrow['course_speaker']),$bgcolor,$color);
	//	tablineB('Descrição',utf8_decode($sqlrow['course_desc']),$bgcolor,$color);
	tablineB('Horário',utf8_decode($sqlrow['shift_str1']),$bgcolor,$color);
	if ($sqlrow['course_desc']) {
	    tablineB('Obs',utf8_decode($sqlrow['course_desc']),$bgcolor,'#cfa10a');
	    
	}
	
	$minival += $sqlrow['course_val'];

	
	
    }

    tablineB('','','white','white');
    tablineB("Valor da inscrição","R$ ".($usrdt['subs_value']+$usrdt['mini_value']).',00','#B0FFB0','green');


    echo "</table>
    <hr>
    <table id='regform' style='width=70%'>
";
    tablineB('Observações:',strtr(($usrdt['acc_obs']),array("\n"=> '<br>')),'white','blue');

    echo "
    </table>
";

    if(($accdt['can_edit'] && $usrdt && !$admin && !$sysclosed)  || ($admin == $DBVALS['DB_ADMIN'])) {
	regform('usrreg_edit','return validate()');
	echo "
    <input type='submit' value='Editar Detalhes de Inscrição'/>
    </form>\n";
    }

    echo "<h2>3o. Formulário de Transferência de Copyright</h2><hr>";
    if($usrdt['num_papers'] > 0) {
	echo "<p align='justify'>
	<b>Faça o download do Formulário, preencha os dados  (<font color='darkred'>autores, autor signatário, data e local</font>), assine, e submeta (abaixo) a digitalização do mesmo.</b><br>
<form  method='post' target='_BLANK' enctype='multipart/form-data' action='/sbai17/pages/copyright.php'>
        <input type='hidden' name='acc_id' value='$usrdt[acc_id]'/>
        <input type='submit' value='Formulário Copyright'/>
        
        </form>
	";
	
    } else {
	echo "<p align='justify'>
	Não se aplica.";
    }
    

    
    echo "<h2>4o. Dados para Depósito Bancário</h2><hr>
	
	<p align='justify'>
	Para efetuar o pagamento da inscrição, os participantes deverão realizar deposito bancário conforme dados a seguir.
					       </p>
	<table id='registration' style='width:50%'>
	<tr><td style='text-align:right; width:50%'>Banco:</td><td style='text-align:left'>Banco Do Brasil</td></tr>
	<tr><td style='text-align:right; width:50%'>Agencia:</td><td style='text-align:left'><b>3798-2</b></td></tr>
	<tr><td style='text-align:right; width:50%'>Conta:</td><td style='text-align:left'><b>302.001-0</b></td></tr>
	<tr><td style='text-align:right; width:50%'>CNPJ:</td><td style='text-align:left'><b>92.971.845/0001-42</b></td></tr>
	<tr><td style='text-align:right; width:50%'>Valor:</td><td style='text-align:left'><b>R$ ".($usrdt['subs_value']+$usrdt['mini_value'])."</b></td></tr>
	
	</table>
	";



    echo "<h2>5o. Comprovantes</h2><hr>";
    echo "
	<table id='regform' style='width=70%'>\n";

    if($usrdt['num_papers'] > 0) {
	$str='Formulário de transferência de Copyright';
	if(($accdt['can_files'] && !$admin) ||  ($admin == $DBVALS['DB_ADMIN'])) {
	    if($usrdt['copy_file']) {
		tabfileB($str,"<a href='$uploadurl$usrdt[copy_file]' target='_blank'>$usrdt[copy_file]</a>",'Copyright(s)','copy_file','black');
	    } else {
		tabfileB($str,"falta submeter",'Copyright(s)','copy_file','red');
		$cansubmit=false;
	    }
	    
	} else {
	    if($usrdt['copy_file']) {
		tablineB($str,"<a href='$uploadurl$usrdt[copy_file]' target='_blank'>$usrdt[copy_file]</a>",'white','black');
	    } else {
		tablineB($str,"falta submeter",'white','red');
		$cansubmit=false;
	    }
	}
    }
    
    
    if($usrdt['assoc_id'] == $DBVALS['DB_SBA']) {
	$str='Comprovante Sociedade SBA';
	if(($accdt['can_files'] && !$admin) ||  ($admin == $DBVALS['DB_ADMIN'])) {
	    if($usrdt['sba_file']) {
		tabfileB($str,"<a href='$uploadurl$usrdt[sba_file]' target='_blank'>$usrdt[sba_file]</a>",'Associação','sba_file','black');
	    } else {
		tabfileB($str,"falta submeter",'Associação','sba_file','red');
		$cansubmit=false;
	    }
	    
	} else {
	    if($usrdt['sba_file']) {
		tablineB($str,"<a href='$uploadurl$usrdt[sba_file]' target='_blank'>$usrdt[sba_file]</a>",'white','black');
	    } else {
		tablineB($str,"falta submeter",'white','red');
		$cansubmit=false;
	    }
	}
    }


    if($usrdt['mod_id'] == $DBVALS['DB_STUDENT']) {
	$str='Comprovante de Matrícula';
	if(($accdt['can_files'] && !$admin)  || ($admin == $DBVALS['DB_ADMIN'])) {
	    if($usrdt['student_file']) {
		tabfileB($str,"<a href='$uploadurl$usrdt[student_file]' target='_blank'>$usrdt[student_file]</a>",'Matrícula','student_file','black');
	    } else {
		tabfileB($str,"falta submeter",'Matrícula','student_file','red');
		$cansubmit=false;
	    }
	} else {
	    if($usrdt['student_file']) {
		tablineB($str,"<a href='$uploadurl$usrdt[student_file]' target='_blank'>$usrdt[student_file]</a>",'white','black');
	    } else {
		tablineB($str,"falta submeter",'white','red');
		$cansubmit=false;
	    }
	    
	}
	
	
    }
    $str='Comprovante de Depósito';
    //    echo "admin?::$admin<br>\n";
    if (($accdt['can_files'] && !$admin)  || ($admin == $DBVALS['DB_ADMIN'])) {
	if($usrdt['receipt_file']) {
	    tabfileB($str,"<a href='$uploadurl$usrdt[receipt_file]' target='_blank'>$usrdt[receipt_file]</a>",'Depósito','receipt_file','black');
	} else {
	    tabfileB($str,"falta submeter",'Depósito','receipt_file','red');
	    $cansubmit=false;
	}
    } else {
	if($usrdt['receipt_file']) {
	    tablineB($str,"<a href='$uploadurl$usrdt[receipt_file]' target='_blank'>$usrdt[receipt_file]</a>",'white','black');
	} else {
	    tablineB($str,"falta submeter",'white','red');
	    $cansubmit=false;
	}
    }


    echo "</table>\n";


    if ($accdt['submitted']) {
	echo "<h2>Status Inscrição</h2><hr>";
	if ($accdt['confirmed']) {
	    echo "<h4><font color='green'><center>Dados Confirmados<br>Obrigado pela sua Inscrição<br>Tenha um bom Congresso</center></font></h4>";
	} else {
	    echo "<h4><font color='blue'><center>Sua inscrição está pendente.<br>Em breve você receberá um Email confirmando sua inscrição.</center></font></h4>";
	}
    } else {
	echo "<h2>6o. Finalização</h2><hr>";
	if ($admin) {
	    echo "<h4><font color='red'>Usuário ainda não submeteu dados Finais !!!</font></h4><br>\n";
	    regform('usr_submit','return true');
	    echo "<input type='submit' value='Submeter Inscrição'/>
        </form>
	\n";
	    
	} else {
	    if ($sysclosed) {
		echo "<h4><font color='red'>Prazo para inscrições on-line encerrado. A partir de agora, apenas no local do congresso.</font></h4><br>\n";
	    } else {
		
		if ($usrdt && $accdt['can_files']) {
		    if ($cansubmit) {
			regform('usr_submit','return true');
			echo "                <b>OBS:</b> Uma vez submetida a inscrição você <b>não poderá mais alterar os dados!</b><br>
	<input type='submit' value='Submeter Inscrição'/>
        </form>
	\n";
		    } else {
			echo "<h4><font color='red'>Por favor, revise os dados acima (em vermelho) antes de sumeter</font></h4><br>\n";
		    }
		    
		} else {
		    echo "<h4><font color='red'>Por favor, preencha os formulários acima, primeiro</font></h4><br>\n";
		}
	    }
	}

    }


    if(file_exists("$receiptdir/Recibo_$usrdt[acc_id].pdf.pdf")) {
	echo "<h2>Recibo</h2><hr>";
	echo "<a href='$receipturl/Recibo_$usrdt[acc_id].pdf.pdf'>Seu Recibo já está disponível.</a><br>
Caso haja algum problema, ou divergência de dados, favor entrar em contato com a secretaria <a href='mailto:sbai17@ufrgs.br'>sbai17@ufrgs.br</a>.";
	
    }
    
    
    echo "</div></div>";


}

?>




