# 0.3.1 · Soru bazında tek rapor

Tek rapor içindeki altı soru artık ayrı öğrenci yanıtı, geri bildirim, rubrik ölçütleri ve soru puanlarıyla değerlendirilir. AI bütün soruları tek istekte alır; manuel yol AI çağırmaz. Öğrenci raporu ve indirmeler aynı eşleşmeleri ve profil karşılaştırmasını içerir.

Geçen kontroller:

- SQLite 63 ve ayrı MariaDB 11.4.12 üzerinde aynı 63 backend kontrolü.
- Gerçek Ai adapter sözleşmesi 10 (mock, harici bağlantı yok).
- Yerel PHP + Chrome 36: soru eşleşmeleri, manuel kayıt, soru bazında düzenleme, öğrenci önizlemesi/profil, kaybolan POST yanıtından sonra otomatik görünüm, tek çağrı, yayın/onay, Word/PDF/XLSX ve 1440/768/390 piksel genişlikler.
- Mevcut temel PHP 87, v2 uyumluluğu 96 ve soru-yanıt çelişkisi 26 kontrol.
- MariaDB çalıştırıcısındaki mevcut bağlantı boşta kalma, temel, yeni deneme, Disco ve 8 tablo sayısıyla ayrı yedek geri yükleme kontrolleri.
- PHP/JS sözdizimi ve git diff boşluk denetimi.

Word/Excel testleri yalnız dosya boyutunu değil, her sorunun tam metni, öğrenci yanıtı ve ilgili geri bildirimin birlikte saklanmasını da doğrular. Her AI ölçüt kanıtı sorunun kendi yanıtından gelir; başka sorudan kanıt veya dolu yanıta yok denmesi reddedilir. Bu kontroller AI puanının bilimsel doğruluğunu garanti etmez; araştırmacı düzenleme ve onayı korunur.

`editor-390.png` sentetik bir sorunun tam kartını gösterir. Paket kaynak dosya SHA-256 özetleri `package-manifest.json` içindedir. Paket config, vendor, SQL veya öğrenci verisi içermez. Canlıya yükleme ve gerçek sağlayıcı kabulü bu çalışma sırasında yapılmadı.
