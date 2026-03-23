{{-- Standalone self-contained logo mark with gradient background --}}
<svg viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg" {{ $attributes }}>
    <defs>
        <linearGradient id="pkBg" x1="0" y1="0" x2="1" y2="1">
            <stop offset="0%" stop-color="#5c68a8"/>
            <stop offset="100%" stop-color="#3d4269"/>
        </linearGradient>
    </defs>

    {{-- Background --}}
    <rect width="48" height="48" rx="12" fill="url(#pkBg)"/>

    {{-- Ground line --}}
    <rect x="5" y="40" width="38" height="1.5" rx="0.75" fill="white" fill-opacity="0.25"/>

    {{-- Bar 1 — left, shortest --}}
    <rect x="5" y="30" width="10" height="10" rx="2" fill="white" fill-opacity="0.4"/>

    {{-- Bar 2 — centre --}}
    <rect x="19" y="23" width="10" height="17" rx="2" fill="white" fill-opacity="0.65"/>

    {{-- Bar 3 — right, tallest (main building) --}}
    <rect x="33" y="15" width="10" height="25" rx="2" fill="white"/>

    {{-- Roof peak on bar 3 --}}
    <path d="M32 15.5 L38 10 L44 15.5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>

    {{-- Rising trend / forecast line --}}
    <path d="M10 30 L24 23 L38 15" stroke="white" stroke-opacity="0.35" stroke-width="1.5" stroke-linecap="round" stroke-dasharray="2.5 2.5"/>
</svg>
