
<?php $thisform=$GBLbasepage.'?q=reports&sq=comgrad'; ?>

<div class="row">
        <h2>Relatório COMGRAD por Departamento </h2>
        <hr>

<?php
	$GBLmysqli->postsanitize();

	echo formpost($thisform);
        formretainvalues(array('semid','courseid','deptid'));

	formselectsql($anytmp,"SELECT * FROM semester ORDER BY semester.name DESC;",'semid',$_POST['semid'],'id','name');
	
	echo 'Curso:'; 
	formselectsql($anytmp,"SELECT * FROM unit WHERE `iscourse` = '1' ORDER BY unit.acronym;",'courseid',$_POST['courseid'],'id','acronym');
	
    $q = "SELECT DISTINCT `disc`.`dept_id` , `unit`.`acronym` , `unit`.`name`  FROM `coursedisciplines` AS `grade` , `discipline` AS `disc` , `unit` " . 
	"WHERE `grade`.`discipline_id` = `disc`.`id` AND `disc`.`dept_id` = `unit`.`id` AND " .
	"`grade`.`course_id` = '". $_POST['courseid']."' ORDER BY `unit`.`acronym`";

    echo '&nbsp;&nbsp;&nbsp;Dept.:';
	formselectsql($anytmp,$q,'deptid',$_POST['deptid'],'dept_id','acronym');
	
	echo  '<br>';
	
	formsceneryselect();
	echo '</form>';


	$inselected = inscenery_sessionlst('sceneryselected');
	list($qscentbl,$qscensql) = scenery_sql($inselected);


	    $q = "SELECT acronym,name FROM unit WHERE id='".$_POST['deptid']."'";
		$result=$GBLmysqli->dbquery($q);
		$sqlrow=$result->fetch_assoc();
		$emailbody = '';
		$emailbodyhdr = '';
		if($sqlrow['acronym']) {
			$temp = '<h4>'.$sqlrow['acronym'] . ' -- ' . $sqlrow['name'] . '</h4>';
			//echo $temp; 
			$emailbodyhdr .= $temp;
		}
		$q = "SELECT * FROM `term` ORDER BY `id`";
		$termsql = $GBLmysqli->dbquery($q);
		while ($termrow = $termsql->fetch_assoc()) {
			$q = "SELECT discipline.* FROM coursedisciplines , discipline WHERE coursedisciplines.course_id = '".$_POST['courseid']."' AND coursedisciplines.discipline_id = discipline.id AND discipline.dept_id = '".$_POST['deptid']."' AND `coursedisciplines`.`term_id` = '".$termrow['id']."' ORDER BY  discipline.name ";
			$discsql = $GBLmysqli->dbquery($q);
			if($discsql->num_rows) {
				$temp = '<hr><b>'.$termrow['name'].'</b><br>';
				//echo $temp; 
				$emailbody .= $temp;
			}
			while ($discrow = $discsql->fetch_assoc()) {
				$flag = 0;
				$temp = '<br><b>'. spanformat('','darkblue',$discrow['code'].' -- '.$discrow['name']) .'</b><br>';
				//echo $temp; 
				$emailbody .= $temp;

				$q = "SELECT DISTINCT class.* , (`vac`.`askednum` + `vac`.`askedreservnum` ) as `askednum`  , `vac`.`askedreservnum` FROM  `class` , `vacancies` AS `vac` " . $qscentbl . " WHERE `class`.`discipline_id` = '" . $discrow['id'] . "' AND " .
				"`class`.`sem_id` = '" . $_POST['semid'] . "' AND `vac`.`class_id` = `class`.`id` AND (`vac`.`askednum` > '0' OR `vac`.`askedreservnum` > '0') AND `vac`.`course_id` = '" . $_POST['courseid'] . "' " . 
				$qscensql . " ORDER BY `class`.`name`"	 ;
				
				$classsql = $GBLmysqli->dbquery($q);
				while($classrow = $classsql->fetch_assoc()) {
					if ($classrow['askednum']>1) { $p='s'; } else { $p=''; };
					 $flag = 1;
					 $temp = 'Turma: ' . $classrow['name'] . ' ('. $classrow['askednum'] . ' vaga'.$p.')';
					 //echo $temp; 
					 $emailbody .= $temp;
                                        if ($classrow['askedreservnum'] > 0) {
                                                $temp = ' das quais ' . $classrow['askedreservnum'] . ' serão para calouros.';
                                                $emailbody .= $temp;
                                        }
					 if ($classrow['agreg']) {
						 $temp = spanformat('','darkorange',' (agregadora)');
						 //echo $temp; 
						 $emailbody .= $temp;
					 } else {
						 if($classrow['partof']) {
							 $q="SELECT `name` FROM `class` WHERE `id` = '".$classrow['partof']."'";
							 $partsql=$GBLmysqli->dbquery($q);
							 $partrow=$partsql->fetch_assoc();
							 $temp = spanformat('','darkorange',' (agregada à '.$partrow['name'].')');
							 //echo $temp; 
							 $emailbody .= $temp;
						 }
					 }					 
					 $temp = '<br>';
					 //echo $temp; 
					 $emailbody .= $temp;
					 $q = "SELECT * FROM `classsegment` AS `seg` WHERE `seg`.`class_id` = '" . $classrow['id'] . "';";
					 $segsql = $GBLmysqli->dbquery($q);
					 while ($segrow = $segsql->fetch_assoc()) {
						 if ($segrow['length']>1) { $p='s'; } else { $p=''; };
						 $temp = '&nbsp;&nbsp;&nbsp;' . spanformat('','gray',$_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 ' . $segrow['length'] . ' Hora'.$p.'-Aula<br>'); 
						 //echo $temp; 
						 $emailbody .= $temp;
					 }
				}
				if(!$flag) {
					$temp = '&nbsp;&nbsp;&nbsp;' . spanformat('','gray',' -- sem demandas --<br>'); 
					//echo $temp; 
					$emailbody .= $temp;
				}
			}
			
		}
		if ($emailbody) {
		$q = "SELECT * FROM `unit`  WHERE `id` = '" . $_POST['courseid'] . "';";
			$coursesql = $GBLmysqli->dbquery($q);
			$courserow = $coursesql->fetch_assoc();

			$q = "SELECT * FROM `unit`  WHERE `id` = '" . $_POST['deptid'] . "';";
			$deptsql = $GBLmysqli->dbquery($q);
			$deptrow = $deptsql->fetch_assoc();

			$q = "SELECT * FROM `semester`  WHERE `id` = '" . $_POST['semid'] . "';";
			$semsql = $GBLmysqli->dbquery($q);
			$semrow = $semsql->fetch_assoc();
			
			if (($_POST['act'] == 'Send Email') & ($_POST['trulysend'])) {
				myhtmlmail($_POST['emailfrom'],$_POST['emailto'],$_POST['emailsubject'],str_replace('\r\n','<br>',$_POST['emailtext']).'<p><hr><h4>Ao</h4>'.$emailbodyhdr.'<h5>Necessidades de Vagas p/ o Curso em '.$courserow['name'].'</h5>'.$emailbody);
			}
	
			echo formpost($thisform);	
			echo formhiddenval('semid',$_POST['semid']);
			echo formhiddenval('courseid',$_POST['courseid']);
			echo formhiddenval('deptid',$_POST['deptid']);
			echo '<hr>';
			echo '&nbsp;from:' . formpatterninput(64,16,'[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+','from:','emailfrom',$courserow['contactemail']).'<br>';
			echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;to:' . formpatterninput(64,16,'[a-zA-Z0-9\._-]+@[a-zA-Z0-9\._-]+','to:','emailto',$deptrow['contactemail']).'<br>';
			echo 'subject:' . formpatterninput(128,64,'[a-zA-Z0-9\._- ]+','subject:','emailsubject','Demandas COMGRAD/'.$courserow['acronym'].' para o semestre '.$semrow['name'].' ('.$deptrow['acronym'].')').'<br>';
			echo 'body:<textarea name="emailtext" rows="10" cols="64"> Prezado(a) ' . $deptrow['contactname'].",\n Seguem abaixo as nossas necessidades de turmas/vagas para o Semestre " . 
					$semrow['name'] . '.' .
					"\n\nColocamo-nos, desde já, a disposição para sanar quaisquer dúvidas." .
					"\n\nAtenciosamente, \n". $courserow['contactname'] . "\nCOMGRAD/" .$courserow['acronym'] ."\n". $courserow['name'] . "\n\n</textarea><br>";
			echo 'Really Send it:'; 
			formselectsession('trulysend','bool',0);
			echo formsubmit('act','Send Email');
			echo '</form>';
			echo '<p><hr>'.$emailbodyhdr.$emailbody; 
			//$scapedbody = htmlspecialchars($emailbody);
			// $scapedbody = htmlspecialchars('thats a " b test');
			//echo "<a href='mailto:" . 'email@test.br' . '?body=' . $scapedbody . "'> EMAIL </a>";
//			mymailX('comgrad_cca@ufrgs.br','alceu.frigeri@ufrgs.br','test report email',$emailbody);
		}
		
		
 ?>
    
 
</div> 