# Panduan Dokumentasi

Folder ini diisi **screenshot** dan **screen record** hasil menjalankan aplikasi. Gunakan **nama file persis** seperti
pada tabel agar gambar otomatis tampil di `README.md`.

## Persiapan sebelum mengambil gambar

1. Impor ulang `SQL/tp_mvc25.sql` agar data kembali ke kondisi awal (4 prodi, 8 dosen, 12 mata kuliah).
2. Buka aplikasi di `http://localhost/tp_mvc25/` pada browser dengan zoom 100%.
3. Pastikan address bar ikut terlihat pada screenshot (supaya terlihat halaman yang dibuka).
4. Format gambar: PNG. Untuk screen record: MP4 atau GIF (maksimal sekitar 50 MB agar muat di GitHub).

## Daftar screenshot

| No | Nama file | Isi yang ditampilkan | Cara mengambil |
|---|---|---|---|
| 1 | `01-db-daftar-tabel.png` | Tiga tabel (`departments`, `lecturers`, `courses`) di database `tp_mvc25` | phpMyAdmin, klik database `tp_mvc25` |
| 2 | `02-db-relasi-designer.png` | Garis relasi Foreign Key antar tiga tabel | phpMyAdmin, database `tp_mvc25`, tab **Designer** |
| 3 | `03-dosen-read.png` | Halaman Dosen: tabel berisi 8 dosen, kolom Program Studi hasil JOIN | Buka `index.php` |
| 4 | `04-dosen-create.png` | Form tambah dosen terisi, dropdown Program Studi terbuka; lalu tampilan setelah sukses (pesan hijau) | Isi form, ambil gambar sebelum dan sesudah klik **Tambah** (boleh digabung) |
| 5 | `05-dosen-update.png` | Form dalam mode ubah (judul *Ubah Data Dosen*, data terisi) dan pesan sukses setelah disimpan | Klik **Edit** pada salah satu dosen, ubah data, simpan |
| 6 | `06-dosen-delete.png` | Dialog konfirmasi hapus dan pesan sukses setelah dosen baru (yang belum mengampu mata kuliah) dihapus | Tambah dosen baru dulu, lalu hapus dosen tersebut |
| 7 | `07-prodi-read.png` | Halaman Program Studi dengan kolom Jumlah Dosen | Buka `department.php` |
| 8 | `08-prodi-create-update.png` | Tambah program studi baru dan ubah datanya (boleh dua gambar digabung) | Tambah prodi (mis. kode `MN`), lalu **Edit** |
| 9 | `09-prodi-delete-ditolak.png` | Pesan merah: program studi tidak dapat dihapus karena masih memiliki dosen | Klik **Hapus** pada prodi `IF` dan konfirmasi |
| 10 | `10-matkul-read.png` | Halaman Mata Kuliah: tabel 12 mata kuliah dengan nama dosen dan prodi (JOIN) | Buka `course.php` |
| 11 | `11-matkul-create.png` | Form tambah mata kuliah dengan dropdown Dosen Pengampu terbuka, dan hasil sukses | Isi form lalu **Tambah** |
| 12 | `12-matkul-update.png` | Form mode ubah (*Ubah Mata Kuliah*) dan pesan sukses | Klik **Edit**, ubah dosen pengampu atau SKS, simpan |
| 13 | `13-matkul-delete.png` | Dialog konfirmasi dan pesan sukses hapus mata kuliah | Hapus mata kuliah yang baru ditambahkan |
| 14 | `14-validasi-duplikat.png` | Pesan merah validasi, misalnya kode `IF305` dipakai dua kali, atau NIDN kurang dari 10 digit | Isi kode mata kuliah yang sudah ada lalu **Tambah** |
| 15 | `15-responsif-mobile.png` | Tampilan layar kecil dengan navbar *hamburger* dan tabel yang bisa digeser | Browser, F12, mode perangkat (Toggle device toolbar), pilih ukuran ponsel |

Catatan untuk nomor 9: kasus serupa dapat diperlihatkan pada dosen. Hapus dosen yang masih mengampu mata kuliah
(misalnya *Budi Santoso*) untuk menampilkan pesan penolakan Foreign Key, dan masukkan sebagai tambahan
screenshot jika diperlukan (misalnya `09b-dosen-delete-ditolak.png`).

## Screen record

| Nama file | Isi |
|---|---|
| `99-screen-record-crud.mp4` | Satu rekaman berurutan (sekitar 2-4 menit): (1) buka tiga halaman lewat navbar, (2) tambah program studi, (3) tambah dosen dengan memilih program studi tadi, (4) tambah mata kuliah dengan memilih dosen tadi, (5) ubah masing-masing satu data, (6) coba hapus data induk yang masih dipakai (ditolak), (7) hapus data dari yang paling bawah (mata kuliah, dosen, program studi) sampai berhasil. |

Alat yang bisa dipakai: Xbox Game Bar (`Win + G`), OBS Studio, atau ScreenToGif.

## Checklist akhir

- [ ] 15 screenshot dengan nama file sesuai tabel
- [ ] 1 screen record `99-screen-record-crud.mp4`
- [ ] Isi nama, NIM, dan kelas di bagian atas `README.md`
- [ ] Struktur akhir: `Project/`, `SQL/`, `Dokumentasi/`, `README.md`
