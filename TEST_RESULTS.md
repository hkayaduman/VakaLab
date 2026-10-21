# Gerçek test sonuçları — 2026-10-20

Güncel sürüm **0.2.2**. Son gerçek koşu ve sınırları dosya sonundaki 0.2.2 kaydındadır. Eski sürüm tabloları tarihçedir; yeniden çalıştırılmamış sonuçları güncel saymayın.

Sürüm 0.1.0, Windows, PHP CLI 8.3.35, MariaDB 11.4.12 (PDO mysql), SQLite (PDO sqlite), Google Chrome headless + Playwright. AI **mock**. Tüm hesap/metin/zaman fixtureları sentetik. Kullanıcı manuel kabulü **yapılmadı**.

| Çalıştırılan kontrol | Gerçek sonuç | Kanıt / kapsam |
|---|---|---|
| PHP lint, app/bin/public/tests kaynakları | Sözdizimi hatası yok | `php -l`; vendor ve test geçici dosyaları ürün kaynağı sayılmadı |
| `node --check public/assets/app.js` | Geçti | JS sözdizimi; browser işlev testi yerine sayılmaz |
| `php bin/console.php check` | PHP/eklenti/vendor OK | PHP 8.3.35; pdo_mysql, mbstring, curl, zip, gd, XML ailesi/fileinfo |
| `composer validate --strict` | Geçti | composer.json/lock geçerli |
| `composer audit --format=plain` | Bildirilen açık bulunmadı | O anki advisory kontrolü; bütün uygulama güvenlik garantisi değildir |
| `php tests/run.php` | **69 assertion geçti — SQLite** | [çıktı](test-evidence/sqlite-results-final.txt) |
| `python tests/mysql_checks.py` | **69 assertion geçti — MariaDB 11.4.12 / PDO mysql** | [çıktı](test-evidence/mysql-results-final.txt); benzersiz ayrı test DB |
| Yerel MariaDB dump → ayrı yeni restore DB | **8 tablo kayıt sayısı eşleşti** | Aynı mysql_checks.py çıktısı; gerçek hosting restore kanıtı değil |
| `python tests/http_checks.py` | **42 HTTP assertion geçti** | [çıktı](test-evidence/http-results-final.txt); iki ayrı PHP worker, ortak DB/session, SQLite |
| `node tests/browser.cjs` | **18 browser kontrolü geçti** | [çıktı](test-evidence/browser-results-final.txt); 1440×1000 ve 390×844 emülasyon |
| PDF Türkçe metin çıkarımı ve tüm sayfaları render | Geçti; sentetik uzun rapor 6 sayfa | pypdfium2 + pdfinfo; Türkçe `ğüşöçıİ` korundu; [sayfa görünümü](test-evidence/report-contact.png) gözle incelendi |
| DOCX/XLSX paket içeriği ve XLSX yeniden yükleme | Geçti | OOXML gerçek ZIP paketi; word/document.xml Türkçe, xl/workbook.xml, ayrı olay/etiket sayfaları |
| Açık yerel önizleme giriş/dosya indirme smoke testi | Geçti | 127.0.0.1:8088; araştırmacı giriş ve sentetik raporda PDF/DOCX HTTP 200, gerçek dosya imzaları |

69 alan/dosya kontrolünün kapsamı: seed idempotency ve geçmişe dokunmama, atama/aktivasyon, IDOR/rol, başlangıç öncesi AI engeli, profil aralığı, revision/no-op/çakışma, atomik kilit/tek teslim/idempotency, aktif AI sırasında autosave ve teslim engeli, vaka sürümü koruma, event dedup, süre birleşimi/belirsiz boşluk, sıralı analiz, 8→10 güven farkı, bağımsız rubrik payloadları, ağırlık/kanıt/aralık doğrulaması, geçersiz mock JSON, kararsız toplam, rapor kapıları, kimliksiz export/varsayılan test dışlama/CSV formül koruması, etiket sürümü, öğrenci no değişirken ilişkiler, özel rapor ekinin öğrenciye sızmaması, gerçek dosyalar.

42 HTTP kontrolünde ayrıca: CSRF, rol/sahiplik, dondurulmuş giriş, oturum çıkışı, özel katılımcı notu allowlist/preserve, mock error/partial/explicit retry, aynı öğrenci mesajını çoğaltmadan iki attempt, timeout gerekçeli kapatma ve **geç yanıtın fence ile reddi**, erken rapor ve global kapı; gerçek HTTP DOCX/PDF/XLSX indirmesi ve özel ekin öğrenci DOCX dosyasından çıkarılması. İlk concurrency koşusunda aynı session autosave **0.047 s** sürdü ve 6 s gecikmeli AI henüz bitmemişti; son koşunun ölçümü kayıtta. Bu ölçüm performans SLA'sı değildir.

18 browser kontrolü: yalnız atanmış aktif vakalar, boş profil, başlangıç kilidi ve AI açılması, kayıt sonrası vaka başlığı, başlangıcı nihai yanında otomatik göstermeme, mock düz metin, taslağın gezinmede korunması, ikinci sekme salt okunurluğu, mobil taşma/gezinme, teslim kilidi, dinamik yedi öğrenci matrisi, vaka/durum filtreleri, katılımcı ekleme, tek uygulama analiz UI ve JS pageerror olmaması. [Öğrenci](test-evidence/student-home.png), [mobil](test-evidence/student-mobile.png), [matris](test-evidence/researcher-matrix.png).

## Kullanılan yerel komutlar

`php` bu oturumda `D:\VakaLab\.tools\php\php.exe`; `python` bundled runtime yoluydu. Browser için PLAYWRIGHT_PATH bundled node_modules/playwright, CHROME_BIN yerel Google Chrome; MariaDB için MARIADB_HOME=`D:\VakaLab\.tools\mariadb\mariadb-11.4.12-winx64`. Taşınabilir araçlar üretim paketi değildir. Test runnerları normalde PATH/ortam değişkeniyle taşınabilir.

Windows sandbox içindeki PHP `is_readable` gerçek okunabilen dosyada false verdi; ilk XLSX tekrar açma denemesi bu nedenle başarısız oldu. Aynı test izinli normal yerel süreçte tekrar çalıştırıldı ve geçti. İlk Python http.py adı standart modülle çakıştı; http_checks.py olarak düzeltildi ve tekrar çalıştırıldı. Bunlar başarı olarak gizlenmedi; BUGS.md'de kayıtlı.

## ÇALIŞTIRILMADI / kabul bekliyor

- Gerçek AI API/model/protokol, maliyet ve timeout davranışı (bilgiler yok).
- Gerçek hosting HTTPS/proxy, erişim izinleri, alt klasör, worker kapasitesi ve hosting yedeği restore.
- Fiziksel telefon klavyesi/rotasyon, ekran okuyucu, Safari/Firefox gerçek cihaz.
- Word uygulamasında DOCX görsel sayfalama; OOXML yapısı geçti, Word açılışı denenmedi.
- Gerçek internet/elektrik kesintisi, tarayıcı crash, ortak cihazda tam kurtarma kabulü; offline olay fixtureları gerçek kesinti değildir.
- Büyük veri/stres testi, kurum rubriği ve onam/saklama politikası.
- MANUAL_TESTS.md T01–T28 kullanıcı kabulü. Otomatik sonuçlar bu 28 senaryonun tamamını geçti yapmaz.

Streaming bu sürümde yoktur; tam yanıt fallback uygulanmıştır. İlk parça/görünür akış ölçümü yapılmış sayılmaz. Gerçek araştırmada kullanıma hazır olduğu iddia edilmez.

## 0.1.1 — Kullanıcı dönütü sonrası gerçek regresyon — 2026-10-20

- `node tests/validation.cjs`: **30 kontrol geçti**, Chrome masaüstü/390 px mobil emülasyon, ayrı yeni SQLite test DB. [Çıktı](test-evidence/validation-results.txt).
- `node tests/browser.cjs`: mevcut **18 kontrol tekrar geçti**. [Yeni koşu](test-evidence/browser-validation-regression.txt).
- `node --check` app.js ve validation.js; `php -l public/index.php`: geçti.
- Hedefli kapsam: alan adıyla hata özeti, sekmedeki eksik sayısı, kırmızı/aria-invalid/yan hata, ilk eksik alana focus, özet/footer linkinden bölüm geçişi, 0 puanın geçerliliği, hata temizlenmesi, onayı iptal, başlangıç sonrası erken teslimin kapalı kalması, dört final gereksinimi, boşluk metni, sunucu kaydı bitmeden teslimin açılmaması, alan silinince kapanması, AI başlangıç/bitiş ve offline/online kapısı, mobil taşma/sabit başlığın alanı örtmemesi, geçerli teslim sonrası kilit.
- İstemci düğmesi DevTools ile zorla açıldığında da doğrulama onaydan önce durdurdu. Doğrudan API'de dört zorunlu final alanı tek tek boş gönderildi: her biri HTTP 422; application working kaldı ve submit snapshot oluşmadı.
- İlk hedefli koşuda 15 kontrolden sonra footer linki blur/autosave sırasında DOM'dan kalktığı için reflection alanına geçiş timeout oldu. FIX-11 ile giderilip aynı senaryo dahil son 30 kontrol geçti. Bu başarısız ilk koşu başarı olarak sayılmadı.
- [Masaüstü eksik alan](test-evidence/validation-missing-profile.png) ve [mobil](test-evidence/validation-mobile.png) görüntüleri incelendi. Fiziksel mobil/ekran okuyucu testi değildir.
- Kullanıcının ikinci vakasına yazma yapılmadı. Salt okunur durum kontrolü: başlangıç kilitli, working, submitted_at boş, final gereksinimleri boş. Eksik gerçek teslim doğrulanmadı; düğmenin erken aktifliği düzeltildi.
- PHP domain/MySQL/restore testleri bu UI değişikliğinde yeniden çalıştırılmadı; önceki sonuçlar önceki koşunun kanıtıdır. Backend kuralları ve şema değişmedi.
- Kullanıcının yeni arayüzü manuel doğrulaması M09–M11 **bekliyor**. M06 refresh ve M07 iptal için eldeki screenshot kanıtı kısmi; tam geçti yazılmadı.

