# VakaLab

VakaLab, katılımcıların bir vakayı nasıl değerlendirdiğini ve kararlarını nasıl gerekçelendirdiğini incelemek için geliştirilmiş Türkçe bir eğitim ve araştırma uygulamasıdır. Katılımcılar önce kendi değerlendirmelerini yazar, ardından yapay zekâyla (AI) görüşür ve son kararlarını gerekçelendirir. Araştırmacılar yanıtları, sohbetleri ve bölüm geçişlerini aynı çalışma içinde inceleyebilir.

Araştırma amaçlarından biri, katılımcıların **yapay zekâya duyduğu güveni (AI trust)** ve **karar verirken yapay zekânın önerilerine ne ölçüde dayandığını (AI reliance)** incelemektir. Araştırmacılar katılımcıların başlangıç ve nihai değerlendirmelerini, sohbet kayıtlarını ve yansıtma yanıtlarını birlikte inceleyerek hangi önerilerin benimsendiğini, uyarlandığını veya reddedildiğini ve bu tercihlerin nasıl gerekçelendirildiğini araştırabilir.

Öğretmen eğitimi, üniversite dersleri, mesleki eğitim ve kurum içi eğitim gibi farklı ortamlarda kullanılabilir. Araştırmacılar vaka metinlerini, değerlendirme rubriklerini ve araştırma etiketlerini kendi alanlarına göre düzenleyebilir. Uygulamada katılımcı hesabı **Öğrenci** olarak adlandırılır.

## Öğrenci nasıl çalışır?

**Vaka → Vaka profili → Başlangıç değerlendirmesi → AI sohbeti → Nihai değerlendirme → Yansıtma → Son teslim**

- Öğrenci kendisine atanan vakaları görür. Taslak yanıtlar otomatik kaydedilir ve vaka yeniden açıldığında korunur.
- Başlangıç değerlendirmesi kesinleşince ilgili yanıtlar düzenlemeye kapanır ve vaka üzerinden AI sohbeti açılır. Öğrenci daha sonra nihai kararını ve yansıtmasını yazar.
- Son teslim, nihai değerlendirme ile yansıtmayı tek işlemde kaydeder. Araştırmacı öğrenci raporunu onaylayıp yayımladığında öğrenci raporu görebilir.

## Kullanıcı rolleri

| Rol | Başlıca işlemler |
| --- | --- |
| **Öğrenci** | Atanan vakalarda çalışır, AI ile görüşür, yanıtlarını teslim eder ve yayımlanan raporunu görür. |
| **Araştırmacı** | Kendi çalışma alanında vaka ve öğrenci yönetir, toplu öğrenci ekler ve vaka atar. Sürümlü vaka metinleri, rubrikler ve etiketler düzenler. Veri toplamayı vaka bazında yönetir, gerektiğinde öğrenciye ek süre verir. Çalışma sürecini inceler, AI destekli değerlendirmeyi gözden geçirir, rapor yayımlar ve araştırma verisini indirir. |
| **Süper Admin** | Ortak hesap havuzunu ve araştırmacı çalışma alanlarını yönetir. Öğrencileri araştırmacılara bağlar ve parola yenileme talepleri için tek kullanımlık kod oluşturur. |

Hesap işlemleri giriş, öğrenci kaydı, profil düzenleme, parola değiştirme ve **Şifremi unuttum** akışlarını kapsar.

## Araştırma verisi ve dışa aktarma

- **Bölüm geçişleri ve süreler.** Vaka içindeki ziyaretler sırayla kaydedilir. Aynı bölüme dönüş ayrı bir ziyaret olarak görünür. Tarayıcı odağı kaybolduğunda geçen süre, bölüm süresinden ayrı gösterilir.
- **AI etkileşimi.** Soru gönderimi, yanıt gelişi ve uygulama içindeki bekleme aralıkları ayrı kaydedilir. Bekleme süresi çalışma toplamına ikinci kez eklenmez.
- **Process Mining ve Sequence Analysis.** Disco uyumlu CSV çıktılarında ziyaret ve etkinlik sırası, zaman damgaları, süreler ve ölçüm kalitesi alanları bulunur. Araştırmacı ayrıca XLSX ve JSON veri çıktıları ile öğrenci raporu için Word ve Excel dosyaları alabilir.
- **Veriyi yorumlama.** Kapanışı bilinmeyen aralıklar süre analizinde ölçülememiş olarak ele alınmalıdır. Olay kayıtları ekranda gözlenen davranışı gösterir. Okuma, dikkat veya öğrenme doğrudan ölçülmez.

