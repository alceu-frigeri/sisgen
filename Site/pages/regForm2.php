<?php include "FLAGS.php" ?>

<?php

 date_default_timezone_set('America/Sao_Paulo');
 $timestamp=time();
 $timestamp=date('Y-m-d H:i:s',$timestamp);

 list($microstamp,$sec) = explode(' ',microtime(false));
 list($nothing,$microstamp) = explode('.',$microstamp);

function hiddenfield($field) {
   echo "<input type='hidden' name='$field' id='$field' value=\"" .$_POST[$field]. "\" />\n";
}

function tablineB($txt,$txtB,$color,$colorB) {
     echo "<tr><td style =\"text-align:right; width:30%; background-color:$color\">${txt}</td><td style=\"text-align:left\"><font color='$colorB'><b>${txtB}</b></font></td></tr>\n";
}

function tabline($txt,$field,$color) {
     echo "<tr><td style =\"text-align:right; width:30%; background-color:$color\">${txt}</td><td style=\"text-align:left\"><b>" .$_POST[$field]. "</b></td></tr>\n";
}

function tabfile($txt,$field) {
   echo "<tr><td style='text-align:right; width:30%'>${txt}</td><td style='text-align:left'><input type='file' name='${field}' id='${field}' required/></td></tr>\n";
}

function sizemsg($errcode) {
 if (($errcode == UPLOAD_ERR_FORM_SIZE) || ($errcode == UPLOAD_ERR_INI_SIZE)) {
    echo " Arquivo muito grande! Deve ser menor que ".${MAXSIZE}."<br>\n";
 } else {
    if ($errcode == UPLOAD_ERR_NO_FILE) {
    echo "NO FILE !<br>\n";
    } else {
    echo "<br>\n";
    }
 };
}


function fileupload($field,$tag,$timestamp,$microstamp) {
   $uploadOK = false;
   $errcode = $_FILES[$field]['error'];

   if ($errcode == UPLOAD_ERR_OK) {
      $tmp_name = $_FILES[$field]["tmp_name"];
      $file_name = $_FILES[$field]["name"];
	    
      $file =   $timestamp.'.'.$microstamp.' '.$tag.': '.$file_name;
      if(move_uploaded_file($tmp_name, '/export/var/www/sbai17/uploads/'.$file)) {
         $uploadOK = true;
      }
   }
   return array($uploadOK,$file,$file_name,$errcode);
}


   $type = $_POST['type'];
   $sba =  $_POST['sba'];
   $mod =  $_POST['mod'];
   $npapers =  $_POST['npapers'];
   $value =  $_POST['TOTAL'];
   $name =  $_POST['name'];
   $familyname =  $_POST['familyname'];
   $fullname =  $_POST['fullname'];
   $afiliation =  $_POST['afiliation'];
   $phone =  $_POST['phone'];
   $email =  $_POST['email'];
   $country =  $_POST['country'];

   $title =  $_POST['title'];
   if (($title == 'Profa.') || ($title == 'Dra.') || ($title == 'Sra.') || ($title == 'Srta.')) {
      $title2 = 'Prezada';
    } else {
       $title2 = 'Prezado';
    }



