<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    <div x-data="mapPicker($wire, {{ $getMapConfig() }}, @js($getState()))" x-init="async () => {
        do {
            await (new Promise(resolve => setTimeout(resolve, 100)));
        } while (!$refs.map);
        attach($refs.map);
    }" wire:ignore>
        <div x-ref="map" class="w-full" style="min-height: 30vh; z-index: 1 !important; {{ $getExtraStyle() }}">
        </div>
    </div>

</x-dynamic-component>
