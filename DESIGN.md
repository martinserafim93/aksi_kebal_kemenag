---
name: AKSI KEBAL
description: Sistem Informasi Absensi Kegiatan Pegawai Kementerian Agama
colors:
  primary: "#10b981"
  primary-hover: "#059669"
  primary-light: "#d1fae5"
  text-main: "#334155"
  text-muted: "#64748b"
  border: "#e2e8f0"
  content-bg: "#f8fafc"
  card-bg: "#ffffff"
  success: "#10b981"
  error: "#ef4444"
  warning: "#f59e0b"
typography:
  body:
    fontFamily: "Figtree, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif"
  headline:
    fontFamily: "Poppins, sans-serif"
    fontWeight: 600
rounded:
  md: "0.5rem"
  lg: "0.75rem"
  xl: "1rem"
  full: "9999px"
spacing:
  sm: "0.5rem"
  md: "1rem"
  lg: "1.5rem"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "#ffffff"
    rounded: "{rounded.md}"
    padding: "0.75rem 1.5rem"
  button-primary-hover:
    backgroundColor: "{colors.primary-hover}"
---

# Design System: AKSI KEBAL

## Overview

**Creative North Star: "The Modern Institution"**

Desain AKSI KEBAL (Aplikasi Sistem Informasi Kehadiran Berbasis Lokasi) mengusung filosofi "The Modern Institution" yang bersih, mengutamakan digital-first, dan mudah diakses oleh pengguna. Antarmuka menyeimbangkan kredibilitas resmi institusi pemerintahan (Kementerian Agama) dengan kenyamanan interaksi dari aplikasi modern. Kami menghindari pola desain kaku masa lalu, menggantinya dengan ruang lega (*whitespace*), tipografi yang ramah (*approachable*), dan umpan balik visual (*micro-interactions*) yang halus. 

