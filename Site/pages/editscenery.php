
<?php 

        $thisform=$GBLbasepage.'?q=edits&sq=scenery'; 
	$can_scenery = $_SESSION['role']['isadmin'];
        foreach ($_SESSION['role.scen'] as $roleid => $rolecanscen) {
                $can_scenery |= $rolecanscen;
        }
	
	$GBLmysqli->postsanitize();
	
	$postedit = false;
	if ( (($_POST['act'] == 'Submit') | ($_POST['act'] == 'Delete') | ($_POST['act'] == 'Insert')) & $can_scenery) {
		$postedit = true;
	} else {
		$_POST['act']='Cancel';
	}
?>

<div class="row">
        <h2>Cenários p/ Perfil</h2>
        <hr>

<?php


	if ($postedit) {

		switch($_POST['act']) {
			case 'Delete':
                                if ($_SESSION['scen.role'][$_POST['sceneryid']] |  $_SESSION['role']['isadmin']) {
        				if ($_POST['scenerydelete']) {
        					$q = "DELETE FROM `scenery` WHERE `id` = '" . $_POST['sceneryid'] . "';";
        					$GBLmysqli->dbquery($q);
        					$_POST['sceneryid']=null;
        				}
                                }
			break;
			case 'Submit':
                                if ($_SESSION['scen.role'][$_POST['sceneryid']] |  $_SESSION['role']['isadmin']) {
					$q = "UPDATE `scenery` SET `name` = '".$_POST['sceneryname'] ."', `desc` = '".$_POST['scenerydesc'] ."' , `hide` = '".$_POST['sceneryhide'] ."' ";
					$q = $q .  " WHERE `id` = '".$_POST['sceneryid']."';";
					$GBLmysqli->dbquery($q);
				        $_POST['sceneryid'] = null;
                                }
			break;
			case 'Insert':
                                if (($_SESSION['role.scen'][$_POST['roleid']]['can_scenery'] | $_SESSION['role']['isadmin'] ) & $_POST['addscenery']) {
					$q = "INSERT INTO `scenery` (`name`, `desc`,`hide`) VALUES ('"   . $_POST['sceneryname'] . "' , '" . $_POST['scenerydesc'] . "' , '" . $_POST['sceneryhide'] . "');";					
					$GBLmysqli->dbquery($q);
                                        $newscenery = $GBLmysqli->insert_id;
                                        $q = "INSERT INTO `sceneryrole` (`scenery_id` , `role_id`) VALUES ('"   . $newscenery . "' , '" . $_POST['roleid'] . "');";
					$GBLmysqli->dbquery($q);
				}
				$_POST['sceneryid'] = null;
			break;
		}
                $GBLmysqli->set_scenerysessionvalues();
	}
?>


<?php

        if($_SESSION['role']['isadmin']) {
                $q = "SELECT role.*   FROM role   WHERE '1' ORDER BY `rolename`;" ;
        } else {
                $q = "SELECT role.*   FROM role ,  accrole accr , account  WHERE " . 
	               "role.id = accr.role_id AND account.id = accr.account_id AND account.id =  '". $_SESSION['userid'] . "' ORDER BY `rolename`;";	
        }

        $sqlroles = $GBLmysqli->dbquery($q);
	while ($rolerow = $sqlroles->fetch_assoc()) {
                echo spanformat('1.2em','blue', 'Perfil ' . $rolerow['rolename'] . ' - ' . $rolerow['description'] . '<br>',null,true);
                $q = 'SELECT scenery.* , role.can_scenery  FROM scenery , role ,  sceneryrole scrole  WHERE ' . 
	               "scenery.id = scrole.scenery_id AND role.id = scrole.role_id AND role.id = '" . $rolerow['id'] . "' ORDER BY  `name`;";	
                $sqlscenery = $GBLmysqli->dbquery($q);
                $ifanyfirst = true;
	        while ($sceneryrow = $sqlscenery->fetch_assoc()) {
                        if ($sceneryrow['can_scenery'] | $_SESSION['role']['isadmin']) {                                
                		if ($sceneryrow['id'] == $_POST['sceneryid']) {
                			echo hiddendivkey('scen',$sceneryrow['id']);
                			highlightbegin();
                			echo formpost($thisform) . formhiddenval('sceneryid',$sceneryrow['id']);
                			echo 'Nome:' . formpatterninput(32,8,$GBLnamepattern,'scenery name','sceneryname',$sceneryrow['name']) .
                			 'Descrição:' . formpatterninput(64,32,$GBLcommentpattern,'scenery description','scenerydesc',$sceneryrow['desc']);
                			echo 'hide scenery? ';
                     		formselectsession('sceneryhide','bool',$sceneryrow['hide']);
                			echo '<br>' . formsubmit('act','Submit');
                			echo '</form>';			
                			highlightend();
                			echo formpost($thisform) . formhiddenval('sceneryid',$sceneryrow['id']);
                			echo spanformat('','red','Delete scenery &lt;'.$sceneryrow['name'] .'&gt;?',null,true);
                			formselectsession('scenerydelete','bool',0);
                			echo spanformat('','red',formsubmit('act','Delete'),null,true);
                			echo '</form><br>';			
                		} else {
                			echo formpost($thisform.targetdivkey('scen',$sceneryrow['id'])) . formhiddenval('sceneryid',$sceneryrow['id']) . formsubmit('act','Edit');
                			echo $sceneryrow['name'] . ' ( ' . $sceneryrow['desc'] . ' )' . ' <br>';
                				if ($sceneryrow['hide']) {
                					echo spanformat('','red','hidden: T ');
                				} else {
                					echo spanformat('','blue','hidden: F ');
                				}
                			echo '<br>';
                			echo '</form>';
                		}
                        } else {
                                echo '&nbsp;&nbsp;&nbsp;&nbsp;'; 
                                echo  $sceneryrow['name'] . ' ( ' . $sceneryrow['desc'] . ' )  ' ;
				if ($sceneryrow['hide']) {
					echo spanformat('','red','hidden: T ');
				} else {
					echo spanformat('','blue','hidden: F ');
				}
        			echo '<br>';
                        }         
                }
                if ($_SESSION['role.scen'][$rolerow['id']]['can_scenery'] | $_SESSION['role']['isadmin']) {                
        		echo formpost($thisform);
                        echo formhiddenval('roleid',$rolerow['id']);        
        		echo 'Nome:' . formpatterninput(32,8,$GBLnamepattern,'scenery name','sceneryname','!') .
        		 'Descriçao:' . formpatterninput(64,32,$GBLcommentpattern,'scenery description','scenerydesc','!');
        			echo '  hidden? ';
        			formselectsession('sceneryhide','bool',0);
        		echo '<br> Add scenery? ';
        		formselectsession('addscenery','bool',0);
        		echo  formsubmit('act','Insert');
        		echo '</form><br>';			
                }
                echo '<hr><hr>';	
        
        
  }
        
?>

		
 
</div> 