<?php include "FLAGS.php" ?>

<?php
 if ($tooearly) {
    $regForm=false;
 }
?>

  <script type="text/javascript">
  var RegForm = false;
  </script>


<?php if ($regForm): ?>
  <script type="text/javascript">
  RegForm = true;
  </script>

  <!-- see https://igorescobar.github.io/jQuery-Mask-Plugin/ -->
  <script type="text/javascript">
  var SPMaskBehavior = function (val) {
    return val.replace(/\D/g, '').length === 11 ? '(00) 0.0000-0000' : '(00) 0000-0000';
    },
  spOptions = {
    onKeyPress: function(val, e, field, options) {
       field.mask(SPMaskBehavior.apply({}, arguments), options);
    }
  };

  $('.phonemask').mask(SPMaskBehavior, spOptions);
  
  </script>


  <form onsubmit="return validate()"  method="post" enctype="multipart/form-data" action="/sbai17/?q=register">

  <!-- MAX_FILE_SIZE must precede the file input field -->
  <input type="hidden" name="MAX_FILE_SIZE" value="5000000" />
<?php endif; ?>

<table id="regform" style="width:70%">
<tr>
<td style="text-align:right; width:30%">Tipo de Inscrição:</td><td style="text-align:left">

<!---- php if block ---->
<?php if ($early): ?>
     <input type="radio" name="type" id="type" value="early" checked onclick="settype('early')"><font color="red">Antecipada (até 15/08)</font></input><br>
     <input type="radio" name="type" id="type" value="late" onclick="settype('late')">Tardia (após 15/08)</input>
<?php else: ?>
     <input type="radio" name="type" id="type" value="late" checked onclick="settype('late')">Tardia (após 15/08)</input>
<?php endif; ?>
<!---- php if block ---->

</td></tr>
<tr>
 <td style="text-align:right">Associação: </td><td style="text-align:left">
    
  <input type="radio" name="sba" id="sba" value="sba" onclick="setsba('sba')">Sócio da SBA</input><br>
  <input type="radio" name="sba" id="sba" value="non" checked onclick="setsba('non')">Não Sócio da SBA</input>
  </td></tr>
</table>

<!---- php if block ---->
<?php if ($regForm): ?>
<div id="sbadiv" hidden>
<table id="regform" style="width:70%">
<tr>
<td style="text-align:right; width:30%">Comprovante Sociedade:</td><td style="text-align:left"><input type="file" name="sbafile" id="sbafile"/></td>
</tr>
</table>
</div>
<?php endif; ?>
<!---- php if block ---->

<table id="regform" style="width:70%">
<tr>
<td style="text-align:right; width:30%">Modalidade: </td><td style="text-align:left">
     <input type="radio" name="mod" id="mod" value="professional" checked onclick="setmod('professional')">Profissional</input><br>
     <input type="radio" name="mod" id="mod" value="student" onclick="setmod('student')">Estudante</input>
     </td></tr>
</table>

<!---- php if block ---->
<?php if ($regForm): ?>
<div id="studentdiv" hidden>
<table id="regform" style="width:70%">
<tr>
<td style="text-align:right; width:30%">Comprovante Matrícula:</td><td style="te xt-align:left"><input type="file" name="studentfile" id="studentfile"/></td>
</tr>
</table>
</div>
<?php endif; ?>
<!---- php if block ---->

<div id="papersdiv" hidden>
<table id="regform" style="width:70%">
<tr>
<td style="text-align:right; width:30%">Número de Artigos:</td><td style="text-align:left">
<table id='regiform'>
<tr>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" checked value="0" onclick="numpapers(0)">Nenhum</input><br></td>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="1" onclick="numpapers(1)">Um Artigo</input><br></td>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="2" onclick="numpapers(2)">Dois Artigos</input><br></td>
</tr>
<tr>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="3" onclick="numpapers(3)">Tres Artigos + R$250</input><br></td>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="4" onclick="numpapers(4)">Quatro Artigos + R$500</input><br></td>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="5" onclick="numpapers(5)">Cinco Artigos + R$750</input><br></td>
</tr>
<tr>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="6" onclick="numpapers(6)">Seis Artigos + R$1.000</input><br></td>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="7" onclick="numpapers(7)">Sete Artigos + R$1.250</input><br></td>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="8" onclick="numpapers(8)">Oito Artigos + R$1.500</input><br></td>
</tr>
<tr>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="9" onclick="numpapers(9)">Nove Artigos + R$1.750</input><br></td>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="10" onclick="numpapers(10)">Dez Artigos + R$2.000</input><br></td>
    <td style="text-align:left"> <input type="radio" name="npapers" id="npapers" value="11" onclick="numpapers(11)">Onze Artigos + R$2.250</input><br></td>
