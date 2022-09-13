<?php




$menu = array (
    "home"  => array("hasChildren" => false,"page" => "home.php", "label" => "Home", "visible" => true),
    "about"  => array("hasChildren" => false,"page" => "about.php", "label" => "Escopo","visible" => true ),
    "invitedSessions"  => array("hasChildren" => false,"page" => "invitedSessions.php", "label" => "Programa","visible" => false ),
    // "program"  => array("hasChildren" => true, "hasLink" => false, "label" => "Programa", "visible" => true,
    //                     "children" => array(
    //                             "invitedSessions" => array("page"=> "invitedSessions.php", "label" => "Sess&otilde;es Convidadas"),                          
    //                         )
    //                       ),
    "committees"  => array("hasChildren" => true, "hasLink" => false, "label" => "Comit&ecirc;s", "visible" => true,
                           "children" => array(
                               "organizingCommittee" => array("page"=> "organizingCommittee.php", "label" => "Comit&ecirc; Organizador"),
                               "internationalCommittee" => array("page"=> "internationalCommittee.php", "label" => "Comit&ecirc; de Programa"),
                           )
        //"page" => "committees.php", "label" => "Committees",
    ),
    "Programação"  => array("hasChildren" => true, "hasLink" => false, "label" => "Programação", "visible" => true,
                             "children" => array(
                                 "programcao" => array("page"=> "programm.php", "label" => "Programação"),
                                 "palestrantes" => array("page"=> "palestrantes.php", "label" => "Palestrantes"),
                             )
    ),
    //"callpapers"  => array("hasChildren" => false,"page" => "callpapers.php", "label" => "Call for Papers","visible" => true ),
    "registration"  => array("hasChildren" => true, "hasLink" => false, "page" => "registration.php", "label" => "Inscriç&otilde;es","visible" => true,
			     "children" => array(
				 "RegInfo" => array("page" => "registration.php", "label" => "Informações"),
				 "Register" => array("page" => "Reg.php", "label" => "Sistema de Inscrição", "visible" => true),			
			     )
			     ),
				 
			     "test"  => array("hasChildren" => false,"page" => "test.php", "label" => "sql Test","visible" => false ),
			     "test1"  => array("hasChildren" => false,"page" => "test1.php", "label" => "sql Test","visible" => false ),
			     "Register"  => array("hasChildren" => false,"page" => "Reg.php", "label" => "Inscrição","visible" => false ),
			     "test3"  => array("hasChildren" => false,"page" => "test3.php", "label" => "sql Test","visible" => false ),
				 
			     "reg"  => array("hasChildren" => false,"page" => "registrationFeesTest.php", "label" => "Fees Test","visible" => false ),
			     "regform"  => array("hasChildren" => false,"page" => "RegistrationForm.php", "label" => "Formulário Registro","visible" => false ),
			     "register"  => array("hasChildren" => false,"page" => "regForm2.php", "label" => "Formulário Registro","visible" => false ),
			     
			     "submission"  => array("hasChildren" => false,"page" => "submission.php", "label" => "Submiss&atilde;o","visible" => true ),
			     "venue"  => array("hasChildren" => false, "page" => "venue.php", "label" => "Local","visible" => true ),
			     "place" => array("hasChildren" => false, "page"=> "place.php", "label" => "Place", "visible" => false),
			     "keynotes" => array("hasChildren" => false, "page"=> "keynotes.php", "label" => "Keynotes", "visible" => false),
			     "registrationFees" => array("hasChildren" => true, "hasLink" => false, "label" => "Inscri&ccedil;&atilde;o", "visible" => false,
							 "children" => array(
							     "registrationFees" => array("page"=> "registrationFeesTest.php", "label" => "Taxas de Inscri&ccedil;&atilde;o"),
							 )
			     ),
    );
    

	/*
	   <li class="dropdown">
	   <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Dropdown <span class="caret"></span></a>
	   <ul class="dropdown-menu">
	   <li><a href="index.php">Action</a></li>
	   <li><a href="#">Another action</a></li>
	   <li><a href="#">Something else here</a></li>
	   <li role="separator" class="divider"></li>
	   <li><a href="#">Separated link</a></li>
	   <li role="separator" class="divider"></li>
	   <li><a href="#">One more separated link</a></li>
	   </ul>
	   </li></ul>
	   
	 */
