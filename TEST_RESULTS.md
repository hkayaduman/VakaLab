# Gerçek test sonuçları — 2026-10-20

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
