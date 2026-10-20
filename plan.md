# Geliştirme Planı ve Durum Takibi

## Mevcut durum

**Güncel — 2026-10-21, 0.2.10 (yerel):** Kullanıcının onayladığı akış: etiketli soru gönderimi (anlık) → AI yanıtı hazırlanıyor (ayrı bekleme süresi) → yanıt geldi (anlık) → AI sekmesindeyse görüntüleme. Bekleme bölüm değişse de aynı trace/request eşleşmesiyle devam eder; timeline_lane=ai_wait olarak öğrenci bölüm ziyaretlerinden ayrı tutulur. Disco Timeline Lane ve Response Section alanlarını taşır. Üstteki bölüm toplamlarına bekleme ayrıca eklenmez; eksik yanıt kaydında süre null kalır. Eski soru noktaları/araştırmacı etiketleri ve teslimde takibin durması korunur. 82 navigation, 115 Chrome, 38 export, 28 etiket/süreç ve 75 SQLite kontrolü geçti; sözdizimi geçti. 40 saniye senaryosu sentetik zaman fixture'ıdır; gerçek sağlayıcı gecikme ölçümü değildir. Mevcut çalışma DB'sinde yazma/seed/reset/yayın/etiket değişikliği yok. Gerçek AI/hosting/Disco import kabulü açık; commit/push yok.

**Önceki aşama — 2026-10-21, 0.2.9 (yerel):** Kullanıcının ekranında eski iki sorunun kaybolduğu saptandı. Salt okunur çalışma DB incelemesinde 2 gerçek öğrenci mesajı ve 2 response_visible var, message_send yok; ilk soru zaten Bilgi istedi etiketliydi. Kullanıcı sonradan ikinciyi Eleştiri istedi olarak etiketledi; iki güncel araştırmacı seçimi salt okunur doğrulandı. Eksik eski gönderim kaydı artık soruyu gizlemez: sunucu mesaj kaydından ayrı soru noktası gösterilir, kaynağı belirtilir, bekleme süresi null/Ölçülemedi kalır. Disco noktasında Duration Seconds boş ve Time Source server_message; istemci saatinden süre uydurulmaz. Yeni kayıtların soru/yanıt/görüntüleme aralıkları korunur, çift soru eklenmez. 77 navigation, 114 Chrome, 38 export, 28 etiket/süreç, 75 SQLite ve sözdizimi kontrolleri geçti. Kullanıcı yeni öğrenciyle denemeyi seçti; sonuç henüz bildirilmedi. Gerçek DB yazma/seed/reset/yayın/etiket değişikliği yapılmadı; yeni otomatik manuel batch dayatılmadı; commit/push yok.

**Önceki aşama — 2026-10-21, 0.2.8 (yerel):** Kullanıcının son ölçüm kuralı: AI sorusu gönderildi → yanıtın tarayıcıya gelişi → öğrenci AI sekmesinde kalıyorsa görüntüleme → sonraki soru/yanıt/görüntüleme → bölüm geçişi. Soru/yanıt bekleme ve görüntüleme ayrı aralıklardır; yanıt gelişi sıfır süreli nokta olayıdır. Aynı türden tekrar sorular ayrı kalır. navigation.activities ve export navigation_activities bu sıralı aralıkları içerir; Disco varsayılanında bu satırlar kullanılır. Parent Visit ID ile özgün ziyaret korunur; süresi tekrar eklenmez. Başka bölümde gelen yanıt öğrenciyi AI sekmesine geçmiş göstermez. Eksik geliş kaydı/saat bozukluğu açık belirtilir, zaman tahmin edilmez. 69 navigation, 113 Chrome, 38 export, 28 etiket/süreç ve 75 SQLite kontrolü geçti; sözdizimi kontrolleri geçti. Mevcut çalışma DB'sinde yazma/seed/reset/yayın yok; manuel batch/commit/push yok. Gerçek AI/hosting/Disco import kabulü açık.

**Önceki aşama — 2026-10-21, 0.2.7 (yerel):** AI ziyaretlerinde yalnız taslak yazma/görüntüleme = AI sohbeti görüntüleme; gerçekten gönderilen öğrenci mesajları istek anahtarıyla eşleştirilerek etkinlik adı güncel etiketlerle oluşturulur (AI sohbeti · Bilgi istedi vb.). Araştırmacı seçimi, boş seçim dahil önceliklidir. Bir ziyarette çoklu soru/etiket + ile birleştirilir; süre çoğaltılmaz. Etiket bekliyor/eşleşme yok/araştırmacı boş seçimi ve eşleşmeyen gönderme denemesi ayrılır. Aynı adlar araştırmacı API/ekranı, navigation_visits Excel/JSON ve Disco Activity alanında kullanılır. 49 navigation, 109 Chrome, 38 export, 28 etiket/süreç, 75 SQLite kontrolü ve PHP/JS sözdizimi geçti. Gerçek DB değişmedi; yeni manuel batch, commit/push yok. Gerçek AI/hosting/Disco import kabulü bekliyor.

