	
<?php 
if (!isset($GBL_index)) {  // it SHOULD'T be here !
    session_start();
    session_destroy();
    echo '<script type="text/javascript">' .
        "setInterval('location.replace(\"/sisgen/\")', 100)" .
        '</script>' .
        '<div class = "row">' .
        '<h2>Invalid Link. </h2>' .
        '<hr></div>' ; 
    exit();
}
?>



