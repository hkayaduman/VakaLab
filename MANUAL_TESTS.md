# VakaLab Manuel Testleri ve Dönüt

Durum 0.2.1: Kullanıcıyla Batch01–13 tamamlandı; B14sürüm ekran kanıtı ve B14.2/B15otomatik karşılıkları MANUAL_TEST_RESULTS.md'de ayrıdır. **Bu belge yeni bir manuel batch isteği değildir.** Yerel12otomatikgrup tamamlandı; gerçek hosting/AI/kurum/fiziksel cihaz-kesinti ve Disco import açık. T01–T28kataloğunun tümü manuel GEÇTİ sayılmaz.

## Güncel ekran ve işlem haritası

- Öğrenci: Vakalarım → Başla / Devam et / Görüntüle; altı çalışma bölümü ve7.Son teslim. Katılım bilgilerim düğmesi/formu kaldırıldı; isteğe bağlı Program/Sınıf araştırmacı kullanıcı formundadır.
- Araştırmacı: Genel durum → Vaka ve öğrenci seç → Vaka / Öğrenci ara / Çalışma durumu → Çalışmayı aç. Matris yoktur.
- Kullanıcılar → + Kullanıcı ekle → Öğrenci/Araştırmacı. Yeni araştırmacı boş ayrı alanda başlar. Kısa form, isteğe bağlı Ek bilgiler; rutin işlem gerekçesi yoktur. Başka araştırmacının verilerine erişemez.
- Vakalar ve atamalar: +Vaka ekle, Öğrenci önizlemesi, Düzenle, Atanan öğrencileri göster; Toplu atama / Ek süre ile checkbox/Tümünü seç. Her kartta Veri toplamayı kapat / Veri toplamayı aç. Kapalı vaka okunur; yalnız seçilen öğrenciye ek süre verilebilir. Başlangıç/teslim kilitleri açılmaz.
- Rubrikler ve etiketler → +Rubrik: ad/yönerge, boyutlar/min/max/ağırlık ve düzey adı/puan/açıklaması. JSON sistem tarafından üretilir. Düzenle yeni sürüm; Sil yeni seçimlerden kaldırır, geçmişi korur.
- Tek öğrenci-vaka dosyasında Yeni analiz sürümü → 1…8sırayla;3…7için gerekçeli karar;8sonrasında Yeni rapor snapshotı oluştur. Raporu onayla ve Ayrıca öğrenciye yayımla ayrıdır. Yayın yalnız ilgili vaka ve öğrencinin ek süresi kapalıysa mümkündür; başka vakaların kapanması gerekmez.
- Mesaj etiketi: Öğrenci yanıtları ve çalışma süreci →3.AI konuşması ve mesaj etiketleri → Etiketleri incele / Değiştir → çoklu seçim/gerekçe → Etiket değişikliğini kaydet.
- Veri indirme → Hangi vaka?:Tüm vakalar/tek vaka; Excel indir / Tüm verileri JSON indir seçilen kapsamdaki tüm kayıtları içerir. Disco CSV indir kodlanmış Case ID/Activity/UTC/Resource alanlarını hazır verir; teknik gürültü ayrı tutulur. Eski filtre checkboxları yoktur.
- İşlem geçmişi: Türkçe okunaklı kayıtlar,arama,20şerDaha fazla göster ve İşlem geçmişini JSON indir. Global kapanış düğmesi yoktur.
- Takılı AI/analiz: timeout+60s sonrası gerekçeli kapatma; sağlayıcı maliyetini iptal etmiş sayılmaz. Streaming/ilk parça ölçümü yoktur.

Senaryolar ancak gerekirse ayrı yeni sentetik ortamda uygulanır. Mevcut8088çalışma DB'sini seed/reset ile değiştirmeyin. Parolalar/anahtarlar chat/Git/kanıtta yer almaz. Ayrı test fixture parola ve yapılandırmaları Git dışındadır; TEST_DATA.md'deki yer tutucu gerçek giriş parolası değildir.

## Her test için kanıt kaydı

