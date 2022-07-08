<template>
  <div id="map" ref="map"></div>
</template>

<script>
import mapboxgl from "mapbox-gl";
import threebox from "threebox-plugin/dist/threebox";

export default {
  components: {},
  data() {
    return {
      map:null,
      minZoom: 16,
      maxZoom: 18,
      zoomStep: 2/5,
      accessToken:
        "pk.eyJ1IjoicXVhbmduZDEwMDMiLCJhIjoiY2w0aHQ3aWFoMDh2YzNlazc5YzhlcmlsciJ9.bmvnKogFKsfOuOscG-2q1A",
      names: {
        compositeSource: "composite",
        compositeSourceLayer: "building",
        compositeLayer: "3d-buildings"
      },
      toggleableLayerIds: []
    };
  },
  mounted() {
    mapboxgl.accessToken = this.accessToken;

    this.map = new mapboxgl.Map({
      container: this.$refs.map,
      interactive: true,
      style: "mapbox://styles/mapbox/navigation-night-v1",
      zoom: 18,
      center: [-87.61694, 41.86625],
      pitch: 60,
      hash: true,
      antialias: true
    }).on("style.load", () => {
      let _this = this;
      this.map.addLayer(_this.createCompositeLayer());
      for (let j = 1; j <= 1; j++) {
        let l = {
          layer: "3d-model" + j,
          origin: [-87.61694 - (j - 1) * 0.0003, 41.86625]
        };
        this.toggleableLayerIds.push(l);
      }
      //createButtons();

      let i = 0;
      this.toggleableLayerIds.forEach(l => {
        i++;
        this.map.addLayer(
          this.createCustomLayer(l.layer, l.origin, i),
          "waterway-label"
        );
        tb.setLayerZoomRange(
          l.layer,
          this.minZoom + i * this.zoomStep,
          this.maxZoom + i * this.zoomStep
        );
      });
    });

    window.tb = new Threebox(
      this.map,
      this.map.getCanvas().getContext("webgl"),
      {
        defaultLights: true,
        enableSelectingObjects: false,
        enableTooltips: true,
        multiLayer: true // this will create a default custom layer that will manage a single tb.update
      }
    );
  },
  methods: {
    createCompositeLayer() {
      let layer = {
        id: this.names.compositeLayer,
        source: this.names.compositeSource,
        "source-layer": this.names.compositeSourceLayer,
        filter: ["==", "extrude", "true"],
        type: "fill-extrusion",
        minzoom: this.minZoom,
        paint: {
          "fill-extrusion-color": [
            "case",
            ["boolean", ["feature-state", "select"], false],
            "lightgreen",
            ["boolean", ["feature-state", "hover"], false],
            "lightblue",
            "#522c91"
          ],

          // use an 'interpolate' expression to add a smooth transition effect to the
          // buildings as the user zooms in
          "fill-extrusion-height": [
            "interpolate",
            ["linear"],
            ["zoom"],
            this.minZoom,
            0,
            this.minZoom + 0.05,
            ["get", "height"]
          ],
          "fill-extrusion-base": [
            "interpolate",
            ["linear"],
            ["zoom"],
            this.minZoom,
            0,
            this.minZoom + 0.05,
            ["get", "min_height"]
          ],
          "fill-extrusion-opacity": 0.9
        }
      };
      return layer;
    },
    addModel(layerId, origin, i) {
      // Attribution, no License specified: Model by https://github.com/nasa/
      // https://nasa3d.arc.nasa.gov/detail/jpl-vtad-dsn34
      var _this = this;
      let options = {
        type: "glb",
        obj:"https://bizverse-model.s3.ap-southeast-1.amazonaws.com/objectnft/d5e77ae5-0325-47b7-8a41-20f2d489a894.glb", //model url
        units: "meters", //units in the default values are always in meters
        scale: 1 / i,
        rotation: { x: 90, y: 180, z: 0 }, //default rotation
        anchor: "center"
      };
    
      tb.loadObj(options, function (model) {
        model.setCoords(origin);
        let l = _this.map.getLayer(layerId);
        model.addTooltip(
          "Zoom:" +
            (_this.minZoom + i * _this.zoomStep) +
            " - " +
            (_this.maxZoom + i * _this.zoomStep),
          true
        );
        tb.add(model, layerId);
      });
    },
    createCustomLayer(layerId, origin, scale) {
     
      var _this = this;
      let customLayer3D = {
        id: layerId,
        type: "custom",
        renderingMode: "3d",
        onAdd: function(map, gl) {
          _this.addModel(layerId, origin, scale);
        },
        render: function(gl, matrix) {
          tb.update(); //is not needed anymore if multiLayer : true
        }
      };
      return customLayer3D;
    }
  }
};
</script>
<style scoped>
#map {
  width: 100%;
  height: 100%;
  padding: 0;
  margin: 0;
}
</style>