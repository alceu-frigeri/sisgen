<?php
require('fpdf.php');
$mysqli = mysqli_init();

if (!$mysqli) {
    die('mysqli_init failed');
}
//   if (!$mysqli->options(MYSQLI_INIT_COMMAND, 'SET AUTOCOMMIT = 0')) {
//       die('Setting MYSQLI_INIT_COMMAND failed');
//   }
if (!$mysqli->options(MYSQLI_OPT_CONNECT_TIMEOUT, 5)) {
    die('Setting MYSQLI_OPT_CONNECT_TIMEOUT failed');
}

if (!$mysqli->real_connect('bdlivre.ufrgs.br', 'sbai17', 'rkefBq4HQTMH', 'sbai17')) {
    die('Connect Error (' . mysqli_connect_errno() . ') '
      . mysqli_connect_error());
}

$mysqli->autocommit(TRUE);

if (!($result = $mysqli->query("SELECT * FROM `acc_papers`,`papers` WHERE acc_id = '$_POST[acc_id]'  AND papers.paper_num = acc_papers.paper_num ORDER BY acc_papers.`paper_num` "))) {
    echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    return NULL;
}
$papers='';
while ($sqlrow=$result->fetch_assoc()) {
    $papers .= "
Artigo   #$sqlrow[paper_num]
Título:  ".utf8_decode($sqlrow[paper_title])."
Autor(es): 


";
    
}




$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(20,15,20);
$pdf->SetAutoPageBreak(true,15);
    $pdf->SetFont('Arial','B',14);
    $pdf->Cell(0,20,'Transferência de Copyright',0,1,'C');
    $pdf->SetFont('Arial','',10);
    $pdf->MultiCell(0,4,"Os direitos de publicação (Copyright) dos artigos abaixo, aceitos para publicação no XIII Simpósio Brasileiro de Automação Inteligente (XIII SBAI), são transferidos para a Sociedade Brasileira de Automática (SBA). A transferência dos direitos de copyright abrange o direito exclusivo da SBA e dos organizadores do XIII SBAI de reproduzir e distribuir o artigo (de forma impressa ou digital), incluindo reimpressões, traduções, reproduções fotográficas, microformatos, formulário eletrônico (on-line, off-line) ou quaisquer outras reproduções de natureza similar. 

Os autores mantém os direitos autorais sobre os artigos, sendo livres para a utilização total ou parcial do conteúdo dos mesmos. A difusão dos artigos em sites Web particulares ou institucionais, pode ser feita desde que juntamente com a menção de 'publicado no XIII Simpósio Brasileiro de Automação Inteligente, 2017'.\n",0,'J');

    $pdf->Cell(10);
    $pdf->Multicell(0,3.5,$papers,0,'J');

    $pdf->MultiCell(0,4,"\nO Autor Signatário desta transferência garante que:

",0,'J');
    $pdf->Cell(10);
    $pdf->MultiCell(0,4,"1.	a contribuição dos artigos a que se refere este copyright são originais e não foram publicadas previamente em outros eventos ou periódicos, não ferindo quaisquer transferências prévias de direitos.

2.	tem plenos poderes para assinar este termo e aceita a responsabilidade pela transferência deste copyright em nome de todo(s) co-autor(es). 

3.	se aplicável, obteve autorização necessária de quem de direito para utilização de qualquer material sobre o qual não é o detentor dos diretos de autor (copyright) nos artigos acima relacionados.
",0,'J');
    $pdf->MultiCell(0,4,"
Data: 

Nome do Autor Signatário:

Assinatura:
",0,'J');

    $pdf->Output();
?>