</tr>
</table>     
     </td>
</tr>
</table>
</div>

<?php
for ($num=1; $num <= $MAXPAPERS; $num++) {
  $color = $num % 2;
  if ($color == 0) {
     $color='green';
  } else {
    $color='blue';
  }
  echo "<div id='paper${num}div' hidden>\n";
  echo " <table id='regform' style='width:70%'>\n";
  if ($regForm) {
     echo "<tr>";
     echo "<td style='text-align:right'><font color='$color'>Identificador do Artigo #${num}</font></td>";
     echo "<td style='text-align:left'><input type='number' size='6' name='paperID${num}' id='paperID${num}' onblur='papercheck($num)' onchange='papercheck($num)' oninput='papercheck($num)'/>";
     echo "<span id='paper${num}color' style='color:white'>Ident. Inválido</span><span id='paper${num}dup' style='color:white'>(dup)</span>";
     echo "</td>";
     echo "</tr>";
  }
  echo "<tr>";
  echo "<td style='text-align:right; width:30%'><font color='$color'>Páginas Extras do Artigo #${num}:</font></td><td style='text-align:left'>";
  echo "<input type='radio' name='xtrapag${num}' id='xtrapag${num}' value='0' checked onclick='xtrapag($num,0)'>Nenhuma</input><br>";
  echo "<input type='radio' name='xtrapag${num}' id='xtrapag${num}' value='0'         onclick='xtrapag($num,1)'>Uma Página + R$50</input><br>";
  echo "<input type='radio' name='xtrapag${num}' id='xtrapag${num}' value='0'         onclick='xtrapag($num,2)'>Duas Páginas + R$100</input><br>";
  echo "</td>";
  echo "</tr>";
  if ($regForm) {
     echo "<tr>";
     echo "<td style='text-align:right; width:30%'><font color='$color'>Apresentação do Artigo #${num}:</font></td><td style='text-align:left'>";
     echo "<input type='radio' name='paper${num}self' id='paper${num}self' value='self' checked onclick=\"setpaperself($num,'self')\">Eu mesmo vou apresentar</input><br>";
     echo "<input type='radio' name='paper${num}self' id='paper${num}self' value='other' onclick=\"setpaperself($num,'other')\">Outra pessoa vai apresentar</input><br>";
     echo "</tr>";
  }
  echo "</table>";

  if ($regForm){
     echo "<div id='speaker${num}div' hidden>";
     echo "<table id='regform' style='width:70%'>";
     echo "<tr>";
     echo "<td style='text-align:right; width:30%'><font color='$color'>Apresentador do Artigo #${num}:</font></td>";
     echo "<td style='text-align:left'><input type='text' name='speaker${num}' id='speaker${num}' size='30' pattern='[a-zA-ZçÇáéíóúäëïöü\. ]+' title='somente letras! e .'/></td>";
     echo "</tr>";
     echo "</table>";
     echo "</div>";
  }
  echo "</div>";


}
?>

<br>
<table id="regform" style="width:70%">
<tr>
<th style="text-align:right; width:30%">
Valor da Incrição:</th><th style="text-align:left"> R$<input type="text" name="TOTAL" id="TOTAL" size="6" readonly style="color: #B80000;" value=""/></th>
</tr>
</table>
<br>
<br>
<?php if ($tooearly): ?>
<h3>O site de Incrição ainda não está aberto!</h3>
Por favor, volte mais tarde.
<?php endif; ?>

<?php if ($regForm): ?>
<table id="regform" style="width:70%">
<tr>
<td style="text-align:right; width:30%">Forma de Tratamento:</td><td style="text-align:left">
                    <select name="title" id="title" onblur="titlecheck()"  onselect="titlecheck()" onchange="titlecheck()">
                      <option value="Prof.">Prof.</option>
                      <option value="Profa.">Profa.</option>
                      <option value="Dr.">Dr.</option>
                      <option value="Dra.">Dra.</option>
                      <option value="Sr.">Sr.</option>
                      <option value="Sra.">Sra.</option>
                      <option value="Srta.">Srta.</option>
                    <option value="selectone" selected>Selecione Um</option> 
                    </select>
		    <span id="selectcolor" style="color:white">Selecione Um !</span>