**Key Characteristics:**
- **Bersih & Lapang**: Penggunaan ruang putih (*whitespace*) yang ekstensif untuk mengurangi beban kognitif.
- **Kredibel & Resmi**: Menggunakan palet hijau Kemenag dengan cara yang terukur, tidak berlebihan.
- **Taktil**: Komponen terasa dapat disentuh melalui penggunaan radius membulat dan efek *depth*/*shadow* yang halus.

## Colors

Palet warna difokuskan pada ketenangan, kebersihan, dan profesionalisme.

### Primary
- **Official Emerald** (#10b981): Digunakan sebagai warna merek utama (logo, tombol aksi utama, dan aksen state aktif). Memancarkan kesan resmi, dapat dipercaya, dan mantap.
- **Emerald Hover** (#059669): Digunakan untuk *state* interaktif (hover/active) pada elemen utama.
- **Emerald Light** (#d1fae5): Digunakan sebagai latar belakang lembut untuk komponen *success* atau aksen ringan.

### Semantic
- **Success** (#10b981): Berbagi nilai dengan warna utama, memperkuat asosiasi positif.
- **Warning** (#f59e0b): Untuk peringatan (seperti timeout sesi).
- **Error** (#ef4444): Untuk pesan kegagalan autentikasi atau validasi form.

### Neutral
- **Text Main** (#334155): Warna teks utama. Tidak menggunakan hitam murni untuk mengurangi ketegangan mata.
- **Text Muted** (#64748b): Teks sekunder, placeholder, dan ikon pendukung.
- **Border** (#e2e8f0): Pembatas elemen dan input form.
- **Content Background** (#f8fafc): Latar belakang utama aplikasi yang memberikan efek bersih dan sejuk.
- **Card Background** (#ffffff): Latar belakang komponen melayang (kartu, modal).

### Named Rules
**The 10% Accent Rule.** Warna *Official Emerald* hanya boleh mendominasi maksimal 10% dari total luas layar. Biarkan elemen bernapas di atas latar belakang netral.

## Typography

**Display Font:** Poppins (with sans-serif)
**Body Font:** Figtree (with system-ui, -apple-system, sans-serif)

**Character:** Bersih, ramah, dan sangat mudah terbaca di perangkat mobile.

### Hierarchy
- **Headline** (600, variabel ukuran): Digunakan untuk judul halaman, tajuk kartu, dan salam sambutan.
- **Body** (400, 1rem, 1.6): Digunakan untuk teks umum, deskripsi, dan isi paragraf.
- **Label** (500, 0.875rem): Digunakan untuk label input form, teks tombol kecil, dan elemen antarmuka yang padat.

### Named Rules
**The Legibility First Rule.** Kontras teks harus selalu melewati standar WCAG AA. Teks sekunder tidak boleh lebih terang dari `#64748b`.

## Layout

Aplikasi dirancang agar responsif secara mulus antara Desktop dan Mobile. Sistem menggunakan model *container-centric* di mana konten sentral (seperti form login) dibatasi lebar maksimumnya (mis. `400px` untuk *auth-card*) dan dipusatkan di layar menggunakan *flexbox* atau *grid*.

## Elevation & Depth

Sistem ini menggunakan bayangan dinamis (*dynamic shadow*) untuk menciptakan ruang taktil yang lembut di atas bidang latar belakang. 

### Shadow Vocabulary
- **Shadow Small** (`var(--shadow-sm)`): Digunakan untuk tombol sekunder atau interaksi *hover* ringan.
- **Shadow Medium** (`var(--shadow-md)`): Standar untuk kartu konten reguler.
- **Shadow Glass** (`0 8px 32px rgba(16, 185, 129, 0.08)...`): Bayangan khusus dengan tint hijau yang membaur untuk kartu utama (seperti *auth-card*), memberikan kesan premium.

### Named Rules
**The Hover Lift Rule.** Bayangan akan sedikit meluas dan lebih pekat saat elemen di-*hover*, menciptakan ilusi bahwa elemen mendekati kursor.

## Shapes

Bentuk didominasi oleh sudut melengkung moderat (*rounded corners*) untuk menumbuhkan kesan *approachable*. 
- **Radius Medium** (`0.5rem` / 8px): Digunakan untuk elemen kecil seperti tombol dan input.
- **Radius X-Large** (`1rem` / 16px): Digunakan untuk komponen struktural besar seperti *card* dan *modal*.

## Components

### Buttons
- **Shape:** Melengkung (0.5rem radius)
- **Primary:** Background Official Emerald (#10b981) dengan teks putih murni. Padding proporsional (0.75rem vertikal, 1.5rem horizontal).
- **Hover / Focus:** Transisi latar belakang menjadi Emerald Hover (#059669) disertai penambahan *drop-shadow*.
- **Ghost/Link:** Hanya warna teks tanpa *background*.

### Inputs / Fields
- **Style:** Latar transparan atau putih utuh, *border* abu-abu halus (`#e2e8f0`), sudut 0.5rem. Terdapat dukungan ikon di sebelah kiri.
- **Focus:** *Border* berubah menjadi hijau utama, dengan tambahan *ring/glow* `rgba(16, 185, 129, 0.2)`.

### Cards
- **Corner Style:** 1rem
- **Background:** Putih Solid (#ffffff)
- **Shadow Strategy:** Shadow Glass / Shadow Medium
- **Internal Padding:** 2.75rem (untuk layar Desktop)

## Do's and Don'ts

### Do:
- **Do** gunakan ruang kosong secara berlimpah untuk memisahkan grup konten.
- **Do** pastikan label input diiringi ikon yang konsisten dari keluarga ikon SVG yang sama (stroke 2px).
- **Do** sediakan mekanisme umpan balik (misalnya animasi transisi halus) untuk setiap aksi pengguna.

### Don't:
- **Don't** gunakan font "Inter" (yang sudah terasa terlalu *generic*); gunakan "Figtree".
- **Don't** gunakan garis tepi beraksen tebal (*side-tab accent border*) di sebelah kartu.
- **Don't** aplikasikan fungsi *easing* memantul (seperti `cubic-bezier(0.34, 1.56, ...)`). Gunakan percepatan halus (`ease-out-quart`).
