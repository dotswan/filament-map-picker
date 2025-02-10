<?php

declare(strict_types=1);

namespace Dotswan\MapPicker\Contracts;

interface MapOptions
{
    public function getMapConfig(): string;

    public function getExtraStyle(): string;

    public function draggable(bool $draggable = true): self;

    public function clickable(bool $clickable): self;

    public function setBoundsToBritishIsles(): self;

    public function defaultLocation(int|float $latitude, float|int $longitude): self;

    public function extraStyles(array $styles = []): self;

    public function rangeSelectField(string $rangeSelectField): self;

    public function drawCircleMarker(bool $draw = true): self;

    public function zoom(int $zoom): self;

    public function maxZoom(int $maxZoom): self;

    public function minZoom(int $minZoom): self;

    public function showMarker(bool $show = true): self;

    public function tilesUrl(string $url): self;

    public function boundaries(bool $on, int|float $southWestLat = 0, int|float $southWestLng = 0, int|float $northEastLat = 0, int|float $northEastLng = 0): self;

    public function detectRetina(bool $detectRetina = true): self;

    public function showZoomControl(bool $show = true): self;

    public function showFullscreenControl(bool $show = true): self;

    public function extraControl(array $control): self;

    public function extraTileControl(array $control): self;

    public function markerColor(string $color): self;

    public function liveLocation(bool $send = false, bool $realtime = false, int $miliseconds = 5000): self;

    public function showMyLocationButton(bool $showMyLocationButton = true): self;

    public function geoMan(bool $show = true): self;

    public function geoManEditable(bool $show = true): self;

    public function geoManPosition(string $position = 'topleft'): self;

    public function rotateMode(bool $rotate = true): self;

    public function drawMarker(bool $draw = true): self;

    public function drawPolygon(bool $draw = true): self;

    public function drawPolyline(bool $draw = true): self;

    public function drawCircle(bool $draw = true): self;

    public function editPolygon(bool $edit = true): self;

    public function deleteLayer(bool $delete = true): self;

    public function dragMode(bool $enable = true): self;

    public function cutPolygon(bool $enable = true): self;

    public function setColor(string $color): self;

    public function setFilledColor(string $filledColor): self;

    public function markerHtml(string $html): self ;

    public function markerIconUrl(?string $url): self;

    public function markerIconSize(array $size): self;

    public function markerIconClassName(string $className): self;

    public function markerIconAnchor(array $anchor): self;

    public function snappable(bool $snappable = true, int $distance = 20): self;

    public function drawRectangle(bool $draw = true): self;

    public function drawText(bool $draw = true): self;
}
