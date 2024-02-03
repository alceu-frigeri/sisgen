

<div class="row">
        <h2>Relatório Depto. p/ Prof.</h2>
        <hr>

<?php 
	$thisform=$basepage.'?q=reports&sq=assignment'; 

	$mysqli->postsanitize();


	formjavaprint(displaysqlitem('','unit',$_POST['deptid'],'acronym') . displaysqlitem(' - Encargos ','semester',$_POST['semid'],'name'));
	
	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY semester.name;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM unit WHERE isdept = 1 AND mark = 1 ORDER BY unit.name;",'deptid',$_POST['deptid'],'id','acronym');
	echo formsubmit('act','Refresh') . '<br>';
	
	formselectscenery('scen.acc.view');	
	echo '</form>';

	echo "<button onclick=\"printContent('Encargos')\">Print</button>";

//	$in = "'0'";
//	foreach ($_SESSION['sceneryselected'] as $scenid => $scenname) {
//		$in .= " , '".$scenid."'";
//	}
//	list($qscentbl,$qscensql) = scenery_sql($in);

	$inselected = inscenery_sessionlst('sceneryselected');
	list($qscentbl,$qscensql) = scenery_sql($inselected);


	echo '<hr><div id="Encargos">';
	
	echo '<h2>' . displaysqlitem('','unit',$_POST['deptid'],'acronym') . displaysqlitem(' - Encargos p/ ','semester',$_POST['semid'],'name') . '</h2>';

	$q = "SELECT prof.* FROM prof,unit WHERE prof.dept_id = unit.id AND unit.id = '".$_POST['deptid']."' AND prof.profkind_id != '5' ORDER BY prof.name;";
	$profsql = $mysqli->dbquery($q);
	while ($profrow = $profsql->fetch_assoc()) {
	   echo '<br>';	
	   $q = "SELECT DISTINCT `discipline`.`name` AS `discname`, `discipline`.* ,  `discipline`.`id` AS `discid`, `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `unit`.`id` AS `discdeptid` " . 
		 " FROM `classsegment` , `class`, `semester`,`unit`,`discipline`,`prof` " . $qscentbl . " WHERE " . 
		 "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " .  
		 "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`prof_id` = `prof`.`id` AND " . 
		 "`unit`.`id` = '".$_POST['deptid']."' AND `semester`.`id` = '".$_POST['semid']."' AND " . 
		 "`prof`.`id` = '".$profrow['id'] . "' " . $qscensql . 
		 " ORDER BY `class`.`name`"
		 ;
		
	  echo   spanformat('larger','',$profrow['name'])  ;
	  $flag =  checkweek($q);	  
	  
	  dbweekmatrix($q,$inselected,null,null,false);

	}
	echo '</dvi>';
  

 ?>
    
 
</div>

