# Öğrenci Paneli İşlevsel Şartnamesi

Sürüm: 1.0 : konuşmada kabul edilen kararların birleştirilmiş hali.
Durum: Tasarım belgesi. Uygulama geliştirilmiş veya test edilmiş değildir.
Dil: Türkçe. Ana hedef: masaüstü laboratuvar kullanımı; mobil uyumluluk zorunlu.

## 1. Amaç ve sınırlar

Öğretmen adayının her pedagojik vaka için şu zinciri tamamlaması sağlanır:
Vaka → başlangıç profili → AI öncesi başlangıç değerlendirmesi → AI ile serbest etkileşim ve nihai yanıtın iç içe hazırlanması → yansıtma → tek son teslim.

Sistem yalnızca son ürünleri değil, bölüm geçişlerini, mesajları ve anlamlı metin sürümlerini de korur. Gözlenen davranış ile yorum ayrıdır. Bölüm açılması okuma, kopyalama benimseme, hareketsizlik düşünmeme anlamına gelmez.

## 2. Temel kurallar

- Kalıcı silme yoktur. Kayıtlar arşivlenir, dondurulur veya gerekçeli analiz dışı işaretlenir.
- Başlangıç yanıtı ve profil AI açılmadan önce sunucuda kesinleşir; sonradan değişmez.
- AI sohbetini bitirme aşaması yoktur. Başlangıç kesinleşince son teslime kadar AI kullanılabilir.
- Aynı anda yalnızca bir AI yanıtı üretilebilir. Yanıt sürerken yeni mesaj gönderilemez veya kuyruğa alınamaz.
- Nihai yanıt ve yansıtma birlikte tek teslimle kilitlenir.
- Kilitli içerik okunabilir kalır. Donuk görünüm düşük kontrast anlamına gelmez.
- Veri toplama boyunca puan, kullanıcı tipi, prompt kalitesi ve AI değerlendirmesi öğrenciye gösterilmez.
- Raporlar ancak araştırma genelindeki beş vakanın veri toplaması kapandıktan ve araştırmacı ayrıca yayımladıktan sonra açılır.
- Her vaka bağımsız sohbettir. Başka vaka konuşması otomatik taşınmaz.

## 3. Giriş ve ana sayfa

### 3.1. Giriş
- Öğrenci numarası ve parola ile giriş önerilir; gerçek hesap politikası kurulumda tanımlanır.
- Dondurulmuş hesap giriş yapamaz. Arşivlenmiş/erişimi kapalı hesapların geçmiş verileri korunur.
- Parola açık metin saklanmaz; öğrenciye başka katılımcının verisi gösterilmez.
- Yeniden giriş yeni vaka oturumu veya yeni sohbet oluşturmaz.
- Oturum sona ererse kayıtlı çalışma korunur; aynı öğrenci doğrulanmadan yerel kurtarma metni gösterilmez.

### 3.2. Başlangıç bilgileri
Araştırma bilgilendirmesi, onam durumu ve program, sınıf, önceki GenAI deneyimi, kullanılan araçlar için alanlar bulunur. Onam metni araştırmacı tarafından sağlanır; uygulama hukuki/etik uygunluğu kendiliğinden ilan etmez. Araştırmadan çekilme talebi ayrı süreçtir; veri sessizce silinmez.

### 3.3. Vaka kartları
Öğrenci yalnızca kendisine atanmış ve yayımlanmış vakaları görür. Kart: başlık, sıra, durum, Başla/Devam et/Görüntüle düğmesi. Deneme vakası açıkça araştırma dışı işaretlenir. Devam et en son kaydedilen bölüme döner. Teslim edilmiş vaka salt okunur açılır. Yeni başlangıca kapalı fakat daha önce başlanmış vaka için erişim kuralı sunucudan uygulanır.

## 4. Çalışma ekranı ve gezinme

### 4.1. Son kabul edilen düzen
Tek çalışma sayfası; seçilen bölümün içeriği gösterilir. Tüm içerikler sürekli alt alta açık değildir. Vaka sürekli görünür veya sabit yan panelde değildir. Öğrenci istediğinde Vaka bölümüne gider. Önceki sabit vaka paneli önerisi geçersizdir.

Sabit üst çubuk:
- Vaka adı.
- Çalışma durumu.
- Kayıt durumu.

Sürekli erişilebilir bölüm gezinmesi:
1. Vaka
2. Vaka profili
3. Başlangıç değerlendirmesi
4. AI sohbeti
5. Nihai değerlendirme
6. Yansıtma
7. Son teslim

Bölüm durumları: Henüz açılmadı, Düzenlenebilir, Kesinleştirildi, Tamamlandı. Metin ve simge birlikte kullanılır; yalnızca renk kullanılmaz. Kilitli başlığa erişim, neden kilitli olduğunu açıklar; yetkisiz içeriği açmaz.

