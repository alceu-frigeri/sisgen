
<?php $thisform=$GBLbasepage.'?q=edits&sq=rooms'; ?>
<div class="row">
    
        <h2>Salas</h2>
        <hr>


	
<?php 
	$can_room = $_SESSION['role']['isadmin'] || ($_SESSION['role'][$_POST['unitid']] && $_SESSION['role'][$_POST['unitid']]['can_room']);

	$GBLmysqli->postsanitize();

	$postedit = false;
	if ( (($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit')) & $can_room) {
		$postedit = true;
	} else {
		$_POST['act']='Cancel';
	}

	echo formpost($thisform);
	
	if (!($_SESSION['roomtype'])) {
		$q = "SELECT * FROM `roomtype`; ";
		$result = $GBLmysqli->dbquery($q);
		while ($sqlrow = $result->fetch_assoc()) {
			$_SESSION['roomtype'][$sqlrow['id']] = $sqlrow['name'];
		}
	}
	
	
	switch($_POST['act']) {
		case 'Insert':
		break;
		case 'Delete':
			if ($_POST['roomdelete']) {
				$q = "DELETE FROM `room` WHERE `id` = '" . $_POST['roomid'] . "';";
				$GBLmysqli->dbquery($q);
			}
		break;
		case 'Submit':
			$q = "UPDATE `room` SET `roomtype_id` = '" . $_POST['roomtype'] . "' , `capacity` = '" . $_POST['capacity']  .  "'  WHERE `id` = '" . $_POST['roomid'] . "'";
			$GBLmysqli->dbquery($q);
			$_POST['roomid'] = null;
		break;
		
	}

	if ($postedit & $can_room) {
		echo formhiddenval('buildingid',$_POST['buildingid']);
		echo displaysqlitem('','building',$_POST['buildingid'],'acronym','name');
		echo formsubmit('act','Cancel');
		echo '</form>';
	} else {
                formretainvalues(array('buildingid'));

		formselectsql($anytmp,"SELECT * FROM building WHERE `mark` = '1' ORDER BY acronym;",'buildingid',$_POST['buildingid'],'id','acronym');
	}
		
	?>
	
<br>



		
<?php


// course, term
  $q = "SELECT * FROM   `room` WHERE `building_id` = '".$_POST['buildingid']."' ORDER BY `name`;";

  $result=$GBLmysqli->dbquery($q);
  $anyone = 0;
  if ($postedit & $can_room) {
	  while ($sqlrow=$result->fetch_assoc()) {
	  	echo formpost($thisform);
		echo formhiddenval('buildingid',$_POST['buildingid']);
		if ($_POST['roomid'] == $sqlrow['id']) {
			echo formhiddenval('roomid',$sqlrow['id']);
			echo '&nbsp;&nbsp; ' . $sqlrow['name'];
			formselectsession('roomtype','roomtype',$sqlrow['roomtype_id']);
			echo '  Capacidade:  ' . formpatterninput(3,1,'[0-9]+','Capacidade','capacity',$sqlrow['capacity']);
			echo formsubmit('act','Submit');
		} else {
			echo formsubmit('act','Edit');
			echo formhiddenval('roomid',$sqlrow['id']);
			echo $sqlrow['name'].'&nbsp;&nbsp;'.$_SESSION['roomtype'][$sqlrow['roomtype_id']].'&nbsp;&nbsp;';
			if ($sqlrow['capacity']) {
				echo ' (cap.:'.$sqlrow['capacity'].')';
			}
			echo '<br>';			
		}
		echo '</form>';
	  }
	  echo '<br>';
 

  } else {
	  while ($sqlrow=$result->fetch_assoc()) {
		  $anyone = 1;
		  echo $sqlrow['name']. '&nbsp;&nbsp;'.$_SESSION['roomtype'][$sqlrow['roomtype_id']].'&nbsp;&nbsp;';
			if ($sqlrow['capacity']) {
				echo ' (cap.:'.$sqlrow['capacity'].')';
			}
			echo '<br>';			
	  }
  }


  if ($postedit & $can_room) {
  } else {
  	if ($anyone & $can_room) {
		echo formsubmit('act','Edit');
	}

  }

	echo '</form>';
	

 ?>

    </div>
  