## 0.1.2 — Ayrı Son teslim sekmesi — 2026-10-20

- `node tests/validation.cjs`: **40 kontrol geçti**. [Kanıt](test-evidence/submit-tab-validation.txt). Önceki 30 kontrol yeni yerleşime uyarlandı; ayrıca altı çalışma bölümünde teslim düğmesi olmaması, yedi sekme, başlangıç öncesi son sekme kilidi, confirm olayının sunucuda kabulü/son bölüm kaydı ve teslim sonrası diğer bölümlerin salt okunurluğu doğrulandı.
- `node tests/browser.cjs`: **18 mevcut kontrol tekrar geçti**; teslim artık kendi sekmesinden yapılıyor. [Kanıt](test-evidence/submit-tab-browser.txt).
- JS sözdizimi ve PHP Telemetry lint geçti. Yeni section kodu confirm, migration gerektirmiyor. Mevcut kullanıcı verisi değiştirilmedi; testler benzersiz sentetik DB'lerde çalıştı.
- [Mobil son teslim sekmesi](test-evidence/submit-tab-mobile.png) incelendi. Fiziksel mobil/gerçek hosting/AI testi değildir.
- Eski backend/MySQL/restore suite bu yerleşim değişikliğinde yeniden çalıştırılmadı. Yeni telemetri section kabulü hedefli gerçek HTTP çağrısıyla doğrulandı.
- Kullanıcı screenshot d35a305b eski ortak kartta dört eksik alan bağlantısı ve kapalı düğmeyi gösteriyor; yeni sekmeli arayüzün manuel kabulü henüz bekliyor.

## 0.1.3 — Eksik kutusu yalnız Son teslim — 2026-10-20

- `node tests/validation.cjs`: **50 hedefli kontrol geçti**. [Kanıt](test-evidence/summary-only-validation.txt). Altı çalışma bölümünde genel eksik kutusu DOM'da yok; kutu yalnız Son teslimde tek bağlantı listesi olarak var. Bağlantıdan nihai/yansıtma alanına gidildiğinde taşınmıyor; kalan alanlar listesi güncelleniyor ve tamamlanınca kayboluyor.
- Önceki kilit, focus, inline hata, dört gereksinimin ayrı HTTP 422 kontrolü, AI/offline/kayıt kapıları, mobil taşma ve gerçek teslim sonrası kilit kontrolleri aynı koşuda geçti.
- İlk koşular 43 kontrolden sonra mobil Son teslim sekmesine geçişte timeout verdi. Olay izinde pointerdown sonrası click'in düşmesi görüldü. Kirli alandan blur/autosave sırasında üst çubuk yerleşim değişimine karşı erken blur önlendi; aynı senaryo son 50 kontrolde geçti. Başarısız ilk koşular gizlenmedi/geçti sayılmadı.
- `node --check` app.js, validation.js ve hedefli test kaynağı geçti. Eski 18 genel browser / backend / MySQL / restore suite bu küçük UI değişikliğinde yeniden çalıştırılmadı; önceki koşuların kanıtları korunuyor.
- Mevcut kullanıcı DB'sine yazılmadı. Yeni düzenin kullanıcı manuel doğrulaması bekliyor; fiziksel mobil testi yapılmadı.

## 0.1.4 — Tarih okunabilirliği — 2026-10-20
- node tests/time.cjs: 10 kontrol GEÇTİ; İstanbul, yıl/gün geçişi, UTC, farklı timezone kış/yaz offsetleri, boş/geçersiz değer, ham verinin korunması. Kanıt test-evidence/readable-time-unit.txt.
- node tests/browser.cjs: 22 kontrol GEÇTİ, ayrı benzersiz sentetik DB. Matris ve süreç özeti okunaklı tarih/UTC+03:00; semantic time datetime/title özgün UTC; ham JSON açıklaması; mevcut giriş/taslak/AI/teslim/ikinci sekme/filtreler/mobil taşma kontrolleri. Kanıt test-evidence/readable-time-browser.txt. Süreç ekran görüntüsü görsel incelendi (tests/tmp/researcher-readable-time.png).
- İlk formatter testi UTC için UTC yerine UTC+00:00 döndüğünü yakaladı. Sıfır offset açık biçime normalize edildi ve 10 kontrol tekrar geçti. Başarısız ilk koşu geçti sayılmadı.
- JS syntax ve index.php PHP lint geçti. Backend/MySQL/restore ve 50 kontrollü validation suite bu değişiklikte yeniden çalıştırılmadı. Fiziksel mobil/hosting/gerçek AI testi değildir. R02-B05 kullanıcının tamam bildirimiyle geçti; R02-B06 bekliyor.

## 0.1.5 — Etiket geçmişi — 2026-10-20
- node tests/labels.cjs: 8 kontrol GEÇTİ (son review, AI ayrımı, eklenen/kaldırılan, girdi değişmezliği, boş review, mesaj izolasyonu, eski satırlardan geçmiş). Kanıt test-evidence/label-history-unit.txt.
- node tests/browser.cjs: 35 kontrol GEÇTİ, benzersiz sentetik DB. İlk seçim boş; iki etiket kaydı/reopen seçili; tek farklı etiketle güncelleme; refresh; önceki/yeni karar ve gerekçeler; isimli ekleme/kaldırma; okunaklı tarihler; 390px dialog taşması; tümünü kaldırma ve boş son review geçmişi; önceki 22 kontrol. Kanıt test-evidence/label-history-browser.txt.
- tests/tmp/label-history.png görsel incelendi; mobile screenshot tests/tmp/label-history-mobile.png. Fiziksel cihaz/ekran okuyucu testi yapılmadı. JS syntax ve index.php PHP lint geçti. Backend/MySQL/restore/50validation bu değişiklikte tekrar çalıştırılmadı. Kullanıcı verisine yazılmadı.
- R02-B06 kullanıcı bildirimiyle geçti; yeni etiket sunumu R02-B07'de manuel kabul bekliyor.

## 0.1.7 — Görünür analiz sırası ve0.1.6 doğrulaması — 2026-10-20
- node tests/analysis-progress.cjs:12 kontrol GEÇTİ. Başlangıçta8adım, sıraya göre açılma, çalışan kilidi, failed/retry, completed retry,3–7 karar kapısı, tamamlananrun ve runizolasyonu. Kanıt test-evidence/analysis-progress-unit.txt.
- node tests/browser.cjs:67 kontrol GEÇTİ, benzersiz sentetik DB. Sekiz kart baştan görünür,1→8 sıralı açılma, istek sürerken kilit,4/8refresh,390px taşma yok,3–7 kararları eksikken8kapalı,kararlar sonrası8açık ve8/8sonrası rapor snapshot düğmesi. Rapor snapshotı oluşturulmadı/yayımlanmadı. Kanıt test-evidence/analysis-progress-browser.txt.
- Aynı koşuda0.1.6 modal gerekçe hatası/odak, seçim koruması,503 hata görünürlüğü, gerekçe koruma,Eleştiri etiketinin başarı bildirimi/reload kalıcılığı doğrulandı. Önceden yarım kalan koşu geçti sayılmadı.
- İlk koşu56kontrol sonrası reload assertionda ekran yüklenmesini beklemediği için başarısız; bekleme düzeltilip67tamamlandı. Başarısız kanıt analysis-progress-first-run.txt korunur.
- analysis-progress-four.png görsel incelendi;analysis-progress-mobile.png üretildi. JS syntax/indexPHP lint geçti. Backend/MySQL/restore/50validation suite tekrar çalıştırılmadı. Mevcut kullanıcı DB'sine yazılmadı.
- R02-B07 kullanıcı bildirimiyle geçti; yeni düzen/R02-B08 manuel kabul bekliyor.

## 0.1.8 — Türkçe olay sözlüğü — 2026-10-20
- `.tools/php/php.exe tests/export-labels.php`:30kontrol GEÇTİ,ayrı in-memory sentetikDB.16izinliolaykodu/7bölümadları,tümsatırlarınkodu/bölümü/UTCkorunması,heartbeatTeknikdurum,kodlanmışpayloadgizleme/hammetinkoruma,DBolaydeğişmezliği,sözlük/CSV/JSONveXLSXyazma-yenidenaçma. Kanıt test-evidence/event-labels-export.txt.
- `.tools/php/php.exe tests/run.php`:69SQLitekontrolü tekrarGEÇTİ;exportfiltreleri/kodlama/analiz/yayınkapısı/dosyabiçimleridahil. Kanıt test-evidence/event-labels-regression.txt.
- EventLabels.php/Export.php/index.php lint veapp.js syntax geçti. Chrome/MySQL/HTTP/restore/gerçekhostingtestleribukoşudayapılmadı. KullanıcıDB'siyazılmadı. YeniTürkçealanlarınmanuelExcelkontrolübekliyor;B10.3–6henüzgeçmedi.

## 0.1.9 — Logout lease —2026-10-20
- İlkizoleChromehataüretimi4kontrolsonrası immediate logout/relogin false lease kontrolünde KALDI;kanıt test-evidence/logout-lease-before.txt.
- Düzeltmesonrası node tests/browser.cjs69kontrolGEÇTİ:anındaçıkış/giriş,sahtekilityok,homegeridönüşüveöncekitestler;gerçekikincisekmereadonlykorunuyor.Kanıt test-evidence/logout-lease-after.txt.
- JSsyntax/indexPHPlintgeçti.Backend/MySQL/HTTP/export/restoreyenidenkoşulmadı.MevcutkullanıcıDB'siyazılmadı;ChromeayrıbenzersizsentetikDBkullandı. YeniUIkullanıcımanueltekrarıbekliyor.

