
<?php $thisform=$GBLbasepage.'?q=admin&sq=sem'; ?>

<div class="row">
    
        <h2>Edição de Semestres</h2>
        <hr>

<?php 


	$GBLmysqli->postsanitize();




	if($_SESSION['role']['isadmin']) {
		switch($_POST['act']) {
			case 'Submit':
				if (fieldscompare('',array('readonly'))) {
					$q = "UPDATE `semester` SET `readonly` = '".$_POST['readonly']."' WHERE `id` = '".$_POST['semid']."';";
					$GBLmysqli->dbquery($q);
				}
				break;
			case 'Delete':
				if($_POST['act'] == 'Delete') {
					if ($_POST['delete']) {
						$q = "DELETE FROM `semester` WHERE `id` = '".$_POST['semid']."';";
						$GBLmysqli->dbquery($q);
					}
				}
				break;
			case 'Duplicate as':
				duplicatesem($_POST['semid'],$_POST['newsem']);
			break;
		}

		$q = "SELECT * FROM `semester` ORDER BY `name`;";
		$semsql = $GBLmysqli->dbquery($q);
		while ($semrow = $semsql->fetch_assoc()) {
			echo formpost($thisform) . formhiddenval('semid',$semrow['id']);
			if (($_POST['semid'] == $semrow['id']) & (($_POST['act'] == 'Edit'))) {
				echo $semrow['name'] . '&nbsp;&nbsp;&nbsp;&nbsp;readonly:';
				formselectsession('readonly','bool',$semrow['readonly']);
				echo formsubmit('act','Submit');
				echo '  Delete? ';
				formselectsession('delete','bool',0);
				echo formsubmit('act','Delete');
				echo '</form>';
				echo formpost($thisform) . formhiddenval('semid',$semrow['id']);
				echo formpatterninput(10,5,'[0-9a-zA-Z \-]+','novo semestre','newsem','!');
				echo formsubmit('act','Duplicate as');
			} else {
				echo formsubmit('act','Edit') . $semrow['name'] . '&nbsp;&nbsp;&nbsp;&nbsp;';
				if ($semrow['readonly']) {
					echo '(readonly)';
				} else {
					echo '(read/write)';
				}		
			}
			echo '</form>';
		}
	}

 ?>

    </div>
  