**Önceki aşama — 2026-10-21, 0.2.6 (yerel):** Ziyaret etkinliği yedi gerçek sekme adı + görüntüleme/güncelleme biçiminde. Kilit durumu etkinlik yerine ayrı Ek bilgi alanında; Nasıl bitti? gerçek hedef sekmeyi gösterir. Sayfanın görünmez olduğu gözlemi açık dille açıklanır; neden/süre tahmin edilmez. CSV aynı etkinlik adlarını ve ayrı Next Section, Next Section Name, Locked sütunlarını taşır. Süre hesabı ve son teslimde takibin durması korundu. Ayrı sentetik ortam: 36 navigation, 105 Chrome, 38 export, 11 süreç JS kontrolü; PHP/JS sözdizimi geçti. Gerçek çalışma veritabanına müdahale, yeni manuel batch, commit/push yok. Gerçek hosting/AI/Disco import kabulü bekliyor.

**Önceki aşama — 2026-10-20, 0.2.2:** Kullanıcı isteğiyle öğrenci Vakalarım ekranındaki Katılım bilgilerim düğmesi ve bağlı istemci formu kaldırıldı. Mevcut metadata/araştırmacı Program-Sınıf alanları/API/DB korundu. JS syntax/indexPHP lint ve ayrı sentetik DBde69Chrome kontrolü geçti (test-evidence/student-0.2.2-browser.txt). Diğer test grupları bu küçük UI değişikliğinde yeniden çalıştırılmadı;0.2.1deki12grup547kontrol tarihsel kanıttır. Gerçek ortam kabulü açık; yeni manuel batch yok. Commit/push yok.

2026-10-20: 0.1.0 sentetik pilot uygulama geliştirildi; yerel önizleme çalışıyor. SQLite ve MariaDB üzerinde 69’ar alan/dosya kontrolü, iki PHP worker ile 42 HTTP kontrolü, Chromium ile 18 browser kontrolü geçti. Bunlar gerçek hosting/AI veya kullanıcı kabulü değildir. Kanıt: TEST_RESULTS.md ve test-evidence/.

Durumlar: BEKLİYOR / DEVAM EDİYOR / ENGELLİ / DOĞRULANDI. Tamamlanma için kod, test kanıtı ve belge gerekir. Tasarımın hazır olması uygulamanın hazır olması değildir.

## Hazırlanan belgeler

- [x] Öğrenci paneli şartnamesi.
- [x] Araştırmacı paneli şartnamesi.
- [x] Codex geliştirme promptu.
- [x] Manuel test senaryoları başlangıç rehberi.
- [x] Devam bağlamı ve bu plan.
- [ ] Gerçek rubrik teslimi/doğrulaması.
- [ ] Hosting ve AI sağlayıcısı bilgilerinin doğrulanması.

## Aşamalar

