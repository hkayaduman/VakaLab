# Hatalar, düzeltmeler ve açık doğrulamalar

## Gerçek GPT-5 Nano davranış açıkları — 2026-10-23

- **AI-BEHAVIOR-01 · AÇIK:** chat-v2 ve doğal API rolleriyle kısa vaka sorusu düzeldi; kapsam dışı finans/hukuk sorusunda model talebi reddetse de istem dışı pedagojik plan/davet ekledi. Prompttaki yasak ve örneklere rağmen tekrarlandı. Teknik başarı veya bütünüyle çözülmüş sayılmaz.
- **AI-LABEL-01 · AÇIK:** güncel açıklamalar her istekte gönderiliyor; ancak adlar sabitken açıklaması daraltılan etiketlerin dışlama kuralları gerçek sentetik GPT-5 Nano testinde uygulanmadı. Normal öğrenci/AI rol eşleşmesi geçti. Katı şema yalnız geçerli kimlik/JSON garantisidir; bu semantik hatayı çözmez.
- Teknik aktarım/cache/audit/yarış korumaları uygulandı; kullanıcının gerçek etiketleri değiştirilmedi ve mevcut vakasında değerlendirme/yeniden etiketleme yapılmadı. Kanıt ve başarısız ilk denemelerin doğru yorumu TEST_RESULTS.md / test-evidence/prompts-presence-2026-10-23/outcome-summary.json içinde.

## Mesaj kutusunda gönderilen taslak kalıyor — DÜZELTİLDİ (yerel, 2026-10-23)

Gönderilen metin trim edilirken, temizlik koşulu özgün draft.question ile kırpılmış metni karşılaştırıyordu. Kullanıcının testinde sondaki satır sonu yüzünden koşul sağlanmadı. Temizlik özgün gönderim taslağına bağlandı; yanıt sırasında yazılmış farklı taslak korunur. Geçmiş teslim edilmiş veri değiştirilmedi. Araştırmacı başlığında artık “Gönderilmemiş” çıkarımı yapılmaz. 11 hedefli ve 131 genel Chrome, 11 süreç JS geçti; test-evidence/chat-prompts-2026-10-23/. Yeni sohbet/etiketleme promptları yalnız taslaktır; kullanıcı değerlendirmesi başlatılmadı.

## İlk gerçek OpenRouter testi — 2026-10-23

OPEN-01 artık anahtar/sağlayıcı bilgisi eksikliği değildir: özel yerel OpenRouter/GPT-5 Nano ayarıyla sentetik sohbet, profil ve tek soru puanlama smoke testi tamamlandı. Gerçek rubrik kalitesi, tam altı sorulu UI akışı ve mesaj etiketleme kabulü açık kalır. İlk testteki yerel CA eksikliği düzeltildi; JSON alan/tür sapmaları için opt-in katı şema, kodlanmış JSON'un öğrenci yorumuna geçmesini önleyen kontrol eklendi. 45 sn puanlama timeout'u local.php'de 90 sn yapıldı. Bir erken örnekte model ölçülmeyen profile sayı uydurdu; ham veri değişmedi, örnek kalite kabulü sayılmadı. Şema anlamsal doğruluk garantisi değildir; insan kararı zorunludur. Ayrıntılar TEST_RESULTS.md ve test-evidence/openrouter-2026-10-23/ içinde. Değişiklikler henüz commit/push edilmedi.

## 2026-10-23 güncel durum

