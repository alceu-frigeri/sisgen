
<?php $thisform=$basepage.'?q=admin&sq=DBimport'; ?>
<div class="row">
        <h2>Initial Data setup/import </h2>
        <hr>

		
<?php

	echo formpost($thisform) . formsubmit('act','Import Data ?') . '</form>';

if(!($_POST['act'])) {
	echo "nothing yet</br>";
} else {
	if($_SESSION['role']['isadmin'] & $sisgensetup) {
		echo "going over it...<br>";
		 include 'pages/DBinsertX.php';
	}
}
 ?>
    
 
</div>