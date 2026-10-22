---
version: alpha
name: EDDY'S AI
description: Design system untuk EDDY'S AI — platform belajar Bahasa Inggris dengan tutor AI (Mr. Khoirul). Tampilan bersih, ramah, dan memotivasi, dengan aksen hangat untuk elemen gamifikasi.
colors:
  primary: "#10375C"
  secondary: "#EB8317"
  accent: "#F3C623"
  surface: "#F4F6FF"
  background: "#FFFFFF"
  on-primary: "#FFFFFF"
  on-secondary: "#FFFFFF"
  on-accent: "#10375C"
typography:
  h1:
    fontFamily: Plus Jakarta Sans
    fontSize: 2.5rem
    fontWeight: 700
    lineHeight: 1.2
  h2:
    fontFamily: Plus Jakarta Sans
    fontSize: 2rem
    fontWeight: 600
    lineHeight: 1.25
  h3:
    fontFamily: Plus Jakarta Sans
    fontSize: 1.5rem
    fontWeight: 600
    lineHeight: 1.3
  body:
    fontFamily: DM Sans
    fontSize: 1rem
    fontWeight: 400
    lineHeight: 1.6
  label:
    fontFamily: DM Sans
    fontSize: 0.875rem
    fontWeight: 500
    lineHeight: 1.4
rounded:
  sm: 6px
  md: 10px
  lg: 16px
  xl: 20px
  2xl: 28px
spacing:
  sm: 8px
  md: 16px
  lg: 24px
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.on-primary}"
    rounded: "{rounded.lg}"
    padding: "py-3 px-5"
  button-secondary:
    backgroundColor: "{colors.secondary}"
    textColor: "{colors.on-secondary}"
    rounded: "{rounded.lg}"
    padding: "py-3 px-5"
  badge-achievement:
    backgroundColor: "{colors.accent}"
    textColor: "{colors.on-accent}"
    rounded: "{rounded.2xl}"
  card:
    backgroundColor: "{colors.surface}"
    textColor: "{colors.primary}"
    rounded: "{rounded.xl}"
    padding: 24px
  card-on-white:
    backgroundColor: "{colors.background}"
    textColor: "{colors.primary}"
    rounded: "{rounded.xl}"
    borderColor: "#E5E9F5"
---

# DESIGN — EDDY'S AI

## Overview

EDDY'S AI adalah platform belajar Bahasa Inggris dengan tutor AI personal, Mr. Khoirul. Karakter tampilan: **bersih, ramah, dan memotivasi** — bukan kaku seperti aplikasi kelas formal, tapi juga tidak berlebihan main-main. Biru navy sebagai warna penopang utama (kepercayaan, fokus belajar), oranye sebagai aksen aksi (tombol, ajakan bertindak), dan kuning keemasan khusus untuk momen pencapaian (badge, sertifikat, streak) — supaya progres belajar terasa dirayakan, bukan sekadar dicatat.

Background utama putih bersih, dengan satu lapis warna `surface` (biru sangat muda) untuk membedakan card/section dari halaman.

## Colors

| Token | Hex | Dipakai untuk |
|-------|-----|---------------|
| Primary (`#10375C`) | Navy gelap | Header, teks judul penting, tombol utama, ikon aktif, elemen yang butuh kesan tepercaya/serius |
| Secondary (`#EB8317`) | Oranye | Tombol aksi sekunder, CTA hangat ("Mulai Belajar", "Lanjutkan"), highlight interaktif |
| Accent (`#F3C623`) | Kuning keemasan | Badge/achievement, indikator streak, elemen gamifikasi, sertifikat — dipakai **terbatas**, bukan warna dominan |
| Surface (`#F4F6FF`) | Biru sangat muda | Background card, section pembeda, area konten sekunder |
| Background (`#FFFFFF`) | Putih | Background halaman utama |

**Aturan pemakaian:**
- Primary dan Secondary tidak boleh dipakai bersamaan sebagai dua tombol setara di satu area (lihat Do's and Don'ts) — Primary untuk aksi utama, Secondary untuk aksi kedua.
- Accent (kuning) khusus untuk elemen pencapaian, jangan dipakai sebagai warna tombol biasa — supaya tetap terasa "istimewa" saat siswa melihatnya.

## Typography

Dua font dengan peran berbeda:
- **Plus Jakarta Sans** — untuk heading (h1, h2, h3). Bentuknya modern dan tegas, cocok untuk kesan memotivasi tanpa kehilangan kejelasan.
- **DM Sans** — untuk body text dan label. Netral dan sangat mudah dibaca di layar kecil, penting karena banyak konten Bahasa Inggris + Indonesia bercampur dalam satu halaman.

Hierarki dibentuk dari ukuran dan ketebalan, bukan mengganti-ganti font.

## Layout

Grid 12 kolom untuk halaman desktop, satu kolom penuh di mobile (mobile-first, karena target pengguna banyak mengakses dari HP). Padding halaman standar `24px`, gap antar section `24px`. Card konten menggunakan `surface` sebagai background untuk memisahkan area belajar dari navigasi.

## Elevation & Depth

- **Card konten belajar (lesson, vocabulary, dsb)**: tanpa shadow, cukup dibedakan lewat warna `surface` vs `background`.
- **Card achievement/certificate**: shadow halus + border tipis `accent`, untuk memberi kesan "spesial" dibanding card biasa.
- **Modal & dropdown**: shadow untuk memisahkan dari konten di belakangnya.
- Hindari shadow berat di elemen belajar sehari-hari — biar halaman tidak terasa "berat" saat dipakai lama (sesi belajar bisa panjang).

## Shapes

| Elemen | Radius |
|--------|--------|
| Input, select | `rounded-md` (10px) |
| Tombol | `rounded-lg` (16px) |
| Card konten | `rounded-xl` (20px) |
| Badge/achievement | `rounded-2xl` (28px) — sengaja lebih bulat, kesan "koin/medali" |

Jangan campur sudut tajam dan membulat di halaman yang sama.

## Components

- **`button-primary`** — aksi utama tiap halaman ("Mulai Lesson", "Submit Jawaban"). Satu per halaman/section, jangan lebih.
- **`button-secondary`** — aksi kedua ("Lewati", "Lihat Detail").
- **`badge-achievement`** — badge/sertifikat/streak. Warna accent, dipakai untuk momen pencapaian saja.
- **`card`** — permukaan default konten belajar, background `surface`.
- **`card-on-white`** — dipakai saat card berada di atas background `surface` (misal dashboard), agar tetap kontras dengan bordernya sendiri.

## Do's and Don'ts

- **Do**: pakai token reference (`{colors.primary}`) supaya satu sumber kebenaran.
- **Do**: pakai warna `accent` (kuning) secara terbatas — hanya untuk pencapaian, supaya efeknya tetap terasa spesial.
- **Do**: jaga kontras teks di atas `primary` (navy) — selalu pakai `on-primary` (putih), jangan teks gelap di atas navy.
- **Don't**: menambah warna baru di luar 4 warna + putih ini tanpa memperluas palet dulu di schema.
- **Don't**: memakai `accent` sebagai warna tombol aksi biasa — bisa bikin gamifikasi terasa "murahan" kalau terlalu sering muncul.
- **Don't**: mencampur `button-primary` dan `button-secondary` sebagai dua pilihan yang kelihatan setara pentingnya di satu layar — harus jelas mana aksi utama.
