<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>Display buildings in 3D</title>
    <base href="{{ asset('') }}">
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.js"></script>
    <link href="https://unpkg.com/babox-plugin/dist/babox.css" rel="stylesheet" />
    <link rel="stylesheet"
        href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css"
        type="text/css">
    <link href="https://unpkg.com/threebox-plugin/dist/threebox.css" rel="stylesheet" />
    <script src="https://unpkg.com/threebox-plugin/dist/threebox.min.js" type="text/javascript"></script>
  
    <style>
        body,
        html {
            width: 100%;
            height: 100%;
            margin: 0;
        }

        #map {
            width: 100%;
            height: 100%;
        }

        #menu {
            background: #fff;
            position: absolute;
            z-index: 1;
            top: 10px;
            right: 10px;
            border-radius: 3px;
            width: 120px;
            border: 1px solid rgba(0, 0, 0, 0.4);
            font-family: 'Open Sans', sans-serif;
        }

        #menu a {
            font-size: 13px;
            color: #404040;
            display: block;
            margin: 0;
            padding: 0;
            padding: 10px;
            text-decoration: none;
            border-bottom: 1px solid rgba(0, 0, 0, 0.25);
            text-align: center;
        }

        #menu a:last-child {
            border: none;
        }

        #menu a:hover {
            background-color: #f8f8f8;
            color: #404040;
        }

        #menu a.active {
            background-color: #3887be;
            color: #ffffff;
        }

        #menu a.active:hover {
            background: #3074a4;
        }

        #info {
            display: table;
            position: relative;
            margin: 0px auto;
            word-wrap: anywhere;
            white-space: pre-wrap;
            padding: 10px;
            border: none;
            border-radius: 3px;
            font-size: 12px;
            text-align: center;
            color: #222;
            background: #fff;
        }
        .mapboxgl-popup {
            max-width: 400px;
            font: 12px/20px 'Helvetica Neue', Arial, Helvetica, sans-serif;
        }
        .marker {
            display: block;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            padding: 0;
        }
        #geocoder-container > div {
            min-width: 50%;
            margin-left: 25%;
            }
    </style>
</head>

