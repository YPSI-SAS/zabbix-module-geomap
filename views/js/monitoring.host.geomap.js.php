<?php

/**
 * @var CView $this
 */

?>

<script type="text/javascript">
  var SEVERITY_NO_PROBLEM = -1;
	var SEVERITY_NOT_CLASSIFIED = 0;
	var SEVERITY_INFORMATION = 1;
	var SEVERITY_WARNING = 2;
	var SEVERITY_AVERAGE = 3;
	var SEVERITY_HIGH = 4;
	var SEVERITY_DISASTER = 5;

  $('head').append('<link rel="stylesheet" type="text/css" href="modules/zabbix-module-geomap/views/css/leaflet.css"/>');
  $('head').append('<script type="text/javascript" src="modules/zabbix-module-geomap/views/js/Leaflet/leaflet.js"/>');
  $('head').append('<script type="text/javascript" src="modules/zabbix-module-geomap/views/js/Leaflet/leaflet.markercluster.js"/>');

  var url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  var data = <?php
		  echo json_encode($data['hosts']); ?>;
    
  var map = L.map('map').setView([46.2757268, 0.4013979],8 ); 
    //'http://{s}.google.com/vt/lyrs=s,h&x={x}&y={y}&z={z}'
  L.tileLayer( url , {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap  </a>&copy; <a href="https://www.ypsi.fr/">YPSI</a>',
    //subdomains: ['mt0','mt1','mt2','mt3'],
    maxZoom: 19,
    minZoom:3,
    noWrap: true
  }).addTo( map );

  L.Map.include({
    updateFilter: function (filter_data) {
      this.getContainer().dispatchEvent(new CustomEvent('filter', { detail: filter_data }));
    },

    elmntCounter: (function () {
      let static = 0;
      return function () {
        return ++static;
      }
    })()
  });

  let val = initSeverities();
  var severity_levels = val[0], icons = val[1];

  initMarker(severity_levels, icons, data, map, ["-1","0","1","2","3","4","5"]);
  initFilter(map, severity_levels);  

  
  function initFilter(map, severity_levels){
    L.Control.severityControlControl = L.Control.extend({

    _severity_levels: null,
    _filter_checked: [],

    initialize: function({checked, severity_levels, disabled}) {
      this._filter_checked = checked;
      this._severity_levels = severity_levels;
      this._disabled = disabled;
    },

    onAdd: function(map) {
      const div = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
      const btn = L.DomUtil.create('a', 'geomap-filter-button', div);
      this.bar = L.DomUtil.create('ul', 'checkbox-list geomap-filter', div);

      btn.ariaLabel = t('Severity filter');
      btn.title = t('Severity filter');
      btn.role = 'button';
      btn.href = '#';

      if (!this._disabled) {
        for (const [severity, prop] of this._severity_levels) {
          const li = L.DomUtil.create('li', '', this.bar);
          const chbox = L.DomUtil.create('input', '', li);
          const label = L.DomUtil.create('label', '', li);
          const span = L.DomUtil.create('span', '');
          const chBoxId = 'filter_severity_' + map.elmntCounter();

          label.append(span, document.createTextNode(prop.name));
          chbox.checked = this._filter_checked.includes(severity.toString(10));
          chbox.classList.add('checkbox-radio');
          chbox.type = 'checkbox';
          chbox.value = severity;
          chbox.id = chBoxId;
          label.htmlFor = chBoxId;
        }

        L.DomEvent.on(btn, 'click', () => {this.bar.classList.toggle('collapsed')});
        L.DomEvent.on(this.bar, 'dblclick', (e) => {L.DomEvent.stopPropagation(e)});
        L.DomEvent.on(div, 'change', () => {
          map.updateFilter([...this.bar.querySelectorAll('input[type="checkbox"]:checked')].map(n => n.value));
        });
      }
      else {
        div.classList.add('disabled');
      }

      L.DomEvent.on(btn, 'dblclick', (e) => {L.DomEvent.stopPropagation(e)});

      return div;
    },

    close: function() {
      this.bar.classList.remove('collapsed');
    }
    });

    L.control.severityControl = function(opts) {
    return new L.Control.severityControlControl(opts);
    };

    map.severityFilterControl = L.control.severityControl({
      position: 'topright',
      checked: [],
      severity_levels: severity_levels,
      disabled: false
    }).addTo(map);

    map.getContainer().addEventListener('click', (e) => {
    if (e.target.classList.contains('leaflet-container')) {
      map.severityFilterControl.close();
    }
    }, false);

    map.getContainer().addEventListener('filter', (e) => {
      if(e.detail.length == 0){
        updateMap(["-1","0","1","2","3","4","5"])
      }else{
        updateMap(e.detail)
      }
      
    }, false);
  }

  function updateMap(severity_filter){
    map.eachLayer(function (layer) {
        if (url != layer._url){map.removeLayer(layer)};
    });
    initMarker(severity_levels, icons, data, map, severity_filter);
  }

  function onClickMarker(e) {
    map.setView(this.getLatLng(), 14);
  }


  function initMarker(severity_levels, icons, data, map, severity_filter) {
    var marker_clusters = L.markerClusterGroup({
      iconCreateFunction: function(cluster){
        const markers = cluster.getAllChildMarkers();
		    const childCount = cluster.getChildCount();
        var list_severity = []
        var color = "";      
        for(const element of markers){
          var severity=-1;
          switch (element.options.className) {
            case 'notClassifiedClass':
              severity = 0;
              break;
            case 'informationClass':
              severity = 1;
              break;
            case 'warningClass':
              severity = 2;
              break;
            case 'averageClass':
              severity = 3;
              break;
            case 'highClass':
              severity = 4;
              break;
            case 'disasterClass':
              severity = 5;
              break;
          }
          if(!list_severity.includes(severity)){
            list_severity.push(severity)
          }
        }
        var max_severity = Math.max(...list_severity)
        if(list_severity.length==0){
          max_severity=-1;
        }
        color = severity_levels.get(max_severity).color;
        return new L.DivIcon({
					html: `
						<div style="background-color: ${color};text-align: center;border-radius: 50%;margin-left: 5px;margin-top: 5px;height: 40px;width: 40px;">
							<span style="line-height: 40px;font-size:20px">${childCount}</span>
						</div>`,
					className: 'geomap-cluster',
					iconSize: new L.Point(40, 40)
				});
      }
    });

    for(var i=0; i<data.length; ++i){
      var list_severity = []
      var nb_not_classified = 0;
      var nb_information = 0;
      var nb_warning = 0;
      var nb_average = 0;
      var nb_high = 0;
      var nb_disaster = 0;
      for(var j=0; j<data[i]['problems'].length; j++){
        const severity = parseInt(data[i]['problems'][j]['severity']);
        if(!list_severity.includes(severity)){
          list_severity.push(severity)
        }
        switch (severity) {
          case 0:
            ++nb_not_classified;
            break;
          case 1:
            ++nb_information;
            break;
          case 2:
            ++nb_warning;
            break;
          case 3:
            ++nb_average;
            break;
          case 4:
            ++nb_high;
            break;
          case 5:
            ++nb_disaster;
            break;
        }
      }
      var max_severity = Math.max(...list_severity)
      if(list_severity.length==0){
        max_severity=-1;
      }

      console.log(max_severity.toString())
      console.log(severity_filter)
      if(severity_filter.includes(max_severity.toString())){
        const host = {
          'name': data[i]['name'],
          0: nb_not_classified,
          1: nb_information,
          2: nb_warning,
          3: nb_average,
          4: nb_high,
          5: nb_disaster
        }
        
        const popup = makePopupContent([host], severity_levels)
        var m = L.marker( [data[i]['inventory']['location_lat'], data[i]['inventory']['location_lon']], {icon: icons[max_severity], className: severity_levels.get(max_severity).classMarker, hostVal: host})
                .bindPopup( popup ).on('click', onClickMarker);

        m.on("mouseover", function(e){
          this.openPopup();
        });
        m.on("mouseout", function(e){
          this.closePopup();
        });
        marker_clusters.addLayer(m);
      }
    }

    marker_clusters.on('clustermouseover', function(c) {
      var markers = c.layer.getAllChildMarkers();
      var hosts = []
      for(const element of markers){
        const host = element.options.hostVal;
        hosts.push(host);
      }
      var textPopup = makePopupContent(hosts, severity_levels);
      var popup = L.popup().setLatLng(c.layer.getLatLng()).setContent(textPopup).openOn(map);

    }).on('clustermouseout',function(c){
      map.closePopup();
    }).on('clusterclick',function(c){
      map.closePopup();
    }); 
    
    map.addLayer(marker_clusters);
  }

  function makePopupContent(hosts, severity_levels){
    const makeDataCell = (host, severity) => {
			if (host[severity]!=0) {
				const style = severity_levels.get(severity).class;
				const problems = host[severity];
				return `<td class="${style}">${problems}</td>`;
			}
			else {
				return `<td></td>`;
			}
		};

    const makeTableRows = () => {
			hosts.sort((a, b) => {
				if (a['name'] < b['name']) {
					return -1;
				}
				if (a['name'] > b['name']) {
					return 1;
				}
				return 0;
			});

			let rows = ``;
			hosts.forEach(host => {
				rows += `
					<tr>
						<td class="nowrap">${host['name']}</td>
						${makeDataCell(host, SEVERITY_DISASTER, severity_levels)}
						${makeDataCell(host, SEVERITY_HIGH, severity_levels)}
						${makeDataCell(host, SEVERITY_AVERAGE, severity_levels)}
						${makeDataCell(host, SEVERITY_WARNING, severity_levels)}
						${makeDataCell(host, SEVERITY_INFORMATION, severity_levels)}
						${makeDataCell(host, SEVERITY_NOT_CLASSIFIED, severity_levels)}
					</tr>`;
			});

			return rows;
		};

    var html = `
			<table class="list-table">
			<thead>
			<tr>
				<th>Host</th>
				<th>${severity_levels.get(SEVERITY_DISASTER).abbr}</th>
				<th>${severity_levels.get(SEVERITY_HIGH).abbr}</th>
				<th>${severity_levels.get(SEVERITY_AVERAGE).abbr}</th>
				<th>${severity_levels.get(SEVERITY_WARNING).abbr}</th>
				<th>${severity_levels.get(SEVERITY_INFORMATION).abbr}</th>
				<th>${severity_levels.get(SEVERITY_NOT_CLASSIFIED).abbr}</th>
			</th>
			</thead>
			<tbody>${makeTableRows()}</tbody>
			</table>`;

    return html
  }

  function initSeverities() {
    const severity_levels = new Map();
    severity_levels.set(SEVERITY_NO_PROBLEM, {
			name: t('No problem'),
			abbr: t('O'),
			class: 'na-bg',
      color: '#86cc89',
      classMarker: 'noProblemClass'
		});
		severity_levels.set(SEVERITY_NOT_CLASSIFIED, {
			name: t('Not classified'),
			abbr: t('N'),
			class: 'na-bg',
      color: '#97aab3',
      classMarker: 'notClassifiedClass'
		});
		severity_levels.set(SEVERITY_INFORMATION, {
			name: t('Information'),
			abbr: t('I'),
			class: 'info-bg',
      color: '#7499ff',
      classMarker: 'informationClass'
		});
		severity_levels.set(SEVERITY_WARNING, {
			name: t('Warning'),
			abbr: t('W'),
			class: 'warning-bg',
      color: '#ffc859',
      classMarker: 'warningClass'
		});
		severity_levels.set(SEVERITY_AVERAGE, {
			name: t('Average'),
			abbr: t('A'),
			class: 'average-bg',
      color: '#ffa059',
      classMarker: 'averageClass'
		});
		severity_levels.set(SEVERITY_HIGH, {
			name: t('High'),
			abbr: t('H'),
			class: 'high-bg',
      color: '#e97659',
      classMarker:'highClass'
		});
		severity_levels.set(SEVERITY_DISASTER, {
			name: t('Disaster'),
			abbr: t('D'),
			class: 'disaster-bg',
      color: '#e45959',
      classMarker: 'disasterClass'
		});

    const icons = {}
    icons[SEVERITY_NOT_CLASSIFIED] = L.icon({
			iconUrl: 'modules/zabbix-module-geomap/views/images/marker-icon-2x-grey.png',
			shadowUrl: 'modules/zabbix-module-geomap/views/images/marker-shadow.png',
			iconSize: [30, 50],
		  iconAnchor: [12, 41],
		  popupAnchor: [1, -34],
		  shadowSize: [41, 41],
			shadowAnchor: [13, 40]
		});
    icons[SEVERITY_INFORMATION] = L.icon({
			iconUrl: 'modules/zabbix-module-geomap/views/images/marker-icon-2x-blue.png',
			shadowUrl: 'modules/zabbix-module-geomap/views/images/marker-shadow.png',
			iconSize: [30, 50],
		  iconAnchor: [12, 41],
		  popupAnchor: [1, -34],
		  shadowSize: [41, 41],
			shadowAnchor: [13, 40]
		});
    icons[SEVERITY_WARNING] = L.icon({
			iconUrl: 'modules/zabbix-module-geomap/views/images/marker-icon-2x-gold.png',
			shadowUrl: 'modules/zabbix-module-geomap/views/images/marker-shadow.png',
			iconSize: [30, 50],
		  iconAnchor: [12, 41],
		  popupAnchor: [1, -34],
		  shadowSize: [41, 41],
			shadowAnchor: [13, 40]
		});
    icons[SEVERITY_AVERAGE] = L.icon({
			iconUrl: 'modules/zabbix-module-geomap/views/images/marker-icon-2x-yellow.png',
			shadowUrl: 'modules/zabbix-module-geomap/views/images/marker-shadow.png',
			iconSize: [30, 50],
		  iconAnchor: [12, 41],
		  popupAnchor: [1, -34],
		  shadowSize: [41, 41],
			shadowAnchor: [13, 40]
		});
    icons[SEVERITY_HIGH] = L.icon({
			iconUrl: 'modules/zabbix-module-geomap/views/images/marker-icon-2x-orange.png',
			shadowUrl: 'modules/zabbix-module-geomap/views/images/marker-shadow.png',
			iconSize: [30, 50],
		  iconAnchor: [12, 41],
		  popupAnchor: [1, -34],
		  shadowSize: [41, 41],
			shadowAnchor: [13, 40]
		});
    icons[SEVERITY_DISASTER] = L.icon({
			iconUrl: 'modules/zabbix-module-geomap/views/images/marker-icon-2x-red.png',
			shadowUrl: 'modules/zabbix-module-geomap/views/images/marker-shadow.png',
			iconSize: [30, 50],
		  iconAnchor: [12, 41],
		  popupAnchor: [1, -34],
		  shadowSize: [41, 41],
			shadowAnchor: [13, 40]
		});
    icons[SEVERITY_NO_PROBLEM] = L.icon({
			iconUrl: 'modules/zabbix-module-geomap/views/images/marker-icon-2x-green.png',
			shadowUrl: 'modules/zabbix-module-geomap/views/images/marker-shadow.png',
			iconSize: [30, 50],
		  iconAnchor: [12, 41],
		  popupAnchor: [1, -34],
		  shadowSize: [41, 41],
			shadowAnchor: [13, 40]
		});
    return [severity_levels, icons];
	}

  

</script>
