# Gerçek test sonuçları — 2026-10-20

## Soru bazında tek rapor 0.3.1 — 2026-10-26

- `tests/holistic-review.php`: SQLite ve ayrı yerel MariaDB 11.4.12 üzerinde **63 kontrol** geçti. Tek AI çağrısı, manuel yolda sıfır çağrı, altı sorunun tam soru/yanıt/geri bildirim/puan eşleşmeleri, başka soru yanıtından kanıt alınmasının reddi, dolu yanıta yok denmesinin reddi, soru/toplam ağırlıkları, ayrı onay/yayın ve eski rapor uyumluluğu doğrulandı. Word içinde her soru/yanıt/geri bildirimin bulunması ve Excel'de aynı satırda kalması da denetlendi; PDF üretimi geçti.
- `tests/holistic-contract.php`: 10 kontrol. Tek kullanıcı veri iletisi, bütün soru/yanıtlar, tam rubrik, profil, her sorunun zorunlu olduğu çıktı şeması ve yalnız kendi yanıtına izin veren kanıt kaynakları doğrulandı.
- `tests/holistic-browser.cjs`: **36 kontrol**. Her sorunun tam öğrenci yanıtı, altı ayrı geri bildirim/puan alanı, manuel kayıt, tek sorunun geri bildirimini düzenlerken diğerinin korunması, öğrenci önizlemesinde aynı altı bölüm ve profil, indirmeler, kaybolan POST yanıtından sonra F5 olmadan güncelleme, tek üretim isteği ve üç ekran genişliği geçti. JavaScript hatası yok.
- Mevcut temel PHP 87, v2 değerlendirme 96 ve soru-yanıt çelişkisi 26 regresyon kontrolü geçti. PHP/JS sözdizimi ve diff boşluk denetimi geçti. MySQL çalıştırıcısındaki mevcut bağlantı, temel, deneme, Disco ve ayrı yedek geri yükleme kontrolleri de geçti.

Sonuçlar `test-evidence/question-report-2026-10-26` içindedir. Harici AI çağrısı yapılmadı; canlı hosting güncellemesi ve yeni sözleşmenin gerçek sağlayıcı kabulü henüz yapılmadı. Üretim verisi/config değiştirilmedi. Yeni SQL geçişi yoktur.

## Tek rapor sözleşmesi 0.3.0 — 2026-10-26

- `tests/holistic-review.php`: SQLite ve ayrı yerel MariaDB üzerinde 40 kontrol. Tek AI çağrısı sağlayıcı sayacıyla, manuel yol sıfır çağrıyla doğrulandı. Tam soru/yanıt eşleşmesi, sahiplik, eşzamanlı ikinci üretim, revizyon kontrolü, gerçek sıfır puan, ağırlıklı toplam, yayın kapısı, hatalı çıktıdan sonra eski raporun korunması, Word/PDF/XLSX ve eski endpointlerin v3 raporunu değiştirememesi kapsanır.
- `tests/holistic-contract.php`: 10 kontrol. Gerçek Ai adapter'ının ilettiği tek kullanıcı iletisinde vaka, tam rubrik, altı soru/yanıt ve profil; şemada izinli ölçüt/düzey/puan/kaynak listeleri; mock çıktı doğrulaması.
- `tests/holistic-browser.cjs`: yerel PHP + Chrome ile 26 kontrol. Manuel kayıt, düzenleme, onay/yayın ayrımı, indirmeler, 1440/768/390 piksel genişlikler, HTML kaçışları, CSRF/GET koruması, kaybolan POST yanıtından sonra F5 olmadan sonuç görünmesi ve ikinci AI isteğinin gönderilmemesi geçti.
- Temel PHP 87, v2 uyumluluğu 96, mevcut soru-yanıt koruması 26 kontrol; gözlemci 5 senaryo grubu ve analiz ilerleme/sıralama 27 kontrol geçti. PHP/JS sözdizimi kontrolleri geçti.
- `tests/mysql_checks.py`: ayrı MariaDB 11.4.12'de yeni 40 kontrol yanında temel 87, yeni deneme 38, Disco 35 ve bağlantı boşta kalma 6 kontrol geçti; ayrı geri yüklenen DB'de 8 tablo sayısı eşleşti.

Yeni testler harici sağlayıcıya istek yapmaz; canlı veri veya konfigürasyon değiştirilmedi. Gerçek AI içerik kalitesi ve hosting üzerindeki yeni sürüm kabulü henüz doğrulanmadı. Eski ayrı kartlara bağlı tarayıcı testi `tests/legacy-review-v2-browser.fixture` olarak saklandı; varsayılan yeni arayüz testi `tests/holistic-browser.cjs` oldu.

## Onaylanan promptlar, güncel etiket sözleşmesi ve odak incelemesi — 2026-10-23

Sohbet chat-v2, etiketleme message-labels-v2 etkinleştirildi; metinler AI_PROMPTS_REVIEW_TR.md içinde uygulamadaki sabitlerden aynen çıkarılmıştır. Vaka bağlamı ile user/assistant API rolleri ayrıldı; konuşmada rol yükseltmesi reddedilir. Her etiket isteğinde güncel ad, description ve ayrıştırılmış body kullanılır; yeni/değişmiş tanım/model/prompt cache'i geçersiz kılar. Audit tanım snapshotını tutar. Sağlayıcı yanıtı beklenirken tanım seti değişirse eski sonuç kaydedilmez. Etiket JSON şeması yalnız mevcut mesaj/etiket kimliklerine izin verir. Hiçbir doğrulama etiketin anlamsal doğruluğunu garanti etmez.

Teknik sonuçlar: etiket sözleşmesi 14, sohbet rol/allowlist 9, süreç 28, presence 60, navigation 82, genel SQLite 81, v2 74, recalc 21, review 17, export 39 geçti. PHP8.4 etiket 12, yarış testi eklenmeden önce geçti. Genel Chrome 131, gerçek presence Chrome 23, süreç JS 11 geçti. 23 Chrome içinde tarayıcının screenshot işlemi odak/görünürlük korunurken sıfır dışarı; gerçek sekme geçişi, AI sırasında ayrılma/dönüş ve minimize vardır. Bu, Windows Snipping Tool veya Win+Shift+S testi değildir. İlk yeni browser kontrolü öğrenci API'sinde researcher navigation alanı beklediği için durdu; fixture DB salt okunur okumasıyla tam tekrar geçti. Kanıt test-evidence/prompts-presence-2026-10-23/; PHP/JS sözdizimi/diff kontrolü ayrı yapıldı.

**Gerçek GPT-5 Nano davranış sonucu KARIŞIK; tam kabul YOK.** Kısa vaka sorusu gerçek rol aktarımından sonra kısa ve yönlendirmesiz yanıtlandı. Finans/hukuk rol değiştirme isteği reddedildi; ardından istenmeyen pedagojik plan ve davet eklenmesi yinelendi: yönlendirmeme koşulu BAŞARISIZ. Öğrenci sorusu ve AI gerekçeli yanıtı doğru farklı etiketlere eşleşti. Aynı etiket adları tutulup açıklamalar açıkça daraltıldığında model önceki etiketleri seçti: güncel açıklama/dışlama koşulu BAŞARISIZ. İstekte doğru güncel açıklamalar vardı; sorun yalnız cache/veri aktarımı değildi. Örnekli prompt güncellemesi de bu iki açık için tam çözüm sağlamadı. Birkaç ilk helper satırındaki passed, yalnız yetersiz biçim kontrollerini ifade eder; insan incelemesinde karşılanmayan davranışlar outcome-summary.json içinde açıkça kaydedildi. Son kullanıcıya modelin her zaman doğru etiketlediği veya tüm yönergelere uyduğu söylenmemeli.

