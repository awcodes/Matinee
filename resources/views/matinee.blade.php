@php
    $state = $getState();
    $name = $getName();
@endphp

<x-filament::fieldset
    :label="$getLabel()"
    :label-hidden="$isLabelHidden()"
    :attributes="
        \Filament\Support\prepare_inherited_attributes($attributes)
            ->merge([
                'id' => $getId(),
            ], escape: false)
            ->merge($getExtraAttributes(), escape: false)
    "
>
    <div class="grid lg:grid-cols-2 gap-6">
        {{ $getChildComponentContainer() }}
        <div class="border border-gray-300 dark:border-gray-700 rounded-xl overflow-hidden aspect-video w-full h-auto bg-gray-300/30 dark:bg-gray-800/20">
            @if($state && $state[$name]['embed_url'])
                <iframe
                    src="{{ $state[$name]['embed_url'] }}"
                    width="640"
                    height="360"
                    allow="autoplay; fullscreen; picture-in-picture"
                    allowfullscreen
                    class="w-full h-full"
                ></iframe>
            @endif
        </div>
    </div>
</x-filament::fieldset>
