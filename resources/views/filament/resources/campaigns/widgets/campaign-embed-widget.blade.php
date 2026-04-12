<x-filament-widgets::widget>
    <x-filament::section heading="Widget Embed Code" description="Customize the parameters below and copy the generated snippet to your website.">

        {{ $this->form }}

        @php
            $uuid = $record->uuid;
            $lang = $data['language'] ?? 'de';
            $source = $data['source'] === 'organic' ? '' : "\n        source: '{$data['source']}',";
            $originValue = $data['origin'] ?? '';
            $origin = blank($originValue) ? '' : "\n        origin: '{$originValue}',";
            $theme = $data['theme'] ?? 'minimal';
            $version = $data['version'] ?? 'latest';
            $showProgress = $data['showProgress'] ? "\n        showProgress: true," : '';

            $widgetUrl = config('app.widget_url');
            $apiUrl = config('app.url') . '/api/v1';

            $code = <<<HTML
<div id="voces-campaign-widget"></div>

<script src="{$widgetUrl}/{$version}/voces-widget.js"></script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    window.voces.widget({
        campaignUuid: '{$uuid}',
        target: '#voces-campaign-widget',
        theme: '{$theme}',
        lang: '{$lang}',{$source}{$origin}
        apiBaseUrl: "{$apiUrl}",
    });
  });
</script>
HTML;
        @endphp

        <div class="mt-6" style="position: relative;" x-data="{ copied: false }">

            <pre style="background: rgb(17, 24, 39); color: rgb(243, 244, 246); padding: 1rem; border-radius: 0.5rem; font-size: 0.875rem; overflow-x: auto;"><code x-ref="codeBlock">{{ trim($code) }}</code></pre>

            <div style="position: absolute; top: 0.5rem; right: 0.5rem;">
                <x-filament::button
                    color="gray"
                    icon="heroicon-m-clipboard-document"
                    size="sm"
                    x-on:click="navigator.clipboard.writeText($refs.codeBlock.innerText); copied = true; setTimeout(() => copied = false, 2000)"
                >
                    <span x-show="!copied">Copy</span>
                    <span x-show="copied" x-cloak style="color: var(--success-500);">Copied!</span>
                </x-filament::button>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
