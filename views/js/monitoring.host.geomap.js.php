<?php

/**
 * @var CView $this
 */

?>

<script type="text/javascript">
	// Transfer information about groups from PHP into JavaScript data Object
	var data = <?php
		echo json_encode($data['hosts']); ?>;

  $('head').append('<link rel="stylesheet" type="text/css" href="modules/zabbix-module-geomap/views/css/leaflet.css"/>');

  $('head').append('<script type="text/javascript" src="modules/zabbix-module-geomap/views/js/Leaflet/leaflet.js"/>');

	
  var map = L.map('map').setView([46.2757268, 0.4013979],8 ); 
    L.tileLayer( 'http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap  </a>&copy; <a href="https://www.ypsi.fr/">YPSI</a>',
    subdomains: ['mt0','mt1','mt2','mt3'],
    maxZoom: 19,
    minZoom:3,
    noWrap: true
  }).addTo( map );

</script>