Olay ve süre tanımları, örnek senaryolar ve CSV alanları için kılavuzlara bakın. [Ölçüm yöntemi](METHODS_EVENTS_DURATIONS_TR.md) · [Olay kataloğu](EVENT_CATALOG.md) · [Veri sözlüğü](DATA_DICTIONARY.md)

## Yerelde deneyin

VakaLab, PHP ve Composer ile çalışır. Arayüz JavaScript ve CSS kullanır. Sunucu kurulumu MySQL veya MariaDB, hızlı yerel deneme SQLite kullanabilir. Desteklenen PHP sürümleri ve eklentiler için kurulum rehberine bakın. [PHP ve hosting gereksinimleri](INSTALL_SHARED_HOSTING.md)

1. Depoyu bilgisayarınıza alın ve proje kökünde bağımlılıkları kurun.

   ```sh
   composer install --no-interaction --prefer-dist
   php bin/console.php check
   ```

2. Yeni bir yerel kurulumda `config/local.php` dosyasını aşağıdaki içerikle oluşturun. Bu yapılandırma, ayrı bir SQLite test veritabanı ve API anahtarı gerektirmeyen örnek AI yanıtları kullanır. PHP'de `pdo_sqlite` etkin olmalıdır.

   ```php
   <?php
   $config = require __DIR__ . '/example.php';
   $config['dsn'] = 'sqlite:' . __DIR__ . '/../storage/vakalab_test.sqlite';
   $config['db_user'] = '';
   $config['db_password'] = '';
   $config['https'] = false;
   return $config;
   ```

3. Şemayı oluşturun. Örnek vakalar ve test hesapları için sentetik veri yükleme adımlarını izleyin. [Sentetik veri ve test hesapları](TEST_DATA.md)

   ```sh
   php bin/console.php migrate
   php bin/console.php schema-check
   ```

4. Yerel sunucuyu başlatın ve tarayıcıda uygulamayı açın. [Yerel VakaLab](http://127.0.0.1:8088/)

   ```sh
   php -S 127.0.0.1:8088 -t public
   ```

## Sunucu kurulumu ve araştırma hazırlığı

Sunucuda web kökü olarak yalnızca `public/` dizinini kullanın. Veritabanı bağlantısını ve AI sağlayıcısının anahtarını web kökü dışındaki `config/local.php` dosyasında tanımlayın. Mevcut bir kurulumu güncellerken veritabanını yedekleyin ve güncel şemayı doğrulayın. [Shared hosting kurulum ve güncelleme rehberi](INSTALL_SHARED_HOSTING.md)

Katılımcılarla veri toplamaya başlamadan önce kurumunuza uygun onam metnini ve rubriği hazırlayın. Kendi sunucunuzda deneme vakasıyla giriş, AI yanıtı, son teslim ve olay dışa aktarımını uçtan uca kontrol edin. [Manuel testler](MANUAL_TESTS.md) · [Test sonuçları](TEST_RESULTS.md)

## Geliştirme ve kullanım izni

PHP uygulama kodu `app/`, web giriş noktaları `public/`, veritabanı geçişleri `migrations/` ve testler `tests/` altında yer alır. PHP testlerini `composer test` ile çalıştırabilirsiniz. [Mimari](ARCHITECTURE.md) · [Sürüm geçmişi](CHANGELOG.md)

Proje, Composer yapılandırmasında **proprietary** olarak işaretlidir. Projeyi kendi kurumunuzda kullanmak, uyarlamak veya yeniden dağıtmak için proje sahibiyle iletişime geçin. [Composer yapılandırması](composer.json)
