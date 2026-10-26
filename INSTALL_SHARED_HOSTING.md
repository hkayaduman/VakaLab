# Shared hosting kurulumu

## 0.3.2 · İnceleme düzeltmeleri

Mevcut şema 001–003 kurulumu için `storage/releases/vakalab-duzeltmeler-0.3.2.zip` paketini `/domains/vakalab.techdocsolution.com/vakalab` dizinine yükleyip aynı dizine çıkarın; mevcut `app` ve `public` klasörleri birleşerek dosyaların üzerine yazılmalıdır. ZIP'in içinde yalnız bu iki klasörün 32 kod dosyası vardır. Yeni SQL, migration veya seed gerekmez. `config/local.php`, `vendor` ve `storage` korunur. Güncelleme öncesinde kod/veritabanı yedeğini public dışında alın; ardından siteyi Ctrl+F5 ile yenileyin. Güncel giriş sayfası `assets/app.js?v=0.3.2` kullanır.

Beş inceleme düzeltmesi ve 133/133 başarılı yerel kontrolün ayrıntıları [düzeltme/test raporunda](test-evidence/code-review-2026-10-26/FIXES_TESTS_TR.md) kayıtlıdır. Canlı hosting testi kullanıcı tarafından sonraya bırakılmıştır; bu kayıt canlıya dağıtım veya hosting kabulü yapıldığını göstermez. Dağıtım paketi ve özel yerel kurulum dosyaları Git'e dahil edilmez.

## 0.3.1 · Soru bazında tek rapor güncellemesi

Mevcut 0.3.0 kurulumu için `storage/releases/vakalab-soru-raporu-0.3.1.zip` paketini `/domains/vakalab.techdocsolution.com/vakalab` içine yükleyip aynı dizine çıkarın. app/public dosyaları birlikte güncellenir; config/local.php, vendor ve storage korunur. SQL/seed/migration gerekmez (mevcut 001–003). Site kökü yine `vakalab/public` kalır. Güncellemeden sonra uygulamayı bir kez yenileyin. Her sorunun tam yanıtı ve ayrı geri bildirim/puan alanları görünmeli. Eski genel raporun soru puanları otomatik çıkarılmaz: AI ile bir kez yeni rapor hazırlayın veya her soruyu manuel değerlendirin. Yeni rapor daha sonra F5 gerektirmeden görünür. Öğrenci raporu ve indirmeler de aynı soru düzenini ve profil karşılaştırmasını içerir. Diğer onay/yayın kuralları korunur. Ayrıntılar `TEK_RAPOR_GUNCELLEME.md` içindedir.

## Mevcut hosting için 0.3.0 kod güncellemesi · 26 Ekim 2026

Tek AI isteği/tek rapor güncellemesi mevcut şema 001–003 üzerinde çalışır; yeniden SQL içe aktarma veya seed gerekmez. Güncelleme öncesi uygulama kodunu ve veritabanını public dışında yedekleyin. `storage/releases/vakalab-tek-rapor-0.3.0.zip` paketini DirectAdmin'de `/domains/vakalab.techdocsolution.com/vakalab` dizinine yükleyip aynı dizine çıkarın; `app` ve `public` mevcut klasörlerle birleşmeli, dosyalar değişmelidir. Pakette config, öğrenci verisi, storage içeriği veya vendor yoktur. Mevcut `config/local.php` ve vendor korunur. Site kökü yine `vakalab/public` kalır.

Paketi tek seferde tamamlayın; ardından uygulama sekmesini bir kez açın/yenileyin. Öğrenci-vaka dosyasında “Değerlendirme ve rapor”, tek metin editörü ve rubrik düzeyleri görünmeli. AI ile rapor ve puanları hazırla düğmesi tek değerlendirme isteği yapar; sonuç sonrasında F5 gerektirmez. Alternatif olarak metin ve düzeyleri elle seçip taslak kaydedin. Önce raporu onaylayın; öğrenciye gösterme ayrı düğmedir ve veri toplama kapalı olmalıdır. Önceki ayrı-adım raporları otomatik dönüştürülmez; yeni alanda yeni bir bütüncül rapor hazırlayın.