- **AUDIT-01 · DÜZELTİLDİ (yerel):** v1 bütünlük adımı yeni teslim kontrolü yerine geçmiş teslim kontrolünü kullanır. Sonradan eklenen `final_severity` eksikse `uncollected_fields` içinde belirtilir; tahmini değer yazılmaz. Yeni teslim gereksinimleri korunur. `tests/legacy-submission.php`: 20 kontrol, PHP 8.3.36 ve 8.4.27 üzerinde geçti; eski v1 akışı/raporu, v2 uyumluluğu, değişmez veriler ve geçersiz yeni teslim reddi kapsanır.
- **AUDIT-02 · DÜZELTİLDİ:** `9098ff7` commit'inde puan–düzey eşleşmesi ve dolu gerekçe kontrolü uygulanmış. `tests/rubric.php` 21 kontrol ve `tests/review-v2.php` 68 kontrol bu incelemede yeniden geçti. AI sonucu ve insan düzenlemesi ortak sunucu doğrulamasını kullanır.
- OPEN-01…06 dış ortam/araştırma kabulleri açık. Yeni manuel batch başlatılmadı. Kanıt: `test-evidence/local-readiness-2026-10-23/`; güncel sayılar TEST_RESULTS.md içinde.

## 2026-10-21 tarihli teşhis — tarihsel kayıt

- **AUDIT-01 · P1 · AÇIK:** final_severity eklenmeden teslim edilmiş kayıt, Analysis::step(1) → Rules::final nedeniyle failed olur; sonraki analiz engellenir. Yalnız bellek içi sentetik DB'de yeniden üretildi. Eski ham yanıtları değiştirmeden geçmiş kayıt doğrulaması ayrılmalı.
- **AUDIT-02 · P2 · AÇIK:** Rules::scores puan-düzey eşleşmesini ve dolu puan açıklamasını doğrulamıyor. Sentetik rubrikte Yetersiz=0 iken score=4/level=Yetersiz/reason=boş kabul edildi. AI ve araştırmacı düzenleme aynı kontrolü kullanıyor.
- Ayrıntı ve öncelikler STATUS_REVIEW.md; çıktı test-evidence/review-2026-10-21-probes.txt. Mevcut genel testler geçti; bu iki negatif senaryo mevcut testlerin dışında kaldı. Bu incelemede düzeltme yapılmadı.

2026-10-20 · Yerel test sürümü 0.1.0. Kullanıcı kabulü yapılmadı; üretime hazır beyanı yok.