Mevcut test vakasının ara olayları salt okunur incelendi: 38.470 sn toplam = 35.502 sn tarayıcı gizli + 2.968 sn görünür/odaksız. Uzun aralıkta iki heartbeat gizlilik durumunu doğrular. Bu ölçüm öğrenci vaka sekmesinin durumudur; VakaLab'ın başka penceresi de aynı koşulu doğurabilir. Hangi site/uygulama açıldığı veya Snipping Tool kullanıldığı kayıttan belirlenemez. Süreler ve ham olaylar yeniden yazılmadı. Gerçek vaka/etiket tanımı değiştirilmedi; kullanıcının vakasında değerlendirme veya yeniden etiketleme başlatılmadı. Bu tur sentetik gerçek sağlayıcı çağrıları yapıldı; API anahtarı kanıtlara yazılmaz. Commit/push yok.

## Mesaj taslağı hatası ve prompt incelemesi — 2026-10-23

Kullanıcının ekranındaki hata ayrı SQLite/gecikmeli mock/Chrome ortamında yeniden üretildi: gönderim metni trim edilirken, temizlikte özgün question ile karşılaştırılıyor; satır sonu olan taslak kutuda ve araştırmacı görünümünde kalıyor. Gönderim anındaki özgün taslakla karşılaştırma düzeltildi. Araştırmacı başlığı “Mesaj kutusu taslağı” yapıldı; mevcut tarihsel metnin hiç gönderilmediği iddia edilmez. Geçmiş teslim edilmiş kayıtlar/snapshotlar yeniden yazılmadı.

Son sonuçlar: `tests/chat-draft-browser.cjs` 11 Chrome kontrolü (satır sonu/baş-son boşluğu, sunucuda temiz taslak, tek mesaj, reload, yanıt sırasında yazılan yeni taslağın korunması, ağ hatası ve aynı istek kimliğinin korunması); genel browser 131; süreç JS 11 geçti. Değişen JS/PHP sözdizimi ve diff whitespace geçti. İlk test eski kodda beklenen şekilde başarısız; ilk düzeltme sonrası test, hata yanıtı gelmeden request key okuduğu için 9 kontrolden sonra başarısızdı. Hata bildirimi beklenerek tam tekrar geçti. Kanıtlar test-evidence/chat-prompts-2026-10-23/; önceki PHP/MariaDB/HTTP grupları bu tur yeniden koşulmadı.

Kullanıcının TEST-OGR-03 / Akranlar arası anlaşmazlık kaydı yalnız `PRAGMA query_only=ON` ile incelendi. Uygulama dışı üç aralık 2.763+2.014+33.693=38.470 sn; girişte görünür fakat odaksız, dönüşte görünür ve odaklı sinyalleri var. Başka uygulama/site adı ölçülmüyor; hedef çıkarılamaz. Kullanıcının vakasında analiz kaydı sayısı sıfır. Promptlar ve yeni öneriler AI_PROMPTS_REVIEW_TR.md içindedir; önerilen promptlar/etiket açıklamaları henüz uygulanmadı. Kullanıcının çalışma verisinde yazma, gerçek değerlendirme/yeniden etiketleme, gerçek sağlayıcı çağrısı veya GitHub push yok.

## İlk gerçek OpenRouter/GPT-5 Nano bağlantısı — 2026-10-23

Gerçek sağlayıcıyla yalnız sentetik metin/test rubriği kullanıldı. Son başarılı senaryolar: sohbet (10.559 sn), eksik nihai ölçümler için sayı uydurmadan profil yorumu (16.130 sn; örnek çıktı insan tarafından incelendi), tek soru rubrik değerlendirmesi (48.472 sn; puan/düzey, kaynaktaki alıntı, dolu gerekçe ve düz metin feedback sunucuda doğrulandı). Bunlar tek bir tam uçtan uca koşu değildir; profil ve sohbet 45 sn sınırıyla, son soru 90 sn sınırıyla doğrulandı. Nihai yerel ayar `structured_outputs=true`, `timeout=90`.

İlk denemelerde Windows PHP CA eksikliği (cURL 60), kök feedback yerine response alanı/yanlış feedback türü, metin içinde kodlanmış JSON ve bir örnekte ölçülmemiş profile varsayılan sayı atama görüldü. Bu çıktılar kabul sonucu sayılmadı. Windows'un güvenilen köklerinden yerel CA paketiyle TLS düzeltildi; sertifika doğrulaması açık kaldı. Profil/soru için katı JSON Schema ve açık eksik-ölçüm/metin talimatları eklendi; OpenRouter require_parameters kullanılır. `Review::text` JSON nesnesi/dizisi içeren yorumları reddeder. Şema tip/alan güvencesidir; anlamsal doğruluk veya bilimsel güvenirlik garantisi değildir. Araştırmacı kararı zorunlu kalır.

Güncel sentetik regresyonlar: v2 74 (6 yeni sözleşme/JSON-metin kontrolü dahil), rubrik 21, karar/feedback 17, öğrenci raporu 15, eski teslim 20, genel SQLite/Word/PDF/XLSX 81 geçti. PHP 8.4.27 v2 74 de geçti. Ürün PHP sözdizimi/diff kontrolü geçti. Chrome/HTTP/MariaDB grupları bu tur yeniden çalıştırılmadı; önceki sonuçlar tarihçedir.

Kanıt: `test-evidence/openrouter-2026-10-23/real-provider-smoke.json`, ayrı regresyon logları ve ilk başarısız/kalite gözlem kayıtları. `pre-schema-quality-observations.json` içindeki teknik passed alanları anlamsal kabul değildir: profil sayı uydurmuştur, soru feedbacki kodlanmış JSON'dur; bu nedenle sonrasında korumalar eklenmiştir. API anahtarı hiçbir kanıtta bulunmaz. Çalışma DB kullanılmadı/yazılmadı; config/local.php ve .tools Git dışındadır. Tam altı sorulu gerçek UI akışı, mesaj etiketi, gerçek rubrik kalibrasyonu ve hosting kabulü açık. Bu yeni düzeltmeler henüz commit/push edilmedi.

## Yerel hazırlık ve hata kapatma — 2026-10-23

AUDIT-01 için yeni regresyon eski kodda ilk bütünlük kontrolünde başarısız oldu; `Rules::submitted` ayrımından sonra geçti. Tarihsel v1 teslim/rapor akışı ve v2 uyumluluğu, eksik ölçümün bilinmiyor kalması, eski draft/snapshot/teslim kilidinin değişmemesi ve yeni eksik teslimin reddi doğrulandı. AUDIT-02'nin `9098ff7` düzeltmesi yeniden test edildi; puan–düzey uyuşmazlığı/boş gerekçe reddediliyor ve kabul edilmiş değerlendirme tekrar düzenlenebiliyor.

Kanıt klasörü: `test-evidence/local-readiness-2026-10-23/`. Aşağıdaki sonuçlar bu tur gerçekten çalıştırıldı; gruplar örtüşen kapsamlar içerir.