## 0.1.9 — İzole erişim/atama ve kapanış/yayın — 2026-10-20

- `python tests/access_publication_checks.py`: **80 kontrol GEÇTİ** (46 erişim/atama/sürüm + 34 kapanış/yayın). Gerçek yerel HTTP, PHP 8.3.35, SQLite, mock. Kanıt: [tam çıktı](test-evidence/access-publication-0.1.9.txt).
- B14.2/B15 karşılıkları: aktif atamasız vaka listede yok/doğrudan başlangıç 403; atanmış açılmamış vaka kapatılınca liste yok/başlangıç 403; başlamış vaka kapalıyken devam/kayıt açık. Devam eden erişimi durdurunca ve atamayı kaldırınca liste yok, doğrudan start/view/save/initial/submit/chat/events 403. Araştırmacı geçmişi görebiliyor.
- Yeniden açma/atama aynı application/assignment kimliğini, ilk metin sürüm 2'yi, taslağı, revisionları, snapshotları ve sohbeti koruyor; yeni öğrenci güncel sürüm 3'ü görüyor. Reddedilen isteklerin yanıt/analiz/rapor tablolarını değiştirmediği parmak iziyle karşılaştırıldı. İşlem aktörü/zamanı/gerekçesi ve atama kaldırma/geri verme ayrı audit kayıtları doğrulandı.
- Ayrı kapanış senaryosu: açık çalışmada onaylı rapor yayını 409; onay/gerekçe/rol kapıları; kapanış sonrası yeni başlangıç/save/initial/submit/chat 409; önceki çalışma okunabilir. Kapanış otomatik yayın yapmıyor. Ayrı yayın sonrası yalnız sahibine onaylı v1 açılıyor ve JSON indiriliyor; iç ek çıkarılıyor, araştırmacıda korunuyor; taslak v2 ve başka öğrencinin indirmesi 403. Rapor gövdeleri değişmiyor, yinelenen kapanış ilk kaydı bozmuyor.
- Her koşu benzersiz tests/tmp SQLite DB/config, rastgele geçici parola ve bağımsız PHP portu üretir. Yalnız bu fixture seed edildi/kapatıldı/yayımlandı. config/local.php yüklenmedi; mevcut çalışma DB'si okunmadı/yazılmadı; 8088 sunucusuna istek gönderilmedi.
- İlk 78 kontrollü koşu geçti; başlangıç kesinleştirme kapanış kapısı ve başarılı öğrenci JSON indirmesi eklenince son **80** kontrol geçti. Yeni hata veya ürün kodu değişikliği yok.
- Bu suite eski 42 HTTP kontrolünün tekrarı değildir. Bu oturumda Chrome, MariaDB, restore, genel SQLite/export suite'leri ve gerçek AI/hosting çalıştırılmadı. Tarihsel sonuçlar kendi sürümlerinde kalır. Manuel ekran kabulü yapılmış sayılmaz; üretim hazır beyanı yok.

## 0.2.0 — Ayrı araştırmacı alanları ve sade panel — 2026-10-20

| Gerçek koşu | Sonuç | Kanıt |
|---|---|---|
| tests/access_publication_checks.py | **119 HTTP geçti**, SQLite/mock | test-evidence/researcher-0.2.0-http.txt |
| tests/http_checks.py | **42 HTTP geçti**, iki worker; AI sürerken autosave 0.015s (SLA değildir) | test-evidence/researcher-0.2.0-concurrency.txt |
| tests/browser.cjs | **69 Chrome geçti**, masaüstü/390px emülasyon | test-evidence/researcher-0.2.0-browser.txt |
| tests/researcher-ui.cjs | **24 yeni Chrome UI geçti**, ayrı hesap, kısa form, checkbox atama, kapanış/kişisel ek süre, Sil, export/CSV indirme, mobil taşma | test-evidence/researcher-0.2.0-ui.txt |
| tests/run.php | **69 SQLite geçti**, gerçek belge biçimleri dahil | test-evidence/researcher-0.2.0-sqlite.txt |
| tests/mysql_checks.py | **69 MariaDB11.4.12 geçti**, ayrı restore DB 8 tablo sayısı eşit | test-evidence/researcher-0.2.0-mariadb.txt |
| tests/export-labels.php | **35 export geçti**; Türkçe alanlar, XLSX roundtrip, Disco bozuk client zamanında fallback, boş başlık, kapsam/parola dışlama | test-evidence/researcher-0.2.0-export.txt |
| bin/upgrade-sqlite.php | Özel SQLite yedeği tekrar açıldı/doğrulandı; migration002 sonrası **22 tablonun eski tüm sütunları ve kayıtları hash olarak eşit**, tekrar migration değişmedi | test-evidence/researcher-0.2.0-local-migration.txt |

119 HTTP kapsamı: atama/erişim/sürüm koruma; yeni araştırmacı boş alanı; doğrudan view/katılımcı/vaka/atama/ek süre/tanım/analiz adımı/karar/rapor/export yetki engelleri; atomik karma toplu işlemin geri alınması; tam seçili vaka metni/sürümleri/katılımcılar/atamalar/raporlar; parola hashleri yokluğu; kodlanmış Disco Case ID/Activity/UTC/Resource/sıra; vaka kapalıyken yeni başlangıç/save/initial/chat/submit engelleri; başka vakanın açık kalması; seçili öğrenciye hem ilk başlangıç hem devam için ek süre; sürenin kaldırılması; vakanın tekrar açılması; kişisel ek süre/vaka açılışında raporun gizlenmesi ve kapanınca aynı snapshotın dönmesi; teslim kilidinin korunması; eski global study kaydına dokunulmaması.

İlk başarısız koşular saklandı: HTTP helper ham JSON download'u data zarfı sandı (54 sonrası; http-first), yeni UI testi async export ekranını beklemeden seçenek saydı (18 sonrası; ui-first), boş CSV başlığı testi tırnaklı CSV'yi düz metin sandı (32 sonrası; export-first). Test beklenti/beklemeleri düzeltilip yukarıdaki son sayılar tamamlandı; ilk koşular geçti sayılmadı. MariaDB ilk probe TLS yüzünden bağlanamadı; ikinci koşuda 69 kontrol geçti ama dump TLS hatası verdi. Yalnız ayrı loopback fixture istemci/dump'ına --skip-ssl eklenip son 69+restore tamamlandı (mariadb-first/second/final). Üretim TLS ayarı değiştirilmedi.

Sade vaka/export masaüstü ve export mobil ekranları tests/tmp/researcher-simple-*.png olarak üretildi; masaüstü görüntüler görsel incelendi. Sentetik testler config/local.php veya8088 kullanmaz. Yalnız yedekli migration mevcut DB'yi yeni sahiplik/kontrol alanlarıyla yükseltti; seed/reset/çalışma kapatma/yayın yapılmadı. Önceki rapor ve yanıt içerikleri korunur. Yeni kullanıcı beyanı/screenshot kabulü alınmadı.

Gerçek AI, hosting/HTTPS/proxy, kurum rubriği/onam süreci, fiziksel cihaz/ekran okuyucu/gerçek kesinti ve Disco uygulamasında import çalıştırılmadı. CSV biçimi Fluxicon resmi import sütunlarına göre hazırlanıp otomatik parse edildi; gerçek Disco kabulü değildir. Eski 0.1.x kanıtları kendi koşularına aittir; bu tablodakiler güncelde gerçekten çalıştırılmıştır.

## 0.2.1 — Ek süre tekrarı, vaka/öğrenci listesi, rubrik formu, işlem geçmişi

2026-10-20; PHP8.3.35, SQLite, Chrome masaüstü/390px emülasyon, mock. Mevcut çalışma DB'sine yazılmadı; şema değişmedi. Kanıt türü otomatik, yeni kullanıcı beyanı/ekran kabulü değildir.

| Bu tur gerçekten çalıştırılan | Sonuç | Kanıt |
|---|---|---|
| tests/access_publication_checks.py | **119 HTTP geçti**; erişim/ayrı alan/atama/vaka kapanışı/kişisel ek süre/rapor kapıları | test-evidence/researcher-0.2.1-http.txt |
| tests/browser.cjs | **69 Chrome geçti**; öğrenci kilit/kayıt/AI/teslim, yeni liste, etiket kararları/sekiz analiz | test-evidence/researcher-0.2.1-browser.txt |
| tests/researcher-ui.cjs | **50 hedefli Chrome geçti**, aşağıdaki yeni kapsam | test-evidence/researcher-0.2.1-ui.txt |
| tests/run.php | **69 SQLite geçti**, dosya formatları/analiz/kapılar dahil | test-evidence/researcher-0.2.1-sqlite.txt |
| tests/rubric.php | **14 rubrik kontrolü geçti**; saf alan doğrulama, DB/config kullanılmaz | test-evidence/researcher-0.2.1-rubric.txt |

Ek süre tekrarının doğrudan kanıtı: yeni UI 17–22 kontrollerinde başlangıçta düzenlenebilir TEST-OGR-05, vaka kapanınca salt okunur, ek süre verilince tekrar düzenlenebilir; seçilmeyen TEST-OGR-04 kapalı kalır; süre kaldırılınca05yeniden salt okunur. HTTP 100–105: seçili öğrenci kayıt, henüz başlamamış öğrenciye ilk başlangıç, seçilmeyen öğrenci engeli, süre iptali ve vaka tekrar açılışı. HTTP115: ek süre teslim edilmiş yanıtları açmaz. Bu sonuçlar sentetik ortam kanıtıdır, mevcut kullanıcı kayıtlarında işlem yapılmadı.