| ID | Sorun / etki | Durum / kanıt |
|---|---|---|
| FIX-01 | Aynı saniyedeki karar sürümlerini sıralarken rastgele ID eski kararı seçebilirdi | UTC mikrosaniye zamanı kullanıldı; karar/rapor testleri tekrar geçti |
| FIX-02 | Teslim tekrarında access(write) idempotent sonucu engelliyordu | Tekrar teslim aynı snapshot döndürür; domain kontrolleri geçti |
| FIX-03 | Autosave yanıtı vaka başlığını kaybediyordu | İstemci application alanlarını birleştirir; browser başlık regresyonu geçti |
| FIX-04 | AI tamamlanırken başka vaka açık olduğunda istemci eski yanıtı yeni ekrana uygulayabilirdi | Async callback çalışma nesnesine bağlandı; server app kapsamı zaten ayrı; ilgili geçişin manuel stres testi bekliyor |
| FIX-05 | Yenileme sonrası istemci sekme kimliği/sırası değişiyordu | Sekme ID ve sıra sessionStorage'da korunur; ikinci sekme salt okunur browser kontrolü geçti; tarayıcının Sekmeyi çoğalt davranışı ayrıca bekliyor |
| FIX-06 | Katılımcı özel notu metadata ile öğrenciye sızabilir, onboarding özel notu ezebilirdi | me allowlist ve metadata merge; 42 HTTP kontrolü içinde üç özel not kontrolü geçti |
| FIX-07 | Windows sandbox is_readable yanlış negatif verdi; dosya açma testi durdu | Uygulama kütüphanesi değiştirilmedi; aynı gerçek XLSX/DOCX/PDF testi normal izinli yerel süreçte geçti |
| FIX-08 | Python test dosyası http.py standart modülle çakıştı | http_checks.py olarak düzeltildi; HTTP kontrolleri geçti |
| FIX-09 | P2: Eksik profil/yanıt hatası hangi alanın boş olduğunu göstermiyordu (kullanıcı 2a6de014) | validation.js: kalıcı linkli özet, alan/sekme vurgusu, aria-invalid/yan hata, ilk eksik alana odak. 30 otomatik kontrol geçti; kullanıcı tekrar kontrolü bekliyor |
| FIX-10 | P2: Başlangıçtan sonra nihai/yansıtma boşken son teslim düğmesi aktifti (f220d500) | Düğme dört gereksinim + kayıt + AI durumunu canlı izler. Sunucuda eksik teslim reddi zaten vardı; 4 ayrı HTTP 422 ve sıfır submit snapshot kontrolü geçti. Mevcut kullanıcı vakası teslim edilmemiş; veri değiştirilmedi |
| FIX-11 | Düzeltme testinde: blur/autosave footer linkini yeniden oluşturup tıklamayı düşürebiliyordu | Değişmeyen içerikte DOM düğümleri korunur; alanlar arası link/focus regresyonu geçti. İlk başarısız test gizlenmedi |
| FIX-12 | P3 kullanıcı yerleşim dönütü: genel eksik kutusu AI/nihai/yansıtma üstünde tekrarlanıyordu | Kutu yalnız confirm bölümünde render edilir; Son teslimde tek liste. Altı bölümde yokluk ve bağlantı sonrası yokluk dahil 50 hedefli kontrol geçti; manuel tekrar bekliyor |
| FIX-13 | P2 regresyon: mobilde alan düzenlerken sekmeye pointerdown → blur/autosave üst çubuğu yeniden yerleştirip pointerup/click'i düşürebiliyordu | Kirli formda pointerdown erken blur'u önler; kayıt normal sekme click akışında sürer. İlk 43 kontrolden sonraki timeout başarılı sayılmadı; düzeltilmiş aynı senaryo dahil 50 kontrol geçti |
| OPEN-01 | Gerçek AI sağlayıcısı/model/anahtar verilmedi | P1 entegrasyon doğrulaması; compatible adapter örneği var, gerçek çağrı yapılmadı |
| OPEN-02 | Gerçek shared hosting, HTTPS/proxy, izinler, alt klasör ve hosting yedeği denenmedi | P1 dağıtım doğrulaması; INSTALL_SHARED_HOSTING.md adımları bekliyor |
| OPEN-03 | Gerçek rubrik ve kurum onam/saklama/çekilme politikaları verilmedi | Araştırma kabulünü engeller; TEST RUBRİĞİ gerçek rubrik değildir |
| OPEN-04 | Fiziksel mobil klavye, ekran okuyucu, farklı gerçek tarayıcılar ve Word uygulaması görsel açılışı denenmedi | P2; Chromium mobil emülasyon ve DOCX OOXML kontrolü bunların yerine sayılmadı |
| OPEN-05 | Tam akış sırasında gerçek internet/elektrik kesintisi ve tarayıcı crash denenmedi | P1 veri kurtarma kabul testi; sessionStorage sekme kapanınca kaybolabilir; sıfır kayıp garantisi yok |
| OPEN-06 | Büyük veri hacmi/dosya boyutu ve hosting memory limit stres testi yok | P2; küçük sentetik örnek ve uzun Türkçe PDF testleri var |

Tasarım sınırları: Tam yanıt aktarımı kullanılır; streaming/ilk parça ölçümü yok. sessionStorage kurtarma aynı sekmede yenileme/yeniden giriş içindir; logout temizler. localStorage yalnız kimliksiz sekme lease'i taşır; sunucuda revision her durumda zorunludur. Tarayıcının storage'ına erişebilen aynı işletim sistemi kullanıcısına karşı şifreli kasa değildir. Oturum sona erdiğinde aynı hesap doğrulanmadan kurtarma ekranı açılmaz.

Windows built-in PHP sunucusu tek worker olduğundan tek portta gecikmeli mock autosave'i sırada bekletebilir. Oturum kilidi testi iki worker ile yapıldı; shared hosting worker kapasitesi ayrıca kontrol edilmeli.

