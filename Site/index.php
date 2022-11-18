<?php session_start();?>
<?php  header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html lang="en">

<?php include "sisgen-head.php"; ?>

<body> 
<?php 	include 'pages/core.php'; ?>

<?php include "sisgen-topmenu.php"; ?>

<!-- Page Content -->
<div class="container">



    <?php
	
	
	if(isset($_SESSION['sessionhash'])){
		 if (($_GET['st'] == 'logout') | ($_GET['q'] == 'logout')) {
			 session_destroy();
			 include 'pages/Start.php';
			 echo pagereload($basepage);
		 } else {
			if ($mysqli->hashcheck()) {
					if ($menu[$query]["hasChildren"]) {
						$pag = "pages/" . $menu[$query]["children"][$childQuery]["page"];
					} else {
						$pag = "pages/" . $menu[$query]["page"];
					} 
					if (file_exists($pag)) {
						include $pag;    
					} else {
						include 'pages/home.php';
					}
				
			} else {
				session_destroy();
				include 'pages/Start.php';
				echo pagereload($basepage);
			}
		 }
	}else{
		switch($_GET['st']) {
	      
			case 'register': // creating the account in the DB
			    echo '<br> account create<br>';
				regacc_create();
				include 'pages/Start.php';
			break;
			case 'passrecovery': // recoverying password
			    echo '<br> password recovery<br>';
				regacc_passrecovery();
				include 'pages/Start.php';
			break;
			case 'valresend': // mail validation hash resend
			    echo '<br> validate link resend<br>';
				regacc_valresend();
				include 'pages/Start.php';
			break;
			case 'validate': // activating the account
			    echo '<br> account validate<br>';
				regacc_validate($_GET['h']);
				include 'pages/Start.php';
			break;
			case 'login': // login (and assigning a session hash to it)
			    echo '<br> account login<br>';
			    if ($mysqli->maillogincheck($_POST['emailA'],$_POST['passA'])) {
					include 'pages/usrhome.php';
					echo pagereload($basepage);
				} else {
					include 'pages/Start.php';
				};
				
			break;
			default: 
				include 'pages/Start.php';
			break;
		}
	}

	


    ?>

<?php include "sisgen-foot.php"; ?>

</div><!-- /.container -->


</body>

</html>
