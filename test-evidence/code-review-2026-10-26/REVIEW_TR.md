# VakaLab kod incelemesi — 26 Ekim 2026

**Sonraki durum:** Aşağıdaki tarihsel bulgular aynı gün düzeltildi. Düzeltmeler ve 133/133 başarılı kontrolün sonuçları [FIXES_TESTS_TR.md](FIXES_TESTS_TR.md) dosyasındadır. Bu rapor ve tekrar üretme betikleri düzeltme öncesi davranışı belgelemek için korunur.

İnceleme, uygulama kodunu değiştirmeden gerçekleştirildi. Beş işlevsel/gizlilik sorunu sentetik verilerle yeniden üretildi. Bu kayıt bir hata düzeltme teslimi değildir.

## Kapsam

- `app/`: erişim ve oturum, kayıt/teslim, sohbet, analiz v1/v2/v3, rubrik doğrulama, etiketler, rapor, dışa aktarım, telemetri ve süre türetme.
- `public/`: API yönlendirme, HTML giriş noktası, JavaScript arayüzleri ve CSS.
- `bin/`, `config/example.php`, SQL migration dosyaları, Composer manifest/kilit dosyası ve test altyapısı.
- README ve mimari belgelerindeki davranışlarla kodun karşılaştırılması.
- Üçüncü taraf `vendor/` kütüphaneleri, taşınabilir PHP/MariaDB ikilileri ve eski test kanıtları satır satır denetlenmedi. Gerçek sağlayıcıya ücretli AI çağrısı ve canlı hosting testi yapılmadı. Özel yapılandırma/anahtar içerikleri rapora alınmadı.

## Doğrulanmış bulgular

### 1. [P2] Öğrenciye dönen başlangıç ve kayıt yanıtları özel araştırmacı gerekçesini açığa çıkarıyor

Konum: `app/App.php:19` ve `app/App.php:61`; API aktarımı `public/api.php:22` ve `public/api.php:24`.

`view()` öğrenci için uygulama alanlarını izin listesiyle sınırlandırırken, `start()` mevcut uygulamanın tüm erişim kaydını ve `save()` tüm uygulama satırını döndürüyor. Araştırmacı uygulamayı analiz dışı bıraktığında girdiği `exclusion_reason` ve `excluded` alanları öğrencinin HTTP yanıtında bulunuyor. Arayüzde gösterilmemesi erişimi engellemiyor. Önceden teslim edilmiş kayıtta idempotent `submit` yanıtı da ham satırı döndürüyor.

Tekrar üretme: araştırmacı sentetik bir çalışmayı özel gerekçeyle analiz dışı bıraktı; aynı öğrenciyle `start` ve `save` çağrıldı. İki yanıtta da gerekçe görüldü; `view` yanıtında gizlendi.

Öneri: öğrenciye dönen uygulama kayıtları için ortak bir izin listesi/projeksiyon uygulayın; normal ve idempotent dönüşleri aynı süzgeçten geçirin.

### 2. [P2] Ağ belirsizliğinden sonra değiştirilen mesaj gönderilmeden temizlenebiliyor

Konum: `public/assets/app.js:99–102`; sunucudaki eşleşme `app/App.php:66`.

Sohbet isteğinin yanıtı ağda kaybolursa `sessionStorage` içindeki istek anahtarı korunuyor. Kullanıcı mesaj kutusunu değiştirip yeniden gönderdiğinde farklı içerik için aynı anahtar kullanılıyor. Sunucu önceki tamamlanmış isteği başarıyla döndürüyor; istemci bunu yeni mesajın gönderilmesi olarak kabul edip yeni taslağı boşaltıyor. Yeni mesaj sohbet geçmişine hiç eklenmiyor. Normal otomatik kayıt sonraki aşamada boş taslağı da sunucuya yazar; eski taslak revizyonları araştırmacı tarafından kurtarılabilir.

