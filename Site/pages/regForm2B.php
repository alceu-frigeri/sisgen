<?php include "FLAGS.php" ?>

<?php

 date_default_timezone_set('America/Sao_Paulo');
 $timestamp=time();
 $timestamp=date('Y-m-d H:i:s',$timestamp);

 list($microstamp,$sec) = explode(' ',microtime(false));
 list($nothing,$microstamp) = explode('.',$microstamp);


function fileupload($field,$tag,$timestamp,$microstamp) {
   $uploadOK = false;

   if ($_FILES[$field]["error"] == UPLOAD_ERR_OK) {
      $tmp_name = $_FILES[$field]["tmp_name"];
      $file_name = $_FILES[$field]["name"];
	    
      $file =   $timestamp.'.'.$microstamp.' '.$tag.': '.$file_name;
      if(move_uploaded_file($tmp_name, '/export/var/www/sbai17/uploads/'.$file)) {
         $uploadOK = true;
      }
   }
   return array($uploadOK,$file,$file_name);
}


list ($receiptuploadOK,$receiptfile,$receiptorg) = fileupload('receiptfile','receipt',$timestamp,$microstamp);


$type = $_POST['type'];
$sba =  $_POST['sba'];
$mod =  $_POST['mod'];
$npapers =  $_POST['npapers'];
$value =  $_POST['TOTAL'];
$title =  $_POST['title'];
$name =  $_POST['name'];
$familyname =  $_POST['familyname'];
$fullname =  $_POST['fullname'];
$afiliation =  $_POST['afiliation'];
$phone =  $_POST['phone'];
$email =  $_POST['email'];
$country =  $_POST['country'];


$pag1 =  $_POST['xtrapag1'];


if ($mod == 'student') {
   list ($studentuploadOK,$studentfile,$studentorg) = fileupload('studentfile','student',$timestamp,$microstamp);
} else {
  $studentuploadOK = true;
  $studentfile='none';
}

if ($sba == 'sba') {
   list ($sbauploadOK,$sbafile,$sbaorg) = fileupload('sbafile','sba',$timestamp,$microstamp);
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

    if (($title == 'Profa.') || ($title == 'Dra.') || ($title == 'Sra.') || ($title == 'Srta.')) {
       $title2 = 'Prezada';
    } else {
       $title2 = 'Prezado';
    }

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
	   echo "<li>seu recibo: <b>".$receiptorg."</b><br>";
	}
	if (!$studentuploadOK) {
	   echo "<li>seu comprovante de matrícula: <b>".$studentorg."</b><br>";
	}
	if (!$sbauploadOK) {
	   echo "<li>seu comprovante de Associação: <b>".$sbaorg."</b><br>";
	}
	echo "</ul></font></h3><p>";
	echo "Por favor, retorne a página anterior, revise os dados/arquivos, e tente novamente.";
    }
?>
