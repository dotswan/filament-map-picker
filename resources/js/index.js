import * as LF from 'leaflet';
import 'leaflet-fullscreen';
import "@geoman-io/leaflet-geoman-free";


document.addEventListener('DOMContentLoaded', () => {
    const mapPicker = ($wire, config, state) => {
        return {
            map: null,
            tile: null,
            marker: null,
            rangeCircle: null,
            drawItems: null,
            rangeSelectField: null,
            formRestorationHiddenInput:null,
            
            createMap: function (el) {
                const that = this;

                this.map = LF.map(el, config.controls);

                if(config.bounds)
                {
                    let southWest = LF.latLng(config.bounds.sw.lat, config.bounds.sw.lng);
                    let northEast = LF.latLng(config.bounds.ne.lat, config.bounds.ne.lng);
                    let bounds = LF.latLngBounds(southWest, northEast);
                    this.map.setMaxBounds(bounds);
                    this.map.fitBounds(bounds);
                    this.map.on('drag', function() {
                        map.panInsideBounds(bounds, { animate: false });
                    });
                }
                this.map.on('load', () => {
                    setTimeout(() => this.map.invalidateSize(true), 0);
                    
                    if (config.showMarker && !config.clickable) {
                        this.marker.setLatLng(this.map.getCenter());
                    }
                });

                if (!config.draggable) {
                    this.map.dragging.disable();
                }

                if(config.clickable)
                {
                    this.map.on('click', function(e) {
                        that.setCoordinates(e.latlng);
                    });
                }

                this.tile = LF.tileLayer(config.tilesUrl, {
                    attribution: config.attribution,
                    minZoom: config.minZoom,
                    maxZoom: config.maxZoom,
                    tileSize: config.tileSize,
                    zoomOffset: config.zoomOffset,
                    detectRetina: config.detectRetina,
                }).addTo(this.map);

                if (config.showMarker) {
                    this.marker = LF.marker(this.getCoordinates(), {
                        icon: this.createMarkerIcon(),
                        draggable: false,
                        autoPan: true
                    }).addTo(this.map);
                    this.setMarkerRange();
                    if(!config.clickable) {
                        this.map.on('move', () => this.setCoordinates(this.map.getCenter()));
                    }
                }

                if(!config.clickable)
                {
                    this.map.on('moveend', () => setTimeout(() => this.updateLocation(), 500));
                }

                this.map.on('locationfound', function () {
                    that.map.setZoom(config.controls.zoom);
                });

                let location = this.getCoordinates();
                if (!location.lat && !location.lng) {
                    this.map.locate({
                        setView: true,
                        maxZoom: config.controls.maxZoom,
                        enableHighAccuracy: true,
                        watch: false
                    });
                } else {
                    this.map.setView(new LF.LatLng(location.lat, location.lng));
                }

                if (config.showMyLocationButton) {
                    this.addLocationButton();
                }

                if (config.liveLocation.send && config.liveLocation.realtime) {
                    setInterval(() => {
                        this.fetchCurrentLocation();
                    }, config.liveLocation.miliseconds);
                }
                this.map.on('zoomend',function(event) {
                    that.setFormRestorationState(false, that.map.getZoom());
                });

                // Geoman setup
                if (config.geoMan.show) {
                        this.map.pm.addControls({
                            snappable: config.geoMan.snappable,
                            snapDistance: config.geoMan.snapDistance,
                            position: config.geoMan.position,
                            drawCircleMarker: config.geoMan.drawCircleMarker,
                            rotateMode: config.geoMan.rotateMode,
                            drawRectangle: config.geoMan.drawRectangle,
                            drawText: config.geoMan.drawText,
                            drawMarker: config.geoMan.drawMarker,
                            drawPolygon: config.geoMan.drawPolygon,
                            drawPolyline: config.geoMan.drawPolyline,
                            drawCircle: config.geoMan.drawCircle,
                            editMode: config.geoMan.editMode,
                            dragMode: config.geoMan.dragMode,
                            cutPolygon: config.geoMan.cutPolygon,
                            editPolygon: config.geoMan.editPolygon,
                            deleteLayer: config.geoMan.deleteLayer
                        });

                        this.drawItems = new LF.FeatureGroup().addTo(this.map);

                        this.map.on('pm:create', (e) => {
                            if (e.layer && e.layer.pm) {
                                e.layer.pm.enable();
                                this.drawItems.addLayer(e.layer);
                                this.updateGeoJson();
                            }
                        });

                        this.map.on('pm:edit', () => {
                            this.updateGeoJson();
                        });

                        this.map.on('pm:remove', (e) => {
                            try {
                                this.drawItems.removeLayer(e.layer);
                                this.updateGeoJson();
                            } catch (error) {
                                console.error("Error during removal of layer:", error);
                            }
                        });

                    // Load existing GeoJSON if available
                    const existingGeoJson = this.getGeoJson();
                    if (existingGeoJson) {
                            this.drawItems = LF.geoJSON(existingGeoJson, {
                                pointToLayer: (feature, latlng) => {
                                    return LF.circleMarker(latlng, {
                                        radius: 15,
                                        color: '#3388ff',
                                        fillColor: '#3388ff',
                                        fillOpacity: 0.6
                                    });
                                },
                                style: function(feature) {
                                    if (feature.geometry.type === 'Polygon') {
                                        return {
                                            color: config.geoMan.color || "#3388ff",
                                            fillColor: config.geoMan.filledColor || 'blue',
                                            weight: 2,
                                            fillOpacity: 0.4
                                        };
                                    }
                                },
                                onEachFeature: (feature, layer) => {

                                    if (typeof feature.properties.title != "undefined") {
                                        layer.bindPopup(feature.properties.title);
                                    }else if (feature.geometry.type === 'Polygon') {
                                        layer.bindPopup("Polygon Area");
                                    } else if (feature.geometry.type === 'Point') {
                                        layer.bindPopup("Point Location");
                                    }


                                    if (config.geoMan.editable) {
                                        if (feature.geometry.type === 'Polygon') {
                                            layer.pm.enable({
                                                allowSelfIntersection: false
                                            });
                                        } else if (feature.geometry.type === 'Point') {
                                            layer.pm.enable({
                                                draggable: true
                                            });
                                        }
                                    }

                                    layer.on('pm:edit', () => {
                                        this.updateGeoJson();
                                    });
                                }
                            }).addTo(this.map);

                            if(config.geoMan.editable){
                                // Enable editing for each layer
                                this.drawItems.eachLayer(layer => {
                                    layer.pm.enable({
                                        allowSelfIntersection: false,
                                    });
                                });
                            }

                            this.map.fitBounds(this.drawItems.getBounds());
                    }
              }
            },
            createMarkerIcon() {
                if (config.markerIconUrl) {
                    return LF.icon({
                        iconUrl: config.markerIconUrl,
                        iconSize: config.markerIconSize,
                        iconAnchor: config.markerIconAnchor,
                        className: config.markerIconClassName
                    });
                }

                const markerColor = config.markerColor || "#3b82f6";
                const defaultHtml = `<svg xmlns="http://www.w3.org/2000/svg" class="map-icon" fill="${markerColor}" width="36" height="36" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`;
                
                return LF.divIcon({
                    html: config.markerHtml || defaultHtml,
                    className: config.markerIconClassName,
                    iconSize: config.markerIconSize,
                    iconAnchor: config.markerIconAnchor
                });
            },
            initFormRestoration: function () {
                this.formRestorationHiddenInput = document.getElementById(config.statePath+'_fmrest');
                window.addEventListener("pageshow", (event) => {
                    // called after form restoration
                    // true if page loaded from session
                    let restoredState = this.getFormRestorationState();
                    if(restoredState){
                        let coords = new LF.LatLng(restoredState.lat, restoredState.lng);
                        config.zoom = restoredState.zoom;
                        config.controls.zoom=restoredState.zoom;
                        this.setCoordinates(coords);
                    }
                });

            },
            setFormRestorationState: function(coords = null, zoom = null) {

                coords = coords || this.getFormRestorationState() || this.getCoordinates();
            
                if (this.map) {
                    coords.zoom = zoom ?? this.map.getZoom();
                }
            
                this.formRestorationHiddenInput.value = JSON.stringify(coords);
            },
            getFormRestorationState: function () {
                if(this.formRestorationHiddenInput.value)
                    return JSON.parse(this.formRestorationHiddenInput.value);
                return false;
            },
            updateGeoJson: function() {
                try {
                    const geoJsonData = this.drawItems.toGeoJSON();
                    if (typeof geoJsonData !== 'object') {
                        console.error("GeoJSON data is not an object:", geoJsonData);
                        return;
                    }
                    $wire.set(config.statePath, {
                        ...$wire.get(config.statePath),
                        geojson: geoJsonData
                    }, true);

                } catch (error) {
                    console.error("Error updating GeoJSON:", error);
                }
            },

            getGeoJson: function() {
                const state = $wire.get(config.statePath) ?? {};
                return state.geojson;
            },
            updateLocation: function() {
                let oldCoordinates = this.getCoordinates();
                let currentCoordinates = this.map.getCenter();
                if(config.clickable)
                    currentCoordinates = this.marker.getLatLng();

                if (oldCoordinates.lng !== currentCoordinates.lng || oldCoordinates.lat !== currentCoordinates.lat) {
                    this.setCoordinates(currentCoordinates);
                }
            },

            removeMap: function (el) {
                if (this.marker) {
                    this.marker.remove();
                    this.marker = null;
                }
                this.tile.remove();
                this.tile = null;
                this.map.off();
                this.map.remove();
                this.map = null;
            },

            getCoordinates: function () {
                let location = $wire.get(config.statePath)  ?? {};
                const hasValidCoordinates = location.hasOwnProperty('lat') && location.hasOwnProperty('lng') &&
                    location.lat !== null && location.lng !== null;

                if (!hasValidCoordinates) {
                    location = {
                        lat: config.default.lat,
                        lng: config.default.lng
                    };
                }

                return location;
            },

            setCoordinates: function (coords) {
                this.setFormRestorationState(coords);
                $wire.set(config.statePath, {
                    ...$wire.get(config.statePath),
                    lat: coords.lat,
                    lng: coords.lng
                }, false);

                if (config.liveLocation.send) {
                    $wire.$refresh();
                }
                this.updateMarker();
                return coords;
            },

            attach: function (el) {
                this.createMap(el);
                const observer = new IntersectionObserver(entries => {
                    entries.forEach(entry => {
                        if (entry.intersectionRatio > 0) {
                            if (!this.map)
                                this.createMap(el);
                        } else {
                            this.removeMap(el);
                        }
                    });
                }, {
                    root: null,
                    rootMargin: '0px',
                    threshold: 1.0
                });
                observer.observe(el);
            },

            fetchCurrentLocation: function () {
                if ('geolocation' in navigator) {
                    navigator.geolocation.getCurrentPosition(async position => {
                        const currentPosition = new LF.LatLng(position.coords.latitude, position.coords.longitude);
                        await this.map.flyTo(currentPosition);

                        this.updateLocation();
                        this.updateMarker();
                    }, error => {
                        console.error('Error fetching current location:', error);
                    });
                } else {
                    alert('Geolocation is not supported by this browser.');
                }
            },

            addLocationButton: function() {
                const locationButton = document.createElement('button');
                locationButton.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path fill="currentColor" d="M12 0C8.25 0 5 3.25 5 7c0 5.25 7 13 7 13s7-7.75 7-13c0-3.75-3.25-7-7-7zm0 10c-1.66 0-3-1.34-3-3s1.34-3 3-3 3 1.34 3 3-1.34 3-3 3zm0-5c-1.11 0-2 .89-2 2s.89 2 2 2 2-.89 2-2-.89-2-2-2z"/></svg>';
                locationButton.type = 'button';
                locationButton.classList.add('map-location-button');
                locationButton.onclick = () => this.fetchCurrentLocation();
                this.map.getContainer().appendChild(locationButton);
            },

            setMarkerRange: function() {

                if ((config.clickable && !this.marker) || !this.rangeSelectField) {
                    return;
                }
            
                const distance = parseInt(this.rangeSelectField.value || 0);
                const coordinates = this.getCoordinates();
                const circleStyle = {
                    color: 'blue',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: distance
                };
                
                if (this.rangeCircle) {
                    this.rangeCircle
                        .setLatLng(coordinates)
                        .setRadius(distance);
                    return;
                }
                
                this.rangeCircle = LF.circle(coordinates, circleStyle).addTo(this.map);
            },

            init: function() {
                this.$wire = $wire;
                this.config = config;
                this.state = state;
                this.rangeSelectField = document.getElementById(config.rangeSelectField);
                this.initFormRestoration();

                let that=this
                if(this.rangeSelectField){
                    this.rangeSelectField.addEventListener('change', function () {that.updateMarker(); });
                }
                $wire.on('refreshMap', this.refreshMap.bind(this));
            },

            updateMarker: function() {
                if (config.showMarker && this.marker) {
                    this.marker.setLatLng(this.getCoordinates());
                    this.setMarkerRange();
                    setTimeout(() => this.updateLocation(), 500);
                }
            },

            refreshMap: function() {
                this.map.flyTo(this.getCoordinates());
                this.updateMarker();
            }
        };
    };

    window.mapPicker = mapPicker;

    window.dispatchEvent(new CustomEvent('map-script-loaded'));
});
