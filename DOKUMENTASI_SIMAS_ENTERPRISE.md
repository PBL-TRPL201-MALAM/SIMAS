# Dokumentasi Sistem SIMAS

## Sistem Informasi Manajemen Administrasi Surat

### Dokumen Spesifikasi Sistem Enterprise

| Atribut | Nilai |
| --- | --- |
| Nama Sistem | SIMAS |
| Kepanjangan | Sistem Informasi Manajemen Administrasi Surat |
| Jenis Sistem | Web-based Document Workflow Management System |
| Domain Bisnis | Administrasi surat, surat keputusan, dokumen legal internal, dan dokumen administratif berbasis workflow |
| Model Dokumen | PDF-Based Document Processing |
| Teknologi Backend | Laravel |
| Teknologi Frontend | Blade dan/atau Vue |
| Database | MySQL |
| PDF Engine | DomPDF dan/atau TCPDF |
| Validasi Dokumen | QR Code berbasis halaman validasi digital |
| Target Pengguna | Pemohon, Admin/TU, Verifikator, Penandatangan, dan Super Admin |
| Status Dokumen | Draft dokumentasi sistem |

---

## Daftar Isi

1. Pendahuluan
2. Ringkasan Eksekutif Sistem
3. Tujuan dan Sasaran Sistem
4. Ruang Lingkup Sistem
5. Prinsip Desain Sistem
6. Konsep Utama Sistem
7. Aktor dan Tanggung Jawab
8. Role-Based Access Control
9. Model Workflow Dokumen
10. Status Dokumen
11. Alur Pengajuan Dokumen
12. Alur Proses Admin/TU
13. Alur Verifikasi Bertingkat
14. Alur Revisi Dokumen
15. Alur Penolakan Dokumen
16. Alur Finalisasi dan Publish Dokumen
17. Alur Validasi Dokumen Menggunakan QR Code
18. Alur Audit Log
19. Struktur Modul Sistem
20. Spesifikasi Fitur Utama
21. Spesifikasi Database Core
22. Relasi Data
23. Aturan Bisnis
24. Validasi Input
25. Keamanan Sistem
26. Manajemen File PDF
27. Dynamic PDF Element Positioning
28. Nomor Surat dan Metadata Dokumen
29. Notifikasi Sistem
30. Logging dan Audit Trail
31. Error Handling
32. Skenario Operasional
33. Non-Functional Requirements
34. Rekomendasi Struktur Aplikasi Laravel
35. Kesimpulan

---

## 1. Pendahuluan

SIMAS atau Sistem Informasi Manajemen Administrasi Surat adalah sistem informasi berbasis web yang dirancang untuk mengelola proses administrasi surat dan surat keputusan secara digital, terstruktur, terukur, dan dapat diaudit. Sistem ini menggantikan proses manual yang sebelumnya bergantung pada pengolahan dokumen secara terpisah, komunikasi informal, serta verifikasi non-terpusat menjadi workflow digital berbasis PDF.

Dokumen ini mendefinisikan rancangan sistem SIMAS pada level enterprise system, meliputi konsep bisnis, alur operasional, peran pengguna, model status, kebutuhan fungsional, kebutuhan non-fungsional, struktur data inti, keamanan, audit log, pengelolaan PDF, serta mekanisme validasi dokumen final menggunakan QR Code.

Dokumentasi ini disusun dengan asumsi bahwa dokumen yang diproses oleh SIMAS menggunakan pendekatan PDF-based document workflow. Dengan pendekatan ini, pemohon mengunggah file PDF draft secara langsung ke sistem. Admin/TU tidak lagi bertugas mengonversi file DOCX menjadi PDF. Admin/TU hanya melakukan review administratif, melengkapi metadata surat, mengatur posisi elemen pada PDF, menentukan jalur verifikasi, dan mengirimkan dokumen ke proses approval bertingkat.

---

## 2. Ringkasan Eksekutif Sistem

SIMAS adalah sistem manajemen dokumen administrasi berbasis workflow yang memproses dokumen PDF dari tahap pengajuan sampai dokumen final dipublikasikan. Sistem mendukung multi-level approval, dynamic PDF element positioning, role-based access control, audit trail, serta validasi dokumen final melalui QR Code.

Dalam proses bisnis SIMAS, pemohon mengunggah dokumen PDF draft dan mengisi metadata awal. Admin/TU kemudian melakukan pemeriksaan awal, melengkapi metadata resmi surat, menentukan penandatangan, mengatur posisi elemen seperti nomor surat, tanggal, tanda tangan elektronik atau QR Code, lalu menentukan jalur verifikasi. Setelah itu dokumen dikirim ke verifikator secara bertingkat. Jika semua level menyetujui, sistem menghasilkan PDF final dengan elemen resmi yang ditempatkan sesuai konfigurasi, kemudian mempublikasikan dokumen dengan status akhir PUBLISHED.

Sistem ini dirancang untuk memastikan bahwa setiap perubahan status, keputusan verifikasi, revisi, penolakan, pengaturan metadata, dan proses publish tercatat secara lengkap pada audit log. Dengan demikian, SIMAS tidak hanya berfungsi sebagai aplikasi unggah dokumen, tetapi sebagai platform administrasi surat digital yang memenuhi kebutuhan kontrol proses, transparansi, validitas dokumen, dan akuntabilitas organisasi.

---

## 3. Tujuan dan Sasaran Sistem

### 3.1 Tujuan Utama

Tujuan utama SIMAS adalah menyediakan sistem digital untuk mengelola dokumen administrasi surat dan surat keputusan melalui workflow yang jelas, terdokumentasi, dan dapat diaudit dari awal pengajuan sampai publikasi dokumen final.

### 3.2 Sasaran Sistem

Sistem SIMAS memiliki sasaran sebagai berikut:

1. Menghilangkan proses manual konversi dokumen oleh Admin/TU.
2. Memastikan pemohon mengunggah dokumen dalam format PDF sejak awal.
3. Menyediakan workflow verifikasi bertingkat sesuai struktur organisasi.
4. Menyediakan pengaturan posisi elemen PDF secara dinamis.
5. Menambahkan nomor surat, tanggal surat, tanda tangan elektronik, dan QR Code pada dokumen final.
6. Menyediakan mekanisme validasi dokumen berbasis QR Code.
7. Mengimplementasikan role-based access control untuk membatasi akses berdasarkan peran.
8. Menyediakan audit log lengkap terhadap seluruh aktivitas dokumen.
9. Menjamin konsistensi status dokumen sepanjang siklus hidup dokumen.
10. Menyediakan fondasi teknis yang dapat dikembangkan untuk integrasi lanjutan, seperti notifikasi, tanda tangan elektronik tersertifikasi, dan arsip digital.

---

## 4. Ruang Lingkup Sistem

### 4.1 Ruang Lingkup Fungsional

Ruang lingkup fungsional SIMAS meliputi:

1. Manajemen pengguna dan peran.
2. Login dan autentikasi pengguna.
3. Pengajuan dokumen PDF oleh pemohon.
4. Pengisian metadata awal oleh pemohon.
5. Review dokumen PDF draft oleh Admin/TU.
6. Pengisian metadata resmi surat oleh Admin/TU.
7. Pengaturan posisi elemen pada PDF.
8. Penentuan jalur verifikasi.
9. Verifikasi bertingkat Level 1 sampai Level 3.
10. Persetujuan dokumen oleh verifikator.
11. Penolakan dokumen oleh verifikator.
12. Permintaan revisi dokumen kepada pemohon.
13. Finalisasi dokumen setelah seluruh level menyetujui.
14. Generate PDF final dengan elemen resmi.
15. Publish dokumen final.
16. Validasi dokumen melalui QR Code.
17. Pencatatan audit log.
18. Pelacakan riwayat status dokumen.

### 4.2 Ruang Lingkup Non-Fungsional

Ruang lingkup non-fungsional SIMAS meliputi:

1. Keamanan akses berbasis role.
2. Integritas file PDF.
3. Konsistensi workflow.
4. Traceability atau keterlacakan proses.
5. Ketersediaan audit trail.
6. Skalabilitas struktur data.
7. Kesiapan integrasi dengan layanan eksternal.
8. Kinerja pemrosesan PDF.
9. Validasi file dan metadata.
10. Kemudahan pemeliharaan aplikasi Laravel.

### 4.3 Di Luar Ruang Lingkup

Komponen berikut tidak termasuk dalam ruang lingkup inti kecuali dikembangkan sebagai modul tambahan:

1. Pembuatan dokumen draft dari template DOCX.
2. Konversi DOCX ke PDF oleh Admin/TU.
3. Tanda tangan elektronik tersertifikasi dari penyelenggara pihak ketiga.
4. Integrasi langsung dengan sistem arsip nasional.
5. Optical Character Recognition untuk membaca isi PDF secara otomatis.
6. Redaksi atau masking konten PDF sensitif secara otomatis.
7. Digital certificate management internal.

---

## 5. Prinsip Desain Sistem

### 5.1 PDF-Based Document Workflow

SIMAS menggunakan PDF sebagai format utama dokumen sejak tahap pengajuan. Setiap pemohon wajib mengunggah dokumen dalam format PDF. Sistem tidak mengandalkan proses konversi dokumen dari format lain pada tahap admin.

Prinsip ini dipilih untuk:

1. Menjaga konsistensi tampilan dokumen.
2. Mengurangi risiko perubahan format setelah dokumen diajukan.
3. Mempercepat proses review administratif.
4. Mengurangi beban kerja Admin/TU.
5. Memastikan proses finalisasi hanya menambahkan elemen resmi ke dokumen yang sudah berbentuk PDF.

### 5.2 Workflow Terpusat

Seluruh proses dokumen berlangsung di dalam sistem. Perpindahan dokumen dari pemohon ke Admin/TU, dari Admin/TU ke verifikator, dan dari verifikator ke proses finalisasi dilakukan berdasarkan status sistem, bukan berdasarkan komunikasi manual.

### 5.3 Approval Bertingkat

SIMAS mendukung verifikasi bertingkat dari Level 1 sampai Level 3. Setiap level memiliki kewenangan untuk menyetujui, menolak, atau meminta revisi dokumen. Dokumen hanya dapat masuk ke level berikutnya jika level sebelumnya telah memberikan persetujuan.

### 5.4 Separation of Duties

Setiap aktor memiliki tanggung jawab berbeda. Pemohon mengajukan dokumen. Admin/TU melakukan review administratif dan konfigurasi workflow. Verifikator melakukan validasi substansi atau kewenangan. Penandatangan bertanggung jawab terhadap pengesahan akhir. Super Admin mengelola konfigurasi sistem dan pengguna.

### 5.5 Auditability

Setiap aktivitas penting harus tercatat. Audit log mencatat siapa yang melakukan aksi, kapan aksi dilakukan, dokumen apa yang terdampak, status sebelum dan sesudah aksi, serta catatan tambahan jika tersedia.

### 5.6 Dynamic Element Placement

Nomor surat, tanggal surat, QR Code, dan tanda tangan elektronik tidak diposisikan secara statis. Admin/TU dapat menentukan posisi elemen tersebut pada halaman PDF melalui konfigurasi posisi. Sistem menggunakan data posisi tersebut saat menghasilkan PDF final.

---

## 6. Konsep Utama Sistem

### 6.1 PDF-Based Document Workflow

PDF-Based Document Workflow adalah konsep pemrosesan dokumen dengan menjadikan file PDF sebagai sumber utama dokumen. Draft yang dikirim oleh pemohon sudah harus berbentuk PDF. Sistem tidak melakukan perubahan isi substansi dokumen, tetapi menambahkan elemen administratif dan validasi pada tahap finalisasi.

