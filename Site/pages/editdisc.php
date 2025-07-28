
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $GBLbasepage . '?q=edits&sq=Disciplines'; 
formretainvalues(array('unitid' , 'orederby'));
  
        

  
$can_discipline = $_SESSION['role']['isadmin'] | ($_SESSION['role'][$_POST['unitid']] & $_SESSION['role'][$_POST['unitid']]['can_disciplines']);

$postedit = false;
if ( (($_POST['act'] == 'Edit') | ($_POST['act'] == 'Submit') | ($_POST['act'] == 'Delete') | ($_POST['act'] == 'Insert')) & $can_discipline) {
    $postedit = true;
} else {
    $_POST['act'] = 'Cancel';
}

switch ($_POST['act']) {
case 'Insert':
    $Query = 
        "INSERT INTO `discipline` (`dept_id` , `code` , `Lcred` , `Tcred` , `name` , `comment`) " .
        "VALUES ( '$_POST[unitid]'  , '$_POST[discdeptcode]$_POST[discsubcode]' ,  '$_POST[discLcred]'  ,  '$_POST[discTcred]'  , '$_POST[discname]'  ,  '$_POST[disccomment]' ) ; " ;

    $GBLmysqli->dbquery( $Query );
    $_POST['discid'] =  $GBLmysqli->insert_id;
    $_POST['act'] = 'Submit';
    break;
case 'Submit':
    $disckey = 'disc' .$_POST['discid'] ;
    $compfields = array('discsubcode' , 'discLcred' , 'discTcred' , 'discname' , 'disccomment' ) ;

    if(fieldscompare($disckey , $compfields)) {
        
        foreach ($compfields as $field) {
            $keypost[$field] = $_POST[$disckey . $field] ;
        }
        $Query = 
            "UPDATE `discipline` " .
            "SET  " . 
                    "`code` = '$_POST[discdeptcode]$keypost[discsubcode]' , " . 
                    "`Lcred` =  '$keypost[discLcred]'  , " . 
                    "`Tcred` =  '$keypost[discTcred]'  , " . 
                    "`name` =  '$keypost[discname]'  , " . 
                    "`comment` =  '$keypost[disccomment]'  " .
            "WHERE `id` =  '$_POST[discid]' ; ";
               
        //vardebug($Query,'query ') ;
        $GBLmysqli->dbquery( $Query );
    } 
    //$_POST['discid'] = null;
    break;
case 'Delete':
    if ($_POST['discdelete']) {
        $Query = 
            "DELETE FROM `discipline` " .
            "WHERE `id` =  '$_POST[discid]' ;";
        $GBLmysqli->dbquery( $Query );
    }
    break;
}
  

echo '<div class = "row">' .
    '<h2>Disciplinas</h2>' .
    '<hr>' ;

echo formpost($thisform);
if ($postedit & $can_discipline) {
    echo formhiddenval('unitid' , $_POST['unitid']);
    echo formhiddenval('orderby' , $_POST['orderby']);
    echo displaysqlitem('' , 'unit' , $_POST['unitid'] , 'acronym' , 'name');
    echo formsubmit('act' , 'Cancel');
    echo '</form>';
} else {

    echo formselectsql($anytmp , 
                  "SELECT * FROM unit  ORDER BY unit . mark DESC , unit . iscourse ASC, unit . acronym ASC;" , 
                  'unitid' , 
                  $_POST['unitid'] , 
                  'id' , 
                  'acronym');
    echo "Ordenado por:  ";
    echo formselectsession('orderby' , 'orderby' , $_POST['orderby'] , false , true);

    $firstofmany = true;

}
      

// course, term
if ($_POST['orderby'] == 0) {
    $ordby = 'discipline.name';
} else {
    $ordby = 'discipline.code';
}

$Query = 
    "SELECT * " .
    "FROM   `discipline` " .
    "WHERE `dept_id` =  '$_POST[unitid]'  " .
    "ORDER BY $ordby ; " ;

