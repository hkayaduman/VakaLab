# Yayın öncesi durum — 2026-10-23

Güncel yerel uygulama: 0.2.18 beş bölümlü v2 değerlendirme, dört insan kararı ve tek güncel öğrenci raporu. Yerel HEAD `9098ff7`; uzak dal bu incelemede sorgulanmadı. AUDIT-01 düzeltildi, AUDIT-02 düzeltmesi yeniden doğrulandı. PHP güvenlik güncellemesi ve yerel hazırlık henüz commit/push edilmedi. Güncel otomatik kanıtlar TEST_RESULTS.md ve `test-evidence/local-readiness-2026-10-23/` içinde.

Güncel rapor yolu: Genel durum → vaka/öğrenci → Çalışmayı aç → Öğrenci raporunu hazırla / Word / Excel / Öğrenciye göster. Öğrenciye göster tek işlemle onay ve yayın yapar; vaka veri toplama kapanış/kişisel ek süre kapısı geçerlidir. Yeni analiz, başarılı yeniden hesaplama veya karar değişikliği görünürlüğü kapatır; güncel inceleme tamamlanınca aynı rapor hazırlanıp yeniden gösterilir. Önceki v1 rapor/snapshot açıklamaları aşağıda tarihsel akışı anlatır.

Yerel kontroller dışındaki kalanlar: ayrı test hostinginde kurulum/HTTPS/worker/izinler/yedekten dönüş; gerçek sağlayıcı ve AI çıktısı kalitesi; gerçek rubrik/kod kitabı/kurum politikaları; hedef cihaz ve gerçek kesinti kurtarması; hedef Disco uygulamasında import. Gerekli bilgi/erişim tablosu aşağıdadır. Yeni manuel batch başlatılmayacak; otomatik yapılamayan kritik kabul kısa tutulacak.

## 0.2.10 ve önceki yayın hazırlığı — tarihsel kayıt

0.2.10: Sade öğrenci raporu, okunabilir süreç ekranı, sıralı/etiketli AI eylemleri ve ayrı AI hazırlık beklemesi eklendi. Son kapsam: 82 navigation, 115 Chrome, 38 export, 28 etiket/süreç ve 75 SQLite kontrolü; diğer geçmiş testler tarihsel kanıttır. Gerçek AI/hosting/Disco import kabulü açık.

Önceki aşama 0.2.2: Öğrenci ana sayfasındaki Katılım bilgilerim düğmesi/formu kaldırıldı. Mevcut bilgiler korundu;69Chrome kontrolü tekrar geçti. Önceki kapsamlı test kayıtları tarihçedir.

Araştırmacı paneli kullanıcının son isteğine göre sadeleştirildi. **Her araştırmacının ayrı çalışma alanı vardır; ortak vaka/araştırmacı üyeliği yoktur.** 0.2.10 değişiklikleri kullanıcı isteğiyle GitHub main dalına 5f8a34964654bd3cbc94dc469447328ee7aab243 commit'iyle gönderildi. Teslim kaydı CONTEXT.md içindedir; gerçek hosting yayını yapılmadı. Üretim kabulü verilmedi.

## 0.2.1 kullanım güncellemesi

- Genel durum: Vaka ve öğrenci seç → Vaka / Öğrenci ara / Çalışma durumu → Çalışmayı aç. Eski geniş matris kaldırıldı; dosyadan dönüşte seçim korunur.
- Vaka kartı: Atanan öğrencileri göster düğmesi ve ilgili vakanın atama yönetimine geçiş.
- Rubrikler ve etiketler → + Rubrik: ad/yönerge, boyut adı/min/max/ağırlık, düzey adı/puan/açıklaması. JSON gerekmez; sistem üretir. Eski sürüm korunur.
- İşlem geçmişi: okunaklı Türkçe kayıtlar, arama,20şerDaha fazla göster ve İşlem geçmişini JSON indir.
- Ek süre yeniden test edildi: seçili öğrenci devam eder, seçilmeyen kapalı kalır; süre iptalinde salt okunur döner. 119HTTP/69genelChrome/50hedefliUI/69SQLite/14rubrik bu tur geçti. Mevcut DB'ye yazılmadı; yeni manuel test batch'i gerekmiyor.

## Önceki tamamlananlar