| Grup | Sonuç | Kanıt |
|---|---|---|
| Eski teslim regresyonu (PHP 8.3.36) | 20 geçti | legacy-submission.txt |
| Rubrik puan/düzey/gerekçe | 21 geçti | rubric.txt |
| SQLite genel + Word/PDF/XLSX | 81 geçti | run.txt |
| V2 inceleme/güncel rapor | 68 geçti | review-v2.txt |
| Adım yeniden hesaplama | 21 geçti | analysis-recalculate.txt |
| Karar/feedback entegrasyonu | 17 geçti | analysis-provenance.txt |
| Öğrenci raporu sözleşmesi | 15 geçti | student-report.txt |
| Süreç/etiket/dışa aktarım | 28 + 39 geçti | process-data.txt, export-labels.txt |
| Gezinme/süre hesaplamaları | 82 + 57 geçti | navigation.txt, presence.txt |
| JS analiz/sıra + V2 durum/sıra | 27 + 13 geçti | js-analysis-progress.txt, js-review-v2.txt |
| JS saat + süreç + etiket | 10 + 11 + 11 geçti | js-time.txt, js-process-data.txt, js-labels.txt |
| Genel Chrome masaüstü/mobil emülasyon | 131 geçti | browser.txt |
| V2 Chrome; kabul sonrası düzenleme/yayını geri çekme | 45 geçti | review-v2-browser.txt |
| HTTP/oturum/dosya/iki worker | 42 geçti | http.txt |
| MariaDB genel + ayrı DB'ye restore | 81 geçti; 8 tablo sayısı eşit | mysql.txt |
| PHP 8.4.27: eski teslim/rubrik/genel/V2 | 20 + 21 + 81 + 68 geçti | php84-*.txt |
| Composer validate + yerel CLI ortamı | Geçti | composer.txt, environment.txt |
| Composer platform, PHP 8.3.36 / 8.4.27 | İkisinde de geçti | platform-php83.txt, platform-php84.txt |
| Sözdizimi | 22 PHP + 8 ürün JS dosyası geçti | syntax.txt |
| Yerel teslim adayı taraması | Sır örüntüsü yok; özel config/DB/PEM/runtime Git dışında | delivery-check.json |

İlk genel SQLite koşusu 76 kontrolden sonra Windows sandbox dosya okunabilirliği kısıtında durdu; normal yerel izinli tekrar 81/81 geçti (`run-sandbox-first.txt` ilk denemedir). İlk MariaDB komutunda kurulumun üst dizini seçildiği için çalıştırılabilir dosya bulunamadı; doğru alt dizinle tekrar geçti (`mysql-path-first.txt` ilk denemedir). Bu ilk denemeler başarı sayılmadı. `js-review-editor.txt` yalnız yardımcı modülün yüklenmesini kaydeder; ayrı bir test sayısı değildir.

Tüm veri testleri ayrı sentetik SQLite/MariaDB ve mock AI kullanır; çalışma DB'sine migration/seed/reset yapılmadı. Gerçek sağlayıcı, hosting, fiziksel cihaz, gerçek kesinti, Word uygulamasında görsel açılış veya gerçek Disco import bu sonuçların kapsamında değildir. Yeni manuel batch, commit/push veya dağıtım yok. Önceki tarihli AUDIT-01/02 açık kayıtları aşağıda tarihçe olarak korunur; güncel durum BUGS.md ve STATUS_REVIEW.md içindedir.

## Genel tarayıcı testi yeniden doğrulaması — 2026-10-22

`tests/browser.cjs` içindeki eski v1 akışını zorlayan istek müdahalesi ve artık görünmeyen `data-analysis-step` beklentileri kaldırıldı. Genel test güncel beş bölümlü v2 ekranını ve tek güncel rapor akışını kullanır. Uygulama kodu, event/süre hesapları ve dışa aktarım davranışı değiştirilmedi.

- Genel Chrome testi: **131/131**. Önceki 93 kontrolün ardından analiz hatasında durma/devam, sıralı hazırlama, dört araştırmacı kararı, bağımsız yeniden hesaplama, başarısız yeniden hesaplamada önceki sonuç ve kararların aynen korunması, rapor güncelleme, Word/Excel indirme ve yayın doğrulandı.
- V2 alan testi: **56/56**; V2 JavaScript durum/sıra testleri: **13/13**.
- İzole geçici SQLite, sentetik hesaplar ve mock AI kullanıldı. Gerçek çalışma veritabanı kullanılmadı. Fiziksel cihaz, gerçek AI/hosting ve Disco içe aktarımı bu koşunun kapsamında değil.

Kanıt: `test-evidence/analysis-browser-2026-10-22.txt`. Önceki genel tarayıcı testindeki zaman aşımı bu koşuyla giderildi; aşağıdaki eski v1 genel test açıklamaları tarihsel sonuçlardır.

## Güncel kullanıcı kararı — 0.2.18 · 2026-10-21

Araştırmacı ekranından önceki yorum/kararlar, önceki değerlendirmeler ve Rapor geçmişi · Onay ve yayın listeleri kaldırıldı. Tek güncel rapor alanında yalnız Öğrenci raporunu hazırla / Word / Excel / Öğrenciye göster var. Word ve Excel doğrudan öğrenci projeksiyonunu indirir. Excel gerçek XLSX dosyasıdır; profil yorumu ve altı soru puanı/feedbacki içerir, iç karar veya geçmiş içermez. PDF backend desteği korunur fakat bu alanda PDF/önizleme düğmesi yoktur (kullanıcının son tercihi).

V2 raporu yeniden hazırlamak aynı reports.id satırını günceller; yeni rapor listesi/snapshot satırı oluşturmaz. Yeniden hesaplanan başarılı sonuç veya yeni karar önceki öğrenci görünürlüğünü kapatır; dört insan kararı yeniden tamamlandığında birleştirme mevcut raporu otomatik günceller. Güncellenen rapor yeniden Öğrenciye göster gerektirir. Tek düğme onay ve yayını birlikte yapar; mevcut vaka veri toplama kapanış/kişisel ek erişim kapısı korunur. Yeni v2 analiz başlatmak da önceki yayını kapatır. Öğrenci güncel v2 rapor dışında eski rapor kimliklerine erişemez. Araştırmacı ekranında tarihi satırlar gösterilmez; eski analiz/karar kayıtları silinmez. Eski v1 rapor üretim sözleşmesi korunur. Yeni v2 feedback API düzenlemesi de aynı raporu günceller. Sürüm etiketi v2 öğrenci ekranı/Word dosyasından kaldırıldı.

Şema/migration ve çalışma verisine doğrudan yazma yok; yalnız uygulama davranışı değişti. Testler ayrı sentetik SQLite/mock ortamında. 53 PHP ve 36 Chrome kontrolü; mobil/masaüstü yerleşim, aynı rapora yazma, otomatik güncelleme, Word/XLSX ve tek tık gösterme doğrulandı. Gerçek AI/hosting veya kullanıcı manuel kabulü sayılmaz. Kanıtlar test-evidence/current-report-*. Commit/push/dağıtım yok. Önceki 0.2.17 snapshot koruma açıklaması yeni v2 raporların üzerine yazılması için artık geçerli değildir.

## Güncel ek sonuç — 0.2.17 · 2026-10-21

Yeni beş bölüm sözleşmesi, izole SQLite ve mock AI ile doğrulandı. Çalışma DB'sine yazılmadı. Gerçek sağlayıcı/hosting ve manuel kullanıcı kabulü bu sonuçların kapsamında değildir.