| ID | İş | Durum | Tamamlanma kanıtı |
|---|---|---|---|
| P01 | Depo/hosting gereksinimleri ve mimari | DOĞRULANDI (yerel) | ARCHITECTURE + CLI check; gerçek hosting bekliyor |
| P02 | Şema, migration, sürümleme, veri sözlüğü | DOĞRULANDI (yerel MariaDB/SQLite) | 001/002, güncel69ikiDB;0.2.0yedekli22tablo geçiş kanıtı |
| P03 | Giriş, roller, sahiplik, kurulum güvenliği | DOĞRULANDI (yerel HTTP) | 42 HTTP kontrolünde rol/CSRF/IDOR/frozen/logout |
| P04 | Ayrı alanlar, vaka/öğrenci listesi ve atamalar | DOĞRULANDI (yerel) | 119HTTP/50hedefliUI;kapalı vaka ve kişisel ek süre |
| P05 | Öğrenci responsive ekranı ve durum makinesi | DOĞRULANDI (yerel Chrome) | 69browser/50validation; fiziksel mobil kabulü bekliyor |
| P06 | Autosave, revision, kurtarma, ikinci sekme | DEVAM EDİYOR | Revision ve ikinci sekme geçti; kesinti/kurtarma kullanıcı kabulü bekliyor |
| P07 | AI adapter, mock, tek istek kilidi, akış | DEVAM EDİYOR | Mock/attempt/fencing + 2-worker concurrency geçti; gerçek API/hosting yok |
| P08 | Olaylar, taslaklar, süreler, kopyalama | DOĞRULANDI (yerel) | Dedup, union/belirsizlik testleri; event katalog + sentetik fixture |
| P09 | Etiket ve rubrik yönetimi/sürümleri | DOĞRULANDI (yerel) | Sürüm ve score/kanıt kuralları geçti; gerçek rubrik bekleniyor |
| P10 | Ayrı sıralı AI ön değerlendirme | DOĞRULANDI (mock) | Sıralı adımlar ve bağımsız payload/JSON negatif testleri geçti |
| P11 | Araştırmacı kararları, rapor snapshotları | DOĞRULANDI (yerel) | Karar, snapshot ve özel ekin öğrenciye sızmaması geçti |
| P12 | Vaka kapanışı, kişisel ek süre ve ayrı yayın | DOĞRULANDI (0.2.1 yerel HTTP) | Vaka/kişisel kapı + onay/yayın; aç/kapat sonrası rapor görünürlüğü; eski global kural iptal |
| P13 | DOCX/PDF/XLSX/CSV/JSON çıktıları | DOĞRULANDI (yerel/sentetik) | Otomatik paket/roundtrip kontrolleri + R02-B09/B10 kullanıcı Word/PDF/Excel/CSV/JSON ekranları; gerçek hosting bekliyor |
| P14 | Sentetik seed ve test hesapları | DOĞRULANDI (yerel) | İdempotent seed, 7 öğrenci/5+1 vaka; TEST_DATA.md |
| P15 | Güvenlik/regresyon ve mobil testler | DEVAM EDİYOR | Güncel12grup547örtüşen kontrol; fiziksel mobil/gerçek kesinti/stres bekliyor |
| P16 | Shared hosting kurulumu ve geri yükleme | DEVAM EDİYOR | Kurulum rehberi + yerel ayrı DB restore 8 tablo eşit; gerçek hosting bekliyor |
| P17 | Kullanıcı manuel testi ve düzeltmeler | DEVAM EDİYOR | Batch01–13 tamamlandı; B14 sürüm ekran kanıtı; B14.2/B15 otomatik karşılıkları geçti. Kritik gerçek cihaz/kesinti kabulü bekliyor |
| P18 | Son kabul ve araştırma öncesi kontrol | BEKLİYOR | Rubrik/onam/AI/hosting ve kullanıcı onayı bekleniyor |

## Her aşama için kayıt şablonu

- Aşama:
- Durum:
- Değişen dosyalar:
- Çalıştırılan test/komut:
- Gerçek sonuç:
- Çalıştırılmayan test ve nedeni:
- Bilinen risk:
- Sonraki adım:

## Açık kararlar ve engeller

| ID | Konu | Durum | Güvenli yaklaşım |
|---|---|---|---|
| D01 | Gerçek rubrik verilmedi | AÇIK | yalnızca sentetik test rubriği |
| D02 | AI sağlayıcısı/model/anahtar yok | AÇIK | açıkça işaretli mock + adapter |
| D03 | PHP/MySQL/eklenti/timeout/streaming bilinmiyor | AÇIK | kurulum ön kontrolü ve gerçek hosting testi |
| D04 | Autosave/heartbeat/hareketsizlik değerleri | AÇIK | yapılandırılabilir ve sürümlü varsayım |
| D05 | Etiketleme tetikleme zamanı | AÇIK | sohbetten bağımsız, belgeli yöntem |
| D06 | Çoklu araştırmacı hesabı | DOĞRULANDI (yerel) | Her araştırmacı ayrı alan; ortak vaka erişimi kullanıcı tarafından iptal edildi |
| D07 | Onam, saklama ve çekilme politikası | AÇIK | kurum metni bekle; uygunluk iddiası yapma |
| D08 | Yedekleme sıklığı ve depolama | AÇIK | kurulum belgesinde doğrula |

## Sorun takip tablosu

Düzeltilen sorunlar ve açık doğrulamalar BUGS.md içinde; kullanıcı dönütleri ve düzeltmeleri kaydedildi. Kritik bir açık araştırma kabulü yapılmış sayılmaz.

| Hata ID | Başlık | Önem | Yeniden üretim | Durum | Düzeltme | Regresyon kanıtı |
|---|---|---|---|---|---|---|
| OPEN-01…06 | Gerçek AI/hosting/kurum kararları ve manuel kabul | bkz BUGS.md | MANUAL_TESTS.md | AÇIK | dış doğrulama bekliyor | yerel kanıt TEST_RESULTS.md |

Önem önerisi: P0 veri kaybı/kimlik sızıntısı/kilit ihlali; P1 ana akış engeli; P2 yanlış rapor/ölçüm; P3 görsel/iyileştirme. Gerçek etkiyle yeniden değerlendir.

## Sonraki somut adım

