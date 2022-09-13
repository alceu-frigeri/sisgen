
<h2>Sistema de Inscrição</h2>

<h4>Se o seu Email já estiver cadastrado, efetue o login. Caso contrário, preencha o formulário de cadastro.</h4>

<div class='row'>
<div class='col-sm-2'>
</div>
<div class='col-sm-8'>

     <input type='radio' name='type' id='type' value='login' checked onclick='formtype('login')'>Já estou cadastrado</input><br>
     <input type='radio' name='type' id='type' value='new-account' onclick='formtype('new-account')'>Meu Email não está cadastrado</input>


 <div id='new-account' hidden>

<form onsubmit='return validate()'  method='post' enctype='multipart/form-data' action='/sbai17/?q=test2'>
<input type='text' name='phase' value='subscribe' hidden />
<table id='regform'>
<tr>
<td style='text-align:right; width:30%'>Email:</td><td style='text-align:left'><input type='email' name='emailA' id='emailA' size='30' required onblur='emailcheck()' /><span id='emailAdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr>
<tr>
<td style='text-align:right; width:30%'>Email (Confirmação):</td><td style='text-align:left'><input type='email' name='emailB' id='emailB' size='30' required onblur='emailcheck()'/><span id='emailBdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr>
<td style='text-align:right; width:30%'>Senha:</td><td style='text-align:left'><input type='password' name='passA' id='passA' size='30' pattern='(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$' title='Senha:Maiúscula+Minúsculas+Número/Simbolo, mínimo 8' required onblur='passcheck()' /><span id='passAdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr>
<tr>
<td style='text-align:right; width:30%'>Senha (Confirmação):</td><td style='text-align:left'><input type='password' name='passB' id='passB' size='30' pattern='(?=^.{8,}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$' title='Senha:Maiúscula+Minúsculas+Número/Simbolo, mínimo 8' required onblur='passcheck()'/><span id='passBdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr><td>
<input type='submit' value='Cadastrar'/>
</td></tr></table>
</form>
Você estará, na seqüência, recebendo um Email de confirmação.
</div>
<div id='login'>

<form method='post' enctype='multipart/form-data' action='/sbai17/?q=test2'>
<input type='text' name='phase' value='login' hidden />
<table id='regform'>
<tr>
<td style='text-align:right; width:30%'>Email:</td><td style='text-align:left'><input type='email' name='emailA' size='30' required /></td>
</tr>
<tr>
<tr>
<td style='text-align:right; width:30%'>Senha:</td><td style='text-align:left'><input type='password' name='passA' size='30' required /></td>
</tr>
<tr>
<tr><td>
<input type='submit' value='Login'/>
</td></tr></table>
</form>


</div>
</div>
</div>

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
  } else {
    document.getElementById('login').style.display='none';
    document.getElementById('new-account').style.display='block';
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
