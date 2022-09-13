<?php
require('fpdf.php');
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetMargins(22,18,22);
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,20,'Transferência de Copyright',0,1,'C');
$pdf->SetFont('Arial','',9);
$pdf->MultiCell(0,5,"asOs direitos de publicação (Copyright) dos artigos abaixo, aceitos para publicação no XIII Simpósio Brasileiro de Automção Inteligente (XIII SBAI), são transferidos para a Sociedade Brasileira de Automática (SBA). A transferência dos direitos de copyright abrange o direito exclusivo da SBA e dos organizadores do XIII SBAI de reproduzir e distribuir o artigo (de forma impressa ou digital), incluindo reimpressões, traduções, reproduções fotográficas, microformatos, formulário eletrônico (on-line, off-line) ou quaisquer outras reproduções de natureza similar. 

Os autores mantém os direitos autorais sobre os artigos, sendo livres para a utilização total ou parcial do conteúdo dos mesmos. A difusão dos artigos em sites Web particulares ou institucionais, pode ser feita desde que juntamente com a menção de 'publicado no XIII Simpósio Brasileiro de Automação Inteligente, 2017'.\n\n",0,'J');

$pdf->Cell(10);
$pdf->Multicell(0,5,"Artigo # 1234",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Título: <direto do banco>",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Autor(es): <a ser preenchido pelos mesmos>\n\n",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Artigo # 1234",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Título: <direto do banco>",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Autor(es): <a ser preenchido pelos mesmos>\n\n",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Artigo # 1234",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Título: <direto do banco>",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Autor(es): <a ser preenchido pelos mesmos>\n\n",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Artigo # 1234",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Título: <direto do banco>",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Autor(es): <a ser preenchido pelos mesmos>\n\n",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Artigo # 1234",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Título: <direto do banco>",0,'J');
$pdf->Cell(10);
$pdf->Multicell(0,5,"Autor(es): <a ser preenchido pelos mesmos>\n\n",0,'J');

$pdf->MultiCell(0,5,"O Autor Signatário desta transferência garante que:

",0,'J');
$pdf->Cell(10);
$pdf->MultiCell(0,5,"1.	a contribuição dos artigos a que se refere este copyright são originais e não foram publicadas previamente em outros eventos ou periódicos, não ferindo quaisquer transferências prévias de direitos.

2.	tem plenos poderes para assinar este termo e aceita a responsabilidade pela transferência deste copyright em nome de todo(s) co-autor(es). 

3.	se aplicável, obteve autorização necessária de quem de direito para utilização de qualquer material sobre o qual não é o detentor dos diretos de autor (copyright) nos artigos acima relacionados.
",0,'J');
$pdf->MultiCell(0,5,"
Data: 

Nome do Autor Signatário:

Assinatura:
",0,'J');

$pdf->Output();
?>