</td>
</tr>
<tr>
<td style="text-align:right; width:30%">Nome Completo: (recibo/comprovantes)</td><td style="text-align:left"><input type="text" name="fullname" id="fullname" size="30" required pattern="[a-zA-ZçÇáéíóúäëïöü\. ]+" title="somente letras! e ."/></td>
</tr>
<tr>
<td style="text-align:right; width:30%">Nome: (Crachá)</td><td style="text-align:left"><input type="text" name="name" id="name" size="20" required pattern="[a-zA-ZçÇáéíóúäëïöü]{1,10}" title="somente letras!"/></td>
</tr>
<tr>
<td style="text-align:right; width:30%">Sobrenome: (Crachá)</td><td style="text-align:left"><input type="text" name="familyname" id="familyname" size="30" required pattern="[a-zA-ZçÇáéíóúäëïöü\. ]+" title="somente letras! e ."/></td>
</tr>
<tr>
<td style="text-align:right; width:30%">Afiliação: (Crachá)</td><td style="text-align:left"><input type="text"name="afiliation" id="afiliation" size="30" required pattern="[a-zA-ZçÇáéíóúäëïöü\. \-/]+" title="somente letras! e '.-/'"/></td>
</tr>
<tr>
<td style="text-align:right; width:30%">Telefone de Contato:</td><td style="text-align:left"><input type="text" name="phone" id="phone" size="20" required class="phonemask" pattern="\([0-9]{2}\) ([0-9].)?[0-9]{4}-[0-9]{4}" title="(##) ####-#### ou (##) #####-####"/></td>
</tr>
<tr>
<td style="text-align:right; width:30%">Email:</td><td style="text-align:left"><input type="email" name="email" id="email" size="30" required onblur="emailcheck()" /><span id="emailAdiff" style="color:white">(diff.)</span></td>
</tr>
<tr>
<td style="text-align:right; width:30%">Email (Confirmação):</td><td style="text-align:left"><input type="email" name="emailB" id="emailB" size="30" required onblur="emailcheck()"/><span id="emailBdiff" style="color:white">(diff.)</span></td>
</tr>

<tr>
<td style="text-align:right; width:30%">Pais:</td><td style="text-align:left">
<select name="country" id="country">
<?php include "countrylist.php" ?>
</select>
</td>
</tr>

<tr>
<td style="text-align:right; width:30%">Recibo de Depósito:</td><td style="text-align:left"><input type="file" name="receiptfile" id="receiptfile" required/></td>
</tr>

</table>
<?php endif; ?>

<?php if ($regForm): ?>
<table id="regform" style="width:50%">
<tr><td>
<input type="submit" value="Inscrever"/>
</td></tr></table>
</form>
<?php endif; ?>



<script type="text/javascript">
function settype(val) {
document.getElementById('type').value=val;
paperfields();
}

function setsba(val) {
document.getElementById('sba').value=val;
paperfields();
}

