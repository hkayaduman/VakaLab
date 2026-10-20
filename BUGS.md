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
