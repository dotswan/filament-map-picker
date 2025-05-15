<?php

declare(strict_types=1);

namespace Dotswan\MapPicker\Contracts;

use Closure;

interface MapOptions
{
    public function getMapConfig(): string;

    public function getExtraStyle(): string;

    public function draggable(Closure|bool $draggable = true): self;

    public function clickable(Closure|bool $clickable): self;

    public function setBoundsToBritishIsles(): self;

    public function defaultLocation(int|float $latitude, float|int $longitude): self;

    public function extraStyles(array $styles = []): self;

    public function rangeSelectField(string $rangeSelectField): self;

    public function drawCircleMarker(Closure|bool $draw = true): self;

    public function zoom(int $zoom): self;

    public function maxZoom(int $maxZoom): self;

    public function minZoom(int $minZoom): self;

    public function showMarker(Closure|bool $show = true): self;

    public function tilesUrl(string $url): self;

    public function boundaries(Closure|bool $on, int|float $southWestLat = 0, int|float $southWestLng = 0, int|float $northEastLat = 0, int|float $northEastLng = 0): self;

    public function detectRetina(Closure|bool $detectRetina = true): self;

    public function showZoomControl(Closure|bool $show = true): self;

    public function showFullscreenControl(Closure|bool $show = true): self;

    public function extraControl(array $control): self;

    public function extraTileControl(array $control): self;

    public function markerColor(string $color): self;

    public function liveLocation(Closure|bool $send = false, Closure|bool $realtime = false, int $miliseconds = 5000): self;

    public function showMyLocationButton(Closure|bool $showMyLocationButton = true): self;

    public function geoMan(Closure|bool $show = true): self;

    public function geoManEditable(Closure|bool $show = true): self;

    public function geoManPosition(string $position = 'topleft'): self;

    public function rotateMode(Closure|bool $rotate = true): self;

    public function drawMarker(Closure|bool $draw = true): self;

    public function drawPolygon(Closure|bool $draw = true): self;

    public function drawPolyline(Closure|bool $draw = true): self;

    public function drawCircle(Closure|bool $draw = true): self;

    public function editPolygon(Closure|bool $edit = true): self;

    public function deleteLayer(Closure|bool $delete = true): self;

    public function dragMode(Closure|bool $enable = true): self;

    public function cutPolygon(Closure|bool $enable = true): self;

    public function setColor(string $color): self;

    public function setFilledColor(string $filledColor): self;

    public function markerHtml(string $html): self ;

    public function markerIconUrl(?string $url): self;

    public function markerIconSize(array $size): self;

    public function markerIconClassName(string $className): self;

    public function markerIconAnchor(array $anchor): self;

    public function snappable(Closure|bool $snappable = true, int $distance = 20): self;

    public function drawRectangle(Closure|bool $draw = true): self;

    public function drawText(Closure|bool $draw = true): self;
}