Yeni50UI: vaka/öğrenci arama/durum, doğru süreç ve geri dönüşte seçim koruma; doğru atanmış öğrenci sayısı ve ilgili vaka önseçimi; boş yeni araştırmacı alanı; toplu atama; rubrik formunda eksik ad/alan odağı, düzey aralığı, ekle-kaldır sırasında veri koruma, iki boyutun JSON'a çevrilip kaydı, yeniden açılış ve eski sürüm/boyutIDkoruma; okunaklı tanım; işlem geçmişinde Türkçe isimler/arama/silinmiş tanım bağlamı/20şer gösterim/tam yetkili JSON indirmesi; mobil taşma ve JS pageerror yokluğu.

Rubrik14: geçerli kesirli ağırlık ve girdi değişmezliği; boş yönerge/boyutadı/düzey açıklaması, eksik boyut/düzey, sıfır ağırlık, ters aralık, aralık dışı/nonnumeric puan, tekrar düzey adı/boyutID ve sonsuz/NaN değerlerin sunucuda reddi.

İlk hedefli UI koşusu, testte process değişkeninin Node global process'i gölgelemesiyle başta durdu; test değişkeni düzeltildi. İlk çıktı researcher-0.2.1-ui-first.txt, son50başarı ayrı korunur. Önce48geçti,20şer geçmiş gösterimi eklenince son50geçti. Masaüstü genel durum/rubrik/işlem geçmişi ekranları tests/tmp/researcher-021-*.png üretildi ve görsel incelendi. Gerçek fiziksel cihaz testi değildir.

Bu tur MariaDB,42iki-workerHTTP,35export ve restore suite'leri yeniden çalıştırılmadı; önceki0.2.0sonuçları kendi kanıtıdır. Gerçek AI/hosting/kurum rubriği/cihaz-kesinti/Disco import tamamlanmış sayılmaz.

## 0.2.1 — Tüm yerel test gruplarının kapsam denetimi — 2026-10-20

Kullanıcının test/context inceleme isteği üzerine depodaki **12 çalıştırılabilir otomatik grubun tamamı güncel kod üzerinde tekrar çalıştırıldı ve geçti**. `tests/Seed.php` yardımcı fixture üreticisidir, bağımsız test grubu değildir. Ürün kodu/şema/mevcut çalışma DB'si değişmedi. Önceki sürüm sonuçlarının yeniden çalıştırılmış gibi gösterilmesi yerine bu koşuya ait yeni kanıtlar üretildi.

| Grup | Gerçek sonuç | Yeni kanıt |
|---|---:|---|
| Tarih/time.cjs | 10 geçti | test-evidence/audit-0.2.1-time.txt |
| Etiket modeli/labels.cjs | 8 geçti | test-evidence/audit-0.2.1-labels.txt |
| Analiz sırası/analysis-progress.cjs | 12 geçti | test-evidence/audit-0.2.1-analysis.txt |
| Rubrik/rubric.php | 14 geçti | test-evidence/audit-0.2.1-rubric.txt |
| Export/export-labels.php | 35 geçti | test-evidence/audit-0.2.1-export.txt |
| SQLite/run.php | 69 geçti | test-evidence/audit-0.2.1-sqlite.txt |
| Eksik alan/teslim validation.cjs | 50 geçti | test-evidence/audit-0.2.1-validation.txt |
| Genel Chrome/browser.cjs | 69 geçti | test-evidence/audit-0.2.1-browser.txt |
| Hedefli Chrome/researcher-ui.cjs | 50 geçti | test-evidence/audit-0.2.1-ui.txt |
| Erişim/atama/ek süre/yayın HTTP | 119 geçti | test-evidence/audit-0.2.1-access.txt |
| İki worker HTTP/concurrency | 42 geçti | test-evidence/audit-0.2.1-http.txt |
| MariaDB11.4.12/mysql_checks.py | 69 geçti; ayrı restore8tablo sayısı eşit | test-evidence/audit-0.2.1-mariadb.txt |

Toplam **547 bildirilen kontrol**; grupların kapsamları örtüşür,547bağımsız senaryo değildir. [Makinece doğrulanmış grup özeti](test-evidence/audit-0.2.1-summary.json) her çıktıdaki tamamlanma işaretini doğrular. İki-worker autosave bu koşuda0.032s; SLA değildir.

Bin/upgrade-sqlite.php bu tur yeniden çalıştırılmadı; mevcut DB'ye tekrar migration/yedek/seed yapılması gerekli değildi.0.2.0'daki22tablo eski alan/satır eşitliği ayrı tarihsel kanıt olarak kaldı. Test DB'leri/portlar ayrı;8088çalışma DB'sinde seed/reset/kapanış/yayın yok.

Bağlam denetimi: CONTEXT.md güncel kararlarındaki eski matris ifadesi, plan aşama tablosu, MANUAL_TESTS.md ekran haritası/T21–22vaka kapıları/export kapsamı düzeltildi.04_MANUEL_TEST_REHBERI.md geliştirme öncesi tarihsel taslak olarak açıkça işaretlendi; eski kullanıcı test sonuçları silinmedi veya yeni manuel GEÇTİ yazılmadı.

**Tamamlanmayan dış doğrulamalar:** gerçek hosting/HTTPS/proxy/alt klasör/izinler ve hosting restore; gerçek AI sağlayıcısı/modeli; gerçek kurum rubriği ve bilgilendirme/onam/saklama/çekilme süreci; fiziksel mobil/yardımcı teknoloji/farklı gerçek tarayıcılar ve Word görsel açılışı; gerçek kesinti/crash/ortak cihaz kurtarması; hedef hosting hacim/bellek testi; Disco masaüstü import.12/12yerel otomatik başarı bunları geçti yapmaz, üretim kabulü değildir.

## 0.2.2 — Öğrenci Katılım bilgilerim düğmesi kaldırıldı

- node tests/browser.cjs: **69Chrome kontrolü geçti**, ayrı sentetik SQLite/mock; test-evidence/student-0.2.2-browser.txt. Öğrenci ana sayfası/başlangıç/kayıt/sohbet/teslim/sekme ve mevcut araştırmacı akışı regresyonu.
- app.js node --check ve public/index.php php -l geçti. Statik incelemede Katılım bilgilerim düğmesi/id=profile/istemci onboarding fonksiyonu ve handler kaldırıldı. Bu düşük etkili kaldırma için yeni test eklenmedi.
- Backend/DB değişmedi; mevcut veriye yazılmadı. Diğer11grup bu tur yeniden çalıştırılmadı;0.2.1tam12grup koşusu tarihçedir. Kullanıcı manuel kabulü henüz yok; yeni batch istenmedi.

## 0.2.2 — Öğrenci rapor gösterimi (ayrı kopya)

- tests/tmp/report_demo_setup.php mevcut SQLite'tan özel ayrı kopya aldı; yalnız kopyada TEST-OGR-01/test-case-3erişimi ve atama açıldı, veri toplama kapandı, onaylıv1yayımlandı. Asıl vaka/rapor değerleri karşılaştırılarak korunma doğrulandı; mevcut raporgövdeleri veparolalar değişmedi. İlk kopyada erişim kapalı olduğu için gösterim durdu; yeni kopyada fixture erişimi açılıp son koşu tamamlandı.
- tests/tmp/report_demo_verify.cjs: **6smoke GEÇTİ**; öğrenciye doğru vaka,7.Son teslimde yalnızv1Word/PDF,teslimkilidi,rapordaveriHTTP200/içekyok,gerçekPDFveDOCXindirmesi. Kanıt test-evidence/student-report-demo.txt. Kopyaya özel CLItest oturumu kullanıldı; gerçek kullanıcının parolası istenmedi/değiştirilmedi ve yeni parola giriş doğrulaması yapılmadı.
- Geçici gösterim http://localhost:8089/;ayrısessiondizini,localhosthostçerezi.8088anaçalışmadakapanış/yayın/seed/reset yapılmadı. Gerçeköğrencikabulü/hosting/AIkanıtı değildir. Bu tur kapsamlı suite yeniden çalıştırılmadı;0.2.1/0.2.2sonuçları tarihsel koşularıdır.

## 0.2.3 — Öğrenci değerlendirme raporu sadeleştirildi

