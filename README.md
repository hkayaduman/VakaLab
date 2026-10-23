# VakaLab

**GitHub teslimi · 23 Ekim 2026:** Aşağıdaki yerel hazırlık değişiklikleri, PHP güvenlik/8.4.27 kanıtları ve eski teslim düzeltmesi `ee31172` commit'iyle `origin/main` dalına gönderildi. Önceki “henüz commit/push edilmedi” kayıtları teslim öncesi durumu anlatır. Hosting dağıtımı yapılmadı; gerçek AI/hosting/kurum kabulü açık.

0.2.18 yerel güncelleme: Beş bölümlü değerlendirme ve tek güncel öğrenci raporu. Rapor alanı yalnız Öğrenci raporunu hazırla / Word / Excel / Öğrenciye göster düğmeleridir; geçmiş listeleri gösterilmez. Yeniden inceleme tamamlanınca aynı rapor güncellenir, öğrenciye tekrar açılması gerekir. Vaka veri toplama kapanış kuralı korunur. Yeni akış için Ctrl+F5; ayrıntılar CONTEXT.md.

**23 Ekim 2026 yerel durum:** Yerel HEAD `9098ff7`; uzak GitHub dalı bu incelemede sorgulanmadı. Puan–rubrik düzeyi eşleşmesi ve dolu gerekçe doğrulaması mevcut. Eski v1 teslimler sonradan eklenen nihai ciddiyet ölçümü olmadan analiz edilebilir; ölçülmemiş alan analiz çıktısında belirtilir, eski yanıtlar değiştirilmez. Yeni teslimlerde nihai ciddiyet zorunludur. PHP güvenlik güncellemesi ve bu yerel düzeltme henüz commit edilmedi. Güncel doğrulamalar [TEST_RESULTS.md](TEST_RESULTS.md), kalan dış kabuller [RELEASE_READINESS.md](RELEASE_READINESS.md).

0.2.1: Genel durumda matris yerine vaka/öğrenci listesi, vaka kartında atanan öğrenci diyaloğu; JSON gerektirmeyen rubrik boyut/düzey formu ve okunaklı/aranabilir/JSON indirilebilir işlem geçmişi. Şema değişikliği yok. Güncel koşular TEST_RESULTS.md, kullanım RELEASE_READINESS.md.

**0.2.0 — Araştırmacı paneli sadeleştirildi:** Ayrı araştırmacı çalışma alanları, kısa kullanıcı formu, toplu atama, etiket/rubrik Sil, vaka bazında veri toplama ve kişisel ek süre; vaka seçerek Excel/JSON veya Disco CSV indirme. [Kullanım ve yayın durumu](RELEASE_READINESS.md). Mevcut kurulumda önce özel yedek ve migration 002 gerekir; yerel SQLite için `php bin/upgrade-sqlite.php` yedeği ve eski alanların korunmasını doğrular.

Öğretmen adaylarının pedagojik vakalarda AI öncesi muhakemesini, vaka bazında AI etkileşimini, nihai kararını ve yansıtmasını toplayan Türkçe araştırma uygulaması. PHP/MySQL + vanilla JS/CSS; shared hosting için kalıcı worker gerektirmez.

Öğrenci paneli: altı çalışma bölümü + ayrı Son teslim sekmesi, başlangıç/profil atomik kilidi, revision/autosave, aynı hesap için sekme kurtarması, tek AI isteği, nihai/yansıtma tek teslimi. Araştırmacı: vaka/öğrenci seçimi, katılımcı/vaka/atama, sürümlü rubrik/etiket, olaylar/taslaklar/süreler, beş bölümlü değerlendirme ve dört insan kararı, tek güncel rapor ve Öğrenciye göster; gerçek DOCX/XLSX/CSV/JSON indirme. Tarihsel v1 raporlar ve PDF backend desteği korunur.

**Araştırmada kullanıma onay verilmedi.** Gerçek rubrik/onam ve AI sağlayıcısı bekleniyor. Mock sentetiktir. Gerçek hosting ve fiziksel mobil testleri yapılmadı. Çalıştırılan testlerin kapsamı [TEST_RESULTS.md](TEST_RESULTS.md), açık işler [plan.md](plan.md), devam bağlamı [CONTEXT.md](CONTEXT.md).

## Yerel başlatma

PHP 8.3.36+ (8.4 serisinde 8.4.27+, 8.5 serisinde 8.5.12+) ve gereken eklentiler, Composer; [kurulum rehberindeki](INSTALL_SHARED_HOSTING.md) bağımlılıklar. Yerel `.tools/php/php.exe` Windows testleri içindir; sunucu dağıtımına eklenmez. 21 Ekim 2026 güvenlik düzeltmeleri gereklidir: [resmî PHP duyurusu](https://www.php.net/archive/2026.php#2026-10-21-5).

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

Kabul edilmiş özgün şartnameler 01_OGRENCI_PANELI.md ve 02_ARASTIRMACI_PANELI.md; 03_CODEX_GELISTIRME_PROMPTU.md görev kaynağıdır. Kalıcı silme yoktur. İlgili vaka ve kişisel ek süre kapanışı + rapor onayı + ayrı yayın olmadan öğrenciye puan/rapor erişimi verilmez.


Makale için ölçüm tanımları, etkinlik sözlüğü ve süre örnekleri: [Olaylar, etiketler ve süreler](METHODS_EVENTS_DURATIONS_TR.md). Uygulama içi özet araştırmacının Bölüm geçişleri ve ziyaret süreleri alanındadır.