Chrome doğrulamasında ilk istek sunucuda tamamlandıktan sonra yanıt kesildi. İkinci farklı mesaj gönderildiğinde sonuç: `draft=""`, `second_message_in_chat=false`, `first_message_in_chat=true`.

Öneri: belirsiz istek için anahtar, değişmez gönderilen metin ve durum birlikte saklansın. Metin değiştiyse önce belirsiz isteğin durumu çözülsün veya yeni mesaj için yeni anahtar ayrılsın. Sunucu da aynı anahtarın farklı içerikle kullanımını açık bir çakışma olarak reddetsin.

### 3. [P2] Yeniden denemenin geç yanıtı başka vakanın sohbetine yazılıyor

Konum: `public/assets/app.js:62`.

Normal `sendChat()` ve `refreshChat()` dönüşte `work` bağlamını kontrol ediyor; başarısız istek için yeniden deneme işleyicisi aynı kontrolü yapmıyor. Öğrenci yeniden deneme sürerken Vakalarım'a dönüp başka vaka açarsa, eski vaka için alınan `requests` ve `messages` yeni global `work` nesnesine atanıyor. Ayrıca yeni vakanın `busy` durumu da eski işlem tarafından sıfırlanabiliyor. Sunucu kayıtları bu gösterim hatasıyla değişmedi, ancak yanlış vaka içeriği görünür hale geldi.

Chrome doğrulaması: A vakasının yeniden deneme yanıtı bekletildi, B vakası açıldı, A yanıtı serbest bırakıldı. B'nin gerçek mesaj sayısı 0 iken istemcide A'nın mesajı B'nin `work.messages` listesine girdi.

Öneri: işleyici başında hedef `work` nesnesini yakalayın; her `await` sonrasında ve hata yolunda yalnız aynı bağlam hâlâ açıksa arayüz durumunu değiştirin.

### 4. [P2] Eski rapor işlemleri v2 güncellik kontrolünü atlayarak eski puanı yeniden yayımlıyor

Konum: `app/Analysis.php:119–120`; daha güçlü kontrol `show` dalında `app/Analysis.php:109–117`.

V2 `show` işlemi rapordaki karar kimliklerini güncel değerlendirmeyle karşılaştırıyor. Aynı rapora açık kalan `approve` ve `publish` işlemleri bu kontrolü yapmıyor. Araştırmacı bir bölümün kararını değiştirdiğinde raporun onayı/yayını kaldırılıyor; ancak eski işlemler çağrılarak değişmemiş rapor tekrar yayımlanabiliyor. Öğrencinin indirme kontrolü raporun onaylı, yayımlanmış ve en son rapor olmasını denetlediğinden, bu eski içeriği kabul ediyor.

Tekrar üretme: v2 raporu hazırlandı/yayımlandı, başlangıç değerlendirmesi reddedildi. `show` 409 verdi; ardından `approve` + `publish` başarılı oldu ve öğrenci raporu eski başlangıç puanıyla indirebildi. Bu bulgu korunmuş v2 akışına aittir; yeni v3 `holistic` işlemi için aynı atlama gösterilmedi.

Öneri: tüm yayın yollarında ortak bir güncellik denetimi kullanın veya eski `approve/publish` işlemlerini yalnız v1 için sınırlandırın. V2 raporu güncel kararlarla yeniden hazırlanmadan yayımlanamasın.

### 5. [P2] Kodlanmış dışa aktarım etiket üretim geçmişindeki serbest metni koruyor

Konum: `app/Export.php:26`; kaynağı `app/MessageLabels.php:33`.

`coded=1` mesaj, taslak, karar ve olay metinlerini temizliyor; ancak `label_batches[].detail` aynen ekleniyor. Bu JSON içindeki `label_definitions` etiket adı, açıklaması ve `body` serbest metinlerini içeriyor. Dolayısıyla kodlanmış paketin “metinleri içermez” sözleşmesi sağlanmıyor; tanımlara girilmiş kişisel veya özel açıklamalar dışarı çıkabiliyor. Bu bulguda öğrenci sohbet metninin doğrudan bu yoldan sızdığı iddia edilmiyor.

