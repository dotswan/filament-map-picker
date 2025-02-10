<?php

declare(strict_types=1);

namespace Dotswan\MapPicker\Infolists;

use Filament\Infolists\Components\Entry;
use Dotswan\MapPicker\Contracts\MapOptions;

class MapEntry extends Entry implements MapOptions
{
    /**
     * Field view
     * @var string
     */
    public string $view = 'map-picker::infolists.osm-map-entry';

    /**
     * Main field config variables
     * @var array
     */
    private array $mapConfig = [
        'statePath'            => '',
        'draggable'            => true,
        'showMarker'           => true,
        'tilesUrl'             => 'http://tile.openstreetmap.org/{z}/{x}/{y}.png',
        'attribution'          => null,
        'zoomOffset'           => -1,
        'tileSize'             => 512,
        'detectRetina'         => true,
        'rangeSelectField'     => 'distance',
        'minZoom'              => 0,
        'maxZoom'              => 28,
        'zoom'                 => 15,
        'clickable'            => false,
        'markerColor'          => '#3b82f6',
        'liveLocation'         => false,
        'bounds'               => false,
        'showMyLocationButton' => [false, false, 5000],
        'default'              => ['lat' => 0, 'lng' => 0],
        'markerHtml'           => '',
        'markerIconUrl'        => null,
        'markerIconSize'       => [36, 36],
        'markerIconClassName'  => '',
        'markerIconAnchor'     => [18, 36],
        'geoMan'               => [
            'show'                  =>  false,
            'editable'              =>  true,
            'position'              =>  'topleft',
            'drawCircleMarker'      =>  true,
            'rotateMode'            =>  true,
            'drawMarker'            =>  true,
            'drawPolygon'           =>  true,
            'drawPolyline'          =>  true,
            'drawCircle'            =>  true,
            'dragMode'              =>  true,
            'cutPolygon'            =>  true,
            'editPolygon'           =>  true,
            'deleteLayer'           =>  true,
            'color'                 =>  '#3388ff',
            'filledColor'           =>  '#cad9ec',
            'snappable'             =>  false,
            'snapDistance'          =>  20,
            'drawText'              =>  true,
            'drawRectangle'         =>  true
        ]
    ];

    /**
     * Leaflet controls variables
     * @var array
     */
    private array $controls = [
        'zoomControl'       => true,
        'scrollWheelZoom'   => 'center',
        'doubleClickZoom'   => 'center',
        'touchZoom'         => 'center',
        'minZoom'           => 1,
        'maxZoom'           => 28,
        'zoom'              => 15,
        'fullscreenControl' => true,
    ];

    private array $extraStyle = [];

    /**
     * Extra leaflet controls variables
     * @var array
     */
    private array $extraControls = [];


    /**
     * Create json configuration string
     * @return string
     */
    public function getMapConfig(): string
    {
        return json_encode(
            array_merge($this->mapConfig, [
                'statePath' => $this->getStatePath(),
                'controls'  => array_merge($this->controls, $this->extraControls)
            ])
        );
    }


    /**
     * Create extra styles string
     * @return string
     */
    public function getExtraStyle(): string
    {
        return implode(';', $this->extraStyle);
    }


    /**
     * Determine if user can drag map around or not.
     * @param bool $draggable
     * @return MapOptions
     * @note Default value is false
     */
    public function draggable(bool $draggable = true): self
    {
        $this->mapConfig['draggable'] = $draggable;
        return $this;
    }



    public function defaultLocation(int|float $latitude, float|int $longitude): self
    {
        $this->mapConfig['default']['lat'] = $latitude;
        $this->mapConfig['default']['lng'] = $longitude;

        return $this;
    }


    /**
     * Set extra style
     * @param array $styles
     * @return self
     */
    public function extraStyles(array $styles = []): self
    {
        $this->extraStyle = $styles;
        return $this;
    }


    /**
     * Set default zoom
     * @param int $zoom
     * @return MapOptions
     * @note Default value 19
     */
    public function zoom(int $zoom): self
    {
        $this->controls['zoom'] = $zoom;
        return $this;
    }

    /**
     * Set max zoom
     * @param int $maxZoom
     * @return $this
     * @note Default value 20
     */
    public function maxZoom(int $maxZoom): self
    {
        $this->controls['maxZoom'] = $maxZoom;
        return $this;
    }

    /**
     * Set min zoom
     * @param int $maxZoom
     * @return $this
     * @note Default value 1
     */
    public function minZoom(int $minZoom): self
    {
        $this->controls['minZoom'] = $minZoom;
        return $this;
    }


    /**
     * Determine if marker is visible or not.
     * @param bool $show
     * @return $this
     * @note Default value is false
     */
    public function showMarker(bool $show = true): self
    {
        $this->mapConfig['showMarker'] = $show;
        return $this;
    }

