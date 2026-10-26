# Tek rapor 0.3.0 doğrulaması · 26 Ekim 2026

Yeni akış için SQLite 40, gerçek Ai adapter sözleşmesi 10, yerel Chrome 26 kontrol geçti. Ayrı MariaDB 11.4.12 üzerinde aynı 40 backend kontrolü geçti. Tek AI çağrısı sağlayıcı sayacıyla, manuel yol sıfır çağrıyla doğrulandı. Gerçek sağlayıcı çağrısı yapılmadı.

Tarayıcı denemesi başarılı POST yanıtını bilerek düşürür: salt okuma gözlemcisi biten raporu F5 olmadan gösterir ve ikinci üretim isteği göndermez. Manuel rapor, sıfır puan, AI taslağının elle düzenlenmesi, ayrı onay/yayın, gizleme, başarısız üretimde eski kaydın korunması ve Word/PDF/XLSX indirmeleri geçti. 1440/768/390 piksel genişliklerde sayfa taşması yok; JavaScript hatası yok. `editor-390.png` yalnız sentetik test verisi içerir; ekran görüntüsü hata denemesinden sonraki bildirimini de içerir.

Ek regresyon: temel PHP 87, v2 96, eski soru-yanıt sözleşmesi 26, HTTP/oturum/eşzamanlılık 42, gözlemci 5 senaryo grubu, analiz ilerleme/sıralama 27 kontrol geçti. PHP/JS sözdizimi ve git diff boşluk denetimi geçti. MariaDB çalıştırıcısında ayrıca bağlantı boşta kalma 6, temel 87, yeni deneme 38 ve Disco 35 kontrol; ayrı geri yüklenen veritabanında 8 tablo sayısı doğrulandı.

`package-manifest.json` paket SHA-256 ve kaynak dosya özetlerini içerir. Paket config, vendor, veritabanı, API anahtarı veya öğrenci verisi içermez. Mevcut 001–003 şemasına SQL değişikliği yapılmaz. Hosting'e yükleme ve yeni sözleşmeyle gerçek sağlayıcı kabulü henüz yapılmadı.

Kod kontrol komutları: `php tests/holistic-review.php`, `php tests/holistic-contract.php`, `node tests/holistic-browser.cjs`, `python tests/mysql_checks.py`. Kullanılan çalıştırıcılar yerel `.tools/php`, paketli Node/Playwright/Chrome ve ayrı taşınabilir MariaDB'dir. Çalışma veritabanı veya özel config değiştirilmedi.
