@props([
    'sayembara',
    'variant' => 'dosen', // 'dosen' or 'kajur'
])

@php
    $participants = $sayembara->participants();
    $count = $participants->count();
    $avatarColors = [
        'bg-emerald-600 text-white',
        'bg-indigo-600 text-white',
        'bg-amber-600 text-white',
        'bg-rose-600 text-white',
        'bg-teal-600 text-white',
        'bg-purple-600 text-white',
    ];
@endphp

<div class="inline-flex items-center gap-2.5">
    @if ($count > 0)
        {{-- Avatar Stack Group --}}
        <div class="flex -space-x-2 overflow-hidden py-0.5">
            @foreach ($participants->take(4) as $index => $participant)
                @php
                    $colorClass = $avatarColors[$index % count($avatarColors)];
                    $initials = $participant->initials();
                @endphp
                <span
                    class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-bold ring-2 ring-white shadow-2xs {{ $colorClass }}"
                    title="{{ $participant->name }} ({{ $participant->nidn ? 'NIDN: ' . $participant->nidn : 'Dosen' }})"
                >
                    {{ $initials }}
                </span>
            @endforeach

            @if ($count > 4)
                <span
                    class="inline-flex items-center justify-center w-7 h-7 rounded-full text-xs font-semibold bg-gray-200 text-gray-700 ring-2 ring-white shadow-2xs"
                    title="{{ $count - 4 }} Dosen lainnya: {{ $participants->skip(4)->pluck('name')->implode(', ') }}"
                >
                    +{{ $count - 4 }}
                </span>
            @endif
        </div>

        {{-- Text Label --}}
        <span class="text-xs font-medium text-emerald-900 flex items-center gap-1">
            <strong class="font-bold text-emerald-800">{{ $count }} Dosen</strong> sedang menyusun modul ini
            <span class="inline-block w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
        </span>
    @else
        @if ($variant === 'kajur')
            {{-- Kajur Tracking Metric: Zero participant alert --}}
            <div class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-full">
                <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>Belum ada peserta</span>
                @if ($sayembara->end_date && now()->diffInDays($sayembara->end_date, false) <= 3)
                    <span class="text-[10px] font-bold uppercase tracking-wider text-red-600 bg-red-100 px-1 rounded">Mendesak</span>
                @endif
            </div>
        @else
            {{-- Dosen View: FOMO Encouragement --}}
            <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-800 bg-emerald-100/70 border border-emerald-200/60 px-2.5 py-1 rounded-full">
                <svg class="w-3.5 h-3.5 text-emerald-600 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <span>Jadilah dosen pertama yang menyusun modul ini!</span>
            </span>
        @endif
    @endif
</div>

