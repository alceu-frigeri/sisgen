
<?php $thisform=$GBLbasepage.'?q=reports&sq=scenery'; ?>

<div class="row">
        <h2>Relatório p/ Cenário </h2>
        <hr>

<?php 
	$GBLmysqli->postsanitize();
        formretainvalues(array('semid','allscenery','sceneryid'));
        
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

	echo "Nome Profs ? ";
	formselectsession('profnicks','bool',$_POST['profnicks'],false,true);
	echo  '<br>';

	echo '</form>';
   

  //vardebug($_POST);
  if (($_POST['semid'] != 0 )& ($_POST['sceneryid'] != 0 )) {
        echo '<h3>' . $_SESSION['scen.all'][$_POST['sceneryid']] . ' ( ' . $_SESSION['scen.desc'][$_POST['sceneryid']] . ' ) </h3>';
        

	if($_POST['profnicks']) {
		$qnicks = " , `prof`.`nickname` AS `profnick`, `prof`.`id` AS `profid`, `prof`.`dept_id` AS `profdeptid`  ";
	} else {
		$qnicks='';
	}
$inselected = $_POST['sceneryid'];

	   $q = "SELECT DISTINCT `discipline`.`name` AS `discname` ,  `discipline`.`id` AS `discid` , `discipline`.* , `class`.`id` AS `classid` , `class`.* , `classsegment`.* , `discdept`.`id` AS `discdeptid`" . $qnicks .
		 " FROM `classsegment` , `class`, `semester`,`discipline`,`prof` , `unit` AS `discdept`,  sceneryclass WHERE " . 
		 "`class`.`discipline_id` = `discipline`.`id` AND `class`.`sem_id` = `semester`.`id` AND " . 
		 "`classsegment`.`class_id` = `class`.`id` AND `classsegment`.`prof_id` = `prof`.`id` AND " .
		 "`discipline`.`dept_id` = `discdept`.`id` AND " .
		 " `semester`.`id` = '" . $_POST['semid'] . "' AND " . 
                 " `class`.`scenery` = '1' AND `sceneryclass`.`class_id` = `class`.`id` AND `sceneryclass`.`scenery_id` = '" . $_POST['sceneryid'] . "'" .
                 " ORDER BY `discipline`.`name` , `class`.`name`";

	   dbweekmatrix($q,$inselected);
}
 ?>
    
 
</div> 