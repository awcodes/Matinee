@php
    $state = $getState();
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
    <div x-data="{preview: @js($shouldShowPreview())}">
        {{ $getChildComponentContainer() }}
        @if($state && $state['embed_url'])
            <div class="mt-6">
                <button
                    type="button"
                    x-on:click="preview = ! preview"
                    class="text-sm text-primary-500 hover:text-primary-400 focus:text-primary-400"
                >
                    <span x-show="!preview">{{ trans('matinee::matinee.show_preview') }}</span>
                    <span x-show="preview">{{ trans('matinee::matinee.hide_preview') }}</span>
                </button>
                <div x-cloak x-show="preview" class="fi-input-wrp mt-2 ring-1 transition duration-75 rounded-lg overflow-hidden aspect-video w-full h-auto bg-gray-300/30 ring-gray-950/10 dark:ring-white/20 dark:bg-gray-800/20">
                    <x-matinee::embed
                        :data="$state"
                        allow="fullscreen; picture-in-picture"
                        allowfullscreen="true"
                        class="w-full h-full"
                    ></x-matinee::embed>
                </div>
            </div>
        @endif
    </div>
</x-filament::fieldset>