Test ID, uygulama sürümü, test DB adı (parola yok), cihaz/tarayıcı, mock modu, adımlar, beklenen/gerçekleşen, ekran görüntüsü veya ilgili kayıt ID, sonuç: GEÇTİ/KALDI/ÇALIŞTIRILAMADI. Fiziksel mobil klavye ve gerçek kesinti denenmediyse emülasyon sonucu bunların yerine geçmez.

## 1. Teste hazırlık

1. INSTALL_SHARED_HOSTING.md ile ayrı test ortamını kur.
2. Üretim verisi olmayan ayrı test veritabanı kullan.
3. Yalnız yeni ayrı test DB'sinde sentetik fixture oluştur; bütün verilerin SENTETİK/TEST olarak işaretlendiğini doğrula.
4. Araştırmacı ve iki öğrenci için ayrı tarayıcı profili veya gizli pencere aç. Aynı hesabın iki sekme testi ayrıca yapılacak.
5. Test sürümünü, tarayıcıyı, cihazı, saat dilimini ve mock/gerçek AI seçimini kaydet.
6. Gerçek API anahtarını ekran görüntüsüne veya hata dönütüne ekleme.
7. Her test için GEÇTİ / KALDI / ÇALIŞTIRILAMADI yaz. Boş bırakılmış test geçti sayılmaz.
8. Gerçek AI testi maliyet oluşturabilir; önce mock ile doğrula. Mock sonucu gerçek sağlayıcı entegrasyonunun kanıtı değildir.

## 2. Test sırası

### T01 : Katılımcı yönetimi
1. Araştırmacı olarak sentetik öğrenci ekle.
2. Öğrenci numarasını düzenle.
3. Hesabı dondur; öğrenci girişi dene.
4. Yeniden etkinleştir; giriş dene.
5. Arşivle ve arşiv görünümünden incele.
Beklenen: veri korunur; teknik kimlik/ilişkiler değişmez; dondurulmuş giriş reddedilir; kalıcı sil yoktur. İşlemler denetim günlüğünde görünür.

### T02 : Vaka, atama ve aktivasyon
1. Taslak vaka oluştur ve öğrenci görünümünde önizle.
2. Öğrenciye atamadan aktif yap; öğrenci hesabını kontrol et.
3. Öğrenciye ata; aktif vakayı aç.
4. Başka öğrenciye atanmadığını doğrula.
5. Genel durum → Vaka listesinde yeni vakayı ve atanmış öğrencilerini kontrol et.
Beklenen: yalnızca aktif + atanmış öğrenci erişir. Doğrudan adres erişimi de korunur.

### T03 : Vaka sürümü
1. Öğrenci vakayı başlatsın.
2. Araştırmacı vaka metnini değiştirsin.
3. Eski uygulamayı ve yeni sürümle başlayan başka öğrenciyi karşılaştır.
Beklenen: eski uygulama eski metni korur; sürümler görünür; geçmiş üzerine yazılmaz.

### T04 : İlk ekran ve mobil gezinme
1. Vakayı aç; Vaka/Profil/Başlangıç/AI/Nihai/Yansıtma başlıklarını kontrol et.
2. Bölümler arasında gezin.
3. Mobilde aynı işlemi ve ekran yönü değişimini dene.
Beklenen: vaka sürekli açık değildir; AI görünür ama kilitli; nihai/yansıtma içerikleri kilitli; durumlar yalnız renkle anlatılmaz; yatay taşma temel kullanımı engellemez.

### T05 : Profil ve başlangıç kilidi
1. Profilde bir alanı boş bırakıp başlangıcı kesinleştirmeyi dene.
2. Dört profil alanını ve üç başlangıç yanıtını doldur.
3. Kesinleştirme onayını önce iptal et; düzenlemeye devam et.
4. Sonra onayla.
5. Başlangıca ve profile dönüp düzenlemeyi dene.
Beklenen: eksik profil engellenir; iptal kilitlemez; başarılı sunucu kaydıyla AI açılır; profil/başlangıç salt okunur olur. Başlangıç nihai ekranda otomatik görünmez.

### T06 : Kayıt ve yeniden giriş
1. Başlangıç veya nihai alana ayırt edici test cümlesi yaz.
2. Sunucuya kaydedildi göstergesini bekle ve sayfayı yenile.
3. Çıkış yapıp aynı öğrenciyle yeniden gir.
Beklenen: doğru sürüm geri gelir; yeni sohbet/uygulama oluşmaz. Kaydedildi mesajı sunucu onayıyla eşleşir.

