<div class="row">
    
        <h2>Relatório p/Curso e Etapa </h2>
        <hr>
<?php
	$GBLmysqli->postsanitize();

	echo formpost($thisform);
	formselectsql($anytmp,"SELECT * FROM semester ORDER BY name DESC;",'semid',$_POST['semid'],'id','name');
	formselectsql($anytmp,"SELECT * FROM unit WHERE iscourse = 1 ORDER BY unit.name;",'courseid',$_POST['courseid'],'id','acronym');
	formselectsql($anytmp,"SELECT * FROM term ORDER BY term.name;",'termid',$_POST['termid'],'id','name');
	echo " Somente OB/AL ? ";
	formselectsession('reqonly','bool',$_POST['reqonly'],false,true);
	echo " Nome Profs ? ";
	formselectsession('profnicks','bool',$_POST['profnicks'],false,true);
	echo  '<br>';	
	formselectscenery('scen.acc.view',formsubmit('act','Refresh')); 
	echo '</form>';
	
// semester, course, term
	if ($_POST['termid']) {
		echo '<p>';
//		$in = "'0'";
//		foreach ($_SESSION['sceneryselected'] as $scenid => $scenname) {
//			$in .= " , '".$scenid."'";
//		}

	$inselected = inscenery_sessionlst('sceneryselected');
	list($qscentbl,$qscensql) = scenery_sql($inselected);

	if($_POST['reqonly']) {
		$qextra = " AND ( `coursedisciplines`.`disciplinekind_id` = '1' OR `coursedisciplines`.`disciplinekind_id` = '3' )";
	} else {
		$qextra='';
	}

	if($_POST['profnicks']) {
		$qnicks = " , `prof`.`nickname` AS `profnick`, `prof`.`id` AS `profid`, `prof`.`dept_id` AS `profdeptid`  ";
	} else {
		$qnicks='';
	}

	  $q = "SELECT DISTINCT `discipline`.`name` AS `discname` ,  `discipline`.`id` AS `discid` , `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `discdept`.`id` AS `discdeptid`, `unit`.`id` AS `courseid` " . $qnicks  . 
	    "FROM  `classsegment` , `class`, `term`, `semester` , `coursedisciplines`,`unit`,`discipline` , `unit` AS `discdept` , `prof` " . $qscentbl . " WHERE " .
		"`coursedisciplines`.`course_id` = `unit`.`id` AND `coursedisciplines`.`term_id` = `term`.`id` AND `coursedisciplines`.`discipline_id` = `discipline`.`id` AND " .
		"`discipline`.`dept_id` = `discdept`.`id` AND `classsegment`.`prof_id` = `prof`.`id` AND " . 
		"`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " .
		"`classsegment`.`class_id` = `class`.`id` AND " .
		"`unit`.`id` = '".$_POST['courseid']."' AND `term`.`id` = '".$_POST['termid']."' AND " .
		"`semester`.`id` = '".$_POST['semid']."' " . $qscensql . $qextra . " ORDER BY `discipline`.`name` , `class`.`name`";

	   dbweekmatrix($q,$inselected,$_POST['courseid'],$_POST['termid']);
    }

 ?>

    </div>
  