Yerel sentetik testler ve MariaDB doğrulandı. Gerçek sağlayıcıyla bu yeni sözleşmenin canlı kabulü, dosyalar sunucuya yüklendikten sonra hosting deneme vakasında yapılmalıdır. Yeni istek sürerken tekrar düğmeye basmak ikinci çağrı yapmaz; başarısız sağlayıcı çağrısı otomatik tekrarlanmaz.

0.2.0 güncellemesi: Mevcut DB için önce özel yedek alın; `php bin/console.php migrate` migration 002 uygular. SQLite yerel geçişinde `php bin/upgrade-sqlite.php` özel yedek + eski tüm sütunların hash karşılaştırmasını yapar. MySQL DDL atomik değildir; yarım kalmış 002 otomatik tekrar edilmez, ayrı DB kopyasında incelenir. SSH yoksa 002.sql sonrası mevcut users(student)/cases/definitions kayıtlarının owner_id alanını mevcut ilk araştırmacı kimliğiyle doldurun ve schema_migrations içine 2 kaydını yalnız başarılı doğrulama sonrası ekleyin; eski global kapanış varsa ilgili cases.collection_closed_at alanlarına taşıyın. Birden çok eski araştırmacı varsa sahiplik eşlemesini dağıtımdan önce açıkça doğrulayın. Taze SSH'siz kurulumda da 001 + 002 birlikte gerekir; yalnız 001 güncel kod için yeterli değildir.

Durum: 23 Ekim 2026'da yerel PHP 8.3.35 → 8.3.36 güvenlik güncellemesi yapıldı; MariaDB 11.4.12 ve SQLite ile denendi. Gerçek hosting, gerçek API, HTTPS/proxy ve hosting yedeği henüz denenmedi. Kullanıcı kabulü olmadan araştırmaya başlanmamalı.

## 1. Ön kontrol

PHP 8.3.36+ (8.4 serisinde 8.4.27+, 8.5 serisinde 8.5.12+); PDO MySQL, mbstring, curl, openssl, zip, gd, dom, xml, xmlreader, xmlwriter, fileinfo; MySQL 8+ veya MariaDB 10.6+ InnoDB ve utf8mb4. Bu alt sürüm sınırları hedef gereksinimdir; gerçekten test edilen DB MariaDB 11.4.12'dir. Minimum 256 MB PHP memory_limit önerilir; uzun dosyalarda gerçek limit testi yapın. AI timeout örneği 45 s; PHP/proxy timeout daha uzun olmalı. Dış HTTPS/cURL, yazılabilir özel temp dizini ve en az iki eşzamanlı PHP isteği gerekli.