    /**
     * Set tiles url
     * @param string $url
     * @return $this
     */
    public function tilesUrl(string $url): self
    {
        $this->mapConfig['tilesUrl'] = $url;
        return $this;
    }

    /**
     * Determine if it detects retina monitors or not.
     * @param bool $detectRetina
     * @return $this
     */
    public function detectRetina(bool $detectRetina = true): self
    {
        $this->mapConfig['detectRetina'] = $detectRetina;
        return $this;
    }

    /**
     * Determine if zoom box is visible or not.
     * @param bool $show
     * @return $this
     */
    public function showZoomControl(bool $show = true): self
    {
        $this->controls['zoomControl'] = $show;
        return $this;
    }


    /**
     * Determine if fullscreen box is visible or not.
     * @param bool $show
     * @return $this
     */
    public function showFullscreenControl(bool $show = true): self
    {
        $this->controls['fullscreenControl'] = $show;
        return $this;
    }

    /**
     * Change the marker color.
     * @param string $color
     * @return $this
     */
    public function markerColor(string $color): self
    {
        $this->mapConfig['markerColor'] = $color;
        return $this;
    }

    /**
     * Enable or disable live location updates for the map.
     * @param bool $send
     * @return $this
     */
    public function liveLocation(bool $send = true, bool $realtime = false, int $miliseconds = 5000): self
    {
        $this->mapConfig['liveLocation'] = [
            'send' => $send,
            'realtime' => $realtime,
            'miliseconds' => $miliseconds
        ];
        return $this;
    }

    /**
     * Enable or disable show my location button on map.
     * @param bool $showMyLocationButton
     * @return $this
     */
    public function showMyLocationButton(bool $showMyLocationButton = true): self
    {
        $this->mapConfig['showMyLocationButton'] = $showMyLocationButton;
        return $this;
    }

    /**
     * Append extra controls to be passed to leaflet map object
     * @param array $control
     * @return $this
     */
    public function extraControl(array $control): self
    {
        $this->extraControls = array_merge($this->extraControls, $control);
        return $this;
    }

    /**
     * Append extra controls to be passed to leaflet tileLayer object
     * @param array $control
     * @return $this
     */
    public function extraTileControl(array $control): self
    {
        $this->mapConfig = array_merge($this->mapConfig, $control);
        return $this;
    }

    /**
     * Determines if the user can click to place the marker on the map.
     * @param bool $clickable
     * @return $this
     */
    public function clickable(bool $clickable): self
    {
        $this->mapConfig['clickable'] = $clickable;
        return $this;
    }

    /**
     * Prevents the map from panning outside the defined box, and sets
     * a default location in the center of the box.
     * @param boolean $on
     * @param int|float $southWestLat
     * @param int|float $southWestLng
     * @param int|float $northEastLat
     * @param int|float $northEastLng
     * @return self
     */
    public function boundaries(bool $on, int|float $southWestLat = 0, int|float $southWestLng = 0, int|float $northEastLat = 0, int|float $northEastLng = 0): self
    {
        if ( ! $on) {
            $this->mapConfig['boundaries'] = false;
            return $this;
        }

        $this->mapConfig['bounds']['sw'] = ['lat' => $southWestLat, 'lng' => $southWestLng];
        $this->mapConfig['bounds']['ne'] = ['lat' => $northEastLat, 'lng' => $northEastLng];
        $this->defaultLocation(($southWestLat + $northEastLat) / 2.0, ($southWestLng + $northEastLng) / 2.0);

        return $this;
    }

    /**
     * Convenience function for appropriate values for boundaries() when
     * you want the British Isles
     * @return self
     **/
    public function setBoundsToBritishIsles(): self
    {
        $this->boundaries(true, 49.5, -11, 61, 2);
        return $this;
    }

    /**
     * Use the value of another field on the form for the range of the
     * circle surrounding the marker
     * @param string $rangeSelectField
     * @return $this
     **/
    public function rangeSelectField(string $rangeSelectField): self
    {
        $this->mapConfig['rangeSelectField'] = $rangeSelectField;
        return $this;
    }

    /**
     * Enable or disable GeoMan functionality.
     * @param bool $show
     * @return $this
     */
    public function geoMan(bool $show = true): self
    {
        $this->mapConfig['geoMan']['show'] = $show;
        return $this;
    }

    /**
     * Enable or disable GeoMan edit mode.
     * @param bool $show
     * @return $this
     */
    public function geoManEditable(bool $show = true): self
    {
        $this->mapConfig['geoMan']['editable'] = $show;
        return $this;
    }

    /**
     * Set GeoMan control position.
     * @param string $position
     * @return $this
     * @note Valid values: 'topleft', 'topright', 'bottomleft', 'bottomright'
     */
    public function geoManPosition(string $position = 'topleft'): self
    {
        $this->mapConfig['geoMan']['position'] = $position;
        return $this;
    }

