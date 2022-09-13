<?php
function usrdata_updt($accdt,$usrdt,$admin=false) {
    global $mysqli;
    $admin = $admin ? '1' : '0';

    if ($usrdt) {
	//	echo "UPDATE<br>";
     	if (!($stmt = $mysqli->prepare("UPDATE `userdata` SET `name` = ?, `familyname` = ?, `fullname` = ?, `fullnamereceipt` = ?, `receipt_data` = ?, `affiliation` = ?, `fullaffiliation` = ?, `phone` = ?, `country_id` = ?, `state_id` = ?, `treat_id` = ?, `usrdata_admin` = ? WHERE `acc_id` = ?;"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
   	}
	$name = utf8_encode($_POST['name']);
	$familyname = utf8_encode($_POST['familyname']);
	$fullname = utf8_encode($_POST['fullname']);
	$fullnamereceipt = utf8_encode($_POST['fullnamereceipt']);
	$receiptdata = utf8_encode($_POST['receiptdata']);
	$affiliation = utf8_encode($_POST['affiliation']);
	$fullaffiliation = utf8_encode($_POST['fullaffiliation']);
	if (!$stmt->bind_param('ssssssssssiii',$name,$familyname,$fullname,$fullnamereceipt,$receiptdata,$affiliation,$fullaffiliation,$_POST['phone'],$_POST['country'],$_POST['state'],$_POST['title'],$admin,$accdt['acc_id'])) {
     	    echo "Binding parameters update failed: (" . $stmt->errno . ") " . $stmt->error;
   	};
	if(!$stmt->execute()) {
	    echo "Execute UPDATE failed: (" . $stmt->errno . ") " . $stmt->error;
	};

    } else {
	//	echo "INSERT<br>";
     	if (!($stmt = $mysqli->prepare("INSERT INTO `userdata` (`name`,`familyname`,`fullname`,`fullnamereceipt`,`receipt_data`,`affiliation`,`fullaffiliation`,`phone`,`country_id`,`state_id`,`treat_id`,`usrdata_admin`,`acc_id`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?);"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
   	}
	$name = utf8_encode($_POST['name']);
	$familyname = utf8_encode($_POST['familyname']);
	$fullname = utf8_encode($_POST['fullname']);
	$fullnamereceipt = utf8_encode($_POST['fullnamereceipt']);
	$receiptdata = utf8_encode($_POST['receiptdata']);
	$affiliation = utf8_encode($_POST['affiliation']);
	$fullaffiliation = utf8_encode($_POST['fullaffiliation']);
	if (!$stmt->bind_param('ssssssssssiii',$name,$familyname,$fullname,$fullnamereceipt,$receiptdata,$affiliation,$fullaffiliation,$_POST['phone'],$_POST['country'],$_POST['state'],$_POST['title'],$admin,$accdt['acc_id'])) {
     	    echo "Binding parameters insert failed: (" . $stmt->errno . ") " . $stmt->error;
   	};
	if(!$stmt->execute()) {
	    echo "Execute INSERT failed: (" . $stmt->errno . ") " . $stmt->error;
	};
    }
    $stmt->close();
    eventlog('USER','Data Updt',"Personal data updated:$fullname");

}


function usrdata_edit($accdt,$usrdt) {
    global $mysqli;

    
    echo "<h2>Sistema de Inscrição</h2>
    <hr>
    <h3>Dados Pessoais</h3>";
    
    regform('usrdata_updt','return titlecheck()');

    echo "
    <table id='regform' style='width:70%'>
    <tr>
    <td style='text-align:right; width:30%'>Forma de Tratamento:</td><td style='text-align:left'>
    <select name='title' id='title' onblur='titlecheck()'  onselect='titlecheck()' onchange='titlecheck()'>\n";


    if (!($result = $mysqli->query("SELECT * FROM `treatment`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($treat=$result->fetch_assoc())) {
	if ($treat['treat_id'] == $usrdt['treat_id']) {
	    echo "<option value='$treat[treat_id]' selected>$treat[treat_str2]</option>\n";
	} else {
	    echo "<option value='$treat[treat_id]'>$treat[treat_str2]</option>\n";
	}
    }
    $result->close();
    if ($usrdt['treat_id']) {
     	echo "<option value='selectone'>Selecione Um</option>";		
    } else {
     	echo "<option value='selectone' selected>Selecione Um</option>";		
    };
    $txtpatternA='[a-zA-ZÀ-ü]';
    $txtpatternB='[a-zA-ZÀ-ü\. \-]';
    $txtpatternC='[a-zA-ZÀ-ü\. \-/]';
    $txtpatternD='[0-9a-zA-ZÀ-ü\. \-/:]';
    echo "</select><span id='selectcolor' style='color:white'>Selecione Um !</span>
    </td>
    </tr>
    <tr>
    <td style='text-align:right; width:30%'>Nome Completo: (comprovantes)</td><td style='text-align:left'>
    <input type='text' name='fullname' id='fullname' size='30' required pattern='${txtpatternB}+' title='somente letras! e .' value='".($usrdt['fullname'])."'/></td>
    </tr>
    <tr>
    <td style='text-align:right; width:30%'>Nome Completo:<br> (recibo pagamento)</td><td style='text-align:left'>
    <input type='text' name='fullnamereceipt' id='fullnamereceipt' size='30' required pattern='${txtpatternD}+' title='somente letras/números! e .' value='".($usrdt['fullnamereceipt'])."'/></td>
    </tr>
    <tr>
    <td style='text-align:right; width:30%'>Dados Adicionais:<br> (recibo pagamento)<br>e.g. CPF/CNPJ/CNPq</td><td style='text-align:left'>
    <input type='text' name='receiptdata' id='receiptdata' size='30' required value='".($usrdt['receipt_data'])."'/></td>
    </tr>
    <tr>
    <td style='text-align:right; width:30%'>Afiliação:<br> (recibos/comprovantes)</td><td style='text-align:left'>
    <input type='text'name='fullaffiliation' id='fullaffiliation' size='30' required pattern='${txtpatternC}+' title='somente letras! e .-/' value='".($usrdt['fullaffiliation'])."'/></td>
    </tr>
    <tr>
    <td style='text-align:right; width:30%'>Nome: (Crachá)</td><td style='text-align:left'>
    <input type='text' name='name' id='name' size='20' required pattern='${txtpatternB}{1,10}' title='somente letras!' value='".($usrdt['name'])."'/></td>
    </tr>
    <tr>
    <td style='text-align:right; width:30%'>Sobrenome: (Crachá)</td><td style='text-align:left'>
    <input type='text' name='familyname' id='familyname' size='30' required pattern='${txtpatternB}+' title='somente letras! e .'  value='".($usrdt['familyname'])."'/></td>
    </tr>
    <tr>
    <td style='text-align:right; width:30%'>Sigla Afiliação: (Crachá)</td><td style='text-align:left'>
    <input type='text'name='affiliation' id='affiliation' size='30' required pattern='${txtpatternC}+' title='somente letras! e .-/' value='".($usrdt['affiliation'])."'/></td>
    </tr>
    <tr>
    <td style='text-align:right; width:30%'>Telefone de Contato:</td><td style='text-align:left'>
    <input type='text' name='phone' id='phone' size='20' required class='phonemask' pattern='\([0-9]{2}\) ([0-9].)?[0-9]{4}-[0-9]{4}' title='(##) ####-#### ou (##) #####-####' value='$usrdt[phone]'/></td>
    </tr>
    <tr>
    <td style='text-align:right; width:30%'>Pais:</td><td style='text-align:left'>
    <select name='country' id='country'>";

    if (!($result = $mysqli->query("SELECT * FROM `country`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($country=$result->fetch_assoc())) {
	if ($country['country_id'] == $usrdt['country_id']) {
     	    echo "<option value='$country[country_id]' selected>$country[country_name]</option>\n";
	} else {
	    if ($usrdt['country_id'] || ($country['country_id'] != 'BR') ) {
     		echo "<option value='$country[country_id]'>$country[country_name]</option>\n";
	    } else {
     		echo "<option value='$country[country_id]' selected>$country[country_name]</option>\n";
	    }
	}
    }


    echo "</select>
    </td>
    </tr>

    <tr>
    <td style='text-align:right; width:30%'>Estado:</td><td style='text-align:left'>
    <select name='state' id='state'>";

    if (!($result = $mysqli->query("SELECT * FROM `states`"))) {
	echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
	return NULL;
    }
    while (($state=$result->fetch_assoc())) {
	$state[state_name] = utf8_decode($state[state_name]);
	
	if ($state['state_id'] == $usrdt['state_id']) {
     	    echo "<option value='$state[state_id]' selected>$state[state_name]</option>\n";
	} else {
	    if ($usrdt['state_id'] || ($state['state_id'] != 'BR') ) {
     		echo "<option value='$state[state_id]'>$state[state_name]</option>\n";
	    } else {
     		echo "<option value='$state[state_id]' selected>$state[state_name]</option>\n";
	    }
	}
    }


    echo "</select>
    </td>
    </tr>



    </table>
    <input type='submit' value='Inserir'/>
    </form>

    <script type='text/javascript'>
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

    <!-- see https://igorescobar.github.io/jQuery-Mask-Plugin/ -->
		 var SPMaskBehavior = function (val) {
      		     return val.replace(/\D/g, '').length === 11 ? '(00) 0.0000-0000' : '(00) 0000-00009';
		 },
    spOptions = {
      	onKeyPress: function(val, e, field, options) {
       	    field.mask(SPMaskBehavior.apply({}, arguments), options);
        }
    };

    $('.phonemask').mask(SPMaskBehavior, spOptions);
    

    </script>";

}

?>





