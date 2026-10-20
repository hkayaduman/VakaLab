# Manuel Test Rehberi ve Geri Bildirim Şablonu

Durum: Bu dosya geliştirme öncesi hazırlanmış **tarihsel kabul taslağıdır**; eski matris/global kapanış gibi hükümler güncel ürünü tanımlamaz. Güncel ekran/işlem rehberi MANUAL_TESTS.md, gerçek kullanıcı kanıtı MANUAL_TEST_RESULTS.md, otomatik sonuçlar TEST_RESULTS.md ve güncel devam CONTEXT.md içindedir. Bu belge üzerinden yeni manuel batch başlatılmaz; tarihsel senaryoları uygulamadan önce güncel vaka bazlı kurallarla karşılaştırın.

## 1. Teste hazırlık

1. Codex'in üreteceği INSTALL_SHARED_HOSTING.md ile ayrı test ortamını kur.
2. Üretim verisi olmayan ayrı test veritabanı kullan.
3. TEST_DATA.md içindeki seed'i çalıştır; bütün verilerin SENTETİK/TEST olarak işaretlendiğini doğrula.
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
5. Araştırmacı matrisinde yeni sütunu kontrol et.
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

### T15 : Matris ve teknik durum
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
1. Veri toplama açıkken onaylı raporu yayımlamayı dene.
2. Kendi beş vakasını bitirmiş öğrenciyle rapora erişmeyi dene.
3. Doğrudan rapor indirme adresini test et.
Beklenen: global kapanış olmadan erişim yok; yalnızca arayüzde gizlemekle yetinilmez.

### T22 : Global kapanış, onay ve yayın
1. Ayrı test senaryosunda beş vaka veri toplamasını gerekçeli kapat.
2. Henüz onaylanmamış raporu yayımlamayı dene.
3. Raporu onayla; otomatik yayımlanmadığını doğrula.
4. Ayrıca yayımla ve öğrenciyle aç.
Beklenen: üç koşul gerekir; öğrenci onaylı karar/puan/rapor görür, iç not ve AI taslağı görmez. Yanıt kilitleri korunur.

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
4. Kodlanmış çıktı indir; öğrenci numarası/ad/eşleme sızıntısı kontrol et.
5. Yalnız testte formül benzeri metin içeren fixtureı aç.
Beklenen: gerçek xlsx; UTF-8 CSV; güvenli metin; açık saat dilimi; test/analiz dışı filtreleri; AI ve araştırmacı etiketleri ayrı.

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
Beklenen: kalıcı silme yok; geçmiş korunur; varsayılan araştırma exportu analiz dışı/test verisini karıştırmaz.

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