Yeni hata dönütü: Test ID, sürüm, sentetik hesap/vaka, adımlar, beklenen/gerçekleşen, cihaz/tarayıcı, hata kodu, tekrar oranı, etki. Gerçek parola/anahtar/katılımcı metni eklemeyin. Düzeltmede kök neden, dosyalar ve gerçek regresyon sonucu burada kaydedilir.

## FIX-14 —0.1.9: Çıkış sonrası gereksiz başka sekme uyarısı
Çıkış sessionStorage içindeki tabkimliğini temizlerken localStorage owneryetkisini20saniyebırakıyordu;hızlıyenigirişyeni tabkimliğiylesaltokunuraçılabiliyordu. releaseLease home/başarılılogouttatimerıdurdurupyalnızold.tab===tabiseleasekaydınıkaldırır. Gerçekikincisekmekorumasıdeğişmez. ÖncehataüretimiFAIL,düzeltmesonrası69ChromeGEÇTİ(test-evidence/logout-lease-*.txt). Manueltekrarbekliyor.

## 0.1.9 — Ek doğrulama, yeni hata yok — 2026-10-20

B14.2/B15 erişim/atama/sürüm ve ayrı global kapanış/yayın karşılıkları tests/access_publication_checks.py ile 80 HTTP kontrolünde geçti (SQLite/mock; test-evidence/access-publication-0.1.9.txt). Ürün kodu değişikliği gerekmedi. OPEN-01/02/03/04/05 ve hedef hosting hacmi doğrulaması açık kalır; gerçek AI/hosting/rubrik/cihaz/kesinti yerine sayılmaz. Yeni manuel batch istenmedi. Güncel bilgi/erişim listesi ve senaryo RELEASE_READINESS.md.

## 0.2.0 — Kullanıcı isteğiyle panel/yetki/yayın kuralları değişti

Eski global kapanış kuralı kullanıcı talebiyle vaka aç/kapat ve kişisel ek süreye dönüştü. Rutin form karmaşıklığı/gerekçe zorunluluğu, tekli atama, tanım kaldırma ve export ekranı sadeleştirildi. Ayrı araştırmacı sahiplik koruması eklendi; ortak vaka erişimi iptal edildi. Bu değişiklikler eski uygulama kurallarının test hatası olarak yorumlanmaz.

Test altyapısındaki async bekleme/ham JSON/CSV tırnak beklentisi ve Windows MariaDB TLS hataları düzeltildi; ilk koşular ayrı saklandı. Son gerçek sayılar119HTTP/42HTTP/69Chrome/24UI/69SQLite/69MariaDB+restore/35export. Gerçek Disco import ve OPEN-01…06 dış kabul sınırları devam eder. Yerel migration yedekli ve22tabloda eski içerik eşitliğiyle tamamlandı.

## 0.2.1 — Kullanıcı isteğiyle araştırmacı kullanımını kolaylaştırma

Matris yerine vaka seçerek öğrenci listesi, atanmış öğrenci diyaloğu, alanlarla rubrik oluşturma ve okunaklı/aranabilir işlem geçmişi eklendi. Ek süre işlevi yeniden119HTTPve50hedefliChrome ile doğrulandı; hata bulunmadı. Rubrik doğrulamasında düzey ad/açıklama/puan/tekrar/sonlu sayı kontrolleri sunucuya da eklendi. İlk yeni UI testinin process isim gölgelemesi test kodunda düzeltildi; ürün hatası değildir. Önceki dış kabul açıkları sürer.

## 0.2.1 kapsam denetimi — 2026-10-20

Güncel kodda12yereltestgrubunun tamamı tekrar geçti;547bildirilen örtüşen kontrol ve8restoretablosayısıeşit. Yeni ürün hatası görülmedi. Bağlam/manuel rehberlerde kalan eski matris/global kapanış/JSONrubrik/rutin gerekçe anlatımları güncellendi; tarihsel test sonuçları korundu. OPEN-01…06vegerçekDiscoimport dış kabul sınırları açık kaldı. Kanıtlar TEST_RESULTS.md ve test-evidence/audit-0.2.1-summary.json.

