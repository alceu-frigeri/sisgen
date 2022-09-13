<?php  header('Content-Type: text/html; charset=iso-8859-1'); ?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="iso-8859-1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="SBAI 2017 - Simpósio Brasileiro de Automação Inteligente">
    <meta name="author" content="">
    
    <link rel="shortcut icon" href="./images/icon_logo.ico">

    <title>SBAI 2017 - Simp&oacute;sio Brasileiro de Automa&ccedil;&atilde;o Inteligente</title>

    <!-- Bootstrap Core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="css/custom.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="js/jquery.js"></script>

    <!-- jQuery Mask (here just for phones...) -->
    <!-- see https://igorescobar.github.io/jQuery-Mask-Plugin/ -->
    <script src="js/jquery.mask.js"></script>


    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
        <![endif]-->

    </head>

    <body> 

        <!-- Navigation -->
        <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <!-- Brand and toggle get grouped for better mobile display -->
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button> 
                </div>
                <!-- Collect the nav links, forms, and other content for toggling -->
                <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                    <ul class="nav navbar-nav">
                        <?php include "menu.php"; ?>
                        <?php 
                        $query = $_GET['q'];
                        if (empty($query)) {
                            $query = "home";
                        } 
                        $childQuery = $_GET['sq'];

                        ?>



                        <?php foreach($menu as $key=>$value):  $active = ""; ?>
                            <?php if(!$value["visible"]) continue; ?>
                            <?php if($key == $query) : $active = "menu-active"?>
                                <li class="menu-active">

                                <?php else : ?>
                                    <li>
                                    <?php endif; ?>                        
                                    <?php if($value["hasChildren"]) : ?>
                                        <li class="dropdown <?php echo $active; ?>">
                                          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                                              <?php echo $value["label"]; ?> <span class="caret"></span></a>
                                              <ul class="dropdown-menu">
                                                <?php foreach($value["children"] as $childKey => $childValue): ?> 

                                                    <li><a href="?q=<?php echo $key; ?>&sq=<?php echo $childKey; ?>"><?php echo $childValue["label"] ?></a></li>                                    

                                                <?php endforeach; ?>                    
                                            </ul>
                                        </li>
                                    <?php else : ?>
                                        <a href="?q=<?php echo $key ?>"><?php echo $value["label"]; ?></a>
                                    <?php endif; ?>                                                    
                                </li>
                            <?php endforeach; ?>



                        </ul>
                    </div>
                    <!-- /.navbar-collapse -->
                </div>
                <!-- /.container -->
            </nav>

            <!-- Image Background Page Header -->
            <!-- Note: The background image is set within the business-casual.css file. --> 


            <header class="business-header">
                <div class="container banner">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="row banner-info" >
                                <div class="col-sm-3"> 
                                    <!--<a href="http://www.ifac-control.org/" target="_blank">
                                        <img class="img-responsive img-center logo" src="./images/logo4.png" alt="">
                                    </a>-->
				                                                <img class="img-responsive img-center logo-congresso" src="./images/SBAI_branco.png" height="160" alt="">

                                </div>
                                <div class="col-sm-2"> 
<!--                                    <a href="http://www.sba.org.br/" target="_blank">
                                            <img class="img-responsive img-center logo-ifac-sba" src="./images/logosba2.png" alt="">
                                        </a> -->


</div>
                                <div class="col-sm-2"> 
                                    <!--<img class="img-responsive img-center logo" src="./images/st_logo.png" alt="">-->
                                </div>

                    <div class="col-sm-4 banner-title">                
                        <span class="title3">Porto Alegre<br>  1-4 de Outubro de 2017</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 banner-title banner-title-local">                
<!--            <span class="title5">Porto Alegre, RS, Brazil<br></span>-->
        </div>
    </div>

</header>
<span class="title4 bottom"><font color=white>SBAI 2017 - Simp&oacute;sio Brasileiro de Automa&ccedil;&atilde;o Inteligente</font><br></span>

<!-- Page Content -->
<div class="container">

    <?php
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

    ?>


    <!-- /.row --> 
    <div class="row">
        <div class="col-sm-12">
            <hr>
	    <center>
	    <table><tr><th><center><h3>Promoção</h3></center></th><th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th><th><center><h3>Organização</h3></center></th><th>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</th><th><center><h3>Apoio</h3></center></th></tr>
	    <tr>
                 <td>                   <a href="http://www.sba.org.br/" target="_blank">
                                            <img class="img-responsive img-center logo-ifac-sba" src="./images/logosba2.png" alt="">
                                        </a></td><td></td>


<td>
		<a href="http://www.ufrgs.br"><img src="./images/image014.png" height="80"></a>&nbsp;&nbsp;&nbsp;&nbsp;
                <a href="http://www.pucrs.br"><img src="./images/logo_pucrs.png" height="80"></a>
</td><td></td><td>                <img src="./images/image004.png">
                <img src="./images/image006.png" height="50">
                <img src="./images/image008.png" height="50">
                <img src="./images/image010.png" height="50">
                <img src="./images/image012.png" height="50">
</td></tr>
	    </table>
	    </center>
        </div>
    </div>


    <!-- Footer -->
    <footer>
        <div class="row">
            <div class="col-lg-12"> 
            </div>
        </div>
        <!-- /.row -->
    </footer>

</div>
<!-- /.container -->


<!-- Bootstrap Core JavaScript -->
<script src="js/bootstrap.min.js"></script>

<script src="js/custom.js"></script> 

</body>

</html>
