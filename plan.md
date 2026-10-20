# Geliştirme Planı ve Durum Takibi

## Mevcut durum

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
| P02 | Şema, migration, sürümleme, veri sözlüğü | DOĞRULANDI (MariaDB/SQLite) | 001.sql, 69 assertion iki DB; FK/unique ve sürümler |
| P03 | Giriş, roller, sahiplik, kurulum güvenliği | DOĞRULANDI (yerel HTTP) | 42 HTTP kontrolünde rol/CSRF/IDOR/frozen/logout |
| P04 | Katılımcı, vaka, atama, dinamik matris | DOĞRULANDI (yerel) | Admin/domain testleri + Chromium matris/katılımcı/filtre |
| P05 | Öğrenci responsive ekranı ve durum makinesi | DOĞRULANDI (Chromium) | 18 browser kontrolü; fiziksel mobil kabulü bekliyor |
| P06 | Autosave, revision, kurtarma, ikinci sekme | DEVAM EDİYOR | Revision ve ikinci sekme geçti; kesinti/kurtarma kullanıcı kabulü bekliyor |
| P07 | AI adapter, mock, tek istek kilidi, akış | DEVAM EDİYOR | Mock/attempt/fencing + 2-worker concurrency geçti; gerçek API/hosting yok |
| P08 | Olaylar, taslaklar, süreler, kopyalama | DOĞRULANDI (yerel) | Dedup, union/belirsizlik testleri; event katalog + sentetik fixture |
| P09 | Etiket ve rubrik yönetimi/sürümleri | DOĞRULANDI (yerel) | Sürüm ve score/kanıt kuralları geçti; gerçek rubrik bekleniyor |
| P10 | Ayrı sıralı AI ön değerlendirme | DOĞRULANDI (mock) | Sıralı adımlar ve bağımsız payload/JSON negatif testleri geçti |
| P11 | Araştırmacı kararları, rapor snapshotları | DOĞRULANDI (yerel) | Karar, snapshot ve özel ekin öğrenciye sızmaması geçti |
| P12 | Global kapanış ve ayrı yayın kapısı | DOĞRULANDI (yerel HTTP) | Global kapanış + onay + ayrı yayın doğrudan HTTP negatif/pozitif test |
| P13 | DOCX/PDF/XLSX/CSV/JSON çıktıları | DOĞRULANDI (yerel/sentetik) | Otomatik paket/roundtrip kontrolleri + R02-B09/B10 kullanıcı Word/PDF/Excel/CSV/JSON ekranları; gerçek hosting bekliyor |
| P14 | Sentetik seed ve test hesapları | DOĞRULANDI (yerel) | İdempotent seed, 7 öğrenci/5+1 vaka; TEST_DATA.md |
| P15 | Güvenlik/regresyon ve mobil testler | DEVAM EDİYOR | 69×2 + 42 + 18 yerel; fiziksel mobil/gerçek kesinti/stres bekliyor |
| P16 | Shared hosting kurulumu ve geri yükleme | DEVAM EDİYOR | Kurulum rehberi + yerel ayrı DB restore 8 tablo eşit; gerçek hosting bekliyor |
| P17 | Kullanıcı manuel testi ve düzeltmeler | DEVAM EDİYOR | R02 Batch01–11 tamamlandı; kanıtlar MANUAL_TEST_RESULTS.md; kalan senaryolar bekliyor |
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
| D06 | Çoklu araştırmacı hesabı | AÇIK | yetki tasarımını genişletilebilir tut |
| D07 | Onam, saklama ve çekilme politikası | AÇIK | kurum metni bekle; uygunluk iddiası yapma |
| D08 | Yedekleme sıklığı ve depolama | AÇIK | kurulum belgesinde doğrula |

## Sorun takip tablosu

Düzeltilen sorunlar ve açık doğrulamalar BUGS.md içinde; kullanıcı hatası dönütü henüz yok. Kritik bir açık araştırma kabulü yapılmış sayılmaz.

| Hata ID | Başlık | Önem | Yeniden üretim | Durum | Düzeltme | Regresyon kanıtı |
|---|---|---|---|---|---|---|
| OPEN-01…06 | Gerçek AI/hosting/kurum kararları ve manuel kabul | bkz BUGS.md | MANUAL_TESTS.md | AÇIK | dış doğrulama bekliyor | yerel kanıt TEST_RESULTS.md |

Önem önerisi: P0 veri kaybı/kimlik sızıntısı/kilit ihlali; P1 ana akış engeli; P2 yanlış rapor/ölçüm; P3 görsel/iyileştirme. Gerçek etkiyle yeniden değerlendir.

## Sonraki somut adım

Kullanıcı yerel sentetik önizlemede TEST-OGR-01 ile ilk akışı deneyecek. Sonra INSTALL_SHARED_HOSTING.md ile ayrı test hostinginde T09/T21/T26/T28; gerçek AI/rubrik/onam bilgileri doğrulanacak. Açık iş bulunduğunda mevcut uygulama üzerine küçük düzeltme ve regresyon yapılacak.

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
