
<?php 
	$thisform = $GBLbasepage . '?q=edits&sq=Disciplines'; 
	$can_discipline = $_SESSION['role']['isadmin'] | ($_SESSION['role'][$_POST['unitid']] & $_SESSION['role'][$_POST['unitid']]['can_disciplines']);
	
	$GBLmysqli->postsanitize();
	
	$postedit = false;
	if ( (($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit') | ($_POST['act'] == 'Delete') | ($_POST['act'] == 'Insert')) & $can_discipline) {
		$postedit = true;
	} else {
		$_POST['act'] = 'Cancel';
	}


?>
<div class = "row">
    
        <h2>Disciplinas</h2>
        <hr>



<?php 

	switch ($_POST['act']) {
		case 'Insert':
			$q = "INSERT INTO `discipline` (`dept_id` , `code` , `Lcred` , `Tcred` , `name` , `comment`) VALUES ('" . $_POST['unitid'] . "' , '" . $_POST['discdeptcode'] . $_POST['discsubcode'] . "' , '" . $_POST['discLcred'] . "' , '" . $_POST['discTcred'] . "' , '" . $_POST['discname'] . "' , '" . $_POST['disccomment'] . "');";
			$GBLmysqli->dbquery($q);
			$_POST['discid'] = null;
		break;
		case 'Submit':
			$q = "UPDATE `discipline` SET `dept_id` = '" . $_POST['unitid'] . "' , `code` = '" . $_POST['discdeptcode'] . $_POST['discsubcode'] . "' , `Lcred` = '" . $_POST['discLcred'] . "' , `Tcred` = '" . $_POST['discTcred'] . "' , `name` = '" . $_POST['discname'] . "' , `comment` = '" . $_POST['disccomment'] . "'  WHERE `id` = '" . $_POST['discid'] . "'";
			$GBLmysqli->dbquery($q);
			$_POST['discid'] = null;
		break;
		case 'Delete':
			if ($_POST['discdelete']) {
				$q = "DELETE FROM `discipline` WHERE `id` = '" . $_POST['discid'] . "';";
				$GBLmysqli->dbquery($q);
			}
		break;
	}
	

	echo formpost($thisform);
        $firstofmany = false;
	if ($postedit & $can_discipline) {
		echo formhiddenval('unitid' , $_POST['unitid']);
		echo formhiddenval('orderby' , $_POST['orderby']);
		echo displaysqlitem('' , 'unit' , $_POST['unitid'] , 'acronym' , 'name');
		echo formsubmit('act' , 'Cancel');
		echo '</form>';
	} else {
                formretainvalues(array('unitid' , 'orederby'));

		formselectsql($anytmp , "SELECT * FROM unit  ORDER BY unit . mark DESC , unit . iscourse ASC, unit . acronym ASC;" , 'unitid' , $_POST['unitid'] , 'id' , 'acronym');
		echo "Ordenado por:  ";
		formselectsession('orderby' , 'orderby' , $_POST['orderby'] , false , true);

                $firstofmany = true;

	}
			

// course, term
  if ($_POST['orderby'] == 0) {
	$ordby = 'discipline.name';
  } else {
	$ordby = 'discipline.code';
  }
  $q = "SELECT * FROM   `discipline` WHERE `dept_id` = '" . $_POST['unitid'] . "' ORDER BY " . $ordby;

  $result = $GBLmysqli->dbquery($q);
  $anyone = 0;
  if ($postedit & $can_discipline) {
	  while ($sqlrow = $result->fetch_assoc()) {
	  	echo formpost($thisform . targetdivkey('disc' , $sqlrow['id']));
		echo formhiddenval('unitid' , $_POST['unitid']);
		if ($_POST['discid'] == $sqlrow['id']) {
			echo hiddendivkey('disc' , $sqlrow['id']);
			highlightbegin();
			echo formhiddenval('discid' , $sqlrow['id']);
			$discdeptcode = substr($sqlrow['code'] , 0 , 5);
			echo formhiddenval('discdeptcode' , $discdeptcode);
			$discsubcode =  substr($sqlrow['code'] , 5 , 3);
			echo $discdeptcode;
			echo formpatterninput(3 , 1 , '[0-9][0-9][0-9]' , '3 digitos' , 'discsubcode' , $discsubcode)  . 
			 $GBL_Tspc . 'T: '  . 
			 formpatterninput(1 , 1 , '[0-8]' , 'single digit' , 'discTcred' , $sqlrow['Tcred'])  . 
			 $GBL_Dspc . 'L: '  . 
			 formpatterninput(1 , 1 , '[0-8]' , 'single digit' , 'discLcred' , $sqlrow['Lcred'])  . 
			 $GBL_Dspc . ' '  . 
			 formpatterninput(120 , 64 , $GBLdiscpattern , 'Nome da disciplina' , 'discname' , $sqlrow['name'])  . 
			 '<br>' . $GBL_Dspc . ' Obs . :' . formpatterninput(48 , 16 , $GBLcommentpattern , 'Comentário qq' , 'disccomment' , $sqlrow['comment']);
			echo formsubmit('act' , 'Submit');
			echo '</form>';
			highlightend();
			echo formpost($thisform) . formhiddenval('unitid' , $_POST['unitid']) . formhiddenval('discid' , $sqlrow['id'])  . 
			 spanformat('' , 'red' , '  ' . $GBL_Tspc . 'remover: ' , '' , true);
			formselectsession('discdelete' , 'bool' , 0);
			echo spanformat('' , 'red' , formsubmit('act' , 'Delete') , '' , true);
			
		} else {
			echo formsubmit('act' , 'Edit');
			echo formhiddenval('discid' , $sqlrow['id']);
			echo formhiddenval('orderby' , $_POST['orderby']);
			$discdeptcode = substr($sqlrow['code'] , 0 , 5);
			echo $sqlrow['code'] . $GBL_Tspc . 'T: ' . $sqlrow['Tcred'] . $GBL_Dspc . 'L: ' . $sqlrow['Lcred'] . $GBL_Dspc . ' ' . $sqlrow['name'];
			if ($sqlrow['comment']) {
				echo $GBL_Tspc . '' . spanformat('smaller' , $GBLcommentcolor , '(' . $sqlrow['comment'] . ')') ;
			}
			echo '<br>';
		}
		echo '</form>';
	  }
	  echo '<br>';
	  echo '<i>inserção</i>';
	  echo formpost($thisform);
	  echo formhiddenval('unitid' , $_POST['unitid']);
	  echo formhiddenval('discdeptcode' , $discdeptcode);
	  echo formhiddenval('orderby' , $_POST['orderby']);
	  $discsubcode =  '-';
	  echo $discdeptcode;
	  echo formpatterninput(3 , 1 , '[0-9][0-9][0-9]' , '3 digitos' , 'discsubcode' , $discsubcode)  . 
		 $GBL_Tspc . 'T: '  . 
		 formpatterninput(1 , 1 , '[0-8]' , 'single digit' , 'discTcred' , 0)  . 
		 $GBL_Dspc . 'L: '  . 
		 formpatterninput(1 , 1 , '[0-8]' , 'single digit' , 'discLcred' , 0)  . 
		 $GBL_Dspc . ' '  . 
		 formpatterninput(120 , 64 , $GBLdiscpattern , 'Nome da disciplina' , 'discname' , '!')  . 
		 $GBL_Dspc . ' Obs.:' . formpatterninput(48 , 16 , $GBLcommentpattern , 'Comentário qq' , 'disccomment' , $sqlrow['comment'])  . 
		 '  ' . $GBL_Tspc . ' '  . 
		 formsubmit('act' , 'Insert')  . 
		 '</form>';
	  

  } else {
	  while ($sqlrow = $result->fetch_assoc()) {
		  $anyone = 1;
                if ($firstofmany) {
                        $firstofmany = false;
                        echo formsubmit('act' , 'Edit') .  '</form><br>';
                }
		  echo $sqlrow['code'] . $GBL_Tspc . 'T: ' . $sqlrow['Tcred'] . $GBL_Dspc . 'L: ' . $sqlrow['Lcred'] . $GBL_Dspc . ' ' . $sqlrow['name'];
		  if ($sqlrow['comment']) {
			  echo $GBL_Tspc . '' . spanformat('smaller' , $GBLcommentcolor , $sqlrow['comment']);
		  }
		  echo '<br>';
	  }
  }

	

 ?>

    </div>
  