Alur konsep ini adalah sebagai berikut:

1. Pemohon menyiapkan dokumen draft di luar sistem.
2. Pemohon memastikan dokumen sudah final secara konten awal.
3. Pemohon mengekspor atau menyimpan dokumen sebagai PDF.
4. Pemohon mengunggah PDF ke SIMAS.
5. Admin/TU melakukan review terhadap PDF.
6. Admin/TU menambahkan metadata dan konfigurasi posisi elemen.
7. Verifikator melakukan approval bertingkat.
8. Sistem membuat PDF final berdasarkan PDF draft dan data metadata.
9. Sistem menambahkan elemen resmi ke PDF final.
10. Sistem mempublikasikan dokumen final.

### 6.2 Multi-Level Approval System

Multi-Level Approval System adalah mekanisme persetujuan dokumen secara bertingkat. Dokumen dapat melewati satu atau lebih level verifikasi sesuai konfigurasi yang ditentukan oleh Admin/TU.

Contoh jalur verifikasi:

1. Level 1 saja untuk dokumen sederhana.
2. Level 1 dan Level 2 untuk dokumen yang memerlukan validasi unit.
3. Level 1, Level 2, dan Level 3 untuk dokumen strategis atau surat keputusan.

Setiap level wajib menyelesaikan keputusan sebelum dokumen berpindah ke level berikutnya.

### 6.3 Dynamic PDF Element Positioning

Dynamic PDF Element Positioning adalah kemampuan sistem untuk menyimpan koordinat elemen yang akan ditempatkan pada PDF final. Elemen dapat mencakup:

1. Nomor surat.
2. Tanggal surat.
3. Nama penandatangan.
4. Jabatan penandatangan.
5. Tanda tangan elektronik.
6. QR Code validasi.
7. Stempel atau label dokumen jika diperlukan.

Setiap posisi elemen disimpan dalam satuan koordinat yang konsisten, misalnya berdasarkan titik X dan Y pada halaman PDF, nomor halaman, lebar elemen, tinggi elemen, dan jenis elemen.

### 6.4 Role-Based Access Control

Role-Based Access Control adalah mekanisme pembatasan akses berdasarkan peran pengguna. Setiap pengguna hanya dapat mengakses fitur yang sesuai dengan tanggung jawabnya.

Contoh:

1. Pemohon hanya dapat membuat pengajuan dan melihat dokumen miliknya.
2. Admin/TU dapat melihat pengajuan, melengkapi metadata, dan mengirim dokumen ke verifikator.
3. Verifikator hanya dapat memproses dokumen yang ditugaskan kepadanya.
4. Penandatangan dapat melihat dokumen yang memerlukan pengesahan atau dipublikasikan atas kewenangannya.
5. Super Admin dapat mengelola pengguna, role, konfigurasi sistem, dan data master.

### 6.5 Digital Document Validation

Digital Document Validation adalah mekanisme validasi keaslian dokumen final. SIMAS menghasilkan QR Code pada dokumen final. QR Code mengarah ke halaman validasi yang dapat menampilkan informasi dokumen, status publikasi, metadata dasar, dan hash atau token validasi.

Dengan mekanisme ini, penerima dokumen dapat memeriksa apakah dokumen yang diterima benar berasal dari sistem dan masih berstatus valid.

---

## 7. Aktor dan Tanggung Jawab

### 7.1 Pemohon

Pemohon adalah pengguna yang mengajukan dokumen ke SIMAS. Pemohon bertanggung jawab memastikan dokumen draft yang diunggah sudah berbentuk PDF dan layak untuk diproses.

Tanggung jawab Pemohon:

1. Login ke sistem.
2. Membuat pengajuan dokumen baru.
3. Mengunggah file PDF draft.
4. Mengisi metadata awal, seperti perihal, ringkasan, dan lampiran.
5. Mengirim pengajuan ke Admin/TU.
6. Memantau status dokumen.
7. Melakukan revisi jika dokumen dikembalikan.
8. Mengunggah ulang PDF revisi jika diminta.
9. Melihat dokumen final setelah dipublikasikan jika memiliki hak akses.

### 7.2 Admin/TU

Admin/TU adalah pengguna yang bertanggung jawab terhadap review administratif, kelengkapan metadata, pengaturan posisi elemen PDF, dan penentuan jalur verifikasi.

Tanggung jawab Admin/TU:

1. Menerima pengajuan dokumen dari pemohon.
2. Membuka dan memeriksa PDF draft.
3. Memastikan file PDF dapat dibaca dan sesuai ketentuan administratif.
4. Melengkapi metadata resmi surat.
5. Mengisi nomor surat jika nomor sudah dapat diberikan pada tahap proses.
6. Mengisi tanggal surat.
7. Menentukan sifat surat.
8. Menentukan penandatangan.
9. Mengatur posisi nomor surat pada PDF.
10. Mengatur posisi tanggal surat pada PDF.
11. Mengatur posisi QR Code atau tanda tangan elektronik pada PDF.
12. Menentukan jalur verifikasi.
13. Mengirim dokumen ke verifikator pertama.
14. Mengembalikan dokumen ke pemohon jika dokumen tidak layak diproses.
15. Melakukan final check sebelum publish jika kebijakan organisasi mengharuskan.

### 7.3 Verifikator Level 1

Verifikator Level 1 adalah pihak pertama yang melakukan pemeriksaan dokumen setelah Admin/TU mengirim dokumen ke workflow verifikasi.

Tanggung jawab Verifikator Level 1:

1. Menerima dokumen dengan status MENUNGGU_VERIFIKASI.
2. Membuka PDF draft dan metadata surat.
3. Memeriksa kelengkapan awal dan kesesuaian substansi.
4. Memberikan persetujuan jika dokumen valid.
5. Meminta revisi jika ada perbaikan yang harus dilakukan pemohon.
6. Menolak dokumen jika tidak dapat dilanjutkan.
7. Memberikan catatan verifikasi secara jelas.

### 7.4 Verifikator Level 2

Verifikator Level 2 adalah pihak kedua yang melakukan pemeriksaan setelah Level 1 menyetujui dokumen.

Tanggung jawab Verifikator Level 2:

1. Menerima dokumen setelah status DISETUJUI_LEVEL_1.
2. Meninjau hasil pemeriksaan Level 1.
3. Memeriksa substansi dokumen pada tingkat kewenangan yang lebih tinggi.
4. Memberikan approve, reject, atau request revision.
5. Mencatat alasan keputusan.
6. Meneruskan dokumen ke Level 3 jika disetujui dan jalur verifikasi membutuhkan Level 3.

### 7.5 Verifikator Level 3

Verifikator Level 3 adalah pihak terakhir dalam proses approval bertingkat sebelum dokumen masuk ke tahap siap publish.

Tanggung jawab Verifikator Level 3:

1. Menerima dokumen setelah status DISETUJUI_LEVEL_2.
2. Melakukan pemeriksaan akhir pada aspek substansi, kewenangan, dan kelayakan publish.
3. Memberikan approve jika dokumen siap difinalisasi.
4. Meminta revisi jika masih ada perbaikan.
5. Menolak dokumen jika dokumen tidak dapat diterbitkan.
6. Memberikan catatan final verifikasi.

### 7.6 Penandatangan

Penandatangan adalah aktor yang namanya digunakan sebagai pengesah dokumen. Dalam implementasi tertentu, penandatangan dapat terlibat langsung sebagai pengguna yang menyetujui dokumen, atau hanya sebagai metadata resmi yang ditentukan oleh Admin/TU.

Tanggung jawab Penandatangan:

1. Menjadi pihak yang bertanggung jawab terhadap pengesahan dokumen.
2. Memastikan dokumen yang diterbitkan sesuai kewenangan.
3. Memberikan persetujuan akhir jika sistem dikonfigurasi untuk membutuhkan approval penandatangan.
4. Menjadi metadata resmi pada PDF final.

### 7.7 Super Admin

Super Admin adalah pengguna dengan kewenangan tertinggi untuk mengelola sistem.

Tanggung jawab Super Admin:

1. Mengelola user.
2. Mengelola role dan permission.
3. Mengelola data master.
4. Mengelola konfigurasi workflow.
5. Melihat seluruh dokumen.
6. Mengakses audit log.
7. Mengatur parameter sistem.
8. Melakukan tindakan korektif administratif jika diperlukan.
9. Memastikan sistem berjalan sesuai kebijakan organisasi.

---

## 8. Role-Based Access Control

### 8.1 Prinsip Akses

Setiap akses ke fitur sistem harus divalidasi berdasarkan role pengguna, status dokumen, kepemilikan dokumen, dan penugasan workflow.

Sistem harus memastikan bahwa:

1. Pengguna tidak dapat melihat dokumen yang bukan haknya.
2. Pengguna tidak dapat mengubah dokumen pada status yang tidak sesuai.
3. Pengguna tidak dapat melakukan approve jika bukan verifikator yang sedang bertugas.
4. Pengguna tidak dapat mempublish dokumen tanpa status yang memenuhi syarat.
5. Super Admin memiliki akses administratif tetapi seluruh aksinya tetap dicatat.

### 8.2 Matriks Hak Akses

| Fitur | Pemohon | Admin/TU | Verifikator | Penandatangan | Super Admin |
| --- | --- | --- | --- | --- | --- |
| Login | Ya | Ya | Ya | Ya | Ya |
| Membuat pengajuan | Ya | Opsional | Tidak | Tidak | Opsional |
| Upload PDF draft | Ya | Opsional | Tidak | Tidak | Opsional |
| Melihat dokumen sendiri | Ya | Ya | Sesuai tugas | Sesuai kewenangan | Ya |
| Review PDF draft | Tidak | Ya | Ya | Ya | Ya |
| Mengisi metadata awal | Ya | Ya | Tidak | Tidak | Ya |
| Mengisi metadata resmi | Tidak | Ya | Tidak | Opsional | Ya |
| Mengatur posisi elemen PDF | Tidak | Ya | Tidak | Tidak | Ya |
| Menentukan jalur verifikasi | Tidak | Ya | Tidak | Tidak | Ya |
| Approve dokumen | Tidak | Opsional | Ya | Opsional | Opsional |
| Reject dokumen | Tidak | Opsional | Ya | Opsional | Opsional |
| Meminta revisi | Tidak | Ya | Ya | Opsional | Ya |
| Generate PDF final | Tidak | Ya | Tidak | Opsional | Ya |
| Publish dokumen | Tidak | Ya | Tidak | Opsional | Ya |
| Validasi dokumen via QR | Ya | Ya | Ya | Ya | Ya |
| Melihat audit log | Terbatas | Terbatas | Terbatas | Terbatas | Ya |
| Mengelola user | Tidak | Tidak | Tidak | Tidak | Ya |

### 8.3 Permission Minimal

Permission minimal yang disarankan:

1. document.create
2. document.view.own
3. document.view.assigned
4. document.view.all
5. document.update.draft
6. document.review
7. document.metadata.update
8. document.position.update
9. document.workflow.assign
10. document.verify.approve
11. document.verify.reject
12. document.verify.revision
13. document.finalize
14. document.publish
15. document.validate
16. audit.view
17. user.manage
18. role.manage
19. system.configure

---

## 9. Model Workflow Dokumen

### 9.1 Siklus Hidup Dokumen

Siklus hidup dokumen di SIMAS dimulai dari pengajuan oleh pemohon dan berakhir ketika dokumen dipublikasikan atau dihentikan karena ditolak.

Urutan utama:

1. Pemohon membuat pengajuan.
2. Pemohon mengunggah PDF draft.
3. Sistem menyimpan dokumen dengan status DIAJUKAN.
4. Admin/TU melakukan review administratif.
5. Admin/TU melengkapi metadata resmi.
6. Admin/TU mengatur posisi elemen PDF.
7. Admin/TU menentukan jalur verifikasi.
8. Sistem mengubah status menjadi MENUNGGU_VERIFIKASI.
9. Verifikator Level 1 melakukan review.
10. Jika approve, sistem mengubah status menjadi DISETUJUI_LEVEL_1.
11. Jika masih ada level berikutnya, sistem menugaskan dokumen ke Verifikator Level 2.
12. Verifikator Level 2 melakukan review.
13. Jika approve, sistem mengubah status menjadi DISETUJUI_LEVEL_2.
14. Jika masih ada level berikutnya, sistem menugaskan dokumen ke Verifikator Level 3.
15. Verifikator Level 3 melakukan review.
16. Jika approve, sistem mengubah status menjadi DISETUJUI_LEVEL_3.
17. Sistem atau Admin/TU menetapkan dokumen menjadi SIAP_PUBLISH.
18. Sistem menghasilkan PDF final.
19. Sistem menambahkan nomor, tanggal, QR Code, dan elemen pengesahan.
20. Sistem mengubah status akhir menjadi PUBLISHED.

### 9.2 Alur Alternatif

Selain alur utama, terdapat alur alternatif:

1. Dokumen dikembalikan ke pemohon dengan status PERLU_REVISI.
2. Dokumen ditolak dengan status DITOLAK.
3. Dokumen dibatalkan oleh pemohon sebelum diproses, jika kebijakan sistem mengizinkan.
4. Dokumen dikoreksi metadata oleh Admin/TU sebelum masuk ke verifikasi.
5. Dokumen gagal dipublish karena error pada proses generate PDF.

---

## 10. Status Dokumen

### 10.1 Daftar Status

| Status | Deskripsi |
| --- | --- |
| DIAJUKAN | Dokumen telah diajukan oleh pemohon dan menunggu proses Admin/TU. |
| DIPROSES | Dokumen sedang direview oleh Admin/TU. |
| MENUNGGU_VERIFIKASI | Dokumen telah dikirim ke workflow verifikasi dan menunggu keputusan verifikator. |
| DISETUJUI_LEVEL_1 | Dokumen telah disetujui oleh Verifikator Level 1. |
| DISETUJUI_LEVEL_2 | Dokumen telah disetujui oleh Verifikator Level 2. |
| DISETUJUI_LEVEL_3 | Dokumen telah disetujui oleh Verifikator Level 3. |
| PERLU_REVISI | Dokumen dikembalikan kepada pemohon untuk diperbaiki. |
| DITOLAK | Dokumen ditolak dan tidak dapat dilanjutkan ke tahap publish. |
| SIAP_PUBLISH | Dokumen telah memenuhi seluruh approval dan siap dibuat menjadi PDF final. |
| PUBLISHED | Dokumen final telah diterbitkan dan dapat divalidasi menggunakan QR Code. |
| GAGAL_PUBLISH | Sistem gagal membuat atau mempublikasikan PDF final. |
| DIBATALKAN | Pengajuan dibatalkan sesuai kewenangan dan kebijakan sistem. |

### 10.2 Transisi Status Standar

Transisi status standar:

1. DIAJUKAN ke DIPROSES.
2. DIPROSES ke MENUNGGU_VERIFIKASI.
3. MENUNGGU_VERIFIKASI ke DISETUJUI_LEVEL_1.
4. DISETUJUI_LEVEL_1 ke DISETUJUI_LEVEL_2.
5. DISETUJUI_LEVEL_2 ke DISETUJUI_LEVEL_3.
6. DISETUJUI_LEVEL_3 ke SIAP_PUBLISH.
7. SIAP_PUBLISH ke PUBLISHED.

### 10.3 Transisi Status Revisi

Transisi status revisi:

1. MENUNGGU_VERIFIKASI ke PERLU_REVISI.
2. DISETUJUI_LEVEL_1 ke PERLU_REVISI jika Level 2 meminta revisi.
3. DISETUJUI_LEVEL_2 ke PERLU_REVISI jika Level 3 meminta revisi.
4. PERLU_REVISI ke DIAJUKAN setelah pemohon mengunggah revisi.
5. PERLU_REVISI ke DIBATALKAN jika pemohon membatalkan pengajuan dan kebijakan mengizinkan.

### 10.4 Transisi Status Penolakan

Transisi status penolakan:

1. MENUNGGU_VERIFIKASI ke DITOLAK.
2. DISETUJUI_LEVEL_1 ke DITOLAK jika Level 2 menolak.
3. DISETUJUI_LEVEL_2 ke DITOLAK jika Level 3 menolak.
4. DIPROSES ke DITOLAK jika Admin/TU memiliki kewenangan menolak dokumen pada tahap awal.

### 10.5 Status Final

Status final adalah status yang menandakan dokumen tidak lagi bergerak dalam workflow aktif.

Status final meliputi:

1. PUBLISHED.
2. DITOLAK.
3. DIBATALKAN.

Dokumen dengan status final tidak boleh diubah kecuali melalui prosedur administratif khusus yang dilakukan oleh Super Admin dan tercatat pada audit log.

---

## 11. Alur Pengajuan Dokumen

### 11.1 Tujuan Alur

Alur pengajuan dokumen digunakan oleh pemohon untuk mengirimkan draft surat atau surat keputusan kepada Admin/TU agar diproses dalam workflow resmi.

### 11.2 Prasyarat

Sebelum mengajukan dokumen, kondisi berikut harus terpenuhi:

1. Pemohon sudah memiliki akun aktif.
2. Pemohon berhasil login ke sistem.
3. Pemohon memiliki role atau permission untuk membuat pengajuan.
4. Dokumen draft sudah tersedia dalam format PDF.
5. Ukuran file PDF tidak melebihi batas maksimal yang ditentukan sistem.
6. File PDF tidak rusak dan dapat dibuka.

### 11.3 Data yang Diisi Pemohon

Pemohon wajib mengisi data awal berikut:

1. Perihal dokumen.
2. Ringkasan dokumen.
3. Jenis dokumen jika tersedia.
4. Unit atau bagian pemohon jika relevan.
5. Lampiran atau keterangan lampiran.
6. Catatan tambahan untuk Admin/TU.
7. File PDF draft.

### 11.4 Langkah-Langkah Pengajuan

Alur step-by-step:

1. Pemohon membuka halaman login SIMAS.
2. Pemohon memasukkan kredensial yang valid.
3. Sistem memvalidasi kredensial pengguna.
4. Jika kredensial valid, sistem menampilkan dashboard pemohon.
5. Pemohon memilih menu pengajuan dokumen.
6. Sistem menampilkan form pengajuan dokumen.
7. Pemohon mengisi perihal dokumen.
8. Pemohon mengisi ringkasan dokumen.
9. Pemohon mengisi informasi lampiran jika ada.
10. Pemohon memilih file PDF dari perangkat lokal.
11. Sistem memvalidasi ekstensi file.
12. Sistem memvalidasi MIME type file.
13. Sistem memvalidasi ukuran file.
14. Sistem melakukan pemeriksaan awal apakah file dapat dibaca sebagai PDF.
15. Jika file tidak valid, sistem menampilkan pesan kesalahan.
16. Jika file valid, sistem menampilkan nama file dan informasi dasar.
17. Pemohon meninjau kembali data yang telah diisi.
18. Pemohon menekan tombol submit pengajuan.
19. Sistem membuat record dokumen baru pada tabel documents.
20. Sistem menyimpan metadata awal pada tabel document_metadata.
21. Sistem menyimpan file PDF draft ke storage yang ditentukan.
22. Sistem menetapkan status dokumen menjadi DIAJUKAN.
23. Sistem mencatat aktivitas pengajuan pada document_logs.
24. Sistem menampilkan notifikasi bahwa dokumen berhasil diajukan.
25. Dokumen masuk ke daftar kerja Admin/TU.

### 11.5 Output Alur

Output dari alur pengajuan:

1. Record dokumen baru tersimpan.
2. File PDF draft tersimpan.
3. Metadata awal tersimpan.
4. Status dokumen menjadi DIAJUKAN.
5. Audit log pengajuan tercatat.
6. Admin/TU dapat melihat dokumen pada daftar pengajuan masuk.

### 11.6 Kondisi Gagal

Pengajuan dapat gagal jika:

1. File bukan PDF.
2. File PDF rusak.
3. Ukuran file melebihi batas.
4. Metadata wajib tidak diisi.
5. Session pemohon berakhir.
6. Storage tidak dapat menyimpan file.
7. Database gagal menyimpan record.

Jika terjadi kegagalan, sistem harus:

1. Menampilkan pesan yang jelas.
2. Tidak membuat data dokumen parsial yang tidak valid.
3. Menghapus file sementara jika upload sudah dimulai.
4. Mencatat error teknis jika error terjadi di sisi server.

---

## 12. Alur Proses Admin/TU

### 12.1 Tujuan Alur

Alur proses Admin/TU digunakan untuk memeriksa dokumen yang diajukan pemohon, melengkapi metadata resmi, menentukan posisi elemen pada PDF, dan mengirim dokumen ke jalur verifikasi.

### 12.2 Prasyarat

Kondisi yang harus terpenuhi:

1. Admin/TU sudah login.
2. Admin/TU memiliki permission document.review.
3. Terdapat dokumen dengan status DIAJUKAN.
4. File PDF draft dapat diakses dari storage.
5. Data pemohon dan metadata awal tersedia.

### 12.3 Data yang Dikelola Admin/TU

Admin/TU dapat mengelola data berikut:

1. Nomor surat.
2. Tanggal surat.
3. Sifat surat.
4. Klasifikasi surat.
5. Penandatangan.
6. Jabatan penandatangan.
7. Tujuan surat jika diperlukan.
8. Catatan administratif.
9. Posisi nomor surat.
10. Posisi tanggal surat.
11. Posisi tanda tangan elektronik.
12. Posisi QR Code.
13. Jalur verifikasi.

### 12.4 Langkah-Langkah Proses Admin/TU

Alur step-by-step:

1. Admin/TU membuka dashboard.
2. Sistem menampilkan daftar dokumen dengan status DIAJUKAN.
3. Admin/TU memilih salah satu dokumen.
4. Sistem membuka halaman detail dokumen.
5. Sistem menampilkan metadata awal dari pemohon.
6. Sistem menampilkan preview PDF draft.
7. Admin/TU memeriksa apakah PDF dapat dibaca.
8. Admin/TU memeriksa kesesuaian perihal dengan isi dokumen.
9. Admin/TU memeriksa kelengkapan administratif.
10. Jika dokumen tidak layak diproses, Admin/TU memilih tindakan revisi atau penolakan sesuai kewenangan.
11. Jika dokumen layak diproses, Admin/TU mengubah status dokumen menjadi DIPROSES.
12. Sistem mencatat perubahan status pada document_logs.
13. Admin/TU mengisi nomor surat jika nomor sudah tersedia.
14. Admin/TU mengisi tanggal surat.
15. Admin/TU menentukan sifat surat.
16. Admin/TU memilih penandatangan.
17. Admin/TU mengisi jabatan penandatangan jika belum tersedia dari data master.
18. Admin/TU membuka fitur positioning elemen PDF.
19. Sistem menampilkan preview PDF dengan area koordinat.
20. Admin/TU memilih elemen nomor surat.
21. Admin/TU menempatkan elemen nomor surat pada halaman dan posisi yang sesuai.
22. Sistem menyimpan koordinat nomor surat.
23. Admin/TU memilih elemen tanggal surat.
24. Admin/TU menempatkan elemen tanggal surat pada halaman dan posisi yang sesuai.
25. Sistem menyimpan koordinat tanggal surat.
26. Admin/TU memilih elemen QR Code atau TTE.
27. Admin/TU menempatkan elemen QR Code atau TTE pada halaman dan posisi yang sesuai.
28. Sistem menyimpan koordinat QR Code atau TTE.
29. Admin/TU melakukan preview hasil posisi elemen.
30. Sistem menampilkan simulasi penempatan elemen pada PDF.
31. Admin/TU melakukan koreksi posisi jika diperlukan.
32. Admin/TU memilih jalur verifikasi.
33. Admin/TU menentukan Verifikator Level 1.
34. Admin/TU menentukan Verifikator Level 2 jika dibutuhkan.
35. Admin/TU menentukan Verifikator Level 3 jika dibutuhkan.
36. Admin/TU meninjau ulang metadata dan jalur verifikasi.
37. Admin/TU menekan tombol kirim ke verifikator.
38. Sistem memvalidasi bahwa metadata wajib telah lengkap.
39. Sistem memvalidasi bahwa posisi elemen wajib telah diatur.
40. Sistem memvalidasi bahwa jalur verifikasi telah ditentukan.
41. Sistem membuat record verifikasi untuk setiap level yang ditentukan.
42. Sistem menetapkan level aktif pertama sebagai Level 1.
43. Sistem mengubah status dokumen menjadi MENUNGGU_VERIFIKASI.
44. Sistem mencatat aktivitas pengiriman ke verifikator pada document_logs.
45. Sistem menampilkan notifikasi bahwa dokumen berhasil dikirim ke verifikasi.

### 12.5 Output Alur

Output dari alur proses Admin/TU:

1. Metadata resmi dokumen tersimpan.
2. Posisi elemen PDF tersimpan.
3. Jalur verifikasi tersimpan.
4. Status dokumen menjadi MENUNGGU_VERIFIKASI.
5. Verifikator Level 1 menerima tugas.
6. Audit log proses Admin/TU tercatat.

### 12.6 Ketentuan Penting

Ketentuan penting pada tahap Admin/TU:

1. Admin/TU tidak melakukan konversi DOCX ke PDF.
2. Admin/TU tidak mengubah substansi isi dokumen PDF.
3. Admin/TU hanya melakukan review, metadata, positioning, dan workflow assignment.
4. Posisi elemen wajib divalidasi sebelum dokumen masuk ke verifikasi.
5. Nomor surat dapat bersifat final atau sementara sesuai kebijakan organisasi.

---

## 13. Alur Verifikasi Bertingkat

### 13.1 Tujuan Alur

Alur verifikasi bertingkat memastikan dokumen diperiksa oleh pihak yang memiliki kewenangan sebelum dokumen diterbitkan. Setiap level verifikasi bertindak sebagai gerbang kontrol.

### 13.2 Prinsip Verifikasi

Prinsip verifikasi:

1. Dokumen hanya dapat diverifikasi oleh user yang ditugaskan.
2. Level verifikasi berjalan berurutan.
3. Level berikutnya tidak dapat memproses dokumen sebelum level sebelumnya menyetujui.
4. Setiap keputusan harus dicatat.
5. Keputusan reject atau perlu revisi menghentikan alur approval normal.
6. Setiap catatan verifikator menjadi bagian dari riwayat dokumen.

### 13.3 Langkah Verifikasi Level 1

Alur step-by-step:

1. Verifikator Level 1 login ke sistem.
2. Sistem menampilkan daftar tugas verifikasi.
3. Verifikator memilih dokumen yang menunggu persetujuan.
4. Sistem menampilkan detail dokumen, metadata, PDF draft, dan riwayat proses.
5. Verifikator membaca metadata awal dari pemohon.
6. Verifikator membaca metadata resmi dari Admin/TU.
7. Verifikator membuka preview PDF.
8. Verifikator memeriksa isi dokumen.
9. Verifikator memeriksa kesesuaian dokumen dengan perihal.
10. Verifikator memeriksa kelengkapan lampiran jika relevan.
11. Verifikator memilih salah satu keputusan: approve, perlu revisi, atau reject.
12. Jika approve, Verifikator mengisi catatan persetujuan jika diperlukan.
13. Sistem menyimpan keputusan approve pada tabel verifications.
14. Sistem mengubah status dokumen menjadi DISETUJUI_LEVEL_1.
15. Jika terdapat Level 2, sistem mengaktifkan tugas verifikasi Level 2.
16. Jika tidak terdapat Level 2, sistem mengubah dokumen menjadi SIAP_PUBLISH.
17. Sistem mencatat keputusan pada document_logs.

### 13.4 Langkah Verifikasi Level 2

Alur step-by-step:

1. Verifikator Level 2 login ke sistem.
2. Sistem menampilkan tugas dokumen yang telah disetujui Level 1.
3. Verifikator membuka detail dokumen.
4. Sistem menampilkan hasil verifikasi Level 1.
5. Verifikator memeriksa catatan Level 1.
6. Verifikator memeriksa PDF draft.
7. Verifikator memeriksa metadata resmi.
8. Verifikator melakukan penilaian sesuai kewenangan Level 2.
9. Verifikator memilih approve, perlu revisi, atau reject.
10. Jika approve, sistem menyimpan keputusan pada tabel verifications.
11. Sistem mengubah status dokumen menjadi DISETUJUI_LEVEL_2.
12. Jika terdapat Level 3, sistem mengaktifkan tugas verifikasi Level 3.
13. Jika tidak terdapat Level 3, sistem mengubah status dokumen menjadi SIAP_PUBLISH.
14. Sistem mencatat aktivitas pada document_logs.

### 13.5 Langkah Verifikasi Level 3

Alur step-by-step:

1. Verifikator Level 3 login ke sistem.
2. Sistem menampilkan tugas dokumen yang telah disetujui Level 2.
3. Verifikator membuka detail dokumen.
4. Sistem menampilkan metadata, PDF draft, dan riwayat approval Level 1 dan Level 2.
5. Verifikator memeriksa keseluruhan dokumen.
6. Verifikator memastikan tidak ada catatan yang belum ditindaklanjuti.
7. Verifikator memilih approve, perlu revisi, atau reject.
8. Jika approve, sistem menyimpan keputusan pada tabel verifications.
9. Sistem mengubah status dokumen menjadi DISETUJUI_LEVEL_3.
10. Sistem mengubah status dokumen menjadi SIAP_PUBLISH sesuai kebijakan transisi.
11. Sistem mencatat keputusan akhir verifikasi pada document_logs.
12. Dokumen masuk ke daftar dokumen siap publish.

### 13.6 Output Verifikasi

Output dari proses verifikasi:

1. Keputusan tiap level tersimpan.
2. Catatan verifikator tersimpan.
3. Status dokumen diperbarui.
4. Level verifikasi berikutnya diaktifkan jika ada.
5. Audit log tercatat.
6. Dokumen masuk ke tahap publish jika seluruh level menyetujui.

---

## 14. Alur Revisi Dokumen

### 14.1 Tujuan Alur

Alur revisi digunakan ketika dokumen belum memenuhi persyaratan tetapi masih dapat diperbaiki oleh pemohon.

### 14.2 Pemicu Revisi

Revisi dapat dipicu oleh:

1. Admin/TU pada tahap review administratif.
2. Verifikator Level 1.
3. Verifikator Level 2.
4. Verifikator Level 3.
5. Penandatangan jika terlibat dalam approval akhir.

### 14.3 Langkah Permintaan Revisi

Alur step-by-step:

1. Aktor pemeriksa membuka detail dokumen.
2. Aktor pemeriksa menemukan kekurangan pada dokumen.
3. Aktor pemeriksa memilih tindakan perlu revisi.
4. Sistem menampilkan form catatan revisi.
5. Aktor pemeriksa mengisi catatan revisi secara jelas.
6. Aktor pemeriksa mengirim permintaan revisi.
7. Sistem menyimpan catatan revisi pada verifications atau document_logs.
8. Sistem mengubah status dokumen menjadi PERLU_REVISI.
9. Sistem menonaktifkan tugas verifikasi aktif.
10. Sistem memberi notifikasi kepada pemohon.
11. Pemohon membuka dokumen yang perlu revisi.
12. Pemohon membaca catatan revisi.
13. Pemohon memperbaiki dokumen di luar sistem.
14. Pemohon menghasilkan PDF revisi.
15. Pemohon mengunggah PDF revisi.
16. Sistem memvalidasi file PDF revisi.
17. Sistem menyimpan file PDF revisi sebagai versi baru.
18. Sistem mengubah status dokumen kembali menjadi DIAJUKAN atau DIPROSES sesuai kebijakan.
19. Sistem mencatat aktivitas upload revisi pada document_logs.
20. Admin/TU memproses kembali dokumen revisi.

### 14.4 Prinsip Versi Dokumen

Sistem disarankan menyimpan versi dokumen agar riwayat revisi tetap dapat ditelusuri.

Prinsip versi dokumen:

1. File PDF awal tidak dihapus.
2. File PDF revisi disimpan sebagai versi baru.
3. Setiap versi memiliki nomor versi.
4. Hanya versi aktif yang diproses dalam workflow.
5. Riwayat versi dapat dilihat oleh aktor yang berwenang.

---

## 15. Alur Penolakan Dokumen

### 15.1 Tujuan Alur

Alur penolakan digunakan ketika dokumen tidak dapat dilanjutkan ke proses berikutnya karena alasan administratif, substansi, kewenangan, atau kebijakan.

### 15.2 Pemicu Penolakan

Penolakan dapat dilakukan oleh:

1. Admin/TU jika dokumen tidak memenuhi persyaratan awal.
2. Verifikator Level 1.
3. Verifikator Level 2.
4. Verifikator Level 3.
5. Penandatangan jika memiliki kewenangan approval akhir.
6. Super Admin dalam kondisi administratif khusus.

### 15.3 Langkah Penolakan

Alur step-by-step:

1. Aktor pemeriksa membuka detail dokumen.
2. Aktor pemeriksa meninjau metadata dan PDF.
3. Aktor pemeriksa menentukan bahwa dokumen tidak dapat dilanjutkan.
4. Aktor pemeriksa memilih tindakan reject atau tolak.
5. Sistem menampilkan form alasan penolakan.
6. Aktor pemeriksa wajib mengisi alasan penolakan.
7. Aktor pemeriksa mengonfirmasi tindakan penolakan.
8. Sistem menyimpan alasan penolakan.
9. Sistem mengubah status dokumen menjadi DITOLAK.
10. Sistem menonaktifkan semua tugas verifikasi aktif.
11. Sistem mencatat aktivitas penolakan pada document_logs.
12. Sistem memberi notifikasi kepada pemohon.
13. Dokumen tidak dapat diproses ke tahap publish.

### 15.4 Dampak Penolakan

Setelah dokumen ditolak:

1. Dokumen menjadi status final.
2. Pemohon dapat melihat alasan penolakan.
3. Dokumen tidak dapat diedit melalui workflow normal.
4. Pengajuan baru harus dibuat jika pemohon ingin mengajukan dokumen yang berbeda.
5. Super Admin dapat melihat riwayat penolakan untuk audit.

---

## 16. Alur Finalisasi dan Publish Dokumen

### 16.1 Tujuan Alur

Alur finalisasi dan publish digunakan untuk menghasilkan dokumen final yang memuat elemen resmi seperti nomor surat, tanggal, QR Code, dan tanda tangan elektronik atau informasi penandatangan.

### 16.2 Prasyarat Finalisasi

