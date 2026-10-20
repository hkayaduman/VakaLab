# Hatalar, düzeltmeler ve açık doğrulamalar

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
