@php
    $resolvedRenderHook = $languageSwitch->getRenderHook();
@endphp

    @vite('resources/css/app.css')
<x-filament::dropdown.list>
    <div
        class="flex flex-row items-center justify-center gap-1 rounded-xl bg-white p-1 shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10"
    >
        @foreach ($locales as $locale)
            <button
                type="button"
                wire:click="changeLocale('{{ $locale }}')"
                @class([
                    'flex flex-1 items-center justify-center rounded-lg p-2 transition-colors focus-visible:outline focus-visible:outline-2 focus-visible:outline-ring-2',
                    'bg-gray-50 text-primary-600 dark:bg-white/5 dark:text-primary-400' => app()->isLocale($locale),
                    'text-gray-400 hover:text-gray-500 focus-visible:text-gray-500 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:text-gray-400' => !app()->isLocale($locale),
                ])
                x-tooltip="{
                    content: @js($languageSwitch->getLabel($locale)),
                    theme: $store.theme,
                    placement: 'bottom',
                }"
            >
                @if ($isFlagsOnly || $hasFlags)
                    <x-language-switch::flag
                        :src="$languageSwitch->getFlag($locale)"
                        :circular="$isCircular"
                        :alt="$languageSwitch->getLabel($locale)"
                        class="w-5 h-5"
                    />
                @else
                    <span class="font-semibold text-sm">{{ $languageSwitch->getCharAvatar($locale) }}</span>
                @endif
            </button>
        @endforeach
    </div>
</x-filament::dropdown.list>
