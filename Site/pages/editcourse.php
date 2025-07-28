  
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $GBLbasepage . '?q=edits&sq=Course';
formretainvalues(array('courseid' , 'termid' , 'orederby'));
  

$can_coursedisciplines = ($_SESSION['role']['isadmin'] | ($_SESSION['role'][$_POST['courseid']] & $_SESSION['role'][$_POST['courseid']]['can_coursedisciplines']));
  
  
$postedit = false;
if ((($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit') | ($_POST['act'] == 'Delete') | ($_POST['act'] == 'Insert') | ($_POST['act'] == 'Reload')) & $can_coursedisciplines) {
    $postedit = true;
} else {
    $_POST['act'] = 'Cancel';
}

echo '<div class = "row">' .    
    '<h2>Grades Curriculares</h2>' .
    '<hr>' ;

echo formpost($thisform);
  
if(!($_SESSION['disckind'])) {
    $q = "SELECT * FROM `disciplinekind`";
    $result = $GBLmysqli->dbquery($q);
    while ($sqlrow = $result->fetch_assoc()) {
        $_SESSION['disckind'][$sqlrow['id']] = $sqlrow['code'];
    }
}
if(!($_SESSION['term'])) {
    $q = "SELECT * FROM `term`";
    $result = $GBLmysqli->dbquery($q);
    while ($sqlrow = $result->fetch_assoc()) {
        $_SESSION['term'][$sqlrow['id']] = $sqlrow['code'];
    }
}
  
switch ($_POST['act']) {
case 'Insert':
    if ($can_coursedisciplines) {
        $Query = 
                "INSERT INTO `coursedisciplines` (`course_id` , `term_id` , `discipline_id` , `disciplinekind_id`) " . 
                "VALUES ( '$_POST[courseid]'  ,  '$_POST[termid]'  ,  '$_POST[discid]'  ,  '$_POST[newdisckind]' ) ; " ;

        $GBLmysqli->dbquery( $Query );
        $_POST['coursediscid'] = $GBLmysqli->insert_id;
        $_POST['act'] = 'Submit';
    }
    break;
case 'Submit':
    if ($can_coursedisciplines) {
        $disckey = 'disc' . $_POST['coursediscid'] ;
        $compfields = array('newterm' , 'newkind');
        if(fieldscompare( $disckey , $compfields)) {
            foreach ($compfields as $field) {
                $keypost[$field] = $_POST[$disckey . $field] ;
            }
            $Query = 
                "UPDATE `coursedisciplines` " . 
                "SET `term_id` =  '$keypost[newterm]'  , " . 
                        "`disciplinekind_id` =  '$keypost[newkind]'  " . 
                "WHERE `id` =  '$_POST[coursediscid]' ; " ;

            $GBLmysqli->dbquery( $Query );
        }
        //$_POST['coursediscid'] = null;
    }
    break;
case 'Delete':
    if ($can_coursedisciplines & $_POST['discdelete']) {
        $q = "DELETE FROM `coursedisciplines` WHERE `id` =  '$_POST[coursediscid]' ;";
        $GBLmysqli->dbquery($q);
        $q = "DELETE FROM `vacancies` WHERE `vacancies` . `course_id` =  '$_POST[courseid]'  AND `vacancies` . `class_id` IN (SELECT `class` . `id` FROM `class` , `discipline` AS `disc` WHERE `class` . `discipline_id` = `disc` . `id` AND `disc` . `id` =  '$_POST[coursediscid]' );";
        $GBLmysqli->dbquery($q);
    }
    break;
}
  


if($postedit & $can_coursedisciplines) {
    echo formhiddenval('courseid' , $_POST['courseid']);
    echo displaysqlitem('' , 'unit' , $_POST['courseid'] , 'acronym');
    echo formhiddenval('termid' , $_POST['termid']);
    echo displaysqlitem('-- ' , 'term' , $_POST['termid'] , 'name');
    echo formhiddenval('orderby' , $_POST['orderby']);
    echo formsubmit('act' , 'Cancel');
    echo '</form>';
} else {
                
    echo formselectsql($anytmp , 
                  "SELECT * FROM unit WHERE iscourse = 1 ORDER BY unit . name;" , 
                  'courseid' , 
                  $_POST['courseid'] , 
                  'id' , 
                  'acronym');
    echo formselectsql($anytmp , 
                  "SELECT * FROM term ORDER BY term . name;" , 
                  'termid' , 
                  $_POST['termid'] , 
                  'id' , 
                  'name');
    echo "Ordenado por:  "; 
    echo formselectsession('orderby' , 'orderby' , $_POST['orderby'] , false , true);
}
  
echo '<br>'; 

if ($_POST['orderby'] == 0) {
  $ordby = 'discipline.name';
} else {
  $ordby = 'discipline.code';
}

// course, term
$Query = 
        "SELECT `discipline` . `code` , " . 
                "`discipline` . `name` , " . 
                "`disciplinekind` . `code` AS disckindcode , " . 
                "`disciplinekind` . `id` AS disckindid , " . 
                "`coursedisciplines` . `id` " . 
        "FROM   `term` , `coursedisciplines` , `discipline` , `disciplinekind` " . 
        "WHERE `coursedisciplines` . `course_id` =  '$_POST[courseid]'  " . 
                "AND `coursedisciplines` . `term_id` = `term` . `id` " . 
                "AND `coursedisciplines` . `discipline_id` = `discipline` . `id` " . 
                "AND `coursedisciplines` . `disciplinekind_id` = `disciplinekind` . `id` " . 
                "AND `term` . `id` =  '$_POST[termid]'  " . 
        "ORDER BY $ordby ; " ;
        
$result = $GBLmysqli->dbquery( $Query );
$anyone = 0;
if ($postedit & $can_coursedisciplines) {
    while ($sqlrow = $result->fetch_assoc()) {
      echo formpost($thisform);
    echo formhiddenval('courseid' , $_POST['courseid']);
    echo formhiddenval('termid' , $_POST['termid']);
    echo formhiddenval('coursediscid' , $sqlrow['id']);
    echo formhiddenval('orderby' , $_POST['orderby']);
    $disckey = 'disc' . $sqlrow['id'] ;
    if ($_POST['coursediscid'] == $sqlrow['id']) {
      if($_POST['act'] == 'Submit') {
          echo highlightbegin();
          echo formsubmit('act' , 'Edit');
          echo $sqlrow['code'] . ' -- ' . $sqlrow['name']. ' (' . $sqlrow['disckindcode'] . ')<br>';
          echo highlightend();
      } else {
      echo highlightbegin();
      echo $sqlrow['code'] . ' -- ' . $sqlrow['name'] . '  ';
      echo formselectsession($disckey . 'newterm' ,  'term' , $_POST['termid']);
      echo formselectsession($disckey . 'newkind' , 'disckind' , $sqlrow['disckindid']);
      echo formsubmit('act' , 'Submit') . '</form>';
      echo highlightend();
      echo formpost($thisform);
      echo spanformat('' , 'red' , '  ' . $GBL_Tspc . 'remover: ' , '' , true);
      echo formselectsession('discdelete' , 'bool' , 0);
      echo formhiddenval('courseid' , $_POST['courseid']);
      echo formhiddenval('termid' , $_POST['termid']);
      echo formhiddenval('coursediscid' , $sqlrow['id']);
      echo formhiddenval('orderby' , $_POST['orderby']);
      echo spanformat('' , 'red' , formsubmit('act' , 'Delete') , '' , true) . '<br>';
      } 
    } else {
      echo formsubmit('act' , 'Edit');
      echo $sqlrow['code'] . ' -- ' . $sqlrow['name']. ' (' . $sqlrow['disckindcode'] . ')<br>';
    }
    echo '</form>';
    }
    echo '<br>';
    echo '<i>inserção</i>';
    echo formpost($thisform);
    echo formhiddenval('courseid' , $_POST['courseid']);
    echo formhiddenval('termid' , $_POST['termid']);
    echo formhiddenval('coursediscid' , $sqlrow['id']);
    echo formhiddenval('orderby' , $_POST['orderby']);
    echo formhiddenval('act' , 'Reload');

    $q = "SELECT * FROM `unit` ORDER BY `acronym`";
    echo formselectsql($anytmp , $q , 'unitid' , $_POST['unitid'] , 'id' , 'acronym');

    $q = "SELECT * FROM `discipline` WHERE `dept_id` =  '$_POST[unitid]'  ORDER BY `name`";
    echo formselectsql($anyone , $q , 'discid' , $_POST['discid'] , 'id' , 'code' , 'name');
    
    echo formselectsession('newdisckind' , 'disckind' , 1);

    if ($anyone) {
        echo formsubmit('act' , 'Insert');
    }
    echo '</form>';
    

} else {
    while ($sqlrow = $result->fetch_assoc()) {
        $anyone = 1;
        echo $sqlrow['code'] . '  ' . $sqlrow['name']. ' (' . $sqlrow['disckindcode'] . ')<br>';
    }
}

if ($postedit & $can_coursedisciplines) {
} else {
    if ($anyone & $can_coursedisciplines) {
        echo formsubmit('act' , 'Edit');
    }
}

echo '</form>';
echo '</div>';
  

?>



