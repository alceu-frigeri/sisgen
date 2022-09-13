<?php

function sizemsg($errcode) {
    global $MAXSIZE;
    
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
    global $uploaddir;
    $uploadOK = false;
    $errcode = $_FILES[$field]['error'];

    if ($errcode == UPLOAD_ERR_OK) {
	$tmp_name = $_FILES[$field]["tmp_name"];
	$file_name = preg_replace ('/[~-ÿ]/i','_',$_FILES[$field]["name"]);
	$file_name = str_replace('#','_',$file_name);
	$file_name = str_replace('?','_',$file_name);
	$file_name = str_replace('&','_',$file_name);
	
	$file =   $timestamp.'.'.$microstamp.' '.$tag.': '.$file_name;
	if(move_uploaded_file($tmp_name, "${uploaddir}$file")) {
            $uploadOK = true;
	}
    }
    return array($uploadOK,$file,$file_name,$errcode);
}


function file_submit($field,$tag,$accdt,$usrdt,$admin=false) {
    global $mysqli;
    global $timestamp;
    global $microstamp;
    global $uploaddir;

    $admin = $admin ? '1' : '0';

    list ($uploadOK,$file,$org,$ERR) = fileupload($field,$tag,$timestamp,$microstamp);
    if ($uploadOK) {
	if ($usrdt[$field]) {
	    unlink($uploaddir.$usrdt[$field]);
	}

	if (!($stmt = $mysqli->prepare("UPDATE `userdata` SET $field = ?, `usrdata_admin` = ?  WHERE `acc_id` = ?;"))) {
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
	}
	if (!$stmt->bind_param('sii',$file,$admin,$usrdt['acc_id'])) {
	    echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
	};
	$result  = $stmt->execute();
	
    } else {
	echo "<h3><font color=\"red\">Infelizmente ocorreu um erro na transmissão do seu arquivo<br>
	    <ul>
	    <li><b>$org</b>";
	sizemsg($ERR);
	echo "</ul></font></h3><p>
	          Por favor, revise o arquivo, e tente novamente.<p>\n";
    }
}



function usrfiles_updt($accdt,$usrdt,$admin=false) {
    global $DBVALS;
    
    if (($accdt['can_files'] && !$admin) || ($admin == $DBVALS['DB_ADMIN'])) {
	if ($_FILES['sba_file']) {
	    file_submit('sba_file','sba',$accdt,$usrdt,$admin);	
	}
	if ($_FILES['student_file']) {
	    file_submit('student_file','student',$accdt,$usrdt,$admin);	
	}
	if ($_FILES['receipt_file']) {
	    file_submit('receipt_file','receipt',$accdt,$usrdt,$admin);	
	}
	if ($_FILES['copy_file']) {
	    file_submit('copy_file','copyright',$accdt,$usrdt,$admin);	
	}
    } else {
	//	echo "but NO<br>\n";
    }
}

?>