Yeni manuel batch yok. RELEASE_READINESS.md içindeki hosting/AI/kurum bilgileri sağlanınca ayrı test hostinginde kurulum, gerçek sağlayıcı ve hedefli regresyon doğrulanacak. Otomatik doğrulanamayan fiziksel cihaz ve gerçek kesinti kontrolleri kısa ayrı kabul adımları olarak kalır. Yalnız somut hata/ihtiyaç için kod değiştirilecek.

## P01 çalışma kaydı — 2026-10-20
Depo ve tüm şartnameler okundu. ARCHITECTURE.md ve config/example.php oluşturuldu. PHP/MySQL PATH üzerinde yok; taşınabilir PHP edinme deneniyor. P01 DEVAM EDİYOR; hosting gereksinimleri gerçek ortamda doğrulanmadı. Sonraki: şema ve alan kuralları.

## P02–P07 geliştirme kaydı
Şema (001.sql), Auth, Db, Rules, App ve Ai yazıldı. Şema FK/unique; optimistic revision, immutable snapshot, tek aktif AI ve idempotency içeriyor. Henüz test edilmedi; durum DEVAM EDİYOR. Sonraki: yönetim, olay, analiz ve arayüz.

## P08–P13 geliştirme kaydı
Olay dedup, süre aralıkları, sürümlü tanımlar, sekiz adımlı analiz, kararlar, rapor snapshot/onay/yayın, gerçek format üreticileri ve iki panel arayüzü eklendi. Test kanıtı henüz yok; DEVAM EDİYOR. Sonraki: CLI seed, sözleşme ve uçtan uca testleri.

## P14–P15 ilk doğrulama
CLI migration/idempotent seed ve test runner eklendi. PHP 8.3.35 + SQLite üzerinde 67 alan/dosya kontrolü geçti. HTTP testleri iki yerel PHP worker ile çalışıyor; eşzamanlı autosave 47 ms, ikinci AI ve teslim engeli doğrulandı. Windows sandbox is_readable yanlış negatif verdi; aynı dosya testi sandbox dışında geçti. MySQL/hosting/manuel mobil henüz doğrulanmadı.

## P16–P18 teslim kaydı — 2026-10-20

- Kod + migration + vendor/lock + sentetik seed + kurulum/manuel test/veri sözlüğü belgeleri üretildi.
- Son gerçek kanıt: SQLite 69, MariaDB 69, HTTP 42, Chromium 18; yerel dump/restore 8 tablo sayısı eşit; PDF 6 sayfa Türkçe render incelendi.
- Rapor özel eki ve katılımcı iç notu öğrenciye verilmez; testleri geçti. Kodlanmış export ham metni çıkarır.
- Önizleme: http://127.0.0.1:8088/; yerel SQLite + sentetik seed. Bu sunucu Windows'ta tek worker, üretim sunucusu değildir.
- Çalıştırılmayanlar: gerçek API/hosting/HTTPS/proxy/alt klasör, fiziksel telefon/Word, gerçek kesinti ve kullanıcı T01–T28. Geçti olarak işaretlenmedi.
- P06/P07/P13/P15/P16 kabul kontrolleri devam ediyor; P17/P18 insan/dış ortam bekliyor. Üretime hazır denmedi.

Son önizleme smoke testi: yerel araştırmacı girişi, sentetik dosya görünümü, PDF ve DOCX gerçek HTTP 200. Son kanıtlar test-evidence içinde. Kullanıcı kabul testleri hâlâ BEKLİYOR.

## Kullanıcıyla manuel test başladı — 2026-10-20
P17 DEVAM EDİYOR. MANUAL_TEST_RESULTS.md oluşturuldu; M01 öğrenci giriş/ana sayfa için kullanıcı kanıtı bekleniyor. Henüz hiçbir yeni manuel test geçti sayılmadı.

Manuel test M01 GEÇTİ (kullanıcı ekran görüntüsü: oturum açılmış öğrenci ana sayfası, sentetik banner, 4 atama sayacı). İlk vaka başlamış; mevcut veriyi koruyarak ikinci açılmamış vakada M02 bölüm/ilk ekran kontrolüne geçiliyor. T04 bütünü henüz geçmedi.

Manuel M02 GEÇTİ: kullanıcı ekran görüntüsünde ikinci vaka sürüm 1, vaka metni, altı tab ve AI/nihai/yansıtma Kilitli göstergeleri doğrulandı. Aktif M03: AI tabına tıklamada açıklama ve mesaj formunun kapalı kalması; henüz kanıt yok. T04 tamamı geçmedi.

Manuel M03 GEÇTİ: kullanıcı screenshot AI kilit açıklamasını ve mesaj formunun yokluğunu gösterdi. Doğrudan API engeli bu kanıtla geçilmiş sayılmadı. Aktif M04: Vaka profili dört alanın başlangıçta Seçilmedi olması; kanıt bekleniyor.