if ($singlephase || ($_POST['phase'] == 'two')) {

   list ($receiptuploadOK,$receiptfile,$receiptorg,$receiptERR) = fileupload('receiptfile','receipt',$timestamp,$microstamp);


   
   if ($mod == 'student') {
      list ($studentuploadOK,$studentfile,$studentorg,$studentERR) = fileupload('studentfile','student',$timestamp,$microstamp);
   } else {
      $studentuploadOK = true;
      $studentfile='none';
   }
      
   if ($sba == 'sba') {
      list ($sbauploadOK,$sbafile,$sbaorg,$sbaERR) = fileupload('sbafile','sba',$timestamp,$microstamp);
   } else {
     $sbauploadOK = true;
     $sbafile='none';
   }


   $register = ',';
   for ($i=1;$i<=$MAXPAPERS;$i++) {
       $register .= ',';
   }
   $register .= $type.',';
   $register .= $sba.',';
   $register .= $mod.',';
   $register .= $npapers.',';
   $register .= $value.',';
   $register .= $title.',';
   $register .= $name.',';
   $register .= $familyname.',';
   $register .= $fullname.',';
   $register .= $afiliation.',';
   $register .= $phone.',';
   $register .= $email.',';
   $register .= $country.',';
   
   for ($i=1;$i<=$MAXPAPERS;$i++) {
       $field='paperID'.$i;
       $register .= $_POST[$field].',';
       $field='xtrapag'.$i;
       $register .= $_POST[$field].',';
       $field='speaker'.$i;
       $register .= $_POST[$field].',';
   }

   $register .= $timestamp.',';
   $register .= $receiptfile.',';
   $register .= $studentfile.',';
   $register .= $sbafile.',';
   $register .= "\n";
   
   /*
   $receiptuploadOK = false;
   $studentuploadOK = false;
   $sbauploadOK = false;
   */
   

    if ($receiptuploadOK && $studentuploadOK && $sbauploadOK) {
       $filename = '/export/var/www/sbai17/logs/registration.csv';
       $log = fopen($filename, "a");
       fwrite($log, $register);
       fclose($log);

        echo "<h2>".$title2." ".$title."<br>";
    	echo $name." ".$familyname."</h2>";
	echo "<h3>Obrigado pela sua Inscrição !</h3><p>";
	echo "Em breve você estará recebendo um Email de confirmação de nossa secretaria.";
    } else {
        echo "<h2>".$title2." ".$title."<br>";
    	echo $name." ".$familyname."</h2>";
	echo "<h3><font color=\"red\">Infelizmente ocorreu um erro na transmissão de<br>";
	echo "<ul>";
	if (!$receiptuploadOK) {
	   echo "<li>seu recibo: <b>".$receiptorg. "</b>";
	   sizemsg($receiptERR);
	}
	if (!$studentuploadOK) {
	   echo "<li>seu comprovante de vínculo: <b>".$studentorg."</b>";
	   sizemsg($studentERR);
	}
	if (!$sbauploadOK) {
	   echo "<li>seu comprovante de Associação: <b>".$sbaorg."</b>";
	   sizemsg($sbaERR);
	}
	echo "</ul></font></h3><p>";
	echo "Por favor, retorne a página anterior, revise os dados/arquivos, e tente novamente.";
    }
} else {
	echo "<div class='row'><div class='col-sm-14'>\n";

  echo "  <form method='post' onsubmit='return validate()'  enctype='multipart/form-data' action='/sbai17/?q=register'>\n";
  echo "    <input type='hidden' name='MAX_FILE_SIZE' value='${MAXSIZE}' />\n";
  echo "    <input type='hidden' name='phase' value='two' />\n";

        echo "<h2>".$title2." ".$title."<br>";
    	echo $name." ".$familyname."</h2>";
	echo "<h3>Por favor, confirme os dados abaixo !</h3><br>\n";
	echo "<table id='regform' style='width=70%'>\n";
	if ($_POST['type']=='early') {
	   	tablineB('Tipo:','Antecipada (até 15/08)','white','red');
	} else {
	   	tablineB('Tipo:','Tardia (após 15/08)','white','black');
	}
	if ($_POST['sba']=='sba') {
	   	tablineB('Associação','Associado SBA','white','black');
	} else {
	   	tablineB('Associação','Não Associado SBA','white','black');
	}
	if ($_POST['sba'] == 'sba') {
	   tabfile('Comprovante Sociedade:','sbafile');
	}
	if ($_POST['mod']=='student') {
	   	tablineB('Modalidade','Estudante','white','black');
	} else {
	   	tablineB('Modalidade','Profissional','white','black');
	}
	if ($_POST['mod'] == 'student') {
	   tabfile('Comprovante Vínculo:','studentfile');
	}
	tabline('Tratamento:','title','white');
	tabline('Nome Completo (recibos):','fullname','white');
	tabline('Nome (Crachá):','name','white');
	tabline('Sobrenome (Crachá):','familyname','white');
	tabline('Afiliação:','afiliation','white');
	tabline('Telefone:','phone','white');
	tabline('Email:','email','white');
	tabline('Pais:','country','white');
	echo "<tr><td></td><td></td></tr>\n";
	tabline('Número de Artigos associados:','npapers','white');
	echo "<tr><td></td><td></td></tr>\n";
	$npapers = $_POST['npapers'];;
	$color='white';
   	for ($i=1;$i<=$MAXPAPERS;$i++) {
	    if ($i <= $npapers) {
               $field='paperID'.$i;
	       tabline("${i}o. Artigo #:",$field,$color);
               $field='xtrapag'.$i;
	       tabline('Núm. Pág. Extras:',$field,$color);
               $field='speaker'.$i;
	       tabline('A ser apresentado por:',$field,$color);
	       if ($color == 'white') {
	       	  $color = '#F8F8FF';
	       } else {
	       	 $color='white';
	       };
	    }
        }
	echo "<tr><td></td><td></td></tr>\n";
	tablineB('Valor da Inscrição:',"R$ ${_POST[TOTAL]},00",'#F4FFF4','red');
   	tabfile('Comprovante Depósito:','receiptfile');
	echo "<tr><td></td><td><input type='submit' value='Finalizar Inscrição'/></td></tr>\n";
	echo "  </table>\n";
	hiddenfield('type');
	hiddenfield('sba');
	hiddenfield('mod');
	hiddenfield('title');
	hiddenfield('fullname');
	hiddenfield('name');
	hiddenfield('familyname');
	hiddenfield('name');
	hiddenfield('afiliation');
	hiddenfield('phone');
	hiddenfield('email');
	hiddenfield('country');
	hiddenfield('npapers');
   	for ($i=1;$i<=$MAXPAPERS;$i++) {
            $field='paperID'.$i;
	    hiddenfield($field);
            $field='xtrapag'.$i;
	    hiddenfield($field);
            $field='speaker'.$i;
	    hiddenfield($field);
        }
	hiddenfield('TOTAL');

	echo "  </form>\n";
        echo "</div></div>";
}


