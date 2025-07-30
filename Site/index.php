<?php 
session_start();
$GBL_index = true;
header('Content-Type: text/html; charset=utf-8'); 
echo '<!DOCTYPE html>' .
    '<html lang="en">';

include 'toppage/head.php'; 
        
echo '<body> ' ;

include 'pages/core.php';
        
include "toppage/topmenu.php"; 

        
echo '<div class="container">';
  
if(isset($_SESSION['sessionhash'])){
    if (($_GET['st'] == 'logout') | ($_GET['q'] == 'logout')) {
        session_destroy();
        include 'pages/Start.php';
        echo pagereload($GBLbasepage);
    } else {
        if ($GBLmysqli->hashcheck()) {
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
            echo pagereload($GBLbasepage);
        }
    }
}else{
    switch($_GET['st']) {
        
    case 'register': // creating the account in the DB
        //echo '<br> account created<br>';
        regacc_create();
        include 'pages/Start.php';
        break;
    case 'passrecovery': // recoverying password
        //echo '<br> password recovery<br>';
        regacc_passrecovery();
        include 'pages/Start.php';
        break;
    case 'valresend': // mail validation hash resend
        //echo '<br> validate link resend<br>';
        regacc_valresend();
        include 'pages/Start.php';
        break;
    case 'validate': // activating the account
        //echo '<br> account validated<br>';
        regacc_validate($_GET['h']);
        include 'pages/Start.php';
        break;
    case 'login': // login (and assigning a session hash to it)
        //echo '<br> account login<br>';
        if ($GBLmysqli->maillogincheck($_POST['emailA'],$_POST['passA'])) {
            include 'pages/usrhome.php';
            echo pagereload($GBLbasepage);
        } else {
            include 'pages/Start.php';
        };
        
        break;
    default: 
        include 'pages/Start.php';
        break;
    }
}
  
include 'toppage/foot.php';
        
echo '</div>' .  // <!-- /.container -->
    '</body>' .
    '</html>' ;
?>