- **Kullanıcılar → + Kullanıcı ekle:** Hesap türü Öğrenci veya Araştırmacı. Yeni araştırmacı kendi boş alanıyla başlar. Kullanıcı adı/öğrenci no genel sistemde benzersizdir. Eklenen araştırmacının hesabını oluşturan kişi hesap erişimini yönetebilir; bu, diğer araştırmacının veri ekranlarına yetki vermez.
- Formda temel hesap alanları vardır; Program/Sınıf/Özel not Ek bilgiler altında isteğe bağlıdır. Onam/GenAI deneyimi/araçlar/çekilme alanları kaldırıldı. Eski metadata kayıtları korunur.
- Rutin kullanıcı, vaka, tanım, atama ve veri toplama işlemlerinde gerekçe kutusu yoktur; aktör/zaman/işlem otomatik kaydedilir. Araştırmacı değerlendirme/etiket inceleme kararları, iç not, analiz dışı ve teknik müdahale gerekçeli kalır.
- **Vakalar ve atamalar → Toplu atama / Ek süre:** Öğrenci checkboxları ve Tümünü seç. Seçilenlere ata / atamayı kaldır / kapalı vakada ek süre ver / ek süreyi kapat. Toplu işlem bir hedefte hata verirse tamamı geri alınır.
- Her vaka kartında **Veri toplamayı kapat / Veri toplamayı aç**. Kapalı vaka okunabilir; yeni başlangıç, kayıt, AI ve teslim engellenir. Seçili öğrenciye ek süre bu engeli o öğrenci için kaldırır; başlangıç ve teslim kilidi açılmaz. Başka vakalar etkilenmez. Vaka yeniden açılır/kapanırken eski kişisel ek süreler sıfırlanır.
- **Rubrikler ve etiketler → Sil:** Tanımın sürüm zinciri yeni listelerden ve seçimlerden çıkarılır. Önceki etiket incelemeleri, analiz ve rapor başvuruları korunur. Bu fiziksel geçmiş silme değildir.
- **Veri indirme → Hangi vaka?:** Tüm vakalar veya tek vaka. Excel/JSON seçilen kapsamdaki vaka metinleri/sürümler, katılımcılar/atamalar, taslaklar/snapshotlar, olaylar/sohbet, AI denemeleri, analiz/kararlar, rapor/iç not ve ilgili tanımları taşır. Test/deneme/analiz dışı veriler durumlarıyla dahildir; parola/hash/API anahtarı yoktur.
- **Disco CSV indir:** Kodlanmış öğrenci-vaka süreç kimliği, etkinlik, UTC başlangıç/bitiş, Resource, sıra ve kalite/kaynak sütunları. Heartbeat ve diğer teknik gürültü bu ayrı günlüğe alınmaz; tam olaylar Excel/JSON'da korunur. AI denemeleri ayrı Resource ile yer alır; okuma/düşünme süresi iddiası yoktur.
- Yayın: yalnız ilgili vaka ve rapor sahibinin ek süresi kapalıyken, onaylı rapor ayrıca yayımlanır. Başka vakaların kapanması gerekmez. Vaka/kişisel ek süre tekrar açılınca yayımlanan rapor erişimi gizlenir; kapanınca aynı rapor tekrar erişilir. Eski rapor gövdesi değişmez.

## 0.2.0 tarihsel doğrulamalar (bu tur yeniden çalıştırılmayanlar dahil)

- 119 yeni/hedefli HTTP kontrolü, 42 iki-worker HTTP kontrolü, 69 genel Chrome + 24 yeni Chrome kontrolü, 69 SQLite, 35 export kontrolü geçti.
- MariaDB 11.4.12 üzerinde güncel 69 kontrol geçti; ayrı restore DB'sinde 8 tablo sayısı eşleşti. İlk test istemcisi/dump TLS bağlantı hataları başarılı sayılmadı; yalnız ayrı loopback fixture istemcilerine --skip-ssl eklenince koşu tamamlandı. Bu hosting TLS doğrulaması değildir.
- Yerel SQLite geçişinde önce özel yedek alındı ve tekrar açılarak doğrulandı. **22 tablonun eski tüm sütunları ve satırları hash ile eşleşti**; ikinci migration çalıştırması değişiklik yapmadı. Sadece sahiplik/yeni kontrol alanları eklendi; mevcut vakalarda kapanış/yayın/seed/reset yapılmadı. Mevcut kayıtlar mevcut ilk araştırmacıya bağlandı.
- Kanıtlar TEST_RESULTS.md ve test-evidence/researcher-0.2.0-* içinde. Otomatik sonuçlar yeni kullanıcı manuel kabulü değildir.

## Disco içe aktarma

İlk içe aktarmada **Case ID → Case**, **Activity → Activity**, **Start Timestamp / Complete Timestamp → Timestamp**, **Resource → Resource** eşleştirilir. Zaman biçimi `yyyy-MM-dd HH:mm:ss.SSS`, UTC. Aynı zamanlı kayıtlarda sıra korunur; özgün zamanlar raw sütunlarda kalır. Client gözlemi/received_at fallback ve kalite ayrı sütunlardadır. Eksik bitişi olan çalışan AI denemesine sahte süre üretilmez; tam kaydı JSON/Excel'dedir.