    /**
     * Enable or disable drawing of circle markers.
     * @param bool $draw
     * @return $this
     */
    public function drawCircleMarker(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawCircleMarker'] = $draw;
        return $this;
    }

    /**
     * Enable or disable rotate mode.
     * @param bool $rotate
     * @return $this
     */
    public function rotateMode(bool $rotate = true): self
    {
        $this->mapConfig['geoMan']['rotateMode'] = $rotate;
        return $this;
    }

    /**
     * Enable or disable drawing of markers.
     * @param bool $draw
     * @return $this
     */
    public function drawMarker(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawMarker'] = $draw;
        return $this;
    }

    /**
     * Enable or disable drawing of polygons.
     * @param bool $draw
     * @return $this
     */
    public function drawPolygon(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawPolygon'] = $draw;
        return $this;
    }

    /**
     * Enable or disable drawing of polylines.
     * @param bool $draw
     * @return $this
     */
    public function drawPolyline(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawPolyline'] = $draw;
        return $this;
    }

    /**
     * Enable or disable drawing of circles.
     * @param bool $draw
     * @return $this
     */
    public function drawCircle(bool $draw = true): self
    {
        $this->mapConfig['geoMan']['drawCircle'] = $draw;
        return $this;
    }

    /**
     * Enable or disable editing of polygons.
     * @param bool $edit
     * @return $this
     */
    public function editPolygon(bool $edit = true): self
    {
        $this->mapConfig['geoMan']['editPolygon'] = $edit;
        return $this;
    }

    /**
     * Enable or disable deletion of layers.
     * @param bool $delete
     * @return $this
     */
    public function deleteLayer(bool $delete = true): self
    {
        $this->mapConfig['geoMan']['deleteLayer'] = $delete;
        return $this;
    }

    /**
     * Enable or disable drag mode.
     * @param bool $enable
     * @return $this
     */
    public function dragMode(bool $enable = true): self
    {
        $this->mapConfig['geoMan']['dragMode'] = $enable;
        return $this;
    }

    /**
     * Enable or disable snappable.
     * @param bool $snappable
     * @param int $distance
     * @return $this
     */
    public function snappable(bool $snappable = true, int $distance = 20): self
    {
        $this->extraControls['snappable'] = $snappable;
        $this->extraControls['snapDistance'] = $distance;
        return $this;
    }

    /**
     * Enable or disable drawing of rectangles.
     * @param bool $draw
     * @return $this
     */
    public function drawRectangle(bool $draw = true): self
    {
        $this->extraControls['drawRectangle'] = $draw;
        return $this;
    }

    /**
     * Enable or disable drawing of text.
     * @param bool $draw
     * @return $this
     */
    public function drawText(bool $draw = true): self
    {
        $this->extraControls['drawText'] = $draw;
        return $this;
    }

    /**
     * Enable or disable polygon cutting.
     * @param bool $enable
     * @return $this
     */
    public function cutPolygon(bool $enable = true): self
    {
        $this->mapConfig['geoMan']['cutPolygon'] = $enable;
        return $this;
    }

    /**
     * Set the stroke color for drawings.
     * @param string $color
     * @return $this
     */
    public function setColor(string $color): self
    {
        $this->mapConfig['geoMan']['color'] = $color;
        return $this;
    }

    /**
     * Set the fill color for drawings.
     * @param string $filledColor
     * @return $this
     */
    public function setFilledColor(string $filledColor): self
    {
        $this->mapConfig['geoMan']['filledColor'] = $filledColor;
        return $this;
    }

    /**
     * Set custom HTML for marker icon
     * @param string $html
     * @return $this
     */
    public function markerHtml(string $html): self
    {
        $this->mapConfig['markerHtml'] = $html;
        return $this;
    }

    /**
     * Set marker icon URL
     * @param string|null $url
     * @return $this
     */
    public function markerIconUrl(?string $url): self
    {
        $this->mapConfig['markerIconUrl'] = $url;
        return $this;
    }

    /**
     * Set marker icon size
     * @param array $size
     * @return $this
     */
    public function markerIconSize(array $size): self
    {
        $this->mapConfig['markerIconSize'] = $size;
        return $this;
    }

    /**
     * Set marker icon class name
     * @param string $className
     * @return $this
     */
    public function markerIconClassName(string $className): self
    {
        $this->mapConfig['markerIconClassName'] = $className;
        return $this;
    }

    /**
     * Set marker icon anchor point
     * @param array $anchor
     * @return $this
     */
    public function markerIconAnchor(array $anchor): self
    {
        $this->mapConfig['markerIconAnchor'] = $anchor;
        return $this;
    }

    /**
     * Setup function
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
    }
}
