  
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['edcourse'];
formretainvalues(array('courseid' , 'termid' , 'orderby'));
  

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
    $Query = "SELECT * FROM `disciplinekind`";
    $result = $GBLmysqli->dbquery($Query);
    while ($sqlrow = $result->fetch_assoc()) {
        $_SESSION['disckind'][$sqlrow['id']] = $sqlrow['code'];
    }
}
if(!($_SESSION['term'])) {
    $Query = "SELECT * FROM `term`";
    $result = $GBLmysqli->dbquery($Query);
    while ($sqlrow = $result->fetch_assoc()) {
        $_SESSION['term'][$sqlrow['id']] = $sqlrow['code'];
    }
}
  
switch ($_POST['act']) {
case 'Insert':
    if ($can_coursedisciplines) {
        if ($_POST['discid'] != 0) {
        $Query = 
                "INSERT INTO `coursedisciplines` (`course_id` , `term_id` , `discipline_id` , `disciplinekind_id`) " . 
                "VALUES ( '$_POST[courseid]'  ,  '$_POST[termid]'  ,  '$_POST[discid]'  ,  '$_POST[newdisckind]' ) ; " ;

        $GBLmysqli->dbquery( $Query );
        $_POST['coursediscid'] = $GBLmysqli->insert_id;
        }
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
        $Query = "DELETE FROM `coursedisciplines` WHERE `id` =  '$_POST[coursediscid]' ;";
        $GBLmysqli->dbquery($Query);
        $Query = "DELETE FROM `vacancies` WHERE `vacancies` . `course_id` =  '$_POST[courseid]'  AND `vacancies` . `class_id` IN (SELECT `class` . `id` FROM `class` , `discipline` AS `disc` WHERE `class` . `discipline_id` = `disc` . `id` AND `disc` . `id` =  '$_POST[coursediscid]' );";
        $GBLmysqli->dbquery($Query);
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
    echo '</form><br>';
} else {
       
    $Query = 
        "SELECT * " .
        "FROM unit " .
        "WHERE iscourse = 1 " .
        "ORDER BY unit . name;" ; 
    echo formselectsql($anytmp , $Query , 'courseid' , $_POST['courseid'] , 'id' , 'acronym');
                  
    $Query = 
        "SELECT * " .
        "FROM term " .
        "ORDER BY term . name;" ;
    echo formselectsql($anytmp , $Query , 'termid' , $_POST['termid'] , 'id' , 'name');
                  
    echo $GBLspc['T'] . "Ordenado por:  "; 
    echo formselectsession('orderby' , 'orderby' , $_POST['orderby'] , false , true);
}
  
//echo '<br>'; 

if ($_POST['orderby'] == 0) {
  $ordby = 'discipline.name';
} else {
  $ordby = 'discipline.code';
}

function coursedisplay($sqlrow) {
  echo formsubmit('act' , 'Edit');
  echo $sqlrow['code'] . ' -- ' . $sqlrow['name']. ' (' . $sqlrow['disckindcode'] . ')<br>';
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

$firstofmany = true;
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
          echo HLbegin();
          coursedisplay($sqlrow);
          echo HLend();
      } else {
      echo HLbegin();
      echo $sqlrow['code'] . ' -- ' . $sqlrow['name'] . '  ';
      echo formselectsession($disckey . 'newterm' ,  'term' , $_POST['termid']);
      echo formselectsession($disckey . 'newkind' , 'disckind' , $sqlrow['disckindid']);
      echo formsubmit('act' , 'Submit') . '</form>';
      echo HLend();
      echo formpost($thisform);
      echo spanformat('' , 'red' , '  ' . $GBLspc['T'] . 'remover: ' , '' , true);
      echo formselectsession('discdelete' , 'bool' , 0);
      echo formhiddenval('courseid' , $_POST['courseid']);
      echo formhiddenval('termid' , $_POST['termid']);
      echo formhiddenval('coursediscid' , $sqlrow['id']);
      echo formhiddenval('orderby' , $_POST['orderby']);
      echo spanformat('' , 'red' , formsubmit('act' , 'Delete') , '' , true) . '<br>';
      } 
    } else {
      coursedisplay($sqlrow);
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

    $Query = 
        "SELECT * " .
        "FROM `unit` " .
        "ORDER BY `acronym`";
    echo formselectsql($anytmp , $Query , 'unitid' , $_POST['unitid'] , 'id' , 'acronym');

    $Query = 
        "SELECT * " .
        "FROM `discipline` " .
        "WHERE `dept_id` =  '$_POST[unitid]'  " .
        "ORDER BY `name`";
    $anyone = 0;
    echo formselectsql($anyone , $Query , 'discid' , $_POST['discid'] , 'id' , 'code' , 'name');
    
    echo formselectsession('newdisckind' , 'disckind' , 1);

    if ($anyone) {
        echo formsubmit('act' , 'Insert');
    }
    echo '</form>';
    

} else {
    while ($sqlrow = $result->fetch_assoc()) {
        if ($firstofmany) {
                $firstofmany = false;
                if($can_coursedisciplines) {
                        echo $GBLspc['D'] . formsubmit('act' , 'Edit') .'</form><br>'  ;
                } else {
                        echo '</form><br>'  ;
                
                }
        }
        echo $sqlrow['code'] . '  ' . $sqlrow['name']. ' (' . $sqlrow['disckindcode'] . ')<br>';
    }
}

echo '</div>';
  

?>



