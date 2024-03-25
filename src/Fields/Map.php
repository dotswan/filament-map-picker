<?php

declare(strict_types=1);

namespace Dotswan\MapPicker\Fields;

use Filament\Forms\Components\Field;
use Dotswan\MapPicker\Contracts\MapOptions;
use Filament\Forms\Concerns\HasStateBindingModifiers;

class Map extends Field implements MapOptions
{
    use HasStateBindingModifiers;
    /**
     * Field view
     * @var string
     */
    public string $view = 'map-picker::fields.osm-map-picker';

    /**
     * Main field config variables
     * @var array
     */
    private array $mapConfig = [
        'statePath'    => '',
        'draggable'    => true,
        'showMarker'   => true,
        'tilesUrl'     => 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
        'attribution'  => null,
        'zoomOffset'   => -1,
        'tileSize'     => 512,
        'detectRetina' => true,
        'minZoom'      => 0,
        'maxZoom'      => 28,
        'zoom'         => 15,
        'markerColor'  => '#3b82f6',
        'liveLocation' => false,
        'showMyLocationButton' => false,
    ];

    /**
     * Leaflet controls variables
     * @var array
     */
    private array $controls = [
        'zoomControl'     => true,
        'scrollWheelZoom' => 'center',
        'doubleClickZoom' => 'center',
        'touchZoom'       => 'center',
        'minZoom'         => 1,
        'maxZoom'         => 28,
        'zoom'            => 15,
        'fullscreenControl' => true,
    ];

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
    public function liveLocation(bool $send = true): self
    {
        $this->mapConfig['liveLocation'] = $send;
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
     * Setup function
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->default(['lat' => 0, 'lng' => 0]);
    }
}