Dokumen hanya dapat difinalisasi jika:

1. Status dokumen adalah SIAP_PUBLISH.
2. Seluruh level verifikasi yang diwajibkan telah menyetujui.
3. Metadata resmi telah lengkap.
4. Posisi elemen wajib telah dikonfigurasi.
5. File PDF draft aktif tersedia.
6. QR Code dapat dibuat.
7. PDF engine tersedia.
8. User yang melakukan publish memiliki permission document.publish.

### 16.3 Elemen yang Ditambahkan ke PDF Final

Elemen final dapat mencakup:

1. Nomor surat.
2. Tanggal surat.
3. Nama penandatangan.
4. Jabatan penandatangan.
5. Tanda tangan elektronik.
6. QR Code validasi.
7. Token validasi.
8. Label status dokumen jika diperlukan.

### 16.4 Langkah Finalisasi

Alur step-by-step:

1. Admin/TU membuka daftar dokumen siap publish.
2. Sistem menampilkan dokumen dengan status SIAP_PUBLISH.
3. Admin/TU memilih dokumen yang akan dipublish.
4. Sistem membuka halaman finalisasi.
5. Sistem menampilkan metadata resmi dokumen.
6. Sistem menampilkan hasil verifikasi seluruh level.
7. Sistem menampilkan preview PDF draft.
8. Sistem menampilkan konfigurasi posisi elemen.
9. Admin/TU melakukan pemeriksaan akhir.
10. Admin/TU menekan tombol generate preview final.
11. Sistem membuat token validasi dokumen.
12. Sistem membuat URL validasi dokumen.
13. Sistem menghasilkan QR Code berdasarkan URL validasi.
14. Sistem membaca PDF draft aktif.
15. Sistem menempatkan nomor surat pada koordinat yang ditentukan.
16. Sistem menempatkan tanggal surat pada koordinat yang ditentukan.
17. Sistem menempatkan QR Code pada koordinat yang ditentukan.
18. Sistem menempatkan tanda tangan elektronik atau informasi penandatangan jika dikonfigurasi.
19. Sistem menghasilkan file PDF preview final.
20. Admin/TU memeriksa preview final.
21. Jika posisi elemen belum sesuai, Admin/TU kembali ke pengaturan posisi.
22. Jika preview sudah sesuai, Admin/TU menekan tombol publish.
23. Sistem menjalankan proses generate PDF final.
24. Sistem menyimpan PDF final ke storage final.
25. Sistem menghitung checksum atau hash file final jika diperlukan.
26. Sistem mengubah status dokumen menjadi PUBLISHED.
27. Sistem menyimpan waktu publish.
28. Sistem mencatat user yang melakukan publish.
29. Sistem mencatat aktivitas publish pada document_logs.
30. Sistem menampilkan notifikasi bahwa dokumen telah dipublikasikan.

### 16.5 Output Finalisasi

Output finalisasi:

1. PDF final tersedia.
2. QR Code tertanam pada PDF final.
3. Metadata publish tersimpan.
4. Status dokumen menjadi PUBLISHED.
5. URL validasi aktif.
6. Audit log publish tercatat.

### 16.6 Kondisi Gagal Publish

Publish dapat gagal jika:

1. PDF draft tidak ditemukan.
2. PDF draft rusak.
3. Metadata wajib belum lengkap.
4. Posisi elemen belum tersedia.
5. QR Code gagal dibuat.
6. PDF engine gagal menulis file.
7. Storage final tidak dapat diakses.
8. Database gagal memperbarui status.

Jika gagal, sistem harus:

1. Mengubah status menjadi GAGAL_PUBLISH atau mempertahankan SIAP_PUBLISH dengan error log.
2. Menyimpan pesan error teknis.
3. Tidak menandai dokumen sebagai PUBLISHED.
4. Tidak mengaktifkan halaman validasi sebagai dokumen valid.
5. Memberikan opsi retry kepada Admin/TU atau Super Admin.

---

## 17. Alur Validasi Dokumen Menggunakan QR Code

### 17.1 Tujuan Validasi

Validasi QR Code digunakan untuk memastikan bahwa dokumen final yang beredar dapat diverifikasi keasliannya melalui sistem SIMAS.

### 17.2 Komponen Validasi

Komponen validasi:

1. Token validasi unik.
2. URL halaman validasi.
3. QR Code yang tertanam di PDF final.
4. Data dokumen final.
5. Status dokumen.
6. Checksum file jika digunakan.

### 17.3 Langkah Validasi oleh Pengguna

Alur step-by-step:

1. Pengguna menerima atau membuka dokumen PDF final.
2. Pengguna memindai QR Code pada dokumen.
3. Perangkat pengguna membuka URL validasi SIMAS.
4. Sistem membaca token validasi dari URL.
5. Sistem mencari dokumen berdasarkan token validasi.
6. Jika token tidak ditemukan, sistem menampilkan status tidak valid.
7. Jika token ditemukan, sistem memeriksa status dokumen.
8. Jika status dokumen PUBLISHED, sistem menampilkan informasi valid.
9. Sistem menampilkan metadata dokumen yang boleh dipublikasikan.
10. Sistem menampilkan nomor surat.
11. Sistem menampilkan tanggal surat.
12. Sistem menampilkan perihal dokumen.
13. Sistem menampilkan penandatangan.
14. Sistem menampilkan waktu publish.
15. Sistem dapat menampilkan checksum dokumen jika tersedia.
16. Sistem tidak menampilkan data sensitif yang tidak boleh diakses publik.

### 17.4 Respons Validasi

Kemungkinan respons validasi:

1. Dokumen valid dan telah dipublikasikan.
2. Dokumen ditemukan tetapi belum dipublikasikan.
3. Dokumen telah dibatalkan atau dicabut.
4. Token validasi tidak ditemukan.
5. Token validasi tidak aktif.
6. Sistem sedang tidak dapat melakukan validasi.

### 17.5 Data yang Ditampilkan pada Halaman Validasi

Data minimal:

1. Status validasi.
2. Nomor surat.
3. Tanggal surat.
4. Perihal.
5. Nama penandatangan.
6. Jabatan penandatangan.
7. Tanggal publish.
8. Identitas instansi atau unit penerbit.

Data yang tidak boleh ditampilkan secara bebas:

1. Catatan internal verifikator.
2. Riwayat revisi.
3. Data pemohon yang bersifat pribadi.
4. File internal draft.
5. Audit log.

---

## 18. Alur Audit Log

### 18.1 Tujuan Audit Log

Audit log digunakan untuk mencatat seluruh aktivitas penting pada dokumen. Audit log menjadi dasar akuntabilitas, investigasi, pelacakan status, dan pemenuhan kontrol internal.

### 18.2 Aktivitas yang Wajib Dicatat

Aktivitas berikut wajib dicatat:

1. Dokumen dibuat.
2. PDF draft diunggah.
3. Metadata awal disimpan.
4. Dokumen diproses Admin/TU.
5. Metadata resmi diperbarui.
6. Posisi elemen PDF diatur.
7. Jalur verifikasi ditentukan.
8. Dokumen dikirim ke verifikator.
9. Verifikator menyetujui dokumen.
10. Verifikator meminta revisi.
11. Verifikator menolak dokumen.
12. Pemohon mengunggah revisi.
13. Dokumen siap publish.
14. PDF final dibuat.
15. Dokumen dipublish.
16. Dokumen gagal dipublish.
17. Dokumen dibatalkan.
18. Super Admin melakukan perubahan administratif.

### 18.3 Struktur Informasi Audit Log

Setiap log minimal memuat:

1. ID log.
2. ID dokumen.
3. ID user pelaku.
4. Role pelaku saat aksi dilakukan.
5. Jenis aksi.
6. Status sebelum aksi.
7. Status setelah aksi.
8. Catatan aksi.
9. Metadata tambahan dalam format JSON jika diperlukan.
10. IP address.
11. User agent.
12. Timestamp.

### 18.4 Prinsip Audit Log

Prinsip audit log:

1. Log tidak boleh dihapus melalui fitur normal.
2. Log tidak boleh diedit oleh pengguna biasa.
3. Perubahan penting harus selalu menghasilkan log.
4. Log harus dapat difilter berdasarkan dokumen, user, aksi, dan tanggal.
5. Log harus tetap tersedia meskipun dokumen sudah berstatus final.

---

## 19. Struktur Modul Sistem

### 19.1 Modul Autentikasi

Modul autentikasi menangani:

1. Login.
2. Logout.
3. Session management.
4. Password reset jika tersedia.
5. Validasi akun aktif.

### 19.2 Modul Manajemen User

Modul manajemen user menangani:

1. Pembuatan user.
2. Perubahan data user.
3. Aktivasi dan deaktivasi user.
4. Penetapan role.
5. Penetapan unit kerja jika diperlukan.

### 19.3 Modul Pengajuan Dokumen

Modul pengajuan dokumen menangani:

1. Form pengajuan.
2. Upload PDF.
3. Validasi PDF.
4. Penyimpanan file.
5. Penyimpanan metadata awal.
6. Daftar pengajuan pemohon.

### 19.4 Modul Review Admin/TU

Modul review Admin/TU menangani:

1. Daftar pengajuan masuk.
2. Preview PDF.
3. Review metadata.
4. Pengisian metadata resmi.
5. Pengembalian dokumen ke pemohon.
6. Pengiriman dokumen ke workflow verifikasi.

### 19.5 Modul PDF Positioning

Modul PDF positioning menangani:

1. Preview halaman PDF.
2. Drag and drop elemen.
3. Penyimpanan koordinat elemen.
4. Preview simulasi elemen.
5. Validasi posisi elemen wajib.

### 19.6 Modul Workflow Verifikasi

Modul workflow verifikasi menangani:

1. Penugasan verifikator.
2. Daftar tugas verifikasi.
3. Approve.
4. Reject.
5. Request revision.
6. Catatan verifikasi.
7. Perpindahan level approval.

### 19.7 Modul Finalisasi PDF

Modul finalisasi PDF menangani:

1. Generate QR Code.
2. Generate token validasi.
3. Penempatan elemen pada PDF.
4. Generate preview final.
5. Generate PDF final.
6. Penyimpanan file final.

### 19.8 Modul Validasi Publik

Modul validasi publik menangani:

1. Pembacaan token validasi.
2. Validasi status dokumen.
3. Tampilan informasi dokumen valid.
4. Penanganan token tidak valid.

### 19.9 Modul Audit Log

Modul audit log menangani:

1. Pencatatan aktivitas.
2. Tampilan riwayat dokumen.
3. Filter log.
4. Ekspor log jika diperlukan.

---

## 20. Spesifikasi Fitur Utama

### 20.1 Upload PDF

Fitur upload PDF memungkinkan pemohon mengirim file draft dokumen.

Ketentuan fitur:

1. Format file wajib PDF.
2. Sistem harus memvalidasi ekstensi dan MIME type.
3. Sistem harus memvalidasi ukuran maksimal file.
4. Sistem harus menyimpan file dengan nama internal yang aman.
5. Sistem tidak boleh mengandalkan nama file asli sebagai identitas utama.
6. Sistem harus menyimpan metadata file seperti ukuran, path, dan checksum jika digunakan.

### 20.2 Approval Bertingkat

Fitur approval bertingkat memungkinkan dokumen melewati beberapa level persetujuan.

Ketentuan fitur:

1. Jalur approval ditentukan oleh Admin/TU.
2. Setiap level memiliki satu atau lebih verifikator sesuai desain sistem.
3. Sistem harus menentukan level aktif.
4. Verifikator hanya dapat memproses level yang aktif.
5. Keputusan approve melanjutkan workflow.
6. Keputusan perlu revisi mengembalikan dokumen ke pemohon.
7. Keputusan reject mengakhiri workflow.

