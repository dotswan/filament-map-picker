import * as L from 'leaflet';
import 'leaflet-fullscreen';

document.addEventListener('alpine:init', () => {
    Alpine.data('mapPicker', ($wire, mapConfig) => {
        return {
            config:{},
            $wire:{},
            map: null,
            tile: null,
            marker: null,
            createMap: function (el) {
                const that = this;

                this.map = L.map(el, this.config.controls);
                this.map.on('load', () => {
                    setTimeout(() => this.map.invalidateSize(true), 0);
                    if (this.config.showMarker === true) {
                        this.marker.setLatLng(this.map.getCenter());
                    }
                });

                if (!this.config.draggable) {
                    this.map.dragging.disable();
                }

                this.tile = L.tileLayer(this.config.tilesUrl, {
                    attribution: this.config.attribution,
                    minZoom: this.config.minZoom,
                    maxZoom: this.config.maxZoom,
                    tileSize: this.config.tileSize,
                    zoomOffset: this.config.zoomOffset,
                    detectRetina: this.config.detectRetina,
                }).addTo(this.map);

                if (this.config.showMarker === true) {
                    const markerColor = this.config.markerColor || "#3b82f6";
                    const svgIcon = L.divIcon({
                        html: `<svg xmlns="http://www.w3.org/2000/svg" class="map-icon" fill="${markerColor}" width="36" height="36" viewBox="0 0 24 24"><path d="M12 0c-4.198 0-8 3.403-8 7.602 0 4.198 3.469 9.21 8 16.398 4.531-7.188 8-12.2 8-16.398 0-4.199-3.801-7.602-8-7.602zm0 11c-1.657 0-3-1.343-3-3s1.343-3 3-3 3 1.343 3 3-1.343 3-3 3z"/></svg>`,
                        className: "",
                        iconSize: [36, 36],
                        iconAnchor: [18, 36],
                    });
                    this.marker = L.marker([0, 0], {
                        icon: svgIcon,
                        draggable: false,
                        autoPan: true
                    }).addTo(this.map);
                    this.map.on('move', () => this.marker.setLatLng(this.map.getCenter()));
                }

                this.map.on('moveend', () =>  setTimeout(()=>this.updateLocation(),500));

                this.map.on('locationfound', function () {
                    that.map.setZoom(this.config.controls.zoom);
                });

                let location = this.getCoordinates();
                if (!location.lat && !location.lng) {
                    this.map.locate({
                        setView: true,
                        maxZoom: this.config.controls.maxZoom,
                        enableHighAccuracy: true,
                        watch: false
                    });
                } else {
                    this.map.setView(new L.LatLng(location.lat, location.lng));
                }

                if(this.config.showMyLocationButton)
                {
                    this.addLocationButton();
                }
            },
            updateLocation: function() {
                let coordinates = this.getCoordinates();
                let currentCenter = this.map.getCenter();
                
                if (this.config.draggable && 
                    (coordinates.lng !== currentCenter.lng || coordinates.lat !== currentCenter.lat)) {
                    
                    this.$wire.set(this.config.statePath, this.map.getCenter(), false);
            
                    if (this.config.liveLocation) {
                        this.$wire.$refresh();
                    }
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
                let location = this.$wire.get(this.config.statePath);
                if (location === null || !location.hasOwnProperty('lat')) {
                    location = {lat: 0, lng: 0};
                }
                return location;
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
                        const currentPosition = new L.LatLng(position.coords.latitude, position.coords.longitude);
                        await this.map.flyTo(currentPosition);
                        
                        this.updateLocation();

                        if (this.config.showMarker === true) {
                            await this.marker.setLatLng(currentPosition);
                            setTimeout(()=> this.updateLocation(),500);
                        }
                        
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
            init:function(){
                this.$wire = $wire;
                this.config = mapConfig
            }
        }
    });

    window.dispatchEvent(new CustomEvent('map-script-loaded'));
});