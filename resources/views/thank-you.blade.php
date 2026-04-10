<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Verification Successful') }} - {{ $campaign->title }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 text-gray-900 antialiased font-sans flex items-center justify-center min-h-screen p-4">

    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 text-center border border-gray-100">

        <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-6">
            <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
            </svg>
        </div>

        <h2 class="text-2xl font-bold text-gray-900 mb-4">
            {{ __('Verification Successful!') }}
        </h2>

        <div class="text-gray-600 mb-8 leading-relaxed">
            @if(!empty($campaign->verification_success_message))
                {{ $campaign->verification_success_message }}
            @else
                {{ __('Thank you! Your email address has been verified and your signature has been counted for') }} <strong>{{ $campaign->title }}</strong>.
            @endif
        </div>

        <a href="https://docs.voces.ch/de" target="_blank" rel="noopener" class="inline-block text-sm font-medium text-gray-600 hover:text-orange-600 transition-colors duration-150">
            {{ __('Built with ❤️ using voces.ch') }}
        </a>

    </div>

</body>
</html>
