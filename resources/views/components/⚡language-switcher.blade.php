<?php

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component
{
    public array $locales = [
        'de' => 'Deutsch',
        'fr' => 'Français',
        'it' => 'Italiano',
        'en' => 'English',
    ];

    public function switchLanguage($locale)
    {
        if (array_key_exists($locale, $this->locales)) {
            $user = Auth::user();
            $user->locale = $locale;
            $user->save();

            // Refresh the page to apply the new locale middleware
            return redirect(request()->header('Referer') ?? '/');
        }
    }
};
?>

<div class="mr-4">
    <x-filament::dropdown placement="bottom-end">
        {{-- The Trigger Button (The Square with the Locale Code) --}}
        <x-slot name="trigger">
            <button
                type="button"
                class="flex items-center justify-center w-9 h-9 font-bold text-xs transition-colors duration-75 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700 ring-1 ring-gray-950/10 dark:ring-white/20"
                title="Change Language"
            >
                {{ strtoupper(auth()->user()->locale ?? 'DE') }}
            </button>
        </x-slot>

        {{-- The Dropdown List --}}
        <x-filament::dropdown.list>
            @foreach($locales as $code => $name)
                <x-filament::dropdown.list.item
                    wire:click="switchLanguage('{{ $code }}')"
                    tag="button"
                    :color="auth()->user()->locale === $code ? 'primary' : 'gray'"
                    :icon="auth()->user()->locale === $code ? 'heroicon-m-check' : null"
                >
                    {{ $name }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
