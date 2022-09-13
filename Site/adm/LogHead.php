<?php include '../pages/FLAGS.php' ?>

<?php
$register = 'receipt verified,';
for ($i=1;$i<=$MAXPAPERS;$i++) {
    $register .= 'paper '.$i.' confirmed,';
}
$register .= 'type,';
$register .= 'sba,';
$register .= 'mod,';
$register .= 'npapers,';
$register .= 'value,';
$register .= 'title,';
$register .= 'name,';
$register .= 'family name,';
$register .= 'full name,';
$register .= 'afiliation,';
$register .= 'phone,';
$register .= 'email,';
$register .= 'country,';
for ($i=1;$i<=$MAXPAPERS;$i++) {
    $register .= 'paper '.$i.',';
    $register .= 'extra '.$i.',';
    $register .= 'speaker '.$i.',';
}
$register .= 'date,';
$register .= 'receipt file,';
$register .= 'student file,';
$register .= 'sba file,';
$register .= "\n";

$filename = '/export/var/www/sbai17/logs/registration.csv';
$log = fopen($filename, "a");
fwrite($log, $register);
fclose($log);

?>
<h3>Cabeçalho Inserido no Arquivo de Logs !</h3><p>
