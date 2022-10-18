
<?php $thisform=$basepage.'?q=admin&sq=courses'; ?>

<div class="row">
        <h2>Edição Cursos</h2>
        <hr>
		<br>

<?php 

	if ($_SESSION['role']['isadmin']) {
		switch($_POST['act']) {
			case 'Submit':
				$q = "UPDATE `unit` SET `acronym` = '" . $_POST['acronym'] . "' , `code` = '" . $_POST['code'] . "' , `name` = '" . $_POST['name']   .  "'  WHERE `id` = '" . $_POST['courseid'] . "'";
				$mysqli->dbquery($q);
				break;
			case 'Delete':
				if ($_POST['coursedelete']) {
					$q = "DELETE FROM `unit` WHERE `id` = '" . $_POST['courseid'] . "';";
					$mysqli->dbquery($q);
				}
				break;
			case 'Duplicate as':
				$q = "INSERT INTO `unit` (`acronym`,`code`,`name`,`iscourse`,`isdept`) VALUES ('".$_POST['newacronym']."','".$_POST['newcode']."','".$_POST['newname']."','1','1')";
				$result = $mysqli->dbquery($q);
				$newid = $mysqli->insert_id;
				$q = "INSERT INTO `coursedisciplines` (`course_id`,`term_id`,`discipline_id`,`disciplinekind_id`) SELECT '".$newid."' , `cd`.`term_id` , `cd`.`discipline_id`  , `cd`.`disciplinekind_id` FROM `coursedisciplines` AS `cd` WHERE `course_id` = '".$_POST['courseid']."'";
				$mysqli->dbquery($q);
			break;
		}

	// course, term
		$q = "SELECT * FROM   `unit` WHERE `iscourse` = '1' ORDER BY `acronym`;";

		$result=$mysqli->dbquery($q);
		$any = 0;
		while ($sqlrow=$result->fetch_assoc()) {
		  $any = 1;
		  echo formpost($thisform);
		  echo formhiddenval('courseid',$sqlrow['id']);
		  if(($_POST['courseid'] == $sqlrow['id']) & (($_POST['act'] == 'Edit'))) {
			  echo formpatterninput(10,3,'[A-Z]+','acronym','acronym',$sqlrow['acronym']) .
			  formpatterninput(5,5,'[A-Z][A-Z][A-Z][0-9][0-9]','code, e.g. CCA99','code',$sqlrow['code']) .
			  formpatterninput(32,16,$namepattern,'nome','name',$sqlrow['name']);
			  echo formsubmit('act','Submit');
			  echo '&nbsp;&nbsp;&nbsp;Deletar:';
			  formselectsessionX('coursedelete','bool',0);
			  echo formsubmit('act','Delete') . '<br>';
			  echo '</form>';
			  echo formpost($thisform);
			  echo formhiddenval('courseid',$sqlrow['id']);
			  echo formpatterninput(10,3,'[A-Z]+','acronym','newacronym','!') .
			  formpatterninput(5,5,'[A-Z][A-Z][A-Z][0-9][0-9]','code, e.g. CCA99','newcode','!') .
			  formpatterninput(32,16,$namepattern,'nome','newname','!');
			  echo formsubmit('act','Duplicate as');
			  echo "</form>";
		  } else {
			echo formsubmit('act','Edit');
			echo $sqlrow['acronym']. "&nbsp;&nbsp;".$sqlrow['code']."&nbsp;&nbsp; ".$sqlrow['name']."<br>";
			echo "</form>";
		  }
		}

	}
 
	

 ?>

    </div>
  