- tests/review-v2.php: 48 kontrol; altı ayrı soru payload/puanı, puansız profil, yorumsuz sohbet, düzenleme/ret/yenile, eski snapshotlar, rapor feedback sürümü ve onay/yayın/erişim kapıları.
- tests/review-v2.cjs: 13 kontrol; sıra, dört karar kapısı, bağımsız yenileme ve güncel birleştirme.
- tests/review-v2-browser.cjs: 28 kontrol; tek başlatma, hatada dur/devam, beş kart, puan/yorum düzenleme, öğrenci raporu ve Word/PDF, 1440/768/390 taşmama. İlk koşu asenkron rapor oluşturmayı beklemeyen test seçicisinde durdu; kesin oluşturma yanıtı beklendikten sonra tam koşu geçti.
- Genel/tarihsel tests/browser.cjs: 130 kontrol; fixture açıkça v1 başlatır. Yeni UI yukarıdaki ayrı testle doğrulandı.
- Genel PHP 81, eski analiz JS 27, tekrar PHP 21, inceleme PHP 17, eski öğrenci raporu PHP 15 geçti.

Kanıtlar test-evidence/review-v2-*. Yeni kaynaklar PHP/JS sözdizimi ve diff kontrolünden geçirildi. Önceki kayıtlar aşağıda tarihçe olarak korunur.

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


## 0.2.13 — Makale yöntemi ve uygulama içi ölçüm özeti

2026-10-21 · 0.2.13 yerel: Kullanıcı için METHODS_EVENTS_DURATIONS_TR.md yöntem taslağı/etkinlik sözlüğü/örnekler hazırlandı. Araştırmacı bölüm geçişleri altında açılır Etkinlikler ve süreler nasıl yorumlanır? özeti eklendi; olay/mesaj etiketi, görüntüleme/güncelleme/kilit, görünür+odak koşulu, hareketsizliğin dahil olması, uygulama/vaka dışı ayrımı, AI beklemesinde çift saymama, teslim ve eksik süre kuralları açıklanır. Ölçüm mantığı/DB/etiketler değişmedi. Yerel sekiz etiketin açıklamaları sentetik başlangıç tanımı olduğundan bilimsel kodlama yönergesinin tamamlanması gerektiği belgede açıklandı; güvenirlik çalışması yapılmış gibi sunulmadı. Chrome 1440/768/390 genişlikte kılavuz taşmıyor; mevcut sentetik yol verisiyle render, DB erişimi yok. JS/PHP syntax ve diff kontrolü geçti. Önceki fonksiyonel testler bu tur yeniden koşulmadı. Bu yeni düzenleme henüz commit/push edilmedi.

Kanıt: test-evidence/measurement-guide-layout.txt.

## 2026-10-21 — Güncel durum incelemesi

Ayrıntılar STATUS_REVIEW.md; kalıcı loglar test-evidence/review-2026-10-21/ altında. Bu tur yeniden çalışan sonuçlar: SQLite genel81, navigation82, presence57, süreç28, export39, öğrenci raporu15, rubrik14, JS zaman10/etiket11/süreç11/analiz12, genel Chromium127, iki-worker HTTP42, erişim/yayın119, MariaDB81. Ayrı restore DB'de8tablo kayıt sayısı eşit; içerik hash eşitliği iddiası yok. PHP28dosya ve ürün JS7dosya syntax/diff kontrolü geçti. Gruplar örtüşür.

İlk sandbox dosya okunabilirliği ve localhost bağlantı koşuları tamamlanmadı; normal izinli yerel tekrarlar geçti. Mevcut çalışma DB'si/ürün kodu değişmedi. Yeni iki bellek içi sentetik probe mevcut testlerin kapsamadığı açıkları doğruladı: AUDIT-01 eski final_severity bulunmayan teslim analiz1'de failed; AUDIT-02 Yetersiz=0 tanımına karşı score4/levelYetersiz/boş reason kabul ediliyor. Bunlar geçti sayılmadı; BUGS.md içinde açık. Probe çıktısı test-evidence/review-2026-10-21-probes.txt; kaynak betik kanıt klasöründe probes.php.txt olarak saklandı.

Özel confirmation/presence/journey browser, validation/researcher-ui ve uzun metin yerleşim grupları bu tur tekrar çalıştırılmadı. Gerçek AI/hosting/fiziksel cihaz-kesinti/Disco import kabulü yapılmadı. Commit/push/dağıtım yok.

## 0.2.14 — Araştırmacı süre özeti ve AI yanıt durumu

İki bölüm kullanıcı isteğiyle sadeleştirildi. 130genelChrome ve11süreçJS geçti. Üç genişlikte (1440/768/390) taşma yok; üç süre grubu, eksik kayıt uyarısı, sorunlu istek, boş kayıt ve tüm istekler başarılıyken kapalı teknik ayrıntılar sentetik DOM ile doğrulandı. Masaüstü ve mobil ekranlar görsel incelendi. İlk Chrome koşusu68kontrolden sonra değişen eksik-süre metni beklentisinde durdu; sıfır eksik kaydın da açık gösterilmesiyle tam tekrar130geçti. Kanıtlar test-evidence/readable-panels-browser-first.txt, readable-panels-browser.txt, readable-panels-layout.txt. Ölçüm/backend/çalışma DB değişmedi; mevcut AUDIT-01/02 açık. Gerçek AI/hosting ve fiziksel cihaz testi bu tur yapılmadı. Commit/push/dağıtım yok.

## 0.2.15 — Tek başlatmayla sıralı analiz, insan incelemesi ve sade öğrenci raporu

Kullanıcı onayıyla1–7adımları tek başlatma sonrası ayrı isteklerle sırayla ilerler; hata/ağ belirsizliğinde durur, tamamlanan adımları yeniden çalıştırmaz.3–7kararları kullanıcıya aittir; son karar sonrası8birleştirme otomatik başlar. Rapor hazırlama/onay/yayın ayrı. Ekrandan ayrılınca sonraki adım başlatılmaz; çalışan istek tamamlanabilir. Tek-adım kontrolleri isteğe bağlı açılır alanda korunur.

Kullanıcının son düzeltmesiyle öğrenci raporuna AI/araştırmacı kaynak etiketi EKLENMEZ. Bu tur ilk yorumda eklenen kaynak alanları/gösterimleri kaldırıldı; iç karar/AI geçmişi araştırmacıda korunur. Öğrenci ekranı ve Word/PDF puan/feedback odaklıdır, yanıt metinlerini tekrarlamaz. Araştırmacı dosyasında öğrenci cevapları ve AI önerileri korunur. Onaysız raporda genel/ölçüt feedback düzenlemesi ayrı yeni onaysız rapor sürümü üretir; puanlar ve önceki rapor değişmez. Onaylı rapor üzerine yazma ve öğrenci hesabından düzenleme reddedilir.