### T07 : Bağlantı kesintisi ve yerel kurtarma
1. Yalnızca test ortamında tarayıcının ağ bağlantısını çevrimdışı yap veya bağlantıyı kes.
2. Nihai alanda yeni test cümlesi yaz; kayıt durumunu kontrol et.
3. Bağlantıyı geri getir ve kaydı doğrula.
4. Ayrı senaryoda kesinti sonrası sayfayı yeniden aç; kurtarmayı dene.
5. Aynı cihazda başka öğrenciyle giriş yap.
Beklenen: çevrimdışıyken sunucuya kaydedildi denmez; aynı hesaba ait kurtarma çalışır; başka öğrenciye metin görünmez; kesinleşmiş veri üzerine yazılmaz. Gerçek elektrik kesintisi taklidi yapılmadıysa yapıldı yazma.

### T08 : AI bağlamı ve mesaj kaydı
1. Başlangıç yanıtına yalnız testte kullanılan ayırt edici bir ifade yaz.
2. Başlangıcı kesinleştir.
3. AI'a vaka hakkında genel bir soru gönder.
4. Araştırmacı test payload incelemesinden gönderilen alanları kontrol et; sırları gösterme.
Beklenen: vaka sürümü vardır; başlangıç/profil/nihai/etiket/rubrik yoktur. Öğrenci kendi metnini gönderirse yalnızca o mesajla görünür. Mesajlar sıralı ve ayrı rollerdedir.

### T09 : AI üretirken ikinci mesaj ve eşzamanlı yazma
1. Gecikmeli mock yanıtını aç.
2. Bir mesaj gönder.
3. İkinci soruyu taslak yaz ve göndermeyi dene.
4. Nihai bölüme geçip metin yaz; sunucuya kaydı kontrol et.
5. Yansıtma ve vakaya geç.
6. Yanıt bitince sohbete dön ve ikinci soruyu gönder.
Beklenen: ikinci mesaj/queue yok; soru taslağı korunur; nihai autosave AI bitmeden sunucuya ulaşır; ekran zorla değişmez. Bu test PHP session lock hatasını da yakalamalıdır.

### T10 : Yanıtı görüntüleme, etkileşimsizlik ve arka plan
1. Gecikmeli/parçalı yanıt başlat; sohbette kal.
2. Görünür etkileşim yapmadan bir süre bekle.
3. Yanıtı kaydır.
4. Başka tarayıcı sekmesine geç ve geri dön.
5. Aynı senaryoda AI sürerken nihai metin yaz.
Beklenen: ilk parça, tamamlanma, görünürlük, odak ve düzenleme ayrı olaylardır. Rapor okudu/düşündü diye kesinlemez. Hosting akışı tamponluyorsa ölçüm eksikliği açık görünür.

### T11 : İkinci sekme ve çift tıklama
1. Aynı öğrenci-vakayı ikinci sekmede aç.
2. Düzenlemeyi ve mesaj göndermeyi dene.
3. İlk sekmede gönder/teslim düğmesine hızlı iki kez bas.
Beklenen: ikinci sekme engelli veya salt okunur; çift mesaj/teslim yok; sunucu kilidi uygulanır.

### T12 : Kopyalama ve yeniden açma
1. Vakadan bir cümle kopyala.
2. Başlangıç yanıtını açıp bir bölüm kopyala.
3. AI yanıtından kopyala ve nihai alana yapıştır.
4. Araştırmacı olay çizelgesini incele.
Beklenen: kaynak bölüm/sürüm/aralık ve olaylar ayrıdır. Kopyalama otomatik benimseme sayılmaz. Genel pano içeriği toplanmaz.

### T13 : Metin sürümleri
1. Nihai alana Taslak A yaz.
2. AI'a soru gönder.
3. Yanıt sonrası nihai metni Taslak B olarak değiştir.
4. Tekrar AI ve Taslak C oluştur.
Beklenen: sürümler ve mesajlar kronolojik bağlanır; yalnız son metin kalmaz; autosave olayları anlamlı düzenlemeyi boğmaz.

