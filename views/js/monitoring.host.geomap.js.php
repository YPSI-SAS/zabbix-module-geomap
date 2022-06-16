<?php

/**
 * @var CView $this
 */

?>

<script type="text/javascript">
  //All global severities variables
  var SEVERITY_NO_PROBLEM = -1;
	var SEVERITY_NOT_CLASSIFIED = 0;
	var SEVERITY_INFORMATION = 1;
	var SEVERITY_WARNING = 2;
	var SEVERITY_AVERAGE = 3;
	var SEVERITY_HIGH = 4;
	var SEVERITY_DISASTER = 5;

  //All JS and CSS import
  $('head').append('<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css"/>');
  $('head').append('<link rel="stylesheet" type="text/css" href="modules/zabbix-module-geomap/views/css/style.css"/>');
  $('head').append('<script type="text/javascript" src="modules/zabbix-module-geomap/views/js/Leaflet/leaflet.js"/>');
  $('head').append('<script type="text/javascript" src="modules/zabbix-module-geomap/views/js/Mask.js"/>');
  $('head').append('<script type="text/javascript" src="modules/zabbix-module-geomap/views/js/Control.js"/>');
  $('head').append('<script type="text/javascript" src="modules/zabbix-module-geomap/views/js/Leaflet/leaflet.markercluster.js"/>');

  //Callback to read geojson file 
  var getJSON = function (url, callback) {
    var xhr = new XMLHttpRequest();
    xhr.open('GET', url, true);
    xhr.responseType = 'json';
    xhr.onload = function () {
        var status = xhr.status;
        if (status === 200) {
          callback(null, xhr.response);
        } else {
          callback(status, xhr.response);
        }
    };
    xhr.send();
  };

  //List of hosts get by action PHP file
  var hosts = <?php echo json_encode($data['hosts']); ?>;

  //List of departments
  var departments = [{name:"ALL DEPARTMENTS", code:"00"},{name:"ain", code:"01"}, {name:"aisne", code:"02"}, {name:"allier", code:"03"}, {name:"alpes-de-haute-provence", code:"04"}, {name:"hautes-alpes", code:"05"}, {name:"alpes-maritimes", code:"06"}, {name:"corse-du-sud", code:"2A"}, {name:"haute-corse", code:"2B"}, {name:"ardeche", code:"07"}, , {name:"ardennes", code:"08"}, , {name:"ariege", code:"09"}, {name:"aube", code:"10"}, {name:"aude", code:"11"}, {name:"aveyron", code:"12"}, {name:"bouches-du-rhone", code:"13"}, {name:"calvados", code:"14"}, {name:"cantal", code:"15"}, {name:"charente", code:"16"}, {name:"charente-maritime", code:"17"}, {name:"cher", code:"18"}, {name:"correze", code:"19"}, {name:"cote-d-or", code:"21"}, {name:"cotes-d-armor", code:"22"}, {name:"creuse", code:"23"}, {name:"dordogne", code:"24"}, {name:"doubs", code:"25"}, {name:"drome", code:"26"}, {name:"eure", code:"27"}, {name:"eure-et-loir", code:"28"}, {name:"finistere", code:"29"}, {name:"gard", code:"30"}, {name:"haute-garonne", code:"31"}, {name:"gers", code:"32"}, {name:"gironde", code:"33"}, {name:"herault", code:"34"}, {name:"ille-et-vilaine", code:"35"}, {name:"indre", code:"36"}, {name:"indre-et-loire", code:"37"}, {name:"isere", code:"38"}, {name:"jura", code:"39"}, {name:"landes", code:"40"}, {name:"loir-et-cher", code:"41"}, {name:"loire", code:"42"}, {name:"haute-loire", code:"43"}, {name:"loire-atlantique", code:"44"}, {name:"loiret", code:"45"}, {name:"lot", code:"46"}, {name:"lot-et-garonne", code:"47"}, {name:"lozere", code:"48"}, {name:"maine-et-loire", code:"49"}, {name:"manche", code:"50"}, {name:"marne", code:"51"}, {name:"haute-marne", code:"52"}, {name:"mayenne", code:"53"}, {name:"meurthe-et-moselle", code:"54"}, {name:"meuse", code:"55"}, {name:"morbihan", code:"56"}, {name:"moselle", code:"57"}, {name:"nievre", code:"58"}, {name:"nord", code:"59"}, {name:"oise", code:"60"}, {name:"orne", code:"61"}, {name:"pas-de-calais", code:"62"}, {name:"puy-de-dome", code:"63"}, {name:"pyrenees-atlantiques", code:"64"},, {name:"hautes-pyrenees", code:"65"}, {name:"pyrenees-orientales", code:"66"}, {name:"bas-rhin", code:"67"}, {name:"haut-rhin", code:"68"}, {name:"rhone", code:"69"}, {name:"haute-saone", code:"70"}, {name:"saone-et-loire", code:"71"}, {name:"sarthe", code:"72"}, {name:"savoie", code:"73"}, {name:"haute-savoie", code:"74"}, {name:"paris", code:"75"}, {name:"seine-maritime", code:"76"}, {name:"seine-et-marne", code:"77"}, {name:"yvelines", code:"78"}, {name:"deux-sevres", code:"79"}, {name:"somme", code:"80"}, {name:"tarn", code:"81"}, {name:"tarn-et-garonne", code:"82"}, {name:"var", code:"83"}, {name:"vaucluse", code:"84"}, {name:"vendee", code:"85"}, {name:"vienne", code:"86"}, {name:"haute-vienne", code:"87"}, {name:"vosges", code:"88"}, {name:"yonne", code:"89"}, {name:"territoire-de-belfort", code:"90"}, {name:"essonne", code:"91"}, {name:"hauts-de-seine", code:"92"}, {name:"seine-saint-denis", code:"93"}, {name:"val-de-marne", code:"94"}, {name:"val-d-oise", code:"95"}, {name:"guadeloupe", code:"971"}, {name:"martinique", code:"972"}, {name:"guyane", code:"973"}, {name:"la-reunion", code:"974"}, {name:"mayotte", code:"976"}];
  
  //List of regions
  var regions = [{name:"ALL REGIONS"}, {name:"auvergne-rhone-alpes"}, {name:"bourgogne-franche-comte"}, {name:"bretagne"}, {name:"centre-val-de-loire"}, {name:"corse"}, {name:"grand-est"}, {name:"guadeloupe"}, {name:"guyane"}, {name:"hauts-de-france"}, {name:"ile-de-france"}, {name:"la-reunion"}, {name:"martinique"}, {name:"mayotte"}, {name:"normandie"}, {name:"nouvelle-aquitaine"}, {name:"occitanie"}, {name:"pays-de-la-loire"}, {name:"provence-alpes-cote-d-azur"}];
  
  //Name of department selected (default: all departments)
  var department_selected = "00-ALL DEPARTMENTS";

  //Url for geojson file for selected department
  var url_department_selected = "";

  //Name of region selected (default: all regions)
  var region_selected = "ALL REGIONS";

  //Url for geojson file for selected region
  var url_region_selected = "";

  var last_element_choose = "";

  //List of all severity selected
  var severity_selected = ["-1","0","1","2","3","4","5"];

  //List of polygons corresponding to department or region selected
  var polygons = new Array();


  //Create a geographical map
  var url = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  var map = L.map('map').setView([46.4336, 2.640771],6); 
  L.tileLayer( url , {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap  </a>&copy; <a href="https://www.ypsi.fr/">YPSI</a>',
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

  //Initialize all severities and all icons
  let val = initSeveritiesAndIcons();
  var severity_levels = val[0], icons = val[1];

  //Initialize all elements on a map
  initMarker();
  initSearchBar(map); 
  initSelectDepartment(map, departments);
  initSelectRegion(map, regions);
  initClearDepartmentAndRegion(map);
  initFilter(map, severity_levels);
  
  /**
   * Search the host ask by user in search bar
   */
  function search(){
    var saisie=document.getElementById("input").value;
    for(var i=0; i<hosts.length; i++){
      var name = hosts[i]['name'];
      if(name.toLowerCase()==saisie.toLowerCase()){
        document.getElementById("input").value = '';
        url_region_selected = "";
        url_department_selected = "";
        updateMap();
        map.setView([hosts[i]['inventory']['location_lat'], hosts[i]['inventory']['location_lon']], 14)
      }
    }
  }

  /**
   * Initialize search bar with control
   * @param {*} map 
   */
  function initSearchBar(map){
    map.searchFilterControl = L.control.searchControl({
      position: 'topright',
      hosts: hosts,
    }).addTo(map);
  }
  
  /**
   * Initialize button filter severities with control
   * @param {*} map
   * @param {Map} severity_levels  
   */
  function initFilter(map, severity_levels){
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
  }

  /**
   * Initialize select department with control
   * @param {*} map
   * @param {Array} departments
   */
  function initSelectDepartment(map, departments){
    map.departmentFilterControl = L.control.departmentControl({
      position: 'topright',
      departments: departments,
    }).addTo(map);
  }

  /**
   * Initalize select region with control
   * @param {*} map
   * @param {Array} regions
   */
  function initSelectRegion(map, regions){
    map.regionFilterControl = L.control.regionControl({
      position: 'topright',
      regions: regions,
    }).addTo(map);
  }

  /**
   * Initalize clear button with control
   * @param {*} map
   */
  function initClearDepartmentAndRegion(map){
    map.clearFilterControl = L.control.clearControl({
      position: 'topright',
    }).addTo(map);
  }

  /**
   * Clear the filters departments and regions to initial values
   */
  function clearDepartmentAndRegion(){
    url_region_selected = "";
    url_department_selected = "";
    document.getElementById("select-dep").value = departments[0].code+"-"+departments[0].name;
    document.getElementById("select-region").value = regions[0].name;
    updateMap();
    map.setView([46.4336, 2.640771],6);
  }

  /**
   * Get geoJSON file for selected department and update MAP
   */
  function getFileDepartment(){
    department_selected=document.getElementById("select-dep").value;
    default_val = departments[0].code+"-"+departments[0].name
    if(department_selected != default_val){
      url_department_selected = "modules/zabbix-module-geomap/resources/departements/".concat('', department_selected.concat('',"/departement-".concat("",department_selected.concat("",".geojson"))));
      last_element_choose = "department";
    }else{
      url_department_selected = "";
      last_element_choose = "";
    }
    updateMap();
  }

  /**
   * Get geoJSON file for selected region and update MAP
   */
  function getFileRegion(){
    region_selected=document.getElementById("select-region").value;
    default_val = regions[0].name
    if(region_selected != default_val){
      url_region_selected = "modules/zabbix-module-geomap/resources/regions/".concat('', region_selected.concat('',"/region-".concat("",region_selected.concat("",".geojson"))));
      last_element_choose = "region";
    }else{
      url_region_selected = "";
      last_element_choose = "";
    }
    updateMap();
  }

  /**
   * Set mask on MAP depending if a region or a department is selected
   * @param {String} url_file
   */
  function setMaskOnMap(url_file){
    if(url_file != ""){
      getJSON(url_file, function (err, values) {
        if (err !== null) {
          console.log(err)
          console.log('Something went wrong: ' + err);
        } else {
          showGeoJSON(values);
        }
      });
    }else{
      showGeoJSON(null);
    }   
  }
  
  /**
   * Show geoJSON file selected and initialize marker
   * @param {Array | null} values
   */
  function showGeoJSON(values) {
    var latLngs=[];
    if(values!=null){
      //Display geoJSON and mask in MAP
      var geoJSON = new L.geoJSON(values, options).addTo(map);
      latLngs.push(geoJSON.getLayers()[0].getLatLngs());
      L.mask(latLngs).addTo(map);

      //Get polygons elements in geoJSON
      polygons = latLngs[0];

      //Set view on the first LatLng object in list
      var object = latLngs[0][0][0];
      if(Array.isArray(object)){
        object = object[0];
      }
      map.setView(new L.LatLng(object.lat, object.lng), 8);
    }else{
      L.maskReset(latLngs).addTo(map);
      polygons = new Array();
    }
    initMarker();
  }

  /**
   * Update map after filter changements
   */
  function updateMap(){
    //Reset layer on MAP
    map.eachLayer(function (layer) {
        if (url != layer._url){map.removeLayer(layer)};
    });
    //Get URL of geoJSON file
    var url_file = "";
    if(last_element_choose =="department"){
      url_file = url_department_selected;
    }else if(last_element_choose =="region"){
      url_file = url_region_selected;
    }
    setMaskOnMap(url_file); 
  }

  /**
   * Zoom on marker selected when user make simple click on it
   */
  function onClickMarker(e) {
    map.setView(this.getLatLng(), 14);
  }

  /**
   * Open information about the host when user make double click on it
   */
  function onDoubleClickMarker(e) {
    host = e.sourceTarget.options.hostVal
    url = window.location.origin+window.location.pathname+"?name="+host['name']+"&action=host.view"
    window.open(url, '_blank').focus();
  }

  /**
   * Calculate if marker is inside a polygon
   * @param {float} x
   * @param {float} y
   * @param {Array} poly
   */
  function isMarkerInsidePolygon(x, y, poly) {
    var inside = false;
    for (var i = 0, j = poly.length - 1; i < poly.length; j = i++) {
        var xi = poly[i].lat, yi = poly[i].lng;
        var xj = poly[j].lat, yj = poly[j].lng;

        var intersect = ((yi > y) != (yj > y))
            && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) inside = !inside;
    }
    return inside;
  };

  /**
   * Initialize markers on MAP
   */
  function initMarker() {
    //Create clusters of markers
    var marker_clusters = L.markerClusterGroup({
      iconCreateFunction: function(cluster){
        const markers = cluster.getAllChildMarkers();
		    const childCount = cluster.getChildCount();

        //Set severities list depending host in cluster
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
          //Add severity only one time in list
          if(!list_severity.includes(severity)){
            list_severity.push(severity)
          }
        }

        //Get the most import severity
        var max_severity = Math.max(...list_severity)
        if(list_severity.length==0){
          max_severity=-1;
        }

        //Get color of the max severity
        color = severity_levels.get(max_severity).color;
        //Create div icon on map
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

    //Create marker and popup for all hosts
    for(var i=0; i<hosts.length; ++i){
      //Calculate how many problems in each severity
      var list_severity = []
      var nb_not_classified = 0;
      var nb_information = 0;
      var nb_warning = 0;
      var nb_average = 0;
      var nb_high = 0;
      var nb_disaster = 0;
      for(var j=0; j<hosts[i]['problems'].length; j++){
        const severity = parseInt(hosts[i]['problems'][j]['severity']);
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

      //Get the most important severity
      var max_severity = Math.max(...list_severity)
      if(list_severity.length==0){
        max_severity=-1;
      }

      //if the most important severity is in severity selected by the user
      if(severity_selected.includes(max_severity.toString())){
        var inside = false;
        //Check if host is in polygons of department or region selected
        if(polygons.length != 0){
          for(var m=0; m<polygons.length; m++){
            var poly = polygons[m];
            if(Array.isArray(poly[0])){
              poly = poly[0];
            }
            if(isMarkerInsidePolygon(hosts[i]['inventory']['location_lat'], hosts[i]['inventory']['location_lon'], poly)){
              inside = true;
            }
          }
        }else{
          //If polygons list is empty => all hosts are displayed
          inside = true;
        }
        
        if(inside){
          //Create host with essential information
          const host = {
            'id': hosts[i]['hostid'],
            'name': hosts[i]['name'],
            0: nb_not_classified,
            1: nb_information,
            2: nb_warning,
            3: nb_average,
            4: nb_high,
            5: nb_disaster
          }

          //Create popup
          const popup = makePopupContent([host], severity_levels);

          //Create Marker
          var m = L.marker( [hosts[i]['inventory']['location_lat'], hosts[i]['inventory']['location_lon']], {icon: icons[max_severity], className: severity_levels.get(max_severity).classMarker, hostVal: host})
                  .bindPopup( popup ).on('click', onClickMarker).on('dblclick', onDoubleClickMarker);

          //Open popup if mouse over
          m.on("mouseover", function(e){
            this.openPopup();
          });

          //Close popup if mouse out
          m.on("mouseout", function(e){
            this.closePopup();
          });

          //Add marker in clusters
          marker_clusters.addLayer(m);
        }
        
      }
    }

    //Create and open popup if mouse over
    marker_clusters.on('clustermouseover', function(c) {
      var markers = c.layer.getAllChildMarkers();
      var hosts = []
      for(const element of markers){
        const host = element.options.hostVal;
        hosts.push(host);
      }
      var textPopup = makePopupContent(hosts, severity_levels);
      var popup = L.popup().setLatLng(c.layer.getLatLng()).setContent(textPopup).openOn(map);

    })

    //Close popup if mouse out
    marker_clusters.on('clustermouseout',function(c){
      map.closePopup();
    })

    //Close popup if cluster is clicked
    marker_clusters.on('clusterclick',function(c){
      map.closePopup();
    }); 
    
    //Add cluster in MAP
    map.addLayer(marker_clusters);
  }

  /**
   * Create popup for host or cluster
   * @param {Array} hosts
   * @param {Map} severity_levels
   */
  function makePopupContent(hosts, severity_levels){
    /**
     * Make cell information in table row
     */
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

    /**
     * Make row in table
     */
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

    //Make table
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

  /**
   * Initialize severities and icons
   */
  function initSeveritiesAndIcons() {
    //Add all severity and his information in Map
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

    //Add all possibile icons depending severity
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