### 20.3 Drag and Drop Posisi Elemen PDF

Fitur drag and drop memungkinkan Admin/TU menentukan posisi elemen resmi pada PDF final.

Ketentuan fitur:

1. Sistem menampilkan preview PDF.
2. Admin/TU dapat memilih jenis elemen.
3. Admin/TU dapat meletakkan elemen pada halaman tertentu.
4. Sistem menyimpan koordinat X dan Y.
5. Sistem menyimpan ukuran elemen.
6. Sistem menyimpan nomor halaman.
7. Sistem dapat menampilkan preview simulasi hasil akhir.

### 20.4 QR Code Validasi

Fitur QR Code validasi memungkinkan dokumen final diverifikasi secara digital.

Ketentuan fitur:

1. QR Code dibuat saat finalisasi.
2. QR Code berisi URL validasi.
3. URL validasi memuat token unik.
4. Token tidak mudah ditebak.
5. Halaman validasi hanya menampilkan data yang aman untuk publik.
6. QR Code ditempatkan pada PDF final sesuai konfigurasi posisi.

### 20.5 Role-Based System

Fitur role-based system memastikan akses fitur mengikuti peran.

Ketentuan fitur:

1. Setiap user memiliki minimal satu role.
2. Setiap role memiliki permission.
3. Middleware Laravel digunakan untuk membatasi akses.
4. Policy atau Gate digunakan untuk otorisasi berbasis dokumen.
5. Akses ditolak jika user tidak memiliki hak.

### 20.6 Audit Log

Fitur audit log mencatat seluruh aktivitas penting.

Ketentuan fitur:

1. Log dibuat otomatis oleh service atau observer.
2. Log mencatat user, aksi, status, dan waktu.
3. Log dapat dilihat oleh user berwenang.
4. Log tidak boleh diubah melalui fitur normal.

---

## 21. Spesifikasi Database Core

### 21.1 Tabel users

Tabel users menyimpan data pengguna sistem.

Kolom yang disarankan:

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Primary key. |
| name | VARCHAR(255) | Nama lengkap pengguna. |
| email | VARCHAR(255) | Email login pengguna. |
| password | VARCHAR(255) | Password terenkripsi. |
| role | VARCHAR(50) | Role utama jika sistem belum menggunakan tabel role terpisah. |
| unit_id | BIGINT UNSIGNED NULL | Unit kerja pengguna jika tersedia. |
| is_active | BOOLEAN | Status aktif akun. |
| last_login_at | TIMESTAMP NULL | Waktu login terakhir. |
| created_at | TIMESTAMP | Waktu pembuatan data. |
| updated_at | TIMESTAMP | Waktu perubahan data. |

Catatan enterprise:

1. Untuk sistem skala besar, role sebaiknya dipisahkan ke tabel roles, permissions, dan role_user.
2. Password wajib disimpan menggunakan hashing Laravel.
3. Email harus unik.
4. Akun nonaktif tidak boleh login.

### 21.2 Tabel documents

Tabel documents menyimpan data utama dokumen.

Kolom yang disarankan:

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Primary key. |
| document_code | VARCHAR(100) | Kode internal dokumen. |
| applicant_id | BIGINT UNSIGNED | User pemohon. |
| current_status | VARCHAR(50) | Status dokumen saat ini. |
| current_level | INTEGER NULL | Level verifikasi aktif. |
| draft_file_path | VARCHAR(500) | Path PDF draft aktif. |
| final_file_path | VARCHAR(500) NULL | Path PDF final setelah publish. |
| original_file_name | VARCHAR(255) | Nama file asli dari pemohon. |
| file_size | BIGINT | Ukuran file dalam byte. |
| file_hash | VARCHAR(128) NULL | Hash file draft atau final. |
| validation_token | VARCHAR(255) NULL | Token validasi QR Code. |
| published_at | TIMESTAMP NULL | Waktu publish. |
| published_by | BIGINT UNSIGNED NULL | User yang melakukan publish. |
| created_at | TIMESTAMP | Waktu pembuatan data. |
| updated_at | TIMESTAMP | Waktu perubahan data. |

Catatan enterprise:

1. current_status harus mengikuti daftar status resmi.
2. validation_token harus unik.
3. final_file_path hanya boleh terisi jika dokumen sudah dipublish.
4. Perubahan status harus dicatat pada document_logs.

### 21.3 Tabel document_metadata

Tabel document_metadata menyimpan metadata dokumen.

Kolom yang disarankan:

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Primary key. |
| document_id | BIGINT UNSIGNED | Relasi ke documents. |
| subject | VARCHAR(255) | Perihal dokumen. |
| summary | TEXT NULL | Ringkasan dokumen. |
| attachment_note | TEXT NULL | Informasi lampiran. |
| letter_number | VARCHAR(150) NULL | Nomor surat resmi. |
| letter_date | DATE NULL | Tanggal surat. |
| letter_type | VARCHAR(100) NULL | Jenis surat. |
| letter_classification | VARCHAR(100) NULL | Klasifikasi surat. |
| letter_nature | VARCHAR(100) NULL | Sifat surat. |
| signer_id | BIGINT UNSIGNED NULL | User penandatangan jika tersedia. |
| signer_name | VARCHAR(255) NULL | Nama penandatangan. |
| signer_position | VARCHAR(255) NULL | Jabatan penandatangan. |
| admin_note | TEXT NULL | Catatan Admin/TU. |
| created_at | TIMESTAMP | Waktu pembuatan data. |
| updated_at | TIMESTAMP | Waktu perubahan data. |

Catatan enterprise:

1. Metadata awal dapat diisi oleh pemohon.
2. Metadata resmi diisi atau dikunci oleh Admin/TU.
3. Perubahan metadata setelah dokumen PUBLISHED tidak diperbolehkan melalui workflow normal.

### 21.4 Tabel document_positions

Tabel document_positions menyimpan posisi elemen pada PDF.

Kolom yang disarankan:

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Primary key. |
| document_id | BIGINT UNSIGNED | Relasi ke documents. |
| element_type | VARCHAR(50) | Jenis elemen, misalnya letter_number, letter_date, qr_code, signature. |
| page_number | INTEGER | Nomor halaman PDF. |
| x_position | DECIMAL(10,2) | Koordinat X. |
| y_position | DECIMAL(10,2) | Koordinat Y. |
| width | DECIMAL(10,2) NULL | Lebar elemen. |
| height | DECIMAL(10,2) NULL | Tinggi elemen. |
| font_size | INTEGER NULL | Ukuran font jika elemen teks. |
| alignment | VARCHAR(20) NULL | Perataan teks jika diperlukan. |
| created_by | BIGINT UNSIGNED | User yang membuat posisi. |
| updated_by | BIGINT UNSIGNED NULL | User yang terakhir memperbarui posisi. |
| created_at | TIMESTAMP | Waktu pembuatan data. |
| updated_at | TIMESTAMP | Waktu perubahan data. |

Catatan enterprise:

1. Setiap dokumen dapat memiliki banyak posisi elemen.
2. Kombinasi document_id dan element_type sebaiknya unik untuk elemen wajib jika hanya boleh ada satu posisi.
3. Nomor halaman harus divalidasi agar tidak melebihi jumlah halaman PDF.
4. Koordinat harus sesuai dengan sistem ukuran PDF engine yang digunakan.

### 21.5 Tabel verifications

Tabel verifications menyimpan jalur dan keputusan verifikasi.

Kolom yang disarankan:

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Primary key. |
| document_id | BIGINT UNSIGNED | Relasi ke documents. |
| level | INTEGER | Level verifikasi. |
| verifier_id | BIGINT UNSIGNED | User verifikator. |
| status | VARCHAR(50) | pending, approved, revision, rejected, skipped. |
| decision_note | TEXT NULL | Catatan keputusan. |
| assigned_at | TIMESTAMP NULL | Waktu tugas diberikan. |
| decided_at | TIMESTAMP NULL | Waktu keputusan dibuat. |
| created_at | TIMESTAMP | Waktu pembuatan data. |
| updated_at | TIMESTAMP | Waktu perubahan data. |

Catatan enterprise:

1. Hanya satu level aktif pada satu waktu untuk workflow berurutan.
2. Verifikator tidak boleh memutuskan dokumen di luar level yang ditugaskan.
3. Catatan wajib diisi untuk keputusan revision dan rejected.

### 21.6 Tabel document_logs

Tabel document_logs menyimpan audit trail dokumen.

Kolom yang disarankan:

| Kolom | Tipe | Keterangan |
| --- | --- | --- |
| id | BIGINT UNSIGNED | Primary key. |
| document_id | BIGINT UNSIGNED | Relasi ke documents. |
| user_id | BIGINT UNSIGNED NULL | User yang melakukan aksi. |
| action | VARCHAR(100) | Jenis aksi. |
| status_before | VARCHAR(50) NULL | Status sebelum aksi. |
| status_after | VARCHAR(50) NULL | Status setelah aksi. |
| description | TEXT NULL | Deskripsi aktivitas. |
| payload | JSON NULL | Metadata tambahan. |
| ip_address | VARCHAR(45) NULL | IP address. |
| user_agent | TEXT NULL | User agent. |
| created_at | TIMESTAMP | Waktu log dibuat. |

Catatan enterprise:

1. document_logs tidak memerlukan updated_at jika log bersifat immutable.
2. Log harus dibuat dalam transaksi yang sama dengan aksi utama jika memungkinkan.
3. Payload JSON digunakan untuk menyimpan detail perubahan tanpa mengubah struktur tabel setiap saat.

---

## 22. Relasi Data

### 22.1 Relasi Utama

Relasi utama:

1. users memiliki banyak documents sebagai pemohon.
2. users dapat memiliki banyak documents sebagai publisher.
3. documents memiliki satu document_metadata.
4. documents memiliki banyak document_positions.
5. documents memiliki banyak verifications.
6. documents memiliki banyak document_logs.
7. users memiliki banyak verifications sebagai verifikator.
8. users memiliki banyak document_logs sebagai pelaku aktivitas.

### 22.2 Kardinalitas

| Relasi | Kardinalitas |
| --- | --- |
| users ke documents | One-to-Many |
| documents ke document_metadata | One-to-One |
| documents ke document_positions | One-to-Many |
| documents ke verifications | One-to-Many |
| documents ke document_logs | One-to-Many |
| users ke verifications | One-to-Many |
| users ke document_logs | One-to-Many |

### 22.3 Integritas Referensial

Ketentuan integritas:

1. document_metadata.document_id harus merujuk ke documents.id.
2. document_positions.document_id harus merujuk ke documents.id.
3. verifications.document_id harus merujuk ke documents.id.
4. document_logs.document_id harus merujuk ke documents.id.
5. documents.applicant_id harus merujuk ke users.id.
6. verifications.verifier_id harus merujuk ke users.id.
7. Penghapusan dokumen tidak disarankan secara fisik jika sudah memiliki audit log.
8. Soft delete dapat digunakan untuk menjaga riwayat.

---

## 23. Aturan Bisnis

### 23.1 Aturan Pengajuan

Aturan pengajuan:

1. Pemohon wajib login.
2. File yang diunggah wajib PDF.
3. Perihal dokumen wajib diisi.
4. Ringkasan dokumen wajib diisi jika ditetapkan oleh kebijakan organisasi.
5. Pengajuan baru selalu dimulai dengan status DIAJUKAN.
6. Pemohon tidak dapat mengubah dokumen setelah masuk ke proses verifikasi kecuali status PERLU_REVISI.

### 23.2 Aturan Admin/TU