Manuel M04 GEÇTİ: dört profil alanı Seçilmedi, kullanıcı screenshot 20d89333. Kullanıcı talebiyle kısa batch testlere geçildi. M05 eksik profil/M06 refresh kayıt/M07 onay iptal/M08 kesinleştirme kilidi birlikte yönlendiriliyor; dördü de henüz BEKLİYOR.

## 0.1.1 — kullanıcı hata dönütü düzeltmesi
M05 eksik profil engeli ve M08 görünen kilit/AI açılışı kullanıcı kanıtıyla geçti; M06 refresh/M07 iptal kısmi. FIX-09 eksik alan yönlendirmesi ve FIX-10 erken aktif son teslim düğmesi düzeltildi; testte bulunan blur/link sorunu FIX-11 olarak giderildi. public/assets/validation.js + app.js/app.css/index.php, tests/validation.cjs ve belgeler güncellendi. Gerçek sonuç: 30 hedefli Chrome + 18 mevcut browser regresyonu geçti; JS/PHP syntax geçti. Eski 69/69/42 backend suite yeniden çalıştırılmadı (backend değişmedi). Kullanıcı verisi değişmedi; ikinci vaka teslim edilmemiş. Sonraki batch M09–M11 Ctrl+F5 sonrası final/reflection tamamlanma-kayıt/silme kapısı. Yeni manuel kabul bekleniyor.

## 0.1.2 — son teslim sekmesi
Kullanıcı talebiyle son teslim ortak footer'dan kaldırılıp 7. Son teslim sekmesine taşındı. Kilit/eksik bağlantıları korunuyor, yeni confirm olay kodu Telemetry'ye eklendi; eski kayıtlar değişmedi. Öğrenci şartnamesi/README/test rehberleri güncellendi. Gerçek sonuç: 40 hedefli + 18 mevcut browser kontrolü geçti. Manuel M12 yeni yerleşim kontrolü bekliyor; kullanıcı sonrasında M10–M11'e ayrı Son teslim tabından devam edecek.

## 0.1.3 — eksik özetinin kapsamı
Kullanıcı talebiyle genel Eksik alanları tamamlayın kutusu yalnız Son teslim'de. Diğer altı bölümde render edilmez, linkten geçişte taşınmaz; son sekmede tek liste var. Testteki mobil blur/sekme tıklama sorunu FIX-13 ile giderildi. 50 hedefli Chrome kontrolü geçti; eski backend/genel suite bu değişiklikte tekrarlanmadı. Plan/context/şartname/test rehberi güncellendi; manuel M13 yeni düzen kontrolü bekliyor.

## Manuel test R02 yeniden başlama
Kullanıcı 0.1.3'te test yönergelerini baştan batch halinde istedi. TEST-OGR-01 üçüncü vaka test-case-3 açılmamış olarak salt okunur doğrulandı; mevcut ikinci vaka korunuyor. R02-B01 giriş/ana sayfa/yedi tab/başlangıç kilitleri/boş profil/ortak kart yokluğu, altı kontrol BEKLİYOR. Eski sonuçlar yeni turda pass sayılmıyor; seed/reset yapılmadı.

R02-B01 altı kontrol kullanıcının hepsi tamam bildirimiyle GEÇTİ olarak kaydedildi (yeni screenshot/API kanıtı yok; bağımsız gözlem iddiası yok). Aktif R02-B02: üçüncü vakada eksik profil/alan odağı, autosave-refresh, onay iptal/kabul ve başlangıç kilidi, boş nihaiyle Son teslim engeli. Altı adım BEKLİYOR; AI ve teslim henüz yapılmayacak.

R02-B02 altı kontrol kullanıcı hepsi tamam bildirimiyle GEÇTİ; yeni screenshot/API kanıtı yok. Aktif R02-B03: iki mock AI soru/yanıtı, nihai taslakla serbest geçiş, refresh sonrası sohbet/taslak korunması, yalnız yansıtma eksik kapısı ve yansıtma sonrası hazır teslim. Henüz teslim yapılmayacak; gecikmeli AI concurrency bu batch'in kapsamı değil.

R02-B03 kullanıcı hepsi tamam bildirimiyle altı kontrolde GEÇTİ. d671177d screenshot ilk soru/mock yanıtını ayrıca doğruluyor; diğer sonuçlar kullanıcı beyanı. Aktif R02-B04 altı kontrol: gerekçe sil/geri kaydet kapısı, ikinci sekme salt okunur, teslim onay iptali, sentetik üçüncü vakayı gerçekten teslim, refresh/readonly ve karttan yeniden görüntüleme. Kontroller bekliyor; global veri toplama kapanışı yapılmayacak.

