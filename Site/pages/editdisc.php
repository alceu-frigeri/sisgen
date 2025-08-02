
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['eddisc'];
formretainvalues(array('unitid' , 'orderby'));
  
  
$can_discipline = $_SESSION['role']['isadmin'] || ($_SESSION['role'][$_POST['unitid']] & $_SESSION['role'][$_POST['unitid']]['can_disciplines']);

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
    echo '</form><br>';
} else {

    $Query = 
        "SELECT * " .
        "FROM unit  " .
        "ORDER BY unit . mark DESC , unit . iscourse ASC, unit . acronym ASC ; " ; 
    echo 'Dept.: ' ;
    echo formselectsql($anytmp , $Query , 'unitid' , $_POST['unitid'] , 'id' , 'acronym');
    echo $GBLspc['D'] . "Ordenado por:  " . formselectsession('orderby' , 'orderby' , $_POST['orderby'] , false , true);

    $firstofmany = true;

}
      

function discdisplay($sqlrow) {
    global $GBLspc, $GBLcommentcolor;
        echo formsubmit('act' , 'Edit');
        echo formhiddenval('discid' , $sqlrow['id']);
        echo formhiddenval('orderby' , $_POST['orderby']);
        
        echo $sqlrow['code'] . $GBLspc['T'] . 'T: ' . $sqlrow['Tcred'] . $GBLspc['D'] . 'L: ' . $sqlrow['Lcred'] . $GBLspc['D'] . ' ' . $sqlrow['name'];
        if ($sqlrow['comment']) {
            echo $GBLspc['T'] . '' . spanformat('smaller' , $GBLcommentcolor , '(' . $sqlrow['comment'] . ')') ;
        }
        echo '<br>';

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

if ($postedit & $can_discipline) {
    while ($sqlrow = $result->fetch_assoc()) {
        $discdeptcode = substr($sqlrow['code'] , 0 , 5);
        echo formpost($thisform . targetdivkey('disc' , $sqlrow['id']));
        echo formhiddenval('unitid' , $_POST['unitid']);
        if ($_POST['discid'] == $sqlrow['id']) {
            if ($_POST['act'] == 'Submit') {
                echo HLbegin();
                discdisplay($sqlrow);
                echo HLend();
            } else 
            {
                echo hiddendivkey('disc' , $sqlrow['id']);
                echo HLbegin();
                echo formhiddenval('discid' , $sqlrow['id']);
                echo formhiddenval('discdeptcode' , $discdeptcode);
                $discsubcode =  substr($sqlrow['code'] , 5 , 3);
                echo $discdeptcode;
                $disckey = 'disc' . $sqlrow['id'] ;
                echo formpatterninput(3 , 1 , '[0-9][0-9][0-9]' , '3 digitos' , $disckey . 'discsubcode' , $discsubcode)  . 
                    $GBLspc['T'] . 'T: '  . 
                    formpatterninput(1 , 1 , '[0-8]' , 'single digit' , $disckey . 'discTcred' , $sqlrow['Tcred'])  . 
                    $GBLspc['D'] . 'L: '  . 
                    formpatterninput(1 , 1 , '[0-8]' , 'single digit' , $disckey . 'discLcred' , $sqlrow['Lcred'])  . 
                    $GBLspc['D'] . ' '  . 
                    formpatterninput(120 , 64 , $GBLdiscpattern , 'Nome da disciplina' , $disckey . 'discname' , $sqlrow['name'])  . 
                    '<br>' . $GBLspc['D'] . ' Obs . :' . formpatterninput(48 , 16 , $GBLcommentpattern , 'Comentário qq' , $disckey . 'disccomment' , $sqlrow['comment']);
                echo formsubmit('act' , 'Submit');
                echo '</form>';
                echo HLend();
                echo formpost($thisform) . formhiddenval('unitid' , $_POST['unitid']) . formhiddenval('discid' , $sqlrow['id'])  . 
                    spanfmtbegin('' , 'red' , '' , true) . '  ' . $GBLspc['T'] . 'Remover: ' ;
                echo formselectsession('discdelete' , 'bool' , 0) ;
                echo formsubmit('act' , 'Delete') . spanfmtend();
            }
        } else {
            discdisplay($sqlrow);
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
        $GBLspc['T'] . 'T: '  . 
        formpatterninput(1 , 1 , '[0-8]' , 'single digit' , 'discTcred' , 0)  . 
        $GBLspc['D'] . 'L: '  . 
        formpatterninput(1 , 1 , '[0-8]' , 'single digit' , 'discLcred' , 0)  . 
        $GBLspc['D'] . ' '  . 
        formpatterninput(120 , 64 , $GBLdiscpattern , 'Nome da disciplina' , 'discname' , '!')  . 
        $GBLspc['D'] . ' Obs.:' . formpatterninput(48 , 16 , $GBLcommentpattern , 'Comentário qq' , 'disccomment' , $sqlrow['comment'])  . 
        '  ' . $GBLspc['T'] . ' '  . 
        formsubmit('act' , 'Insert')  . 
        '</form>';
    

} else {
    while ($sqlrow = $result->fetch_assoc()) {
        if ($firstofmany) {
            $firstofmany = false;
            if ($can_discipline) {
                echo formsubmit('act' , 'Edit') .  '</form><br>';
            } else {
                echo   '</form><br>';
            }
        }
        echo $sqlrow['code'] . $GBLspc['T'] . 'T: ' . $sqlrow['Tcred'] . $GBLspc['D'] . 'L: ' . $sqlrow['Lcred'] . $GBLspc['D'] . ' ' . $sqlrow['name'];
        if ($sqlrow['comment']) {
            echo $GBLspc['T'] . '' . spanformat('smaller' , $GBLcommentcolor , $sqlrow['comment']);
        }
        echo '<br>';
    }
}

echo '</div>';

?>
  


