<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">

    <div x-data="mapPicker($wire, {{ $getMapConfig() }}, @js($getState()))" x-init="async () => {
        do {
            await (new Promise(resolve => setTimeout(resolve, 100)));
        } while (!$refs.map && !$refs.formRestorationInput);
        attach($refs.map, $refs);
    }" wire:ignore>
        <div x-ref="map" class="w-full" style="min-height: 30vh; {{ $getExtraStyle() }}"></div>

        <input type="text" x-ref="formRestorationInput" id="{{ $getId() }}_fmrest" style="display:none" />
    </div>

</x-dynamic-component>
