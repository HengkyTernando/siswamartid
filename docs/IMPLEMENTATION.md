# SiswaMart — Panduan Implementasi Style (Neubrutalism Rounded Lembut)

Dokumen ini adalah **sumber kebenaran** untuk tampilan SiswaMart. Semua halaman, komponen, dan fitur baru wajib mengikutinya agar tampilan konsisten dengan beranda.

> Catatan: nilai warna dan ukuran di bawah dirumuskan dari tampilan beranda yang sudah jadi. Jika ada selisih kecil dengan kode yang sudah ada, **samakan kode ke dokumen ini** (atau perbarui dokumen ini jika kode terbukti lebih tepat). Jangan biarkan keduanya berbeda.

---

## 1. Karakter visual

SiswaMart terasa **hangat, ramah, dan seperti toko kue/jajanan**, bukan agresif atau korporat.

Tiga ciri utama yang harus selalu muncul:

1. **Neubrutalism lembut:** border tegas berwarna cokelat tua + bayangan offset keras (tanpa blur).
2. **Serba membulat:** tidak ada sudut tajam. Kartu, tombol, chip, ikon, dan wadah semuanya bulat.
3. **Ikon outline pastel:** ikon garis cokelat tua di dalam wadah lingkaran berwarna pastel, bukan ikon hitam solid.

---

## 2. Design Tokens

Definisikan sebagai CSS variables (atau theme config) di satu tempat dan **pakai token, jangan hardcode nilai**.

```css
:root {
  /* Warna dasar */
  --ink: #3A2417;          /* pengganti hitam: teks judul, border, bayangan, ikon */
  --ink-soft: #6B4E3D;     /* teks sekunder / deskripsi */
  --cream: #FFF6E5;        /* latar halaman */
  --cream-2: #FFEBCB;      /* latar footer, chip, tombol sekunder */
  --white-warm: #FFFBF4;   /* isi kartu */

  /* Brand */
  --brand: #FF7A00;        /* oranye utama: navbar dan tombol */
  --brand-ink: #B34A00;     /* oranye gelap untuk harga dan angka */
  --brand-display: #E36A00; /* penekanan judul dengan kontras lebih tinggi di latar krem */

  /* Pastel kategori (latar kartu/tile/wadah ikon) */
  --lavender: #E3D9FB;     /* Dessert */
  --peach: #F8C9B0;        /* Makanan Berat */
  --mint: #B9E6D9;         /* Minuman */
  --butter: #FFE79F;       /* Snack */

  /* Blok Cara Memesan / aksen sukses */
  --green: #4E7344;

  /* Aksen angka & status */
  --accent-purple: #8B6BD0;
  --accent-teal: #2F7F6F;
  --accent-gold: #B8860B;
  --success-bg: #BFE8C8;   /* badge TERSEDIA */
  --warning-bg: #FFD54A;   /* badge PRE-ORDER / FAVORIT */

  /* Bentuk */
  --border-w: 2.5px;
  --border: var(--border-w) solid var(--ink);
  --r-card: 28px;
  --r-tile: 28px;
  --r-block: 36px;         /* blok besar (Cara Memesan) */
  --r-input: 18px;
  --r-pill: 999px;

  /* Bayangan neubrutal (offset keras, tanpa blur) */
  --shadow: 4px 4px 0 var(--ink);
  --shadow-hover: 6px 6px 0 var(--ink);
  --shadow-active: 2px 2px 0 var(--ink);

  /* Layout */
  --container: 1200px;     /* lebar maksimum halaman dalam */
  --gutter: clamp(16px, 4vw, 24px);
  --section-gap: clamp(40px, 6vw, 80px);

  /* Container homepage dipertahankan lebih lebar sesuai komposisi hero */
  --container-home: 1280px;
  --gutter-home: clamp(18px, 3.5vw, 48px);
  --section-gap-home: clamp(40px, 4vw, 64px);
}
```

Aturan token:

- **Dilarang** memakai `#000`, `#111`, atau hitam murni untuk teks, border, bayangan, atau ikon. Selalu `var(--ink)`.
- Warna pastel kategori selalu dipakai konsisten: Dessert = lavender, Makanan Berat = peach, Minuman = mint, Snack = butter. Jangan menukar atau menambah warna kategori tanpa memperbarui dokumen ini.
- Teks kecil berwarna oranye (harga, angka) memakai `--brand-ink`, bukan `--brand`, agar lolos kontras.

---

## 3. Tipografi