- Yeni raporda başlangıç ve nihai özgün yanıtlar, ölçüt puanı/düzeyi/kısa geri bildirim, nihai ağırlıklı toplam ve genel geri bildirim var. AI ve iç inceleme anlatımı öğrenci API/görünüm/Word/PDF'den çıkarıldı. Kaynak öğrenci metni değiştirilmez. Araştırmacı iç raporu ve üretim izi korunur; eski rapor gövdeleri değişmez. Eski sürümlerde yeni feedback yoksa uydurulmaz, yeni sürüm hazırlanır.
- Yeni feedback çağrısı onaylanmış puan/rubrik/yanıtları alır. Puanlar uygulamada hesaplanır; sağlayıcı transaction dışında çağrılır; snapshot öncesi karar parmak izi karşılaştırılır. Geçersiz/eksik/teknik üretim ifadeli çıktı rapor oluşturmaz. Belirsiz/ret puan sıfır sayılmaz. Araştırmacı öğrenci görünümünü ve aynı Word/PDF'yi önizleyebilir; onaysız dosya taslak diye işaretlenir. Onay ve vaka/kişisel süre kapısı + ayrı yayın korunur.
- **15 birim**, **75 SQLite**, **81 Chrome**, **119 HTTP erişim/yayın**, **35 export**, **75 MariaDB 11.4.12** kontrolü geçti; ayrı restore8tablosayısıeşit. Bunlar örtüşen kontrollerdir. Kanıt test-evidence/student-report-0.2.3-{unit,sqlite,browser,access,export,mariadb}.txt. Chrome yeni rapor oluşturma/önizleme/onay/yayın/öğrenci özgün yanıt/toplam/feedback/teknik kayıt dışlama/Word-PDF/mobil kapsamını içerir. İlk ek Chrome testinde kapalı details içindeki düğmeyi görünür bekleyen test-harness düzeltildi; ürün hatası değildi, ilk stdout browser-first.txt'de korunur.
- Ayrı localhost:8089 kopyasında yeni **Rapor Sürüm3** onaylandı/yayımlandı; 6smoke geçti (demo.txt). Eski kopya rapor gövdeleri ve asıl5tablonun hashleri eşit; asıl8088veritabanında kapanış/yayın/seed/reset yok. PDF2sayfa görsel incelendi; nihai bölüm yeni sayfadan başlar, başlık/yanıt ayrılığı giderildi. Ekran modalının alt tarafta açılmasına neden olan kapat düğmesi odağı da rapor başına alınarak düzeltildi. DOCX paket/XML/Türkçe/metin ve tam sayıA4ölçüsü doğrulandı. render_docx.py denendi fakat LibreOffice yok; Word görsel açılışı **tamamlandı sayılmaz**. Belgeler kanıtı documents.txt.
- Gerçek sağlayıcı çağrısı ve geri bildirimin kurum değerlendirmesi olarak kalitesi hâlâ doğrulanmadı: testler sentetik mock/TEST RUBRİĞİ kullanır. Mevcut demo0puanı gerçek başarı değerlendirmesi değildir. Tüm12grup bu tur tekrar koşulmadı;0.2.1tam denetimi tarihçedir. Diğer gerçek hosting/onam/rubrik/cihaz/kesinti/Discoimport sınırları sürer. Kullanıcı manuel kabulü yapılmış sayılmadı, yeni batch istenmedi. Commit/push yok.

## 0.2.4 — Öğrenci-vaka dosyasında okunabilir süreç ve etiketler

- Öğrenci yanıtları ve çalışma süreci ekranında JSON gösterimi kaldırıldı. Yanıtlar Türkçe alan adlarıyla, kesinleşen kayıtlar ayrı görünür. Yanıtların değişiklik geçmişi, kayıt zamanı ve Önce/Sonra metinleriyle sunulur. İşlem çizelgesinde Türkçe olay/bölüm/açıklama, bölüm ve metin filtresi, isteğe bağlı periyodik durum kayıtları ve 50 satırlık Daha fazla göster vardır. Ham JSON ve Disco CSV indirmesi korunur.
- Çalışma süreleri ve eksik ölçümler bölüm bazında görünür/odakta aralık birleşimini, kayıt sayısını ve bilinmeyen aralıkları gösterir. Çakışan sekmeler toplamda bir kez sayılır; ölçülmeyen aralıklar sıfıra çevrilmez. AI yanıtları ve bağlantı sorunları; sonuç, bekleme süresi, denemeler ve o istekte gönderilmiş bağlamı okunabilir gösterir. Sohbet payload'ı değişmedi. Analiz çıktıları, karar geçmişi ve puan/özet düzenleme de JSON gerektirmez. Açık paneller ve filtreler aynı çalışma içinde yeniden çizimde korunur.
- AI ile mesajları etiketle doğrudan araştırmacı işlemidir; tam analiz dizisini beklemez. Rol/çalışma alanı/mesaj ve tanım kimlikleri/eksik çıktı/konuşma değişikliği doğrulanır; sağlayıcı çağrısı transaction dışında yapılır. Mesaj ve tanımlar aynıysa son öneri tekrar kullanılır; analiz 6. adımının yeni önerileri de aynı cache ve kayıt yoluyla korunur. Boş AI eşleşmeleri dahil üretim grupları audit'te kayıtlıdır. Araştırmacı düzeltmeleri ve açık boş seçimler önceliklidir; AI önerileri silinmez.
- Disco CSV'ye mesaj başına sunucu zamanlı nokta olayı eklendi. AI Labels, Researcher Labels, Effective Labels, Label Source, Label Status, Message ID ve Message Role ayrı sütunlardır. Öğrenci kimliği kodlanır, mesaj metni dosyada bulunmaz. Excel/JSON etiket ve inceleme geçmişine label_batches metadata tablosu eklenir. Olaylar/yanıtlar/eski rapor gövdeleri değiştirilmez.
- **97 Chrome**, **28 etiket/Disco PHP**, **11 süreç JS**, **11 etiket JS**, **75 SQLite**, **35 export**, **119 HTTP erişim/yayın**, **75 MariaDB 11.4.12** kontrolü geçti; ayrı restore 8 tablo sayısı eşit. Kapsamlar örtüşür; bağımsız senaryo sayısı diye toplanmaz. Kanıtlar test-evidence/process-0.2.4-{browser,labels,ui-unit,label-unit,sqlite,export,access,mariadb}.txt. Chrome ayrıca yeni endpoint POST/CSRF kısıtını, gerçek Disco HTTP indirmesini ve JSON'suz puan düzenlemesini doğruladı. PHP/JS sözdizimi ve git diff --check geçti.
- İlk ek Chrome denemelerinde kapalı alt geçmişi açmadan metin bekleme ve fixture'da kaydı olmayan nihai bölümde mutlaka olay bulunacağını varsayma test kodunda düzeltildi. browser-first/second.txt bu çıktıları korur. Paneller açık tutulunca bulunan gerçek mobil süre tablosu taşması kapsayıcı kaydırmayla düzeltildi; browser-mobile-first.txt ilk kanıttır. Son 97 kontrol başarıyla tamamlandı. Sentetik yanıt/geçmiş/süre/istek ekranları görsel incelendi; fiziksel cihaz kabulü sayılmaz.
- Şema/migration değişmedi. Mevcut çalışma verisinde seed/reset, etiketleme, kapanış, erişim değişikliği veya rapor yayını yapılmadı; tüm yeni yazma testleri ayrı fixture'larda. Gerçek AI sınıflandırma kalitesi, hosting, kurum rubriği/onam ve gerçek Disco uygulamasında import hâlâ doğrulanmadı. Eski tüm-suite sonuçları bu tur yeniden çalıştırılmış sayılmaz. Kullanıcıdan yeni manuel batch istenmedi. Commit/push yok.

## 0.2.4 — Process mining çıktı yeterliliği incelemesi
Yeni ürün testi değil, mevcut CSV tanı çalıştırmasıdır. Ayrı bellek SQLite/mock fixture: 9 CSV satırı, tam dataset içinde 8 visits aralığı, Disco CSV içinde 0 visits aralığı, öğrenci/tarayıcıya ait 7 nokta olayında başlangıç=bitiş. Etiketler ayrı özellik, mesaj Activity genel. Kanıt process-mining-review-0.2.4.txt; değerlendirme PROCESS_MINING_REVIEW.md. Ayrıntılı bölüm süresi/bottleneck/sequence çıktısı tamamlandı sayılmaz. Gerçek Disco import yapılmadı; önceki suite tekrar koşulmadı; mevcut DB ve ürün kodu değişmedi.

## 0.2.5 — Sekme ziyaret süresi ve son teslimde durdurma (2026-10-21)

Kullanıcının ölçüm tanımı uygulandı: bölüm açılışı → sonraki bölüm açılışı / çalışma ekranından ayrılış / başarılı kesinleştirme arasındaki geçen süre. Yedi sekmenin tamamı kapsanır; aynı sekmeye her dönüş ayrı ziyarettir. Bu süre görünür/odaktaki ölçümden ayrıdır; 30 saniyelik heartbeat sınırı bu geçen süreye uygulanmaz. Arka plan gözlemi, eksik olay ve eşzamanlı sekme bilgisi ayrıca korunur. Açık uçlar ve geçersiz/geriye giden saatler için süre tahmin edilmez.

- Navigation.php ham kayıtları değiştirmeden navigation_visits ve navigation_milestones üretir. Açılış örneği/view_id ile yenilemeler ayrı tutulur; kesinleşen başlangıç ve son teslim nokta etkinlikleridir. Başarılı son teslimden sonra yeni sekme olayları/heartbeat istemcide durur; sunucu yeni teslim-sonrası olayları kaydetmez. Gecikmiş teslim-öncesi olaylar ve ilk istemci teslim doğrulaması korunur. Eski kayıtların teslim sonrası kısmı ziyaret/Disco çalışma akışından çıkarılır; eski DB satırları silinmez. Eski kayıtta son kapanış bilinmiyorsa sıfır veya tahmini süre verilmez.
- Çıkış/Vakalarım dönüşü section_leave ile kapanır. Gerçek sayfa ayrılışı pagehide ve keepalive ile gönderilir; iptal edilen beforeunload onayı ziyaret bitişi sayılmaz. Kuyruklar çalışma değişiminde karışmaz, çıkış sırasında yeniden oluşmaz. İlk alan değişiklikleri ortak throttle nedeniyle kaybolmaz. AI gönderim girişimindeki request_key, etiketleri doğru ziyarete saatler arasında tahmini join yapmadan bağlar.
- Araştırmacıda Bölüm geçişleri ve ziyaret süreleri: yedi bölümün ziyaret/kapanan sayısı/toplam geçen süre ve her ziyaretin başlangıç/bitiş/süresi/bitiş nedeni görünür. Eski görünür/odak tablosu korunur. Varsayılan Disco: geçişler ve süreler, kapanmış ziyaretleri ve kesinleşme/teslim noktalarını içerir; Case ID, Activity, UTC başlangıç/bitiş, Duration Seconds, Activity Code, Sequence, Previous/Next Activity, Trace ID, Edited Fields ve kalite bilgileri vardır. Açık uçlar bu CSV'ye girmez; sayıları ayrı belirtilir. Disco: ayrıntılı olaylar ve AI eski ayrıntılı profildir; AI isteğinin güncel etiketleri Activity'de de görünür, sunucu yanıt beklemeleri ayrı kalır. Dosya adları ayrıdır. Excel/JSON iki yeni navigation tablosunu da taşır.

