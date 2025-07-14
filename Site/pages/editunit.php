
<?php $thisform=$GBLbasepage.'?q=edits&sq=unit'; ?>
<div class="row">
    
        <h2>Departamentos/Cursos</h2>
        <hr>


	
<?php 
	$can_edit = $_SESSION['role']['isadmin'] || ($_SESSION['usercanedit']);

	$GBLmysqli->postsanitize();

	$postedit = false;
	if ((($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit')) & $can_edit) {
		$postedit = true;
	} else {
		$_POST['act']='Cancel';
	}

	echo formpost($thisform);
	
	switch($_POST['act']) {
		case 'Insert':
		break;
		case 'Delete':
		break;
		case 'Submit':
			$q = "UPDATE `unit` SET `contactname` = '" . $_POST['contactname'] . "' , `contactemail` = '" . $_POST['contactemail'] . "' , `contactphone` = '" . $_POST['contactphone']  .  "'  WHERE `id` = '" . $_POST['unitid'] . "'";
			$GBLmysqli->dbquery($q);
			$_POST['unitid'] = null;
		break;
		
	}
		
	?>
	
<br>



		
<?php


// course, term
  $q = "SELECT * FROM   `unit`  ORDER BY `iscourse` DESC, `name` ASC;";

	$result=$GBLmysqli->dbquery($q);
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
		if ($postedit & $can_edit) {
			echo formpost($thisform.targetdivkey('unit',$sqlrow['id']));
			if ($_POST['unitid'] == $sqlrow['id']) {
		//echo '<tr>';
		echo '<tr '.iddivkey('unit',$sqlrow['id']).'><td>&nbsp;<td></tr>';
		echo '<tr><td>&nbsp;</td></tr>';
		echo '<tr><td>&nbsp;</td></tr>';
		echo '<tr  '.$GBLhighlightstyle.'>';
		//echo hiddendivkey('unit',$sqlrow['id']); 
				echo '<td>' . formsubmit('act','Edit') . formhiddenval('unitid',$sqlrow['id']) . '&nbsp;&nbsp;&nbsp;</td>';
				echo '<td>' . $sqlrow['acronym'] . '&nbsp;&nbsp;&nbsp;</td><td>' . $sqlrow['name'] . '&nbsp;&nbsp;&nbsp;</td>';
				echo '<td>Contato:' . formpatterninput(64,20,$GBLnamepattern,'Contato','contactname',$sqlrow['contactname'])  .'&nbsp;&nbsp;&nbsp;</td>'.
				'<td>Email:'. formpatterninput(64,20,$GBLnamepattern,'Email','contactemail',$sqlrow['contactemail']) . '&nbsp;&nbsp;&nbsp;</td>' .
				' <td>Fone:'. formpatterninput(16,9,'[0-9\.]+','Telefone','contactphone',$sqlrow['contactphone']) . '&nbsp;&nbsp;&nbsp;' . '</td></tr><tr><td></td><td></td><td></td><td>' .formsubmit('act','Submit') . '</td>';
				
			} else {
		echo '<tr>';
				echo '<td>' . formsubmit('act','Edit') . formhiddenval('unitid',$sqlrow['id']) . '&nbsp;&nbsp;&nbsp;</td>';				
				echo '<td>' . $sqlrow['acronym'] . '&nbsp;&nbsp;&nbsp;</td><td>' . $sqlrow['name'] . '&nbsp;&nbsp;&nbsp;</td>';
				echo '<td>Contato:' . $sqlrow['contactname'] .'&nbsp;&nbsp;&nbsp;</td>'.
				'<td>Email:'. $sqlrow['contactemail'] . '&nbsp;&nbsp;&nbsp;</td>' .
				' <td>Fone:'. $sqlrow['contactphone'] . '&nbsp;&nbsp;&nbsp;</td><td></td>';
			}
		echo '</tr>';
			echo "</form>";
		} else {
		echo '<tr>';
				echo '<td>' .  '</td>';
				echo '<td>' . $sqlrow['acronym'] . '&nbsp;&nbsp;&nbsp;</td><td>' . $sqlrow['name'] . '&nbsp;&nbsp;&nbsp;</td>';
				echo '<td>Contato:' . $sqlrow['contactname'] .'&nbsp;&nbsp;&nbsp;</td>'.
				'<td>Email:'. $sqlrow['contactemail'] . '&nbsp;&nbsp;&nbsp;</td>' .
				' <td>Fone:'. $sqlrow['contactphone'] . '&nbsp;&nbsp;&nbsp;</td>';
		echo '</tr>';
		}
		//echo '</tr>';
  }
  echo '</table>';

	echo '</form>';
	

 ?>

    </div>
  