### 4.2. Mobil
Masaüstü ana görünüm korunur; dar ekranda bölüm seçici veya erişilebilir açılır gezinme kullanılır. Metin alanları ve mesaj kutusu ekrana sığar. Klavye açılınca gönder/teslim düğmeleri ve kayıt durumu erişilebilir kalır. Dokunma hedefleri ve odak göstergeleri anlaşılır olur. Ekran yönü değişimi taslakları kaybettirmez. Mobilde farklı bir araştırma akışı uygulanmaz.

### 4.3. Gezinme davranışı
- Bölüm değişiminde metin ve kaydırma konumu korunur.
- Başlangıç yanıtı nihai bölümde otomatik gösterilmez; öğrenci özellikle açarsa salt okunur görür.
- Yeni AI yanıtı öğrenciyi başka bölüme zorla götürmez. Yeni AI yanıtı hazır bildirimi gösterilir.
- Vaka, başlangıç ve sohbeti yeniden açma olayları ayrı kaydedilir.
- Klavye ile gezinme, görünür odak ve anlaşılır alan etiketleri desteklenir.

## 5. Vaka profili

Dört soru, 0–10; başlangıçta hiçbir değer seçili değildir:
1. Ön bilgi: Bu konu hakkında ne kadar bilginiz var?
2. Başlangıç güveni: Şu anda bu vakada uygun bir karar verebileceğinize ne kadar güveniyorsunuz?
3. Algılanan zorluk: Bu vaka size ne kadar zor geliyor?
4. Algılanan ciddiyet: Bu vakada yanlış bir karar vermenin sonucu sizce ne kadar ciddi?

Profil tamamlanmadan başlangıç kesinleştirilemez. Başlangıç kesinleşene kadar profil düzenlenebilir ve otomatik kaydedilir. Profil başlangıç yanıtıyla aynı kesinleştirme işleminde kilitlenir. Sonradan görüntülenebilir; değiştirilemez. Puanlar AI sohbetine otomatik gönderilmez.

## 6. Başlangıç değerlendirmesi

Üç ayrı alan:
1. Bu vakadaki temel problemi nasıl değerlendiriyorsunuz?
2. Kararınız nedir?
3. Bu kararı neden veriyorsunuz?

Yardım metni: Cevabınızda kararınızı etkileyen vaka bilgilerini, kişileri, olası riskleri veya sonuçları belirtiniz.

- Otomatik taslak kaydı vardır.
- Kesinleştirmeden önce düzenlenebilir.
- AI alanı görünür fakat kullanılamaz.
- Nihai değerlendirme ve yansıtma başlıkları görünür; içerikleri açılmaz.
- Yapay kelime sayısı veya minimum prompt sayısı dayatılmaz; zorunlu alan kontrolü yapılır.

AI kilit açıklaması:
> Başlangıç değerlendirmenizi kesinleştirdiğinizde AI sohbeti açılacaktır.

Düğme:
> Başlangıç yanıtımı kesinleştir ve AI sohbetini aç

Onay:
> Başlangıç yanıtınız ve vaka profili yanıtlarınız bundan sonra değiştirilemeyecek. Devam etmek istiyor musunuz?

Sunucu işlemi atomik olmalıdır: en güncel taslak sürümü doğrulanır, profil ve üç yanıt kaydedilir, kesinleştirme kaydı oluşturulur, AI erişimi açılır. Sunucu onayı gelmeden AI açılmaz. Ağ hatasında metin korunur; tekrar deneme çift kesinleştirme üretmez. Eski sekme veya doğrudan istek kilidi aşamaz.

Kesinleştirme sonrası açıklama:
> Başlangıç yanıtınız kesinleştirildi. Bu bölüm yalnızca görüntülenebilir.

## 7. AI sohbeti

### 7.1. Bağlam ve araştırma tarafsızlığı
AI'a otomatik sağlanan içerik: öğrencinin gördüğü vaka sürümü ve sabit, sürümlenmiş sohbet yönergesi.
Otomatik sağlanmayanlar: profil, başlangıç yanıtı, nihai taslak, yansıtma, rubrik, puanlar, araştırmacı notları, analiz etiketleri, diğer vaka konuşmaları. Öğrenci kendi metnini isterse kendisi aktarır.

Hazır prompt, karşı görüş iste düğmesi, prompt puanı, yeniden üret ve konuşma dallandırma yoktur. Öğrenci AI önerisini kabul etmeye zorlanmaz. Gerçek kişilere ait tanımlayıcı bilgi paylaşmaması için genel uyarı bulunur; uyarı belirli prompt davranışı öğretmez.