Gerçek bu tur sonuçları: **30 Navigation/CSV/teslim durdurma**, **103 Chrome**, **50 validation**, **119 HTTP erişim/yayın**, **42 iki-worker HTTP**, **75 SQLite**, **75 MariaDB 11.4.12**, **38 export**, **28 etiket/Disco** kontrolü geçti; ayrı restore 8 tablo sayısı eşit. Kapsamlar örtüşür, bağımsız senaryo diye toplanmaz. Kanıt test-evidence/navigation-0.2.5-{unit,browser,validation,access,http,sqlite,mariadb,export,labels}.txt. İlk Navigation fixture'ında zorunlu client_key/quality alanları eksikti; test kurulumu düzeltildi, unit-first.txt ilk çıktıyı korur. Ürün hatası değildi.

Navigation fixture kullanıcının örneğiyle tüm yedi sekmeyi, tekrarlı dönüşleri, 10:00 → 10:02 = 120 saniyeyi, son teslimde bitişi, açık/geçersiz süreleri ve istemci/sunucu saati farklıyken request-key etiket eşleşmesini doğrular. Chrome son teslimden sonra yedi sekmenin tümünü yeniden dolaşarak seq/kuyruk artmadığını, yeni varsayılan CSV ve ziyaret tablosunu doğrular; sentetik tablo görsel incelendi. Fiziksel cihaz/gerçek crash/diske gönderilemeyen kapanış ve gerçek Disco import kabulü değildir. Son CSV metadata/dosya adı düzenlemesi PHP lint ve Navigation fixture ile doğrulandı.

Mevcut çalışma DB'sinde seed/reset, migration, vaka kapanışı/açılışı, etiketleme veya rapor yayını yapılmadı; test yazmaları ayrı fixture'larda. Sonuçlar yeni üretim/hosting/gerçek AI/rubrik/onam kabulü değildir; eski tüm-suite sonuçları bu tur tekrar koşuldu sayılmadı. Yeni manuel batch yok; commit/push yok.

## 0.2.6 — Açıklayıcı ziyaret adları (2026-10-21)

Yalnız ayrı sentetik ortamlarda: tests/navigation.php 36 kontrol, tests/browser.cjs 105 Chrome kontrolü, tests/export-labels.php 38 kontrol, tests/process-data.cjs 11 kontrol geçti. Navigation.php ve Export.php PHP lint, process.js Node syntax geçti. Kanıtlar test-evidence/navigation-0.2.6-{unit,browser,export}.txt; Chrome ekranı tests/tmp/navigation-visits.png görsel olarak incelendi. Yeni kontroller hedef sekmenin adı, kilit bilgisinin ayrı kalması, görüntüleme/güncelleme ayrımı ve CSV sütunlarını kapsar; süre sınırları ve teslim sonrası takip koruması da yeniden doğrulandı. İlk unit denemesinde test yanlış ziyaret indeksini kontrol etti; fixture beklentisi düzeltildi, ilk çıktı unit-first.txt içinde korunuyor. Bu tur MariaDB/HTTP/genel SQLite grupları yeniden çalıştırılmadı; önceki sonuçlar tarihsel kanıttır. Gerçek kullanıcı kabulü veya Disco uygulamasına import testi yapılmış sayılmaz.

## 0.2.7 — AI sohbeti etkinlik adlarında mesaj etiketleri (2026-10-21)

Ayrı sentetik ortamlarda tests/navigation.php 49, tests/browser.cjs 109 Chrome, tests/export-labels.php 38, tests/process-data.php 28, tests/run.php 75 SQLite kontrolü geçti. App/Navigation/Export PHP lint ve process.js Node syntax geçti. Kanıt: test-evidence/navigation-0.2.7-{unit,browser,export,labels,sqlite}.txt. Mesaj gönderilmeyen ziyaret, yalnız soru taslağı, gerçek request_key eşleşmesi, AI etiketi, araştırmacı düzeltmesi/boş seçim, çoklu soru/tekrar etiket, başka uygulamaya ait eşleşmeyi dışlama, süreyi çoğaltmama ve UI/Disco Activity tutarlılığı doğrulandı. Chrome görüntüsü tests/tmp/navigation-intents.png görsel incelendi. İlk unit fixture'ında gerekçe eksikti; ilk Chrome kontrolünde kapalı details içeriği okunmaya çalışıldı; testler düzeltilip tekrar çalıştırıldı, ilk çıktılar *-first.txt içinde korunuyor. MariaDB/HTTP grupları bu tur tekrar koşulmadı. Gerçek sağlayıcı/Disco import doğrulanmadı; çalışma DB'sinde etiket üretimi veya veri değişikliği yapılmadı.

## 0.2.8 — Sıralı soru/yanıt/görüntüleme ve ayrı süreler (2026-10-21)

Kullanıcının son kuralına göre mesaj → yanıtın gelişi → görüntüleme ayrı etkinliklerdir. tests/navigation.php 69 kontrol, tests/browser.cjs 113 Chrome kontrolü, tests/export-labels.php 38, tests/process-data.php 28, tests/run.php 75 SQLite kontrolü geçti. Navigation/App/Export PHP lint ve app.js/process.js Node syntax geçti. Kanıtlar: test-evidence/navigation-0.2.8-{unit,browser,export,labels,sqlite}.txt. İlk unit beklentisindeki eski CSV satır sayısı yeni segmentlere göre güncellendi; ilk çıktı unit-first.txt içinde korundu.

Sentetik 60 saniyelik örnek: görüntüleme 10 → Bilgi istedi 10 → yanıt geldi 0 → görüntüleme 10 → Eleştiri istedi 15 → yanıt geldi 0 → görüntüleme 15. Toplam 60; soru ve görüntüleme süreleri ayrı. Aynı zamanlı yanıt/görüntüleme sırası, tekrarlı amaç, boş araştırmacı seçimi, açık son sınır, ters saat, başka bölümde gelen yanıt, hata yanıtının başarılı gösterilmemesi, CSV ayrı süreleri doğrulandı. Chrome gerçek tarayıcı akışında bir yanıtın bir kez kaydı, soruyu kapatıp görüntülemeyi başlatması ve toplamların eşitliği doğrulandı. tests/tmp/navigation-intents.png sentetik ekranı geliştirici görsel kontrolüdür; kullanıcı manuel kabulü değildir. İstemci toparlanma sorgusu da yanıt gelişini kaydeder; aynı sonuç çift kaydedilmez, vaka değişmişse eski yanıt yeni vakaya yazılmaz. MariaDB/HTTP grupları bu tur yeniden çalıştırılmadı. Gerçek sağlayıcı, gerçek Disco import ve fiziksel cihaz doğrulaması hâlâ açık. Mevcut çalışma DB'sine müdahale yok.

## 0.2.9 — Eski gönderim olayı olmayan sorular (2026-10-21)

Kullanıcı ekran bildirimi: yalnız AI görüntüleme/yanıt satırları, amaç soruları görünmüyordu. Salt okunur SQLite incelemesi (PRAGMA query_only=ON) iki öğrenci sorusu ve iki yanıt geliş kaydını, sıfır message_send olayını doğruladı. İlk sorunun Bilgi istedi etiketi zaten vardı; kullanıcı ikinciyi sonradan Eleştiri istedi olarak etiketledi, iki seçim DB'den salt okunur doğrulandı. Bu bir etiket eksikliği sorunu değildi: eski sürümün gönderim telemetrisi yoktu. Düzeltmeden sonra aynı salt okunur view'da iki eski amaç sorusu görünüyor; süreleri null ve saat kaynağı server_message. Özgün kayıtlar değiştirilmedi.

Ayrı sentetik testler: navigation 77, Chrome 114, export 38, etiket/süreç 28, SQLite 75 geçti; Navigation/App/Export PHP lint ve process.js Node syntax geçti. Kanıtlar test-evidence/navigation-0.2.9-{unit,browser,export,labels,sqlite,local-readonly}.txt. Eski soruların güncel etiketlerle görünmesi, boş/eksik etiket durumları, CSV bilinmeyen süreyi boş bırakması, modern soruyu iki kez eklememe ve eski soru HTML açıklaması doğrulandı. Chrome sonucu sentetik arayüz doğrulamasıdır; yerel eski kaydın tarayıcıda kullanıcı kabulü henüz tamamlandı sayılmaz. Kullanıcı kendi isteğiyle yeni öğrenciyle deneyeceğini bildirdi; sonucu bekleniyor. MariaDB/HTTP grupları yeniden çalıştırılmadı. Gerçek AI/hosting/Disco import kabulü açık.

## 0.2.10 — Gönderim ve AI hazırlık beklemesinin ayrılması (2026-10-21)

Ayrı sentetik ortamlarda navigation 82, Chrome 115, export 38, etiket/süreç 28, SQLite 75 kontrolü geçti. Navigation/Export PHP lint, process.js Node syntax ve ilgili git diff whitespace kontrolü geçti. Kanıtlar: test-evidence/navigation-0.2.10-{unit,browser,export,labels,sqlite}.txt. İlk unit denemesinde eski süreli soru satırı beklentisi yeni anlık gönderim + bekleme düzenine göre güncellendi; ilk çıktı unit-first.txt içinde korunuyor.

