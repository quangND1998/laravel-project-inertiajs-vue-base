<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Display buildings in 3D</title>
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no">
    <link href="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v2.8.2/mapbox-gl.js"></script>
    < link href="https://unpkg.com/ babox-plugin/dist/babox.css " rel=" stylesheet " />
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        #map {
            position: absolute;
            top: 0;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>

    <script src="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.min.js">
    </script>
    <link rel="stylesheet"
        href="https://api.mapbox.com/mapbox-gl-js/plugins/mapbox-gl-geocoder/v5.0.0/mapbox-gl-geocoder.css"
        type="text/css">
    <script src="https://unpkg.com/three@0.126.0/build/three.min.js"></script>
    <script src="https://unpkg.com/three@0.126.0/examples/js/loaders/GLTFLoader.js"></script>
    <script src=" https://unpkg.com/threebox-plugin/dist/threebox.min.js " type=" text/javascript"> </script>
    <script src="config.js"></script>
    <div id="map"></div>
    <script>
        mapboxgl.accessToken = 'pk.eyJ1IjoicXVhbmduZDEwMDMiLCJhIjoiY2w0aHQ3aWFoMDh2YzNlazc5YzhlcmlsciJ9.bmvnKogFKsfOuOscG-2q1A';
        const map = new mapboxgl.Map({
            style: 'mapbox://styles/mapbox/navigation-night-v1'
            , center: [-87.61694, 41.86625]
            , zoom: 15.5
            , pitch: 45
            , bearing: -17.6
            , container: 'map'
            , antialias: true
        });

        // parameters to ensure the model is georeferenced correctly on the map
        const modelOrigin = [-87.61694, 41.86625];
        const modelAltitude = 0;
        const modelRotate = [Math.PI / 2, 0, 0];

        const modelAsMercatorCoordinate = mapboxgl.MercatorCoordinate.fromLngLat(
            modelOrigin
            , modelAltitude
        );

        // transformation parameters to position, rotate and scale the 3D model onto the map
        const modelTransform = {
            translateX: modelAsMercatorCoordinate.x
            , translateY: modelAsMercatorCoordinate.y
            , translateZ: modelAsMercatorCoordinate.z
            , rotateX: modelRotate[0]
            , rotateY: modelRotate[1]
            , rotateZ: modelRotate[2],
            /* Since the 3D model is in real world meters, a scale transform needs to be
             * applied since the CustomLayerInterface expects units in MercatorCoordinates.
             */
            scale: modelAsMercatorCoordinate.meterInMercatorCoordinateUnits()
        };

        const THREE = window.THREE;
        const customLayer = {
            id: '3d-model'
            , type: 'custom'
            , renderingMode: '3d'
            , onAdd: function(map, gl) {
                this.camera = new THREE.Camera();
                this.scene = new THREE.Scene();

                // create two three.js lights to illuminate the model
                const directionalLight = new THREE.DirectionalLight(0xffffff);
                directionalLight.position.set(0, -70, 100).normalize();
                this.scene.add(directionalLight);

                const directionalLight2 = new THREE.DirectionalLight(0xffffff);
                directionalLight2.position.set(0, 70, 100).normalize();
                this.scene.add(directionalLight2);

                // use the three.js GLTF loader to add the 3D model to the three.js scene
                const loader = new THREE.GLTFLoader();
                loader.load(
                    'https://bizverse-model.s3.ap-southeast-1.amazonaws.com/objectnft/d5e77ae5-0325-47b7-8a41-20f2d489a894.glb'
                    , (gltf) => {
                        this.scene.add(gltf.scene);
                    }
                );
                this.map = map;

                // use the Mapbox GL JS map canvas for three.js
                this.renderer = new THREE.WebGLRenderer({
                    canvas: map.getCanvas()
                    , context: gl
                    , antialias: true
                });

                this.renderer.autoClear = false;
            }
            , render: function(gl, matrix) {
                const rotationX = new THREE.Matrix4().makeRotationAxis(
                    new THREE.Vector3(1, 0, 0)
                    , modelTransform.rotateX
                );
                const rotationY = new THREE.Matrix4().makeRotationAxis(
                    new THREE.Vector3(0, 0, 0)
                    , modelTransform.rotateY
                );
                const rotationZ = new THREE.Matrix4().makeRotationAxis(
                    new THREE.Vector3(0, 0, 1)
                    , modelTransform.rotateZ
                );

                const m = new THREE.Matrix4().fromArray(matrix);
                const l = new THREE.Matrix4()
                    .makeTranslation(
                        modelTransform.translateX
                        , modelTransform.translateY
                        , modelTransform.translateZ
                    )
                    .scale(
                        new THREE.Vector3(
                            modelTransform.scale
                            , -modelTransform.scale
                            , modelTransform.scale
                        )
                    )
                    .multiply(rotationX)
                    .multiply(rotationY)
                    .multiply(rotationZ);

                this.camera.projectionMatrix = m.multiply(l);
                this.renderer.resetState();
                this.renderer.render(this.scene, this.camera);
                this.map.triggerRepaint();
            },
            
        
        };


        map.on('load', () => {
            // Insert the layer beneath any symbol layer.
            const layers = map.getStyle().layers;
            const labelLayerId = layers.find(
                (layer) => layer.type === 'symbol' && layer.layout['text-field']
            ).id;

            // The 'building' layer in the Mapbox Streets
            // vector tileset contains building height data
            // from OpenStreetMap.
            map.addLayer({
                    'id': 'add-3d-buildings'
                    , 'source': 'composite'
                    , 'source-layer': 'building'
                    , 'filter': ['==', 'extrude', 'true']
                    , 'type': 'fill-extrusion'
                    , 'minzoom': 15
                    , 'paint': {
                        'fill-extrusion-color': '#522c91',

                        // Use an 'interpolate' expression to
                        // add a smooth transition effect to
                        // the buildings as the user zooms in.
                        'fill-extrusion-height': [
                            'interpolate'
                            , ['linear']
                            , ['zoom']
                            , 15
                            , 0
                            , 15.05
                            , ['get', 'height']
                        ]
                        , 'fill-extrusion-base': [
                            'interpolate'
                            , ['linear']
                            , ['zoom']
                            , 15
                            , 0
                            , 15.05
                            , ['get', 'min_height']
                        ]
                        , 'fill-extrusion-opacity': 0.6
                    }
                }
                ,  'waterway-label'
            );

            map.addSource('floorplan', {
                'type': 'geojson',
                /*
                 * Each feature in this GeoJSON file contains values for
                 * `properties.height`, `properties.base_height`,
                 * and `properties.color`.
                 * In `addLayer` you will use expressions to set the new
                 * layer's paint properties based on these values.
                 */
                'data': 'https://docs.mapbox.com/mapbox-gl-js/assets/indoor-3d-map.geojson'
            });

            map.addLayer({
                'id': 'room-extrusion'
                , 'type': 'fill-extrusion'
                , 'source': 'floorplan'
                , 'minzoom': 15
                , 'paint': {
                    'fill-extrusion-color': '#522c91',

                    // Use an 'interpolate' expression to
                    // add a smooth transition effect to
                    // the buildings as the user zooms in.
                    'fill-extrusion-height': [
                        'interpolate'
                        , ['linear']
                        , ['zoom']
                        , 15
                        , 0
                        , 15.05
                        , ['get', 'height']
                    ]
                    , 'fill-extrusion-base': [
                        'interpolate'
                        , ['linear']
                        , ['zoom']
                        , 15
                        , 0
                        , 15.05
                        , ['get', 'min_height']
                    ]
                    , 'fill-extrusion-opacity': 0.6
                }
            });
        });


        const coordinatesGeocoder = function(query) {
            // Match anything which looks like
            // decimal degrees coordinate pair.
            const matches = query.match(
                /^[ ]*(?:Lat: )?(-?\d+\.?\d*)[, ]+(?:Lng: )?(-?\d+\.?\d*)[ ]*$/i
            );
            if (!matches) {
                return null;
            }

            function coordinateFeature(lng, lat) {
                return {
                    center: [lng, lat]
                    , geometry: {
                        type: 'Point'
                        , coordinates: [lng, lat]
                    }
                    , place_name: 'Lat: ' + lat + ' Lng: ' + lng
                    , place_type: ['coordinate']
                    , properties: {}
                    , type: 'Feature'
                };
            }

            const coord1 = Number(matches[1]);
            const coord2 = Number(matches[2]);
            const geocodes = [];

            if (coord1 < -90 || coord1 > 90) {
                // must be lng, lat
                geocodes.push(coordinateFeature(coord1, coord2));
            }

            if (coord2 < -90 || coord2 > 90) {
                // must be lat, lng
                geocodes.push(coordinateFeature(coord2, coord1));
            }

            if (geocodes.length === 0) {
                // else could be either lng, lat or lat, lng
                geocodes.push(coordinateFeature(coord1, coord2));
                geocodes.push(coordinateFeature(coord2, coord1));
            }

            return geocodes;
        };
        map.addControl(
            new MapboxGeocoder({
                accessToken: mapboxgl.accessToken
                , localGeocoder: coordinatesGeocoder
                , zoom: 15
                , placeholder: 'Try: -40, 170'
                , mapboxgl: mapboxgl
                , reverseGeocode: true
            })
        , );
        //Model
        window.threebox = new Threebox (map);
        babox.setupDefaultLights ();
        map.addControl(new mapboxgl.NavigationControl());
        map.on('load', () => {
            map.addLayer(customLayer, 'waterway-label');
            });

    </script>

</body>

</html>