@props(['size' => 'h-9 w-auto'])

@if (file_exists(public_path('images/logo.png')))
    <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name', 'PRISMA') }}" {{ $attributes->merge(['class' => 'object-contain ' . $size]) }}>
@elseif (file_exists(public_path('images/logo.svg')))
    <img src="{{ asset('images/logo.svg') }}" alt="{{ config('app.name', 'PRISMA') }}" {{ $attributes->merge(['class' => 'object-contain ' . $size]) }}>
@else
    {{-- Default PRISMA Modern Icon Badge --}}
    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => 'shrink-0 ' . $size]) }}>
        <rect width="40" height="40" rx="10" fill="url(#prisma_grad)" />
        <path d="M14 28V12H20.5C23.5376 12 26 14.4624 26 17.5C26 20.5376 23.5376 23 20.5 23H18.5V28H14ZM18.5 19.5H20.5C21.6046 19.5 22.5 18.6046 22.5 17.5C22.5 16.3954 21.6046 15.5 20.5 15.5H18.5V19.5Z" fill="white" />
        <defs>
            <linearGradient id="prisma_grad" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                <stop stop-color="#059669" />
                <stop offset="1" stop-color="#0d9488" />
            </linearGradient>
        </defs>
    </svg>
@endif
