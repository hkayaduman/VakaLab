# Sekme menüsü sonrası tam yerel regresyon — 28 Ekim 2026

Tam koşu: **135/135 başarılı**, başarısız paket yok. 46 işlevsel paket ve 89 sözdizimi/diff kontrolü. Ek duyarlı görünüm testi 28/28 geçti. Bu koşuda ürün veya test kodu değiştirilmedi.

## Sekmeler ve süreler

- Yedi bölüm arasında gerçek Chrome tıklamaları: 39/39. Bağımsız performance.now ölçümü toplam 3,0284 sn; kayıt toplamı 3,029 sn. Toplam fark 0,6 ms; tek aralıkta en büyük fark 5,3 ms (tolerans 150 ms).
- navigation.php: 82/82; presence.php: 60/60. Sıra, hedef, kilitli bölüm, eksik kapanış, geri giden saat ve çakışan AI bekleme aralıkları kontrol edildi.
- presence-browser.cjs: 24/24. Başka sekmeye geçme/dönme ve küçültülmüş pencere süresi bölüm içi süreye eklenmedi.
- Karma yolculuk: 27/27. 15,006 sn = 5,579 sn bölüm içi + 6,500 sn odak dışı + 2,927 sn vaka dışı. Bağımsız zaman farkları, UI/API ve indirilen 20 satırlı CSV eşleşti; AI beklemesi öğrenci toplamına ikinci kez eklenmedi.
- Ana tarayıcı: 115/115; zorunlu alan/teslim: 50/50. Taslak korunması, tekrar giriş, kilitler, geçerli/geçersiz teslim ve hata alanına odaklanma başarılı.

## İndirilen veriler

- export-roundtrip.php: 267/267. Ham ve kodlanmış 25 tablonun CSV/JSON/Excel çıktıları yeniden okunarak kaynak veriyle karşılaştırıldı.
- Excel: 4.857 veri hücresi, başlıklar ve satır sayıları eşleşti. CSV hücreleri ve JSON değerleri eşleşti.
- Türkçe karakter, tırnak, ayraç, satır sonu, sıfır değer ve CSV formül kaçışı kontrolleri geçti.
- Ham/kodlanmış çıktı ayrımı, özel alanların gizlenmesi ve export sırasında kaynak tabloların korunması geçti.
- HTTP indirme/yetki kontrolleri, Word/PDF/Excel rapor içerikleri ve soru/yanıt/geri bildirim eşleşmeleri geçti.
- MariaDB: ayrı test veritabanlarında iş akışı, denemeler, Disco, rapor ve inceleme testleri geçti. Ayrı veritabanına yedekten geri yüklemede sekiz tablonun satır sayısı eşleşti.

## Görünüm ve kaynaklar

320/390/768/1440 pikselde yedi bölüm, toplam 28 görünüm: tüm sekmeler sıralı ve görünür; yatay taşma yok; yapışkan menü ve kontrol boyutları başarılı. Araştırmacı görünümünün karşılaştırılan stilleri değişmedi. Kaynak manifestindeki 88 dosya ve öğrenci CSS dosyası koşu sonunda eşleşti (source-verification.json).

## Kapsam

Testler ayrı sentetik SQLite/MariaDB verileri ve mock AI ile çalıştı. Altı isteğe bağlı canlı sağlayıcı betiği yalnız sözdizimi kontrolünden geçti; gerçek sağlayıcı, canlı hosting, fiziksel telefon ve Disco uygulamasına gerçek import bu koşuya dahil değildir. Yerel başarı tüm olası veriler için garanti değildir.

Çalışma veritabanı dosyasının gözlenen hash değeri koşu sırasında değişti; bunun nedenini hash ölçümü belirlemez. Bu nedenle çalışma dosyasının değişmediği iddia edilmez. Gözlemler working-file-observations.json içinde saklandı; test paketleri kendi ayrı veritabanlarını kullanır.

Bütün paket günlükleri ve results.json bu dizindedir. Üst klasörün 24 Ekim adı mevcut çalıştırıcının sabit konumudur; bu koşu 28 Ekim 2026 tarihindedir. Commit/push/dağıtım yapılmadı.