Son sonuçlar:119Chrome,81genelSQLite,15öğrenciraporunit,17inceleme/feedbackintegration,12analizilerleme+10sıralamaJS geçti.119erişim/yayın kontrolü bu tur sıralı analiz/source ilk düzenlemesinden sonra geçti; son feedback düzenleme endpointi için sahiplik/rol/onaylı snapshot korumaları17integration içinde test edildi. PHP/JS syntax ve diff whitespace geçti. Chrome gerçek UI: geçersiz mock çıktıda3.adımda durma,1/2tekrar etmeden3–7devam,aynı andaençok1adımisteği,otomatikkararverilmemesi,yenilemede7sonucun korunması,manuel3–7kararısonrasıotomatik8,feedbackdüzenleme/yeni sürüm/onay/yayın,öğrencidekaynaketiketiyokluğu,mobilrapor,PDF/DOCXindirme. DOCX XML ayrıca incelendi: düzenlenen feedback var, kaynak etiketleri ve yinelenen öğrenci yanıtları yok. Mobil analiz/öğrenci raporu ekranı görsel incelendi.

Kanıtlar test-evidence/analysis-reviewed-report-{final,domain,unit,integration}.txt ve analysis-reviewed-sequence-unit.txt. Erken denemeler analysis-sequence-browser-first.txt (kapalı details içindeki hatayı görünür bekleyen test seçicisi) ve analysis-sequence-browser-label-first.txt (değişen düğme adını bekleyen eski test) başarılı sayılmadı; son tam koşu geçti.118kontrollü önceki sıralı/provenance koşuları tarihçedir; final119kontrollü sade rapor koşusu günceldir. Gerçek AI/hosting/Disco veya fiziksel cihaz doğrulaması yok. Çalışma DB'si/seed/migration/commit/push/dağıtım yok; AUDIT-01/02açık.

## 0.2.16 — Açık inceleme kartları ve yalnız seçili değerlendirmeyi yenileme

Kullanıcının eski ekranın karışık olduğu bildirimiyle ana alan5açık karta dönüştü.3/4ölçütpuanları/ağırlıklıtoplam/feedbackaçık;5–7niteldeğerlendirmesayısalpuanüretilmedengösterilir. Teknik1/2/8,girdi/kanıt,eskiAIsonuçları/kararlarkapalıayrıntılardakorunur. DoğrudanKabul et,Düzenle,Yalnız bunu yeniden hesaplat;editpuanveöğrencifeedbackiilebaşlar. Kabul ettıklamasıaraştırmacıkararıdır,otomatikinsanonayıyoktur. YeniAIrubriksonucundafeedbackayrıalandır;eskikayıttayoksaaçıkaçıklanır,hamverideğiştirilmez. Onaylıfeedbackraporaaynımetinleaktarılır;öğrenciyekaynakbilgisigösterilmez.

Sonuçlar:130Chrome,81genelSQLite,21seçiliadımtekrar,17inceleme/feedback,15öğrencirapor,27analizJS(12ilerleme+5yenisonuç/birleştirme+10sıralama),28süreçPHP/11süreçJSgeçti.5PHPve3ürünJSsyntaxkontrolü/diffwhitespacegeçti. Chrome:doğrudangörünürpuan/feedback/3düğme,kabul/edit,yalnız3adımıyenileme,hatalıyenilemedeeskisonucukoruma,diğer4adımındeğişmemesi,yenisonucunyenionayistemesi,8birleştirmeyiyenileme,öğrenciyanıtınıkarttanaçma,mobiltaşmama,karttadüzenlenenfeedbackinrapordaaynıkalması,rapordüzenleme/onay/yayın/DOCX/PDF. PHPayrıcaeskiadımkimliğinekararverilememesi,aynırundaparalelistek/karar/raporkoruması,eskiraporlarınsabitkalmasıveaynıkararlarıtekrarbirlestirmemereddinidoğruladı.

Kanıt:test-evidence/review-cards-*.txt;review-card-desktop.png/review-card-mobile.pnggörselincelendi. İlk Chrome koşusu 107 kontrolden sonra kapalıteknik8satırınıgörünürbekleyentestseçicisindedurdu;attachedbeklemesinegeçilince130tamgeçti. İlkstandartPHPkoşusuXLSXis_readablesandboxsınırındadurdu;normalizinlison81koşugeçti. İlkkoşulargeçtisayılmadı. ÇalışmaDB/seed/migrationyok;gerçekAI/hosting/Discoimport/fizikselcihazdoğrulamasıyapılmadı. Kullanıcıyenikartdüzeninikabul etmişsayılmadı;yenimanuelbatchçıkarılmadı. Commit/push/dağıtımyok. AUDIT-01/02buçalışmadakapatılmadı.

## PHP güvenlik güncellemesi · 2026-10-23

Yerel PHP 8.3.35 → 8.3.36; resmî Windows NTS x64 ZIP SHA256 doğrulandı. Mevcut 8.3 dalı ve php.ini korundu. Yeni paketin eklentileri kullanıldı; mevcut yerel VC runtime DLL'leri taşındı. Composer bağımlılık sürümleri değişmedi. PHP minimum güvenlik seviyeleri Composer ve CLI ortam kontrolüne eklendi.

| Kontrol | Sonuç | Kanıt klasörü: test-evidence/php-security-2026-10-23/ |
|---|---|---|
| Ortam ve eklentiler | Geçti | environment.txt |
| SQLite genel/Word/PDF/XLSX | 81 geçti | domain.txt |
| Güncel v2 öğrenci raporu | 68 geçti | review-v2.txt |
| Veri çıktıları | 39 geçti | exports.txt |
| HTTP/oturum/iki worker/dosya indirme | 42 geçti | http.txt |
| MariaDB işlevler + ayrı DB'ye restore | 81 geçti; 8 tablo sayısı eşleşti | mysql.txt |
| Eski PHP 8.3.35 ortam kontrolü | Beklenen exit 1 ile reddedildi | old-version-rejected.txt |

Toplam 311 otomatik kontrol geçti; ayrıca 8 restore tablo sayısı doğrulandı. Aktif `.tools/php/php.exe` üzerinde sürüm, CLI check ve Composer validate tekrar geçti; Composer platform gereksinimleri yeni runtime'da geçti. İlk sandbox check storage erişiminde durdu; normal izinli tekrar başarılı. Python PATH takma adı bulunamadığı için HTTP/MariaDB testleri paketlenmiş Python'un mutlak yoluyla tekrar çalıştırıldı. Testler yalnız ayrı sentetik DB/mock kullanır; çalışma DB'sinde migration/seed/reset yapılmadı. Hosting, gerçek AI ve tarayıcı görsel kabulü bu koşuda test edilmedi. Canlı sunucu güncellemesi veya commit/push yok.

## PHP 8.4.27 uyumluluk doğrulaması · 2026-10-23

Hostingini PHP 8.4.27'ye aldığını belirten kullanıcının uyumluluk sorusu üzerine resmî Windows NTS VS17 x64 paketi ayrı `.tools/php-8.4.27-test` klasöründe test edildi. SHA256 `93bd8a815a52156606fb906d0573437908c61f96e2cc52c03a51e36a41bf5236` doğrulandı. Varsayılan yerel PHP 8.3.36 olarak kaldı.

Composer platform gereksinimleri ve CLI ortam kontrolü geçti. 81 SQLite genel (Word/PDF/XLSX dahil), 68 review-v2, 39 export, 42 gerçek HTTP/oturum/iki worker ve 81 MariaDB kontrolü: toplam 311 geçti. Ayrı restore DB'de 8 tablo sayısı eşleşti. Üç doğrudan PHP grubu E_ALL (32767) ile çalıştı; çıktılarda PHP Warning/Deprecated/Fatal görülmedi. Kanıtlar `test-evidence/php-8.4.27-compatibility/`. Ürün kodunda uyumluluk düzeltmesi gerekmedi. Çalışma verisine migration/seed/reset yok; canlı hosting/gerçek AI/fiziksel cihaz doğrulaması değildir.