$result = $GBLmysqli->dbquery( $Query );
$anyone = 0;
if ($postedit & $can_discipline) {
    while ($sqlrow = $result->fetch_assoc()) {
        echo formpost($thisform . targetdivkey('disc' , $sqlrow['id']));
        echo formhiddenval('unitid' , $_POST['unitid']);
        if ($_POST['discid'] == $sqlrow['id']) {
            if ($_POST['act'] == 'Submit') {
                echo highlightbegin();
                echo formsubmit('act' , 'Edit');
                echo formhiddenval('discid' , $sqlrow['id']);
                echo formhiddenval('orderby' , $_POST['orderby']);
                $discdeptcode = substr($sqlrow['code'] , 0 , 5);
                echo $sqlrow['code'] . $GBL_Tspc . 'T: ' . $sqlrow['Tcred'] . $GBL_Dspc . 'L: ' . $sqlrow['Lcred'] . $GBL_Dspc . ' ' . $sqlrow['name'];
                if ($sqlrow['comment']) {
                    echo $GBL_Tspc . '' . spanformat('smaller' , $GBLcommentcolor , '(' . $sqlrow['comment'] . ')') ;
                }
                echo '<br>';
                echo highlightend();
            } else 
            {
                echo hiddendivkey('disc' , $sqlrow['id']);
                echo highlightbegin();
                echo formhiddenval('discid' , $sqlrow['id']);
                $discdeptcode = substr($sqlrow['code'] , 0 , 5);
                echo formhiddenval('discdeptcode' , $discdeptcode);
                $discsubcode =  substr($sqlrow['code'] , 5 , 3);
                echo $discdeptcode;
                $disckey = 'disc' . $sqlrow['id'] ;
                echo formpatterninput(3 , 1 , '[0-9][0-9][0-9]' , '3 digitos' , $disckey . 'discsubcode' , $discsubcode)  . 
                    $GBL_Tspc . 'T: '  . 
                    formpatterninput(1 , 1 , '[0-8]' , 'single digit' , $disckey . 'discTcred' , $sqlrow['Tcred'])  . 
                    $GBL_Dspc . 'L: '  . 
                    formpatterninput(1 , 1 , '[0-8]' , 'single digit' , $disckey . 'discLcred' , $sqlrow['Lcred'])  . 
                    $GBL_Dspc . ' '  . 
                    formpatterninput(120 , 64 , $GBLdiscpattern , 'Nome da disciplina' , $disckey . 'discname' , $sqlrow['name'])  . 
                    '<br>' . $GBL_Dspc . ' Obs . :' . formpatterninput(48 , 16 , $GBLcommentpattern , 'Comentário qq' , $disckey . 'disccomment' , $sqlrow['comment']);
                echo formsubmit('act' , 'Submit');
                echo '</form>';
                echo highlightend();
                echo formpost($thisform) . formhiddenval('unitid' , $_POST['unitid']) . formhiddenval('discid' , $sqlrow['id'])  . 
                    spanformatstart('' , 'red' , '' , true) . '  ' . $GBL_Tspc . 'Remover: ' ;
                echo formselectsession('discdelete' , 'bool' , 0) ;
                echo formsubmit('act' , 'Delete') . spanformatend();
            }
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
            if ($candiscipline) {
                echo formsubmit('act' , 'Edit') .  '</form><br>';
            } else {
                echo   '</form><br>';
            }
        }
        echo $sqlrow['code'] . $GBL_Tspc . 'T: ' . $sqlrow['Tcred'] . $GBL_Dspc . 'L: ' . $sqlrow['Lcred'] . $GBL_Dspc . ' ' . $sqlrow['name'];
        if ($sqlrow['comment']) {
            echo $GBL_Tspc . '' . spanformat('smaller' , $GBLcommentcolor , $sqlrow['comment']);
        }
        echo '<br>';
    }
}

echo '</div>';

?>
  


