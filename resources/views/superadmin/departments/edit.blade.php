<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a
                href="{{ route('departments.index') }}"
                class="p-2 rounded-xl text-gray-500 hover:text-gray-700 hover:bg-gray-100 transition"
                title="Kembali ke Daftar Jurusan"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Jurusan') }}
                </h2>
                <p class="mt-0.5 text-sm text-gray-500">
                    Perbarui data jurusan dan fakultas naungan.
                </p>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl border border-gray-200/80 p-6 sm:p-8 shadow-xs">
                <form action="{{ route('departments.update', $department) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Dropdown Pilih Fakultas --}}
                    <div>
                        <label for="faculty_id" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Fakultas <span class="text-red-500">*</span>
                        </label>
                        <select
                            name="faculty_id"
                            id="faculty_id"
                            required
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 @error('faculty_id') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        >
                            <option value="">-- Pilih Fakultas --</option>
                            @foreach ($faculties as $faculty)
                                <option value="{{ $faculty->id }}" {{ old('faculty_id', $department->faculty_id) == $faculty->id ? 'selected' : '' }}>
                                    {{ $faculty->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('faculty_id')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Input Nama Jurusan --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Nama Jurusan / Program Studi <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $department->name) }}"
                            placeholder="Contoh: Teknik Informatika"
                            required
                            class="w-full rounded-xl border-gray-300 shadow-2xs focus:border-emerald-500 focus:ring-emerald-500 text-sm py-2.5 px-3.5 placeholder:text-gray-400 @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                        >
                        @error('name')
                            <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a
                            href="{{ route('departments.index') }}"
                            class="px-4 py-2.5 rounded-xl border border-gray-300 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition"
                        >
                            Batal
                        </a>
                        <button
                            type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white font-semibold text-sm rounded-xl shadow-xs transition"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>Perbarui Jurusan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