## 23 Ekim 2026 — öğrenci / AI etiket grupları ve prompt v3

Etiket tanımları artık body.target_role=user veya assistant ile açıkça grupludur. Oluşturma/düzenleme ekranı, manuel seçim API/UI, AI JSON şeması ve sonuç doğrulaması aynı rol kuralını uygular. Eski grubu olmayan tanımlar tahminle atanmaz; yeni AI etiketlemesinden önce araştırmacı düzenlemelidir. Tarihsel kararlar korunur. Güncel tanımların tamamı her etiketleme isteğine eklenir; grup da önbellek anahtarının parçasıdır.

Sohbet chat-v3 ve etiketleme message-labels-v3 promptlarının tam metni ve birlikte odak testi adımları AI_PROMPTS_REVIEW_TR.md içinde. İlk prompt başarısızlıkları tarihsel bulgudur; son sürüm sonuçları test-evidence/label-groups-2026-10-23 altında ayrıca tutulur. Genel model doğruluğu iddiası yoktur. Kullanıcının mevcut çalışmasında değerlendirme veya yeniden etiketleme başlatılmadı.


## 23 Ekim 2026 — odak ölçümü adlandırması

Kullanıcıyla TEST-OGR-04 üzerinde yapılan denemede sayfa görünürken 3,440 sn odak kaybı gözlendi; ekran yakalama aracı kullanımıyla uyumlu. Bu gözlem hedef uygulamayı belirlemez. “Uygulama dışında” başlığı, süreç ekranı, olay adları ve yeni üretilen gezinme/dışa aktarım açıklamalarında “Vaka sekmesi etkin değil” olarak düzeltildi. Hesaplama, ham olaylar, outside/APP_OUTSIDE teknik kodları değişmedi. Süre 60, gezinme 82 ve süreç JavaScript 11 kontrolü geçti.

Mevcut yedi öğrenci etiketi için dahil etme/dışlama ölçütleri ve 12 örnekten oluşan STUDENT_LABELS_DRAFT_TR.md taslağı hazırlandı. Kullanıcının etiket tanımları veritabanında değiştirilmedi; taslak model çıktısı veya onaylı araştırma kodlama şeması değildir.


## 23 Ekim 2026 — kabul edilen etiketlerin yerelde etkinleştirilmesi

7 öğrenci etiketi v2, 9 AI yanıtı etiketi v1 olarak Admin::mutate/definition üzerinden kaydedildi. SQLite yedeği alındı; yanıt/mesaj/geçmiş etiket/değerlendirme tabloları değişmedi. Katalog label-definitions/2026-10-23.json içinde. Model testi güncel tanımları salt okunur aldı ve yalnız sentetik mesaj gönderdi.

message-labels-v4 promptu tekrarları yasaklar, eleştiri/açıklama sınırını ve çoklu davranışı netleştirir; önbellek sürümü yenilendi. Gerçek GPT-5 Nano karşılaştırmasında 32 mesajdan 27 tam küme eşleşmesi (öğrenci 13/16; AI 14/16). Beş uyuşmazlık giderilmiş sayılmaz. İlk 32 mesajlık çağrı başarısız; v3 gruplarında tekrarlı etiket ve anlam hataları vardı. Tüm denemeler test-evidence/label-catalog-2026-10-23 altında saklandı. Detay RESULTS_TR.md. 22 yerel sözleşme kontrolü geçti. Kullanıcının vakalarında etiketleme veya değerlendirme başlatılmadı.


## 23 Ekim 2026 — öğrenci etiketlemesi önceliği / message-labels-v6

Kullanıcının isteğiyle öğrenci etiketlemesi ana işlem, AI yanıtı etiketlemesi ayrı ve isteğe bağlı oldu. Öğrenci API hedefi user, AI bağlamı context_messages içindedir. Yalnız seçilen grubun güncel ad/açıklamaları iletilir. Öğrenci için etiket başına boolean şema ve katı decoder kullanılır; sonuç benzersiz etiket listesine çevrilir. Açıklama wire JSON içinde yinelenmez. Öğrenci çağrıları dört hedeflik parçalara ayrılır ve tamamı doğrulanmadan yazılmaz. Grup bazlı önbellek ayrıdır; araştırmacı seçimleri korunur. Eski karma analiz çağrıları all uyumluluğunu sürdürür.

Gerçek GPT-5 Nano: önceki öğrenci örnekleri 15/16, yeni örnekler 10/12; toplam25/28 tam etiket kümesi eşleşmesi. İki kapsam dışı finansal istek ve bir temel kavram tanımı hâlâ yanlış etiketleniyor. Öğrenci etiketlemesi tamamen doğrulanmış sayılmaz. Testler yalnız sentetik mesajlarla ve salt okunur güncel tanımlarla yapıldı; kullanıcının vakalarında yeniden etiketleme/değerlendirme yok. Rapor test-evidence/student-labels-2026-10-23/RESULTS_TR.md. Yeni20 yerel kontrol geçti.

Öğrenci önceliği son regresyon: Chrome134, öğrenci sözleşmesi20, etiket sözleşmesi22, süreç28, sohbet9, süreç JavaScript11, değerlendirme74 kontrolü geçti. Canlı modelin 3/28 uyuşmazlığı açık.


## 24 Ekim 2026 — öğrenci etiketleme hataları / message-labels-v11

Önceki üç hata son kontrollü koşuda giderildi. Gerçek GPT-5 Nano:44/44 regresyon + uygulama/prompt sabitken ilk kez denenen 8/8; toplam 52/52. Değiştirilmiş ad/açıklama ve açık konu dışı etiket kontrolü 4/4. Önceki denemeler korunur. Bu bir genel %100 doğruluk garantisi değildir. Tam rapor test-evidence/student-labels-2026-10-24/RESULTS_TR.md; kaynak/fixture sabitleme özetleri message-labels-v11/frozen-holdout-manifest.json.

Öğrenci hedefi kendi context_before alanındaki önceki konuşmayla kodlanır. Yeni topic_scope alanı araştırmacı kontrolündedir:pedagogical/outside/any. Kabul edilmiş7 öğrenci tanımı açıklamaları korunarak v3 pedagojik kapsamla kaydedildi; SQLite yedeği alındı, yanıt/geçmiş karar/değerlendirme tabloları değişmedi. Model message_scope döndürür; sunucu uygun olmayan kapsamı dışlar ve konu kararını audite yazar. Güncel Dahil/Dışla örnekleri tam açıklamanın yanında ayrı ipuçları olarak iletilir. Wire kimlikleri kısaM/L kodlarıdır; doğrulama sonrasında kalıcı kimliklere eşlenir. Önbellek v11 ile yenilendi. Hiçbir kullanıcı vakasında yeniden etiketleme veya puanlama başlatılmadı.

Kontroller: öğrenci33, genel etiket22, süreç28, değerlendirme74, ana81, dışa aktarım39, gezinme82, süre60; Chrome ana135 ve grup/kapsam11 geçti. Güncel tam prompt AI_PROMPTS_REVIEW_TR.md içinde.

## 24 Ekim 2026 gerçek model ile sentetik uçtan uca test