R02-B04 altı kontrol kullanıcı hepsi tamam bildirimiyle GEÇTİ. f3512ebe screenshot teslim durumu/zamanı ve görünen salt okunur tabları ayrıca doğruluyor. Aktif R02-B05: araştırmacı login/matris, doğru öğrenci-vaka, ham profil/yanıtlar, sohbet, taslak/olay/süre kalitesi ve gerekçeli iç notun korunması. Altı kontrol bekliyor; analiz/yayın/global kapanış yok.

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

## P04/P12/P17 — 0.1.9 otomasyonla kalan erişim senaryoları — 2026-10-20

- B14.2/B15 iş kurallarının otomatik karşılıkları DOĞRULANDI (46 HTTP); ayrı sentetik global kapanış/yayın DOĞRULANDI (34 HTTP). Toplam 80; test-evidence/access-publication-0.1.9.txt. Ekran/kullanıcı kabulü yerine yazılmaz.
- Eklenenler: tests/access_publication_checks.py, RELEASE_READINESS.md, yeni test kanıtı; CONTEXT/TEST_RESULTS/MANUAL_TEST_RESULTS/BUGS kayıtları. Uygulama kodu/şema/sürüm değişmedi; mevcut config/DB kullanılmadı. Mevcut veride seed/reset/kapanış/yayın yapılmadı.
- Bu kapsamda hata çıkmadığından ürün geliştirmesi veya geniş regresyon gerekmedi. Önceki MariaDB/HTTP/Chrome/export sonuçları yeniden çalıştırılmadı. Gerçek ortam/AI/rubrik/onam ve kritik cihaz/kesinti kabulü BEKLİYOR.
- Sonraki: RELEASE_READINESS.md bilgi/erişim listesine göre ayrı test hostingini ve gerçek sağlayıcıyı doğrulamak. Yeni manuel batch isteme; yalnız otomatik doğrulanamayan kritik kontrolleri daha sonra kısaca iste. Test/belge değişiklikleri henüz commit/push edilmedi.

## P19 — 0.2.0 araştırmacı sadeleştirmesi — DOĞRULANDI (yerel)

- Ayrı çalışma alanları, araştırmacı hesabı oluşturma, kısa formlar, rutin gerekçeleri kaldırma, toplu atama, kişisel ek süre, vaka veri toplama aç/kapat, tanım silme, tam vaka export ve Disco CSV tamamlandı.
- Migration002 yerel özel yedek sonrası uygulandı; 22 tablonun eski tüm alan/satırları eşit. Mevcut veri toplama/yayın durumuna müdahale yok.
- Testler: 119 hedefli HTTP +42 concurrency HTTP; 69 genel Chrome +24 yeni UI; 69 SQLite +69 MariaDB; 8 restore tablo sayısı; 35 export. TEST_RESULTS.md ve test-evidence/researcher-0.2.0-*.
- Gerçek hosting/sağlayıcı/rubrik/kurum/kritik cihaz-kesinti ve gerçek Disco import bekliyor. Kullanıcıya yeni manuel batch yok. Sonraki adım dış ortam bilgileri sağlanınca ayrı test kurulumunda doğrulama. Kod/test/belgeler henüz commit/push edilmedi.

## P20 — 0.2.1 seçilebilir araştırmacı ekranları — DOĞRULANDI (yerel)

- Ek süre yeniden test edildi; vaka kartında atanan öğrenci listesi/ilgili atama yönetimine geçiş; matris yerine tek vaka/öğrenci arama/durum listesi; JSON gerektirmeyen rubrik formu; okunaklı ve indirilebilir işlem geçmişi tamamlandı.
- Güncel koşular: 119 HTTP,69Chrome,50hedefliUI,69SQLite,14rubrik. Kapsam ve kanıt TEST_RESULTS.md. Şema/mevcut DB değişmedi; önceki MariaDB/42HTTP/export/restore yeniden çalıştırılmadı.
- Şartname/devam/kullanım belgeleri güncellendi. Kullanıcıya manuel test yükü yok. Sonraki: gerçek ortam/kurum bilgileri ve önceki dış kabul sınırları. Commit/push henüz yok.

## P21 — Tüm yerel otomatik gruplar ve bağlam tutarlılığı — 2026-10-20

12/12çalıştırılabilir grup GEÇTİ;547bildirilen kontrol,ayrırestore8tablosayısıeşit. TEST_RESULTS.md ve audit-0.2.1-summary.json grup bazında gerçek kanıtı gösterir. Bu tur ürün kodu/şema/mevcut DB değişmedi. Tarihsel manuel sonuçlar yeni kabul gibi yazılmadı; güncel rehber ekran haritası düzeltildi. P18gerçekortam/kurum/kritik dış kabul bekliyor. Commit/push yok.

