<?php


$menu = array (
    'home'  => array('hasChildren' => false,'page' => 'usrhome.php', 'label' => 'Home', 'visible' => true));

if(isset($_SESSION['sessionhash']) && !$_SESSION['userchgpasswd']){
	if ($mysqli->hashcheck() & $_SESSION['role']) {
		$menu['reports'] = array(
			'hasChildren' => true,'page' => 'home.php', 'label' => 'Relatórios','visible' => true,
			'children' => array(
				'course' => array('page'=> 'reportcourse.php', 'label' => 'Cursos'),
				'prof' => array('page'=> 'reportprof.php', 'label' => 'Professores'),
				'room' => array('page'=> 'reportroom.php', 'label' => 'Salas'),
				'dept' => array('page'=> 'reportdept.php', 'label' => 'Departamento'),
				'comgrad' => array('page'=> 'reportcomgrad.php', 'label' => 'comgrad'),
			)
		);
		$menu['check'] = array(
			'hasChildren' => true,'page' => 'home.php', 'label' => 'Verificações','visible' => true,
			'children' => array(
				'course' => array('page'=> 'checkcourse.php', 'label' => 'Cursos'),
				'prof' => array('page'=> 'checkprof.php', 'label' => 'Professores'),
				'room' => array('page'=> 'checkroom.php', 'label' => 'Salas'),
				// 'dept' => array('page'=> 'reportdept.php', 'label' => 'Departamento'),
				// 'comgrad' => array('page'=> 'reportcomgrad.php', 'label' => 'comgrad'),
			)
		);
		$menu['edits'] = array(
			'hasChildren' => true,'page' => 'home.php', 'label' => 'Edição','visible' => true,
			'children' => array(
				'classes' => array('page'=> 'editclass.php', 'label' => 'Turmas'),
				'Disciplines' => array('page'=> 'editdisc.php', 'label' => 'Disciplinas'),
				'Course' => array('page'=> 'editcourse.php', 'label' => 'Grades'),
				'Prof' => array('page'=> 'editprof.php', 'label' => 'Professores'),
				'rooms' => array('page'=> 'editroom.php', 'label' => 'Salas'),
				'unit' => array('page'=> 'editunit.php', 'label' => 'Dados Dept.'),
			)
		);
		if($_SESSION['role']['isadmin']) {
			$menu['admin'] = array('hasChildren' => true, 'hasLink' => false, 'label' => 'Administração', 'visible' => true,
				'children' => array(
					'sem' => array('page'=> 'adminsem.php', 'label' => 'Ed. Semestres'),
					'courses' => array('page'=> 'admincourse.php', 'label' => 'Ed. Cursos'),
					'scenery' => array('page'=> 'adminscenery.php', 'label' => 'Ed. Cenários'),
                    'adminacc' => array('page'=> 'adminacc.php', 'label' => 'Usuários'),
                    'adminroles' => array('page'=> 'adminroles.php', 'label' => 'Roles'),
                )
			);
		}
		if ($sisgensetup) {
			$menu['admin']['children']['DBimport'] = array('page'=> 'admindb.php', 'label' => 'DB data');
		}
	} 
	$menu['logout'] = array('hasChildren' => false,'page' => 'Start.php', 'label' => 'logout', 'visible' => true);					   
}
