
<?php $thisform=$GBLbasepage.'?q=reports&sq=scenery'; ?>

<div class="row">
        <h2>Relatório p/ Cenário </h2>
        <hr>

<?php 
	$GBLmysqli->postsanitize();

	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY semester.name DESC;",'semid',$_POST['semid'],'id','name');
	echo " Todos ? ";
	formselectsession('allscenery','bool',$_POST['allscenery'],false,true);        
        if($_SESSION['role']['isadmin']) {
                formselectsql($anytmp,"SELECT DISTINCT scen.* FROM scenery scen ORDER BY name;",'sceneryid',$_POST['sceneryid'],'id','name');
        } else {
                if($_POST['allscenery']) {
                        $qallscen = "( scen.hide = '0') OR ";
                } else {
                        $qallscen = '';
                }
	       formselectsql($anytmp,"SELECT DISTINCT scen.* FROM scenery scen , sceneryrole scenrole,  accrole WHERE " . $qallscen . " ( scen.id = scenrole.scenery_id AND scenrole.role_id = accrole.role_id AND accrole.account_id = '" . $_SESSION['userid'] .  "' ) ORDER BY name;",'sceneryid',$_POST['sceneryid'],'id','name');
        }

//	echo "Nome Profs ? ";
//	formselectsession('profnicks','bool',$_POST['profnicks'],false,true);
	echo  '<br>';

	echo '</form>';
   

  //vardebug($_POST);
  if (($_POST['semid'] != 0 )& ($_POST['sceneryid'] != 0 )) {
        echo '<h3>' . $_SESSION['scen.all'][$_POST['sceneryid']] . ' ( ' . $_SESSION['scen.desc'][$_POST['sceneryid']] . ' ) </h3>';
        

	$q="SELECT DISTINCT disc.* FROM discipline disc , sceneryclass scenclass , class  WHERE class.sem_id = '" . $_POST['semid'] . "' AND class.discipline_id = disc.id AND class.id = scenclass.class_id AND scenclass.scenery_id = '" . $_POST['sceneryid']  .  "' ORDER BY  `name`;";

        dbweekmatrix($q);


	$discsql = $GBLmysqli->dbquery($q);
	
	echo '<div id="Turmas">';
	
	$anyone = false;
	while ($discrow = $discsql->fetch_assoc()) {
		echo '<br><b>'. spanformat('','darkblue',$discrow['code'].' -- '.$discrow['name']) .'</b><br>';
                $anyone = true;

		$q = "SELECT DISTINCT class.*  FROM  `class` ,  `sceneryclass` scenclass WHERE `class`.`discipline_id` = '" . $discrow['id'] . "' AND " .  
		"`class`.`sem_id` = '" . $_POST['semid'] . "' AND " . 
		"`class`.`id` = scenclass . class_id  AND " . 
		"`scenclass`.`scenery_id` = '" . $_POST['sceneryid'] . "' " .
		 " ORDER BY `name`;" ;
		 
		$classsql = $GBLmysqli->dbquery($q);
		while($classrow = $classsql->fetch_assoc()) {
			 echo 'Turma: ' . $classrow['name'];
			 if ($classrow['agreg']) {
				 echo spanformat('','darkorange',' (agregadora)');
			 } else {
				 if($classrow['partof']) {
					 $q="SELECT `name` FROM `class` WHERE `id` = '".$classrow['partof']."'";
					 $partsql=$GBLmysqli->dbquery($q);
					 $partrow=$partsql->fetch_assoc();
					 echo spanformat('','darkorange',' (agregada à '.$partrow['name'].')');
				 }
			 }
			 
			 echo '<br>';
			 $q = "SELECT `seg`.* , `building`.`acronym` AS `buildingname` , `room`.`acronym` AS `roomname` , `room`.`capacity` AS `capacity` , `prof`.`nickname` , `prof`.`name`  FROM `classsegment` AS `seg` , `room` , `building`, `prof` WHERE " .
				"`seg`.`room_id` = `room`.`id` AND `room`.`building_id` = `building`.`id`  AND `seg`.`prof_id` = `prof`.`id` AND  `seg`.`class_id` = '" . $classrow['id'] . "';";
			 $segsql = $GBLmysqli->dbquery($q);
			 while ($segrow = $segsql->fetch_assoc()) {
				 if ($segrow['length']>1) { $p='s'; } else { $p=''; };
				 echo '&nbsp;&nbsp;&nbsp;' . 
					spanformat('','gray',$_SESSION['weekday'][$segrow['day']] . ' -- ' . $segrow['start'] . ':30 ' . $segrow['length'] . ' Hora'.$p.'-Aula') . 
					', ' . $segrow['name'] . ',  ' . 
					spanformat('','gray','Sala: ' . $segrow['roomname'] . ' (' . $segrow['buildingname'] . ')');
					if ($segrow['capacity']) {
						echo ' (cap.: ' . $segrow['capacity'] . ')';
					}
					echo '<br>'; 
			 }
			 $q="SELECT `vac`.* , `unit`.`acronym`, `kind`.`code` AS `disckind` FROM `vacancies` AS `vac`,`unit` , `coursedisciplines` AS `grade` , `disciplinekind` AS `kind` WHERE " .
				"`vac`.`course_id` = `unit`.`id` AND `vac`.`course_id` = `grade`.`course_id` AND `grade`.`disciplinekind_id` = `kind`.`id` AND " .
				"`grade`.`discipline_id` = '" . $discrow['id'] . "' AND `vac`.`class_id` = '" . $classrow['id'] . "' ORDER BY `unit`.`acronym`";
			 $vacsql = $GBLmysqli->dbquery($q);
			 while ($vacrow = $vacsql->fetch_assoc()) {
				 if ($vacrow['givennum']==1) { $p=''; } else { $p='s'; };			 
				 echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . $vacrow['acronym'] . ' : ' . $vacrow['givennum'] . ' Vaga'.$p. ' ('.$vacrow['disckind'] .')<br>';
			 }
			 
		}
	}
        if (!$anyone) {
                echo '<b>'. spanformat('','gray',$GBLDspc.' -none- ') .'</b><br>';
        }
	echo '</div>';
}
 ?>
    
 
</div> 