Aturan Admin/TU:

1. Admin/TU hanya dapat memproses dokumen dengan status DIAJUKAN atau DIPROSES.
2. Admin/TU wajib melengkapi metadata resmi sebelum mengirim ke verifikasi.
3. Admin/TU wajib mengatur posisi elemen yang diwajibkan.
4. Admin/TU wajib menentukan minimal satu verifikator.
5. Admin/TU tidak bertugas mengonversi DOCX ke PDF.

### 23.3 Aturan Verifikasi

Aturan verifikasi:

1. Verifikator hanya dapat memproses dokumen yang ditugaskan.
2. Verifikator Level 2 tidak dapat memproses sebelum Level 1 approve.
3. Verifikator Level 3 tidak dapat memproses sebelum Level 2 approve.
4. Catatan wajib diisi untuk revisi dan penolakan.
5. Approve harus mengubah status sesuai level.
6. Reject harus mengubah status menjadi DITOLAK.
7. Request revision harus mengubah status menjadi PERLU_REVISI.

### 23.4 Aturan Publish

Aturan publish:

1. Dokumen hanya dapat dipublish jika status SIAP_PUBLISH.
2. Seluruh verifikasi wajib harus approved.
3. PDF final harus berhasil dibuat sebelum status menjadi PUBLISHED.
4. QR Code validasi wajib tertanam pada PDF final jika kebijakan sistem mewajibkan validasi digital.
5. Dokumen PUBLISHED tidak boleh diubah melalui workflow normal.

### 23.5 Aturan Validasi QR Code

Aturan validasi QR Code:

1. Token validasi harus unik.
2. Token validasi dibuat saat finalisasi.
3. Halaman validasi hanya menyatakan dokumen valid jika status PUBLISHED.
4. Token tidak valid tidak boleh menampilkan data dokumen.
5. Validasi publik tidak boleh membuka data internal.

---

## 24. Validasi Input

### 24.1 Validasi File PDF

Validasi file PDF:

1. Ekstensi file harus .pdf.
2. MIME type harus application/pdf.
3. Ukuran file tidak melebihi batas konfigurasi.
4. File harus dapat dibaca oleh PDF parser atau PDF engine.
5. File tidak boleh kosong.
6. Nama file asli harus disanitasi sebelum ditampilkan.

### 24.2 Validasi Metadata Pemohon

Validasi metadata pemohon:

1. Perihal wajib diisi.
2. Perihal memiliki batas panjang karakter.
3. Ringkasan tidak boleh melebihi batas panjang yang ditentukan.
4. Catatan lampiran harus dalam format teks aman.
5. Input harus dilindungi dari script injection.

### 24.3 Validasi Metadata Admin/TU

Validasi metadata Admin/TU:

1. Nomor surat wajib jika dokumen akan dipublish.
2. Tanggal surat wajib jika dokumen akan dipublish.
3. Penandatangan wajib jika dokumen membutuhkan pengesahan.
4. Sifat surat harus berasal dari pilihan valid jika menggunakan data master.
5. Jalur verifikasi wajib memiliki minimal satu level.

### 24.4 Validasi Posisi Elemen

Validasi posisi elemen:

1. element_type harus valid.
2. page_number harus lebih besar dari 0.
3. page_number tidak boleh melebihi jumlah halaman PDF.
4. x_position dan y_position harus berada dalam batas halaman.
5. width dan height tidak boleh negatif.
6. QR Code harus memiliki ukuran minimal agar dapat dipindai.

---

## 25. Keamanan Sistem

### 25.1 Autentikasi

Ketentuan autentikasi:

1. Sistem menggunakan autentikasi Laravel.
2. Password disimpan dalam bentuk hash.
3. Session harus memiliki masa berlaku.
4. Logout harus menghapus session aktif.
5. Akun nonaktif tidak dapat login.

### 25.2 Otorisasi

Ketentuan otorisasi:

1. Middleware role digunakan pada level route.
2. Policy digunakan pada level dokumen.
3. Permission digunakan untuk aksi sensitif.
4. Setiap aksi approve, reject, publish, dan update metadata harus memeriksa hak akses.

### 25.3 Keamanan File

Ketentuan keamanan file:

1. File PDF disimpan di storage yang tidak dapat diakses langsung tanpa kontrol aplikasi jika dokumen bersifat internal.
2. Nama file internal menggunakan nama acak atau UUID.
3. File final memiliki path berbeda dari file draft.
4. File draft tidak boleh tersedia pada halaman validasi publik.
5. Download file harus melalui controller yang memeriksa akses.

### 25.4 Perlindungan Input

Ketentuan perlindungan input:

1. Semua input harus divalidasi.
2. Output teks harus di-escape pada tampilan.
3. Form harus menggunakan CSRF protection.
4. Upload file harus dibatasi tipe dan ukuran.
5. Payload JSON harus divalidasi sebelum digunakan.

### 25.5 Keamanan QR Code

Ketentuan keamanan QR Code:

1. Token validasi tidak boleh berupa ID dokumen berurutan.
2. Token harus sulit ditebak.
3. URL validasi tidak boleh memuat data sensitif.
4. Halaman validasi harus membatasi data yang ditampilkan.

---

## 26. Manajemen File PDF

### 26.1 Kategori File

SIMAS mengelola beberapa kategori file:

1. PDF draft awal.
2. PDF revisi.
3. PDF preview final.
4. PDF final published.

### 26.2 Penyimpanan File

Struktur penyimpanan yang disarankan:

```text
storage/app/documents/
  drafts/
    {year}/
      {document_uuid}.pdf
  revisions/
    {year}/
      {document_uuid}_v{version}.pdf
  previews/
    {year}/
      {document_uuid}_preview.pdf
  finals/
    {year}/
      {document_uuid}_final.pdf
```

### 26.3 Penamaan File

Aturan penamaan file:

1. Sistem tidak menggunakan nama asli sebagai nama penyimpanan utama.
2. Nama file internal menggunakan UUID atau identifier acak.
3. Nama asli hanya disimpan sebagai metadata.
4. File final dapat menggunakan format nama yang lebih informatif saat diunduh.

### 26.4 Akses File

Aturan akses file:

1. Pemohon dapat melihat file draft miliknya.
2. Admin/TU dapat melihat file draft dan final sesuai kewenangan.
3. Verifikator dapat melihat file yang ditugaskan.
4. Publik hanya dapat melihat informasi validasi, bukan otomatis mengakses file final kecuali kebijakan memperbolehkan.

---

## 27. Dynamic PDF Element Positioning

### 27.1 Elemen yang Dapat Diposisikan

Elemen yang dapat diposisikan:

1. Nomor surat.
2. Tanggal surat.
3. QR Code.
4. Tanda tangan elektronik.
5. Nama penandatangan.
6. Jabatan penandatangan.
7. Stempel digital jika diperlukan.

### 27.2 Data Posisi

Setiap posisi harus menyimpan:

1. ID dokumen.
2. Jenis elemen.
3. Nomor halaman.
4. Koordinat X.
5. Koordinat Y.
6. Lebar elemen.
7. Tinggi elemen.
8. Ukuran font jika elemen teks.
9. Alignment jika elemen teks.

### 27.3 Langkah Pengaturan Posisi

Alur step-by-step:

1. Admin/TU membuka halaman positioning.
2. Sistem memuat PDF draft.
3. Sistem merender halaman PDF sebagai preview.
4. Admin/TU memilih halaman yang akan dikonfigurasi.
5. Admin/TU memilih elemen yang akan ditempatkan.
6. Admin/TU melakukan drag elemen ke posisi yang diinginkan.
7. Sistem menangkap koordinat elemen pada area preview.
8. Sistem mengonversi koordinat preview ke koordinat PDF sebenarnya.
9. Admin/TU menyimpan posisi.
10. Sistem menyimpan posisi pada document_positions.
11. Admin/TU menjalankan preview final.
12. Sistem menampilkan hasil simulasi penempatan elemen.
13. Admin/TU menyesuaikan kembali jika posisi belum tepat.

### 27.4 Standarisasi Koordinat

Sistem harus memastikan koordinat konsisten antara tampilan frontend dan PDF engine.

Ketentuan:

1. Skala preview harus diketahui.
2. Ukuran halaman PDF harus dibaca secara akurat.
3. Koordinat dari browser harus dikonversi ke koordinat PDF.
4. Perbedaan origin koordinat harus ditangani.
5. Hasil preview harus mendekati hasil final.

---

## 28. Nomor Surat dan Metadata Dokumen

### 28.1 Nomor Surat

Nomor surat adalah identitas resmi dokumen yang ditambahkan ke PDF final. Nomor surat dapat diisi manual oleh Admin/TU atau dihasilkan otomatis oleh sistem jika tersedia modul penomoran.

Aturan nomor surat:

1. Nomor surat harus unik sesuai kebijakan organisasi.
2. Nomor surat tidak boleh kosong saat publish.
3. Perubahan nomor surat setelah publish tidak diperbolehkan melalui workflow normal.
4. Jika terjadi koreksi nomor setelah publish, harus melalui prosedur administratif khusus dan audit log.

### 28.2 Tanggal Surat

Tanggal surat menunjukkan tanggal resmi penerbitan dokumen.

Aturan tanggal surat:

1. Tanggal surat wajib sebelum publish.
2. Tanggal surat dapat berbeda dari tanggal pengajuan.
3. Tanggal surat dapat berbeda dari tanggal publish jika kebijakan organisasi mengizinkan.
4. Format tampilan tanggal harus konsisten.

### 28.3 Penandatangan

Penandatangan adalah pihak yang mengesahkan dokumen.

Aturan penandatangan:

1. Penandatangan harus dipilih sebelum publish.
2. Nama dan jabatan penandatangan harus ditampilkan sesuai format resmi.
3. Jika menggunakan tanda tangan elektronik, file atau representasi TTE harus tersedia.
4. Jika hanya menggunakan QR Code, sistem harus menampilkan informasi pengesahan yang sesuai.

---

## 29. Notifikasi Sistem

### 29.1 Tujuan Notifikasi

Notifikasi digunakan untuk memberi tahu pengguna bahwa terdapat aksi yang harus dilakukan atau status dokumen telah berubah.

### 29.2 Jenis Notifikasi

Jenis notifikasi:

1. Pengajuan berhasil dikirim.
2. Dokumen masuk ke daftar Admin/TU.
3. Dokumen dikirim ke verifikator.
4. Dokumen menunggu approval.
5. Dokumen disetujui oleh level tertentu.
6. Dokumen perlu revisi.
7. Dokumen ditolak.
8. Dokumen siap publish.
9. Dokumen telah dipublish.
10. Publish gagal.

### 29.3 Kanal Notifikasi

Kanal notifikasi yang dapat digunakan:

1. Notifikasi dalam aplikasi.
2. Email.
3. WhatsApp gateway jika tersedia.
4. Integrasi sistem notifikasi organisasi jika tersedia.

### 29.4 Aturan Notifikasi

Aturan notifikasi:

1. Notifikasi harus dikirim kepada aktor yang relevan.
2. Notifikasi tidak boleh membocorkan data sensitif.
3. Notifikasi harus memuat tautan ke detail dokumen jika pengguna memiliki akses.
4. Notifikasi harus dicatat jika diperlukan untuk audit operasional.

---

## 30. Logging dan Audit Trail

### 30.1 Application Log

Application log mencatat kejadian teknis seperti error PDF engine, kegagalan storage, exception aplikasi, dan gangguan integrasi.

Ketentuan:

1. Error teknis dicatat pada log aplikasi Laravel.
2. Informasi sensitif tidak boleh ditulis secara eksplisit pada log.
3. Log harus dapat dianalisis oleh administrator teknis.

