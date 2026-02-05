<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @php
        $state = $getState();
        $config = json_decode($getMapConfig(), true);
        if ($state && isset($state['lat']) && isset($state['lng'])) {
            $config['default']['lat'] = $state['lat'];
            $config['default']['lng'] = $state['lng'];
        }
    @endphp

    <div x-data="mapPicker($wire, @js($config))" x-init="async () => {
        do {
            await (new Promise(resolve => setTimeout(resolve, 100)));
        } while (!$refs.map && !$refs.formRestorationInput);
        attach($refs.map, $refs);
    }" wire:ignore>
        <div x-ref="map" class="w-full" style="min-height: 30vh; {{ $getExtraStyle() }}"></div>

        <input type="text" x-ref="formRestorationInput" id="{{ $getId() }}_fmrest" style="display:none" />
    </div>

</x-dynamic-component>