| Peran | Font | Aturan |
|---|---|---|
| Judul (hero, judul section, angka langkah, nama brand) | Font bulat ramah yang sudah dipakai di repo (Fredoka / Baloo 2) | Bobot 600–700, UPPERCASE untuk judul section, line-height 1.05–1.1, letter-spacing 0 hingga -0.01em |
| Body | Font sans yang sudah dipakai di repo | 16px, line-height 1.6, warna `--ink` atau `--ink-soft` |
| Label kecil (badge, chip, eyebrow) | Font body | Bobot 700, 11–13px, UPPERCASE untuk badge dan eyebrow |

Skala (gunakan `clamp()` agar responsif):

- Hero H1: `clamp(2.4rem, 5.2vw, 4.5rem)`
- Judul section H2: `clamp(1.75rem, 3.6vw, 2.75rem)`
- Judul kartu: 18–20px
- Body: 16px, teks kecil: 14px, label: 11–13px

Penekanan kata pada judul memakai `--brand-display` (#E36A00) untuk menjaga kontras teks besar terhadap latar krem; tombol dan elemen brand memakai `--brand`.

---

## 4. Layout & Spasi

- Semua konten (termasuk **navbar**) berada di container yang sama. Halaman dalam menggunakan `--container`/`--gutter`; beranda memakai pasangan `--container-home`/`--gutter-home` agar komposisi referensi tetap seimbang. Tepi kiri-kanan navbar, hero, kartu, dan blok hijau harus lurus.
- Jarak vertikal antar-section: `var(--section-gap)`. Tidak ada celah kosong yang lebih besar dari itu.
- Grid produk: 4 kolom (≥1024px), 2 kolom (≥640px), 1–2 kolom di bawahnya. Gap 24px (16px di mobile).
- Navbar bersifat sticky; beri `scroll-margin-top` (tinggi navbar + 16px) pada section beranchor.
- Tidak boleh ada scroll horizontal pada lebar 360px ke atas.

---

## 5. Komponen

### 5.1 Navbar (pill melayang)
- Bentuk pill, latar `--brand`, border `--border`, bayangan `--shadow`.
- Kiri: logo "Siswa**Mart**" di pill krem berborder (teks tidak boleh terpotong, `white-space: nowrap`).
- Tengah: Beranda, Kategori, Cara Memesan, Tentang Kami. Teks satu baris. Item aktif = pill `--cream`.
- Kanan: tombol cari berbentuk lingkaran + tombol Masuk/Dashboard (pill gelap `--ink`, teks krem).
- Mobile: hamburger yang membuka panel membulat dengan gaya sama.

### 5.2 Tombol
- Selalu **pill** (`--r-pill`), border `--border`, bayangan `--shadow`, padding ±14px 28px, teks bobot 700.
- Varian:
  - **Primer:** latar `--brand`, teks `--ink`. Maksimal satu per viewport.
  - **Sekunder:** latar `--cream-2`, teks `--ink`.
  - **Gelap:** latar `--ink`, teks `--cream` (Dashboard/Masuk).
  - **Outline/tautan:** teks bergaris bawah (mis. "Lihat semua →").
- Interaksi: hover `translate(-2px,-2px)` + `--shadow-hover`; active `translate(2px,2px)` + `--shadow-active`; fokus keyboard outline 3px `--ink` offset 3px.
- Tombol aksi WhatsApp memakai varian primer dengan ikon WhatsApp di kiri.

### 5.3 Chip & Badge
- Pill, border 2px `--ink`, font label.
- Chip informasi (lokasi, kategori, rating, dilihat): latar pastel/krem, ikon 14–16px **inline** di sebelah teks (tanpa wadah bulat), `inline-flex; align-items:center; gap:4–6px`.
- Badge status di pojok kiri atas gambar: TERSEDIA (`--success-bg`), PRE-ORDER dan FAVORIT (`--warning-bg`).

### 5.4 Wadah Ikon Bulat (inti konsistensi)
Satu elemen, satu SVG di tengah:

```css
.icon-wrap {
  display: grid; place-items: center; aspect-ratio: 1;
  border-radius: 50%; border: 2px solid var(--ink);
  background: var(--tint, var(--cream-2));
  color: var(--ink);
}
```

| Konteks | Wadah | Ikon | Stroke |
|---|---|---|---|
| Tile kategori, langkah Cara Memesan | 56–64px | 28–32px | 2 |
| Poin hero, chip lokasi | 40px | 20–22px | 2 |
| Panah tile, ikon sosial footer, tombol cari | 32–40px | 16–20px | 2 |
| Fallback gambar produk | 112–128px | 56–64px | 1.5 |

Aturan: **tidak boleh** ada lingkaran isian, offset, atau transform di dalam SVG. Ikon harus tepat di tengah wadah di semua breakpoint.

### 5.5 Ikon
- Satu set saja: **Tabler Icons (outline, MIT)** sebagai SVG inline lewat komponen ikon reusable (`<x-icon name="..."/>` atau setara). Jangan memuat library JS penuh.
- Atribut: `viewBox 24x24`, `fill="none"`, `stroke="currentColor"`, `stroke-linecap/linejoin="round"`, `aria-hidden="true"` bila dekoratif.
- Warna ikon: `--ink`. Pengecualian: bintang rating terisi kuning-oranye.

Pemetaan makna (jangan diganti sembarangan):

| Konsep | Ikon |
|---|---|
| Dessert | cake / ice-cream |
| Makanan Berat | burger / soup |
| Minuman | cup / glass-full (bukan cangkir kopi panas) |
| Snack | cookie |
| Lokasi | map-pin |
| WhatsApp | brand-whatsapp |
| Instagram / TikTok | brand-instagram / brand-tiktok |
| Penjual | building-store |
| Rating | star (terisi) |
| Dilihat | eye |
| Cari | search |
| Panah | arrow-right |
| Sekolah | school |
| Harga | coin / cash |
| Langkah: pilih / ulasan / hubungi / atur | search / star / brand-whatsapp / circle-check |

Ikon fallback produk dan pill marquee mengikuti **kategori** produk, bukan nama produk.

### 5.6 Kartu Produk
- Radius `--r-card`, border, bayangan, latar `--white-warm`.
- Area gambar rasio 4:3 (`object-fit: cover`). Jika tidak ada gambar atau gagal dimuat: latar pastel kategori + pola titik halus + wadah ikon besar. **Dilarang menampilkan alt text atau ikon gambar rusak.**
- Badge status di pojok kiri atas gambar.
- Isi: nama (maks 2 baris), penjual (ikon store 14–16px + nama), chip kategori.
- Baris bawah (dipisah garis putus-putus): harga `--brand-ink` tebal di kiri; chip rating dan dilihat di kanan.
- Hover: naik 2px + `--shadow-hover`. Seluruh kartu adalah tautan ke detail produk.

### 5.7 Tile Kategori
- Radius `--r-tile`, latar pastel kategori, border, bayangan.
- Isi: wadah ikon 56–64px + nama (UPPERCASE) + panah dalam wadah 32px, semua sejajar vertikal di tengah.

### 5.8 Blok Hijau (Cara Memesan dan blok sejenis)
- Latar `--green`, radius `--r-block`, border, bayangan, teks putih/krem.
- Kartu langkah: latar `--white-warm`, radius 24px, miring ±1°, wadah ikon berwarna pastel berbeda, angka besar berwarna aksen (oranye, ungu, teal, emas).
- Dekorasi blob di dalam blok harus dipotong rapi (`overflow: hidden` pada blok) dan **tidak menimpa tombol atau teks**.

### 5.9 Marquee
- Deretan pill pastel (ikon 20px inline + teks UPPERCASE), jarak antar-item seragam, loop mulus (dua track identik, `translateX(-50%)`).
- Wajib `prefers-reduced-motion: reduce` → animasi dimatikan.

### 5.10 Form (login, daftar, cari, dashboard)
- Input/select/textarea: radius `--r-input`, border `--border`, latar `--white-warm`, padding 14px 18px, font body.
- Fokus: bayangan `--shadow` + outline 3px `--ink` offset 2px. Label di atas input, bobot 700.
- Error: border merah tua + pesan di bawah input dengan ikon alert; jangan hanya mengandalkan warna.
- Tombol submit memakai varian primer. Kartu form: radius `--r-card`, border, bayangan, di tengah halaman.

### 5.11 Footer
- Latar `--cream-2`, sudut atas membulat besar (±40px) dengan border atas.
- Grid 4 kolom (brand+tombol, Jelajahi, Informasi, Hubungi Kami); mobile 1–2 kolom, semua rata atas.
- Ikon kontak/sosial dalam wadah 32px. Bar hak cipta di bawah dengan garis pemisah.

---

## 6. Dekorasi (secukupnya)

- Blob pastel samar (opacity 0.35–0.5) di belakang hero atau sudut section.
- Elemen kecil seperti titik atau bintang 4 sudut, **maksimal 6–8 per halaman**.
- Semua dekorasi `aria-hidden="true"`, `pointer-events: none`, tidak menutupi teks/tombol, dan tidak menyebabkan scroll horizontal.
- Pemisah antar-section memakai sudut besar membulat atau gelombang lembut, bukan garis lurus kaku.

---

## 7. Pola Halaman (untuk halaman selain beranda)

Setiap halaman memakai kerangka yang sama: **Navbar → konten dalam container → (opsional blok CTA) → Footer.**

1. **Page header:** judul H1 UPPERCASE (font judul), deskripsi singkat satu kalimat, opsional breadcrumb berbentuk chip pill. Tanpa hero raksasa; tinggi header ringkas.
2. **Halaman daftar (Kategori, hasil cari, semua produk):** header + chip filter kategori (pill, berwarna pastel kategori, aktif = berisi `--ink`/teks krem) + grid kartu produk + paginasi pill.
3. **Halaman detail produk:** dua kolom (gambar/fallback besar di kiri; info di kanan: badge, nama, penjual, harga, chip, deskripsi, tombol primer WhatsApp). Mobile: satu kolom, tombol WhatsApp sticky di bawah bila memungkinkan. Ulasan memakai kartu bulat dengan avatar lingkaran.
4. **Halaman konten (Cara Memesan, Tentang Kami):** blok-blok bulat bergantian warna (krem, pastel, hijau), langkah/poin memakai kartu dan wadah ikon yang sama dengan beranda.
5. **Halaman auth (login/daftar):** kartu form di tengah, logo di atas, latar krem dengan blob dekoratif.
6. **Keadaan kosong / error (404, tidak ada produk):** ilustrasi ikon besar di wadah bulat pastel, judul ramah, tombol primer kembali ke beranda/katalog.
7. **Dashboard (penjual/admin):** boleh lebih tenang dan padat, tetapi **tetap** memakai token (warna, border, radius, bayangan, tombol, input, chip). Tabel: header berlatar `--cream-2`, baris dipisah garis tipis `--ink` 1px opacity 0.15, sudut kontainer bulat.

---

## 8. Responsif

Uji di 360, 768, 1024, 1440, dan 1920px.

- Navbar jadi hamburger di bawah ±900px.
- Grid produk/kategori sesuai aturan di bagian 4.
- Judul memakai `clamp()`; tidak ada teks terpotong atau keluar kontainer.
- Target sentuh minimal 44×44px.

---

## 9. Aksesibilitas

- Kontras teks minimal WCAG AA (perhatikan teks putih di atas hijau dan oranye).
- Fokus keyboard selalu terlihat.
- Ikon dekoratif `aria-hidden`; ikon bermakna (tombol cari, sosial) diberi `aria-label`.
- Gambar produk beralt bermakna; lazy-load untuk gambar di bawah fold; beri `aspect-ratio` agar tidak ada layout shift.
- Hormati `prefers-reduced-motion` untuk marquee dan efek hover bergerak.

---

## 10. Larangan (Don't)

- Hitam murni, ikon solid hitam, atau ikon dari set berbeda yang dicampur.
- Sudut tajam atau radius kecil (<16px) pada kartu/tombol/input.
- Bayangan berblur atau bayangan lembut ala material design.
- Border lebih tipis dari 2px atau lebih tebal dari 3px pada komponen utama.
- Lingkaran ganda, offset, atau transform di dalam SVG ikon.
- Alt text atau ikon rusak yang tampil di tempat gambar.
- Lebih dari satu tombol primer dalam satu viewport.
- Warna kategori yang tidak konsisten, atau font judul yang berbeda dari font bulat yang ditetapkan.
- Nilai warna/ukuran yang di-hardcode padahal sudah ada token.

---

## 11. Checklist Sebelum Merge (Halaman/Komponen Baru)

- [ ] Memakai token dari bagian 2 (tidak ada warna/radius/bayangan hardcode).
- [ ] Navbar dan footer sama dengan beranda; lebar konten sejajar container.
- [ ] Semua tombol pill dengan efek hover/active yang benar.
- [ ] Semua ikon dari satu set, di tengah wadahnya, dan maknanya sesuai.
- [ ] Kartu/blok/input berradius sesuai token, border dan bayangan konsisten.
- [ ] Gambar produk punya fallback; tidak ada alt text bocor.
- [ ] Rapi di 360–1920px, tanpa scroll horizontal.
- [ ] Kontras, fokus keyboard, dan `prefers-reduced-motion` terpenuhi.
- [ ] Tidak ada elemen yang melanggar daftar Larangan.

---

## 12. Cara Menambah Komponen Baru

1. Cek apakah sudah ada komponen serupa di bagian 5; pakai ulang atau perluas, jangan duplikasi.
2. Bangun dari token (bagian 2) dan wadah ikon (5.4).
3. Dokumentasikan varian baru di dokumen ini pada PR yang sama.


<!--  -->