Sorunun amacı araştırmacı seçimiyle anlık gönderimde kalır; AI_WAIT ayrı süreli kayıttır, ardından yanıt noktası ve görüntüleme gelir. Eşleşme aynı trace/request üzerinden, bölüm değişse de sürer. 40 saniyelik sentetik zaman örneği: 09:00:10 gönderim → 09:00:20 nihai bölüme geçiş → 09:00:50 yanıt gelişi; AI hazırlığı 40 saniye, nihai bölüm ziyareti ayrı 50 saniye. Bu aralıklar farklı kulvardadır, birbirine toplanmaz. Aynı 40 saniye CSV Duration Seconds=40, Timeline Lane=ai_wait, Response Section=final olarak doğrulandı. Chrome gerçek tarayıcı akışında anlık etiketli gönderim, ayrı ölçülmüş AI hazırlığı, tek yanıt geliş kaydı ve görüntülemeye dönüş geçti. Gerçek sağlayıcıda 40 saniyelik çağrı çalıştırılmış sayılmaz. Eksik yanıtta bekleme süresi sonraki sorudan/bölümden uydurulmaz; eski soru noktaları korunur. Mevcut çalışma DB'sinde işlem yapılmadı. MariaDB/HTTP grupları bu tur yeniden koşulmadı; gerçek AI/hosting/Disco import kabulü açık.

## GitHub teslim kontrolü — 2026-10-21

Commit öncesi 41 PHP/JS kaynak/test dosyasının syntax kontrolü geçti; git diff --check temiz. 198 Git adayı dosyada sır örüntüsü bulunmadı; yerel yapılandırmadaki dolu anahtar/parola değerleriyle eşleşme taramasında bulgu yok. Özel çalışma verileri ve runtime dizinleri Git dışında doğrulandı. Bu teslim sırasında tüm regresyon grupları yeniden çalıştırılmadı; 0.2.10 test kanıtları yukarıda tarihsel kapsamıyla korunur.

## Nihai ciddiyet ve puan tablosu — 2026-10-21 yerel

Ayrı sentetik ortamda tests/run.php 81, tests/validation.cjs 50, tests/browser.cjs 119 ve tests/process-data.cjs 11 kontrol geçti. Kanıtlar test-evidence/scores-final-severity-{domain,validation,browser}.txt. Yeni alanın eksikliği/aralığı/tam sayı kuralı, 0 puanının kayıt/teslim snapshotı/veri çıktısında korunması, teslim gereksinimleri, altı satırlı araştırmacı tablosu, nihai güven/ciddiyetin görünmesi ve eski eksik değerin yanıt yok gösterilmesi doğrulandı. Araştırmacı tablosu tests/tmp/researcher-score-table.png ile görsel incelendi. PHP/JS syntax ve whitespace kontrolleri geçti. İlk sandbox domain denemesi dosya kütüphanesi erişiminde durdu; tam koşu normal izinle ayrı test DB üzerinde geçti. Çalışma DB değiştirilmedi; fiziksel cihaz, gerçek sağlayıcı, hosting ve MariaDB bu tur test edilmedi.

## Kesinleşmiş yanıtları karşılaştırma — 2026-10-21

2026-10-21 yerel: Kesinleşmiş başlangıç ve son teslim yanıtları tek karşılaştırma tablosunda yan yana gösteriliyor. Sekiz satır: ön bilgi, güven, zorluk, ciddiyet, temel problem, karar, gerekçe, yansıtma. Yalnız immutable snapshotlar kullanılır; taslakla doldurulmaz. Aşamada sorulmayan, henüz kesinleşmeyen ve eski kayıtta yanıtı bulunmayan alanlar ayrı açıklanır. 123 Chrome kontrolü, JS syntax ve diff whitespace geçti; tablo görseli incelendi. İlk Chrome denemesinde iki tabloyu birlikte seçen eski test seçicisi daraltıldı, tam koşu tekrar geçti. Çalışma DB değişmedi; commit/push yok. Kanıt: test-evidence/snapshot-comparison-browser.txt; görsel: tests/tmp/researcher-snapshot-comparison.png.

## Tablolarda uzun metin — 2026-10-21

2026-10-21 uzun metin yerleşimi: Karşılaştırma hücrelerinde açık pre-wrap, başlık/puan hücrelerinde normal satır kırma ve sabit sütunlar tanımlandı; genel tablo nowrap kuralı bu tablolarda geçersiz kılındı. 1440/768/390 px genişliklerde uzun paragraflar ve 60.000 boşluksuz karakterle 30 Chrome yerleşim kontrolü geçti: hücre taşması yok, sayfa yatay taşmıyor, metin bütünü korunuyor, satırlar uzuyor ve mobilde yatay kaydırma yalnız tablo kapsayıcısında. Kanıt: test-evidence/table-long-text.txt; test: tests/table-layout.cjs. JS syntax/diff kontrolü geçti. DB erişimi yok; commit/push yok.

## Yanıt değişiklik geçmişi tablosu — 2026-10-21

2026-10-21 yerel: Yanıt değişiklik geçmişindeki kayıt açılır kutuları yerine Zaman/kayıt, Değişen alan, Önceki yanıt, Yeni yanıt tablosu eklendi. En yeni kayıt önce, alan filtresi, 30 satırlık kademeli gösterim ve uzun metinde tam metni aç seçeneği var. Önceki revizyon eksikse karşılaştırılamıyor açıklaması korunur. Alan filtresi aynı dosya yeniden çizildiğinde korunur. 125 Chrome ve uzun metinlerle 30 yerleşim kontrolü geçti; tablo görseli incelendi. Kanıtlar test-evidence/revision-table-{browser,long-text}.txt. İlk test denemesi test dosyasında değişken adı çakışması nedeniyle başlamadı; düzeltme sonrası tam koşu geçti. DB verileri değişmedi; commit/push yok.

## İşlem çizelgesi tablosu — 2026-10-21

2026-10-21 yerel: Öğrencinin işlem çizelgesi Zaman, Bölüm, İşlem, İlgili alan, Açıklama ve ayrıntılar sütunlarıyla tabloya dönüştürüldü. Bölüm/arama filtreleri, varsayılan gizli periyodik kayıtlar ve 50 satırlık kademeli gösterim korunur. Teknik bilgiler ve kopyalanan metin satır içinde açılır. Sunucuya ulaşma sırası korunur. 127 Chrome ve 30 uzun metin yerleşim kontrolü geçti; tablo görseli incelendi. İlk Chrome koşusu 4 kontrolden sonra giriş gezinmesinde zaman aşımına uğradı; aynı test tekrarında tamamı geçti. Kanıtlar test-evidence/event-table-browser.txt, event-table-browser-retry.txt ve event-table-long-text.txt. Syntax/diff kontrolü geçti. Çalışma DB değişmedi; commit/push yok.

## 0.2.11 — Uygulama dışında süre modeli

**2026-10-21 · 0.2.11 yerel:** Onaylanan uygulama içi/dışı süre modeli uygulandı. Başka Chrome sekmesine/uygulamaya geçildiğinde bölüm aralığı kapanır; dönüşe kadar APP_OUTSIDE / Uygulama dışında aralığı oluşur, dönüşte yeni bölüm aralığı başlar. Yeni istemci navigation_version=2, app_active, pending_request ve app_presence kaydeder (görünür + odakta); eski kayıtlarda yalnız kayıtlı visible durumları kullanılır, eksik sınırlar tahmin edilmez. Kilitli AI erişimi açık adlandırılır. Aynı AI sorusunun beklemesi dışarıda sayılmaz, dönüşte gerekirse sürer; yanıt dışarıda geldiyse gerçek anlık kayıt korunur, sahte görüntüleme/soru oluşturulmaz. API/araştırmacı/Excel-JSON/Disco aynı navigation hesaplarını kullanır; CSV Measurement Version ve Presence Source içerir. Süre özet ekranı aynı navigation kaynağına bağlandı; eski heartbeat/30 sn odak hesabı ana ekran ölçümü değil. Teknik sunucu istek süresi ayrı adlandırıldı. 26 presence, 82 navigation, 127 genel Chrome, 9 gerçek Chrome sekme geçişi, 81 SQLite, 39 export, 28 süreç ve 11 süreç UI kontrolü geçti. Mevcut çalışma veritabanına yazma/seed/reset yok; commit/push yok.

Kanıtlar: test-evidence/presence-domain.txt (26), presence-navigation.txt (82), presence-regression-browser.txt (127), presence-browser-real-tabs.txt (9), presence-regression.txt (81), presence-export.txt (39), presence-process.txt (28). 11 süreç UI kontrolü konsolda geçti; PHP/JS syntax ve git diff whitespace geçti. Gerçek Chrome testi varsayılan odak emülasyonunu noDefaults CDP bağlantısıyla devre dışı bırakıp iki gerçek sekme arasında geçiş yaptı; ayrı geçici profil ve DB kullandı. İlk odak emülasyonlu testler sekmeden ayrılmayı göremedi ve zaman aşımına uğradı; geçmiş failure presence-browser.txt içinde. İlk unit denemelerinde yeni aralıklar için kimlikler ve önceki görünürlük/etiket beklentileri düzeltildi. Başarısız denemeler tests/tmp/presence-*.txt altında durur. Gerçek AI sağlayıcısı, hosting ve fiziksel mobil bu tur test edilmedi. Görsel: tests/tmp/presence-navigation.png.

## 0.2.11 — Genişletilmiş süre/geçiş yeniden testi

