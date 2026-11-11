@props([
    'module',
])

@php
    $author = $module->authorUser;
    $isKajurOrAdmin = in_array(auth()->user()?->role, ['kajur', 'super_admin']);
    $isReviewable = $isKajurOrAdmin && $module->status === 'waiting_review';
    $avatarColors = ['bg-emerald-600', 'bg-indigo-600', 'bg-teal-600', 'bg-purple-600'];
    $colorClass = $avatarColors[($author?->id ?? 0) % count($avatarColors)];
@endphp

@if ($isKajurOrAdmin && in_array($module->status, ['waiting_review', 'approved', 'revision']))
    <div class="bg-white rounded-2xl border border-slate-200 shadow-xs overflow-hidden mb-6" x-data="{ showRevisionForm: false }">

        <div class="flex flex-col md:flex-row md:items-start gap-5 p-5">

            {{-- Author Identity --}}
            <div class="flex items-center gap-4 min-w-0 flex-1">
                {{-- Avatar / Initials --}}
                <div class="w-14 h-14 rounded-xl {{ $colorClass }} text-white flex items-center justify-center text-xl font-bold shrink-0 shadow-xs">
                    {{ $author?->initials() ?? '?' }}
                </div>

                {{-- Academic Data --}}
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-slate-900 truncate">
                        {{ $author?->name ?? 'Penyusun Tidak Diketahui' }}
                    </h3>
                    @if ($author?->nidn)
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            <span class="text-slate-400">NIDN:</span> {{ $author->nidn }}
                        </p>
                    @endif
                    @if ($author?->department)
                        <p class="text-xs text-emerald-700 font-semibold mt-0.5">
                            {{ $author->department->name }}
                        </p>
                    @endif
                    {{-- Submission Timestamp --}}
                    <p class="text-[11px] text-slate-400 mt-1">
                        Submitted at: <strong class="text-slate-500">{{ $module->updated_at->format('d M Y, H:i') }} WIB</strong>
                    </p>
                </div>
            </div>

            {{-- Status Badge + Quick Actions --}}
            <div class="flex flex-col items-end gap-2.5 shrink-0">

                {{-- Current Module Status --}}
                @if ($module->status === 'waiting_review')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        In Review — Menunggu Evaluasi
                    </span>
                @elseif ($module->status === 'approved')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Disetujui (ACC)
                    </span>
                @elseif ($module->status === 'revision')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Dikembalikan untuk Revisi
                    </span>
                @endif

                @if ($isReviewable)
                    {{-- Quick Action Buttons --}}
                    <div class="flex items-center gap-2">
                        {{-- ACC / Approve Module --}}
                        <form method="POST" action="{{ route('kajur.modules.approve', $module) }}"
                              onsubmit="return confirm('Setujui modul ini? Status berubah menjadi Approved dan modul dapat diakses publik.');">
                            @csrf
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white text-xs font-bold rounded-xl shadow-xs transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                ACC / Terima Modul
                            </button>
                        </form>

                        {{-- Toggle Revision Notes Form --}}
                        <button type="button"
                                @click="showRevisionForm = !showRevisionForm"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-50 hover:bg-amber-100 border border-amber-300 text-amber-800 text-xs font-bold rounded-xl transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                            Kembalikan (Revisi)
                        </button>
                    </div>
                @endif
            </div>
        </div>

        {{-- Revision Notes (Kajur only) --}}
        @if ($module->status === 'revision' && $module->kajur_notes)
            <div class="mx-5 mb-5 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-xs font-bold text-amber-800 uppercase tracking-wider mb-1">Catatan Revisi dari Kajur</p>
                <p class="text-sm text-amber-900 leading-relaxed">{{ $module->kajur_notes }}</p>
            </div>
        @endif

        {{-- Inline Revision Notes Form (expandable) --}}
        @if ($isReviewable)
            <div x-show="showRevisionForm" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="border-t border-slate-100 bg-slate-50/50 px-5 py-4">
                <form method="POST" action="{{ route('kajur.modules.reject', $module) }}">
                    @csrf
                    <label for="kajur_notes_{{ $module->id }}" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Catatan Perbaikan untuk Dosen <span class="text-red-500">*</span>
                    </label>
                    <textarea name="kajur_notes"
                              id="kajur_notes_{{ $module->id }}"
                              rows="3"
                              required
                              placeholder="Tuliskan catatan revisi secara spesifik agar dosen dapat memperbaiki modul dengan tepat..."
                              class="w-full text-sm rounded-xl border-slate-300 focus:border-amber-500 focus:ring-amber-500 py-2.5 px-3.5 placeholder:text-slate-400"></textarea>
                    <div class="flex justify-end gap-2 mt-3">
                        <button type="button" @click="showRevisionForm = false"
                                class="px-4 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                                class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs rounded-xl shadow-xs transition">
                            Kirim Catatan & Kembalikan Modul
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- Sayembara context: ACC as Master Module --}}
        @if ($isKajurOrAdmin && $module->sayembara_id && $module->status === 'waiting_review' && !$module->is_master)
            <div class="border-t border-slate-100 px-5 py-3 bg-purple-50/30 flex items-center justify-between gap-3">
                <p class="text-xs text-purple-800">
                    <strong>Sayembara:</strong> {{ $module->sayembara?->title ?? '-' }} —
                    Modul ini dapat ditetapkan langsung sebagai <strong>Master Modul Resmi</strong> untuk mata kuliah terkait.
                </p>
                <form method="POST" action="{{ route('sayembaras.approve-master', [$module->sayembara_id, $module]) }}"
                      onsubmit="return confirm('Tetapkan sebagai MASTER MODUL? Semua kelas mata kuliah ini otomatis menggunakan modul ini.');">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold rounded-xl shadow-xs transition whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                        ACC & Tetapkan Master
                    </button>
                </form>
            </div>
        @endif
    </div>
@endif

