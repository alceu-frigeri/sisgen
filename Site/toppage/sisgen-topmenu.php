
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
                        <?php include "sisgen-menu.php"; ?>
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

                                                    <li><a href="<?php echo $GBLbasepage; ?>?q=<?php echo $key; ?>&sq=<?php echo $childKey; ?>"><?php echo $childValue["label"]; ?></a>
                          </li>                                    

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
                                                        <img class="img-responsive img-center logo-congresso" src="./images/logo-delae.png" max-height="35" alt="">

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
                        <span class="title3">Porto Alegre<br>  <?php echo date("Y"); ?></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4 banner-title banner-title-local">                
<!--            <span class="title5">Porto Alegre, RS, Brazil<br></span>-->
        </div>
    </div>

</header>
<span class="title4 bottom"><font color=white>SISGEN - Sistema de Apoio à Gerência de Turmas e Encargos</font><br></span>

