<?php

declare(strict_types=1);

namespace Dotswan\MapPicker\Contracts;

interface MapOptions
{
    public function getMapConfig(): string;

    public function clickable(bool $clickable): self;

    public function rangeSelectField(string $rangeSelectField): self;

    public function draggable(bool $draggable = true): self;

    public function zoom(int $zoom): self;

    public function maxZoom(int $maxZoom): self;

    public function minZoom(int $minZoom): self;

    public function showMarker(bool $show = true): self;

    public function tilesUrl(string $url): self;

    public function detectRetina(bool $detectRetina = true): self;

    public function showZoomControl(bool $show = true): self;

    public function showFullscreenControl(bool $show = true): self;

    public function extraControl(array $control): self;

    public function extraTileControl(array $control): self;

    public function markerColor(string $color): self;

    public function liveLocation(bool $send = false, bool $realtime = false, int $miliseconds = 5000): self;
}
