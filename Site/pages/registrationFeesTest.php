
<?php $regForm=False; ?>

        <div class="row">
            <div class="col-sm-14">             
                <h2>Etapas de Inscrição </h2>
		<hr>
		<h3> <b>1o.</b> Determine o valor de sua Inscrição conforme o formulário abaixo</h3>
		<h3> <b>2o.</b> Efetive o pagamento via Depósito Bancário na conta indicada abaixo</h3>
		<h3> <b>3o.</b> (de posse do comprovante de Depósito Bancário) Acesse o <a href="https://www.ufrgs.br/sbai17/?q=regform">Formulário de Registro</a></h3>
<br>
 	    </div>

	    <div class="col-sm-14">
                <h2>Instruções Gerais </h2>
                <hr>  

<?php
include "RegDescription.php"
?>


            </div>
            <div class="col-sm-14">
                <h2>Valores </h2>
                <hr>  
<?php
include "regForm1.php"
?>

</div>

<div class="row">

    <div class="col-sm-14">
        <h2>Dados Pagamento</h2>
        <hr>    
      
        <p align="justify">
          Para efetuar o pagamento da inscrição, os participantes deverão depositar o valor correspondente em conta bancária conforme dados a seguir.
        </p>
	<table id="registration" style="width:50%">
	<tr><td style="text-align:right; width:50%">Banco:</td><td style="text-align:left">Bando Do Brasil</td></tr>
	<tr><td style="text-align:right; width:50%">Agencia:</td><td style="text-align:left"><b>3798-2</b></td></tr>
	<tr><td style="text-align:right; width:50%">Conta:</td><td style="text-align:left"><b>302.001-0</b></td></tr>
	<tr><td style="text-align:right; width:50%">CNPJ:</td><td style="text-align:left"><b>92.971.845/0001-42</b></td></tr>
	
	</table>
    </div>


    <div class="col-sm-14">
        <h2>Registro da Inscrição</h2>
        <hr>    
      
        <p align="justify">
          Após realizado o depósito, favor preencher o <a href="https://www.ufrgs.br/sbai17/?q=regform">formulário de registro</a> (submetendo o comprovante de depósito).
	  
        </p>  
    
</div>

</div>

<script type="text/javascript">
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
      (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
      m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-75883469-1', 'auto');
  ga('send', 'pageview');

</script>