## 0.2.3 — Öğrenci raporu anlaşılabilirliği
Kullanıcının teknik ve karmaşık rapor bildirimi yanıt/puan/feedback/nihai toplam görünümüyle giderildi. Öğrenciye iç inceleme alanları artık allowlist biçiminde sunulur. Eski gövdeler korunur; yeni feedback için yeni sürüm gerekir. Sentetik otomatik doğrulama TEST_RESULTS.md; gerçek sağlayıcı geri bildirim kalitesi ve Word görsel kabulü hâlâ açık.

## 0.2.4 — Süreç JSON okunabilirliği ve Disco etiketleri
Kullanıcının öğrenci-vaka dosyasında yoğun JSON bildirimi alan/liste/karşılaştırma gösterimiyle giderildi. Bağımsız AI etiketleme ve Disco mesaj etiketleri eklendi; araştırmacı boş seçimi dahil güncel seçim önceliği doğrulandı. Açık süre tablosunun mobil taşması düzeltildi. 97 Chrome ve hedefli sunucu/etiket/export kanıtı TEST_RESULTS.md içinde; gerçek AI kalitesi/Disco import dış kabulü açık.

## 0.2.5 — Disco ziyaret süreleri ve tamamlanma sınırı
0.2.4 incelemesinde saptanan ziyaret sürelerinin Disco'ya aktarılmaması, yeni click-to-click ziyaret profiliyle giderildi. Tüm yedi sekme, tekrar ziyaret, son teslimde bitiş ve sonrasını dışlama otomatik doğrulandı. İptal edilen beforeunload'un kapanış sayılması ve çıkış sırasında kuyruğun yeniden oluşması engellendi. Açık uçlar bilinmiyor kalır; gerçek cihaz/crash ve gerçek Disco import kabulü açık.

## 0.2.9 — Eski AI sorularının amaç satırları görünmüyor

Düzeltildi: message_send olayları eklenmeden önce oluşmuş gerçek öğrenci mesajları yalnız yanıt ve görüntüleme olarak görünüyordu; etiket mevcut olsa da soru satırı oluşmuyordu. Gerçek mesaj kaydı artık ayrı amaç noktası olarak gösterilir, sunucu zamanı ve bilinmeyen bekleme süresi açıkça belirtilir. Yerel kayıt salt okunur doğrulandı; sentetik 77 navigation/114 Chrome ve ilgili export testleri geçti. Kullanıcının yeni öğrenci denemesi sonucu henüz bildirilmedi.

## 0.2.11 — Dönüşte bilgisayar saatinin geriye gitmesi

Genişletilmiş süre testinde yakalandı ve düzeltildi: başlangıç/bitiş sırası ters olan dışarıdaki aralık, AI beklemesini bölerken üst üste binen parçalar üretebiliyordu. Aynı kayıt akışında geri giden saat tespit edilince etkilenen bölüm ve AI bekleme süreleri invalid_clock/null olarak işaretlenir; bitiş/başlangıç ham zamanları korunur, sayısal süre üretilmez ve bu aralıklar süre CSV’sine alınmaz. Normal saatli tekrar dönüşler ve aynı zaman damgalı geçişler geçerlidir. 49 presence ve 82 navigation kontrolü dahil yeniden doğrulandı.

## DÜZELTİLDİ (0.2.12) — Native onay penceresi sahte uygulama dışı aralığı üretiyor

2026-10-21 yeni kullanıcı bildirimi incelemesi (yalnız teşhis): Son teslimden önceki dışarı satırı 13:20:39.882–39.883, yalnız1ms; visible=true ve focused=false→true. Gerçek Chrome’da native confirm kabulü ayrı sentetik DB’de aynı hatayı 2ms olarak yeniden üretti (test-evidence/native-confirm-investigation.txt). Başlangıç kesinleştirmede de 0ms aynı iz var. Bu, gerçek uygulamadan ayrılış değil; native onay penceresinin odak etkisi. HATA AÇIK, uygulama kodu bu incelemede değiştirilmedi. Ayrıca Vakalarım’a dönüş13:14:08.481 ile yeniden vaka açılışı13:19:27.989 arasındaki5dk19.508sn mevcut kapsam gereği izlenmiyor; açık vaka içindeki dışarı toplamına dahil değil. Bu boşluk için nerede zaman geçirildiği çıkarılamaz. Kullanıcıya iki durum ayrı açıklanacak; eski geçti kabulü önceki gerçek pencere geçişi senaryosuyla sınırlıdır. Çalışma DB salt okunur incelendi.


