<?php
include "how_to_get_to_poa.php";
?>

<style>
 #map {
     width: 500px;
     height: 400px;

 }
</style>

<div class="row">
    <div class="col-sm-14">
	<h2>Hospedagem e Turismo</h2>
	<hr>
	A agência de viagem Innovare Turismo é a agência oficial do SBAI 2017. 
	No link abaixo são apresentadas opções de hospedagem, reserva de passagens aéreas, transfers e passeios guiados (não inclusos na taxa de inscrição do evento).</p>

	<a target='_BLANK' href="https://www.innovareturismo.com.br/turismo-sbai-2017-pt">Innovare Turismo</a>

	<h2>Local</h2>
	<hr>  

	<p align="justify">O SBAI17 realizar-se-á no Teatro e salas de aula do prédio 40 da PUCRS  <a href="?q=place">Porto Alegre</a>, Rio Grande do Sul.
	    <!-- e (<a onclick="modal()" href="javascript:void(0);">clique aqui para informações sobre como chegar em Porto Alegre</a>), capital do Rio Grande do Sul.-->

	    <OL type="A">
		<li>	Estrutura para Eventos:</li>
		O Centro de Eventos da PUCRS, CEPUC, possui auditório para 3.000 pessoas, que pode ser dividido em até cinco salas distintas. Um espaço de cerca de 3.000m² pode ser usado para feiras e exposições. O complexo também dispõe ainda de um teatro para 536 pessoas, diversas salas para cerca de 100 pessoas, gerador próprio, equipamentos audiovisuais e multimídia, internet banda larga, serviços como farmácia, bancos, correio, livrarias, lojas, restaurantes e lanchonetes e estacionamento para 4.000 veículos. O mesmo está localizado junto ao amplo campus da PUCRS, Hospital São Lucas e Museu de Ciência e Tecnologia, distante 11 km do aeroporto e apenas 9 km do centro da cidade.
		<center>
		    <div id="map"></div>
		</center>

		<li>Mapas de Acesso Local</li>
		<ul>
		    <li>Acesso ao Prédio 40 (local de realização do Simpósio)
			<img class="img-responsive maps" src="./images/MapaCampus03.png" alt="Mapa Campus PUC">
		    </li>
		    <li>Acesso ao local de realização do Coquetel de Abertura.
			<img class="img-responsive maps" src="./images/MapaCoquetel.png" alt="Mapa Coquetel">
		    </li>
		    <li>Localização das salas.
			<img class="img-responsive maps" src="./images/MapaFoyer.png" alt="Mapa Foyer">
		    </li>
		    <li>Mapa geral do Prédio 40.
			<img class="img-responsive maps" src="./images/MapaPredio40.png" alt="Mapa Prédio 40">
		    </li>
		</ul>

		<li> Estacionamento Local:</li>
		A PUCRS tem a disposição uma série de estacionamentos pagos, com <a target="_BLANK" href="http://www.pucrs.br/campus/#estacionamento">controle informatizado de ocupação</a>
	    </OL>

	    <br>

    </div>

    <script src="https://maps.googleapis.com/maps/api/js"></script>
    <script> 
     function modal(){ 
	 $("#howToGetPoaModal").modal();
     }
     function initialize() {
	 var mapCanvas = document.getElementById('map');
	 var mapOptions = {
	     center: new google.maps.LatLng(-30.0596689,-51.1784437),
	     zoom: 12,
	     mapTypeId: google.maps.MapTypeId.ROADMAP
	 }
	 var map = new google.maps.Map(mapCanvas, mapOptions);
	 var marker = new google.maps.Marker({
             // The below line is equivalent to writing:
             // position: new google.maps.LatLng(-34.397, 150.644)
             position: {lat: -30.059674, lng: -51.176255},
             map: map
         });
	 var infowindow = new google.maps.InfoWindow({
             content: '<center><a href="http://www.pucrs.br/institucional/a-universidade/o-campus/" target=_blank>Centro de Eventos da PUCRS</a></p><img src="/sbai17/images/PUC-campus-central.jpg" height="60">'
         });
	 google.maps.event.addListener(marker, 'click', function() {
             infowindow.open(map, marker);
         });
     } 
     initialize();
    </script>
</div>

<script type="text/javascript">
 (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
     (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
 })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

 ga('create', 'UA-75883469-1', 'auto');
 ga('send', 'pageview');

</script>