Tekrar üretme: etiket açıklamasına yalnız sentetik `PRIVATE_LABEL_DESCRIPTION_20261026` işareti eklendi, etiketleme çalıştırıldı. İşaret `coded=1` veri paketinde bulundu.

Öneri: kodlanmış pakete etiket üretim geçmişinin yalnız kimlik, seçim, kapsam ve zaman gibi gerekli yapısal alanlarını aktarın; tanım metinlerini ve iç içe `body` içeriğini çıkarın. Yeni eklenen tüm dışa aktarım koleksiyonları için aynı politika uygulanmalı.

## Test sonuçları

Ana regresyon koşusu: `test-evidence/disco-attempts-2026-10-24/20261026T194445Z/results.json`.

- 127 test paketi/sözdizimi/kontrol adımından 124'ü geçti.
- Çalıştırılan 20 yerel PHP test paketinin tamamı geçti. Sözdizimi denetimleri geçti. HTTP ve erişim/yayın testleri geçti. Bunlar yukarıdaki yeni senaryoları yakalamıyor.
- `browser.cjs` 22 kontrolden sonra eski “Yeni değerlendirme başlat” düğmesini beklediği için durdu (`tests/browser.cjs:23`). Üretim ekranı `renderAnalysisRuns()` üzerinden v3 ekranını döndürüyor; test beklentisi güncellenmeli. Bu durma testin kalan kontrollerinin doğrulandığı anlamına gelmez.
- `journey-browser.cjs` ilk koşuda beklenenden fazla uygulama dışı odak aralığı gördü. Aynı anda başka bir inceleme tarayıcısı da çalıştırılmıştı. Diğer tarayıcılar kapalıyken tek başına tekrarında 27 kontrolün tamamı geçti (`journey-rerun.txt`). İlk hatanın odak etkileşiminden kaynaklandığı olasıdır; kesin kök neden ayrıca kanıtlanmadı.
- `mysql_checks.py` ilk koşuda yanlış `MARIADB_HOME` yolu nedeniyle başlayamadı. Doğru taşınabilir MariaDB diziniyle yapılan ayrı tekrar geçti (`mysql.txt`): alan/iş akışı, yeni deneme, Disco ve bütüncül rapor testleri ile ayrı veritabanına yedekten geri yükleme denetlendi.
- Bu iki tekrar hesaba katıldığında ilk 127 adımın 126'sı başarıyla doğrulandı; kalan `browser.cjs` eski arayüz beklentisinde duruyor. Yeni bulunan beş sorun, mevcut testlerin geçmesinden bağımsız olarak özel tekrar üretmelerle doğrulandı.
- İlk sandbox koşularındaki PHP `is_readable/is_writable` ve eksik Playwright/Chrome yolu hataları çalışma ortamından kaynaklandı. Sandbox dışında ve doğru çalışma zamanı yollarıyla PHP ve ilgili tarayıcı paketleri yeniden çalıştırıldı; bunlar uygulama hatası olarak sayılmadı.

## Yeni tekrar üretme kanıtları

- `reproduce.php`: yalnız bellek içi SQLite ve sentetik seed; özel alan sızıntısı, kodlanmış çıktı, eski rapor yayını ve aynı anahtar/farklı metin davranışı.
- `reproduce-browser.cjs`: bağımsız sentetik SQLite, yerel PHP sunucusu ve headless Chrome; ağda yanıt kaybı ile yeniden denemede vaka değişimi. Canlı sağlayıcı çağrısı yoktur.
- `observations.json`: bu iki tekrar üretmenin gözlenen sonuçları.

Üretim dosyalarında değişiklik yapılmadı; yalnız bu inceleme raporu, tekrar üretme betikleri ve test kanıtları eklendi. Commit, push veya dağıtım yapılmadı.