## 0.2.12 — Onay düzeltmesinin doğrulaması

**2026-10-21 · 0.2.12 yerel:** Native confirm kaynaklı sahte uygulamadan ayrılma düzeltildi: öğrenci başlangıç/teslim/kurtarma/yeniden deneme/kirli çıkış onayları await edilen sayfa içi dialog kullanır. Onayla, Vazgeç ve Escape destekli; gerçek başka pencereye geçiş onay açıkken de kaydedilir. Eski ham kayıtlar silinmedi/yeniden yazılmadı; geçmiş native confirm artefaktları olduğu gibi korunur. Vakalarım’a dönüş ile aynı vakayı yeniden açma arasında CASE_AWAY / Vaka dışında türetilir; APP_OUTSIDE ile karıştırılmaz, bölüm/app içi süreye eklenmez. Geri açılış gözlenmeden süre uydurulmaz; paralel/salt okunur sekmede sahte boşluk oluşturulmaz. UI/API/Excel-JSON/Disco aynı kaynağı kullanır. Hiç ziyaret edilmeyen bölümlerde Ziyaret yok gösterilir. 55 presence, 82 navigation, 15 gerçek Chrome onay/boşluk, 127 genel Chrome, 50 doğrulama, 81 SQLite ve39 export kontrolü geçti. Çalışma DB değişmedi; commit/push yok. Yeni tek manuel senaryo kullanıcıya verilecek; 0.2.12 kullanıcı kabulü bekleniyor.

## 0.2.12 — Eski açık uç yeni Vaka dışında aralığını gizliyordu (düzeltildi)

2026-10-21 0.2.12 kullanıcı yeni ekran incelemesi: TEST-OGR-01 / Sınıfta dijital araç kullanımı, son onay 13:38:06.747–13:38:17.978 (11.231sn) kesintisiz submit_confirmed ile kapandı; sahte native ayrılış yok. Önceki 13.384 ve8.273sn dışarı aralıklarında visible=false gözlemi var. Vakalarım13:36:49.989→yeniden açılış13:37:24.941 arası34.952sn ilk hesapta görünmüyordu: eski günden kalma end=null trace, sonsuza kadar açık paralel pencere varsayılıp boşluğu engelliyordu. Düzeltildi: sadece bitişi bilinen ve gerçekten çakışan aralık bu boşluğu engeller. Eski açık uçlar bilinmiyor kalır; kapanış uydurulmaz. Salt okunur gerçek DB yeniden hesabı Vaka dışında34.952sn doğruladı. 57 presence,82navigation,16 gerçek Chrome onay/boşluk testi geçti; Chrome fixture eski açık uç kaydını da içerir, CSV/UI doğrulandı. Çalışma DB yazılmadı; yeni manuel tekrar gerekmiyor. Kullanıcının düzeltme sonrası ekran teyidi henüz yok. Commit/push yok.


## 23 Ekim 2026 — öğrenci / AI etiket grupları ve prompt v3

Etiket tanımları artık body.target_role=user veya assistant ile açıkça grupludur. Oluşturma/düzenleme ekranı, manuel seçim API/UI, AI JSON şeması ve sonuç doğrulaması aynı rol kuralını uygular. Eski grubu olmayan tanımlar tahminle atanmaz; yeni AI etiketlemesinden önce araştırmacı düzenlemelidir. Tarihsel kararlar korunur. Güncel tanımların tamamı her etiketleme isteğine eklenir; grup da önbellek anahtarının parçasıdır.

