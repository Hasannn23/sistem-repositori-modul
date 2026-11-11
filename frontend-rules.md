# Repositori Modul Frontend Rules & Anti-Slop UI

Anda adalah ahli Frontend Developer. Setiap kali diminta membuat atau mengedit file `.blade.php`, Anda WAJIB mematuhi aturan berikut tanpa terkecuali:

## 1. Tech Stack
- HANYA gunakan **Tailwind CSS** dan **Alpine.js**. 
- DILARANG KERAS menggunakan Bootstrap, jQuery, atau menulis custom CSS/JS vanilla jika tidak diminta.

## 2. Desain Anti-Slop & Palet Warna Akademis
- **Background Utama:** Gunakan `bg-slate-50` (Jangan gunakan `bg-white` murni untuk latar belakang aplikasi).
- **Kartu/Kontainer:** Gunakan `bg-white border border-slate-200/80 rounded-xl shadow-sm`.
- **Tombol Aksi Utama:** Gunakan warna hijau kampus `bg-emerald-700 hover:bg-emerald-800 text-white transition-colors duration-200`.
- **Aksen & Badge:** Gunakan `bg-emerald-50 text-emerald-800`.
- **Teks:** Gunakan `text-slate-900` untuk judul/heading, dan `text-slate-600` untuk paragraf.

## 3. Komponen & UX Detail
- Hindari shadow yang berlebihan/tebal. Preferensi batas halus: `ring-1 ring-slate-900/5` atau `border-slate-200`.
- Form Input: Harus menggunakan `border-slate-300 focus:border-emerald-500 focus:ring-emerald-500`.
- Hirarki Tipografi: Gunakan `text-xs font-semibold tracking-wider uppercase` untuk label atau metadata kecil.
- Untuk area baca dokumen (LMS Canvas), WAJIB gunakan class Tailwind Typography: `prose prose-slate max-w-none`.

## 4. Interaktivitas
- Selalu gunakan `x-data`, `x-show`, `x-transition` dari Alpine.js untuk fitur buka-tutup (seperti dropdown, modal, atau accordion). Jangan membuat script manual.
- Tampilan wajib responsif (mobile-first) menggunakan breakpoint bawaan Tailwind (sm:, md:, lg:).