### T14 : Son teslim
1. Zorunlu nihai/yansıtma alanı eksikken teslim dene.
2. AI üretirken teslim dene.
3. Yanıt tamamlanınca alanları doldur ve onayla.
4. Teslim sonrası tüm alanları ve sohbeti kontrol et.
Beklenen: eksik/aktif AI engellenir; başarılı teslimde her şey salt okunur; zaman görünür; tüm süreç görüntülenebilir.

### T15 : Vaka/öğrenci listesi ve teknik durum
1. Açmamış, taslak, AI, nihai ve teslim fixturelarını incele.
2. Son etkinlik ve son bölüm bilgilerini kontrol et.
3. Hata üreten mock senaryosunu çalıştır.
Beklenen: durumlar doğru; son bölüm canlı varlık diye sunulmaz; hata tam yanıt sayılmaz; filtreler çalışır.

### T16 : Etiket düzenleme ve yeni etiket
1. Bir mesaja AI etiketini incele.
2. Etiketi değiştir; ikinci etiket ekle.
3. Yeni etiket tanımlayıp aynı mesaja ata.
4. Etiket adını düzenle veya kullanımdan kaldır.
Beklenen: AI önerisi korunur; araştırmacı kararı ve geçmiş görünür; öğrenci görmez; export çoklu ilişkiyi kaybetmez.

### T17 : Rubrik ve sürüm
1. TEST RUBRİĞİ metnini ve yapılandırılmış alanlarını gir.
2. Boyut/düzey/puan/ağırlık tanımla.
3. Bir değerlendirmede kullan.
4. Rubriği değiştir.
Beklenen: yeni sürüm oluşur; eski değerlendirme eski rubriğe bağlı kalır; aralık dışı puan kabul edilmez. Gerçek rubrikmiş gibi sunulmaz.

### T18 : Ayrı AI analiz adımları
1. Teslim edilmiş tek öğrenci-vakayı seç.
2. Veri kontrolünden başlayarak adımları sırayla çalıştır.
3. Başlangıç ve nihai rubrik payloadlarını test görünümünde incele.
4. Bir adımı hatalı mock ile çalıştır; yalnız o adımı yeniden dene.
Beklenen: başka öğrenci/vaka karışmaz; bağımsız çağrılarda diğer metin/puan yok; önceki sonuçlar korunur; toplu analiz düğmesi yoktur.

### T19 : Hesaplama ve kanıt
1. Güveni 8 → 10 olan fixtureı aç.
2. Farkın +2 olduğunu doğrula.
3. Rubrik boyut ve ağırlıklarını elle hesaplayıp karşılaştır.
4. Geçersiz JSON/olmayan kanıt referansı mock'unu dene.
Beklenen: uygulama hesapları doğru; AI yanlış toplamı kabul ettiremez; kanıtsız çıktı onaylı puana dönüşmez.

### T20 : Araştırmacı kararları
1. Farklı önerilerde kabul/düzenle/reddet/karar verilemedi uygula.
2. Raporu ve toplamları kontrol et.
3. Analizi yeni sürümle yeniden çalıştır.
Beklenen: ilk AI önerisi korunur; araştırmacı kararı ayrıdır; kararsız boyut tamamlanmış toplam gibi gösterilmez; eski analiz kaybolmaz.

### T21 : Erken yayın engeli
1. Ayrı sentetik testte ilgili vaka açıkken onaylı raporu ayrıca yayımlamayı dene.
2. Vaka kapalı ama rapor sahibine ek süre açıkken tekrar dene.
3. Öğrenci doğrudan rapor verisi/indirme adresine erişmeyi denesin.
Beklenen: ilgili vaka veya kişisel ek süre açıkken erişim yok; sunucu da denetler.