?>

<?php if($_POST['phase'] == 'one'): ?>
<script type="text/javascript">

$('#sbafile').bind('change', function() {
 if (this.files[0].size > MAXSIZE) {
    alert("Arquivo muito grande! por favor, reduza o mesmo.");
 }
});
$('#studentfile').bind('change', function() {
 if (this.files[0].size > MAXSIZE) {
    alert("Arquivo muito grande! por favor, reduza o mesmo.");
 }
});
$('#receiptfile').bind('change', function() {
 if (this.files[0].size > MAXSIZE) {
    alert("Arquivo muito grande! por favor, reduza o mesmo.");
 }
});

function validate() {
    filesOK = true;
    
    if (document.getElementById('mod').value == 'student') {
       if (document.getElementById('studentfile').files[0].size > MAXSIZE) {
       	  alert("Arquivo Comprovante (modalidade estudante) muito Grande !\n Reduzá-o, por favor.");
	  filesOK=false;
       }
    }
    if (document.getElementById('sba').value == 'sba') {
       if (document.getElementById('studentfile').files[0].size > MAXSIZE) {
       	  alert("Arquivo Comprovante (associação SBA) muito Grande !\n Reduzá-o, por favor.");
	  filesOK=false;
       }
    }
    if (document.getElementById('receiptfile').files[0].size > MAXSIZE) {
       alert("Arquivo Comprovante de Depósito muito Grande !\n Reduzá-o, por favor.");
       filesOK=false;
   }

   if (filesOK) {
    return true;
   } else {
    alert('Por favor, revise e corrija os dados no formulário!');
    return false;
   }
}
</script>

<?php endif; ?>