### 7.2. Mesaj kaydı
- Öğrenci ve AI mesajları ayrı kimlik, sıra, rol ve zamanlarla saklanır.
- Gönderilmiş mesaj düzenlenmez veya silinmez.
- Öğrenci mesajı sağlayıcı çağrısından önce güvenli biçimde kaydedilir.
- Yanıt tamamlandı, kısmi veya başarısız olarak ayrılır.
- Model ve yönerge sürümü, istek/deneme kimliği kaydedilir; gizli anahtarlar kaydedilmez.
- Öğrenci mesaj taslağı gönderilmiş mesaj sayılmaz.

### 7.3. Tek yanıt kilidi
Mesaj gönderilince sunucuda vaka-sohbet kapsamlı kilit oluşur. AI yanıtı sürerken gönder düğmesi kapalıdır. Yeni mesaj kuyruğu yoktur. Öğrenci sonraki sorusunu taslak yazabilir; bu taslak AI'a iletilmez. Sayfa yenileme, iki sekme ve yinelenen istek kilidi aşamaz. Yanıt tamamlanınca gönderme açılır. Hata durumunda güvenli yeniden deneme ve işlem durumu görünür olur; aynı öğrenci mesajı tekrar üretilmez.

### 7.4. Yanıt sürerken serbest çalışma
Yanıt mümkünse parça parça gösterilir. Öğrenci bu sırada vakaya, başlangıç yanıtına, nihai değerlendirmeye veya yansıtmaya geçebilir ve açık alanları düzenleyebilir. İlk görünür parça ve tamamlanma ayrı kaydedilir. Yanıt tamamlanınca zorla ekran değişmez.

Paylaşımlı hosting aktarımı tamponlarsa gerçek olmayan akış animasyonu veya sahte ilk parça zamanı üretilmez. Tam yanıtla çalışma alternatifi açıkça belgelenir; bu durumda parça görünürlüğü ölçülemedi olarak işaretlenir. Gerçek akış desteği dağıtım testinde doğrulanmalıdır.

## 8. Nihai değerlendirme ve yansıtma

Nihai alanlar:
1. Nihai kararınız nedir?
2. Nihai kararınızın gerekçesi nedir?
3. Nihai kararınıza ne kadar güveniyorsunuz? 0–10, başlangıçta seçilmemiş.

Yansıtma:
> AI ile etkileşiminiz nihai kararınızı nasıl etkiledi? AI'ın hangi önerisini kabul ettiniz, değiştirdiniz veya reddettiniz? Neden?

Başlangıç kesinleşince ikisi de erişilebilir olur. Nihai yanıt, yansıtma ve AI arasında serbest geçiş vardır. AI etkileşimini bitir düğmesi yoktur. Metinler son teslime kadar düzenlenebilir. Anlamlı ara sürümler korunur. Başlangıç yanıtı nihai bölümde otomatik yan yana gösterilmez.

## 9. Son teslim

Düğme:
> Vakayı tamamla ve tüm yanıtlarımı kesinleştir

Onay, yeni mesaj ve düzenleme yapılamayacağını; tüm sürecin görüntülenebileceğini açıklar.

Kontroller:
- En son taslakların sunucuya ulaştığı doğrulanır.
- Zorunlu alanlar ve nihai güven kontrol edilir.
- Sürüm çakışması varsa sessiz üzerine yazma yapılmaz.
- AI işlemi sürüyorsa teslim engellenir ve nedeni açıklanır.
- AI hata durumundaysa hata kaydı korunur; veri eksikliği gizlenmez.
- Başarılı teslim tek işlemle tüm açık alanları kilitler.
- Tekrarlanan tıklama ikinci teslim oluşturmaz.

Sonrasında tamamlanma zamanı görünür. Vaka, profil, başlangıç, sohbet, nihai yanıt ve yansıtma salt okunur kalır. Rapor yayımlanması bu kilitleri kaldırmaz.

## 10. Kayıt ve kurtarma

### 10.1. Sunucu kaydı
Yazma sırasında kısa aralıklarla, alan terkinde ve bölüm geçişinde kayıt hedeflenir. Aralık yapılandırılır; kabul edilmiş sabit saniye değeri yoktur. Teknik autosave ile anlamlı analitik düzenleme olayları ayrıdır. Değişmeyen metin için gereksiz sürüm oluşturulmaz.

### 10.2. Yerel kurtarma
Sunucuya ulaşmayan değişiklikler için cihazda geçici kurtarma taslağı tutulur. Hesap, vaka ve sürümle eşleştirilir. Aynı öğrenci yeniden doğrulanmadan gösterilmez. Ortak bilgisayarda başka hesaba sızdırılmaz. Sunucu onayı sonrası temizleme ve çıkış politikası belgelenir. Yerel taslak sunucudaki kesinleşmiş metni değiştiremez. Kurtarma sürüm çakışmasında öğrenciye açıklama gösterilir; olay kaydedilir.