İzole test DB üzerinde sohbet → öğrenci etiketi → teslim → V2 değerlendirme → araştırmacı düzeltmesi simülasyonu → öğrenci raporu → PDF/DOCX/XLSX/Disco akışı tamamlandı. Çalışma DB açılmadı, mevcut kullanıcı verisi değişmedi. Yerel439 ve Chrome etiket grubu/kapsam11 kontrolü geçti. PDF son üç sayfa render/görsel ve altı yanıt/toplam metin kontrolü geçti; DOCX paket/metin geçti, LibreOffice bulunmadığından görsel kontrol tamamlanamadı.

AI-SCORE-04 AÇIK: GPT-5 Nano gerçek vaka ilişkisini içeren yanıtı yok sayıp sıfır verebiliyor. Yeni review-v2.1 promptu ve iki sabit karşılaştırma2/2 bu geniş akıştaki hatayı kapatmıyor. Sentetik rapor yalnız araştırmacı düzeltmesi simülasyonuyla tamamlandı, akademik uzman onayı değildir. Geçersiz JSON/kanıt çıktıları için şema, düz metin doğrulaması ve güvenli hata audit kaydı güçlendirildi. Puanlamada gerçek alıntı cümlesi seçimi ve gerekçe-önce üretimi var; insan inceleme kapısı sürer. PDF soru başlıkları yanıttan ayrılmayacak şekilde düzenlendi. Ayrıntı test-evidence/live-workflow-2026-10-24/RESULTS_TR.md. GitHub gönderimi yapılmadı. Sonraki iş: araştırmacının önceden puanladığı farklı düzeylerde örneklerle tekrarlı puanlama kalibrasyonu; öğrenci etiketleme önceliği korunur.

## 24 Ekim 2026 kısa ön değerlendirme promptu review-v2.2

Kullanıcı değerlendirmeyi araştırmacının inceleyeceği bir ön öneri olarak kullanacağını netleştirdi. Soru sistem promptu1579 karakterden808 karaktere indirildi (%49). Rubrik açıklamaları, sorunun kapsamı, gerçek kanıt, düzey-puan tutarlılığı, kısa geri bildirim ve araştırmacının son kararı korundu. Teknik şema/doğrulama kuralları, sohbet ve etiketleme promptları değişmedi. Önceden sabitlenmiş güçlü gerekçe ve gerekçesiz yanıtın gerçek GPT-5 Nano kontrolü2/2, yerel değerlendirme kontrolleri85/85 geçti. Kanıt: test-evidence/live-workflow-2026-10-24/scoring-20261024-182653. Bu küçük kontrol önceki anlamsal tutarsızlık kaydını kapatmaz; araştırmacı incelemesi mevcut iş akışının parçasıdır. Güncel metin AI_PROMPTS_REVIEW_TR.md son bölümünde.

## 24 Ekim 2026 nihai algılanan zorluk ölçümü

Kullanıcı isteğiyle nihai değerlendirmeye final_difficulty alanı eklendi. Yeni teslimlerde boş başlar ve0–10 tam sayı seçimi zorunludur;0 geçerli yanıttır. Kaydetme, teslim snapshotı, araştırma veri dışa aktarımı, araştırmacı puan tablosu ve başlangıç/nihai karşılaştırması alanı korur. V2 profil ön değerlendirmesine zorluk değişimi de iletilir. Örnek tarayıcı kontrolünde başlangıç6, nihai0, fark−6 birebir doğrulandı. Eski teslimlerde eksik nihai zorluk uydurulmaz; eski kayıtlar ve rapor snapshotları otomatik değiştirilmez. Şema migrasyonu gerekmez; veri mevcut JSON gövdelerinde tutulur. İstemci dosyalarının önbellek sürümleri0.2.26 olarak yenilendi.

Kontroller: ana87, V2 değerlendirme86, eski teslim uyumluluğu21, dışa aktarım39, süreç28; Chrome doğrulama50 ve tam tarayıcı137 geçti. JavaScript/PHP sözdizimleri ve diff kontrolü geçti. Testler izole sentetik veriler/mock sağlayıcıyla yapıldı; çalışma DB değiştirilmedi. Canlı modele çağrı veya GitHub gönderimi yapılmadı.

## 24 Ekim 2026 yerel analiz süre kesilmesi düzeltmesi

Kullanıcı ekranındaki Unexpected token '<' hatasında çalışan8088 PHP cli-server üzerinden max_execution_time=30, display_errors=1 ve log_errors=0 ölçüldü; sağlayıcı timeout90 saniye. Çok sorulu adım PHP sınırında kesilip HTML hata çıktısıyla JSON yanıtını bozuyor ve analysis_steps satırını running bırakıyordu. Gerçek HTTP sentetik testinde aynı hata yeniden üretildi.

ApiRuntime: sağlayıcı çağrısı öncesi PHP bütçesi timeout+15 saniye olarak yenilenir; cURL timeoutu korunur. API ekran hata çıktısını kapatıp günlüklemeyi açar. Fatal kapanışta yalnız bu isteğe bağlı çalışan analiz satırı failed olur ve güvenli JSON hata yanıtı döner. Başka/tamamlanmış sonuçlar değiştirilmez. İstemci JSON dışı hata çıktısında okunabilir Türkçe mesaj verir. Profil promptu kısa doğal dil ister, final_collected vb teknik alan adları ayrıca yorum doğrulamasında reddedilir. Soru promptu değişmedi; değerlendirme directive review-v2.3, app.js önbelleği0.2.27.

Çalışma SQLite için backup-before-analysis-timeout-20261024-184738-cec2bb0e28ae289be8f53f15999b396e.sqlite yedeği alındı. Gözlenen iki kesilmiş adım ea9d7b5969d04834cb1f03da9e51d0cd ve18205466b515f379b6cc725234db939b yalnız failed/request_interrupted durumuna alındı, audit kaydı eklendi. Öğrenci taslakları/revizyonları/teslim kilitleri, snapshotlar, mesajlar, insan kararları, raporlar ve tamamlanmış adımların hashleri aynı kaldı. AI değerlendirmesi otomatik yeniden başlatılmadı.

Kontroller: tests/api-runtime.cjs gerçek HTTP'de1 saniyelik sunucu sınırı, ardışık iki uzun beklemede bütçe yenileme, fatal/timeout JSON yanıtları, yalnız ilgili satırın kurtarılması ve tamamlanmış sonucun korunmasını doğruladı. Chrome138, ana87, V2 değerlendirme87, eski teslim21 kontrolü geçti. Tanı endpointi kaldırıldı. Testlerde gerçek sağlayıcı çağrısı yok. Kullanıcı Ctrl+F5 ardından Analize devam et ile ilerleyebilir. GitHub'a gönderim yapılmadı.

## 24 Ekim 2026 öğrenci raporu görünürlüğünü geri alma

Kullanıcı isteğiyle V2 rapor kartına ayrı Göstermeyi geri al düğmesi eklendi. Raporun durumu metinli ve renkli etiketle Öğrenciye açık/Öğrenciye kapalı gösterilir. Açık raporda göster düğmesi pasif, geri al düğmesi etkindir; geri alındığında ve yayın koşulları sağlandığında tersi olur. Yayın koşulları sağlanmıyorsa mevcut kapanış uyarısı ve engeli sürer. Pasif düğmeler nötr gri, etkin göster yeşil, etkin geri al kırmızı tonludur. Görünürlük eylemleri hazırlama/indirme düğmelerinden ayrı alandadır.

