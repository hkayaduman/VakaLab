# Proje Devam Bağlamı

Bu dosya Codex'in yeni oturumda kaldığı yerden devam etmesi içindir. Sır, gerçek parola, API anahtarı veya gerçek öğrenci metni ekleme.

## Proje

Öğretmen adaylarının beş pedagojik vakada AI öncesi muhakemesi, GenAI etkileşimi, nihai muhakemesi ve yansıtmasını toplayan araştırma uygulaması. Yaklaşık 6–7 katılımcı; veri modeli bu sayıya sabitlenmez. HTML/CSS/JS/PHP/MySQL, shared hosting, Türkçe, responsive.

## Şu anda gerçekten yapılan

2026-10-20: 0.1.0 sentetik pilot uygulama kodlandı. PHP/MySQL iki panel, revision/kilitler, mock/compatible adapter, olaylar, sürümlü tanımlar, sıralı analiz, karar/rapor/yayın ve gerçek format indirme var. Yerel SQLite/MariaDB/HTTP/Chromium testleri çalıştırıldı; ayrıntı TEST_RESULTS.md. Gerçek AI/hosting/kurum rubriği/onam ve kullanıcı kabulü henüz yok.

## Okuma sırası

1. Bu dosya.
2. plan.md.
3. 01_OGRENCI_PANELI.md.
4. 02_ARASTIRMACI_PANELI.md.
5. 03_CODEX_GELISTIRME_PROMPTU.md.
6. 04_MANUEL_TEST_REHBERI.md.

## Son kabul edilmiş kararlar

- Vaka sürekli açık/sabit panel değil. Tek çalışma sayfasında bölüm gezinmesi: Vaka, Profil, Başlangıç, AI, Nihai, Yansıtma.
- Profil 0–10 dört alan; başlangıçta boş. Başlangıçla birlikte kilitlenir.
- Başlangıç üç alan; sunucuda kesinleşmeden AI açılmaz. Kesinleşmiş metin değişmez.
- AI vaka sürümünü bilir; profil/başlangıç/nihai/yansıtma/rubrik/etiket otomatik verilmez.
- AI bitirme düğmesi yok. Nihai/yansıtma ve AI arasında serbest geçiş.
- Yanıt sürerken ikinci AI mesajı veya kuyruk yok. Soru taslağı yazılabilir; diğer bölümler çalışır.
- Nihai/yansıtma tek teslimle kilitlenir; süreç salt okunur görünür kalır.
- Kalıcı silme yok. Arşiv/dondurma/analiz dışı ve tüm geçmiş sürümleri korunur.
- Öğrenci numarası panel kimliği; iç teknik kimlik değişmez. Kodlanmış export ayrı.
- Araştırmacı katılımcı/vaka/atama yönetir; vaka eklenince matris genişler.
- Olay, taslak ve süreler process mining için tutulur. Kopyalama kaynak metin/sürüm/aralıkla kaydedilir.
- Görünürlük/odak/etkileşimsizlik okuma veya düşünme diye yorumlanmaz.
- AI üretimi ve öğrenci etkinliği ayrı kulvar; süreler çift sayılmaz.
- Etiketler sade, çoklu ve araştırmacı tarafından değiştirilebilir/eklenebilir. AI önerisi ayrı korunur.
- Rubrik serbest metin + yapılandırılmış boyut/puan/ağırlık; gerçek rubrik henüz yok.
- Bir öğrenci-vaka, ayrı sıralı analiz adımları. Toplu AI analizi yok.
- Başlangıç ve nihai rubrik çağrıları bağımsız; diğer metin/puan gösterilmez.
- Sayısal fark/toplam uygulamada; AI yalnızca öneri ve kanıt sağlar.
- Kabul/düzenle/reddet/karar verilemedi. Araştırmacı kararı ham veri/AI önerisini ezmez.
- Rapor onayı ve yayını ayrı. Araştırma genelindeki beş vaka veri toplaması kapanmadan hiçbir öğrenciye puan/rapor yok.
- Öğrenci yalnızca onaylı yayımlanmış karar/puan/rapor görür; iç AI taslakları görünmez.
- Word/PDF rapor, gerçek XLSX/CSV süreç çıktısı ve JSON yapısal çıktı. Toplu indirme var.
- Kullanıcı test verisi ve adım adım manuel test rehberi istiyor; sonra dönütle düzeltilecek.

## Geçersiz kalan önceki fikirler

Sabit vaka yan paneli; AI etkileşimini bitir düğmesi; başlangıcın nihai yanında otomatik gösterimi; kalıcı silme; veri toplama sürerken öğrenci raporu; tüm öğrencileri tek AI çağrısıyla değerlendirme. Bunları geri getirme.

## Kritik teknik riskler

PHP session lock uzun AI isteğinde autosave'i engelleyebilir. Oturum kilidini güvenli serbest bırak; kısa DB transaction ve ayrı AI işlem kilidi kullan. Hosting streaming tamponlayabilir; sahte streaming zamanı üretme. Yeniden denemeler çift mesaj/yanıt oluşturmamalı. Offline olay sırası ve süre belirsizliği korunmalı. Yerel kurtarma ortak cihazda başka öğrenciye sızmamalı. Üretim verisi test seed/reset ile temizlenmemeli.

## Bilinmeyenler

Hosting sürüm/limitleri; AI sağlayıcısı/modeli; gerçek rubrik; onam/saklama metni; autosave/heartbeat/idle eşikleri; etiketleme tetikleme zamanı; çoklu araştırmacı; yedek düzeni. Teknik varsayımı belgele; kullanıcı kararı gibi gösterme.

## Oturum sonu güncelleme şablonu

- Son çalışma özeti:
- Gerçekten tamamlanan aşamalar ve dosyalar:
- Çalıştırılan testler/sonuçları:
- Çalıştırılamayan testler:
- Açık hatalar ve yeniden üretim:
- Sonraki ilk somut adım:
- Ortam/kurulum notları (sır içermez):
- Kullanıcıdan beklenen test dönütü:

İlk başlangıç kaydı tarihçedir. Güncel devam noktası aşağıdaki son oturum kaydıdır; uygulamayı yeniden üretme.

## Geliştirme başladı — P01
Şartnameler okundu; mimari ve güvenli public kökü seçildi. Yerel PHP/MySQL henüz yok. Gerçek AI yerine test adapter kullanılacak; sağlayıcı/hosting doğrulaması yapılmadı.

P02–P07 kodu eklendi; henüz doğrulanmadı. PHP yerel .tools içine indirildi; Windows VC runtime sorunu araştırılıyor. Config ve veriler public dışında tutuluyor.

P08–P13 ilk uygulaması yazıldı. PHP 8.3.35 taşınabilir runtime çalışıyor; Composer bağımlılıkları kuruluyor. Henüz MySQL yok. Tüm test durumları doğrulama bekliyor; sonraki iş seed ve otomatik kontroller.

Sentetik seed ve test hesap haritası kodlandı. tests/run.php 67 kontrol geçti (SQLite, PHP 8.3.35; vendor kurulu). HTTP/session concurrency çalışıyor; domain sonuçları gerçek sağlayıcı/hosting kanıtı değildir. Sonraki: tarayıcı, belge ve MySQL doğrulama.

## SON OTURUM KAYDI — 2026-10-20 · 0.1.0

