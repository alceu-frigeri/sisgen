<?php
include 'bailout.php';

$menu = 
    array (
        'home'  => 
            array('hasChildren' => false , 'page' => 'usrhome.php', 'label' => 'Home', 'visible' => true, 
                  'id' => 'home' )
    );

if(isset($_SESSION['sessionhash']) && !$_SESSION['userchgpasswd']){
    if ($_SESSION['role']) {
        $menu['grids'] = 
            array(
                'hasChildren' => true , 'page' => 'home.php', 'label' => 'Grades' , 'visible' => true , 
                'children' => 
                    array(
                        'course' => 
                            array('page'=> 'gridcourse.php', 'label' => 'Cursos' , 
                                  'id' => 'course' ) , 
                        'prof' => 
                            array('page'=> 'gridprof.php', 'label' => 'Professores' , 
                                  'id' => 'prof' ) , 
                        'room' => 
                            array('page'=> 'gridroom.php', 'label' => 'Salas' , 
                                  'id' => 'room' ) , 
                        'comgrad' => 
                            array('page'=> 'gridcomgrad.php', 'label' => 'comgrad' , 
                                  'id' => 'comgrad' ) , 
                        'scen' => 
                            array('page'=> 'gridscenery.php', 'label' => 'Cenários' ,
                                  'id' => 'scen' ) , 
                    )
            );
        $menu['check'] = 
            array(
                'hasChildren' => true , 'page' => 'home.php', 'label' => 'Verificações' , 'visible' => true , 
                'children' => 
                    array(
                        'course' => 
                            array('page'=> 'checkcourse.php', 'label' => 'Cursos' ,
                                  'id' => 'chkcourse' ) , 
                        'prof' => 
                            array('page'=> 'checkprof.php', 'label' => 'Professores' ,
                                  'id' => 'chkprof' ) , 
                        'room' => 
                            array('page'=> 'checkroom.php', 'label' => 'Salas' ,
                                  'id' => 'chkroom' ) , 
                    )
            );
        $menu['reports'] = 
            array(
                'hasChildren' => true , 'page' => 'home.php', 'label' => 'Relatórios' , 'visible' => true , 
                'children' => 
                    array(
                        'dept' => 
                            array('page'=> 'reportdept.php', 'label' => 'Depto. p/ Disc.' , 
                                  'id' => 'rpdept' ) , 
                        'assign' => 
                            array('page'=> 'reportassignment.php', 'label' => 'Depto. p/ Prof.' , 
                                  'id' => 'rpassign' ) ,         
                        'enroll' => 
                            array('page'=> 'reportenrollment.php', 'label' => 'Hist. Ocupação' ,
                                  'id' => 'rpenroll' ) , 
                        'grid' => 
                            array('page'=> 'reportgrid.php', 'label' => 'Grade Depto.' ,
                                  'id' => 'rpgrid' ) , 
                    )
            );
        $menu['edits'] = 
            array(
                'hasChildren' => true , 'page' => 'home.php', 'label' => 'Edição' , 'visible' => true , 
                'children' => 
                    array(
                        'class' => 
                            array('page'=> 'editclass.php', 'label' => 'Turmas' , 
                                  'id' => 'edclass' ) , 
                        'scen' => 
                            array('page'=> 'editscenery.php', 'label' => 'Cenários' , 
                                  'id' => 'edscen' ),                        
                        'disc' => 
                            array('page'=> 'editdisc.php', 'label' => 'Disciplinas' ,
                                  'id' => 'eddisc' ) , 
                        'course' => 
                            array('page'=> 'editcourse.php', 'label' => 'Grades' , 
                                  'id' => 'edcourse' ) , 
                        'prof' => 
                            array('page'=> 'editprof.php', 'label' => 'Professores' ,
                                  'id' => 'edprof' ) , 
                        'room' => 
                            array('page'=> 'editroom.php', 'label' => 'Salas' , 
                                  'id' => 'edroom' ) , 
                        'unit' => 
                            array('page'=> 'editunit.php', 'label' => 'Dados Dept.' ,
                                  'id' => 'edunit' ) , 
                    )
            );
        if($_SESSION['role']['isadmin']) {
            $menu['admin'] = 
                array('hasChildren' => true, 'hasLink' => false, 'label' => 'Administração', 'visible' => true , 
                      'children' => 
                      array(
                          'sem' => 
                              array('page'=> 'adminsem.php', 'label' => 'Ed. Semestres' ,
                                    'id' => 'admsem' ) , 
                          'courses' => 
                              array('page'=> 'admincourse.php', 'label' => 'Ed. Cursos' , 
                                    'id' => 'admcourse' ) , 
                          'accounts' => 
                              array('page'=> 'adminacc.php', 'label' => 'Usuários' ,
                                    'id' => 'admacc' ) , 
                          'roles' => 
                              array('page'=> 'adminroles.php', 'label' => 'Roles' ,
                                    'id' => 'admrole' ) , 
                      )
                );
        }
        if ($GBLsisgensetup) {
            $menu['admin']['children']['dbset'] = 
                array('page'=> 'admindb.php', 'label' => 'DB data' ,
                      'id' => 'admdb' );
        }
    } 
    $menu['logout'] = 
        array('hasChildren' => false , 'page' => 'Start.php', 'label' => 'logout', 'visible' => true);             
}


?>
