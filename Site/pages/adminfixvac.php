
<?php $thisform=$basepage.'?q=admin&sq=fixvac'; ?>
<div class="row">
        <h2>Vacancies Fix </h2>
        <hr>

		
<?php

echo formpost($thisform) . formsubmit('act','Fix ?') . '</form>';

if(!($_POST['act'])) {
	echo "nothing yet</br>";
} else {
	include 'pages/DBfixvac.php';
}
 ?>
    
 
</div>