2026-10-21 süre/geçiş yeniden doğrulaması: 49 hesaplama/presence, 82 navigation, 22 gerçek Chrome geçiş/AI/minimize, 127 genel Chrome, 81 SQLite, 39 export ve 28 süreç kontrolü yeniden geçti. Gerçek Chrome: kilitli AI sekmesinden ayrılma/dönüş; 6 sn gecikmeli mock yanıtın dışarıdayken gelişi; ikinci soruda yanıt gelmeden dönüş; Nihai bölümünde pencereyi küçültme/geri açma; gerçek CSV indirme. Zaman fixtureları: iki ayrılma, 3 saat dışarıda, aynı zaman damgası, eski görünürlük kayıtları, eksik dönüş, aradaki eksik olaylar, teslim sonrası izlememe, kısmi/hatalı yanıt, farklı bölümde yanıt. Yeni testte bilgisayar saati geri giderse AI beklemesinin fazla süre üretebildiği bulundu ve düzeltildi: etkilenen aralıklar invalid_clock/null olur, süre CSV’sine sayısal süre olarak çıkmaz. Normal akış kuralları değişmedi. Mevcut çalışma DB’sine dokunulmadı. Kullanıcıya tek kısa manuel Vaka→dışarı→Vaka→Başlangıç senaryosu verilecek; kullanıcı kabulü henüz bekleniyor. Commit/push yok.

Kanıtlar: test-evidence/presence-expanded-domain.txt (49), presence-recheck-navigation.txt (82), presence-expanded-browser-final.txt (22), presence-recheck-browser.txt (127), presence-recheck-domain.txt (81), presence-recheck-export.txt (39), presence-recheck-process.txt (28). İlk clock edge failure tests/tmp/presence-clock-edge.txt içindedir. Minimize testinin ilk denemelerinde otomasyon, bölümün asenkron geçişini/görünürlüğün dönüşünü beklemeden ilerledi; adımlar ve ekran dışı pencerenin occlusion ayarı düzeltilip son koşu tamamlandı. Ara denemeler presence-expanded-browser.txt ve presence-expanded-browser-retry.txt içinde korunur. Gerçek sekme/Windows küçültme CDP noDefaults ile ayrı Chrome profilinde çalıştırıldı. Gecikmeli AI sentetik mock’tur; gerçek sağlayıcı testi değildir. Gerçek Disco uygulamasına import bu tur yapılmadı.

## 0.2.12 — Sayfa içi onay ve Vaka dışında aralığı

**2026-10-21 · 0.2.12 yerel:** Native confirm kaynaklı sahte uygulamadan ayrılma düzeltildi: öğrenci başlangıç/teslim/kurtarma/yeniden deneme/kirli çıkış onayları await edilen sayfa içi dialog kullanır. Onayla, Vazgeç ve Escape destekli; gerçek başka pencereye geçiş onay açıkken de kaydedilir. Eski ham kayıtlar silinmedi/yeniden yazılmadı; geçmiş native confirm artefaktları olduğu gibi korunur. Vakalarım’a dönüş ile aynı vakayı yeniden açma arasında CASE_AWAY / Vaka dışında türetilir; APP_OUTSIDE ile karıştırılmaz, bölüm/app içi süreye eklenmez. Geri açılış gözlenmeden süre uydurulmaz; paralel/salt okunur sekmede sahte boşluk oluşturulmaz. UI/API/Excel-JSON/Disco aynı kaynağı kullanır. Hiç ziyaret edilmeyen bölümlerde Ziyaret yok gösterilir. 55 presence, 82 navigation, 15 gerçek Chrome onay/boşluk, 127 genel Chrome, 50 doğrulama, 81 SQLite ve39 export kontrolü geçti. Çalışma DB değişmedi; commit/push yok. Yeni tek manuel senaryo kullanıcıya verilecek; 0.2.12 kullanıcı kabulü bekleniyor.

Kanıtlar: test-evidence/confirmation-gap-domain.txt (55), confirmation-gap-navigation.txt (82), confirmation-gap-browser-final.txt (15), confirmation-regression-browser-final.txt (127), confirmation-validation.txt (50), confirmation-regression-domain.txt (81), confirmation-export.txt (39). İlk custom Chrome denemesinde test, asenkron vaka açılışını beklemeden work okudu; bekleme eklendi. İlk genel Chrome testinde bölüm sayımına yeni away kategorisi girdi; yedi uygulama bölümü sayımı dışında tutularak tekrar koşu geçti. Bu denemeler confirmation-gap-browser.txt ve confirmation-regression-browser.txt içinde korunur. Gerçek Chrome kabul/iptal/Escape, onay açıkken ayrılış/dönüş, tek kesinleştirme, sahte dışarı olmaması, home/reopen boşluğu ve gerçek CSV test edildi. Görsel tests/tmp/confirmation-gap-fixed.png incelendi. PHP/JS syntax ve whitespace geçti. Gerçek AI/hosting/Disco uygulama importu test edilmedi.

## 0.2.12 — Eski açık uç içeren geçmişle regresyon

2026-10-21 0.2.12 kullanıcı yeni ekran incelemesi: TEST-OGR-01 / Sınıfta dijital araç kullanımı, son onay 13:38:06.747–13:38:17.978 (11.231sn) kesintisiz submit_confirmed ile kapandı; sahte native ayrılış yok. Önceki 13.384 ve8.273sn dışarı aralıklarında visible=false gözlemi var. Vakalarım13:36:49.989→yeniden açılış13:37:24.941 arası34.952sn ilk hesapta görünmüyordu: eski günden kalma end=null trace, sonsuza kadar açık paralel pencere varsayılıp boşluğu engelliyordu. Düzeltildi: sadece bitişi bilinen ve gerçekten çakışan aralık bu boşluğu engeller. Eski açık uçlar bilinmiyor kalır; kapanış uydurulmaz. Salt okunur gerçek DB yeniden hesabı Vaka dışında34.952sn doğruladı. 57 presence,82navigation,16 gerçek Chrome onay/boşluk testi geçti; Chrome fixture eski açık uç kaydını da içerir, CSV/UI doğrulandı. Çalışma DB yazılmadı; yeni manuel tekrar gerekmiyor. Kullanıcının düzeltme sonrası ekran teyidi henüz yok. Commit/push yok.

Kanıtlar: test-evidence/stale-gap-domain.txt (57), stale-gap-navigation.txt (82), stale-gap-browser.txt (16). Önce hata tests/tmp/stale-gap-first.txt ile yeniden üretildi; düzeltmeden sonra geçti. Aynı tarayıcı testinde native onayın yerini alan sayfa içi onay da tekrar doğrulandı. Gerçek kullanıcı kayıtları yalnız read-only sorgulandı.


## Farklı karma öğrenci yolu — 0.2.12

2026-10-21: Kullanıcının ek test isteği için tests/journey-browser.cjs ile ayrı SQLite ve gerçek Chrome profilinde farklı karma yol denendi. Vaka A → Vakalarım → Vaka B'de iki bölüm → A'ya dönüş → gecikmeli AI sorusu → nihai bölüme geçiş → başka Chrome sekmesindeyken yanıt gelişi → uygulamaya dönüş → AI/yansıtma → Vakalarım → A'ya dönüş → teslim onayını iptal → yeniden onay/teslim → teslim sonrası gezinme. Eski kapanmamış trace fixture'ı da vardı. 27 kontrol geçti: bilinen aralıklarda sıfır boşluk/çakışma, her sürenin ham zaman farkına eşitliği, bölüm/uygulama dışı/vaka dışı ayrımı, tek yeni soru/yanıt, onayda sahte ayrılış olmaması ve teslim sonrası takibin durması. Gerçek indirilen CSV'de süreler, sıra, önceki/sonraki etkinlik ve benzersiz kimlikler; araştırmacı UI/API toplamları karşılaştırıldı. Gözlenen A yolunda 5.394sn bölüm + 6.266sn uygulama dışında + 2.853sn vaka dışında = 14.513sn; AI bekleme kulvarı bu toplama yeniden eklenmedi. Eski açık uç null kaldı. Kanıt: test-evidence/journey-mixed-final.txt; sentetik ekran tests/tmp/journey-mixed.png görsel incelendi. Çalışma DB'si ve ürün kodu bu tur değiştirilmedi; yeni manuel test istenmiyor. Gerçek AI yerine 6sn gecikmeli mock kullanıldı; gerçek hosting/sağlayıcı/Disco import kabulü sayılmaz. Commit/push yok.

İlk deneme journey-mixed-first.txt içinde korunur: test AI isteği sürerken nihai alan autosave yanıtını beklediği için tek worker PHP sunucusunda yanıt başka sekmeye geçmeden tamamlandı; bu nedenle dışarıdayken yanıt beklentisi sağlanmadı. Testin nihai alan hazırlığı AI isteğinden önceye taşındı; gerçek dışarı geçiş sırasında gecikmeli yanıtla son koşu 27/27 geçti. Bu test sıralaması sorunudur; ürün hatası olarak raporlanmadı. Önceki 57/82/16 ve diğer regresyon sonuçları tarihsel kanıttır, bu tur yeniden koşulmuş sayılmaz.


## 0.2.12 — Uygulamadan ayrılmadan bölüm geçişi manuel kontrolü

2026-10-21 kullanıcı ekranları ve salt okunur DB incelemesi: TEST-OGR-01 / Grup çalışmasında adalet, yeni view 13:52:19.123–13:53:25.015. Sekiz bölüm aralığı, sıfır APP_OUTSIDE ve sıfır app_active=false gözlemi. İki AI soru/yanıt akışı ekranda ayrı; son bölüm 13:53:23.002–25.015 (2.013sn) submit_confirmed ile kapandı. Başlangıç kesinleştirme 13:52:46.775, AI açılışı46.777; 2ms ayrı olay sınırı korunur, süre uydurulmaz. Bilinen bölüm toplamı65.890sn. Aynı vaka 20 Ekimden eski geçmiş içeriyor; önceki dışarı/açık uçlar bu yeni teste ait değil ve silinmedi. Yeni manuel senaryoda sahte ayrılış görülmedi. Kanıt test-evidence/manual-no-exit-check.txt. Kod/çalışma DB değişmedi; önceki otomatik testler bu tur yeniden koşulmadı.
