# Pemetaan Hosting & Domain Proyek Tony (PDV Consulting)

Dokumen ini berisi informasi pemetaan server hosting dan domain untuk aplikasi-aplikasi yang dikelola Tony, sebagai konteks permanen AI assistant.

---

## 1. Aplikasi di Hosting Server `pdvconsulting.id` (cPanel: `pdvh7299`)
Server hosting ini berbasis cPanel Apache + PHP + MySQL (tanpa fitur Node.js runtime):

1. **Aplikasi Coaching & Mentoring Hub**
   - **Path Lokal:** `C:\laragon\www\coaching`
   - **Framework Backend:** Laravel 11 (PHP 8.2+)
   - **Frontend:** React (Vite SPA) ter-bundle di `public/`
   - **Domain / Subdomain:** `https://cm.pdvconsulting.id`
   - **Folder Hosting:** `public_html/cm.pdvconsulting.id`
   - **Database MySQL:** `pdvh7299_coachingapp`
   - **User Database:** `pdvh7299_coachingapp`
   - **FTP Account:** `coaching@cm.pdvconsulting.id` (Host: `ftp.pdvconsulting.id`, Port: 21)

2. **Aplikasi Webinar**
   - **Path Lokal:** `C:\laragon\www\webinar`
   - **Server / Hosting:** `pdvconsulting.id`
   - **Database MySQL Terkait:** `pdvh7299_webinar`

3. **Aplikasi Simentas**
   - **Path Lokal:** `C:\laragon\www\simentas`
   - **Server / Hosting:** `pdvconsulting.id`
   - **Database MySQL Terkait:** `simentas_db`

---

## 2. Aplikasi LMS
1. **LMS ODH (Organization Digital Hub)**
   - **Path Lokal:** `C:\laragon\www\people-org`
   - **Domain:** `https://peopledigitalinnovation.com`
   - **FTP Server:** `ftp.peopledigitalinnovation.com` (Port: 21)
   - **FTP User:** `odh@peopledigitalinnovation.com`
   - **FTP Password:** `Alhamdulill4h1230!`
   - **Database MySQL:** `peop3793_odh`
   - **Framework:** Laravel 11 + Filament v3

2. **LMS One Learning**
   - **Path Lokal:** `C:\laragon\www\lms`
   - **Keterangan:** LMS One Learning.

*Catatan:* Kedua aplikasi LMS ini terpisah dan memiliki basis kode/folder masing-masing. Jangan sampai tertukar foldernya.