Sohbet chat-v3 ve etiketleme message-labels-v3 promptlarının tam metni ve birlikte odak testi adımları AI_PROMPTS_REVIEW_TR.md içinde. İlk prompt başarısızlıkları tarihsel bulgudur; son sürüm sonuçları test-evidence/label-groups-2026-10-23 altında ayrıca tutulur. Genel model doğruluğu iddiası yoktur. Kullanıcının mevcut çalışmasında değerlendirme veya yeniden etiketleme başlatılmadı.


## AI-LABEL-02 — 16 tanımla kalan anlam eşleşmesi sorunları (açık)

message-labels-v4 / GPT-5 Nano: 32 sentetik mesajın 5’inde beklenen etiket kümesinden sapma. Eleştiri isteğinin açıklama sayılması, fazladan eleştiri/karar etiketi, kapsam dışı talebe etiket verilmesi, AI sorusuna fazladan kapsam/metin düzenleme etiketi ve devam davetinin atlanması görüldü. Rol doğrulaması geçti; anlam doğruluğu kesinleşmiş değildir. Tam örnekler test-evidence/label-catalog-2026-10-23/RESULTS_TR.md içinde. Beklenen etiketler veya tanımlar model sonucuna göre değiştirilmedi.


## AI-LABEL-03 — öğrenci öncelikli v6: 3/28 uyuşmazlık (24 Ekim v11 kontrol kümesinde giderildi)

Kapsam dışı iki yatırım talebine etiket verilmesi ve “Arabuluculuk kavramını bir cümleyle tanımla” talebinin Açıklama istedi sayılması sürdü. Eski küme15/16; yeni küme10/12. Bu örneklerin beklenen etiketleri değiştirilmedi. Ayrıntı test-evidence/student-labels-2026-10-23/RESULTS_TR.md içinde. Öğrenci grubu, çoklu/boş karar dönüşümü ve insan düzeltmeleri yerel kontrollerde doğrulandı; anlam hataları kapanmış sayılmaz.


## 24 Ekim 2026 — AI-LABEL-03 doğrulaması

Önceki üç örnek v11 koşusunda doğru.52/52 öğrenci mesajı ve4/4 değişen tanım testi geçti. Rapor: test-evidence/student-labels-2026-10-24/RESULTS_TR.md. Önceki yanlış sonuçlar silinmedi; tüm olası gelecek mesajlarda hatasızlık iddiası yok. İsteğe bağlı AI yanıtı etiketlemesinin eski anlam hataları bu öğrenci testleriyle kapanmış sayılmaz.

## AI-SCORE-04 — GPT-5 Nano puanlama tutarsızlığı açık

24 Ekim canlı sentetik uçtan uca testinde, doğru vaka bağlantısı içeren bir yanıt için model hem gerçek alıntıyı seçti hem de bağlantının bulunmadığını ileri sürerek sıfır verdi. İlk koşuda gerekçe, güncel review-v2.1 promptuyla tekrarında temel problem yanıtı etkilendi. Katı JSON, gerçek alıntı ve düzey-puan eşleşmesi bu anlamsal hatayı tek başına engellemiyor. Açık gerekçe / gerekçesiz yanıt için sabit iki karşılaştırma geçti; bu dar sonuç genel puanlama doğrulaması değildir.

Düz metin yerine kodlanmış nesne/dizi veya alan parçası döndürülmesi için geri bildirim şeması ve sunucu doğrulaması güçlendirildi. Kaynakta olmayan kanıt alıntısı için katı soru şemasında yalnız mevcut öğrenci yanıtından çıkarılan cümleler seçilebilir. Puanlamada kaynak/kanıt/gerekçe, düzey ve puandan önce üretilir. Araştırmacı onay/düzeltme kapısı sürer. Sentetik rapor testindeki düzeltme gerçek öğrenci veya uzman onayı sayılmaz. Canlı sonuçlar test-evidence/live-workflow-2026-10-24 altında; önceki hatalar silinmedi.