Biçim [Fluxicon'un resmi içe aktarma yapısına](https://www.fluxicon.com/book/read/import/) göre hazırlanmıştır. CSV üretimi/kolonları/zamanları otomatik doğrulandı; **Disco masaüstü uygulamasında gerçek import henüz denenmedi.**

## Yayın öncesi zorunlu kalanlar

| Konu | Gereken bilgi/erişim | Sağlanınca yapılacak doğrulama |
|---|---|---|
| Hosting | Sağlayıcı/panel; ayrı test alan adı ve kök/alt klasör; SSH/SFTP/panel erişim yöntemi; public kökü ayarı; PHP/DB sürümü, eklentiler, bellek/istek/proxy süreleri ve worker sınırı | Ayrı test DB'sinde kurulum/migration, HTTPS/session, özel dizinlerin korunması, iki eşzamanlı istek, Türkçe export ve hedef hacim |
| Veritabanı/yedek | Ayrı test DB ve sınırlı hesap; yedek/restore yöntemi; kurumun sıklık/saklama kararı | Hosting yedeğini ayrı DB'ye restore, ilişki/sürüm/rapor karşılaştırması |
| AI | Sağlayıcı, tam HTTPS endpoint, model kimliği; Chat Completions uyumluluğu, JSON yanıt desteği; kota/bütçe ve timeout kararı | Sentetik sohbet/bağımsız analiz, model/girdi kaydı, hata/timeout/kısmi yanıt, tekrar ve AI sürerken autosave |
| Güvenli yapılandırma | DB parolası/API anahtarı sunucudaki özel config/local.php içine güvenli yöntemle girilmeli | Tarayıcıya/hataya/exporta sır sızmaması; Git dışında kalması. Sırları chat veya ekran görüntüsüne koymayın. |
| Kurum | Gerçek rubrik metni, boyut/düzey/puan/ağırlık ve sürüm; bilgilendirme/onam/saklama/çekilme kararları | Örnek değerlendirmelerin kurumca kabulü. Formdaki alanların kaldırılması kurum politikasını belirlemez; TEST RUBRİĞİ/mock gerçek değerlendirme değildir. |
| Gerçek kullanım | Fiziksel hedef cihaz/tarayıcı, yardımcı teknoloji, güvenli sentetik kesinti oturumu | Mobil klavye/erişilebilirlik, gerçek bağlantı/sekme kaybı sonrası kurtarma; hedef araçta Disco import |

Yeni manuel test batch'i başlatılmayacak. Ortam sağlandığında otomatik kontroller önce yürütülecek; yalnız otomatik doğrulanamayan az sayıdaki kritik kabul adımı kısa ve açık istenecek.

## Daha sonra yapılabilecekler

Somut ihtiyaç halinde ek performans ölçümü ve küçük kullanılabilirlik iyileştirmeleri. Ortak araştırmacı erişimi istenmiyor. Yeni özellikler üretim kabulü için gereksiz yere eklenmeyecek.

## Ayrı sentetik kapanış/yayın senaryosu

Normal pencere araştırmacı, gizli pencere öğrenci; yalnız ayrı test kurulumu. Araştırmacı Vakalar ve atamalar'da tek vakanın Veri toplamayı kapat düğmesini kullanır. Öğrenci yenileyince salt okunur olmalı; farklı vaka açık kalmalı. Toplu atama / Ek süre ile yalnız seçili öğrenciye ek süre verilince onun devam eden çalışması düzenlenebilmeli; diğerleri kapalı kalmalı. Ek süre kaldırılınca tekrar salt okunur olmalı. Rapor onaylı olsa da kapanış tek başına yayımlamaz; Genel durum → Vaka ve öğrenci seç → Çalışmayı aç ile öğrenci-vaka dosyası → Rapor geçmişi · Onay ve yayın → Ayrıca öğrenciye yayımla gerekir. Vaka/kişisel ek süre tekrar açılınca rapor gizlenmeli, kapanınca aynı sürüm dönmeli. Teslim edilmiş yanıtlar ve eski raporlar değişmemeli. Bu iş kuralları otomatik suite'te doğrulandı; mevcut çalışma DB'sinde denenmedi.

## 0.2.1 yerel tam tekrar koşusu — 2026-10-20

Yerelde çalıştırılabilir12otomatiktestgrubu güncel kodda tekrar geçti; toplam547bildirilen kontrol örtüşen kapsamlar içerir. MariaDBayrırestore8tablosayısıeşit. Önceki0.2.0sonuçlarını taşımak yerine test-evidence/audit-0.2.1-*yeni kanıtları üretildi. Bin/upgrade-sqlite.php yeniden çalıştırılmadı; önceki migration kanıtı tarihçedir. Gerçek hosting/AI/kurum/fizikselcihaz/Word/gerçekkesinti/hacim/Discoimport kabulü hâlâ tamamlanmadı. Yeni manuel batch yok.