## 0.2.2 — Katılım bilgilerim kaldırıldı

Öğrenci ana sayfasındaki düğme, olay bağlantısı ve kullanılmayan onboarding istemci fonksiyonu kaldırıldı. Önceden kaydedilmiş veriler, araştırmacının isteğe bağlı Program/Sınıf alanları ve backend değişmedi.69Chrome/JSsyntax/PHPlint geçti; şema/mevcut DBdeyazma yok. Yeni manuel test istenmedi. Test sonucu TEST_RESULTS.md; bu tur eski12grup yeniden koşulmadı.

## Öğrenci raporu örnek gösterimi — HAZIR (ayrı yerel kopya)

Kullanıcının tek rapor gösterimi isteği için localhost:8089ayrıDB/session ile açıldı. TEST-OGR-01, Akranlar arası anlaşmazlık, onaylı/yayımlanmışSürüm1;7.Son teslimdeWord/PDF. Mevcut8088durumuyazılmadı; yalnız kopyada erişim/kapanış/yayın var.6hedefliHTTP/Chrome smoke geçti (student-report-demo.txt);kullanıcı parolası değişmedi ve yeni parola-login testi yapılmadı.0.2.2ürünkodudeğişmedi. Dış kabul açık,commit/pushyok.

## 0.2.3 — Sade öğrenci raporu (yerel geliştirme)

Öğrenciye başlangıç/nihai özgün yanıtlar, her ölçütte puan/düzey/kısa geri bildirim, nihai toplam ve genel geri bildirim sunulur. Toplam = nihai ölçüt puanı × ağırlık toplamı; başlangıç ayrı karşılaştırmadır. Reddedilen/belirsiz puan null kalır. Üretim geri bildirimi onaylı puan/rubrik ve özgün yanıtlardan hazırlanır; puanları uygulama hesaplar. Sağlayıcı çağrısı transaction dışında, karar değişikliği kontrolüyle yeni değişmez rapora eklenir. Eksik/geçersiz/teknik üretim ifadeli geri bildirim kaydedilmez. Araştırmacı Öğrenci raporunu önizle ile inceleyip ayrı onay/yayın yapar. Öğrenci raporunda AI/teknik inceleme/iç notlar yok; iç üretim kaydı araştırmacı snapshotında korunur. Öğrenci API/Word/PDF aynı sade içeriktir. Yeni Raporu görüntüle düğmesi var. Eski gövdeler değiştirilmez, eski sürümde yeni feedback yoksa açıkça henüz hazırlanmadı gösterilir; yeni rapor sürümü oluşturulur.

Ayrı localhost:8089 gösterim kopyasında TEST-OGR-01 / Akranlar arası anlaşmazlık için Sürüm3 oluşturulup onay/yayın yapıldı. Eski kopya rapor gövdeleri ve asıl cases/reports/applications/snapshots/decisions hashleri eşit. Asıl8088veri toplama/yayın/yanıtlar değişmedi. Testler yalnız sentetik mock; gerçek sağlayıcı geri bildirim kalitesi henüz doğrulanmadı. Word görsel renderer LibreOffice bulunamadığından tamamlanmadı; paket/metin ve PDF görsel kontrolü ayrıdır. Son test dökümü TEST_RESULTS.md: 15birim/75SQLite/81Chrome/119HTTP/35export/75MariaDB ve8restoretablosayısı, ayrıdemo6smoke; hepsi sentetik. Commit/push yok.

## 0.2.4 — Okunabilir öğrenci-vaka dosyası (yerelde doğrulandı)

Ham veriler ve süreç yerine Öğrenci yanıtları ve çalışma süreci: alan etiketli yanıtlar/kesinleşmiş kayıtlar, Önce/Sonra yanıt geçmişi, Türkçe işlem çizelgesi (bölüm/arama/periyodik kayıt filtresi ve50şer), görünür-odak süre tablosu ve bilinmeyen aralıklar, AI istek sonucu/süre/denemeler/gönderilen bağlam açıklaması. Analiz sonuçları ve karar geçmişi de okunabilir; puan/özet düzenleme alanlarla, JSON girişi yok. Ham JSON ve Disco indirmeleri ayrı düğmelerdir. Yeni AI ile mesajları etiketle bağımsız öneri çağrısıdır; aynı mesaj/tanımda cache, sahiplik/rol/çıktı/konuşma değişikliği denetimi var. Eski etiketler ve araştırmacı boş dahil kararları silinmez. Analiz6etiketleme de aynı kayıt/validasyon yoluna bağlandı. Disco'ya mesaj başına sunucu zamanlı nokta olayı; AI Labels/Researcher Labels/Effective Labels/Label Source/Label Status eklendi. Araştırmacı seçimi, boş seçimi de kapsayarak önceliklidir. Gerçek veride seed/reset/şema/veri toplama/yayın değişikliği yok. Testler ayrı sentetik DB; sonuçlar TEST_RESULTS.md içinde: 97 Chrome, 28 etiket/Disco PHP, 11 süreç JS, 11 etiket JS, 75 SQLite, 35 export, 119 HTTP, 75 MariaDB ve 8 restore tablo sayısı. Gerçek sağlayıcı/Discoimport/hosting/kurum kabulü bekliyor.

