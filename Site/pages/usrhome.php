<?php

	$thisform=$GBLbasepage.'?q=home';
	$logout=$GBLbasepage.'?st=logout';

function usrdata() {
	echo "<div id='currentdata'><h3>Dados Atuais</h3><hr>" .
		'Nome: ' . $_SESSION['userdisplayname'] . '<br>' .
		'Nome Completo: ' . $_SESSION['username'] . '<br></div>';
	echo formpost($thisform);
	echo formsubmit('act','Mudar Senha');
	echo formsubmit('act','Editar Dados');
	echo '</form>';
}

?>


<div class="row">
    <div class="col-sm-8">
        <h2>Bem vindo <b><?php echo $_SESSION['userdisplayname'];?></b> ao SISGEN </h2>
        <hr>
        <h4><B>Apresentação</B></h4>
        <p align="justify">
	    Work to be tone
	</p>     <p align="justify">
	</p>     <p align="justify">

		<h5><a href="<?php echo $logout; ?>">logout</a></h5>
		<p><p>
		
	<?php 
		switch($_POST['act']) {
			case 'Mudar Senha':
				include 'usrpasswd.php';
				break;
			case 'Atualizar Senha':
				$passwd = $GBLmysqli->real_escape_string($_POST['passORG']);
				$newpasswd = $GBLmysqli->real_escape_string($_POST['passA']);
				if ($GBLmysqli->hashpasswdcheck($passwd)) {
				  $q = "UPDATE `account` SET `chgpasswd` = '0' ,  `password` =  '".$newpasswd."' WHERE `id` = '" . $_SESSION['userid'] . "';";
				  $GBLmysqli->dbquery($q);
				  echo 'Senha Atualizada.<br>';
				  if ($_SESSION['userchgpasswd']) {
					$_SESSION['userchgpasswd'] = 0;
					echo pagereload($GBLbasepage);
				  }
				  
				} else {
				  echo 'Senha Inválida<br>';
				}
				if ($_SESSION['userchgpasswd']) {
					include 'pages/usrpasswd.php';
				} else {
					usrdata();
				}
				break;
			case 'Editar Dados':
				echo formpost($thisform);
				echo '<table>';
				echo '<tr><td>Nome: </td><td>' . formpatterninput(16,16,$GBLnamepattern,'Nome Abreviado','usrdisplayname',$_SESSION['userdisplayname']) . '</td></tr>';
				echo '<tr><td>Nome Completo: </td><td>' . formpatterninput(128,48,$GBLnamepattern,'Nome Completo','usrname',$_SESSION['username']) . '</td></tr>';
				echo '<tr><td></td><td>' .formsubmit('act','Atualizar Dados') . '</td></tr>';
				echo '</table>';
				echo '</form>';
			    break;
			case 'Atualizar Dados':
				$_SESSION['username'] = $_POST['usrname']; 
				$_SESSION['userdisplayname'] = $_POST['usrdisplayname']; 
				$q = "UPDATE `account` SET `name` =  '" . $GBLmysqli->real_escape_string($_POST['usrname'])  ."' , `displayname` =  '" . $GBLmysqli->real_escape_string($_POST['usrdisplayname'])  . "' WHERE `id` = '" . $_SESSION['userid'] . "';";
				$GBLmysqli->dbquery($q);
				usrdata();
				break;
			default :
				if ($_SESSION['userchgpasswd']) {
					include 'pages/usrpasswd.php';
				} else {
					usrdata();
				}
				break;
			
		}
	?>

		
		
    </div>
    
    <div class="col-sm-4">
        <h2>Notícias</h2>
        <hr>
        <table width="100%" id="news" style="border-spacing: 15px; padding:10px">

            <tr>
                <th style="float:left;">Nothing:</th>
                <td ></td>
                <td ></td>
            </tr>
        </table>
    </div>
    

    <div class="col-sm-4">
	<h2>Contato</h2>
	<hr>
	<address>
            <strong>Universidade Federal do Rio Grande do Sul</strong>
            <br>Departmento de Sistemas Elétricos de Automação e Energia (DELAE)
            <br>CEP: 90035-190 - Porto Alegre, RS - Brasil
            <br> 
	</address>
    </div>

</div>

