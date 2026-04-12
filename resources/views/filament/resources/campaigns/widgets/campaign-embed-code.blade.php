<pre style="background: rgb(17, 24, 39); color: rgb(243, 244, 246); padding: 1rem; border-radius: 0.5rem; font-size: 0.875rem; overflow-x: auto;">
    <code x-ref="codeBlock" class="language-javascript">
        <div id="voces-campaign-widget"></div>

        <script src="{{ $appUrl }}/widget/{{ $version }}/voces-widget.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.voces.widget({
                campaignUuid: '{{ $uuid }}',
                target: '#voces-campaign-widget',
                theme: '{{ $theme }}',
                lang: '{{ $lang }}',{{ $source }}{{ $origin }},
                apiUrl: "{{ config('app.url') }}/api/v1",
            });
        });
        </script>
    </code>
</pre>