### T22 : Vaka kapanışı, kişisel ek süre, onay ve yayın
1. Ayrı testte yalnız seçili vakanın Veri toplamayı kapat düğmesini kullan; başka vaka açık kalsın.
2. Öğrenci eski kaydı görüntüleyebilmeli; yeni başlangıç/kayıt/AI/teslim engellenmeli.
3. Toplu atama / Ek süre ile yalnız seçili öğrenciye ek süre ver; seçilmeyen kapalı kalmalı. Süreyi iptal et; yeniden salt okunur olmalı.
4. İlgili vaka ve rapor sahibinin ek süresi kapalıyken onaylı raporu ayrıca yayımla. Başka vakaların açık olması engel değildir.
5. Vakayı/kişisel ek süreyi yeniden aç; yayımlanmış rapor gizlenmeli. Kapatınca aynı rapor dönmeli.
Beklenen: vaka bağımsızlığı,kişisel erişim ve ayrı yayın; başlangıç/teslim kilidi ve geçmiş rapor gövdesi değişmez.

### T23 : Word/PDF
1. Tek öğrenci-vaka raporunu iki biçimde indir.
2. Türkçe karakter, uzun metin, tablo, sayfa taşması ve sürüm bilgisini kontrol et.
3. Öğrenci raporunda iç not bulunmadığını doğrula.
4. Bir öğrencinin ayrı raporlarını birleştir.
Beklenen: gerçek DOCX/PDF açılır; aynı rapor sürümü; birleştirme yeni toplu analiz çalıştırmaz.

### T24 : Excel/CSV/JSON
1. Tek öğrenci-vaka ve tüm öğrenciler için indir.
2. Olaylar, ziyaretler, mesajlar, etiketler, taslaklar ve veri sözlüğünü kontrol et.
3. Çoklu etiketin olayları çoğaltmadığını doğrula.
4. Disco CSV indir; kodlanmış öğrenci-vaka kimliği/etkinlik/UTC/zaman kaynağını ve kimlik sızıntısı yokluğunu kontrol et.
5. Yalnız testte formül benzeri metin içeren fixtureı aç.
Beklenen: gerçek xlsx; UTF-8 CSV; güvenli metin; açık saat dilimi; seçili vaka/tüm vakalar kapsamı ve kayıtların test/deneme/analiz dışı durumları; AI ve araştırmacı etiketleri ayrı.

### T25 : Süre çakışması
1. AI üretirken nihai yazma fixtureını incele.
2. Olay çizelgesiyle bölüm ziyaretlerini karşılaştır.
3. AI ve yazma sürelerinin toplama iki kez eklenmediğini kontrol et.
4. Eksik kapanış fixtureını aç.
Beklenen: ayrı kulvar; belirsiz süre açık; okuma/bekleme kesinliği yok.

### T26 : Yetki ve veri sızıntısı
1. Öğrenci A ile öğrenci B'nin uygulama/rapor kimliğine erişmeyi dene.
2. Öğrenci hesabıyla araştırmacı adreslerini dene.
3. Çıkış sonrası rapor indirmeyi dene.
4. Public alandan config/log/backup erişimini kurulum rehberindeki güvenli testle kontrol et.
Beklenen: erişim reddi; veri ve sır sızıntısı yok. Teknik endpoint testlerini Codex otomatik testlerle de desteklemelidir.

### T27 : Arşiv ve geçmiş
1. Katılımcı/vaka/etiket/rubrik/raporun arşiv veya eski sürümünü aç.
2. Analiz dışı işaretle ve gerekçe gir.
3. Filtreli export al.
Beklenen: kalıcı silme yok; geçmiş korunur; tam Excel/JSON test/analiz dışı durumlarını korur; Disco çıktısı kodlanmıştır.

### T28 : Gerçek hosting ve geri yükleme
1. Gerçek test hostinginde HTTPS, alt klasör, API bağlantısı ve akışı kontrol et.
2. AI sürerken autosave testini burada tekrar yap.
3. Yedeği ayrı test veritabanına geri yükle.
4. Uygulama/mesaj/sürüm/rapor bağlantılarını kontrol et.
Beklenen: gerçek ortam kanıtı; streaming desteklenmiyorsa dürüst sınırlılık; geri yükleme üretim verisini ezmez.

## 3. Kullanıcı geri bildirim şablonu

```text
Test ID:
Uygulama sürümü:
Tarayıcı / cihaz:
Mock veya gerçek AI:
Öğrenci no (yalnız sentetik):
Vaka / sürüm:
Adımlar:
Beklenen:
Gerçekleşen:
Tekrar oluyor mu:
Ekran görüntüsü / hata kodu:
Önem / etkisi:
Sonuç: GEÇTİ / KALDI / ÇALIŞTIRILAMADI
```