<body>
    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js">
    </script>
    <link rel="stylesheet"
        href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css"
        type="text/css">
    <nav id="menu"></nav>
    
    <div id="map"></div>
    {{--  <pre id="info"></pre>  --}}
    <script type="module">
        mapboxgl.accessToken = "pk.eyJ1IjoicXVhbmduZDEwMDMiLCJhIjoiY2w0aHQ3aWFoMDh2YzNlazc5YzhlcmlsciJ9.bmvnKogFKsfOuOscG-2q1A";
        var map = (window.map = new mapboxgl.Map({
                    container: 'map',
                    style: 'mapbox://styles/mapbox/navigation-night-v1',
                    zoom: 18,
                    center: [-87.61694, 41.86625],
                    pitch: 60,
                    hash: true,
                    antialias: true // create the gl context with MSAA antialiasing, so custom layers are antialiased
        }));
          window.tb = new Threebox(
            map,
            map.getCanvas().getContext('webgl'),
            {
                defaultLights: true,
                enableSelectingObjects: false,
                enableTooltips: true,
                multiLayer: true, // this will create a default custom layer that will manage a single tb.update
            }
        );

        let stats;
        let items = 1;
        let minZoom = 16;
        let maxZoom = 18;
        let zoomStep = (maxZoom - minZoom) / 5;
        let toggleableLayerIds = [];
          let names = {
            compositeSource: "composite",
            compositeSourceLayer: "building",
            compositeLayer: "3d-buildings"

        }
        const geojson= {
                'type': 'geojson'
                , 'data': {
                    'type': 'FeatureCollection'
                    , 'features': [{
                            'type': 'Feature'
                            , 'properties': {
                                'iconSize': [40, 40],
                                'description': '<img src="https://dev.holomia.com/asset/img/Holomia.png" />'
                            }
                            , 'geometry': {
                                'type': 'Point'
                                , 'coordinates': [-87.61694, 41.86625]
                            }
                        }
                       
                    ]
                }
            };

		import Stats from 'https://threejs.org/examples/jsm/libs/stats.module.js';
		function animate() {
			requestAnimationFrame(animate);
			stats.update();
        }

        map.on('style.load', function () {

            // stats
            //Building
            map.addLayer(createCompositeLayer());

			for (let j = 1; j <= items; j++) {
				let l = {
					layer: '3d-model' + j,
					origin: [-87.61694 - ((j - 1) * (0.0003)), 41.86625]
				}
				toggleableLayerIds.push(l);
            }
            //createButtons();

            let i = 0;
            toggleableLayerIds.forEach((l) => {
                i++;
                map.addLayer(createCustomLayer(l.layer, l.origin, i), 'waterway-label');
				tb.setLayerZoomRange(l.layer, minZoom + (i * zoomStep), maxZoom + (i * zoomStep));
            });

        });
        map.on('load', () => {
            map.addSource('places',geojson);
            // Add a layer showing the places.
            map.addLayer({
                'id': 'places'
                , 'type': 'circle'
                , 'source': 'places'
                , 'paint': {
                    'circle-color': '#4264fb'
                    , 'circle-radius': 6
                    , 'circle-stroke-width': 2
                    , 'circle-stroke-color': '#ffffff'
                }
            });

            // Create a popup, but don't add it to the map yet.
            const popup = new mapboxgl.Popup({
                closeButton: true
                , closeOnClick: true
            });

            map.on('mouseenter', 'places', (e) => {
                // Change the cursor style as a UI indicator.
                map.getCanvas().style.cursor = 'pointer';

                // Copy coordinates array.
                const coordinates = e.features[0].geometry.coordinates.slice();
                const description = e.features[0].properties.description;

                // Ensure that if the map is zoomed out such that multiple
                // copies of the feature are visible, the popup appears
                // over the copy being pointed to.
                while (Math.abs(e.lngLat.lng - coordinates[0]) > 180) {
                    coordinates[0] += e.lngLat.lng > coordinates[0] ? 360 : -360;
                }

                // Populate the popup and set its coordinates
                // based on the feature found.
                popup.setLngLat(coordinates).setHTML(description).addTo(map);
            });

           
        });


       
		function createCustomLayer(layerId, origin, scale) {
			//create the layer
			let customLayer3D = {
				id: layerId,
				type: 'custom',
				renderingMode: '3d',
				onAdd: function (map, gl) {
					addModel(layerId, origin, scale);
				},
				render: function (gl, matrix) {
					//tb.update(); is not needed anymore if multiLayer : true
				}
			};
			return customLayer3D;

		};

         function createCompositeLayer() {
            let layer = {
                'id': names.compositeLayer,
                'source': names.compositeSource,
                'source-layer': names.compositeSourceLayer,
                'filter': ['==', 'extrude', 'true'],
                'type': 'fill-extrusion',
                'minzoom': minZoom,
                'paint': {
                    'fill-extrusion-color':
                        [
                            'case',
                            ['boolean', ['feature-state', 'select'], false],
                            "lightgreen",
                            ['boolean', ['feature-state', 'hover'], false],
                            "lightblue",
                            "#522c91"
                        ],

                    // use an 'interpolate' expression to add a smooth transition effect to the
                    // buildings as the user zooms in
                    'fill-extrusion-height': [
                        'interpolate',
                        ['linear'],
                        ['zoom'],
                        minZoom,
                        0,
                        minZoom + 0.05,
                        ['get', 'height']
                    ],
                    'fill-extrusion-base': [
                        'interpolate',
                        ['linear'],
                        ['zoom'],
                        minZoom,
                        0,
                        minZoom + 0.05,
                        ['get', 'min_height']
                    ],
                    'fill-extrusion-opacity': 0.9
                }
            };
            return layer;
        }

        function addModel(layerId, origin, i) {
            // Attribution, no License specified: Model by https://github.com/nasa/
			// https://nasa3d.arc.nasa.gov/detail/jpl-vtad-dsn34
			let options = {
				type: 'glb', 
				obj: 'https://bizverse-model.s3.ap-southeast-1.amazonaws.com/objectnft/d5e77ae5-0325-47b7-8a41-20f2d489a894.glb', //model url
				units: 'meters', //units in the default values are always in meters
				scale: 1 / i,
				rotation: { x: 90, y: 180, z: 0 }, //default rotation
				anchor: 'center'
			}
			tb.loadObj(options, function (model) {
				model.setCoords(origin);
				let l = map.getLayer(layerId);
				model.addTooltip("Zoom:" + (minZoom + (i * zoomStep)) + " - " + (maxZoom + (i * zoomStep)), true);
				tb.add(model, layerId);
			});
		}
        for (const marker of geojson.data.features) {
            // Create a DOM element for each marker.
            const el = document.createElement('div');
            const width = marker.properties.iconSize[0];
            const height = marker.properties.iconSize[1];
            el.className = 'marker';
            el.style.backgroundImage = `url(https://dev.holomia.com/Projects/1/2/2Dimage/1654741524120-01-canal-kientruc-nha-pho-my-can-giuajpg.jpg)`;
            el.style.width = `${width}px`;
            el.style.height = `${height}px`;
            el.style.backgroundSize = '100%';

            // Add markers to the map.
            new mapboxgl.Marker(el)
            .setLngLat(marker.geometry.coordinates)
            .addTo(map);
        }
        
        map.addControl(
            new MapboxGeocoder({
            accessToken: mapboxgl.accessToken,
            marker: {
            color: 'orange'
            },
            mapboxgl: mapboxgl
            })
        );
       
        map.addControl(new mapboxgl.FullscreenControl());
        map.addControl(new mapboxgl.NavigationControl());
       
   

      
    </script>

</body>

</html>