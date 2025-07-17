
<?php $thisform = $GBLbasepage . '?q=admin&sq=DBimport'; ?>
<div class = "row">
        <h2>Initial Data setup/import </h2>
        <hr>

		
<?php

  include 'coreX.php';
  




  if($_SESSION['role']['isadmin'] & $sisgensetup) {
	switch($_POST['act']) {
		case 'Import Initial Data':
			if($_POST['importdata'] & $sisgenfullsetup) {
				echo '<h3>Importing Initial Data</h3>';
				echo 'going over it...<br>';
				
					echo '<h3>Inserting Courses Curricula</h3>';
					if(!($file = fopen('csv/grades.csv' , 'r'))) {
						echo 'grades.csv file not found...';
					};
					$line = fgetcsv($file , 512 , ',' , '"' , '"');
					while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
						DBinsertgrade($line[0] , $line[1] , $line[2] , $line[3] , $line[4] , $line[5]);
					}					
					fclose($file);
					echo '<p>';
					echo '<h3>Inserting Classes</h3>';						
					if(!($file = fopen('csv/semestres.csv' , 'r')))	{
						echo 'semestres.csv file not found...';
					};
					$line = fgetcsv($file , 512 , ',' , '"' , '"');
					while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
						DBinsertdept($line[0] , $line[1] , $line[2] , $line[3] , $line[4] , $line[5] , $line[6] , $line[7] , $line[8] , $line[9] , $line[10] , $line[11] , $line[12] , $line[13] , $line[14]);
					}
					fclose($file);
					echo '<p>';

					echo '<h3>Fixing Rooms Info</h3>';
					$q = "UPDATE `semester` SET `readonly` = '1';";
					$GBLmysqli->dbquery($q);
					$q = "SELECT `id` FROM `building` WHERE `acronym` = 'Eletro';";
					$result = $GBLmysqli->dbquery($q);
					$sqlrow = $result->fetch_assoc();
					$q = "SELECT `id` FROM `roomtype` WHERE `acronym` = 'Teo';";
					$result = $GBLmysqli->dbquery($q);
					$roomT = $result->fetch_assoc();
					$q = "SELECT `id` FROM `roomtype` WHERE `acronym` = 'Lab';";
					$result = $GBLmysqli->dbquery($q);
					$roomL = $result->fetch_assoc();
					$q = "SELECT `id` FROM `roomtype` WHERE `acronym` = 'Inf';";
					$result = $GBLmysqli->dbquery($q);
					$roomI = $result->fetch_assoc();

					roomset($roomL['id'] , '20' , $sqlrow['id'] , '110');
					roomset($roomL['id'] , '18' , $sqlrow['id'] , '200');
					roomset($roomL['id'] , '18' , $sqlrow['id'] , '201');
					roomset($roomL['id'] , '18' , $sqlrow['id'] , '202');
					roomset($roomI['id'] , '20' , $sqlrow['id'] , '301');
					roomset($roomT['id'] , '20' , $sqlrow['id'] , '301a');
					roomset($roomT['id'] , '40' , $sqlrow['id'] , '303');
					roomset($roomT['id'] , '40' , $sqlrow['id'] , '304');
					roomset($roomI['id'] , '20' , $sqlrow['id'] , '305');
					roomset($roomT['id'] , '60' , $sqlrow['id'] , '306');
					
					echo '<h3>Fixing Vacancies</h3>';
					fixvacancies();

				echo 'done<p>';
			}
		break;
		
		case 'Import Term Data':
			if($_POST['termdata']) {
				echo '<h3>Importing Term Data</h3>';
				echo 'going over it...<br>';
					echo '<h3>Inserting Classes</h3>';						
					if(!($file = fopen('csv/TERM.csv' , 'r')))	{
						echo 'TERM.csv file not found...';
					};
					$line = fgetcsv($file , 512 , ',' , '"' , '"');
					while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
						DBinsertdept($line[0] , $line[1] , $line[2] , $line[3] , $line[4] , $line[5] , $line[6] , $line[7] , $line[8] , $line[9] , $line[10] , $line[11] , $line[12] , $line[13] , $line[14]);
					}
					fclose($file);
					echo '<p>';

					echo '<h3>Fixing Vacancies</h3>';
					fixvacancies();

				echo 'done<p>';
			}
		break;

                case 'Import Term Reserv Data':
			if($_POST['termreservdata']) {
				echo '<h3>Importing Term Reserv Data</h3>';
				echo 'going over it...<br>';
					if(!($file = fopen('csv/TERMreserv.csv' , 'r')))	{
						echo 'TERMreserv.csv file not found...';
					};
					$line = fgetcsv($file , 512 , ',' , '"' , '"');
					while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
						DBinsertreserv($line[0] , $line[1] , $line[2] , $line[3] , $line[4] , $line[5] , $line[6]);
					}
					fclose($file);
				echo 'done<p>';
			}
                break;

		case 'Import Course Data':
			if($_POST['coursedata']) {
				echo '<h3>Importing Course Data</h3>';
				echo 'going over it...<br>';
					echo '<h3>Inserting Courses Curricula</h3>';						
					if(!($file = fopen('csv/COURSE.csv' , 'r')))	{
						echo 'COURSE.csv file not found...';
					};
					$line = fgetcsv($file , 512 , ',' , '"' , '"');
					while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
						DBinsertgrade($line[0] , $line[1] , $line[2] , $line[3] , $line[4] , $line[5]);
					}					
					fclose($file);
					echo '<p>';

					echo '<h3>Fixing Vacancies</h3>';
					fixvacancies();

				echo 'done<p>';
			}
		break;
		
		
		case 'Fix Vacancies':
			if($_POST['fixvacancies']) {
				echo '<h3>Fixing Vacancies...</h3>';
				fixvacancies();
				echo 'done<br>';
			}
		break;
		case 'Export acc/unit tables':
			if($_POST['exporttables']) {
				echo '<h3>Exporting acc/unit tables</h3>';
				ftableexport('accounts.csv' , "SELECT * FROM `account` ORDER BY `id`;");
				ftableexport('roles.csv' , "SELECT `role` . *, `unit` . `acronym` FROM `role` , `unit` WHERE `role` . `unit_id` = `unit` . `id` ORDER BY `role` . `id`;");
				ftableexport('accroles.csv' , "SELECT `account` . `email` , `role` . `rolename` FROM `accrole` , `account` , `role` WHERE `accrole` . `account_id` = `account` . `id` AND `accrole` . `role_id` = `role` . `id`  ORDER BY `accrole` . `id`;"); 
				ftableexport('units.csv' , "SELECT * FROM `unit` ORDER BY `id`;");

				ftableexport('scenery.csv' , "SELECT * FROM `scenery` ORDER BY `id`;");
				ftableexport('sceneryroles.csv' , "SELECT `scenery` . `name` , `role` . `rolename` FROM `scenery` , `role` , `sceneryrole` WHERE `sceneryrole` . `scenery_id` = `scenery` . `id` AND `sceneryrole` . `role_id` = `role` . `id` ORDER BY `sceneryrole` . `id`;");
				echo 'done<br>';
			}
		break;
		case 'Restore acc/unit tables':
			if($_POST['restoretables']) {
				echo '<h3>Restoring acc/unit tables</h3>';
				
				if(!($file = fopen('csv/accounts.csv' , 'r'))) {
					echo 'accounts.csv file not found...<br>';
				};
				while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
					foreach ($line as &$val) {
						$val = $GBLmysqli->real_escape_string($val);
					}
					$q = "SELECT * FROM `account` WHERE `email` = '" . $line[1] . "';";
					$result = $GBLmysqli->dbquery($q);
					if($result->fetch_assoc()){
						$q = "UPDATE `account` SET `password` = '" . $line[2] . "', `name` = '" . $line[7] . "', `displayname` = '" . $line[8] . "'  WHERE `email` = '" . $line[1] . "';";
						$GBLmysqli->dbquery($q);
					} else {
						unset($line[0]);
						unset($line[4]);
						unset($line[5]);
						$q = "INSERT INTO `account` (`email` , `password` , `chgpasswd` , `activ` , `name` , `displayname`) VALUES ('" . implode("','" , $line) . "');";
						$GBLmysqli->dbquery($q);
					}
				}
				fclose($file);


				if(!($file = fopen('csv/roles.csv' , 'r'))) {
					echo 'roles.csv file not found...<br>';
				};
				while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
					foreach ($line as &$val) {
						$val = $GBLmysqli->real_escape_string($val);
					}
					$q = "SELECT * FROM `unit` WHERE `acronym` = '" . $line[16] . "';";
					$unitsql = $GBLmysqli->dbquery($q);
					$unitrow = $unitsql->fetch_assoc();
					$q = "SELECT * FROM `role` WHERE `rolename` = '" . $line[1] . "';";
					$result = $GBLmysqli->dbquery($q);
					if($result->fetch_assoc()){
						$q = "UPDATE `role` SET " . "`description` = '" . $line[2] . "',  `isadmin` = '" . $line[3] . 
							"', `can_edit` = '" . $line[4] . "', `can_dupsem` = " . $line[5] . ", `can_class` = " . $line[6] . 
							", `can_addclass` = " . $line[7] . ", `can_vacancies` = " . $line[8] . ", `can_disciplines` = " . $line[9] . 
							", `can_coursedisciplines` = " . $line[10] . ", `can_prof` = " . $line[11] . ", `can_room` = " . $line[12] . 
							", `can_viewlog` = " . $line[13] . ", `unit_id` = " . $unitrow['id'] . " WHERE `rolename` = '" . $line[1] . "';";
						$GBLmysqli->dbquery($q);
					} else {
						unset($line[0]);
						unset($line[14]);						
						unset($line[15]);		
						unset($line[16]);						
					

						$q = "INSERT INTO `role` (`rolename` , `description` ,  `isadmin` , `can_edit` , `can_dupsem` , `can_class` , `can_addclass` ,  `can_vacancies` , `can_disciplines` , `can_coursedisciplines` , `can_prof` , `can_room` , `can_viewlog` , `unit_id`) VALUES "  . 
						"('". implode("','" , $line) . "','" . $unitrow['id'] . "');";
						$GBLmysqli->dbquery($q);
					}
				}
				fclose($file);



				if(!($file = fopen('csv/accroles.csv' , 'r'))) {
					echo 'accroles.csv file not found...<br>';
				};
				while ($line = fgetcsv($file , 512 , ',' , '"','"')) {
					$q = "SELECT `account` . `id`  FROM `account` WHERE `email` = '" . $line[0] . "';" ;
					$result = $GBLmysqli->dbquery($q);
					$accrow = $result->fetch_assoc();
					$q = "SELECT `role` . `id`  FROM `role` WHERE `rolename` = '" . $line[1] . "';" ;
					$result = $GBLmysqli->dbquery($q);
					$rolerow = $result->fetch_assoc();
					$q = "SELECT * FROM `accrole` WHERE `account_id` = '" . $accrow['id'] . "' AND `role_id` = '" . $rolerow['id'] . "';";
					$result = $GBLmysqli->dbquery($q);
					if(!$result->fetch_assoc()){
						$q = "INSERT INTO `accrole` (`account_id` , `role_id`) VALUES ('" . $accrow['id'] . "' , '" . $rolerow['id'] . "');";
						$GBLmysqli->dbquery($q);
					}
				}
				fclose($file);


				if(!($file = fopen('csv/units.csv' , 'r'))) {
					echo 'units.csv file not found...<br>';
				};
				while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
					$q = "UPDATE `unit` SET `contactname` = '" . $GBLmysqli->real_escape_string($line[4]) . "', `contactemail` = '" . $GBLmysqli->real_escape_string($line[5]) . "', `contactphone` = '" . $GBLmysqli->real_escape_string($line[6]) . "'  WHERE `id` = '" . $line[0] . "';";
					$GBLmysqli->dbquery($q);
				}					
				fclose($file);

				
				if(!($file = fopen('csv/scenery.csv' , 'r'))) {
					echo 'scenery.csv file not found...<br>';
				};
				while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
					$q = "SELECT * FROM `scenery` WHERE `name` = '" . $line[1] . "';";
					$result = $GBLmysqli->dbquery($q);
					if(!$result->fetch_assoc()) {					
						$q = "INSERT INTO `scenery` (`name` , `desc`) VALUES  ('" . $line[1] . "' , '" . $line[2] . "');";
						$GBLmysqli->dbquery($q);
					}
				}					
				fclose($file);

				if(!($file = fopen('csv/sceneryroles.csv' , 'r'))) {
					echo 'sceneryroles.csv file not found...<br>';
				};
				while ($line = fgetcsv($file , 512 , ',' , '"' , '"')) {
					$q = "SELECT `scenery` . `id`  FROM `scenery` WHERE `name` = '" . $line[0] . "';" ;
					$result = $GBLmysqli->dbquery($q);
					$scenrow = $result->fetch_assoc();
					$q = "SELECT `role` . `id`  FROM `role` WHERE `rolename` = '" . $line[1] . "';" ;
					$result = $GBLmysqli->dbquery($q);
					$rolerow = $result->fetch_assoc();
					$q = "SELECT * FROM `sceneryrole` WHERE `scenery_id` = '" . $scenrow['id'] . "' AND `role_id` = '" . $rolerow['id'] . "';";
					$result = $GBLmysqli->dbquery($q);
					if(!$result->fetch_assoc()){
						$q = "INSERT INTO `sceneryrole` (`scenery_id` , `role_id`) VALUES ('" . $scenrow['id'] . "' , '" . $rolerow['id'] . "');";
						$GBLmysqli->dbquery($q);
					}
				}					
				fclose($file);

				
				
				
				echo 'done<br>';
			}
		break;
		case 'Courses Adjust':
		if($_POST['courseadjust'] & $sisgenDBsetupHacks){

			echo 'Adjusting CCA<br>';
			$courseid = $_SESSION['unitbycode']['CCA99']['id'];
			duplicatecourse($courseid , 'oCCA' , 'CCA98' , 'Antiga Grade CCA' , 'dup. from CCA');
			DBinsertgrade('CCA99' , 'Etp.05' , 'ENG0700a' , 'TERMODINÂMICA E TRANSFERÊNCIA DE CALOR' , '5' , 'OB');
			DBinsertgrade('CCA99' , 'Etp.09' , 'ENG0700b' , 'CONTROLE AVANÇADO DE PROCESSOS' , '3' , 'OB');
			DBinsertgrade('CCA99' , 'EL' , 'ENG1000a' , 'ROBÓTICA MÓVEL' , '4' , 'EL');
			DBinsertgrade('CCA99' , 'EL' , 'ENG1000b' , 'SISTEMAS DE TEMPO REAL' , '4' , 'EL');
			DBinsertgrade('CCA99' , 'Etp.06' , 'CCA9900a' , 'PROJETO INTEGRADO I' , '2' , 'OB');
			DBinsertgrade('CCA99' , 'Etp.08' , 'CCA9900b' , 'PROJETO INTEGRADO II' , '2' , 'OB');
			DBinsertgrade('CCA99' , 'EL' , 'CCA9900c' , 'PROJETO INTEGRADO III' , '2' , 'EL');
			disccourserem(array('ENG07041' , 'ENG07031' , 'CCA99002' , 'CCA99003' , 'CCA99004' , 'ENG03091' , 'ENG03374' , 'ECO02063') , $courseid);
			
			courseupdt('CCA99' , 'ENG10021' , 'EL' , 'EL');
			courseupdt('CCA99' , 'ENG10046' , 'EL' , 'EL');
			courseupdt('CCA99' , 'ENG10027' , 'EL' , 'EL');
			courseupdt('CCA99' , 'ENG10050' , 'EL' , 'EL');
			courseupdt('CCA99' , 'ENG03386' , 'EL' , 'EL');
			courseupdt('CCA99' , 'ENG03046' , 'EL' , 'EL');
			courseupdt('CCA99' , 'ENG03047' , 'EL' , 'EL');
			courseupdt('CCA99' , 'ENG03044' , 'OB' , 'Etp.04');
			courseupdt('CCA99' , 'ENG03316' , 'OB' , 'Etp.04');
			courseupdt('CCA99' , 'ENG03048' , 'OB' , 'Etp.10');
			courseupdt('CCA99' , 'ENG03380' , 'AL' , 'Etp.05');
			courseupdt('CCA99' , 'ENG10026' , 'AL' , 'Etp.05');
			courseupdt('CCA99' , 'ENG10044' , 'OB' , 'Etp.05');
			courseupdt('CCA99' , 'ENG04475' , 'OB' , 'Etp.07');
			echo 'done<p><br>';
		
			echo 'Adjusting ENE<br>';
			$courseid = $_SESSION['unitbycode']['EEN99']['id'];
			duplicatecourse($courseid , 'oENE' , 'EEN98' , 'Antiga Grade ENE' , 'dup. from ENE');
			DBinsertgrade('EEN99' , 'Etp.02' , 'EEN9900a' , 'PROJETO INTEGRADOR I' , '2' , 'OB');
			DBinsertgrade('EEN99' , 'Etp.07' , 'EEN9900b' , 'PROJETO INTEGRADOR II' , '2' , 'OB');
			DBinsertgrade('EEN99' , 'Etp.08' , 'EEN9900c' , 'PROJETO INTEGRADOR III' , '2' , 'OB');
			DBinsertgrade('EEN99' , 'EL' , 'EEN9900d' , 'PROJETO INTEGRADOR IV' , '2' , 'EL');
			DBinsertgrade('EEN99' , 'EL' , 'EEN9900e' , 'TÓPICOS ESPECIAIS EM ENGENHARIA DE ENERGIA II' , '2' , 'EL');
			DBinsertgrade('EEN99' , 'EL' , 'HUM03127' , 'HISTÓRIA E RELAÇÕES ÉTNICO-RACIAIS' , '4' , 'AD');
			disccourserem(array('EEN99011' , 'EEN99012' , 'EEN99013' , 'ENG04421' , 'ENG04422' , 'ENG03050' , 'ENG03355' , 'ENG03663' , 'MAT01031') , $courseid);

			courseupdt('EEN99' , 'ENG09002' , 'EL' , 'EL');
			courseupdt('EEN99' , 'ADM01135' , 'OB' , 'Etp.04');
			courseupdt('EEN99' , 'ENG10017' , 'OB' , 'Etp.05');
			courseupdt('EEN99' , 'ENG10044' , 'OB' , 'Etp.06');
			courseupdt('EEN99' , 'ENG03041' , 'OB' , 'Etp.03');
			courseupdt('EEN99' , 'ENG02213' , 'OB' , 'Etp.06');
			courseupdt('EEN99' , 'ENG03055' , 'AL' , 'Etp.10');
			courseupdt('EEN99' , 'MED05011' , 'AL' , 'Etp.10');
			

			echo 'done<p>';
			
			echo 'Adjusting MEC<br>';
			$courseid = $_SESSION['unitbycode']['MEC99']['id'];
			duplicatecourse($courseid , 'oMEC' , 'MEC98' , 'Antiga Grade MEC' , 'dup. from MEC');
			DBinsertgrade('MEC99' , 'Etp.01' , 'ARQ0300a' , 'GEOMETRIA DESCRITIVA APLICADA À ENGENHARIA' , '4' , 'OB');
			DBinsertgrade('MEC99' , 'Etp.04' , 'ENG0300a' , 'LABORATÓRIO DE MECÂNICA' , '4' , 'OB');
			DBinsertgrade('MEC99' , 'Etp.05' , 'ENG0300b' , 'PROJETO INTEGRADO I' , '2' , 'OB');
			DBinsertgrade('MEC99' , 'Etp.06' , 'ENG0300c' , 'INSTRUMENTAÇÃO' , '6' , 'OB');
			DBinsertgrade('MEC99' , 'Etp.07' , 'ENG0300d' , 'PROJETO INTEGRADO II' , '2' , 'OB');
			DBinsertgrade('MEC99' , 'Etp.07' , 'ENG0300e' , 'MÉTODOS GERENCIAIS EM MANUTENÇÃO' , '2' , 'AL');
			DBinsertgrade('MEC99' , 'Etp.07' , 'ENG0300f' , 'ANALÍTICA DE DADOS E PROJETO DE EXPERIMENTOS' , '4' , 'AL');
			DBinsertgrade('MEC99' , 'Etp.07' , 'ENG0300g' , 'METODOLOGIA CIENTÍFICA' , '2' , 'AL');
			DBinsertgrade('MEC99' , 'Etp.09' , 'ENG0300h' , 'PROJETO INTEGRADO III' , '2' , 'OB');
			DBinsertgrade('MEC99' , 'Etp.09' , 'ENG0300i' , 'PROJETO DE TRABALHO DE CONCLUSÃO DE CURSO' , '2' , 'OB');

			DBinsertgrade('MEC99' , 'Etp.07' , 'ENG0300j' , 'ANÁLISE DE DADOS EXPERIMENTAIS E DOE' , '4' , 'AL');
			
			
			DBinsertgrade('MEC99' , 'Etp.06' , 'ENG03382' , 'MÁQUINAS AGRÍCOLAS' , '4' , 'EL');
			DBinsertgrade('MEC99' , 'Etp.08' , 'ENG03074' , 'ENERGIA SOLAR FOTOVOLTAICA' , '4' , 'EL');


			DBinsertgrade('MEC99' , 'EL' , 'HUM04002' , 'INTRODUÇÃO À SOCIOLOGIA - A' , '4' , 'EL');
			DBinsertgrade('MEC99' , 'EL' , 'HUM03127' , 'HISTÓRIA E RELAÇÕES ÉTNICO-RACIAIS' , '4' , 'EL');
			

			courseupdt('MEC99' , 'ENG04453' , '' , 'Etp.05');
			courseupdt('MEC99' , 'ENG02001' , '' , 'Etp.02');
			courseupdt('MEC99' , 'ENG03092' , '' , 'Etp.03');
			courseupdt('MEC99' , 'ENG02002' , '' , 'Etp.04');
			courseupdt('MEC99' , 'ENG03004' , '' , 'Etp.04');
			courseupdt('MEC99' , 'MAT02219' , '' , 'Etp.03');
			courseupdt('MEC99' , 'ENG03080' , '' , 'Etp.05');
			courseupdt('MEC99' , 'ENG02003' , '' , 'Etp.05');
			courseupdt('MEC99' , 'ENG03331' , '' , 'Etp.06');
			courseupdt('MEC99' , 'ENG03384' , '' , 'Etp.06');
			courseupdt('MEC99' , 'ENG03324' , '' , 'Etp.06');
			courseupdt('MEC99' , 'ENG03325' , '' , 'Etp.07');
			courseupdt('MEC99' , 'ENG03010' , '' , 'Etp.08');
			courseupdt('MEC99' , 'ADM01135' , '' , 'Etp.08');
			courseupdt('MEC99' , 'ENG03073' , 'OB' , 'Etp.07');
			courseupdt('MEC99' , 'ENG03048' , 'AL' , 'Etp.07');
			courseupdt('MEC99' , 'ENG03055' , 'OB' , 'Etp.08');

			courseupdt('MEC99' , 'ENG03332' , 'EL' , 'Etp.06');
			courseupdt('MEC99' , 'ENG06101' , 'EL' , 'Etp.07');
			courseupdt('MEC99' , 'ENG06648' , 'OB' , 'Etp.07');
			courseupdt('MEC99' , 'ADM01134' , 'EL' , 'Etp.08');
			courseupdt('MEC99' , 'ENG03112' , 'EL' , 'Etp.08');
			courseupdt('MEC99' , 'ENG03342' , 'EL' , 'Etp.08');
			courseupdt('MEC99' , 'DIR04423' , 'EL' , 'Etp.08');
			courseupdt('MEC99' , 'ECO02254' , 'EL' , 'Etp.08');
			courseupdt('MEC99' , 'ENG03001' , 'EL' , 'Etp.09');
			disccourserem(array('ARQ03317' , 'ARQ03320' , 'ENG03353' , 'ENG03350' , 'ENG03333' , 'ENG03081' , 'ENG03037' , 'ENG03108' , 'ENG03091' , 'ENG03065' , 'ENG03355' , 'MED05011') , $courseid);

			
			echo 'done<p>';
			
			echo 'Adjusting ELE<br>';
			$courseid = $_SESSION['unitbycode']['ELE99']['id'];
			duplicatecourse($courseid , 'oELE' , 'ELE98' , 'Antiga Grade ELE' , 'dup. from ELE');
			DBinsertgrade('ELE99' , 'Etp.09' , 'ELE9900a' , 'SEMINÁRIO DE ANDAMENTO E PRÁTICA EXTENSIONISTA DE ESTÁGIO SUPERVISIONADO' , '2' , 'OB');
			DBinsertgrade('ELE99' , 'Etp.09' , 'ELE9900b' , 'SEMINÁRIO DE ANDAMENTO E PRÁTICA EXTENSIONISTA DE PROJETO DE DIPLOMAÇÃO I' , '2' , 'OB');
			DBinsertgrade('ELE99' , 'Etp.10' , 'ELE9900c' , 'SEMINÁRIO DE ANDAMENTO E PRÁTICA EXTENSIONISTA DE PROJETO DE DIPLOMAÇÃO II' , '2' , 'OB');
			DBinsertgrade('ELE99' , 'Etp.09' , 'ELE9900d' , 'PROJETO DE DIPLOMAÇÃO I - ELE' , '1' , 'OB');
			DBinsertgrade('ELE99' , 'Etp.10' , 'ELE9900e' , 'PROJETO DE DIPLOMAÇÃO II - ELE' , '1' , 'OB');
		
			echo 'done<p>';

			echo '<h3>Fixing Vacancies...</h3>';
			fixvacancies();
			echo 'done<br>';
		}
		break;
		default:
		break;
		
	}
        if ($sisgenfullsetup) {
        	echo formpost($thisform);
        	formselectsession('importdata' , 'bool' , 0);
        	echo formsubmit('act' , 'Import Initial Data') . '</form><p>';
        }

	echo formpost($thisform);
	formselectsession('fixvacancies' , 'bool' , 0);
	echo formsubmit('act' , 'Fix Vacancies') . '</form><p>';

	echo formpost($thisform);
	formselectsession('exporttables' , 'bool' , 0);
	echo formsubmit('act' , 'Export acc/unit tables') . '</form><p>';

        if ($sisgenfullsetup) {
        	echo formpost($thisform);
        	formselectsession('restoretables' , 'bool' , 0);
        	echo formsubmit('act' , 'Restore acc/unit tables') . '</form><p>';	
        }

        if ($sisgenDBsetupHacks) {
        	echo formpost($thisform);
        	formselectsession('courseadjust' , 'bool' , 0);
        	echo formsubmit('act' , 'Courses Adjust') . '</form><p>';	
        }

	echo formpost($thisform);
	formselectsession('termdata' , 'bool' , 0);
	echo formsubmit('act' , 'Import Term Data') . '</form><p>';

	echo formpost($thisform);
	formselectsession('termreservdata' , 'bool' , 0);
	echo formsubmit('act' , 'Import Term Reserv Data') . '</form><p>';

	echo formpost($thisform);
	formselectsession('expreservdata' , 'bool' , 0);
        formselectsql($anytmp,  "SELECT * FROM `semester`  ORDER BY `name` DESC;"   , 'semid' , $_POST['semid'] , 'id' , 'name');
	echo formsubmit('act' , 'Export Reserv Data') . '</form><p>';

	echo formpost($thisform);
	formselectsession('impreservdata' , 'bool' , 0);
        formselectsql($anytmp,  "SELECT * FROM `semester` WHERE `readonly` = '0' ORDER BY `name` DESC;"   , 'semid' , $_POST['semid'] , 'id' , 'name');
	echo formsubmit('act' , 'Import Reserv Data') . '</form><p>';

	echo formpost($thisform);
	formselectsession('coursedata' , 'bool' , 0);
	echo formsubmit('act' , 'Import Course Data') . '</form><p>';

  }

	
 ?>
    
 
</div> 