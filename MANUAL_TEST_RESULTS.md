# Birlikte manuel test kaydı

Güncel devam — 0.2.1 kapsam denetimi: Yerel12otomatikgrup tamamlandı; bu yeni manuel kabul değildir. Kullanıcı Batch01–13veB14sürüm kanıtları tarihsel olarak korunur. Yeni manuel batch başlatılmadı; geniş matris/JSONrubrik/global kapanış yönergeleri güncel değildir. Güncel ekran haritası MANUAL_TESTS.md; kalan dış kabul RELEASE_READINESS.md.

Başlangıç: 2026-10-20. Ortam: kullanıcının yeniden başlatma sonrası yerel VakaLab kurulumu; sürüm 0.1.0, sentetik/mock. İlk kanıt: Windows üzerinde Chrome; paylaşılan ekran görüntüsü 1920×1080 (tarayıcı viewport'u ayrıca ölçülmedi).

Durumlar: BEKLİYOR / GEÇTİ / KALDI / KISMEN DOĞRULANDI / ÇALIŞTIRILAMADI. Yalnız ekran görüntüsünün veya çıktının kanıtladığı davranış doğrulanır. Eski otomatik sonuçlar manuel kabul yerine yazılmaz. Hatalar BUGS.md'ye ve düzeltme/regresyon kayıtlarına bağlanır.

| Adım | Kapsam | Beklenen | Durum | Kanıt / sonuç |
|---|---|---|---|---|
| M01 | Öğrenci girişi ve ana sayfa; hazırlık | TEST-OGR-01 ile Vakalarım açılır; hesap ve sentetik ortam bilgisi görünür | GEÇTİ | Kullanıcının 2026-10-20 ekran görüntüsü: TEST-OGR-01 · Öğrenci, sentetik/mock banner, Vakalarım ve 4 atama sayacı görünür. Kimlik bilgisi gönderimi ayrı gözlenmedi; doğrulanan, oturum açılmış ana sayfadır. |
| M02 | T04 başlangıcı: yeni vaka ilk ekranı ve kilit göstergeleri | 2. vaka açılır; vaka metni ve altı bölüm başlığı görünür; AI/nihai/yansıtma Kilitli olarak işaretli | GEÇTİ | Kullanıcı ekran görüntüsü de8c1b45: doğru vaka başlığı, sürüm 1, başlangıç aşaması, vaka metni/yönerge ve altı tab görünür. AI/nihai/yansıtma Kilitli. Kilide tıklama davranışı henüz gözlenmedi. |
| M03 | T04: başlangıç öncesinde AI bölümünün kilit davranışı | AI tabına tıklayınca kilit açıklaması gösterilir; mesaj kutusu/Gönder açılmaz | GEÇTİ | Kullanıcı ekran görüntüsü 6c6df1c8: AI tabı seçili; Bu bölüm henüz açık değil açıklaması ve başlangıç kesinleştirme gereği görünür; mesaj alanı/gönder yok. Teslim düğmesi de devre dışı görünümünde. Bu kanıt doğrudan API engeli veya düğmenin işlevsel devre dışılığını ayrıca doğrulamaz. |
| M04 | T05 başlangıcı: boş başlangıç profili | Vaka profili açıldığında dört 0–10 alanının tamamı Seçilmedi durumunda; önceden puan atanmış değil | GEÇTİ | Kullanıcının 20d89333 ekran görüntüsünde dört alan da Seçilmedi. |
| M05 | T05: eksik profil doğrulaması | İlk üç profil alanı ve başlangıç metinleri dolu, ciddiyet boşken kesinleştirme engellenir; AI kilitli kalır | GEÇTİ | 2a6de014 ve 78a5dc2c: eksik profil hatası ve AI kilidi doğrulandı. Alanın yerinin belirgin olmaması ayrıca FIX-09 olarak düzeltildi. |
| M06 | T06: profil/metin kaydı ve refresh | Dört profil ve üç başlangıç metni sunucu kaydı sonrası yenilemede korunur | KISMEN DOĞRULANDI | Profil 5/8/6/7 ve başlangıç metni sonraki ekranlarda korunuyor. Yenileme adımının gerçekleştiği ayrıca belirtilmedi; refresh bütünü geçti sayılmadı. |
| M07 | T05: kesinleştirme onayını iptal | İptal profil/başlangıcı kilitlemez; düzenleme sürer, AI kilitli kalır | KISMEN DOĞRULANDI | b4049450: onay diyaloğu görünür. İptal sonrası düzenlenebilirlik ayrıca gösterilmedi. |
| M08 | T05: başlangıcı kesinleştirme | Onay sonrası AI açılır; profil ve başlangıç alanları değiştirilemez | GEÇTİ | c5f9d526, 91231d1e, f220d500: başlangıç/profil Salt okunur, AI sohbeti açık. Arayüz kilidi doğrulandı; bu screenshotlar doğrudan API testi değildir. |

Aktif batch M09–M11 (0.1.1 düzeltmesi sonrası teslim koşulları). Kullanıcı daha hızlı ilerlemek için birden fazla adım istedi; sonraki yönergeler kısa batch'ler halinde verilecek. TEST-OGR-01 / ikinci vaka (Sınıfta dijital araç kullanımı), sürüm 1. İlk vaka taslağına dokunulmadı. Mevcut veriler sıfırlanmayacak. T04 bütün gezinme/mobil testi henüz tamamlanmadı. Yayın/global kapanış testi diğer veri toplama testlerinden sonra ayrı senaryoda yapılacak.

## Kullanıcı bulguları ve düzeltme kontrolü — 0.1.1

- FIX-09: Eksik alan mesajı vardı, hangi alan/bölüm eksik olduğu vurgulanmıyordu. Kırmızı alan/yan hata, kalıcı linkli özet, sekmede eksik sayısı ve ilk eksik alana odak eklendi. Otomatik regresyon geçti; kullanıcı yeni arayüzü henüz doğrulamadı.
- FIX-10: Başlangıç kesinleşince son teslim düğmesi nihai/yansıtma boşken aktif görünüyordu. Sunucu zaten boş nihai/yansıtmayı reddediyordu. Kullanıcının mevcut ikinci vakası yalnız okuma ile kontrol edildi: working, submitted_at boş; dört nihai gereksinim boş. Veri değiştirilmedi.
- Güncel teslim koşulu: nihai karar + gerekçe + 0–10 güven + yansıtma tamam, son taslak sunucuya kaydedilmiş, AI üretimi yok. Eksik/boşluk metni veya kayıt sürerken düğme kapalı. Footer eksik alana link verir. AI mesajı gönderme zorunluluğu eklenmedi.

| Adım | Kapsam | Beklenen | Durum | Kanıt / sonuç |
|---|---|---|---|---|
| M09 | 0.1.1: yenileme sonrası mevcut ikinci vaka | Başlangıç/profil kilitleri korunur; nihai alanlar boşken teslim kapalı, eksik listesi görünür | KISMEN DOĞRULANDI | d35a305b: dört eksik bağlantı ve kapalı düğme geçti; refresh/diğer kilitler aynı screenshotta gösterilmedi |
| M10 | Nihai karar/gerekçe/güven tamam, yansıtma boş | Son teslim kapalı kalır, yansıtma bağlantısı ilgili alana götürür | BEKLİYOR | Kullanıcı kontrolü |
| M11 | Yansıtma + sunucu kaydı; ardından zorunlu alanı temizleme/geri doldurma | Tamam ve kayıtta düğme açılır; alan silinince anında kapanır; yeniden kayıtta açılır | BEKLİYOR | Henüz teslim yapılmayacak; sonraki batch AI etkileşimi |

Eksik profil vurgusunun manuel tekrar testi için başlangıcı kilitli ikinci vaka açılmayacak/sıfırlanmayacak; gerekirse üçüncü açılmamış vaka kullanılacak. Yeni doğrulamanın 30 otomatik kontrolü ayrı test DB'de geçti; bu manuel tabloda otomatik pass sayılmadı.

## 0.1.2 — Kullanıcı yerleşim talebi

Kullanıcı d35a305b ekran görüntüsünde son teslim kartının her bölüm altında görünmesini istemedi; gezinmenin sonuna ayrı tab istedi. Son teslim 7. sekmeye taşındı. Aynı screenshot dört eksik alan bağlantısını ve kapalı düğmeyi doğruluyor (M09 görünür teslim kapısı GEÇTİ; refresh/diğer durumlar ayrıca kanıtlanmadı). M10–M11 henüz tamamlanmadı; yeni yerleşimde Son teslim sekmesine gidilerek sürdürülecek.

Yeni aktif kontrol M12: Ctrl+F5 → mevcut ikinci vaka → ilk altı bölümde son teslim kartı yok; en sonda 7. Son teslim var; o sekmede eksik alanlar ve kapalı düğme var. Kullanıcı screenshotu BEKLİYOR. Otomatik 40+18 sonuç manuel kabul yerine yazılmadı.

## 0.1.3 — Kullanıcı kutu kapsamı talebi

b250d8ae, 8d280cb2 ve c414dd0b screenshotları genel eksik kutusunun AI/nihai/yansıtma ekranlarında tekrarlandığını gösterdi. Kullanıcı yalnız Son teslim'de olmasını istedi. Kutu diğer bölümlerin DOM'undan kaldırıldı; Son teslim'de tek liste ve bağlantılar korundu. Otomatik 50 kontrol geçti; kullanıcı yeni düzeni henüz doğrulamadı.

Aktif kontrol M13: Ctrl+F5 → aynı ikinci vaka → AI/Nihai/Yansıtma'da genel kutu yok; Son teslim'de eksik listesi var; bir eksik bağlantısıyla alana gidildiğinde genel kutu yok. Kullanıcı kanıtı BEKLİYOR. Veri sıfırlanmadı ve teslim yapılmadı. Diğer M10–M11 kontrolleri henüz tamamlanmadı.

## Yeni manuel test turu R02 — 2026-10-20 — 0.1.3

Kullanıcı test yönergelerinin baştan, tek tek değil kısa batch'ler halinde verilmesini istedi. Eski kanıtlar/sonuçlar korunuyor; R02 için otomatik veya önceki manuel sonuçlar yeni pass sayılmaz.

Mevcut yerel veritabanı yalnız okunarak kontrol edildi: TEST-OGR-01 için üçüncü vaka (test-case-3, Akranlar arası anlaşmazlık) ve dördüncü vaka açılmamış. Başlangıcı kilitli ikinci vaka değiştirilmeden korunacak. Yeni tur üçüncü vakada başlıyor; seed/reset/hesap değişikliği yapılmadı.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B01.1 | Ctrl+F5, çıkış ve TEST-OGR-01 ile giriş | Vakalarım, doğru hesap, mock/sentetik banner | GEÇTİ (kullanıcı bildirimi) |
| R02-B01.2 | Vaka kartları | 4 atama sayacı; üçüncü vaka Açmadı/Başla | GEÇTİ (kullanıcı bildirimi) |
| R02-B01.3 | Üçüncü vakayı açma | Doğru vaka/metin/sürüm ve 7 bölüm | GEÇTİ (kullanıcı bildirimi) |
| R02-B01.4 | AI/Nihai/Yansıtma/Son teslim başlangıç kilidi | Kilit açıklaması; mesaj/nihai/yansıtma/teslim formu açılmaz | GEÇTİ (kullanıcı bildirimi) |
| R02-B01.5 | İlk profil | Dört 0–10 alanı Seçilmedi | GEÇTİ (kullanıcı bildirimi) |
| R02-B01.6 | İlk yerleşim | Çalışma bölümlerinde genel eksik kutusu ve ortak teslim kartı yok | GEÇTİ (kullanıcı bildirimi) |

Aktif batch yalnız R02-B01; değer girilmeyecek/kesinleştirme/teslim yapılmayacak. Kullanıcıdan vaka ilk ekranı ve boş profil screenshotları, kilitler için kısa sonuç özeti istenir. Sonraki batch kanıttan sonra verilecek. Daha sonraki batch'lerde başlangıç doğrulama/kayıt/kilit, AI, nihai/teslim, araştırmacı yönetimi/analiz/rapor/çıktılar ve ilgili negatif testler sürdürülecek.

## R02-B01 sonucu ve R02-B02 başlangıcı

Kullanıcı R02-B01 için “hepsi tamam” bildirdi. Altı kontrol kullanıcı beyanıyla GEÇTİ kaydedildi; yeni screenshot/çıktı gelmediğinden bağımsız görsel veya API doğrulaması iddia edilmedi. Önceki otomatik kanıtlarla bu yeni manuel tur birbirine karıştırılmadı.

Aktif R02-B02, TEST-OGR-01 / üçüncü vaka Akranlar arası anlaşmazlık:

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B02.1 | Profilde 5/8/6, ciddiyet boş | İlk üç değer seçilir; son alan boş bırakılır | GEÇTİ (kullanıcı bildirimi) |
| R02-B02.2 | Üç başlangıç metni sonrası eksik profille kesinleştirme | Ciddiyet alanına otomatik geçiş, kırmızı yan hata; AI açılmaz, genel kutu diğer bölümlerde yok | GEÇTİ (kullanıcı bildirimi) |
| R02-B02.3 | Ciddiyet 7, sunucu kaydı sonrası refresh | Profil 5/8/6/7 ve üç başlangıç metni korunur | GEÇTİ (kullanıcı bildirimi) |
| R02-B02.4 | Kesinleştirme onayını iptal | Başlangıç düzenlenebilir, AI kilitli | GEÇTİ (kullanıcı bildirimi) |
| R02-B02.5 | Kesinleştirme onayını kabul | AI açılır, profil/başlangıç salt okunur olur | GEÇTİ (kullanıcı bildirimi) |
| R02-B02.6 | Son teslim kontrolü | Nihai/yansıtma boşken yalnız Son teslim'de eksikler görünür; teslim kapalı | GEÇTİ (kullanıcı bildirimi) |

Bu batch sonunda AI mesajı veya son teslim henüz yapılmayacak. Sonuç kullanıcıdan bekleniyor; hiçbir R02-B02 adımı şimdiden pass sayılmadı.

## R02-B02 sonucu ve R02-B03 başlangıcı

Kullanıcı Batch 2 için “hepsi tamam” bildirdi. Altı kontrol kullanıcı beyanıyla GEÇTİ; yeni ekran görüntüsü/API çıktısı yok. Bağımsız gözlem iddiası eklenmedi.

Aktif R02-B03, aynı TEST-OGR-01 / üçüncü vaka:

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B03.1 | İlk sentetik AI sorusunu gönderme | Bir öğrenci mesajı ve açıkça mock/sentetik işaretli bir AI yanıtı | GEÇTİ (kullanıcı bildirimi) |
| R02-B03.2 | Nihai karar/gerekçe/güven taslağı | Başlangıç otomatik yanında görünmez; alanlar düzenlenebilir ve sunucuya kaydolur | GEÇTİ (kullanıcı bildirimi) |
| R02-B03.3 | İkinci AI mesajı ve tekrar nihaiye dönüş | Sohbet sürer; nihai metin/puan korunur; yeni mesaj ilkini çoğaltmaz | GEÇTİ (kullanıcı bildirimi) |
| R02-B03.4 | Yenileme ve devam | Aynı sohbetin iki soru/iki yanıtı ve nihai taslak korunur | GEÇTİ (kullanıcı bildirimi) |
| R02-B03.5 | Yansıtma boşken Son teslim | Yalnız Yansıtma eksik, teslim kapalı; link doğru bölüme götürür | GEÇTİ (kullanıcı bildirimi) |
| R02-B03.6 | Yansıtmayı doldurma/kaydetme | Genel eksik kutusu yansıtmada yok; Son teslim'de eksikler kalkar ve kayıt sonrası düğme açılır | GEÇTİ (kullanıcı bildirimi) |

Son teslim henüz yapılmayacak. Bu batch normal hızlı mock ile işlev/kayıt testidir; gecikmeli AI sırasında eşzamanlı autosave/tek istek kontrolünü geçti saymaz. Windows tek worker yerel sunucu kısıtı nedeniyle o kontrol ayrı koşulda ele alınacak.

## R02-B03 sonucu ve R02-B04 başlangıcı

Kullanıcı Batch 3 için “hepsi tamam” bildirdi. Altı kontrol kullanıcı beyanıyla GEÇTİ. d671177d screenshot ilk kullanıcı sorusunu ve #2 SENTETİK MOCK yanıtını doğrudan doğruluyor; ikinci soru/yanıt, refresh, nihai ve yansıtma sonuçları beyana dayanıyor, screenshotta görülmüş sayılmadı. Normal hızlı mock akışı eşzamanlılık testi yerine sayılmaz.

Aktif R02-B04, aynı TEST-OGR-01 / üçüncü vaka:

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B04.1 | Nihai gerekçeyi geçici temizle/geri yaz | Son teslim temizlenince kapanır, tek gerekçe eksik; geri yazılıp kayıtta yeniden açılır | GEÇTİ (kullanıcı bildirimi) |
| R02-B04.2 | Aynı vakayı ikinci normal sekmede aç | İkinci sekme uyarısı/salt okunur alanlar, teslim kapalı; sonra ikinci sekme kapatılır | GEÇTİ (kullanıcı bildirimi) |
| R02-B04.3 | Son teslim onayını iptal | Teslim olmaz, alanlar düzenlenebilir ve süreç devam eder | GEÇTİ (kullanıcı bildirimi) |
| R02-B04.4 | Tek son teslim onayını kabul | Teslim edildi durumu/zamanı; yeni düzenleme/mesaj kapanır | GEÇTİ (kullanıcı bildirimi) |
| R02-B04.5 | Teslim sonrası bölümleri incele/refresh | Metin/sohbet korunur; profil/başlangıç/nihai/yansıtma salt okunur, yeni mesaj yok | GEÇTİ (kullanıcı bildirimi) |
| R02-B04.6 | Vakalarım ve tekrar görüntüleme | Kart Teslim edildi/Görüntüle; açılış kilitleri korur; öğrenciye puan/rapor henüz yok | GEÇTİ (kullanıcı bildirimi) |

Batch4 gerçek son teslim testini içerir; yalnız mevcut sentetik üçüncü vakada uygulanacak. Başlangıç kilidi açılmayacak/veri sıfırlanmayacak. Henüz hiçbir B04 kontrolü geçti sayılmadı. Sonraki batch araştırmacı incelemesi olacak; global veri kapanışı ayrı senaryoda sona bırakılacak.

## R02-B04 sonucu ve R02-B05 başlangıcı

Kullanıcı Batch4 için hepsi tamam bildirdi. Altı kontrol kullanıcı beyanıyla GEÇTİ. f3512ebe screenshot TEST-OGR-01 üçüncü vaka için Teslim edildi, Son teslim ve diğer görünen tablarda Salt okunur, devre dışı teslim düğmesi ve 20.10.2026 19:29:55 teslim zamanını doğruluyor. İkinci sekme/iptal/reopen/diğer içerik kontrolleri screenshotta gözlenmedi, beyana dayanıyor.

Aktif R02-B05 araştırmacı ham süreç incelemesi:

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B05.1 | TEST-ARASTIRMACI girişi | Araştırmacı paneli ve dinamik matris | GEÇTİ (kullanıcı bildirimi) |
| R02-B05.2 | TEST-OGR-01 × üçüncü vaka hücresi | Teslim edildi; doğru öğrenci/vaka ve sürüm dosyası açılır | GEÇTİ (kullanıcı bildirimi) |
| R02-B05.3 | Ham profil/başlangıç/nihai/yansıtma | Öğrencinin test metinleri, profil 5/8/6/7, nihai güven10, snapshotlar | GEÇTİ (kullanıcı bildirimi) |
| R02-B05.4 | AI konuşması | İki öğrenci ve iki mock AI mesajı, doğru sıra ve içerik | GEÇTİ (kullanıcı bildirimi) |
| R02-B05.5 | Taslak/olay/süreler | Taslak sürümleri ve olaylar mevcut; süre kalitesi/belirsizlik açık, okudu/düşündü iddiası yok | GEÇTİ (kullanıcı bildirimi) |
| R02-B05.6 | Araştırmacıya özel iç not ekleme | Gerekçeli not iç geçmişte korunur; refresh sonrası kalır | GEÇTİ (kullanıcı bildirimi) |

AI analizi/yayın veya global kapanış bu batch'te yapılmayacak. Gerçek zaman/muhakeme çıkarımı yapılmış gibi yorumlama yok. Sonuçlar bekleniyor.

## R02-B05 sonucu — 2026-10-20
Kullanıcı “Tüm maddeler TAMAM” bildirdi; altı kontrol kullanıcı beyanıyla GEÇTİ. 9d41670c/db9c6c75/0a81fb32 görüntüleri araştırmacı paneli, matris ve doğru öğrenci-vaka dosyasını; 008a9d91 profil 5/8/6/7, nihai güven10 ve yanıt/snapshotları; a2e1d4a6 dört sohbet mesajını; 6362ea8d/ee401779/82a2d503/8b8c6a8b taslak, olay, süre ve AI kulvarını destekliyor. İç notun kaydı/refresh kalıcılığı kullanıcı beyanıdır; screenshotta ayrıca doğrulanmadı. ISO tarih okunabilirliği düzeltme talebi 0.1.4 ile ele alındı.

## Aktif R02-B06 — Etiket sürümleri ve ilk iki analiz adımı
Aynı TEST-OGR-01 / üçüncü vaka; aşağıdakiler henüz BEKLİYOR:

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B06.1 | Ctrl+F5 ve tarih gösterimi | Başlangıç 20.10.2026 19:19:58, teslim 19:29:55; UTC+03:00; matris de okunaklı | GEÇTİ (kullanıcı bildirimi) |
| R02-B06.2 | İlk öğrenci mesajına iki etiket + gerekçe | Yeni etiket kararı kaydolur ve yenileme sonrası geçmişte kalır | GEÇTİ (kullanıcı bildirimi) |
| R02-B06.3 | Aynı mesaja tek etiketle ikinci karar | İlk sürüm silinmez; iki gerekçeli karar geçmişte korunur | GEÇTİ (kullanıcı bildirimi) |
| R02-B06.4 | Test rubriğiyle yeni analiz sürümü | Aynı öğrenci-vakaya ait 0/8 analiz oluşur | GEÇTİ (kullanıcı bildirimi) |
| R02-B06.5 | 1. Veri bütünlüğü adımını çalıştır | completed; çıktı ve girdi snapshotı açılır, varsa uyarılar görünür | GEÇTİ (kullanıcı bildirimi) |
| R02-B06.6 | 2. Profil ve güven farkı adımını çalıştır | completed; initial_confidence8, final_confidence10, difference2; 2/8 | GEÇTİ (kullanıcı bildirimi) |

Bu batch 2. analiz adımında durur; 3–8, rapor/yayın ve global kapanış henüz yapılmayacak.

## R02-B06 sonucu ve 0.1.5 etiket okunabilirliği
Kullanıcı “tüm maddeler TAMAM” bildirdi; altı kontrol kullanıcı beyanıyla GEÇTİ. 7e6087ac ilk kararda Bilgi istedi/Açıklama istedi kayıtlarını; d24abca1 ikinci kararda Metin düzenletmek istedi ve iki ayrı gerekçeyi ham JSON içinde doğruluyor. 97855104 bütünlük submitted, revision19, warnings[] ve 1/8; 25227215 güven8/10, difference2 ve 2/8 gösteriyor. Tarih manuel kabulü/refresh ve girdi ayrıntıları beyana dayanıyor. Etiket seçim/geçmiş sunumu anlaşılmaz bulundu: ham JSON öncelikli ve açılış checkboxları boştu. Kullanıcı talebiyle 0.1.5'te güncel seçim işaretli açılır; mesaj bağlamı, isimli önce/sonra ve eklenen/kaldırılan önizlemesi, gerekçeli/tarihli/aktörlü karar geçmişi eklendi. AI önerisi ayrı, boş etiket kararı da son seçim kabul edilir. Yeni düzenin manuel kabulü BEKLİYOR.

## Aktif R02-B07 — Yeni etiket görünümü ve bağımsız rubrikler
Aynı TEST-OGR-01 / üçüncü vaka; mevcut f2e4ae74 analizine 2/8'den devam. Yeni analiz oluşturma.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B07.1 | Ctrl+F5; ilk mesaj etiket ekranı/geçmiş/iptal | Güncel Metin düzenletmek istedi seçili; önce Bilgi+Açıklama, sonra Metin adlarıyla geçmiş; seçim değişince önizleme, iptal kayıt yapmaz | GEÇTİ (kullanıcı bildirimi) |
| R02-B07.2 | 3. Başlangıç rubriğini çalıştır | completed ve 3/8; mock ön değerlendirme çıktısı | GEÇTİ (kullanıcı bildirimi) |
| R02-B07.3 | 3. adım girdi snapshotını incele | sources.initial var, nihai yanıt/reflection/profil güveni puanlama girdisinde yok | GEÇTİ (kullanıcı bildirimi) |
| R02-B07.4 | 3. adım Kabul et + gerekçe | Karar AI çıktısından ayrı kaydolur | GEÇTİ (kullanıcı bildirimi) |
| R02-B07.5 | 4. Nihai rubriğini çalıştır/girdiyi incele | completed/4/8, sources.final var; başlangıç yanıtı/reflection/profil güveni yok | GEÇTİ (kullanıcı bildirimi) |
| R02-B07.6 | 4. adım Kabul et + gerekçe, yenile | Her iki karar, çıktılar ve 4/8 kalır | GEÇTİ (kullanıcı bildirimi) |

Bu batch 4/8'de durur. Sentetik mock puanların gerçek pedagojik değerlendirmesi yapılmış sayılmaz. Rapor/yayın/global kapanış yok.

## Güncel açıklama — Batch7 tekrar bekliyor
Kullanıcı etiketin güncellenmemesinin gerekçeyi boş bırakmasından kaynaklandığını doğruladı; kayıt hatası iddiasını geri çekti. Salt okunur DB'de ilk iki review vardı, yeni Eleştiri kaydı yoktu. Gerekçe zorunluluğu beklenen davranış. Hata bildiriminin dialog arkasında kalması ayrı UX kusuru olarak yeniden üretildi (label-save-error-before.txt: 26 kontrol sonrası görünür dialog-error beklerken timeout). Görünür modal hata/odak ve başarı bildirimi için 0.1.6 kodu yazıldı; genişletilmiş test komutu kullanıcı tarafından kesildi, son koşu tamamlandı/geçti sayılmayacak. Önceki 0.1.5 35+8 sonucu korunuyor. Mevcut kullanıcı DB'sine yazılmadı. Kullanıcı şimdi Batch7 yönergelerini tekrar istiyor: aynı TEST-OGR-01 üçüncü vaka ve mevcut 2/8 analiz, etiket görünümü/iptal, step3 initial girdisi/kabul+zorunlu gerekçe, step4 final girdisi/kabul+zorunlu gerekçe/refresh; 4/8'de dur. Tüm B07 maddeleri bekliyor. 0.1.6 doğrulamasını sonraki geliştirme turunda tamamla; henüz başarı ilan etme.

## R02-B07 sonucu — 2026-10-20
Kullanıcı “adımlar tamam” bildirdi; altı kontrol kullanıcı beyanıyla GEÇTİ. b6550f3e başlangıç rubriği completed ve source initial mock çıktı; af6c3b7c nihai rubriği completed/source final; b8ee228e aynı f2e4ae74 analizinde 4/8 ve sıradaki5 gösteriyor. Girdi snapshotları/karar kayıtları/refresh ve etiket iptal kontrolü kullanıcı beyanıdır; görüntüler karar kayıtlarından önce [] gösterdiği için kabul kararı screenshotla kanıtlanmış sayılmaz. Kullanıcı tüm analiz adımlarının baştan görünmesini istedi; 0.1.7 ile sekiz adım görünür, sırayla açılır, durumlar Türkçe.

## Aktif R02-B08 — Sekiz adımlı görünüm ve adım5–7
Aynı öğrenci-vaka, mevcut analiz f2e4ae74 (4/8); yeni analiz oluşturma.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B08.1 | Ctrl+F5, mevcut analiz görünümü | Sekiz adım görünür;1–4 Tamamlandı;5 Sıradaki;6–8 Kilitli. Önceki sonuçlar açılır | GEÇTİ (kullanıcı bildirimi) |
| R02-B08.2 | 5. Dönüşüm çalıştır | 5/8,5 tamamlandı,6 aktif,7–8 kilitli | GEÇTİ (kullanıcı bildirimi) |
| R02-B08.3 | 5. Sonuçlar ve inceleme → Kabul et+gerekçe | Ayrı karar kaydolur | GEÇTİ (kullanıcı bildirimi) |
| R02-B08.4 | 6. Etkileşim ve etiketler çalıştır, incele, Kabul et+gerekçe | 6/8;6 tamamlandı/kararlı;7 aktif | GEÇTİ (kullanıcı bildirimi) |
| R02-B08.5 | 7. Yansıtma çalıştır, incele, Kabul et+gerekçe | 7/8;7 tamamlandı/kararlı | GEÇTİ (kullanıcı bildirimi) |
| R02-B08.6 | Yenile, aynı dosyayı aç | 7/8 ve kararlar korunur;3–7 kararları varsa8 aktif | GEÇTİ (kullanıcı bildirimi) |

8. adım bu batch'te çalıştırılmayacak, rapor/yayın/global kapanış yok. Çıktılar sentetik mock; pedagojik geçerlik kanıtı değil.

## R02-B08 sonucu — 2026-10-20
Kullanıcı “hepsi tamam” bildirdi; altı kontrol kullanıcı beyanıyla GEÇTİ. 2d4be508 kartlı görünümü ve3.kabul durumunu; a151b83e/e587f87d adım5/6 mock çıktılarının tamamlanmasını (karar öncesi Bekliyor); c4ed37db mevcut f2e4ae74 7/8 ilerlemesini; bca7c7f4 adım7 Tamamlandı/Kabul edildi ve8 Sıradaki adım/aktif düğmeyi destekliyor.5/6 kabul kayıtlarının ayrıntıları ve yenileme kalıcılığı kullanıcı beyanı; screenshotta ayrıca gözlemlenmiş sayılmadı.8henüz çalıştırılmadı.

## Aktif R02-B09 — Rapor birleştirme, indirme ve onay
Aynı TEST-OGR-01 / üçüncü vaka; mevcut analiz f2e4ae74 7/8.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B09.1 | 8.Rapor birleştirme çalıştır | 8/8, tüm adımlar Tamamlandı, yeni rapor snapshot düğmesi | GEÇTİ (kullanıcı bildirimi) |
| R02-B09.2 | Yeni rapor snapshotı oluştur (bir kez) | Rapor geçmişinde yeni sürüm İncelemede | GEÇTİ (kullanıcı bildirimi) |
| R02-B09.3 | Rapor gövdesini incele | Doğru öğrenci/vaka/sürüm; güven8/10/fark2; sentetik mock ve sınırlamalar korunur | GEÇTİ (kullanıcı bildirimi) |
| R02-B09.4 | Word indir/aç | DOCX açılır; doğru öğrenci/vaka/Türkçe ve bölümler okunur | GEÇTİ (kullanıcı bildirimi) |
| R02-B09.5 | PDF indir/aç | PDF açılır; Türkçe karakterler/bölümler okunur, görünür kesilme yok | GEÇTİ (kullanıcı bildirimi) |
| R02-B09.6 | Raporu onayla, refresh/yeniden aç | Onaylı / Yayımlanmadı durumu kalır | GEÇTİ (kullanıcı bildirimi) |

Bu batch'te Ayrıca öğrenciye yayımla ve global veri toplama kapanışı yapılmayacak. Word açacak uygulama yoksa indirme başarılı olsa bile görsel Word kontrolü ÇALIŞTIRILMADI olarak bildirilecek.

## R02-B09 sonucu — 2026-10-20
Kullanıcı “hepsi tamam” bildirdi; altı kontrol kullanıcı beyanıyla GEÇTİ.47e6a25a8Tamamlandı/snapshotdüğmesi;8023d07cSürüm1İncelemede;7bd5a4c9/1305a34fdoğruTEST-OGR-01/üçüncüvaka/testrubrik/profil/sınırlamalar;534fe559güven8/10/fark2 ve içnot; b0b6573fWorduygulamasındaDOCX açılışı/görünenTürkçebölümler; c8573736ChromePDF5sayfa/görünenTürkçe/profil/güventablosu; c1b5b800Sürüm1Onaylı/Yayımlanmadı destekleniyor. TümWord/PDFsayfalarının görsel kontrolü ve refresh kullanıcı beyanı; yalnız screenshotta görünen bölümler bağımsız gözlemlendi. Kod değişmedi, otomatik testler yeniden çalıştırılmadı.

## Aktif R02-B10 — Veri indirme ve öğrenci görünürlüğü
Aynı yerel sentetik veri; global kapanış/yayın yapılmayacak.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B10.1 | Veri indirme,TEST-OGR-01 kapsamı,testdahil kapalı,kodlanmış açık;Excel indir/aç | applications sayfasında sentetik uygulama satırı yok | GEÇTİ (ekran görüntüsü ve kullanıcı bildirimi) |
| R02-B10.2 | Testdahil açık,kodlanmış açık;Excel indir/aç | applications/events/messages/labels vb.sayfalar;participantP-kodu,metinlerçıkarılmış | GEÇTİ (ekran görüntüleri) |
| R02-B10.3 | CSVtablosuevents,CSVindir/aç | Başlık/satırlar,UTCzamanları;Türkçebozulmamış | GEÇTİ (ekran görüntüleri) |
| R02-B10.4 | KodlanmışYapısalJSON indir/aç | applications/events/messages/labels/snapshots/analyses/decisions bölümleri;mesajmetinleriçıkarılmış | GEÇTİ (ekran görüntüleri) |
| R02-B10.5 | Kodlanmışı kapat,JSONyenidenindir/aç | TEST-OGR-01 ve hamtestmetinleri mevcut;UTCkorunur | GEÇTİ (ekran görüntüsü) |
| R02-B10.6 | Çıkış,TEST-OGR-01giriş,üçüncüvakaSon teslim | Teslimsaltokunur;Onaylı/Yayımlanmamışrapor öğrenciye görünmez | GEÇTİ (ekran görüntüsü) |

İndirilen ikiJSONdosyası ayrı tutulmalı. CSVExceltek sütun açılırsaUTF-8/virgül içeaktar seçeneğiyleayır;çıktıyıbozuk saymadan gerçekiçeriğikontrol et. Sonra dur;globalstudyclose ve yayın yok.

## R02-B10.1 sonucu ve devam
Kullanıcı Excelde tüm veri sayfalarının boş, yalnız dictionary dolu olduğunu bildirdi.9a8fe65a boş applications sayfasını,7cd480b6 dictionary alanlarını/Türkçe açıklamalarını doğruluyor. Testdahilkapalı ilk kontrol için beklenen davranış; B10.1 GEÇTİ. Diğer B10.2–6 henüz BEKLİYOR. Sonraki: aynı Veri indirme ekranında KapsamTEST-OGR-01,Sentetik test verisini dahil et açık,Kodlanmış çıktı açık;Excel yenidenindir/eski boşdosyayla karıştırma;applications P-kodlu satırlar ve messages çıkarılmışmetinler beklenir. Hâlâboşsa indirmeekranıseçimlerini gösteren screenshot iste. Yayın/globalkapanış yok. Kod değişmedi/otomatiktestkoşulmadı.

## R02-B10.2 sonucu — kodlanmış Excel
7f904cb4 applications3satır/P-17061f920b41/üçüncüsubmitted;2388d357 ve59590b55 events/type/UTC/payloadçıkarılmış;f607332d messages4satır/userassistant/metinçıkarılmış gösteriyor. B10.2 GEÇTİ. B10.3–6 BEKLİYOR; Excelde events görülmesi ayrı CSVindirme kanıtı değil. Kullanıcı heartbeat/type anlamını soruyor; EVENT_CATALOG.md veapp.js/config doğrulandı: heartbeat15000msperiyodikdurumgözlemi,okuma/düşünmekanıtıdeğil. section_open bölüm,focus pencereodağıdeğişimi,visibilitysayfagörünürlüğü,field_focusalanodağı,text_changedmetindüzenleme,profile_changedseçimdeğişimi,copy/paste. Kod değişmedi; sonrakiCSV,ikiJSON,öğrencigörünmezrapor adımları sürüyor.

## Güncel devam — 0.1.8 olay açıklamaları
Kullanıcı type kodlarının process mining kullanımı ve daha anlaşılır görünmesini istedi; sonra kalan testlere geçilecek. EventLabels.php16olay/7bölüm için Türkçe ad/grup/açıklama ve sözlük sağlar. Export events satırlarına type_tr/section_tr/event_category/event_description eklenir; özgünkodlar/UTC/satırlar/veritabanı değişmez. Dictionary tüm eşlemeleri ve app_id süreçörneği,istemci/sunucu zaman farkını açıklar. heartbeat teknik gözlem,okuma/düşünmeeylemi değil; teknik grup analizamacınagöre filtrelenebilir,otomatik olay silinmez. VeriindirmeUI açıklaması,asset0.1.8. Ctrl+F5 ve yeni dosyaindirme gerekir; eskiExcel değişmez. KullanıcıDB'sine yazılmadı.
PHP tests/export-labels.php30kontrol +tests/run.php69SQLite kontrolü geçti. ÜretilenXLSXPhpSpreadsheetile yeniden açıldı;CSV/JSONTürkçe eşlemeleri,kodlanmışmetinçıkarma,hamverikoruma,tüm16kodunmappingi veDBolaydeğişmezliği doğrulandı. Kanıt test-evidence/event-labels-export.txt veevent-labels-regression.txt. PHP lint/JSsyntax geçti. Chrome/MySQL/hosting/restore bu değişiklikte tekrarlanmadı. DATA_DICTIONARY/EVENT_CATALOG güncellendi.
ManuelB10.1 veB10.2geçti; yeniTürkçesütunlarınmanuelkontrolü veB10.3–6 BEKLİYOR. Sonraki kısa batch: Ctrl+F5 testdahilaçık kodlanmışExcel yenidenindir/events sağsondakiyeni4alan/dictionary;eventsCSV;codedJSON;rawJSON;öğrenciTEST-OGR-01üçüncüvakaSon teslim raporyok/saltokunur. Globalstudyclose/yayın yok.

## R02-B10 ekran kanıtları —0.1.8
Kullanıcı5screenshot paylaştı, ayrıca tümü geçti beyanı yok. a3dbbb12Excel events dörtTürkçe sütunu,heartbeatTeknikdurum veözgün kodları gösteriyor: yeniTürkçesunum GEÇTİ. f4381d73CSVExceldeyanlışayrılmış (başlıklartekalan, açıklamalı satırlarda bazımetinlerayrısütun):B10.3KISMİ;CSVindirildi/Türkçeokunuyorama doğru14sütuniçeaktarma bekliyor. Bu screenshot dosyayı bozuk ilan etmeye yetmez;yerelayırıcıetkisiolabilir, UTF-8/virgül/çifttırnak ile temizdosyadaniçeaktar.
1239afe7kodlanmışJSON P-kodluapplications veçıkarılmışevents payload/Türkçeaçıklamalar: B10.4KISMİ,messages.content çıkarılmış kontrolüscreenshotta görünmüyor.010d345bhamJSON login/testmetinleri/UTC: B10.5GEÇTİ. JSONgörüntüleritümkatılımcıkapsamıiçeriyor;yalnızTEST-OGR-01filtresibuJSONlargöstermez,APIhatasıçıkarımıyok. b1cda681öğrenciüçüncüvakaSon teslimsaltokunur/raporyok: B10.6GEÇTİ. Yayımlanmışrapor/CSVtümsütun/JSONmessages görünmüşsayılmadı.
Aktifsonikikontrol: CSV'yiyeniboşExceldeVeri>Metinden/CSVdenUTF-8virgülçifttırnak ileiçeaktar→14kolon(id…event_description);kodlanmışJSONda[kodlanmış çıktıda metin çıkarıldı]aranmalı. Sonuçbekleniyor. Kod değişmedi/testlertekrarkoşulmadı;globalstudyclose/yayın yok.

## R02-B10 tamamlandı
Eb776866 virgül ve çift tırnak içeaktarma ayarlarını/ayrılan önizlemeyi;767d4009 Excelde CSV başlık ve olayların doğru sütunlarda oluşunu doğruluyor. ef90c246 kodlanmış JSON messages.content alanlarında [kodlanmış çıktıda metin çıkarıldı] gösteriyor. B10.3 veB10.4 GEÇTİ;Batch10altıkontroltamamlandı. Son sağdakiCSVkolonların tamamı screenshotta görünmüyor; yalnızgörünen sütun ayrımı bağımsızdoğrulandı. Yeni kod/testkoşusu yok.

## Aktif R02-B11 — Karar geçmişi ve rapor değişmezliği
Aynı TEST-OGR-01/üçüncüvaka, f2e4ae74 8/8 analiz veonaylı/yayımlanmamışSürüm1. Tüm kontroller BEKLİYOR.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B11.1 | 5.adım Düzenle, yalnızsummarymetnini değiştir,gerekçeli kaydet | GüncelDüzenlendi,eskiAIçıktısıvekarar korunur | GEÇTİ (ekran/metin ve salt okunur DB kanıtı) |
| R02-B11.2 | RaporSürüm1içinde düzenlenen ifadeyi ara | Yeni ifade Sürüm1deyok;onaylıeski snapshot korunur | GEÇTİ (ekran/metin ve salt okunur DB kanıtı) |
| R02-B11.3 | 5.adımReddet ve6.adımKararverilemedi,gerekçeli kaydet | İki ayrı durum/geçmiş;kabul edilmiş toplam gibi gösterilmez | GEÇTİ (ekran/metin ve salt okunur DB kanıtı) |
| R02-B11.4 | Yeni rapor snapshotı yalnızbirkez | Sürüm2İncelemede;Sürüm1Onaylı/Yayımlanmadı | GEÇTİ (ekran/metin ve salt okunur DB kanıtı) |
| R02-B11.5 | İki raporu karşılaştır | Sürüm2partsstep5reject,step6uncertain;Sürüm1deikisideaccept | GEÇTİ (ekran/metin ve salt okunur DB kanıtı) |
| R02-B11.6 | 5ve6yıtekrarKabulet+gerekçe,refresh | GüncelikiKabul;eskikararlarveraporSürüm2değişmez | GEÇTİ (metin/screenshot ve salt okunur DB) |

Yeni analiz/8.adımı tekrar çalıştırma. Sürüm2onaylama/yayınlama/globalstudyclose yok. Tüm yazmalar mevcut sentetik test akışının parçası;başlangıç/öğrenciyanıtlarıdeğiştirilmez.

## R02-B11.1 açıklama — henüz düzenleme testi geçmedi
Kullanıcı1.adımdaKabulet seçtiğini belirtti.2b872866 screenshot5Dönüşümadımıiçin ikiacceptkararı,sonundaSENTETİK TEST:Düzenleme kontrolü gerekçesi veözgünAIbody gösteriyor. Sistemacceptiçin özgünAIçıktısını kullanır;editseçilmediği için düzenlenmişsummaryuygulanmaz,bukayıphatasıdeğil. B11.1düzenlemebeklenenisonuçHENÜZTAMAMLANMADI;diğerB11adımlarınıpasssayma. Kullanıcıekrandakibutonisimleriyleaçıkbatchtalimatıistiyor:5.Dönüşüm>Sonuçlar ve inceleme>Kabul / Düzenle / Reddet / Karar verilemedi düğmesi>dialogKararaçılırlistesiDüzenle>JSONsummarydeğeri>Karargerekçesi>Kararı ayrı kaydet. KabulvarsayılanınınbilerekDüzenleyedeğiştirilmesivurgulanmalı. Eskiacceptkararısilinmez,yenieditkararıeklenir;Sürüm1değişmez. Koddeğişmedi/testkoşulmadı;B11altıkontrolbekliyor.

## R02-B11 kanıt incelemesi — son kabul dönüşü eksik
5e4e9436 screenshot5Dönüşümde eskiacceptler+yeniedit/düzenlenensummary/gerekçe gösteriyor.5502d1ac Sürüm1Onaylı/Yayımlanmadı veSürüm2İncelemede. ÜçekmetinJSONolarakokundu:3fc7e273ve75bf6173raporgövdeleri normalizeJSONolaraktamamenaynı;ikisininstep5/6accept. b955c62fmetnindestep5reject,step6uncertainvar;buüçüncümetinDBSürüm2bodyilebirebireşit. B11.1–5GEÇTİ. SaltokunurDBkontrolü:5geçmişiaccept/accept/edit/reject,sonacceptyok;6accept/uncertain/accept/accept,sonkararkabul. DolayısıylaB11.6KISMİ,5DönüşümüKabuletgeridönüşüeksik;6ikiayrıacceptkaydıkorunmuş,verikaybıyok. Sürüm2hâlâreject/uncertain,son6kabulüeski raporudeğiştirmemiş. KullanıcıDB'sineyazılmadı/koddeğişmedi/testsuitekoşulmadı.
Sonraki:yalnız5.Dönüşüm>Sonuçlar ve inceleme>Kabul / Düzenle / Reddet / Karar verilemedi>KararKabulet>gerekçeSENTETİK TEST: Kabul durumuna dönüş.>Kararı ayrı kaydet;refresh5ve6Kabuledildi,raporSürüm2aynıkalmalı. Yenianaliz/snapshot/onay/yayın/globalcloseyok. BuadımkabulgelmedenB11tamamlandıdeme.

## R02-B11 tamamlandı ve aktif R02-B12
1301bc7c screenshot ve kullanıcımetni5Dönüşümün sonacceptkararını eaa079c4...,17:58:30UTC veKabul durumuna dönüş gerekçesiyle doğruluyor. SaltokunurDB:5accept,6accept;raporSürüm2parts5reject/6uncertainolarakkorunmuş. B11.6GEÇTİ veBatch11tamamlandı. Kod/testsuite değişmedi/koşulmadı;DByazılmadı.
AktifB12ikiayrısenaryoyuhafifbatchtebirleştirir:globalveritoplamaaçıkkenSürüm1yayındenemesiengeli;ikincihalençalışılanvakadayalnıznihaitaslakileChromeçevrimdışıkayıt. Üçüncüteslimedilmişvaka/rapor2değiştirilmeyecek.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B12.1 | AraştırmacıüçüncüvakaRaporSürüm1>Ayrıca öğrenciye yayımla | Araştırma genelinde veri toplama açık hatası;yayınolmaz | GEÇTİ (ekran görüntüsü) |
| R02-B12.2 | Refreshaynıraporvesistemdurumu | Onaylı/Yayımlanmadı;SistemveişlemgeçmişiGlobaldurumAçık | GEÇTİ (kullanıcı bildirimi) |
| R02-B12.3 | ÖğrencigirişiüçüncüvakaSon teslim | Başarısızyayındenemesinden sonra raporhâlâyok | GEÇTİ (kullanıcı bildirimi) |
| R02-B12.4 | İkinciSınıfta dijital araç kullanımıNihai değerlendirme;F12NetworkOffline;kararmetniyaz | Sunucuyakaydedildidenmez;çevrimdışıuyarısı | GEÇTİ (kullanıcı bildirimi) |
| R02-B12.5 | NetworkNo throttling/Online | Aynıtaslakkaydedilir;sunucukayıtonayı | GEÇTİ (kullanıcı bildirimi) |
| R02-B12.6 | F12kapat,refreshikinciNihai değerlendirme | Testkararı korunur;son teslimyapılmaz | GEÇTİ (kullanıcı bildirimi) |

Kullanılacakikinci-vakanihaitestmetni:SENTETİK TEST: Bağlantı kesintisinde yazılan karar. Offlineikenrefresh/çıkışyapma;yalnızOnlinekayıtonayısonrasırefresh. Gerçekgüçkesintisitestideğildir. SistemdeGlobalveritoplamayıkapatdüğmesinebasılmayacak.

## R02-B12.1 kanıtı ve talimat açıklaması
A4036a63 screenshotSürüm1Onaylı/Yayımlanmadı veAraştırma genelinde veri toplama açık hatası gösteriyor:B12.1GEÇTİ. B12.2refresh/sistemdurumu ayrıca doğrulanmış değil. Kullanıcı2.adımdansonrasınıanlamadı;öğrencihesabınageçiş/üçüncüraporyokluğu/ikincihenüzteslimedilmeyenvakadanNetworkOffline→metin→Online→kayıtonayı→refresh açıkça tekrarlanacak. B12.3–6bekliyor,henüzpassyok. Kod/testdeğişmedi.

## R02-B12 tamamlandı ve aktif R02-B13
Kullanıcı “hepsi tamam” bildirdi;B12altıkontrolGEÇTİ.79356063üçüncüvakaöğrencisaltokunur/raporyok;1abd92dbOfflineveBağlantı yok,sunucuya gönderilemedi,başarısızsave/events;82d24e11No throttling,sunucukayıtonayıveHTTP200save/events gösteriyor. Refreshmetinkalıcılığı/sistemAçık kullanıcıbeyanı. BuChromeçevrimdışısınamasıdır;gerçekelektrik/PCcrashkurtarmasısayılmaz. İkincivakaçalışmayadevam,nihaitestkararıeklendi,teslimedilmedi. Koddeğişmedi/testlerkoşulmadı.
AktifB13katılımcıyönetimiayrıTEST-YONETIM-01hesabıyla yapılacak. NormalChromearaştırmacı,gizlipencereöğrenci. HerformgerekçesiSENTETİK TEST: Katılımcı yönetimi. ParolayalnızTEST_DATA.mddekitestparolası;paylaşılmayacak.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B13.1 | Katılımcılar>+Katılımcı ekle,TEST-YONETIM-01/Sentetik Yönetim Testi/Etkin/gerekçe | Hesaplistedeactive | GEÇTİ (ekran ve salt okunur DB) |
| R02-B13.2 | Vakalar ve atamalar>Atama yönet,ilkGrupçalışmasındaadaletvakasınıyenihesabaata | Öğrenciyegörünenatanmışaktifvaka | GEÇTİ (ekran ve salt okunur DB) |
| R02-B13.3 | Gizlipencereyenihesapgirişi,vakabaşlat,başlangıçproblemtaslağıkaydet | Sunucukayıtonayı;kesinleştirmeyok | GEÇTİ (ekran ve salt okunur DB) |
| R02-B13.4 | AraştırmacıDüzenle/Dondur/Arşivle>Hesap durumuDondurulmuş,kaydet;öğrenciçıkış/giriş | Girişengellenir | GEÇTİ (ekran ve salt okunur DB) |
| R02-B13.5 | HesapEtkin/öğrencinoTEST-YONETIM-02/parolaboş;kaydet;yenilogingiriş | Atama/taslakaynı;parola/değişmezkimlikkorunur | GEÇTİ (ekran ve salt okunur DB) |
| R02-B13.6 | HesapArşiv,kaydet/listedearşivlenmiş;tekrarEtkin,kaydet;öğrencigiriş | Geriaktifleştirmedetaslak/atamakorunur | GEÇTİ (ekran ve salt okunur DB) |

Problemtaslağı:SENTETİK TEST: Hesap yönetiminde korunacak taslak. Yeniöğrencibaşlangıcıhenüzkesinleştirmeyecek;mevcutTEST-OGR-01verisine dokunma. Global kapanış/yayın yok.

## R02-B13 açıklama — donma henüz uygulanmamış
Kullanıcı4.adımdansonrasınıanlamadığınıbildirdi.52fbb8ddlistedeTEST-YONETIM-01active,255d541dtekatananaktifvakayıgösteriyor (screenshotvakabaşlamadanönce). SaltokunurDBgüncel:hesapactive,id a56e4d952346041cd7526dbfffb86ce3;test-case-1initialproblemtaslağıSENTETİK TEST: Hesap yönetiminde korunacak taslak.kaydedilmiş. Audittekparticipantoluşturma;dondurma/yenidenadlandırmahenüzyok. B13.1–3GEÇTİ;B13.4–6BEKLİYOR. Sonrakitalimat4tenbaşlayacak,normalpencerearaştırmacı/gizliöğrenciayrımıherişlemdebelirtilecek;yalnızTEST-YONETIM-01hedeflenecek,TEST-OGR-07zatenfrozensatırınadokunma. 4dondur/girişengeli→EtkinveTEST-YONETIM-02yenino/parolaboş→yenilogingiriş/taslak→Arşiv→Etkingeridönüş/taslak. KullanıcıDB'siyazılmadı/koddeğişmedi/testkoşulmadı.

## R02-B13 sonucu ve0.1.9sekme düzeltmesi
E3ee722agirişengeli,7fba4864TEST-YONETIM-02giriş/atananvaka,63619574taslakkorunması,c191d0acbaşkasekmeuyarısısaltokunur. SaltokunurDBauditaynıteknikid a56e4d952346041cd7526dbfffb86ce3içinactive→frozen→active+TEST-YONETIM-02→archived→activeveproblemtaslağıkorunmuş;B13.4–6GEÇTİ,katılımcıyönetimikontrolleritamam. AyrıUXregresyonu:güvenliçıkışsessionStoragetabkimliğinitemizliyoramalocalStorageeskiownerkilidini20saniyebırakıyordu;hızlıyenigirişbaşkasekmeuyarısıverebiliyor. İzolesentetikChrometestindeyenidenüretildi(logout-lease-before.txt4kontrolsonrasıFAIL). 0.1.9releaseLeaseyalnızsahibiolduğukilidihome/başarılılogouttatemizler;başkasekmekilidinitemizlemez. Kodpublic/assets/app.js,indexassets0.1.9.69Chrome kontrolüGEÇTİ(logout-lease-after.txt),gerçekikincisekmehalenreadonly. JSsyntax/indexPHPlintgeçti. Backend/MySQL/export/restoreyenidenkoşulmadı;mevcutDB'yeyazılmadı.
Yeni0.1.9manuelkontrolBEKLİYOR:diğeröğrencisekmelerinikapat,Ctrl+F5,TEST-YONETIM-02Grupçalışmasıvakasınıaç;gerekiyorsa20saniyesonrayenile,uyarıyok/düzenlenebilir. Problemtaslağınakısasentetikekyaz/sunucukayıtbekle;çıkışvehemenyenigiriş/açılıştasahtebaşkasekmeuyarısıyok/metinkorunmuş. Buretesthenüzpasssayılmayacak;sonrakibatchvaka/atama/sürümyönetimiolacak. Globalstudyclose/yayınyok.

## 0.1.9 manuel tekrar tamamlandı — R02-B14
Kullanıcı üçkontrol için “hepsi tamam” bildirdi.0.1.9sahteleaseuyarısınınkalkması,düzenlenebilirtaslak/sunucukaydı,hızlıçıkış-girişmetinkorunması kullanıcıbeyanıylaGEÇTİ;yeniscreenshotyok. B13veleasekullanıcıtekrarıtamam. Koddeğişmedi/testsuitekoşulmadı.
AktifB14vaka/atama/sürümyönetimiBEKLİYOR. AyrıSENTETİK TEST · Vaka sürümü adlıDenemevakasıoluşturulacak;mevcutvakalardüzenlenmeyecek. Normalpencerearaştırmacı,gizlipencereöğrenci.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B14.1 | Vakalar ve atamalar>+Vaka ekle;Taslak,Denemevakası;İLK METİN | YeniTaslak;Öğrenciönizlemesiaynımetin | GEÇTİ (ekran/salt okunur DB) |
| R02-B14.2 | Yeni kartDüzenle/Yeni sürüm>DurumAktif;öğrenciTEST-YONETIM-02giriş/refresh | Atanmadığıiçingörünmez | BEKLİYOR |
| R02-B14.3 | Atama yönet TEST-YONETIM-02yenivakaata;öğrenciBaşla | İlkmetinvesürümnumarasıyüklenir;başlatılır | GEÇTİ (ekran/salt okunur DB) |
| R02-B14.4 | YalnızyenitestvakasımetniGÜNCEL METİNileyenisürümkaydet | Yeniaktifvakasürümüoluşur | GEÇTİ (ekran/salt okunur DB) |
| R02-B14.5 | TEST-YONETIM-02mevcutçalışmayenile | İLK METİNveöncekisürümkorunur | GEÇTİ (ekran/salt okunur DB) |
| R02-B14.6 | TEST-OGR-02yenivakaata;öğrenciçıkış/girişBaşla | GÜNCEL METİNvedahayenisürüm | GEÇTİ (ekran/salt okunur DB) |

Başlık:SENTETİK TEST · Vaka sürümü;Sıra7;Vakametni:[İLK METİN] Öğrenciler grup görevi için farklı çalışma yöntemleri öneriyor.;Yönerge:Kararınızı ve gerekçenizi yazın.;RubrikTESTRUBRİĞİ;Denemevakasıişaretli;Devam eden erişimi de durdurişaretsiz;hergerekçeSENTETİK TEST: Vaka sürümü kontrolü. Güncelmetin:[GÜNCEL METİN] Öğrenciler grup görevinde görev paylaşımı konusunda anlaşamıyor. Aktifyaparken de sürümartacağından İLK METİN/GÜNCEL METİN işaretleri gerçekv1/v2numarasıdeğildir;ekrandakigerçeksürümnumaralarınıkarşılaştır. Yeniöğrenciyanıtıkesinleştirme/teslimyok,globalclose/yayınyok.

## R02-B14 ekran kanıtı ve aktifB15
6aa6b2c6taslakv1önizlemesi;7028becfTEST-YONETIM-02sürüm2İLK METİN;563c4241aynıöğrenci/sürüm2ilkmetin;89f8a191TEST-OGR-02sürüm3GÜNCEL METİN. SaltokunurDBvaka2311909712f51cb7c2702554b488906dactive/practice1;ikibaşlangıçsürümü2/3ayrımetinlerde. B14.1,3,4,5,6GEÇTİ.B14.2atamaöncesigörünmezlikscreenshot/beyanyok,BEKLİYOR. SonrakiB15.1aynısenaryoyuhenüzatanmamışTEST-OGR-03iletekrarlayıpkapatacak. Koddeğişmedi/DB'yeyazılmadı/testsuitekoşulmadı.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B15.1 | TEST-OGR-03Vakalarım'dayenitestvakasıyok;sonraaraştırmacıata,öğrenciBaşlabasma | Atamasızgörünmezlikveaçılmamışatamahazırlığı | BEKLİYOR |
| R02-B15.2 | YalnızyenitestvakasıDurumYeni başlangıçlara kapalı,stopkapalı;kaydet;TEST-OGR-03refresh | Henüzbaşlamadığıiçinvakagörünmez | BEKLİYOR |
| R02-B15.3 | TEST-YONETIM-02giriş/mevcutyenivakaDevam et | Başlanmışçalışmaerişilebilir;İLK METİNsürüm2 | BEKLİYOR |
| R02-B15.4 | YalnızyenitestvakasıDevam eden erişimi de durdurişaretli,kaydet;öğrencirefresh | Başlanmışvaka daVakalarım'dagörünmez/erişimkapalı | BEKLİYOR |
| R02-B15.5 | DurumAktif,stopişaretsiz,kaydet;TEST-YONETIM-02refresh | Vakaeskiçalışma/sürüm2ilegerigelir | BEKLİYOR |
| R02-B15.6 | TEST-YONETIM-02yenitestvakasıatamasınıkaldır>öğrencirefreshyok;yenidenata>refreshDevamet | Atamaerişimdenetimi;eskiuygulama/sürüm2korunur | BEKLİYOR |

HerişlemgerekçesiSENTETİK TEST: Vaka erişimi kontrolü.YalnızSENTETİK TEST · Vaka sürümüdenemevakası;orijinalaraştırmavakalarınıdeğiştirme. TEST-OGR-03bu batchvakayıbaşlatmayacak. Kapanışglobaldeğilvakadüzeyinde;globalstudyclose/yayın/teslimyok.

## 0.2.0 — Yeni kurallar, yeni manuel batch yok

Kullanıcı araştırmacı panelini sadeleştirme ve ayrı çalışma alanlarını istedi; ortak vaka araştırmacısı fikrini sonradan açıkça iptal etti. Güncel akış RELEASE_READINESS.md'dedir. Önceki global kapanış ve beş vaka kapısı yönergeleri tarihsel kalır. Yeni özellikler otomatik HTTP/Chrome testleriyle doğrulandı; kullanıcı yapmış gibi manuel GEÇTİ yazılmadı. Mevcut kayıtların korunması yedekli migration ile doğrulandı; hiçbir batch ilerletilmedi.

## 0.2.1 — Ek süre için kullanıcıya yeni batch gerekmedi

Kullanıcının tekrar isteği otomatik karşılandı: seçili öğrenci açılıyor, seçilmeyen kapalı kalıyor, süre iptalinde yeniden salt okunur. Yeni arayüz/rubrik/geçmiş otomatik test ve sentetik görsellerle doğrulandı; kullanıcı yeni manuel test yapmış sayılmadı. Mevcut çalışma DB'sinde yeni yazma yok.

## 0.2.3 — Sade rapor otomatik doğrulaması
Yeni kullanıcı manuel kabulü yok. Otomatik Chrome/HTTP ve sentetik ekran/PDF incelemesi TEST_RESULTS.md kayıtlarındadır. Ayrı8089kopyasındaSürüm3hazır; gerçek değerlendirme veya ana8088yayını değildir. Yeni manuel batch istenmedi.

## 0.2.4 — Süreç ekranı sadeleştirmesi
Kullanıcının JSON okunabilirliği ve AI etiketleme bildirimi uygulandı; yeni manuel kullanıcı kabulü yapılmış sayılmaz. Otomatik Chrome/etiket/Disco/sunucu sonuçları ve sentetik görsel inceleme TEST_RESULTS.md içindedir. Kullanıcıya yeni manuel batch çıkarılmadı.

## 0.2.5 — Yedi sekme süresi
Kullanıcı süreyi girişten sonraki geçişe kadar geçen zaman ve son teslimde bitiş olarak tanımladı. Bu kural otomatik fixture/Chrome/HTTP testleriyle doğrulandı; kullanıcı manuel test yapmış gibi işaretlenmedi. Yeni batch istenmedi. Gerçek cihaz/Disco kabulü bekliyor.

## 0.2.6 — Ziyaret açıklamaları

Kullanıcının belirsiz etkinlik ve kapanış adları bildirimi uygulandı. Otomatik sonuçlar TEST_RESULTS.md içinde; kullanıcı manuel doğrulaması yapılmış sayılmadı, yeni batch istenmedi. Sentetik Chrome ekranı geliştirici tarafından görsel incelendi.

## 0.2.8 — AI akışı ve süre sınırları

Kullanıcı akışı soru → yanıt geldi → sohbet görüntüleme → yeni soru → yanıt geldi → görüntüleme → başka bölüm olarak netleştirdi. Bu kural ayrı sentetik fixture ve Chrome ile doğrulandı; yeni manuel batch istenmedi. Kullanıcı manuel kabulü veya gerçek Disco import tamamlandı sayılmaz. Otomatik kanıtlar TEST_RESULTS.md içindedir.

## 0.2.9 — Eksik eski soru satırı bildirimi

Kullanıcı ekranında yalnız görüntüleme/yanıt satırlarını bildirdi ve sonra iki öğrenci sorusunu etiketlediğini söyledi. Yerel DB salt okunur inceleme, soruların Bilgi istedi ve Eleştiri istedi araştırmacı seçimlerini doğruladı. Sorun eski gönderim telemetrisi eksikliğiydi; düzeltme türetilmiş görünümde uygulandı. Kullanıcı yeni öğrenciyle test etmeyi seçti; sonucu henüz gelmedi, geçti olarak işaretlenmedi. Yeni manuel batch talep edilmedi. Otomatik kanıt TEST_RESULTS.md içinde.

## 0.2.10 — Kullanıcının yeni akış önerisi

Kullanıcı yeni öğrenci ekranındaki Bilgi istedi/Eleştiri istedi ve yanıt/görüntüleme sırasını ekranla gösterip olumlu bildirdi. Bu, gösterilen akış için kullanıcı bildirimi/ekran kanıtıdır; 40 saniye veya tüm cihazlar manuel geçti sayılmaz. Ardından kullanıcı etiketli gönderim → AI hazırlanıyor → yanıt geldi → görüntüleme ayrımını önerip uygulamayı onayladı. Bu değişiklik otomatik sentetik testlerle doğrulandı; yeni manuel batch istenmedi. 40 saniye testi zaman fixture'ıdır, gerçek sağlayıcı çağrısı değildir.
