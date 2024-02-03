
<?php $thisform=$basepage.'?q=edits&sq=unit'; ?>
<div class="row">
    
        <h2>Departamentos/Cursos</h2>
        <hr>


	
<?php 
	$can_edit = $_SESSION['role']['isadmin'] || ($_SESSION['usercanedit']);
	$postedit = (($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit'));

	$mysqli->postsanitize();

	echo formpost($thisform);
	
	
	switch($_POST['act']) {
		case 'Insert':
		break;
		case 'Delete':
		break;
		case 'Submit':
			$q = "UPDATE `unit` SET `contactname` = '" . $_POST['contactname'] . "' , `contactemail` = '" . $_POST['contactemail'] . "' , `contactphone` = '" . $_POST['contactphone']  .  "'  WHERE `id` = '" . $_POST['unitid'] . "'";
			$mysqli->dbquery($q);
			$_POST['unitid'] = null;
		break;
		
	}
		
	?>
	
<br>



		
<?php


// course, term
  $q = "SELECT * FROM   `unit`  ORDER BY `iscourse` DESC, `name` ASC;";

	$result=$mysqli->dbquery($q);
	$anyone = 0;
	
  if ($postedit & $can_edit) {
	echo formsubmit('act','Cancel');
  } else {
  	if ($can_edit) {
		echo formsubmit('act','Edit');
	}
  }

	echo '<table>';
	while ($sqlrow=$result->fetch_assoc()) {
		echo '<tr>';
		if ($postedit & $can_edit) {
			echo formpost($thisform);
			if ($_POST['unitid'] == $sqlrow['id']) {
				echo '<td>' . formsubmit('act','Edit') . formhiddenval('unitid',$sqlrow['id']) . '&nbsp;&nbsp;&nbsp</td>';
				echo '<td>' . $sqlrow['acronym'] . '&nbsp;&nbsp;&nbsp</td><td>' . $sqlrow['name'] . '&nbsp;&nbsp;&nbsp</td>';
				echo '<td>Contato:' . formpatterninput(64,20,$namepattern,'Contato','contactname',$sqlrow['contactname'])  .'&nbsp;&nbsp;&nbsp</td>'.
				'<td>Email:'. formpatterninput(64,20,$namepattern,'Email','contactemail',$sqlrow['contactemail']) . '&nbsp;&nbsp;&nbsp</td>' .
				' <td>Fone:'. formpatterninput(16,9,'[0-9\.]+','Telefone','contactphone',$sqlrow['contactphone']) . '&nbsp;&nbsp;&nbsp' .formsubmit('act','Submit') . '</td>';
				
			} else {
				echo '<td>' . formsubmit('act','Edit') . formhiddenval('unitid',$sqlrow['id']) . '&nbsp;&nbsp;&nbsp</td>';				
				echo '<td>' . $sqlrow['acronym'] . '&nbsp;&nbsp;&nbsp</td><td>' . $sqlrow['name'] . '&nbsp;&nbsp;&nbsp</td>';
				echo '<td>Contato:' . $sqlrow['contactname'] .'&nbsp;&nbsp;&nbsp</td>'.
				'<td>Email:'. $sqlrow['contactemail'] . '&nbsp;&nbsp;&nbsp</td>' .
				' <td>Fone:'. $sqlrow['contactphone'] . '&nbsp;&nbsp;&nbsp</td>';
			}
			echo "</form>";
		} else {
				echo '<td>' .  '</td>';
				echo '<td>' . $sqlrow['acronym'] . '&nbsp;&nbsp;&nbsp</td><td>' . $sqlrow['name'] . '&nbsp;&nbsp;&nbsp</td>';
				echo '<td>Contato:' . $sqlrow['contactname'] .'&nbsp;&nbsp;&nbsp;</td>'.
				'<td>Email:'. $sqlrow['contactemail'] . '&nbsp;&nbsp;&nbsp</td>' .
				' <td>Fone:'. $sqlrow['contactphone'] . '&nbsp;&nbsp;&nbsp</td>';
		}
		echo '</tr>';
  }
  echo '</table>';

	echo '</form>';
	

 ?>

    </div>
  