Yetkili araştırmacının report/hide işlemi yalnız published_at değerini temizler. Rapor onayı, içeriği ve kimliği korunur; işlem audit kaydına yazılır. Öğrenci rapor listesi ve indirme erişimi kapanır. Aynı rapor mevcut veri toplama ve güncel değerlendirme kapılarıyla yeniden gösterilebilir. Öğrenci bu işlemi yapamaz. Mevcut çalışma raporlarının görünürlüğü otomatik değiştirilmedi.

Kontroller: V2 değerlendirme94, ana87, eski teslim21, tam Chrome142 geçti. Testler göster→geri al→yeniden göster, düğmelerin karşılıklı etkinliği, durum etiketleri, öğrenci erişiminin kapanması, içerik/onay korunması, tekrarlı geri alma ve audit kaydını kapsar. Son masaüstü kapalı ve mobil açık ekran görüntüleri görsel incelendi. Cache sürümü app/review/CSS0.2.28. Gerçek sağlayıcı çağrısı veya GitHub gönderimi yok. Kullanıcı Ctrl+F5 ile yenileyebilir.
# 2026-10-24 — Yeni deneme (0.2.29)

- `tests/new-attempt.php`: SQLite 39, MariaDB 11.4.12 üzerinde 38 kontrol. Dolu şema 002→003 geçişi, eski kayıtların korunması, yetki/gerekçe, boş yeni deneme, aynı vaka sürümü, tekrar istek güvenliği, ikinci teslim ve üçüncü deneme doğrulandı. MariaDB yedek/geri yüklemede sekiz tablo sayısı eşleşti.
- `tests/new-attempt-browser.cjs`: Chrome masaüstü/mobil 13 kontrol. Araştırmacı açılışı, gerekçe doğrulaması, deneme geçişleri, eski yanıtın salt okunur olması, yeni yanıtın kaydı, tek vaka kartı ve mobil taşma kontrolü geçti. Gerçek AI kullanılmadı.
- Mevcut testler: `tests/run.php` 87, `tests/researcher-ui.cjs` 50, `tests/legacy-submission.php` 21, `tests/review-v2.php` 94 kontrol geçti. PHP dosya okunabilirliği ve Chrome için Windows sandbox dışındaki ayrı sentetik test ortamı kullanıldı.
- Yerel SQLite: `bin/upgrade-sqlite.php` özel yedeği doğruladı; 22 tablonun eski sütunları ve kayıtları aynı kaldı. Migration tekrarında değişiklik yok. Yerel öğrenci adına yeni deneme açılmadı; canlı sağlayıcı isteği gönderilmedi.

## 24 Ekim 2026 — Disco deneme numarası / 0.2.30

Disco visits/events CSV dosyalarında bütün satırlar, kesinleştirme/teslim kayıtları ve boş başlıklar Deneme numarası içerir. İki deneme aynı Participant/Vaka ID, farklı Case ID ile ayrı süreçlerdir; aradaki bekleme süreye eklenmez. SQLite ve MariaDB üzerinde 35'er Disco kontrolü, gerçek HTTP indirmesinde her iki kimlik ve sütun doğrulandı. Tam koşuda bulunan değerlendirme düzenleyicisinin erken kapanma yarışı düzeltildi; eski test metinleri/verileri güncellendi. Odak testi, bağımsız DOM gözlemlerini başlangıç/bitişlerle karşılaştırır; üretim süre kuralları değişmedi.

Son tam koşu 20261024T194310Z: 38/38 yerel paket, 78/78 sözdizimi/diff, toplam 116/116 başarılı. Önceki başarısız koşular korunur. Kanıt ve kapsam: test-evidence/disco-attempts-2026-10-24/RESULTS_TR.md. Altı canlı sağlayıcı testi başarılı sayılmadı: harici veri aktarımı ve ücret için açık onay gerektiğinden başlatma komutu otomatik onay denetimince reddedildi. Gerçek AI çağrısı veya bu turda çalışma veritabanı değişikliği yapılmadı.

## 26 Ekim 2026 — shared hosting MySQL boş bağlantı süresi

Canlı günlükte uzun değerlendirme sonrası PDOException görüldü; phpMyAdmin ekranında global wait_timeout ve interactive_timeout 60 saniye. Sürücü hata numarası kaydedilmediğinden canlı PDOException'ın kesin alt nedeni henüz doğrulanmadı. Db bağlantısı artık SET SESSION wait_timeout = GREATEST(@@SESSION.wait_timeout, 600) uygular. Global sunucu ayarını değiştirmez, daha uzun mevcut oturum süresini düşürmez; SQLite dalı korunur.

Yeni tests/db-idle-timeout.php yalnız geçici loopback vakalab_test_ veritabanında çalışır. Kısa global süreyi yalnız bu izole testte 2 saniyeye ayarlar ve finally içinde geri yükler. Korumasız bağlantının 3 saniye beklemede 2006/2013 ile kapanması, uygulama bağlantısının açık kalması, global değerin korunması ve bekleme sonrası transaction içinde yazma dahil 6/6 kontrol geçti. mysql_checks.py bu testi de çalıştırır. MariaDB 11.4.12 ana 87/87, yeni deneme 38/38, Disco 35/35 ve ayrı yedeğin geri yüklenmesinde 8 tablo sayısı eşleşti. SQLite ana 87/87 geçti. PHP syntax ve diff kontrolü geçti.

İlk sandbox denemesinde yerel MariaDB loopback erişimi 10013 ile engellendi; SQLite dosya dışa aktarımında okuma hatası oluştu. Otomatik incelemeden geçen sandbox dışı tekrarlar başarılıdır. Gerçek sağlayıcı çağrısı veya canlı veritabanına bu testlerden erişim yok. Canlıya kullanıcı tarafından app/Db.php yüklenmesi ve yeniden değerlendirme doğrulaması bekleniyor. Hazır tek dosya storage/releases/mysql-idle-timeout-fix/Db.php; SHA-256 A673B47DF81C019E0DF81CFE8D2BA40D9623DE3DA96A28A1C9BC7888A98A5C13.

26 Ekim hosting takip: İlk GREATEST ifadesi içeren dosya yüklendikten sonra başlangıç API isteği hata verdi; doğrudan sürücü kodu yok. Kullanıcının phpMyAdmin bağlantısında SET SESSION wait_timeout = 600 ve SELECT sonucu 600 başarılı. Uygulama dosyası aynı sabit tamsayı ifadesini kullanacak şekilde revize edildi; artık mevcut daha uzun oturum değerini koruma iddiası geçerli değil. PHP syntax geçti. Bu son ifadenin canlı uygulama DB kullanıcısıyla doğrulaması bekleniyor; önceki yerel test başarıları ilk sürüme aittir.

## 26 Ekim 2026 — review-v2.4 yanıt varlığı ve puanlama girdisi
Açık öğrenci yanıtı alanı, soru kapsamlı prompt ve dar yanıt-yok çelişki doğrulaması eklendi. Yeni canlı model başarısı iddia edilmez. scoring-presence26, review-v2 96, ana87, eski teslim21, sohbet9, etiket22 geçti. Kapsam/sınırlar: test-evidence/scoring-answer-2026-10-26/RESULTS_TR.md. Canlıda yalnız Ai.php ve Review.php yüklenmesi, başlangıç bölümünün yeniden hesaplanması ve araştırmacı incelemesi bekleniyor.
