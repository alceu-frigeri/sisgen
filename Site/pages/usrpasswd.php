<?php
    include 'bailout.php';

    $passcheck = "onkeyup='passcheck()' onchange='passcheck()' onblur='passcheck()'";
?>


    <h3>Trocar Senha</h3>
    <hr>
<form onsubmit='return validate()'  method='post' enctype='multipart/form-data' action='<?php echo $thisform; ?>'>
<table id='regform'>
<tr>
<td style='text-align:right; width:30%'>
    Senha Atual:</td><td style='text-align:left'><input type='password' name='passORG' id='passORG' size='30' required /></td>
</tr>
<tr>
<tr>
<td style='text-align:right; width:30%'>
    Nova Senha:</td><td style='text-align:left'>
  <input type='password' name='passA' id='passA' size='30' pattern='<?php echo $GBLpasswdpattern ?>' title='Senha:Maiúscula+Minúsculas+Número/Simbolo, mínimo 8' required <?php echo $passcheck; ?> />
  <span id='passAdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr>
<tr>
<td style='text-align:right; width:30%'>
    Nova Senha (Confirmação):</td><td style='text-align:left'>
  <input type='password' name='passB' id='passB' size='30' pattern='<?php echo $GBLpasswdpattern ?>' title='Senha:Maiúscula+Minúsculas+Número/Simbolo, mínimo 8' required <?php echo $passcheck; ?> />
  <span id='passBdiff' style='color:white'>(diff.)</span></td>
</tr>
<tr><td></td>
<td>
<input type='submit' name='act' value='Atualizar Senha'/>
</td></tr></table>
</form>


<script type='text/javascript'>

function check(fieldA , fieldB , diffA , diffB) {
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


function passcheck() {
  return check('passA' , 'passB' , 'passAdiff' , 'passBdiff');
}

function validate() {
    if (passcheck()) {
       return true;
    }
    else {
        alert('Por favor, revise e corrija os dados no formulário!');
        return false;
    }

}

</script>