21 Ekim 2026 tarihli kritik açıklar için [resmî PHP güvenlik duyurusu](https://www.php.net/archive/2026.php#2026-10-21-5). Hosting panelinden uygulamanın web PHP sürümünü ve ayrıca CLI sürümünü doğrulayın; yerel `.tools/php` güncellemesi hostingi güncellemez. Sağlayıcı dağıtım yaması kullanıyorsa iki CVE'nin giderildiğini sağlayıcıdan doğrulayın. Güncelleme öncesi özel yedek alın; sonra yukarıdaki ortam kontrolünü, giriş/oturum, DB bağlantısı ve Word/PDF/XLSX çıktısını ayrı test ortamında tekrar kontrol edin. Mevcut veriye seed veya yeni kurulum SQL'i uygulamayın.

```text
php bin/console.php check
composer install --no-dev --prefer-dist --optimize-autoloader
composer check-platform-reqs --no-dev
```

Kurulu sürümler composer.lock ile sabitlenir: PHPWord 1.4.0 (LGPL-3.0), PhpSpreadsheet 5.10.0 (MIT), mPDF 8.3.1 (GPL-2.0-only). Dağıtırken vendor içindeki lisansları koruyun. Kurumun dağıtım/lisans değerlendirmesi ayrıdır. Kaynaklar: [PHPWord kurulumu](https://phpoffice.github.io/PHPWord/install.html), [PhpSpreadsheet](https://phpspreadsheet.readthedocs.io/en/stable/), [mPDF yerelden vendor yükleme](https://mpdf.github.io/installation-setup/using-without-composer.html).

Hostingde Composer/SSH olması gerekmez: hedef PHP ile uyumlu yerel ortamda **composer.lock üzerinden** vendor oluşturup dosyaları yükleyin. `.tools`, `tests`, `.git`, yerel SQLite ve test çıktılarını sunucuya yüklemeyin. Node/Python hostingde gerekli değildir.

## 2. Güvenli dosya yerleşimi

Tercih edilen örnek:

```text
/home/HESAP/vakalab/             app, bin, config, migrations, storage, vendor
/home/HESAP/vakalab/public/      index.php, api.php, assets, .htaccess
```

Alan adı/subdomain document root'u **yalnız public/** yapın. storage ve config webden erişilemez olmalı. Gizli dosyaları FTP aracıyla da yükleyin. Kök `.htaccess` tüm erişimi reddeder; `public/.htaccess` dizin listelemeyi kapatır. Apache AllowOverride desteği yoksa hosting panelinden aynı kuralları tanımlayın; yalnız dosyanın varlığı güvenlik kanıtı değildir.

Document root değişmiyorsa:

```text
/home/HESAP/vakalab-private/    app, bin, config, migrations, storage, vendor
/home/HESAP/public_html/vakalab/  public içeriği
```

Bu alternatifte **api.php'deki** bootstrap yolunu `/home/HESAP/vakalab-private/app/bootstrap.php` olarak ayarlayın. Bootstrap'ın app/config/vendor yolları kendi konumuna göredir; başka kaynak yolu değiştirilmez. Uygulama bütününü public_html içine koymayın. public olmayan konum sağlanamıyorsa araştırma verisiyle kurulumu ilerletmeyin; hosting sağlayıcısından özel dizin isteyin.

Dosya izinleri sağlayıcının kullanıcı modeline göre genellikle dizin 750, dosya 640; config/local.php 600; storage PHP kullanıcısınca yazılabilir. **777 vermeyin.** vendor/public okunabilir, app/config/bin webden erişilemez olmalı.

## 3. Veritabanı ve yapılandırma

Panelden ayrı DB ve ayrı DB kullanıcısı açın; yalnız bu veritabanına yetki verin. Test DB adı `_test` içersin. Üretim DB ve test DB aynı olmasın. Charset utf8mb4, InnoDB seçin. Boş DB'ye:

```text
php bin/console.php migrate
```

SSH yoksa phpMyAdmin → hedef **boş** DB → migrations/001.sql import. Başarılı olduğunu ve tüm tabloları doğruladıktan sonra:

```sql
INSERT INTO schema_migrations(version, applied_at)
VALUES (1, DATE_FORMAT(UTC_TIMESTAMP(6), '%Y-%m-%dT%H:%i:%s.%fZ'));
```

Migration mevcut veriyi sıfırlamaz. MySQL DDL atomik değildir: yarım kurulumda otomatik tekrar yapılmaz; ayrı test DB üzerinde hatayı düzeltin. Dolu üretim DB'sine schema.sql tekrar import etmeyin. Sürüm 001 mevcutken CLI veri değiştirmez.

config/example.php → config/local.php kopyalayın. DSN, DB kullanıcı/parola, env, HTTPS, kurum onam metni, AI ayarlarını sunucuda girin. Bunları chat/ekran görüntüsü/git'e koymayın. Örnek `https=true`; yerel HTTP için yalnız testte false. Alt klasörde base_path=`/vakalab`, kökte boş. UI göreli URL kullandığından ters proxyde doğru klasörü koruyun. Session cookie path base_path ile eşleşir. HTTPS'i hosting panelinden zorlayın; secure cookie proxy arkasında da true olmalı.

Üretim: env=production, gerçek compatible sağlayıcı/model/HTTPS endpoint/key. Gerçek onam/rubrik kurumsal doğrulamayı bekler. Mock production'da çağrı yapamaz. AI anahtarı yalnız sunucu config'inde; farklı bir sağlayıcı protokolü gerekiyorsa Ai adapter'ı eklenmelidir.

OpenRouter/GPT-5 Nano yerel bağlantısı 23 Ekim 2026'da sentetik sohbet, profil ve tek soru puanlamasıyla doğrulandı. `provider=compatible`, `endpoint=https://openrouter.ai/api/v1/chat/completions`, `model=openai/gpt-5-nano`, `structured_outputs=true` kullanılır; anahtar yalnız özel local.php içinde tutulur. Destekleyen endpointlere yönlendirme için uygulama katı şema isteklerinde OpenRouter'a `provider.require_parameters=true` gönderir. Profil ve v2 soru puanlaması JSON Schema kullanır; diğer/tarihsel görevler mevcut JSON nesnesi sözleşmesini korur. Yerelde 45 saniyelik sınır puanlamada yetersiz kaldı; local.php içinde `ai.timeout=90` ile doğrulandı. Hosting PHP/proxy süreleri ayrıca uyumlu olmalı. JSON biçiminin doğru olması bilimsel puanlama kalitesini doğrulamaz.

Windows yerel PHP'de CA dosyası tanımlı değilse TLS doğrulaması başarısız olabilir. Yerel testte Windows güvenilen köklerinden bir CA paketi oluşturulup `.tools/php/php.ini` içindeki `curl.cainfo` / `openssl.cafile` ayarlandı; SSL doğrulaması kapatılmadı. Bu Windows'a özel yolları sunucuya taşımayın; hostingin geçerli CA yapılandırmasını kullanın. Açık yerel PHP sunucusu php.ini değişikliğinden sonra yeniden başlatılmalıdır.

## 4. İlk araştırmacı ve kurulum kilidi

Web kurucusu veya web seed endpointi yok; CLI PHP_SAPI kontrolüyle kapalı. İlk hesap:

```powershell
$env:VAKALAB_ADMIN_PASSWORD='BURAYA-GUCLU-OZEL-PAROLA'
php bin/console.php researcher arastirmaci
Remove-Item Env:\VAKALAB_ADMIN_PASSWORD
```

SSH yoksa yerelde aynı env değişkeniyle `php bin/console.php researcher-sql arastirmaci > researcher-install.sql` üretin. Hash içeren bu **özel** SQL'i phpMyAdmin'de bir kez import edin; dosyayı public dizine koymayın, erişimi kapatın. Bu komut DB bağlantısı gerektirmez. Varsayılan üretim parolası yoktur. Araştırmacı yeni öğrencinin parolasını panelden yenileyebilir; boş bırakırsa eskisi korunur.

Kurulum kilidi, web kurucu olmaması + schema_migrations kaydı + benzersiz login ile sağlanır. Kurulum sonrası config salt okunur, CLI erişimi yöneticiyle sınırlı olsun. Test seed'ini üretime taşımayın.

## 5. İlk hosting doğrulaması

1. HTTPS ve alt klasörde login, çıkış ve yeniden giriş. Oturum süresi son etkinlikten 2 saat.
2. `/config/local.php`, `/storage/`, `/bin/console.php`, `/vendor/`, `/.git/config` adresleri 403/404 vermeli. Gizli dosya indirilmemeli. Özel dizin yerleşiminde zaten URL'leri yoktur.
3. MANUAL_TESTS.md T05/T09/T11/T14/T21/T26: doğrudan API dahil kilitler, CSRF, sahiplik, aynı oturumda AI sürerken autosave.
4. Gerçek sağlayıcıyı ayrı test ortamında bir sentetik mesajla deneyin. Süre, status ve model kaydını kontrol edin; API anahtarını kaydetmeyin.
5. Aktarım **non-stream**: gerçek ilk parça ölçümü yok. Hosting tamponlasa da sahte parça zamanı yoktur. Bu sürüm streaming desteği iddia etmez.
6. Rapor Word/PDF, XLSX/CSV/JSON; Türkçe font, uzun tablolar ve mobile klavye. API hata ekranlarında sır olmadığını doğrulayın.

PHP/session kilidi kodda bırakılır; hosting worker kapasitesi veya proxy seri işliyorsa yereldeki concurrency sonucu hosting kanıtı değildir. Timeout sonrası istek görünümü + araştırmacı gerekçeli kapatma kullanılır; otomatik yeniden üretme yok. Yeni deneme maliyet doğurabilir.

## 6. Yedekleme ve ayrı DB'ye geri yükleme

Sıklık/saklama/şifreleme kurum kararıdır. Öneri: veri toplama günlerinde en az günlük ve her güncelleme öncesi; bu öneri kabul edilmiş politika değildir. DB ile config/özel dosyaları ayrı, erişimi sınırlı, public dışı yedekleyin.

```text
mysqldump --single-transaction --default-character-set=utf8mb4 -u DBUSER -p DBNAME > private-backup.sql
mysql -u RESTOREUSER -p VAKALAB_RESTORE_TEST < private-backup.sql
```

Parolayı komut argümanına yazmayın; istemde girin veya özel client config kullanın. Panelde phpMyAdmin Export/Import alternatifi kullanılabilir. Restore DB **ayrı** olmalı; üretim DSN'sini bu sırada değiştirmeyin. Geri yükleme sonrası users/applications/messages/ai_attempts/snapshots/analiz/rapor sayıları, FK ilişkileri ve eski vaka/rubrik sürümlerini karşılaştırın. Aynı örnek hesabın eski kayıt ve raporunu açın; sonuç/kanıt kaydedin.

Yerel MariaDB smoke restore testi yapılır; gerçek hosting yedeği ve yedek saklama düzeni henüz doğrulanmadı. Yedek geri yüklemek normal kalıcı silme özelliği değildir; üretim üzerine restore ayrıca kontrollü operasyon gerektirir.

## 7. Güncelleme ve rollback

Önce bakım bildirimi ve güncel özel yedek; aynı DB'nin ayrı kopyasında migration/regresyon. Kod + vendor + composer.lock birlikte sürümlenir. Henüz yalnız migration 001 vardır; sonraki sürüm geçmişi ezmeyen yeni migration eklemelidir. Değişen dosyaları yükleyin, config'i koruyun. Hata olursa önceki kod paketine dönün; yeni şema eski kodla uyumsuzsa **ayrı geri yüklenmiş DB** üzerinde doğrulayın. Otomatik destructive down migration yok. Veri toplama sırasında seed/reset çalıştırmayın.


### Öğrenci etiketleri için konu kapsamı (24 Ekim 2026)

Yeni veritabanı kolonu gerekmez; alan definitions.body JSON içinde topic_scope olarak saklanır. Sunucudaki mevcut öğrenci etiketleri için Rubrikler ve etiketler ekranından grup ve konu kapsamını seçip yeni sürüm kaydedin. Kapsam seçilmeden öğrenci etiketlemesi başlamaz. Yerel SQLite test veritabanını veya yedeklerini üretime yüklemeyin.

## 0.2.29 — Şema 003 zorunlu
Bu sürüm için 001/002 yanında Migration::run içindeki 003 de uygulanmalıdır. Önce özel yedek alın; CLI ile php bin/console.php migrate çalıştırın. Yerel SQLite için php bin/upgrade-sqlite.php yedeği ve eski sütunların korunmasını doğrular. MySQL tarafında uygulama tablosuna attempt_no eklenir, eski öğrenci/vaka benzersiz indeksi öğrenci/vaka/deneme indeksiyle tek ALTER işleminde değiştirilir. Önceki SSH'siz 001/002 talimatları bu sürüm için yeterli değildir; Migration::run ile eşdeğer geçiş ve şema 3 kaydı gerekir. Öğrenci başına tek vaka kaydı varsayan eski kod sürümüne geri dönüş yapılmamalıdır.
