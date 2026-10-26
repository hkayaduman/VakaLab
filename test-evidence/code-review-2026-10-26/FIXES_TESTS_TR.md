# İnceleme düzeltmeleri ve kapsamlı test — 26 Ekim 2026

Beş bulgu düzeltildi. Tam koşudaki **45 test paketinin tamamı**, ayrıca sözdizimi ve diff denetimleri geçti: **133/133 adım, sıfır başarısızlık**. Testler ayrı sentetik SQLite/MariaDB veritabanlarında çalıştı; canlı veriye veya gerçek AI sağlayıcısına işlem yapılmadı.

## Düzeltmeler

| Bulgu | Uygulanan düzeltme | Doğrulama |
|---|---|---|
| Özel araştırmacı gerekçesinin öğrenciye gitmesi | `App::studentApplication()` başlangıç, kayıt, kesinleştirme, teslim ve tekrar yanıtlarında aynı izin listesini uygular. | Öğrenciye gerekçe/analiz dışı alanları gitmez; araştırmacı erişimi korunur. |
| Ağ hatasından sonra değiştirilen mesajın gönderilmeden silinmesi | Bekleyen anahtar metinle eşleştirilir; değişen mesaj yeni anahtar alır. Sunucu aynı anahtar ve farklı metni 409 ile reddeder. | Teslimden önce ve sonra bağlantı kesilmesi, metnin değişmesi ve aynı metnin tekrarında mesaj sayıları kontrol edildi. |
| Eski yeniden deneme yanıtının başka vakaya yazılması | Yeniden deneme hedef çalışmayı yakalar; dönüş ve hata yollarında aynı çalışma açık değilse arayüz durumunu değiştirmez. | A vakası yanıtı bekletilip B açıldı; B'nin mesajları ve bekleme durumu korunurken A yanıtı kendi kaydında kaldı. |
| Eski v2 raporun yeniden yayını | `show`, `approve`, `publish` ortak güncel rapor/değerlendirme/karar denetimini kullanır. | Eski kararlı veya daha yeni değerlendirmesi olan rapor üç yoldan da reddedildi; güncel raporun eski API uyumluluğu korundu. |
| Kodlanmış çıktıda tanım metninin kalması | Etiket üretim geçmişinde yalnız gerekli yapısal alanlar bırakılır. | İç içe tanım metinleri çıkarıldı; ham geçmiş, etiket seçimleri ve araştırmacı seçim önceliği korundu. |

Yeni testler: `tests/review-regressions.php` 28 kontrol (SQLite ve MariaDB), `tests/chat-recovery-browser.cjs` 12 kontrol, `tests/navigation-timing-browser.cjs` 39 kontrol. Mevcut geniş tarayıcı testindeki eski v2 ekran beklentileri mevcut v3 ekranına uyarlandı; bu paket artık 115 kontrolü tamamlıyor. Korunan v2 alan sözleşmesi kendi testlerinde 96 kontrolle doğrulandı. İstemci betiğinin önbellek sürümü 0.3.2 oldu. Yeni migration gerekmez.

## Sekme geçişleri ve süreler

Yedi bölümün tamamı gerçek Chrome tıklamalarıyla dolaşıldı: Vaka → Vaka profili → Başlangıç değerlendirmesi → AI sohbeti → Nihai değerlendirme → Yansıtma → Son teslim → Vaka. Her geçişin hedefi ve tek bir kapanan ziyaret üretmesi kontrol edildi. Süreler uygulamanın zaman hesabından bağımsız, testin tıklama yakalayıcısındaki `performance.now()` farkıyla karşılaştırıldı.

Bu tam koşuda yedi aralığın bağımsız toplamı **3,0636 saniye**, kaydedilen toplamı **3,065 saniye** idi; toplam fark **1,4 ms**, tek aralıktaki en büyük fark **8,7 ms** oldu. Test toleransı 150 ms'dir. Ekrandaki bölüm toplamları API'deki hassas sürelerin beklenen yuvarlanmış değerleriyle eşleşti. Bu test sürelerinin kısa olması nedeniyle uzun aralıkların davranışı ayrıca deterministik alan testlerinde denetlenir.

Mevcut süre testleri de baştan çalıştırıldı:

- `navigation.php`: 82 kontrol; yedi bölüm, tekrar ziyaretleri, kilitli bölümler, milisaniyeli sınırlar, bozuk/geri giden saat, eksik kapanış, AI soruları ve 40 saniyelik eşzamanlı AI bekleme aralığı.
- `presence.php`: 60 kontrol; uygulama içi/dışı aralıkların ayrılması ve ölçülemeyen kayıtların korunması.
- `presence-browser.cjs`: 24 kontrol; bağımsız DOM odağı gözlemleri, gerçek tarayıcı sekmesi değişimi, geri dönüş ve pencerenin küçültülmesi. Uygulama dışı süre bölüm süresine eklenmedi.
- `journey-browser.cjs`: 27 kontrol; iki vaka arasındaki gidiş/dönüş, iki ayrı vaka dışı aralık, AI yanıtı beklerken uygulamadan ayrılma, teslim iptali/başarısı, teslimden sonra takibin durması, sürelerde boşluk/çakışma olmaması, gerçek CSV indirmesi ve UI/API tutarlılığı.

Karma yolculukta **15,651 saniye = 5,745 saniye bölüm içi + 6,915 saniye uygulama odağı dışında + 2,991 saniye vaka dışında** olarak doğrulandı. AI bekleme çizgisi ayrı kaldı; öğrenci sürelerine tekrar eklenmedi. CSV'deki sıra, önceki/sonraki etkinlikler ve her satırın başlangıç/bitiş/süre eşleşmesi doğrulandı.

## Kanıtlar

- Tam koşu: `../disco-attempts-2026-10-24/20261026T195923Z/results.json` ve yanındaki paket günlükleri.
- Aynı klasördeki `source-sha256.json`, test edilen kaynakların parmak izlerini içerir.
- `navigation-summary.json`: bağımsız saat karşılaştırmasının yedi ayrı satırı.
- `journey-summary.json`: karma yolculuğun hassas toplamları.
- MariaDB koşusu temel iş akışı, tekrar denemeler, Disco, bütüncül rapor ve yeni inceleme düzeltmelerini ayrı veritabanlarında doğruladı; yedek ayrı veritabanına geri yüklendi ve sekiz tablonun satır sayıları eşleşti.

Canlı hosting, gerçek sağlayıcı yanıt kalitesi ve fiziksel mobil cihaz testi bu koşunun kapsamına girmez. Commit, push veya dağıtım yapılmadı.
