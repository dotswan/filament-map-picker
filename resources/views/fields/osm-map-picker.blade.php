<x-filament-forms::field-wrapper
    :id="$getId()"
    :label="$getLabel()"
    :label-sr-only="$isLabelHidden()"
    :helper-text="$getHelperText()"
    :hint="$getHint()"
    :required="$isRequired()"
    :state-path="$getStatePath()"
>
    <div
        x-data="async () => {
            do {
                await (new Promise(resolve => setTimeout(resolve, 100)));
            } while (window.mapPicker === undefined);
            const m = mapPicker($wire, {{ $getMapConfig()}});
            m.attach($refs.map);
        }"
        wire:ignore>
        <div
            x-ref="map"
            class="w-full" style="min-height: 30vh; z-index: 1 !important;">
        </div>
    </div>
</x-filament-forms::field-wrapper>