- Son çalışma özeti: Çalışan PHP/MySQL sentetik pilot, iki responsive panel, schema 001 ve CLI, mock + compatible AI adapter, rapor/export ve tüm teslim belgeleri eklendi. Mevcut özgün şartnameler korundu.
- Dosyalar: app/{Auth,Db,Rules,App,Ai,Telemetry,Admin,Analysis,Export}.php; public/api.php/index.php/assets; bin/console.php; migrations/001.sql; composer.json/lock + yerel vendor; tests/*; README/INSTALL_SHARED_HOSTING/TEST_DATA/MANUAL_TESTS/TEST_RESULTS/DATA_DICTIONARY/EVENT_CATALOG/ARCHITECTURE/AI_INPUT_CONTRACTS/BUGS/CHANGELOG.
- Gerçek testler: PHP lint/JS syntax; CLI check; Composer validate/audit; SQLite 69 assertion; MariaDB 11.4.12/PDO mysql 69 assertion; iki yerel PHP worker ortak session ile 42 HTTP assertion; Google Chrome headless masaüstü/390px emülasyonda 18 kontrol. Yerel MariaDB yedeği ayrı restore DB'ye alındı, sekiz tablo sayısı eşit. PDF altı sayfa render/metin çıkarımıyla Türkçe incelendi; DOCX/XLSX gerçek paket, XLSX yeniden açıldı. Kanıt test-evidence/.
- Test komutları: php tests/run.php; python tests/http_checks.py; node tests/browser.cjs; MARIADB_HOME ayarlı python tests/mysql_checks.py. PHP_BIN/PLAYWRIGHT_PATH/CHROME_BIN gerektirebilir. Windows sandbox is_readable yanlış negatif verdiği için gerçek dosya testleri normal izinli yerel süreçte tekrar çalıştırıldı ve geçti. Sisteme global kurulum yapılmadı.
- Ortam: taşınabilir PHP .tools/php/php.exe (8.3.35); .tools/composer.phar; .tools/mariadb/mariadb-11.4.12-winx64. Bunları üretime yükleme. Config/local.php git dışı yerel SQLite, storage/vakalab_test.sqlite sentetik fixture. Parola yalnız TEST_DATA.md'de; buraya sır eklenmedi.
- Önizleme http://127.0.0.1:8088/ üzerinde PHP dev server. Çalışmıyorsa `.tools/php/php.exe -S 127.0.0.1:8088 -t public`. Windows dev server tek worker; AI gecikmesinde concurrency testini iki worker/gerçek hostingde yap.
- Açık kontroller: gerçek AI/API/model; gerçek hosting HTTPS/alt klasör/worker/izin/restore; kurum rubriği ve onam/saklama; fiziksel mobil klavye/ekran okuyucu; Word görsel açılışı; gerçek kesinti/crash ve ortak cihaz kurtarması; büyük veri. Hiçbiri geçti sayılmadı. BUGS.md OPEN-01…06.
- Ürün sınırları: non-stream fallback, ilk parça ölçülmez. sessionStorage kurtarma aynı sekme/hesap/vaka içindir, çıkış temizler, sekme kapanışı kayıp yaratabilir. localStorage yalnız advisory lease; asıl koruma sunucu revision/aktif AI kilidi. Aynı veri boşuna yeniden seed edilmez; seed geçmişi/parolayı sıfırlamaz.
- Sonraki ilk somut adım: Kullanıcı TEST-OGR-01 ile manuel başlangıç→AI→nihai/yansıtma→teslim akışını denesin; TEST-ARASTIRMACI matristen süreci incelesin. Yayın kapısını ana akıştan sonra ayrı test DB'de dene; global kapanış yazmaları kapatır.
- Kullanıcıdan beklenen dönüt: MANUAL_TESTS.md test ID, cihaz/tarayıcı, sentetik hesap/vaka, adımlar, beklenen/gerçekleşen ve hata kodu. Gerçek parola/anahtar/katılımcı metni isteme.
- P17 kullanıcı manuel testi ve P18 son kabul BEKLİYOR. Uygulama üretime hazır ilan edilmedi. Gerçek ortam erişimi verilmediği için hosting kurulumunu yapılmış gibi göstermeyin.

Son teslim kontrolü: yerel önizleme normal yerel PHP süreciyle 127.0.0.1:8088 üzerinde açık; araştırmacı girişi ve sentetik PDF/DOCX indirme HTTP 200 doğrulandı. Windows sandbox ağ kısıtı nedeniyle preview sunucusu normal izinli süreçte başlatıldı. Plan/sonuç belgeleri HTTP 42 son sayısına güncellendi.

## Güncel devam noktası: kullanıcıyla adım adım manuel test
Kullanıcı PC'yi yeniden başlattı; yerel sunucu başlatma yönergesi verildi. Ekran görüntüsü/çıktıyla her adımı birlikte değerlendirmeyi ve gerektiğinde düzeltmeyi istedi. MANUAL_TEST_RESULTS.md kaydı açıldı. Aktif adım M01: TEST-OGR-01 giriş ve Vakalarım ekranı. Kanıt bekleniyor; manuel pass yok. Sonraki adımı kanıtı gördükten sonra ver; bütün test listesini birden yükleme. Parola yalnız TEST_DATA.md içinde, ekran görüntüsünde parola isteme.

Manuel test güncellemesi: M01 görünür ana sayfa kontrolleri GEÇTİ. Kanıt kullanıcı Chrome/Windows 1920x1080 ekran görüntüsü; hesap TEST-OGR-01, mock banner, 4 atama sayacı. İlk vaka Başlangıç aşamasında/Devam et; onu sıfırlama. Aktif M02: ikinci vaka SENTETİK · Sınıfta dijital araç kullanımı → Başla; ilk çalışma ekranı screenshot bekleniyor. Sonraki kontroller altı bölüm ve AI/nihai/yansıtma kilitleri; henüz geçmedi.

Güncel manuel devam: M02 ilk vaka ekranı/görünür kilit göstergeleri GEÇTİ (de8c1b45 ekran görüntüsü). Aynı TEST-OGR-01 ikinci vaka sürüm 1; metin girişi yapılmadı. Aktif M03: 4. AI sohbeti · Kilitli tabına tıkla; kilit açıklaması ve mesaj kutusu/Gönder olmaması screenshot bekleniyor. Sunucu engeli bu screenshotla doğrulanmış sayılmayacak.

Güncel aktif manuel adım M04. M03 GEÇTİ: 6c6df1c8 screenshot AI kilit açıklaması ve mesaj kutusu/Gönder yokluğu. M04 için kullanıcıya 2. Vaka profili tabına tıklayıp hiçbir değer seçmeden dört alanın ekran görüntüsünü paylaşması söylenecek. Beklenen dört Seçilmedi. Aynı TEST-OGR-01 ikinci vaka sürüm 1; veri girilmedi.

Güncel kullanıcı tercihi: tek ekranlık adımlar çok yavaş; birkaç kontrolü aynı batch halinde ver. M04 GEÇTİ (20d89333 screenshot dört Seçilmedi). Aktif batch M05–M08, aynı ikinci vaka: profil 5/8/6/ciddiyet boş + üç başlangıç metni; eksik kesinleştirme engeli; sonra ciddiyet7, sunucu kaydı/refresh; onay iptal kilitlememeli; son onay AI açmalı ve başlangıç/profil kilitlenmeli. Kullanıcı sonuçlarını/screenshotlarını bekle; şimdiden pass yazma.

## SON DEVAM — 0.1.1 kullanıcı dönütü
Kullanıcı screenshotlarla eksik alanın kolay bulunmasını ve final/reflection tamamlanmadan Tek son teslim düğmesinin kapalı olmasını istedi. İkisi düzeltildi: validation.js kalıcı linkli hata özeti/alan/sekme vurgusu/focus, canSubmit dört final gereksinimi + sunucu kaydı + AI/online durumunu izler. Asset query sürümü0.1.1; Ctrl+F5 gerekir. Mevcut veri sıfırlanmadı, başlangıç açılmadı. Salt okunur SQLite durum: TEST-OGR-01/test-case-2 working, initial_locked true, submitted false, dört final alanı boş. Kullanıcının ekranı erken aktif düğmeyi doğruluyor; gerçekten eksik teslim edilmiş değil. 30 hedefli Chrome + 18 mevcut browser kontrolü geçti; ilk link/blur timeout FIX-11 ile düzeldi. Kanıt test-evidence/validation-results.txt ve browser-validation-regression.txt. M05/M08 kullanıcı kanıtıyla geçti; M06/M07 kısmi. Aktif batch M09–M11: Ctrl+F5, ikinci vakaya dön, teslim boşken kapalı; final karar/gerekçe/güven tamamken reflection eksik kapalı; reflection ve kayıttan sonra açık; alan silince kapalı/geri doldurup kayıtta açık. Henüz teslim yapmasın; sonraki batch AI sohbeti testleri. Eksik profil highlight manuel tekrar istenirse üçüncü açılmamış vaka kullan, kilitli ikinci başlangıcı açma. Kullanıcı daha hızlı birkaç adımlık batch tercih ediyor.

## SON DEVAM — 0.1.2
Kullanıcı son teslim kartını her sayfa altında istemedi; ayrı en son tab istedi. Artık 7. Son teslim (section=confirm) var. Diğer altı bölümde kart/düğme yok; rapor linkleri/teslim zamanı da son tabda. Başlangıçtan önce kilitli; eksik alan/kayıt/AI koşulları aynı. Telemetry confirm kabul ediyor, DB migration yok. Assetv0.1.2, Ctrl+F5 gerekir. 40 hedefli + 18 mevcut browser kontrolü ayrı sentetik veride geçti; test-evidence/submit-tab-*. Mevcut kullanıcının verisi değiştirilmedi. d35a305b screenshot eski footer'da dört eksik link ve disabled submit gösteriyor; M09 görünen kapı geçti, M10–11 hâlâ bekliyor. Yeni aktif M12: Ctrl+F5 → ikinci vaka → altı bölümde teslim yok, 7.Son teslim'de eksik alanlar ve kapalı düğme; screenshot bekleniyor. Kullanıcı birkaç adımlık kısa batch istiyor; henüz teslim yaptırma, sonraki sohbet testleri bekliyor.

## SON DEVAM — 0.1.3
Kullanıcı genel Eksik alanları tamamlayın kutusunun AI/Nihai/Yansıtma vb. sayfalarda görünmesini istemedi; yalnız Son teslim'de istedi. Artık validation-summary sadece section=confirm iken render edilir; tek eksik listesi orada, linkle hedef alana taşınmaz. Alan içi hata/focus ve sekme işaretleri korunur. Footer'da mükerrer liste yok. Assetv0.1.3; Ctrl+F5. Mobil kirli alandan sekmeye geçişte blur/autosave tıklamayı düşürüyordu: pointerdown erken blur'u engeller, asıl kayıt mevcut click akışında yapılır (FIX-13). İlk timeoutlar sonrası 50 hedefli kontrol geçti; kanıt test-evidence/summary-only-validation.txt. Eski genel/backend suite yeniden çalıştırılmadı. Mevcut kullanıcı verisi değiştirilmedi. Aktif manuel M13: Ctrl+F5, aynı ikinci vaka, diğer bölümlerde genel kutu yok, Son teslim'de liste var, linkle alana gidince kutu yok. Kullanıcı onayı bekleniyor; henüz teslim yaptırma. Kullanıcı birkaç adımlık kısa batch tercih ediyor.

## AKTİF DEVAM — yeni test turu R02, batch yaklaşımı
Kullanıcı test yönergelerini baştan, birkaç adımı bir arada batch halinde istedi. Salt okunur yerel DB kontrolü: TEST-OGR-01 üçüncü Akranlar arası anlaşmazlık (test-case-3) ve dördüncü vaka hiç açılmamış. Yeni tur üçüncü vakada başlayacak, ilk/ikinci eski veriyi bozma; seed/reset yok. Aktif R02-B01: Ctrl+F5, çıkış/giriş TEST-OGR-01; Vakalarım + 4 atama; üçüncü vaka Başla; doğru metin/7tab; AI/Nihai/Yansıtma/Son teslim kilit açıklaması; profil dörtSeçilmedi; çalışma sekmelerinde ortak eksik/teslim kartı yok. Değer doldurmayacak/kesinleştirmeyecek/teslim etmeyecek. İki screenshot (ilk vaka ekranı + boş profil), kilitler için kısa kullanıcı sonucu bekle. Altı kontrol BEKLİYOR, geçmiş sonuçları yeniden pass sayma. Sonraki batch başlangıç doğrulama/kayıt/kilit olacak; bütün test listesini birden verme.

## AKTİF DEVAM — R02-B02
Kullanıcı R02-B01 için hepsi tamam dedi; altı kontrol kullanıcı beyanıyla geçti, yeni screenshot yok. Aynı TEST-OGR-01 / üçüncü vaka Akranlar arası anlaşmazlıkta devam. Batch2: profil 5/8/6+ciddiyet boş; üç başlangıç metni doldurup kesinleştirmeyi dene → ciddiyet alanına focus/yan hata, AI kilitli. Ciddiyet7 ve sunucu kaydı sonrası refresh → metin/profil korunmalı. Başlangıç onayını önce iptal → düzenleme devam/AI kilitli. Sonra kabul → profil/başlangıç salt okunur/AI açık. Son teslim tabı nihai/yansıtma boşken eksikler/disabled submit. Henüz AI mesajı/son teslim yok. B02 altı kontrol bekliyor. Kullanıcı toplu birkaç adımlık batch ve kısa tamam bildirimi tercih ediyor; sorun olursa screenshot/çıktı al ve düzelt.

## AKTİF DEVAM — R02-B03
Kullanıcı Batch2 için hepsi tamam dedi; altı kontrol kullanıcı beyanıyla geçti. Aynı TEST-OGR-01 / üçüncü vaka başlangıcı kilitli, AI açık. Batch3 normal mock: ilk soru gönder ve tek soru/tek mock yanıtı kontrol et; nihai karar/gerekçe/güven10 doldur/kaydet; AI'a dön ikinci soru gönder; nihai taslak korunmalı; kayıt sonrası refresh ile sohbet iki soru/iki yanıt ve nihai taslak korunmalı; Yansıtma boşken Son teslim'de yalnız yansıtma eksik/disabled; linkle yansıtmaya git, doldur/kaydet, Son teslim'de eksik yok/button enabled. Henüz teslim yaptırma. Normal hızlı mock testi concurrency kanıtı değildir; Windows devserver tek worker uyarısını unutma. Kullanıcı kısa batch'ler ve tamam bildirimi tercih ediyor; hata halinde screenshot alıp düzelt.

## AKTİF DEVAM — R02-B04
Kullanıcı Batch3 hepsi tamam dedi; altı kontrol beyanıyla geçti, d671177d screenshot ilk usermessage ve açık SENTETİK MOCK cevabını gösteriyor (ikinci mesaj/nihai/reflection screenshotta doğrulanmadı). Aynı TEST-OGR-01 üçüncü vaka nihai/yansıtma dolu ve kaydedilmiş varsayımı kullanıcı raporu. Batch4: nihai gerekçe sil→son teslim disabled/tek eksik; geri yaz/save→enabled. İkinci normalsekmede aynı vakayı aç→readonlywarning/disabled, kapat. Ana sekmede son teslim confirm önceCancel→veri açık; sonraaccept→gerçek sentetik teslim. Bölümler/refresh tümreadonly/messageskorundu. Vakalarım üçüncü kart Teslim edildi/Görüntüle, tekraraynıkilit; öğrenci rapor/puan yok. B04 altı adım henüz bekliyor. Kullanıcıya iki screenshot öner: ikinci sekme uyarısı ve teslim sonrası Son teslim zaman/durum. Global studykapatma yok, sonraki araştırmacı incelemesi. Kısa batch yaklaşımı sürüyor.

## AKTİF DEVAM — R02-B05
Kullanıcı Batch4 hepsi tamam dedi; altı kontrol kullanıcı beyanıyla geçti. f3512ebe screenshot üçüncü vaka Akranlar arası anlaşmazlık Teslim edildi, salt okunur tablar ve 20.10.2026 19:29:55 gösteriyor; ikinci sekme/cancel/reopen ayrı screenshotta yok. Öğrenci akışı sentetik teslimle tamamlandı. Batch5: çıkış TEST-ARASTIRMACI login→matris TEST-OGR-01 × üçüncü vaka Teslim edildi hücresi→doğru dosya. Hamprofil5/8/6/7, başlangıç/nihai/reflection metinleri, finalconf10 ve snapshotları kontrol; iki soru/iki mockcevap; taslak/olay/süre kalitesi ve çıkarım yokluğu. İç araştırmacı notu ekle + gerekçe + refreshkalıcılık. Henüz analiz/yayın/global kapanış başlatma. B05 altı adım bekliyor; sonraki batch tekvaka analiz/etiket/karar sürecine hazırlanacak. Parola yalnız TEST_DATA.md; kullanıcı birkaç adımlık batch tercih ediyor.

## Güncel devam — 0.1.4 ve R02-B06
R02-B05 altı adım kullanıcı “Tüm maddeler TAMAM” beyanıyla geçti; screenshotlar matris/doğru dosya, profil/yanıt/snapshot, dört mesaj, taslak/olay/süreyi destekliyor. İç not kalıcılığı yalnız kullanıcı beyanı. Tarihler için time.js eklendi: matris son etkinliği, süreç başlangıç/teslimi, öğrenci teslimi ve global kapanış yapılandırılmış timezone ile dd.MM.yyyy HH:mm:ss (UTC+03:00) gösterilir. time datetime/title tam UTC hassasiyetini korur; ham JSON/DB/export değişmedi, ham veri ekranında UTC açıklaması var. Asset0.1.4, Ctrl+F5 gerekir. 10 tarih kontrolü + 22 Chrome masaüstü/mobil emülasyon kontrolü ayrı sentetik DB'de geçti. İlk UTC sıfır offset beklentisi testte farklı çıktı; çıktı UTC+00:00 olarak normalize edilip tekrar geçti. Backend/MySQL/restore/50validation suite yeniden çalıştırılmadı. Kullanıcı DB'si sıfırlanmadı/değiştirilmedi.
Aktif R02-B06 altı adım BEKLİYOR: tarih manuel kabulü, ilk öğrenci mesajına iki etiket/gerekçe ve refresh geçmişi; aynı mesaja tek etiket/gerekçeyle ikinci sürüm eskiyi korumalı; test rubriğiyle 0/8 yeni analiz; adım1 bütünlük completed/çıktı-girdi; adım2 güven8→10 difference2 ve 2/8. Sonra dur; adım3–8/rapor/yayın/global kapanış henüz yok. Kısa batch yaklaşımı sürüyor. Sonuç ayrıntıları MANUAL_TEST_RESULTS.md.

## Güncel devam — 0.1.5 etiket sunumu ve R02-B07
R02-B06 altı kontrol kullanıcı “tüm maddeler TAMAM” bildirimiyle geçti. Screenshotlar iki etiket review (önce Bilgi istedi+Açıklama istedi, sonra Metin düzenletmek istedi), bütünlük submitted/revision19/warnings[], güven8→10/fark2 ve mevcut analiz f2e4ae74 2/8 gösteriyor. Tarih/refresh/girdi ayrıntıları kullanıcı beyanı. Ham JSON etiket sunumu kullanıcıya anlaşılmaz geldi: 0.1.5'te labels.js model + güncel seçim, işaretli checkboxlar, mesaj bağlamı, canlı önce/sonra/eklenen/kaldırılan önizlemesi, isimli/tarihli/gerekçeli/aktörlü karar geçmişi var. Geçmiş JSON kapalı ayrıntıda. AI önerileri güncel araştırmacı kararıyla karışmaz; son boş review önceki dolu seçimi geçersiz kılar. DB/schema/API yazımı değişmedi; mevcut veriye dokunulmadı. 8 model testi ve ayrı sentetik DB'de 35 Chrome kontrolü geçti (test-evidence/label-history-*.txt); desktop ekran görsel incelendi, 390px dialog taşmıyor. JS syntax/index PHP lint geçti. Backend/MySQL/restore/validation suite bu UI değişikliğinde yeniden çalıştırılmadı. Asset0.1.5, Ctrl+F5.
Aktif R02-B07 BEKLİYOR: yeni etiket ekranını kontrol/değiştir-iptal; aynı 2/8 analizde step3 çalıştır/yalnız initial girdisi/kabul+gerekçe; step4 çalıştır/yalnız final girdisi/kabul+gerekçe ve refresh. 4/8'de dur; yeni analiz/global kapanış/rapor yok. Mock puanlar gerçek AI/kurumsal rubrik kabulü değildir. Manuel ayrıntılar MANUAL_TEST_RESULTS.md.

## Güncel açıklama — Batch7 tekrar bekliyor
Kullanıcı etiketin güncellenmemesinin gerekçeyi boş bırakmasından kaynaklandığını doğruladı; kayıt hatası iddiasını geri çekti. Salt okunur DB'de ilk iki review vardı, yeni Eleştiri kaydı yoktu. Gerekçe zorunluluğu beklenen davranış. Hata bildiriminin dialog arkasında kalması ayrı UX kusuru olarak yeniden üretildi (label-save-error-before.txt: 26 kontrol sonrası görünür dialog-error beklerken timeout). Görünür modal hata/odak ve başarı bildirimi için 0.1.6 kodu yazıldı; genişletilmiş test komutu kullanıcı tarafından kesildi, son koşu tamamlandı/geçti sayılmayacak. Önceki 0.1.5 35+8 sonucu korunuyor. Mevcut kullanıcı DB'sine yazılmadı. Kullanıcı şimdi Batch7 yönergelerini tekrar istiyor: aynı TEST-OGR-01 üçüncü vaka ve mevcut 2/8 analiz, etiket görünümü/iptal, step3 initial girdisi/kabul+zorunlu gerekçe, step4 final girdisi/kabul+zorunlu gerekçe/refresh; 4/8'de dur. Tüm B07 maddeleri bekliyor. 0.1.6 doğrulamasını sonraki geliştirme turunda tamamla; henüz başarı ilan etme.

## Güncel devam — 0.1.7 ve R02-B08
R02-B07 altı kontrol kullanıcı “adımlar tamam” bildirimiyle geçti. Screenshotlar initial/final mock çıktılarını ve mevcut f2e4ae74 4/8 gösteriyor; kabul kararları/girdi ayrıntıları/refresh kullanıcı beyanı (screenshotlar henüz [] karar gösteriyor). Kullanıcı sekiz adımın baştan görünmesini istedi. analysis.js + renderRun sekiz görünür kart, Tamamlandı/Sıradaki/Kilitli/Çalışıyor durumları, her adım altında sonuçlar ve inceleme, geçmiş denemeler, araştırmacı karar durumu ekler. Yalnız sıradaki adım çalışır; istek sürerken tüm düğmeler kapanır. Adım8 önce3–7 kararlarını ister, eksikleri adları/numaralarıyla açıklar. Backend/DB/şema değişmedi; kullanıcı verisi sıfırlanmadı/yazılmadı. Asset0.1.7 Ctrl+F5.
12 ilerleme modeli +67 Chrome kontrolü geçti (test-evidence/analysis-progress-*.txt), desktop4/8 screenshot görsel incelendi;390px taşma yok. İlk browser koşusunda56 kontrol sonrası reload sayımı ekranın async yüklenmesini beklemeden yapıldığı için başarısızdı; test beklemesi düzeltildi ve67 tamamlandı. İlk kayıt analysis-progress-first-run.txt. 0.1.6 yarım kalan modal hata/gerekçe odağı/Eleştiri kaydı/503 hata koruması da aynı67 içinde geçti; artık doğrulandı. JS syntax ve index PHP lint geçti; backend/MySQL/restore/50validation tekrar çalıştırılmadı.
Aktif R02-B08 BEKLİYOR: mevcut4/8'de tüm8 kart görünür/5aktif;5çalıştır/kabul+gerekçe;6çalıştır/kabul+gerekçe;7çalıştır/kabul+gerekçe;refresh7/8 kararlar ve8aktif. 8'i henüz çalıştırma, rapor/global kapanış yok. Kullanıcı kısa batch istiyor. Ayrıntılar MANUAL_TEST_RESULTS.md.

## Güncel devam — R02-B09
R02-B08 altı kontrol kullanıcı “hepsi tamam” bildirimiyle geçti. Screenshotlar kartlı görünümü,adım5/6 tamamlanan mock çıktıları,mevcut f2e4ae74 7/8 ve adım7Kabul edildi/8aktif gösteriyor.5/6 karar ayrıntısı ve refresh kullanıcı beyanı; screenshotlarda o kararlar öncesi Bekliyor var. Kod değişmedi/test suite yeniden koşulmadı. Aktif R02-B09 altı kontrol BEKLİYOR:8çalıştır→8/8;Yeni rapor snapshotı oluştur yalnızbirkez→İncelemede;doğru öğrenci/vaka/sürüm,güven8/10/fark2 ve sentetik sınırlamalar;Wordindir/aç;PDFindir/aç;Raporu onayla→refreshOnaylı/Yayımlanmadı. Ayrıca öğrenciye yayımla/globalstudyclose bu batch'te yapılmayacak. Word uygulaması yoksa açılışkontrolünü geçti sayma. Mevcut kullanıcı sentetik verisini sıfırlama; aynıüçüncüvaka/analizden devam. MANUAL_TEST_RESULTS.md güncellendi.

## Güncel devam — R02-B10
R02-B09 altı kontrol kullanıcı “hepsi tamam” bildirimiyle geçti. Screenshotlar8Tamamlandı,snapshotSürüm1,doğruöğrenci/vaka/profil/güven8→10/fark2,WorduygulamasındaDOCXaçılışı,ChromePDF5sayfa/Türkçe veOnaylı/Yayımlanmadı gösteriyor. Tüm belge sayfaları/refresh kullanıcı beyanı; görünmeyensayfalar görseltestedilmiș sayılmadı. Kod değişmedi/otomatiktestlertekrarlanmadı. AktifR02-B10 altı kontrolBEKLİYOR: araştırmacıVeriindirmeTEST-OGR-01kapsamı testdahilkapalı/kodlanmışaçıkExcelboşapplications;testdahilaçıkExcelçoklusayfalar/kodluPkimlik/metinlerçıkarılmış;eventsCSV;codedJSON;rawJSONkimlik/metin/UTC;öğrenciyeçıkış-girişüçüncüvakaSon teslim'de onaylıyayımlanmamışraporyokluğu. Yayın/globalstudyclose yapılmayacak. Sonraki muhtemelen karar alternatifleri/snapshotdeğişmezliği ve ensonayrısenaryodayayınkapısı. MANUAL_TEST_RESULTS.md güncellendi.

## R02-B10.1 sonucu ve devam
Kullanıcı Excelde tüm veri sayfalarının boş, yalnız dictionary dolu olduğunu bildirdi.9a8fe65a boş applications sayfasını,7cd480b6 dictionary alanlarını/Türkçe açıklamalarını doğruluyor. Testdahilkapalı ilk kontrol için beklenen davranış; B10.1 GEÇTİ. Diğer B10.2–6 henüz BEKLİYOR. Sonraki: aynı Veri indirme ekranında KapsamTEST-OGR-01,Sentetik test verisini dahil et açık,Kodlanmış çıktı açık;Excel yenidenindir/eski boşdosyayla karıştırma;applications P-kodlu satırlar ve messages çıkarılmışmetinler beklenir. Hâlâboşsa indirmeekranıseçimlerini gösteren screenshot iste. Yayın/globalkapanış yok. Kod değişmedi/otomatiktestkoşulmadı.

## R02-B10.2 sonucu — kodlanmış Excel
7f904cb4 applications3satır/P-17061f920b41/üçüncüsubmitted;2388d357 ve59590b55 events/type/UTC/payloadçıkarılmış;f607332d messages4satır/userassistant/metinçıkarılmış gösteriyor. B10.2 GEÇTİ. B10.3–6 BEKLİYOR; Excelde events görülmesi ayrı CSVindirme kanıtı değil. Kullanıcı heartbeat/type anlamını soruyor; EVENT_CATALOG.md veapp.js/config doğrulandı: heartbeat15000msperiyodikdurumgözlemi,okuma/düşünmekanıtıdeğil. section_open bölüm,focus pencereodağıdeğişimi,visibilitysayfagörünürlüğü,field_focusalanodağı,text_changedmetindüzenleme,profile_changedseçimdeğişimi,copy/paste. Kod değişmedi; sonrakiCSV,ikiJSON,öğrencigörünmezrapor adımları sürüyor.

## Güncel devam — 0.1.8 olay açıklamaları
Kullanıcı type kodlarının process mining kullanımı ve daha anlaşılır görünmesini istedi; sonra kalan testlere geçilecek. EventLabels.php16olay/7bölüm için Türkçe ad/grup/açıklama ve sözlük sağlar. Export events satırlarına type_tr/section_tr/event_category/event_description eklenir; özgünkodlar/UTC/satırlar/veritabanı değişmez. Dictionary tüm eşlemeleri ve app_id süreçörneği,istemci/sunucu zaman farkını açıklar. heartbeat teknik gözlem,okuma/düşünmeeylemi değil; teknik grup analizamacınagöre filtrelenebilir,otomatik olay silinmez. VeriindirmeUI açıklaması,asset0.1.8. Ctrl+F5 ve yeni dosyaindirme gerekir; eskiExcel değişmez. KullanıcıDB'sine yazılmadı.
PHP tests/export-labels.php30kontrol +tests/run.php69SQLite kontrolü geçti. ÜretilenXLSXPhpSpreadsheetile yeniden açıldı;CSV/JSONTürkçe eşlemeleri,kodlanmışmetinçıkarma,hamverikoruma,tüm16kodunmappingi veDBolaydeğişmezliği doğrulandı. Kanıt test-evidence/event-labels-export.txt veevent-labels-regression.txt. PHP lint/JSsyntax geçti. Chrome/MySQL/hosting/restore bu değişiklikte tekrarlanmadı. DATA_DICTIONARY/EVENT_CATALOG güncellendi.
ManuelB10.1 veB10.2geçti; yeniTürkçesütunlarınmanuelkontrolü veB10.3–6 BEKLİYOR. Sonraki kısa batch: Ctrl+F5 testdahilaçık kodlanmışExcel yenidenindir/events sağsondakiyeni4alan/dictionary;eventsCSV;codedJSON;rawJSON;öğrenciTEST-OGR-01üçüncüvakaSon teslim raporyok/saltokunur. Globalstudyclose/yayın yok.

## R02-B10 ekran kanıtları —0.1.8
Kullanıcı5screenshot paylaştı, ayrıca tümü geçti beyanı yok. a3dbbb12Excel events dörtTürkçe sütunu,heartbeatTeknikdurum veözgün kodları gösteriyor: yeniTürkçesunum GEÇTİ. f4381d73CSVExceldeyanlışayrılmış (başlıklartekalan, açıklamalı satırlarda bazımetinlerayrısütun):B10.3KISMİ;CSVindirildi/Türkçeokunuyorama doğru14sütuniçeaktarma bekliyor. Bu screenshot dosyayı bozuk ilan etmeye yetmez;yerelayırıcıetkisiolabilir, UTF-8/virgül/çifttırnak ile temizdosyadaniçeaktar.
1239afe7kodlanmışJSON P-kodluapplications veçıkarılmışevents payload/Türkçeaçıklamalar: B10.4KISMİ,messages.content çıkarılmış kontrolüscreenshotta görünmüyor.010d345bhamJSON login/testmetinleri/UTC: B10.5GEÇTİ. JSONgörüntüleritümkatılımcıkapsamıiçeriyor;yalnızTEST-OGR-01filtresibuJSONlargöstermez,APIhatasıçıkarımıyok. b1cda681öğrenciüçüncüvakaSon teslimsaltokunur/raporyok: B10.6GEÇTİ. Yayımlanmışrapor/CSVtümsütun/JSONmessages görünmüşsayılmadı.
Aktifsonikikontrol: CSV'yiyeniboşExceldeVeri>Metinden/CSVdenUTF-8virgülçifttırnak ileiçeaktar→14kolon(id…event_description);kodlanmışJSONda[kodlanmış çıktıda metin çıkarıldı]aranmalı. Sonuçbekleniyor. Kod değişmedi/testlertekrarkoşulmadı;globalstudyclose/yayın yok.

## Güncel devam — R02-B11
B10sonikikontrol screenshotlarlaGEÇTİ:eb776866CSVvirgül/çifttırnak,767d4009doğrusütunlar;ef90c246codedJSONmessagesmetniçıkarılmış. Batch10tamam. Koddeğişmedi/testlertekrarkoşulmadı. AktifB11altıkontrolBEKLİYOR:aynıf2e4ae74üçüncüvaka8/8;5DüzenleJSONdayalnızsummary='SENTETİK TEST: Araştırmacı tarafından düzenlenen dönüşüm açıklaması.'gerekçelikayıt;eskiAIçıktısı/kararlarveSürüm1değişmez;5Reddet6Kararverilemedigerekçeleri;yenirapor snapshotSürüm2İncelemede;partsstep5reject6uncertain/Sürüm1acceptkarşılaştır;5ve6Kabuletgeridönüş+gerekçe/refresh,Sürüm2değişmez. İki raporuonay/yayınyok,globalstudycloseyok,yenianalizyok. Sonrakiuygunbatchyayınöncesikapı,globalkapanıştestiniayrısenaryoyap. MANUAL_TEST_RESULTS.mdgüncellendi.

## R02-B11.1 açıklama — henüz düzenleme testi geçmedi
Kullanıcı1.adımdaKabulet seçtiğini belirtti.2b872866 screenshot5Dönüşümadımıiçin ikiacceptkararı,sonundaSENTETİK TEST:Düzenleme kontrolü gerekçesi veözgünAIbody gösteriyor. Sistemacceptiçin özgünAIçıktısını kullanır;editseçilmediği için düzenlenmişsummaryuygulanmaz,bukayıphatasıdeğil. B11.1düzenlemebeklenenisonuçHENÜZTAMAMLANMADI;diğerB11adımlarınıpasssayma. Kullanıcıekrandakibutonisimleriyleaçıkbatchtalimatıistiyor:5.Dönüşüm>Sonuçlar ve inceleme>Kabul / Düzenle / Reddet / Karar verilemedi düğmesi>dialogKararaçılırlistesiDüzenle>JSONsummarydeğeri>Karargerekçesi>Kararı ayrı kaydet. KabulvarsayılanınınbilerekDüzenleyedeğiştirilmesivurgulanmalı. Eskiacceptkararısilinmez,yenieditkararıeklenir;Sürüm1değişmez. Koddeğişmedi/testkoşulmadı;B11altıkontrolbekliyor.

## R02-B11 kanıt incelemesi — son kabul dönüşü eksik
5e4e9436 screenshot5Dönüşümde eskiacceptler+yeniedit/düzenlenensummary/gerekçe gösteriyor.5502d1ac Sürüm1Onaylı/Yayımlanmadı veSürüm2İncelemede. ÜçekmetinJSONolarakokundu:3fc7e273ve75bf6173raporgövdeleri normalizeJSONolaraktamamenaynı;ikisininstep5/6accept. b955c62fmetnindestep5reject,step6uncertainvar;buüçüncümetinDBSürüm2bodyilebirebireşit. B11.1–5GEÇTİ. SaltokunurDBkontrolü:5geçmişiaccept/accept/edit/reject,sonacceptyok;6accept/uncertain/accept/accept,sonkararkabul. DolayısıylaB11.6KISMİ,5DönüşümüKabuletgeridönüşüeksik;6ikiayrıacceptkaydıkorunmuş,verikaybıyok. Sürüm2hâlâreject/uncertain,son6kabulüeski raporudeğiştirmemiş. KullanıcıDB'sineyazılmadı/koddeğişmedi/testsuitekoşulmadı.
Sonraki:yalnız5.Dönüşüm>Sonuçlar ve inceleme>Kabul / Düzenle / Reddet / Karar verilemedi>KararKabulet>gerekçeSENTETİK TEST: Kabul durumuna dönüş.>Kararı ayrı kaydet;refresh5ve6Kabuledildi,raporSürüm2aynıkalmalı. Yenianaliz/snapshot/onay/yayın/globalcloseyok. BuadımkabulgelmedenB11tamamlandıdeme.

## R02-B11 tamamlandı ve aktif R02-B12
1301bc7c screenshot ve kullanıcımetni5Dönüşümün sonacceptkararını eaa079c4...,17:58:30UTC veKabul durumuna dönüş gerekçesiyle doğruluyor. SaltokunurDB:5accept,6accept;raporSürüm2parts5reject/6uncertainolarakkorunmuş. B11.6GEÇTİ veBatch11tamamlandı. Kod/testsuite değişmedi/koşulmadı;DByazılmadı.
AktifB12ikiayrısenaryoyuhafifbatchtebirleştirir:globalveritoplamaaçıkkenSürüm1yayındenemesiengeli;ikincihalençalışılanvakadayalnıznihaitaslakileChromeçevrimdışıkayıt. Üçüncüteslimedilmişvaka/rapor2değiştirilmeyecek.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B12.1 | AraştırmacıüçüncüvakaRaporSürüm1>Ayrıca öğrenciye yayımla | Araştırma genelinde veri toplama açık hatası;yayınolmaz | BEKLİYOR |
| R02-B12.2 | Refreshaynıraporvesistemdurumu | Onaylı/Yayımlanmadı;SistemveişlemgeçmişiGlobaldurumAçık | BEKLİYOR |
| R02-B12.3 | ÖğrencigirişiüçüncüvakaSon teslim | Başarısızyayındenemesinden sonra raporhâlâyok | BEKLİYOR |
| R02-B12.4 | İkinciSınıfta dijital araç kullanımıNihai değerlendirme;F12NetworkOffline;kararmetniyaz | Sunucuyakaydedildidenmez;çevrimdışıuyarısı | BEKLİYOR |
| R02-B12.5 | NetworkNo throttling/Online | Aynıtaslakkaydedilir;sunucukayıtonayı | BEKLİYOR |
| R02-B12.6 | F12kapat,refreshikinciNihai değerlendirme | Testkararı korunur;son teslimyapılmaz | BEKLİYOR |

Kullanılacakikinci-vakanihaitestmetni:SENTETİK TEST: Bağlantı kesintisinde yazılan karar. Offlineikenrefresh/çıkışyapma;yalnızOnlinekayıtonayısonrasırefresh. Gerçekgüçkesintisitestideğildir. SistemdeGlobalveritoplamayıkapatdüğmesinebasılmayacak.

## R02-B12.1 kanıtı ve talimat açıklaması
A4036a63 screenshotSürüm1Onaylı/Yayımlanmadı veAraştırma genelinde veri toplama açık hatası gösteriyor:B12.1GEÇTİ. B12.2refresh/sistemdurumu ayrıca doğrulanmış değil. Kullanıcı2.adımdansonrasınıanlamadı;öğrencihesabınageçiş/üçüncüraporyokluğu/ikincihenüzteslimedilmeyenvakadanNetworkOffline→metin→Online→kayıtonayı→refresh açıkça tekrarlanacak. B12.3–6bekliyor,henüzpassyok. Kod/testdeğişmedi.

## R02-B12 tamamlandı ve aktif R02-B13
Kullanıcı “hepsi tamam” bildirdi;B12altıkontrolGEÇTİ.79356063üçüncüvakaöğrencisaltokunur/raporyok;1abd92dbOfflineveBağlantı yok,sunucuya gönderilemedi,başarısızsave/events;82d24e11No throttling,sunucukayıtonayıveHTTP200save/events gösteriyor. Refreshmetinkalıcılığı/sistemAçık kullanıcıbeyanı. BuChromeçevrimdışısınamasıdır;gerçekelektrik/PCcrashkurtarmasısayılmaz. İkincivakaçalışmayadevam,nihaitestkararıeklendi,teslimedilmedi. Koddeğişmedi/testlerkoşulmadı.
AktifB13katılımcıyönetimiayrıTEST-YONETIM-01hesabıyla yapılacak. NormalChromearaştırmacı,gizlipencereöğrenci. HerformgerekçesiSENTETİK TEST: Katılımcı yönetimi. ParolayalnızTEST_DATA.mddekitestparolası;paylaşılmayacak.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B13.1 | Katılımcılar>+Katılımcı ekle,TEST-YONETIM-01/Sentetik Yönetim Testi/Etkin/gerekçe | Hesaplistedeactive | BEKLİYOR |
| R02-B13.2 | Vakalar ve atamalar>Atama yönet,ilkGrupçalışmasındaadaletvakasınıyenihesabaata | Öğrenciyegörünenatanmışaktifvaka | BEKLİYOR |
| R02-B13.3 | Gizlipencereyenihesapgirişi,vakabaşlat,başlangıçproblemtaslağıkaydet | Sunucukayıtonayı;kesinleştirmeyok | BEKLİYOR |
| R02-B13.4 | AraştırmacıDüzenle/Dondur/Arşivle>Hesap durumuDondurulmuş,kaydet;öğrenciçıkış/giriş | Girişengellenir | BEKLİYOR |
| R02-B13.5 | HesapEtkin/öğrencinoTEST-YONETIM-02/parolaboş;kaydet;yenilogingiriş | Atama/taslakaynı;parola/değişmezkimlikkorunur | BEKLİYOR |
| R02-B13.6 | HesapArşiv,kaydet/listedearşivlenmiş;tekrarEtkin,kaydet;öğrencigiriş | Geriaktifleştirmedetaslak/atamakorunur | BEKLİYOR |

Problemtaslağı:SENTETİK TEST: Hesap yönetiminde korunacak taslak. Yeniöğrencibaşlangıcıhenüzkesinleştirmeyecek;mevcutTEST-OGR-01verisine dokunma. Global kapanış/yayın yok.

## R02-B13 açıklama — donma henüz uygulanmamış
Kullanıcı4.adımdansonrasınıanlamadığınıbildirdi.52fbb8ddlistedeTEST-YONETIM-01active,255d541dtekatananaktifvakayıgösteriyor (screenshotvakabaşlamadanönce). SaltokunurDBgüncel:hesapactive,id a56e4d952346041cd7526dbfffb86ce3;test-case-1initialproblemtaslağıSENTETİK TEST: Hesap yönetiminde korunacak taslak.kaydedilmiş. Audittekparticipantoluşturma;dondurma/yenidenadlandırmahenüzyok. B13.1–3GEÇTİ;B13.4–6BEKLİYOR. Sonrakitalimat4tenbaşlayacak,normalpencerearaştırmacı/gizliöğrenciayrımıherişlemdebelirtilecek;yalnızTEST-YONETIM-01hedeflenecek,TEST-OGR-07zatenfrozensatırınadokunma. 4dondur/girişengeli→EtkinveTEST-YONETIM-02yenino/parolaboş→yenilogingiriş/taslak→Arşiv→Etkingeridönüş/taslak. KullanıcıDB'siyazılmadı/koddeğişmedi/testkoşulmadı.

## R02-B13 sonucu ve0.1.9sekme düzeltmesi
E3ee722agirişengeli,7fba4864TEST-YONETIM-02giriş/atananvaka,63619574taslakkorunması,c191d0acbaşkasekmeuyarısısaltokunur. SaltokunurDBauditaynıteknikid a56e4d952346041cd7526dbfffb86ce3içinactive→frozen→active+TEST-YONETIM-02→archived→activeveproblemtaslağıkorunmuş;B13.4–6GEÇTİ,katılımcıyönetimikontrolleritamam. AyrıUXregresyonu:güvenliçıkışsessionStoragetabkimliğinitemizliyoramalocalStorageeskiownerkilidini20saniyebırakıyordu;hızlıyenigirişbaşkasekmeuyarısıverebiliyor. İzolesentetikChrometestindeyenidenüretildi(logout-lease-before.txt4kontrolsonrasıFAIL). 0.1.9releaseLeaseyalnızsahibiolduğukilidihome/başarılılogouttatemizler;başkasekmekilidinitemizlemez. Kodpublic/assets/app.js,indexassets0.1.9.69Chrome kontrolüGEÇTİ(logout-lease-after.txt),gerçekikincisekmehalenreadonly. JSsyntax/indexPHPlintgeçti. Backend/MySQL/export/restoreyenidenkoşulmadı;mevcutDB'yeyazılmadı.
Yeni0.1.9manuelkontrolBEKLİYOR:diğeröğrencisekmelerinikapat,Ctrl+F5,TEST-YONETIM-02Grupçalışmasıvakasınıaç;gerekiyorsa20saniyesonrayenile,uyarıyok/düzenlenebilir. Problemtaslağınakısasentetikekyaz/sunucukayıtbekle;çıkışvehemenyenigiriş/açılıştasahtebaşkasekmeuyarısıyok/metinkorunmuş. Buretesthenüzpasssayılmayacak;sonrakibatchvaka/atama/sürümyönetimiolacak. Globalstudyclose/yayınyok.

## 0.1.9 manuel tekrar tamamlandı — R02-B14
Kullanıcı üçkontrol için “hepsi tamam” bildirdi.0.1.9sahteleaseuyarısınınkalkması,düzenlenebilirtaslak/sunucukaydı,hızlıçıkış-girişmetinkorunması kullanıcıbeyanıylaGEÇTİ;yeniscreenshotyok. B13veleasekullanıcıtekrarıtamam. Koddeğişmedi/testsuitekoşulmadı.
AktifB14vaka/atama/sürümyönetimiBEKLİYOR. AyrıSENTETİK TEST · Vaka sürümü adlıDenemevakasıoluşturulacak;mevcutvakalardüzenlenmeyecek. Normalpencerearaştırmacı,gizlipencereöğrenci.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B14.1 | Vakalar ve atamalar>+Vaka ekle;Taslak,Denemevakası;İLK METİN | YeniTaslak;Öğrenciönizlemesiaynımetin | BEKLİYOR |
| R02-B14.2 | Yeni kartDüzenle/Yeni sürüm>DurumAktif;öğrenciTEST-YONETIM-02giriş/refresh | Atanmadığıiçingörünmez | BEKLİYOR |
| R02-B14.3 | Atama yönet TEST-YONETIM-02yenivakaata;öğrenciBaşla | İlkmetinvesürümnumarasıyüklenir;başlatılır | BEKLİYOR |
| R02-B14.4 | YalnızyenitestvakasımetniGÜNCEL METİNileyenisürümkaydet | Yeniaktifvakasürümüoluşur | BEKLİYOR |
| R02-B14.5 | TEST-YONETIM-02mevcutçalışmayenile | İLK METİNveöncekisürümkorunur | BEKLİYOR |
| R02-B14.6 | TEST-OGR-02yenivakaata;öğrenciçıkış/girişBaşla | GÜNCEL METİNvedahayenisürüm | BEKLİYOR |

Başlık:SENTETİK TEST · Vaka sürümü;Sıra7;Vakametni:[İLK METİN] Öğrenciler grup görevi için farklı çalışma yöntemleri öneriyor.;Yönerge:Kararınızı ve gerekçenizi yazın.;RubrikTESTRUBRİĞİ;Denemevakasıişaretli;Devam eden erişimi de durdurişaretsiz;hergerekçeSENTETİK TEST: Vaka sürümü kontrolü. Güncelmetin:[GÜNCEL METİN] Öğrenciler grup görevinde görev paylaşımı konusunda anlaşamıyor. Aktifyaparken de sürümartacağından İLK METİN/GÜNCEL METİN işaretleri gerçekv1/v2numarasıdeğildir;ekrandakigerçeksürümnumaralarınıkarşılaştır. Yeniöğrenciyanıtıkesinleştirme/teslimyok,globalclose/yayınyok.

## R02-B14 ekran kanıtı ve aktifB15
6aa6b2c6taslakv1önizlemesi;7028becfTEST-YONETIM-02sürüm2İLK METİN;563c4241aynıöğrenci/sürüm2ilkmetin;89f8a191TEST-OGR-02sürüm3GÜNCEL METİN. SaltokunurDBvaka2311909712f51cb7c2702554b488906dactive/practice1;ikibaşlangıçsürümü2/3ayrımetinlerde. B14.1,3,4,5,6GEÇTİ.B14.2atamaöncesigörünmezlikscreenshot/beyanyok,BEKLİYOR. SonrakiB15.1aynısenaryoyuhenüzatanmamışTEST-OGR-03iletekrarlayıpkapatacak. Koddeğişmedi/DB'yeyazılmadı/testsuitekoşulmadı.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B15.1 | TEST-OGR-03Vakalarım'dayenitestvakasıyok;sonraaraştırmacıata,öğrenciBaşlabasma | Atamasızgörünmezlikveaçılmamışatamahazırlığı | BEKLİYOR |
| R02-B15.2 | YalnızyenitestvakasıDurumYeni başlangıçlara kapalı,stopkapalı;kaydet;TEST-OGR-03refresh | Henüzbaşlamadığıiçinvakagörünmez | BEKLİYOR |
| R02-B15.3 | TEST-YONETIM-02giriş/mevcutyenivakaDevam et | Başlanmışçalışmaerişilebilir;İLK METİNsürüm2 | BEKLİYOR |
| R02-B15.4 | YalnızyenitestvakasıDevam eden erişimi de durdurişaretli,kaydet;öğrencirefresh | Başlanmışvaka daVakalarım'dagörünmez/erişimkapalı | BEKLİYOR |
| R02-B15.5 | DurumAktif,stopişaretsiz,kaydet;TEST-YONETIM-02refresh | Vakaeskiçalışma/sürüm2ilegerigelir | BEKLİYOR |
| R02-B15.6 | TEST-YONETIM-02yenitestvakasıatamasınıkaldır>öğrencirefreshyok;yenidenata>refreshDevamet | Atamaerişimdenetimi;eskiuygulama/sürüm2korunur | BEKLİYOR |

HerişlemgerekçesiSENTETİK TEST: Vaka erişimi kontrolü.YalnızSENTETİK TEST · Vaka sürümüdenemevakası;orijinalaraştırmavakalarınıdeğiştirme. TEST-OGR-03bu batchvakayıbaşlatmayacak. Kapanışglobaldeğilvakadüzeyinde;globalstudyclose/yayın/teslimyok.

## Son kullanıcı yönlendirmesi — GitHub teslimi
Kullanıcı testlerin uzaması nedeniyle yorulduğunu bildirdi; yeni manuel batch istemeden yalnız gerekli kontrolleri önceliklendir. R02-B15 çalıştırılmadı ve BEKLİYOR; B14.2 de yeni kanıt bekliyor. Üretim/hosting/gerçekAI/kalanmanuelkontroller geçti sayılmayacak. Son istek son sürümü https://github.com/hkayaduman/VakaLab deposuna push etmek. Yerel Git geçmişi yok ve remote HEAD/main/master ref sorgusu boş döndü; ilkmaincommiti hazırlanıyor. config/local.php,storageDBleri,vendor,.tools,tests/tmp,.log dışarıda;TEST_DATA.mddekiörnekparolayalnızaçıkçasentetikseedörneği. Yeni kod değişikliği yok;songeçerli sonuçlar0.1.9Chrome69,0.1.8export30veSQLite69. Push sonucu ayrıca kaydedilecek.

GitHub teslim kontrolü: otomatik onay denetimi TEST_DATA.mdde kullanılan sabit test parolası nedeniyle ilk commit/push isteğini çalıştırmadan reddetti. Güvenli düzeltme: belgedeki parola yer tutucuya çevrildi; yerel hesaplar/config/DB değişmedi. Commit/push yeniden doğrulama bekliyor.

## GitHub teslimi tamamlandı — 2026-10-20
VakaLab0.1.9 kaynakları,belgeleri ve sentetik test kanıtları https://github.com/hkayaduman/VakaLab main dalına gönderildi. İlk kaynak commit4df25e0. Sabit test parolası commitöncesinde yer tutucuyla değiştirildi;hiçbircommitparolayıiçermiyor. Yerelconfig/DB/vendor/.tools/tests/tmp dışarıda. Sonrakiiletideyeni manuelbatchotomatikbaşlatma;kullanıcıyorulduğunuveotomasyontercihiniiletmişti. KalanB14.2/B15vegerçekhosting/AItestleribekliyor;üretimhazırilanedilmedi. Bu teslimdeuygulamakodudeğişmedi,eskigerçektestsonuçlarıkorundu,yenitestkoşulmadı.