## Process mining hedefi denetimi — 0.2.4

Kullanıcı bölüm dönüşleri, AI soru amaçları, ziyaret süreleri ve darboğaz/sequence analysis hedefini ayrıntılandırdı. Mevcut CSV temel geçiş ve mesaj verisini taşır; ancak visits süreleri Disco'ya aktarılmıyor, mesaj amaçları Activity yerine etiket özelliğinde, bazı alan/oturum/sekme ayrıntıları kodlanmış CSV'de yok. Önceki Disco hazır ifadesi ayrıntılı süre/darboğaz analizi için yeterli doğrulama sayılmamalı. PROCESS_MINING_REVIEW.md mevcut/eksik/sonraki kapsamı kaydeder. Bu tur yalnız kod + resmî Disco belgesi incelemesi ve ayrı bellekte SQLite/mock tanı çalıştırması yapıldı (test-evidence/process-mining-review-0.2.4.txt); gerçek Disco import ve yeni ürün geliştirmesi yok. Mevcut DB/kod/raporlar değişmedi; eski testler tekrar geçmiş gibi yazılmadı. Sonraki somut ihtiyaç: davranış kodları, ayrı ziyaret/süre çıktısı ve açık sequence hazırlama kuralları; kullanıcının örnek yoluyla doğrulama. Mevcut arayüz korunacak.

## 0.2.5 — Sekme giriş/çıkış süreleri ve teslimde durma (yerelde doğrulandı)

Kullanıcı süreyi açıkça sekmeye girişten sonraki sekmeye geçişe kadar geçen zaman olarak tanımladı; tüm 7 sekme için aynı kural, her geri dönüş ayrı ziyaret. Son teslimin başarıyla kaydından sonra izleme gerekmiyor; yeni öğrenci telemetrisi istemci/sunucuda bu sınırla durduruluyor. Gecikmiş teslim-öncesi kayıtlar korunuyor, ilk istemci teslim doğrulaması kabul ediliyor. Önceki görünür/odak ölçümü ayrı kalıyor. Navigation sınıfı ham kayıtlardan ziyaret/başlangıç/bitiş/süre/sonuç üretir; açık uçlar tahmin edilmez. Varsayılan Disco geçiş/süre görünümü; ayrıntılı AI/olay görünümü ayrıca korunur. Excel/JSON'a navigation_visits ve navigation_milestones eklendi. Ürün asset 0.2.5; şema/çalışma DB'si değişmedi. Güncel sonuç: 30 Navigation, 103 Chrome, 50 validation, 119 erişim HTTP, 42 iki-worker HTTP, 75 SQLite, 75 MariaDB + 8 restore tablo sayısı, 38 export, 28 etiket kontrolü geçti; kanıt TEST_RESULTS.md ve navigation-0.2.5-* dosyalarında. Eski tüm-suite sonuçları bu tur tekrar koşulmadı. Gerçek Disco import/hosting/AI/kurum/fiziksel cihaz kabulü hâlâ açık. Commit/push yok.

Önemli kullanıcı ölçüm kararı: Son teslim başarıyla kaydedilince araştırma süreci tamamlanır; sonradan sekmelerde/raporda dolaşma süreye ve davranış akışına katılmaz. Ölçülemeyen kapanışlar tahmin edilmez.

## GitHub teslimi — 2026-10-21

Kullanıcı tüm biriken düzenlemelerin GitHub'a gönderilmesini istedi. Hedef origin/main (hkayaduman/VakaLab), başlangıç commit'i 194f9d0f5b4be549c1921b027a46204ed6fc7960; uzak dal bu commit ile aynı doğrulandı. 0.2.10 kod/belge/test kanıtları commit için hazırlanıyor. 198 aday dosyada sır örüntüsü ve yapılandırılmış anahtar eşleşmesi bulunmadı; config/local.php, storage DB, vendor, .tools ve tests/tmp dışarıda. 41 PHP/JS dosyasında sözdizimi geçti; mevcut son testler yeniden yapılmış gibi yazılmadı. Gerçek ortam kabulü ayrı kalır. Gönderim sonucu henüz doğrulanmadı.