function setmod(val) {
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

function titlecheck() {
var title = document.getElementById('title').value;
if (title == 'selectone') {
  document.getElementById('selectcolor').style.color='red';
  return false;
} else {
  document.getElementById('selectcolor').style.color='white';
  return true;
}
}

function emailcheck() {
var email = document.getElementById('email').value;
var emailB = document.getElementById('emailB').value;
if (email == emailB) {
   document.getElementById('emailAdiff').style.color="white";
   document.getElementById('emailBdiff').style.color="white";
   return true;
} else {
   document.getElementById('emailAdiff').style.color="red";
   document.getElementById('emailBdiff').style.color="red";
   return false;
}

}

function paperIDcheck(paper) {
<?php include "paperslist.php" ?>
var found = false;
var i;

for (i=0; i< papers.length; i++) {
    if (papers[i] == paper) {
    found = true;
    }
}
return found;
}

function papercheck(pap) {
var field ='paperID'+pap;
var paper = document.getElementById(field).value;
var OK = false;

var found=paperIDcheck(paper);
var colorfield = 'paper'+pap+'color';

if (found == true) {
   OK = true;
   document.getElementById(colorfield).style.color="white";
} else {
   document.getElementById(colorfield).style.color="red";
}
var npap = document.getElementById('npapers').value;
var i;
var dupA = 'paper'+pap+'dup';
var dupcolor='white';

for (i=1;i<=npap;i++) {
    field = 'paperID'+i;
    var paperB = document.getElementById(field).value;
    var dupB = 'paper'+i+'dup';
    if (i != pap) {
       if (paper == paperB) {
       	  OK=false;
	  dupcolor='red';
          document.getElementById(dupB).style.color=dupcolor;
       } else {
          document.getElementById(dupB).style.color='white';
       };
    }
}
document.getElementById(dupA).style.color=dupcolor;

return OK;
}


function validate() {
var valid = true;

    var type = document.getElementById('type').value;
    var sba = document.getElementById('sba').value;
    var mod = document.getElementById('mod').value;
    var npap = document.getElementById('npapers').value;

    var i;
    for (i=1;i<=npap;i++) {
    	var selffield = 'paper'+i+'self';
    	var speakerfield = 'speaker'+i;
	if (document.getElementById(selffield).value == 'self') {
	   document.getElementById(speakerfield).value = document.getElementById('fullname').value;
	}
    }

    if ((type == 'late') || (mod == 'student')) {
       document.getElementById('npapers').value=0;
       for (i=1;i<=MAXPAPERS;i++) {
       	   var xfield='xtrapag'+i;
       	   var IDfield='paperID'+i;
       	   var speakerfield='speaker'+i;
       	   document.getElementById(xfield).value=0;
       	   document.getElementsByName(xfield)[0].checked = true;
       	   document.getElementById(IDfield).value=0;
       	   document.getElementById(speakerfield).value='none';
       }
    } else {
      for (i=1;i<=MAXPAPERS;i++) {
      	  if (i > npap) {
       	     var xfield='xtrapag'+i;
       	     var IDfield='paperID'+i;
       	     var speakerfield='speaker'+i;
       	     document.getElementById(xfield).value=0;
       	     document.getElementsByName(xfield)[0].checked = true;
       	     document.getElementById(IDfield).value=0;
       	     document.getElementById(speakerfield).value='none';
	  }
      }
    }
    
    npap = document.getElementById('npapers').value;
    var xpags = [];
    var paperIDs = [];
    var paperspeakers = [];
    var i;
       for (i=1;i<=npap;i++) {
           var field='xtrapag'+i;
    	   xpags.push(document.getElementById(field).value);
           field='paperID'+i;
    	   paperIDs.push(document.getElementById(field).value);
           field='speaker'+i;
    	   paperspeakers.push(document.getElementById(field).value);
       }

    var title =  document.getElementById('title').value;
    var name =  document.getElementById('name').value;
    var familyname =  document.getElementById('familyname').value;
    var fullname =  document.getElementById('fullname').value;
    var title2;
    if ((title == 'Profa.') || (title == 'Dra.') || (title == 'Sra.') || (title == 'Srta.')) {
       title2 = 'Prezada';
    } else {
       title2 = 'Prezado';
    }
    var papersOK = true;
    if ((type == "early") && (mod == "professional")) {
      for (i=1;i<=npap;i++) {
      	  papersOK = papersOK && papercheck(i);
      }
    } else {
      papersOK = true;
    }
    

    var txt = title2 + ' ' + title + "\n";
    txt += fullname + '(' + name + ' ' + familyname + ")\n";
    
    if (type == "early") {
      txt += " Inscrição: Antecipada (até 15/08)\n";
    } else {
      txt += " Inscrição: Tardia (após 15/08)\n";
    }
    if (sba == "sba") {
      txt += " Associado à SBA\n";
    } else {
      txt += " Não Associado à SBA\n";
    }
    if (mod == "student") {
      txt += " Modalidade: Estudante\n";
    } else {
      txt += " Modalidade: Profissional\n";
    }
    if (npap == 0) {
      txt += "  Não associado à qualquer paper\n"; 
    } else {
      for (i=0;i<npap;i++) {
      	pages = 6;
	pages += 1*xpags[i];
	var ii = 1;
	ii += 1*i;
        txt += "  "+ii+"o. Artigo:        " + paperIDs[i] + " (" + pages + " pág.)";
	txt += "  apresentado por: " + paperspeakers[i] + "\n";
      }
    }
    txt += "\n";
    var val = regvalue(type,sba,mod,npap,xpags);

    txt += "Valor da Inscrição: R$" + val + ",00\n\n";
    
    var afiliation =  document.getElementById('afiliation').value;
    var phone =  document.getElementById('phone').value;
    var email =  document.getElementById('email').value;
    var country =  document.getElementById('country').value;
    txt += "Afiliação: " + afiliation;
    txt += "\nTelefone Contato: " + phone;
    txt += "\nEmail Contato: " + email;
    txt += "\nPaís:" + country + "\n";
    txt += "                             Você confirma os dados acima?"

    var receiptfile = document.getElementById('receiptfile').value;
    receiptfile = receiptfile.split(/(\\|\/)/g).pop();
    var txt2 = "Você confirma que o arquivo (PDF)\n\n";
    txt2 += receiptfile + "\n\n";
    txt2 += "contem o comprovante do depósito de R$" + val + ",00\n";
    txt2 += "na conta bancária\n";
    txt2 += "   Banco do Brasil\n";
    txt2 += "   Agencia: 3798-2\n";
    txt2 += "   Conta:   302.001-0\n";
    txt2 += "   CNPJ:    92.971.845/0001-42\n\n";

    if (mod == 'student') {
       var studentfile = document.getElementById('studentfile').value;
       studentfile = studentfile.split(/(\\|\/)/g).pop();
       txt2 += "E que o arquivo (PDF) abaixo contem seu comprovante de matrícula\n\n";
       txt2 += studentfile + "\n\n";
    };

    if (sba == 'sba') {
       var sbafile = document.getElementById('sbafile').value;
       sbafile = sbafile.split(/(\\|\/)/g).pop();
       txt2 += "E que o arquivo (PDF) abaixo contem seu comprovante de Associação\n\n";
       txt2 += sbafile + "\n\n";
    };

    if(papersOK && titlecheck() && emailcheck()) {
        if (confirm(txt)) {
	  return confirm(txt2);
	} else {
	  return false;
	}
    }
    else {
        alert('Por favor, revise e corrija os dados no formulário!');
        return false;
    }
}

function regvalue(type,sba,mod,npapers,xpags) {
var val = 0;

    if (type == "late") {
       if (sba == "sba") {
       	 if (mod == "student") {
	    val = 400;
	 } else {
	   val = 750;
	 }
       } else {
       	 if (mod == "student") {
	    val = 450;
	 } else {
	   val = 900;
	 }
       }
    } else {
       if (sba == "sba") {
       	 if (mod == "student") {
	    val = 350;
	 } else {
	   val = 650;
	 }
       } else {
       	 if (mod == "student") {
	    val = 400;
	 } else {
	   val = 800;
	 }
       }
    }

    if((mod == "professional") && (type == "early")) {
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
    initfield('paperID1');
    initfield('xtrapag1');
    initfield('paperID2');
    initfield('xtrapag2');
    initfield('paper1self');
    initfield('paper2self');
    paperfields();
}

function paperfields(){
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

    var val = 0;

    if (RegForm) {
        if (mod == 'student') {
    	   document.getElementById('studentdiv').style.display='block';
	   document.getElementById('studentfile').required=true;
	} else {
           document.getElementById('studentdiv').style.display='none';
           document.getElementById('studentfile').required=false;
        }

        if (sba == 'sba') {
    	   document.getElementById('sbadiv').style.display='block';
	   document.getElementById('sbafile').required=true;
	} else {
           document.getElementById('sbadiv').style.display='none';
           document.getElementById('sbafile').required=false;
        }
	
    }

    if ((mod == "student") || (type == "late") ) 
    {
     document.getElementById('papersdiv').style.display="none";
     for (i=1;i<=MAXPAPERS;i++) {
     	 field = 'paper'+i+'div';
     	 document.getElementById(field).style.display="none";
	 if (RegForm) {
	    field = 'speaker'+i;
	    document.getElementById(field).required=false;
	    field = 'speaker'+i+'div';
	    document.getElementById(field).style.display='none';
	 }
     }
    } else {
      document.getElementById('papersdiv').style.display="block";
      for (i=1;i<=MAXPAPERS;i++) {
      	  field = 'paper'+i+'div';
      	  if (i<=pap) {
             document.getElementById(field).style.display='block';
	  } else {
             document.getElementById(field).style.display='none';
	  }
      }
    }

     if (RegForm) {
     	for (i=1;i<=MAXPAPERS;i++) {
	    speakerfield='speaker'+i;
	    speakerdiv='speaker'+i+'div';
	    selffield='paper'+i+'self';
	    if (i<=pap) {
	       if (document.getElementById(selffield).value == 'self') {
   	       	 document.getElementById(speakerfield).required=false;
		 document.getElementById(speakerdiv).style.display='none';
	       } else {
   	       	 document.getElementById(speakerfield).required=true;
		 document.getElementById(speakerdiv).style.display='block';
	       }
	    } else {
   	       document.getElementById(speakerfield).required=false;
	    }
	}
     }

   val = regvalue(type,sba,mod,pap,xpags);
   document.getElementById('TOTAL').value = val;

}




    </script>

<?php if ($regForm): ?>
<script>
    window.onload = start; //notice no parenthesis
</script>
<?php else: ?>
<script>
    window.onload = paperfields; //notice no parenthesis
</script>
<?php endif; ?>