### 30.2 Business Audit Trail

Business audit trail mencatat aktivitas bisnis pada dokumen.

Ketentuan:

1. Setiap perubahan status menghasilkan audit trail.
2. Setiap keputusan verifikasi menghasilkan audit trail.
3. Setiap perubahan metadata penting menghasilkan audit trail.
4. Publish dan kegagalan publish menghasilkan audit trail.

### 30.3 Retensi Log

Kebijakan retensi log harus mengikuti aturan organisasi.

Rekomendasi:

1. Audit log dokumen disimpan selama masa retensi dokumen.
2. Application log teknis dapat dirotasi secara berkala.
3. Log keamanan disimpan lebih lama jika diperlukan.

---

## 31. Error Handling

### 31.1 Prinsip Error Handling

Prinsip error handling:

1. Error harus ditampilkan dalam bahasa yang dapat dipahami pengguna.
2. Detail teknis hanya dicatat pada log.
3. Sistem tidak boleh menampilkan stack trace kepada pengguna umum.
4. Operasi database penting harus menggunakan transaksi.
5. Jika satu langkah gagal, sistem harus mencegah status tidak konsisten.

### 31.2 Error Upload

Jika upload gagal:

1. Sistem menolak file.
2. Sistem menampilkan penyebab umum.
3. Sistem tidak membuat pengajuan valid.
4. Sistem menghapus temporary file jika ada.

### 31.3 Error Verifikasi

Jika verifikasi gagal disimpan:

1. Sistem tidak boleh mengubah status dokumen.
2. Sistem menampilkan pesan gagal.
3. Sistem mencatat error teknis.
4. Verifikator dapat mencoba kembali.

### 31.4 Error Generate PDF

Jika generate PDF gagal:

1. Sistem tidak boleh mengubah status menjadi PUBLISHED.
2. Sistem menyimpan informasi kegagalan.
3. Sistem menampilkan pesan kepada Admin/TU.
4. Sistem menyediakan opsi retry setelah masalah diperbaiki.

---

## 32. Skenario Operasional

### 32.1 Skenario Normal Pengajuan Sampai Publish

Langkah operasional:

1. Pemohon login.
2. Pemohon membuka menu pengajuan.
3. Pemohon mengunggah PDF draft.
4. Pemohon mengisi perihal, ringkasan, dan lampiran.
5. Sistem menyimpan dokumen dengan status DIAJUKAN.
6. Admin/TU membuka pengajuan masuk.
7. Admin/TU memeriksa PDF draft.
8. Admin/TU mengisi nomor surat, tanggal, sifat, dan penandatangan.
9. Admin/TU mengatur posisi nomor, tanggal, QR Code, dan TTE.
10. Admin/TU menentukan Verifikator Level 1, Level 2, dan Level 3.
11. Sistem mengubah status menjadi MENUNGGU_VERIFIKASI.
12. Verifikator Level 1 approve.
13. Sistem mengubah status menjadi DISETUJUI_LEVEL_1.
14. Verifikator Level 2 approve.
15. Sistem mengubah status menjadi DISETUJUI_LEVEL_2.
16. Verifikator Level 3 approve.
17. Sistem mengubah status menjadi DISETUJUI_LEVEL_3.
18. Sistem menetapkan dokumen menjadi SIAP_PUBLISH.
19. Admin/TU membuka halaman publish.
20. Sistem membuat QR Code.
21. Sistem menghasilkan PDF final.
22. Admin/TU memeriksa preview final.
23. Admin/TU melakukan publish.
24. Sistem menyimpan PDF final.
25. Sistem mengubah status menjadi PUBLISHED.
26. Sistem mencatat audit log.
27. Pengguna dapat memvalidasi dokumen melalui QR Code.

### 32.2 Skenario Revisi oleh Verifikator

Langkah operasional:

1. Dokumen berada pada status MENUNGGU_VERIFIKASI.
2. Verifikator membuka dokumen.
3. Verifikator menemukan kesalahan isi.
4. Verifikator memilih perlu revisi.
5. Verifikator mengisi catatan revisi.
6. Sistem mengubah status menjadi PERLU_REVISI.
7. Pemohon menerima notifikasi.
8. Pemohon memperbaiki dokumen di luar sistem.
9. Pemohon mengunggah PDF revisi.
10. Sistem menyimpan versi revisi.
11. Sistem mengubah status menjadi DIAJUKAN.
12. Admin/TU memproses ulang dokumen.

### 32.3 Skenario Penolakan

Langkah operasional:

1. Verifikator membuka dokumen.
2. Verifikator menentukan dokumen tidak layak diproses.
3. Verifikator memilih reject.
4. Verifikator mengisi alasan penolakan.
5. Sistem mengubah status menjadi DITOLAK.
6. Sistem menonaktifkan workflow aktif.
7. Pemohon menerima notifikasi penolakan.
8. Dokumen menjadi final dan tidak dapat dipublish.

### 32.4 Skenario Publish Gagal

Langkah operasional:

1. Dokumen berada pada status SIAP_PUBLISH.
2. Admin/TU menjalankan publish.
3. Sistem mencoba generate PDF final.
4. PDF engine gagal memproses file.
5. Sistem membatalkan proses publish.
6. Status dokumen tidak menjadi PUBLISHED.
7. Sistem mencatat error teknis.
8. Admin/TU menerima pesan kegagalan.
9. Admin/TU atau Super Admin memperbaiki penyebab.
10. Publish dapat dicoba kembali.

---

## 33. Non-Functional Requirements

### 33.1 Availability

Sistem harus tersedia selama jam operasional organisasi. Jika SIMAS digunakan sebagai sistem utama administrasi surat, downtime harus direncanakan dan dikomunikasikan.

### 33.2 Performance

Kebutuhan performa:

1. Halaman daftar dokumen harus dapat dimuat secara cepat dengan pagination.
2. Preview PDF harus dioptimalkan agar tidak membebani server secara berlebihan.
3. Generate PDF final harus diproses dengan batas waktu yang wajar.
4. File besar harus ditangani dengan batas ukuran yang jelas.

### 33.3 Scalability

Sistem harus dapat berkembang untuk:

1. Penambahan jenis dokumen.
2. Penambahan level verifikasi.
3. Penambahan role baru.
4. Integrasi dengan layanan tanda tangan elektronik.
5. Integrasi dengan sistem arsip.

### 33.4 Maintainability

Kebutuhan maintainability:

1. Kode Laravel mengikuti struktur controller, service, model, policy, dan request validation.
2. Logika workflow sebaiknya ditempatkan pada service khusus.
3. Generate PDF sebaiknya ditempatkan pada service khusus.
4. Status dokumen sebaiknya didefinisikan dalam enum atau constant.
5. Validasi form sebaiknya menggunakan Form Request.

### 33.5 Security

Kebutuhan keamanan:

1. Semua endpoint sensitif harus dilindungi autentikasi.
2. Semua aksi dokumen harus divalidasi otorisasi.
3. File PDF tidak boleh diakses tanpa hak.
4. Input pengguna harus divalidasi.
5. Audit log harus tersedia untuk aktivitas penting.

### 33.6 Traceability

Kebutuhan traceability:

1. Setiap dokumen memiliki riwayat status.
2. Setiap keputusan approval dapat dilacak.
3. Setiap perubahan metadata penting tercatat.
4. Setiap file revisi dapat ditelusuri.

---

## 34. Rekomendasi Struktur Aplikasi Laravel

### 34.1 Struktur Domain

Struktur yang disarankan:

```text
app/
  Enums/
    DocumentStatus.php
    VerificationStatus.php
  Http/
    Controllers/
      DocumentController.php
      AdminDocumentController.php
      VerificationController.php
      PublishDocumentController.php
      ValidationController.php
    Requests/
      StoreDocumentRequest.php
      UpdateDocumentMetadataRequest.php
      StoreDocumentPositionRequest.php
      VerificationDecisionRequest.php
  Models/
    User.php
    Document.php
    DocumentMetadata.php
    DocumentPosition.php
    Verification.php
    DocumentLog.php
  Policies/
    DocumentPolicy.php
  Services/
    DocumentWorkflowService.php
    PdfPositioningService.php
    PdfFinalizationService.php
    QrCodeService.php
    AuditLogService.php
```

### 34.2 Service Layer

Service layer disarankan untuk memisahkan logika bisnis dari controller.

Service utama:

1. DocumentWorkflowService untuk transisi status.
2. PdfPositioningService untuk konversi dan validasi koordinat.
3. PdfFinalizationService untuk generate PDF final.
4. QrCodeService untuk pembuatan QR Code.
5. AuditLogService untuk pencatatan log.

### 34.3 Policy Layer

Policy digunakan untuk menjawab pertanyaan otorisasi seperti:

1. Apakah user dapat melihat dokumen ini.
2. Apakah user dapat mengubah metadata dokumen ini.
3. Apakah user dapat memverifikasi dokumen ini.
4. Apakah user dapat mempublish dokumen ini.
5. Apakah user dapat melihat audit log dokumen ini.

### 34.4 Enum Status

Status dokumen disarankan didefinisikan secara terpusat.

Contoh status:

```text
DIAJUKAN
DIPROSES
MENUNGGU_VERIFIKASI
DISETUJUI_LEVEL_1
DISETUJUI_LEVEL_2
DISETUJUI_LEVEL_3
PERLU_REVISI
DITOLAK
SIAP_PUBLISH
PUBLISHED
GAGAL_PUBLISH
DIBATALKAN
```

### 34.5 Transaction Boundary

Operasi berikut harus menggunakan database transaction:

1. Submit pengajuan dan simpan metadata awal.
2. Kirim dokumen ke verifikasi.
3. Simpan keputusan verifikasi dan ubah status.
4. Upload revisi dan ubah status.
5. Generate final dan publish.

Tujuan penggunaan transaction:

1. Mencegah data parsial.
2. Menjaga konsistensi status.
3. Memastikan audit log tercatat bersama aksi utama.

---

## 35. Kesimpulan

SIMAS adalah sistem manajemen dokumen digital berbasis workflow PDF yang dirancang untuk menggantikan proses administrasi surat manual menjadi proses digital yang terstruktur, terkontrol, dan dapat diaudit. Sistem ini menempatkan PDF sebagai format utama sejak tahap pengajuan, sehingga Admin/TU tidak lagi melakukan konversi dokumen dari DOCX ke PDF.

Dengan dukungan multi-level approval, dynamic PDF element positioning, role-based access control, QR Code validation, audit log, dan finalisasi PDF otomatis, SIMAS menyediakan fondasi sistem administrasi surat yang sesuai untuk kebutuhan organisasi modern. Setiap tahapan proses, mulai dari pengajuan, review, verifikasi, revisi, penolakan, finalisasi, hingga publish, memiliki status yang jelas dan jejak audit yang dapat ditelusuri.

Perubahan utama dari proses lama ke proses baru adalah penghapusan tanggung jawab konversi manual oleh Admin/TU. Pemohon wajib mengunggah PDF sejak awal. Admin/TU berfokus pada review administratif, pengisian metadata, pengaturan posisi elemen PDF, dan pengelolaan workflow approval. Perubahan ini meningkatkan efisiensi, mengurangi risiko kesalahan format, memperjelas tanggung jawab tiap aktor, dan memperkuat akuntabilitas proses penerbitan dokumen.

Dengan rancangan ini, SIMAS dapat dikembangkan sebagai enterprise document workflow system yang tidak hanya memproses dokumen, tetapi juga menjaga validitas, integritas, keamanan, dan keterlacakan seluruh siklus hidup dokumen administrasi.