### 10.3. Kayıt göstergeleri
- Kaydediliyor
- Sunucuya kaydedildi
- Bağlantı yok, sunucuya gönderilemedi
- Kaydedilmemiş değişiklik var

Kaydedildi yalnızca sunucu onayıyla gösterilir. Ani kesintide henüz kaydedilmemiş son değişikliklerin kaybolabileceği dürüstçe açıklanır; sıfır kayıp garantisi yoktur.

## 11. Süreç verisi ve ölçüm sınırları

Kaydedilecekler: bölüm açma/yeniden açma, alan odağı, metin değişimi, taslak sürümü, mesaj gönderme, yanıt başlangıcı/ilk parça/tamamlanma/hata, kopyalama, uygulama içi yapıştırma gözlemi, kaydırma, sayfa görünürlüğü, pencere odağı, kesinleştirme, teslim ve kurtarma.

Kopyalama: kaynak bölüm, metin sürümü, seçili aralık, uzunluk, zaman ve sıra. Panonun genel içeriği okunmaz; uygulama dışındaki faaliyet izlenmez. Mobil/tarayıcı sınırlamaları veri kalitesi notudur; algılanmayan olay gerçekleşmedi diye kesinlenmez.

Öğrenci etkinliği ve AI üretimi ayrı zaman çizgileridir. AI bekleme süresi ile nihai yazma süresi toplanarak toplam aktif süre üretilmez. Etkileşimsiz süre okuma/bekleme/düşünme olarak kesin sınıflandırılmaz. Eksik kapanış olaylarında süre belirsiz işaretlenir.

## 12. Rapor ve puan erişimi

Öğrenci veri toplama sırasında puan veya rapor görmez. Araştırma genelindeki beş vakanın veri toplaması araştırmacı tarafından kapatıldıktan ve rapor ayrıca onaylanıp yayımlandıktan sonra:
- Gerekçeli karar.
- Boyut bazında puanlar.
- Toplam puan.
- Yayımlanan rapor ve Word/PDF indirme.

gösterilir. AI taslakları, iç notlar, etiketler ve onay geçmişi gösterilmez. Gerçeğe aykırı yazarlık ifadesi eklenmez. Erken bitiren öğrenciye erken yayın yoktur.

## 13. Kabul ölçütleri

- [ ] Atanmamış veya aktif olmayan vaka başlatılamıyor.
- [ ] Vaka sürekli açık değil; gezinmeyle açılıyor ve olay oluşuyor.
- [ ] Profil tamamlanmadan başlangıç kesinleşmiyor.
- [ ] Başlangıç kesinleşmeden AI'a doğrudan istek de gönderilemiyor.
- [ ] Kesinleşmiş profil/başlangıç API üzerinden değiştirilemiyor.
- [ ] AI ve nihai/yansıtma arasında geçiş veri kaybettirmiyor.
- [ ] Yanıt sürerken ikinci mesaj ve teslim sunucuda engelleniyor.
- [ ] Soru taslağı gönderilmiş mesaj olarak kaydedilmiyor.
- [ ] Ara taslaklar AI mesajlarıyla kronolojik ilişkilendirilebiliyor.
- [ ] Kesinti/kurtarma başka öğrencinin metnini göstermiyor.
- [ ] Teslim sonrası tüm düzenleme istekleri reddediliyor.
- [ ] Rapor yayın kapısı hem veri kapanışını hem araştırmacı yayınını gerektiriyor.
- [ ] Mobilde temel akış ve klavye kullanımı tamamlanabiliyor.
- [ ] Kopyalama ve bölüm açma okuma/benimseme diye sunulmuyor.

## 2026-10-20 kullanıcı testinde kabul edilen düzen güncellemesi — 0.1.2

Son teslim kartı her bölümün altında tekrarlanmaz. Gezinmenin en sonunda ayrı **7. Son teslim** sekmesi vardır. Nihai yanıt/yansıtma tek teslimi, eksik alanlara bağlantılar, kayıt/AI kilitleri ve onay bu sekmededir. Başlangıç kesinleşmeden kilit açıklaması gösterilir; sonrasında eksikler görülebilir fakat zorunlu alanlar tamamlanıp sunucuya kaydedilmeden teslim düğmesi açılmaz. Teslim sonrası tamamlanma zamanı ve varsa yayımlanmış rapor bağlantıları bu sekmede gösterilir; diğer bölümler salt okunur incelenebilir. Sekme olay kodu confirm'dir.

### 0.1.3 kullanıcı arayüzü netleştirmesi
Genel Eksik alanları tamamlayın listesi yalnız Son teslim sekmesinde bulunur. Diğer çalışma bölümlerinin üstünde bu özet gösterilmez. Son teslimdeki bağlantı ilgili alana götürür; genel kutu hedef bölüme taşınmaz. Eksik alanın kendi yan açıklaması/odak vurgusu ve teslim engelleri korunur.
