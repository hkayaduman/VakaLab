# VakaLab

Öğretmen adaylarının pedagojik vakalarda AI öncesi muhakemesini, vaka bazında AI etkileşimini, nihai kararını ve yansıtmasını toplayan Türkçe araştırma uygulaması. PHP/MySQL + vanilla JS/CSS; shared hosting için kalıcı worker gerektirmez.

Öğrenci paneli: altı çalışma bölümü + ayrı Son teslim sekmesi, başlangıç/profil atomik kilidi, revision/autosave, aynı hesap için sekme kurtarması, tek AI isteği, nihai/yansıtma tek teslimi. Araştırmacı: dinamik matris, katılımcı/vaka/atama, sürümlü rubrik/etiket, olaylar/taslaklar/süreler, ayrı sıralı analiz, kararlar, rapor snapshot/onay/yayın, gerçek DOCX/PDF/XLSX/CSV/JSON indirme.

**Araştırmada kullanıma onay verilmedi.** Gerçek rubrik/onam ve AI sağlayıcısı bekleniyor. Mock sentetiktir. Gerçek hosting ve fiziksel mobil testleri yapılmadı. Çalıştırılan testlerin kapsamı [TEST_RESULTS.md](TEST_RESULTS.md), açık işler [plan.md](plan.md), devam bağlamı [CONTEXT.md](CONTEXT.md).

## Yerel başlatma

PHP 8.3 ve gereken eklentiler, Composer; [kurulum rehberindeki](INSTALL_SHARED_HOSTING.md) bağımlılıklar. Yerel `.tools/php/php.exe` bu çalışma sırasında Windows testleri için edinilmiştir; sunucu dağıtımına eklenmez.

```text
composer install --no-interaction --prefer-dist
php bin/console.php check
```

config/example.php → config/local.php kopyalayın. MySQL test DB adı `_test` içersin. SQLite hızlı yerel deneme için örnek değişiklik:

```php
$c = require __DIR__.'/example.php';
$c['dsn'] = 'sqlite:'.__DIR__.'/../storage/vakalab_test.sqlite';
$c['db_user'] = ''; $c['db_password'] = '';
$c['https'] = false;
return $c;
```

`pdo_sqlite` gerekir; üretim hedefi MySQL'dir. [TEST_DATA.md](TEST_DATA.md) komutlarıyla migrate + seed yapın; sonra:

```text
php -S 127.0.0.1:8088 -t public
```

[Yerel uygulama](http://127.0.0.1:8088/) → test hesapları TEST_DATA.md'de. PHP'nin Windows built-in sunucusu tek worker'dır: gecikmeli AI sırasında aynı sunucudaki autosave sırada bekleyebilir. Bu, uygulama session kilidi değildir; concurrency otomatik testleri iki bağımsız PHP worker ile çalışır. Hostingde en az iki worker ile tekrar deneyin. Üretimde built-in server kullanılmaz.

## Dizinler

| Yol | İşlev |
|---|---|
| public/ | Tek web kökü; index.php, api.php, assets |
| app/ | Auth, Db, domain, AI, analiz, telemetri, export |
| config/ | example.php ve git dışı local.php |
| migrations/001.sql | FK/unique içeren ilk şema |
| bin/console.php | check, migrate, seed, researcher, researcher-sql |
| storage/ | Web dışı geçici dosyalar / yerel test DB |
| tests/ | Alan, HTTP concurrency, Chromium, MariaDB/restore testleri |
| vendor/, composer.lock | Sabitlenmiş PHP dosya üretim kütüphaneleri |

## Testleri yeniden çalıştırma

```text
php tests/run.php
python tests/http_checks.py
node tests/browser.cjs
python tests/mysql_checks.py
```

HTTP testleri PHP_BIN ortam değişkenini veya Windows `.tools/php/php.exe` yolunu kullanır. Browser için Playwright paketini development ortamına kurun; `PLAYWRIGHT_PATH`, gerekirse `CHROME_BIN` belirtin. MariaDB testi için `MARIADB_HOME` taşınabilir Windows MariaDB dizinine işaret etmeli. Hostingde bu araçlar gerekmez. Otomatik testler benzersiz ayrı disposable DB üretir; normal kullanıcı/üretim verisini temizlemez.

## Belgeler

[Shared hosting kurulumu](INSTALL_SHARED_HOSTING.md) · [Sentetik veri](TEST_DATA.md) · [Manuel test](MANUAL_TESTS.md) · [Test sonuçları](TEST_RESULTS.md) · [Mimari](ARCHITECTURE.md) · [Veri sözlüğü](DATA_DICTIONARY.md) · [Olaylar](EVENT_CATALOG.md) · [AI girdileri](AI_INPUT_CONTRACTS.md) · [Hatalar](BUGS.md).

Kabul edilmiş özgün şartnameler 01_OGRENCI_PANELI.md ve 02_ARASTIRMACI_PANELI.md; 03_CODEX_GELISTIRME_PROMPTU.md görev kaynağıdır. Kalıcı silme yoktur. Veri toplama kapanışı + rapor onayı + ayrı yayın olmadan öğrenciye puan/rapor erişimi verilmez.
