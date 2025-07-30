
<?php 
include 'bailout.php';

$GBLmysqli->postsanitize();
$thisform = $_SESSION['pagelnk']['admcourse'];

echo '<div class = "row">' .
    '<h2>Edição Cursos</h2>' . 
    '<hr><br>' ;

if ($_SESSION['role']['isadmin']) {
    switch($_POST['act']) {
    case 'Submit':
        if(fieldscompare('' , array('acronym' , 'code' , 'name'))) {
            $Query = 
                "UPDATE `unit` " .
                "SET `acronym` =  '$_POST[acronym]'  , " .
                        "`code` =  '$_POST[code]'  , " .
                        "`name` =  '$_POST[name]'   " .
                "WHERE `id` =  '$_POST[courseid]' ; " ; 
            $GBLmysqli->dbquery( $Query );
        }
        break;
    case 'Delete':
        if ($_POST['coursedelete']) {
            $Query = 
                "DELETE FROM `unit` " .
                "WHERE `id` =  '$_POST[courseid]' ;";
            $GBLmysqli->dbquery( $Query );
        }
        break;
    case 'Duplicate as':
        duplicatecourse($_POST['courseid'] , $_POST['newacronym'] , $_POST['newcode'] , $_POST['newname'] , 'dup. from ' . $_POST['oldcourseacro']);
        break;
    }

    // course, term
    $Query = 
        "SELECT * " .
        "FROM   `unit` " .
        "WHERE `iscourse` = '1' " .
        "ORDER BY `acronym` ; " ;

    $result = $GBLmysqli->dbquery( $Query );
    $any = 0;
    while ($sqlrow = $result->fetch_assoc()) {
        $any = 1;
        echo formpost($thisform);
        echo formhiddenval('courseid' , $sqlrow['id']);
        if(($_POST['courseid'] == $sqlrow['id']) & (($_POST['act'] == 'Edit'))) {
            echo HLbegin();
            echo formpatterninput(10 , 3 , '[A-Z]+' , 'acronym' , 'acronym' , $sqlrow['acronym'])  . 
                formpatterninput(5 , 5 , '[A-Z][A-Z][A-Z][0-9][0-9]' , 'code, e.g. CCA99' , 'code' , $sqlrow['code'])  . 
                formpatterninput(32 , 16 , $GBLnamepattern , 'nome' , 'name' , $sqlrow['name']);
            echo formsubmit('act' , 'Submit');
            echo spanfmtbegin('','red',null,true);
            echo $GBLspc['T'] . 'Deletar:';
            echo formselectsession('coursedelete' , 'bool' , 0);
            echo formsubmit('act' , 'Delete') . '<br>';
            echo spanfmtend();
            echo '</form>';
            echo HLend();
            echo formpost($thisform);
            echo formhiddenval('courseid' , $sqlrow['id']);
            echo formhiddenval('oldcourseacro' , $sqlrow['acronym']);
            echo formpatterninput(10 , 3 , '[A-Z]+' , 'acronym' , 'newacronym' , '!')  . 
                formpatterninput(5 , 5 , '[A-Z][A-Z][A-Z][0-9][0-9]' , 'code, e.g. CCA99' , 'newcode' , '!')  . 
                formpatterninput(32 , 16 , $GBLnamepattern , 'nome' , 'newname' , '!');
            echo formsubmit('act' , 'Duplicate as');
            echo "</form>";
        } else {
            echo formsubmit('act' , 'Edit');
            echo $sqlrow['acronym'].  $GBLspc['D']  . $sqlrow['code'] .  $GBLspc['D']  . $sqlrow['name'] . "<br>";
            echo "</form>";
        }
    }

}
 
echo '</div>' ;

?>

    
  
