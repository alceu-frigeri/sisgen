<?php

//
// Account Register functions
//

function regacc_validate($gethash) {
    global $mysqli;
    global $regpage;
    
    echo "<h2>Email Confirmado</h2><hr>\n";
    $sql = "SELECT * FROM `account` WHERE `account`.`valhash` = '$gethash'";
    if(!($result = $mysqli->query($sql))) {
	printf("Error: %s<br>\n", $mysqli->error);
    }
    if ($result->num_rows) {
	echo "Obrigado por confirmar seu Email<br>\n";
	$sql ="UPDATE `sisgen`.`account` SET `activ` = '1' WHERE `account`.`valhash` = '$gethash'";
	$result = $mysqli->query($sql);
	if (!$result) {printf("Error: %s<br>\n", $mysqli->error);};
	echo "Agora você já pode se logar no sistema !<br>\n";
	reg_default();
    } else {
	echo "<font color='red'><b>Link Inválido ou Expirado</b></font><br>\n";
    }
}





function reg_subscribe() {
    global $mysqli;
    global $regpage;
    global $baseurl;
    global $microstamp;
    
    //    echo "<h2>Sistema de Inscrição</h2><hr>\n";
    //    echo "Dados submetidos:<br>\n";

    $email=$_POST['emailA'];
    $passwd=$_POST['passA'];
    $emailhash=md5($email);
    $hashadmin=md5("$email-$microstamp");
    $today = date('Y-m-d');
    //    echo "email: $email<br>\n";
    //    echo "password: $passwd<br>\n";
    //    echo "md5(email):$emailhash<br>\n";

    $email = $mysqli->real_escape_string($email);
    $passwd = $mysqli->real_escape_string($passwd);


    
    if (!($stmt = $mysqli->prepare("SELECT email,password,activ FROM `account` WHERE `email` = ?;"))) {
	echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    if (!$stmt->bind_param('s',$email)) {
	echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    };
    $result  = $stmt->execute();
    $stmt->bind_result($mail2,$pass2,$activ2);

    if ($stmt->fetch()) {
	if($activ2) {
	    echo "<h3>email já cadastrado!</h3> Acabamos de lhe re-enviar um Email com a sua senha de acesso.<br>\n";
	    mymail($mail2,"Senha de Acesso","Prezado(a)\n Sua senha é: $pass2");
	} else {
	    echo "<h3>email já cadastrado!</h3> Acabamos de lhe re-enviar um Email de ativação da sua conta.<br>\n";
	    $msg = "Obrigado por criar uma conta. \nPor favor, acesse o link abaixo para ativar a mesma\n\n".
   		   "${baseurl}?q=${regpage}&h=$emailhash\n\nAtt. Secretaria sisgen";
	    mymail($email,"Confirmação de Email",$msg);
	}
	
	
	$stmt->close();
    } else {
	$stmt->close();
	$type = T_CONG;


	$sql = "INSERT INTO `account` (`email`, `password`, `valhash`, `hashdate`) VALUES ('$email','$passwd','$emailhash','$today');";
	if (!($result=$mysqli->query($sql))) {
	    printf("<br>Error: %s<br>\n", $mysqli->error);
	}

	//     $mysqli->commit();
	echo "<h4>Obrigado por criar uma conta.</h4><br>
Você estará recebendo, em breve, um Email com instruções para ativar a sua conta.<br>";


	$msg = "Obrigado por criar uma conta. \nPor favor, acesse o link abaixo para ativar a mesma\n\n".
   	       "${baseurl}?q=${regpage}&h=$emailhash\n\nAtt. sisgen";
	mymail($email,"Confirmação de Email",$msg);

    } 
}




function reg_default() {
    global $regpage;

    $emailcheck="onkeyup='emailcheck()' onchange='emailcheck()' onblur='emailcheck()'";
    $passcheck="onkeyup='passcheck()' onchange='passcheck()' onblur='passcheck()'";

    
    $paglogin =
	"<h2>Sistema de Inscrição</h2>
     <hr>

<h4>Se o seu Email já estiver cadastrado, efetue o login. Caso contrário, preencha o formulário de cadastro.</h4>
<h5><font color='red'>Este cadastro <b>não</b> esta vínculado ao cadastro realizado no sistema de submissão (easychair). Você precisa criar uma conta local.</font></h5>

<div class='row'>
<div class='col-sm-8'>

     <input type='radio' name='type' id='type' value='login' checked onclick=\"formtype('login')\">Já estou cadastrado</input><br>
     <input type='radio' name='type' id='type' value='new-account' onclick=\"formtype('new-account')\">Meu Email não está cadastrado</input><br>
     <input type='radio' name='type' id='type' value='forgot' onclick=\"formtype('forgot')\">Esqueci minha senha</input><br>
     <input type='radio' name='type' id='type' value='re-send' onclick=\"formtype('re-send')\">Re-enviar Email de confirmação de conta</input>


 <div id='new-account' hidden>

<form onsubmit='return validate()'  method='post' enctype='multipart/form-data' action='/sisgen/test-index.php?q=$regpage'>
<input type='text' name='phase' value='subscribe' hidden />
<table id='regform'>
<tr>
<td style='text-align:right; width:30%'>Email:</td><td style='text-align:left'><input type='email' name='emailA' id='emailA' size='30' required $emailcheck /><span id='emailAdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr>
<tr>
<td style='text-align:right; width:30%'>Email (Confirmação):</td><td style='text-align:left'><input type='email' name='emailB' id='emailB' size='30' required $emailcheck /><span id='emailBdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr>
<td style='text-align:right; width:30%'>Senha:</td><td style='text-align:left'><input type='password' name='passA' id='passA' size='30' pattern='(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$' title='Senha:Maiúscula+Minúsculas+Número/Simbolo, mínimo 8' required $passcheck /><span id='passAdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr>
<tr>
<td style='text-align:right; width:30%'>Senha (Confirmação):</td><td style='text-align:left'><input type='password' name='passB' id='passB' size='30' pattern='(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$' title='Senha:Maiúscula+Minúsculas+Número/Simbolo, mínimo 8' required $passcheck /><span id='passBdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr><td></td>
<td>
<input type='submit' value='Cadastrar'/>
</td></tr></table>
</form>
Você estará, na seqüência, recebendo um Email de confirmação.
</div>
<div id='login'>

<form method='post' enctype='multipart/form-data' action='/sisgen/test-index.php?q=$regpage'>
<input type='text' name='phase' value='login' hidden />
<table id='regform'>
<tr>
<td style='text-align:right; width:30%'>Email:</td><td style='text-align:left'><input type='email' name='emailA' size='30' required /></td>
</tr>
<tr>
<tr>
<td style='text-align:right; width:30%'>Senha:</td><td style='text-align:left'><input type='password' name='passA' size='30' required /></td>
</tr>
<tr><td></td>
<td>
<input type='submit' value='Login'/>
</td></tr></table>
</form>


</div>

<div id='forgot' hidden>

<form method='post' enctype='multipart/form-data' action='/sisgen/test-index.php?q=$regpage'>
<input type='text' name='phase' value='forgot' hidden />
<table id='regform'>
<tr>
<td style='text-align:right; width:30%'>Email:</td><td style='text-align:left'><input type='email' name='emailA' size='30' required /></td>
</tr>
<tr>
<td></td>
<td>
<input type='submit' value='Recuperar Senha'/>
</td></tr></table>
Você receberá um Email com sua senha, caso já esteja cadastrado e sua conta confirmada.
</form>
</div>

<div id='re-send' hidden>

<form method='post' enctype='multipart/form-data' action='/sisgen/test-index.php?q=$regpage'>
<input type='text' name='phase' value='forgot' hidden />
<table id='regform'>
<tr>
<td style='text-align:right; width:30%'>Email:</td><td style='text-align:left'><input type='email' name='emailA' size='30' required /></td>
</tr>
<tr>
<td></td>
<td>
<input type='submit' value='Re-enviar Email de confirmação'/>
</td></tr></table>
Você receberá um Email de confirmação, caso sua conta esteja cadastrada mas não confirmada.
</form>
</div>


</div>
</div>

<h2>ATENÇÃO</h2><hr>
$closedmsg<br>
Novas inscrições  serão aceitas a partir das 12h30 do dia 01/10 no local do evento

	<h2>Roteiro para inscrição</h2><hr>
	Segue um pequeno roteiro, passo-a-passo, do uso do sistema de inscrição:
	<a href='/sisgen/downloads/Sistema de Inscrição.pdf'  target='_blank'>Roteiro de Inscrição</a>


<script type='text/javascript'>

function check(fieldA,fieldB,diffA,diffB) {
var valA = document.getElementById(fieldA).value;
var valB = document.getElementById(fieldB).value;
if (valA == valB) {
   document.getElementById(diffA).style.color='white';
   document.getElementById(diffB).style.color='white';
   return true;
} else {
   document.getElementById(diffA).style.color='red';
   document.getElementById(diffB).style.color='red';
   return false;
}

}

function formtype(type) {
  if (type == 'login') {
    document.getElementById('login').style.display='block';
    document.getElementById('new-account').style.display='none';
    document.getElementById('forgot').style.display='none';
    document.getElementById('re-send').style.display='none';
  } else {
    if (type == 'new-account') {
       document.getElementById('login').style.display='none';
       document.getElementById('new-account').style.display='block';
       document.getElementById('forgot').style.display='none';
       document.getElementById('re-send').style.display='none';
    } else {
       if (type == 'forgot') {
          document.getElementById('login').style.display='none';
          document.getElementById('new-account').style.display='none';
          document.getElementById('forgot').style.display='block';
          document.getElementById('re-send').style.display='none';
       } else {
          document.getElementById('login').style.display='none';
          document.getElementById('new-account').style.display='none';
          document.getElementById('forgot').style.display='none';
          document.getElementById('re-send').style.display='block';
       }
    }
  }
}

function emailcheck() {
  return check('emailA','emailB','emailAdiff','emailBdiff');
}

function passcheck() {
  return check('passA','passB','passAdiff','passBdiff');
}

function validate() {
    if (passcheck() && emailcheck()) {
       return true;
    }
    else {
        alert('Por favor, revise e corrija os dados no formulário!');
        return false;
    }

}

</script>
";
    echo $paglogin;
}

?>





