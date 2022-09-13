<?php
//$early=true; 
$tooearly=false; 
$singlephase=false;

date_default_timezone_set('America/Sao_Paulo');
$timestamp=time();
$earlytime=mktime(1,0,0,8,16,2017);
If($timestamp > $earlytime) {
    $early=false;
} else {
    $early=true;
}


$closetime=mktime(18,0,0,9,25,2017);
If($timestamp > $closetime) {
    $sysclosed=true;
} else {
    $sysclosed=false;
}

$MAXPAPERS=11; 
$ADMINMAXPAPERS=11; 
$MAXSIZE=1000000;
$NRECORDS=40;
$regpage='Register';
$uploaddir='/export/var/www/sbai17/uploads/';
$receiptdir='/export/var/www/sbai17/recibos/';
$baseurl='https://www.ufrgs.br/sbai17/';
$uploadurl="${baseurl}uploads/";
$receipturl="${baseurl}recibos/";
$debug=false;


$paperstime=mktime(18,0,0,8,15,2017);
If($timestamp > $paperstime) {
    $nopapers=true;
    $MAXPAPERS=0; 
} else {
    $nopapers=false;
}


echo "<script type='text/javascript'>
        var MAXPAPERS = ${MAXPAPERS};
        var MAXSIZE = ${MAXSIZE};
        var EARLY = $early;
";

if ($nopapers) {
    echo "
        var NOPAPERS = true;
";
} else {
    echo "
        var NOPAPERS = false;
";
}



if ($singlephase) {
    echo "  var SinglePhase=true;\n";
} else {
    echo "  var SinglePhase=false;\n";
}
echo "</script>\n";
?>