Gerçek parola, API anahtarı veya gerçek katılımcı yanıtı paylaşma. Dönüt Codex'e verilince hata BUGS.md'ye kaydedilir; kök neden/düzeltme/regresyon eklenir, plan.md ve CONTEXT.md güncellenir. Düzeltme sonrası ilgili test ve ilişkili kilit/yayın/veri koruma testleri tekrar çalıştırılır.

## 4. Son kabul

Araştırmaya başlamadan önce: gerçek rubrik doğrulandı; gerçek AI/hosting test edildi; seed üretimden ayrıldı; varsayılan test hesapları kapalı; yedek geri yükleme denendi; kritik hata yok; yayın kapısı ve veri kilitleri sunucuda doğrulandı; onam/saklama metinleri araştırmacı tarafından sağlandı. Yapılmayan kontroller tamamlandı diye işaretlenmez.

## 0.1.1 hata vurgusu ve teslim düğmesi regresyonu

Eksik profil/başlangıçta kesinleştirmeyi deneyin: onaydan önce ilk eksik alanın bölümüne gidilmeli, alan kırmızı çerçeve/yan açıklamayla odaklanmalı; diğer eksikler kalıcı özet linklerinde ve sekmelerde görünmeli. Değer girince ilgili hata temizlenmeli. 0 geçerli puan.

Başlangıç kesinleştikten sonra nihai karar, gerekçe, güven ve yansıtma tamamlanmadan son teslim kapalı kalmalı. Eksik listesi doğru bölüme götürmeli. Tümü tamam ve sunucuya kayıtta açılmalı; zorunlu alanı silmek anında kapatmalı. AI sürerken veya bağlantı yokken teslim kapalı olmalı. Aynı kilitler sunucuda ayrıca korunur. Başlangıcı kilitli veriyi bu testi tekrarlamak için açmayın; ayrı açılmamış sentetik vaka kullanın.

## 0.1.2 son teslim sekmesi

Ctrl+F5 sonrası gezinmede 7. Son teslim en sonda olmalı. Vaka/Profil/Başlangıç/AI/Nihai/Yansıtma ekranlarının altında teslim kartı bulunmamalı. Son teslim sekmesine gidince eksik alan bağlantıları ve koşullu düğme görünmeli. Eksik bağlantısı doğru bölüme götürmeli; o bölümde teslim kartı yeniden görünmemeli. Doldurup sunucuya kaydettikten sonra yeniden Son teslim'e geçin. Yalnız bütün gereksinimler tamam ve AI boşta iken teslim açılır. Kullanıcı kabulü sırasında istenmedikçe teslim etmeyin. Başlangıç kesinleşmeden son sekmede kilit açıklaması beklenir.

## 0.1.3 eksik kutusunun kapsamı

Ctrl+F5 sonrası AI/Nihai/Yansıtma ve diğer çalışma bölümlerinin üstünde genel Eksik alanları tamamlayın kutusu olmamalı. Eksikler yalnız 7. Son teslim'de tek listede görünmeli. Listedeki bağlantıya basınca doğru alan açılmalı; genel kutu o alana taşınmamalı. Düzenleme sonrası Son teslim'e dönünce eksik listesi güncellenmeli; zorunlu alan/kayıt/AI koşulları aynı kalmalı. Alan içi hata vurgusu ayrı korunur.

## Analiz sırası görünümü —0.1.7
Yeni analizde sekiz adım görünür, yalnız1aktif olmalı. Her adım tamamlanınca kendi düğmesi kapanır ve sıradaki açılır; sonuçlar kendi kartındaki Sonuçlar ve inceleme altında korunur. İstek sürerken diğer başlatmalar kapalıdır. Başarısız adıma Yeniden dene sunulur.7tamamlandıktan sonra8için3–7araştırmacı kararları gerekir; eksik karar numaraları gösterilir. Kararlar tamamlanınca8açılır.8/8sonrası ayrı rapor snapshotı işlemi görünür; onay/yayın ayrıdır. Mevcut4/8analiz yenilemede dört tamamlanmış+5aktif+6–8kilitli olarak kalır.
