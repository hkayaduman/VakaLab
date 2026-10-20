# Araştırmacı Paneli İşlevsel Şartnamesi

Güncel karar 0.2.0: Her araştırmacının ayrı çalışma alanı vardır; ortak araştırmacı/vaka üyeliği yoktur. Kullanıcılar → + Kullanıcı ekle ile yeni araştırmacı oluşturulabilir; yeni hesap boş alanla başlar. Tüm ekran ve API işlemleri sahiplik kapsamındadır. Atama öğrenci checkboxları/Tümünü seç ile atomik toplu yapılır. Veri indirme Tüm vakalar veya seçili vaka, Excel/JSON ve Disco CSV düğmeleriyle sunulur. Veri toplama vakanın kendi kartından açılır/kapanır.

Sürüm: 1.0. Durum: Kabul edilmiş tasarımın geliştirme girdisi; çalışan uygulama değildir.
İlgili belge: [Öğrenci paneli](01_OGRENCI_PANELI.md).

## 1. Amaç

Üç alan ayrıdır: uygulamayı yönetmek, ham süreci incelemek, AI destekli ön değerlendirmeyi araştırmacı denetiminde yürütmek. AI önerisi, ham veri ve araştırmacı kararı ayrı kayıtlar olarak korunur. AI sonucu nihai araştırma bulgusu veya otomatik notlandırma kararı sayılmaz.

## 2. Ana menü

Genel durum; Katılımcılar; Vakalar ve atamalar; Öğrenci-vaka süreçleri; Rubrikler; Etiketler; AI ön değerlendirmeleri; Raporlar ve yayımlama; Process mining ve veri indirme; Sistem ayarları ve işlem geçmişi.

Masaüstü öncelikli, mobil uyumlu. Öğrenci listeleri seçilen vakaya göre daraltılır; yatay matris kullanılmaz. Metin ve simgeler renkleri tamamlar.

## 3. Veri koruma

- Kalıcı silme düğmesi, API'si veya rutin temizleme görevi yoktur.
- Dondurma, arşivleme, atama kaldırma ve analiz dışı işaretleme vardır.
- Geçmiş vaka, rubrik, etiket, rapor, analiz ve yanıt sürümleri korunur.
- Hatalı kayıt gerekçeli geçersiz/analiz dışı işaretlenir.
- Kilitli öğrenci verisine araştırmacı da sessiz müdahale edemez.
- Teknik müdahale ek kayıt ve gerekçe oluşturur; geçmişi yeniden yazmaz.
- Bu ürün kuralı sınırsız hukuki saklama izni veya kayıpsızlık garantisi değildir. Saklama/onam/çekilme koşulları kurum tarafından ayrıca belirlenir; otomatik hukuki uygunluk iddiası yapılmaz.
- Yedekler, geri yükleme denemeleri ve veri bütünlüğü kontrolleri gereklidir.

## 4. Katılımcı yönetimi

### 4.1. Alanlar
Hesap türü (Öğrenci/Araştırmacı), öğrenci numarası/kullanıcı adı, ad soyad, parola ve hesap durumu. Program, sınıf ve özel not isteğe bağlı Ek bilgiler içindedir. Onam/GenAI deneyimi/araçlar/çekilme alanları kaldırılmıştır.

Öğrenci numarası görünür katılımcı kimliğidir. Değişmeyen teknik kimlik ayrı tutulur. Öğrenci numarası anonim değildir; kodlanmış dışa aktarma ayrıca desteklenir.

### 4.2. İşlemler
Ekle, düzenle, parola/giriş yenile, dondur, yeniden etkinleştir, arşivle, arşivden çıkar, vakaya ata, atamayı kaldır, analiz dışı işaretle. Toplu katılımcı içe aktarma henüz kabul edilmiş zorunlu özellik değildir; eklenirse ayrıca belgelenir.

Dondurma girişi engeller. Arşivleme aktif listeden kaldırır. Atama kaldırma vaka erişimini kapatır; oluşmuş veriyi silmez. İşlem zamanı, kişi ve değişiklik otomatik saklanır. Rutin kullanıcı/vaka/tanım/atama işlemleri gerekçe istemez; değerlendirme kararları, mesaj etiket incelemesi, iç not, analiz dışı işareti ve teknik müdahale gerekçeli kalır.

## 5. Vaka yönetimi

Alanlar: başlık, metin, sıra, öğrenci yönergeleri, deneme/araştırma ayrımı, atamalar, rubrik bağlantısı, durum, sürüm, araştırmacıya özel notlar.

Akış: Taslak → Öğrenci önizlemesi → Atama → Aktif hale getirme.

Öğrenci yalnızca atanmış ve aktif vakayı görür. Kullanılmamış taslak düzenlenebilir. Bir katılımcı başladıktan sonra kullanılan metin/yönerge korunur; değişiklik yeni sürüm olur. Devam eden uygulama gördüğü sürümde kalır. Her uygulamanın vaka ve yönerge sürümü izlenir.

Kapatma işlemleri ayrıdır:
- Yeni başlangıçları kapat: başlamış katılımcılar devam edebilir.
- Vaka görüntüleme erişimini de durdur seçeneği vardır; veri toplama kapanışından ayrıdır.
- Arşivle: veri korunur.

Araştırmacı notları öğrenci AI'ına otomatik gönderilmez. Vaka sayısı yapılandırılabilir; bu çalışma beş araştırma vakası ve isteğe bağlı deneme vakasıyla planlanır. Vaka odaklı Excel/JSON indirmesi test, deneme ve analiz dışı dahil seçilen kapsamdaki tüm kayıtları durumlarıyla içerir. Disco CSV teknik gürültüyü dışlayan ayrı kodlanmış süreç günlüğüdür.

## 6. Genel durum: vaka ve öğrenci seçimi

Geniş öğrenci × vaka matrisi kaldırılmıştır. Araştırmacı önce Vaka seçer, Öğrenci ara ile numara/ad arar ve Çalışma durumu ile daraltır. Yalnız seçilen vakaya atanmış öğrenciler ve varsa geçmiş uygulamalar satır halinde görünür. Satırda durum, kişisel ek süre, hesap durumu, son etkinlik, analiz/rapor özeti ve Çalışmayı aç vardır. Başlamamış öğrenci için yanıltıcı açma düğmesi yoktur. Dosyadan dönüldüğünde vaka/arama/durum seçimi korunur. Kaldırılan atama geçmiş uygulamada ayrıca belirtilir.

Vaka kartında Atanan öğrencileri göster (sayı) düğmesi aktif atamaları, hesap/çalışma/ek süre durumlarını açar. Bu vakanın atamalarını yönet ilgili vakayı önseçerek toplu atama formuna götürür. Son etkinlik canlı varlık/okuma/düşünme kanıtı değildir.

## 7. Öğrenci-vaka dosyası

### 7.1. Özet
Öğrenci numarası, teknik kimlik, vaka/sürüm, başlangıç, başlangıç kesinleştirme, teslim, son etkinlik, eksik veri, teknik kesinti, model/yönerge sürümü, analiz ve yayın durumu.

### 7.2. Ham veri sırası
1. Görülen vaka sürümü.
2. Başlangıç profili.
3. Başlangıç değerlendirmesi.
4. Tam AI konuşması.
5. Nihai değerlendirme ve taslak sürümleri.
6. Yansıtma.

Sekmeler: özet, ham veriler, taslaklar, olay çizelgesi, süreler, mesaj etiketleri, AI ön değerlendirmesi, araştırmacı kararları, rapor geçmişi. Devam eden taslaklar kesinleşmiş veri gibi sunulmaz. Araştırmacı notu ayrı alandadır.

## 8. Öğrenci akışını denetleme

Profil ve başlangıç kesinleşmeden AI kapalıdır. Kesinleşince profil/başlangıç kilitli, AI/nihai/yansıtma açıktır. AI bitirme aşaması yoktur. Öğrenci istediğinde vakaya veya başlangıca döner.

AI üretirken yeni mesaj veya mesaj kuyruğu yoktur. Sonraki soru taslak yazılabilir. Aynı anda nihai/yansıtma düzenlenebilir. Yanıt parça parça görünür olabilir; ilk parça ve tamamlanma ayrıdır. Sunucu kilidi sekme/yenilemeyle aşılamaz. Son teslimde AI işlemi sürmemeli, son taslaklar doğrulanmalıdır. Teslim tüm alanları salt okunur yapar.

Araştırmacı bunların olaylarını ve başarısız denemelerini görebilir; kilitli veriyi yeniden yazamaz.

## 9. Olay günlüğü

### 9.1. Etkinlik adları
- Vaka açıldı / yeniden açıldı / vakadan metin kopyalandı.
- Vaka profili açıldı / alanına odaklanıldı / profil değiştirildi / kaydedildi.
- Başlangıç değerlendirmesi açıldı / alanına odaklanıldı / metni değiştirildi / taslağı kaydedildi / metin kopyalandı / kesinleştirildi.
- AI sohbeti açıldı / soru taslağı değiştirildi / mesaj gönderildi / yanıt başladı / ilk parça görüntülenebilir oldu / yanıt tamamlandı / yanıt başarısız oldu / yanıtından kopyalandı / sohbet kaydırıldı.
- Nihai değerlendirme açıldı / alanına odaklanıldı / metni değiştirildi / taslağı kaydedildi / metin kopyalandı / güven değiştirildi.
- Yansıtma açıldı / değiştirildi / kaydedildi.
- Tamamlama istendi / vaka teslim edildi.
- Bağlantı kesintisi algılandı / yeniden bağlandı / taslak kurtarıldı / sayfa görünürlüğü değişti / pencere odağı değişti.
- Uygulama içinde gözlenen yapıştırma ayrı olaydır.

### 9.2. Ortak alanlar
Olay kimliği, öğrenci-vaka uygulama kimliği, öğrenci teknik kimliği, vaka/sürüm, oturum/sekme kimliği, istemci sıra numarası, sunucu sıra/zamanı, gerekiyorsa istemci zamanı, olay türü, kaynak/hedef bölüm, alan, mesaj/taslak bağlantısı, başarı/hata ve ölçüm kalitesi.

Çevrimdışı gelen olaylarda istemci zamanı tek başına kesin kronoloji sayılmaz. Kaynak ve sunucu alınma zamanı korunur; belirsizlik gösterilir. Tekrarlanan gönderim olay çoğaltmaz.

### 9.3. Kopyalama
Kaynak bölüm, metin sürümü, seçili aralık, uzunluk ve zaman tutulur. Uygulama dışı pano takibi yapılmaz. Kopyalama AI'a aktarma veya benimseme sayılmaz. Tarayıcı nedeniyle gözlenemeyen olaylar gerçekleşmedi diye kesinlenmez.

## 10. Taslak sürümleri

Başlangıç, nihai ve yansıtmanın anlamlı ara sürümleri korunur. Teknik autosave kayıtları analitik düzenleme bölümlerinden ayrılır. Her sürüm alan, önceki sürüm, zaman, içerik ve olay bağlantısı taşır. Böylece nihai taslak → AI sorusu → AI yanıtı → yeni taslak zinciri incelenir. Değişmeyen metin için gereksiz yeni sürüm üretilmez. Ham sürümler AI analizine karışık sırayla verilmez.

## 11. Süre ve eşzamanlılık

Ölçüler:
- Bölümde geçen süre.
- Sayfa görünür ve odaktayken geçen süre.
- Yapılandırılmış hareketsizlik kuralıyla etkileşimli süre.
- AI yanıt süresi.
- AI üretirken başka bölümde geçirilen süre.
- İlk açılış–teslim takvim aralığı.
- Eksik/belirsiz ölçüm durumu.

Öğrenci etkinliği ve AI üretimi ayrı kulvarlardır. AI yanıt üretirken öğrenci sohbeti görüntüleyebilir, kaydırabilir, nihai metin yazabilir, başka bölüme geçebilir veya görünür etkileşim yapmayabilir. AI üretim süresi ile öğrenci yazma süresi toplanarak toplam aktif süre oluşturulmaz.

Kullanılacak ifadeler: sohbet ekranında etkileşimsiz süre; yanıt görüntüleme/kaydırma etkinliği; eşzamanlı nihai yanıt düzenleme; sayfa görünür değil; pencere odakta değil.

Okudu, düşündü, bekledi, başka iş yaptı gibi içsel durumlar kesinlenmez. Yanıtın tamamlanması görüldüğü anlamına gelmez; görünürlük de okuma kanıtı değildir. Kapanış alınamadığında süre uydurulmaz. Hareketsizlik eşiği, heartbeat ve süre kuralları sürümlenir. Akış desteklenmiyorsa ilk parça ölçümü yoktur; veri kalitesi notu düşülür.

## 12. Etiket yönetimi

Başlangıç etiketleri: Bilgi istedi; Açıklama istedi; Metin düzenletmek istedi; Eleştiri istedi; Alternatif fikir istedi; Karar istedi; Diğer; Belirsiz.

Bir mesaj birden fazla etiket alabilir. Karar istedi, karar devriyle eşit değildir.

Araştırmacı: kabul eder, değiştirir, ekler, yeni etiket tanımlar, ad/açıklama düzenler ve Sil ile yeni seçimlerden kaldırır. Aynı işlem rubrikler için de vardır; kullanılmış eski tanımlar geçmiş kayıtlarda korunur. Eski tanım ve atamalar korunur. AI ilk önerisi, araştırmacı güncel kararı, inceleme durumu, kişi/zaman/gerekçe ve etiket sürümü ayrı saklanır.

Etiketler öğrenciye görünmez ve sohbet AI'ına geri beslenmez. Etiketleme hatası veri toplamayı engellemez. İşleme zamanı uygulamada belirlenir; öğrenciye yanıt verme yolundan bağımsızdır. Process mining çıktısında AI ve araştırmacı etiketleri ayrı alanlar/tablolardır.

## 13. Rubrik yönetimi

Rubrik adı ve genel değerlendirme yönergesi, boyut adı/min/max/ağırlık ve her düzeyin adı/puanı/açıklaması alanları vardır. JSON girmek gerekmez; sistem üretir. Boyut/düzey eklenip kaldırılabilir; mevcut kimlikler ve eski sürüm korunur. Araştırmacı rubrik ekler, düzenler, vakaya bağlar ve sürümler. Yapıştırılmış rubrik otomatik ayrıştırılırsa araştırmacı doğrulamadan etkinleşmez.

Alanlar: ad, açıklama, boyutlar, düzey tanımları, puanlar, ağırlıklar, toplam hesaplama kuralı, sürüm. Gerçek rubrik henüz verilmemiştir. Test rubriği sentetiktir ve araştırma rubriği diye sunulmaz.

Her boyut çıktısı: önerilen puan, düzey, rubrik bağlantısı, kanıt pasajı, gerekçe, yetersiz kanıt notu. AI düzey/puan önerir; toplam ve ağırlıklı hesabı uygulama yapar. Aralık dışı veya eksik AI çıktısı onaylı puana dönüşmez.

Kullanılmış rubrik değişmez; yeni sürüm oluşur. Eski değerlendirme korunur. Başlangıç ve nihai metne aynı rubriğin bağımsız uygulanması mevcut tasarım varsayımıdır; gerçek rubrik geldiğinde doğrulanmalıdır. Farklı vakalar otomatik tek öğrenci toplamına dönüştürülmez.

## 14. AI ön değerlendirme: kapsam ve sıra

Bir işlem bir öğrenci-vakadır. Tüm öğrencileri veya öğrencinin tüm vakalarını analiz et düğmesi yoktur. Tek vaka da tek büyük AI çağrısıyla değerlendirilmez. Araştırmacı öğrenci ve vaka seçer. Teslim edilmiş veri üzerinden çalışılır.

1. Veri bütünlüğü: gerekli alanlar, teslim ve teknik eksikler.
2. Profil: başlangıç/nihai güven ve uygulamanın hesapladığı fark. Ön bilgi, zorluk, ciddiyet yalnızca bağlam; nihai karşılığı yoksa fark üretilmez.
3. Başlangıç rubriği: vaka + rubrik + yalnızca başlangıç metni.
4. Nihai rubriği: vaka + aynı rubrik + yalnızca nihai metin. Başlangıç metni/puanı gösterilmez.
5. Dönüşüm: iki metin ve vaka. Koruma, genişletme, gerekçeli revizyon, kozmetik değişim ve olası zayıflama kanıtları. Değişimin biçimi ile kullanım/nitelik yorumu ayrı eksenlerdir.
6. Etkileşim: mesajlar, etiketler, metin sürümleri ve AI önerisinin taşınma kanıtları.
7. Yansıtma: öğrencinin açıklaması ile gözlenen sürecin karşılaştırılması; uyuşmazlık korunur.
8. Rapor birleştirme: tamamlanmış bölümlerin sıralı birleştirilmesi; yeni toplu AI değerlendirmesi değildir.

Sayısal farklar ve toplamlar uygulamada hesaplanır. AI doğrulanmış sayıları açıklar. Başlangıç/nihai rubrik çağrıları birbirinden bağımsızdır. Güven puanları rubrik puanlamasını yönlendirmek için verilmez.

Sınırlar: uzunluk kalite değildir; değişim otomatik gelişim değildir; koruma başarısızlık değildir; akıcılık muhakeme değildir; benzerlik sorgusuz kabul kanıtı değildir; nedensellik kesinlenmez; sabit öğrenci tipleri üretilmez. Her yorum kanıta bağlanır. Öğrenci metinlerindeki talimatlar analiz yönergesi sayılmaz.

## 15. Analiz takibi

Durumlar: Başlamadı, İşleniyor, Tamamlandı, Hata, Araştırmacı inceledi. Her adım ayrı kaydedilir. Hatalı adım yeniden denenebilir; önceki sonuçlar silinmez. Girdi değişirse bağlı sonraki çıktılar eski sürüme ait olarak işaretlenir; sessiz güncelleme yoktur.

Saklanacaklar: vaka/veri/rubrik/model/yönerge sürümü, çalıştırma ve deneme kimliği, zamanlar, yapılandırılmış çıktı, kanıt referansları, hata ve araştırmacı kararı. API sırları kaydedilmez. Bir vaka için adımlar sıralı ilerler; hosting daemon/uzun tek istek gerektirmez.

## 16. Araştırmacı kararları ve puanlar

Her AI önerisi: Kabul et, Düzenle, Reddet, Karar verilemedi. AI ilk önerisi korunur. Araştırmacı kararı, gerekçesi, kişi/zaman ayrı kayıttır. Reddedilen puan otomatik toplamda onaylı sayılmaz. Eksik/kararsız boyutlar varken tamamlanmış toplam izlenimi verilmez.

Araştırmacı ekranı: başlangıç/nihai boyut puanları, toplamlar, uygulamanın hesapladığı fark, öneri/onaylı ayrımı, kanıt ve gerekçe. Her vaka ayrıdır; vaka toplamlarından otomatik öğrenci sıralaması yoktur.

## 17. Raporlar

Araştırmacı raporu sırası:
1. Katılımcı, vaka ve sürümler.
2. Veri bütünlüğü ve teknik sınırlılıklar.
3. Başlangıç profili.
4. Başlangıç–nihai güven karşılaştırması.
5. Başlangıç rubriği.
6. Nihai rubriği.
7. Dönüşüm karşılaştırması.
8. Etkileşim ve etiketler.
9. Yansıtma karşılaştırması.
10. Araştırmacı kararları/notları.

İç görünüm AI taslaklarını, inceleme geçmişini, teknik notları ve üretim kaynağını içerir. Öğrenci görünümü yalnızca onaylı gerekçeli karar, boyut puanları, toplam ve yayımlanmış rapordur. Gerçeğe aykırı yazarlık beyanı eklenmez.

Word ve PDF aynı kayıtlı rapor sürümünden üretilir. Her öğrenci-vaka ayrı rapor alır. Bir öğrencinin ayrı raporları tek belgede sıralı birleştirilebilir; yeniden toplu analiz yapılmaz. Türkçe karakterler, uzun tablolar ve sayfa taşmaları test edilir. İç raporda AI destekli ön değerlendirme ve araştırmacı incelemesi ayrımı açıktır.

## 18. Yayın kapısı

Durumlar: AI taslağı → Araştırmacı incelemesinde → Onaylandı → Öğrenciye yayımlandı.

Onay ve yayın ayrıdır. Yayın için ilgili vakada veri toplama kapalı ve rapor sahibinin kişisel ek süresi kapalı olmalıdır. Her vaka bağımsız kapanır/açılır; beş vaka veya global kapanış koşulu yoktur. Araştırmacı kapalı vakada seçili öğrencilere ek süre verebilir ve kaldırabilir. Kapanış otomatik yayın değildir. Vaka veya kişisel ek süre yeniden açıldığında önceki yayımlanmış rapor erişimi geçici gizlenir; kapanınca aynı sürüm döner. Yalnızca onaylı rapor ayrıca yayımlanır. Yayın sürümü ve zamanı korunur. Sonradan düzeltme yeni sürümdür. Kilitli yanıtlar açılmaz.

## 19. Process mining ve dışa aktarma

Süreç örneği öğrenci × vaka uygulamasıdır. Kapsam: tek öğrenci-vaka, bir öğrencinin tüm vakaları, seçilenler veya herkes. Toplu indirme vardır; toplu AI analizi yoktur.

Biçimler: Excel (.xlsx), CSV; tam yapısal arşiv ve sohbet için JSON. Excel gerçek xlsx olmalı, HTML dosyasına xls adı verilmemelidir.

Tablolar/sayfalar:
- Olaylar: her satır bir olay.
- Bölüm ziyaretleri: giriş, çıkış, süre, kalite.
- Mesajlar: rol, sıra, içerik, durum, bağlantılar.
- Mesaj etiketleri: her satır mesaj–etiket eşleşmesi; AI/araştırmacı kaynağı ayrıdır.
- Taslak sürümleri: içerik ve olay bağlantıları.
- Profil ve yanıtlar.
- Değerlendirmeler: öneri/onay ayrımı.
- Veri sözlüğü: alanlar, saat dilimi, olay ve süre kuralları.

Seçenekler: kimlikli/kodlanmış, öğrenci olayları/AI işlemleri birlikte, ham olay/hesaplanmış ziyaretler, AI/araştırmacı etiketleri, analiz dışı/deneme kayıtları. Varsayılan araştırma çıktısı deneme verisini dışlar. Çoklu etiketler tek olay satırını çoğaltarak süreç sırasını bozmaz; ayrı ilişki tablosu sağlanır. CSV/Excel formül enjeksiyonuna karşı kullanıcı metni güvenli dışa aktarılır. Kodlanmış dosya kimlik eşlemesini içermez.

## 20. Teknik sorunlar ve müdahaleler

Başarısız kayıt, bağlantı kesintisi, kısmi AI yanıtı, eksik teslim, yerel kurtarma, ikinci sekme, tekrar istek ve eksik süre kapanışı görünürdür. Sayfa yenileme yeni sohbet üretmez. Başka modele sessiz geçiş yoktur. Teknik düzeltme eski kaydı yeniden yazmaz. Kilitlenmiş başlangıcı açıp düzenletme normal bir yönetim işlemi değildir; olağanüstü uygulama geçersiz sayılıp yeni uygulama gerekirse ayrı kayıt ve gerekçe gerekir.

## 21. Sistem ayarları ve denetim

Öğrenci AI'ı ve analiz AI'ı ayrı ayarlardır. Model, yönerge sürümleri, vaka bağlamı, analiz adımları, rubrik ve etiketler sürümlenir. API anahtarları tarayıcıya/rapora/loga sızmaz. Yetki her sunucu isteğinde kontrol edilir.

İşlem geçmişi ham JSON yerine Türkçe işlem adı/hesap/vaka/öğrenci/zaman/gerekçe satırlarıdır; aranabilir ve20şerDaha fazla göster ile ilerler. Son100yetkili kayıt JSON olarak ayrıca indirilebilir.

Denetim günlüğü: katılımcı işlemleri, vaka/sürüm, atama, rubrik/etiket, analiz başlatma/tekrar, öneri kararı, rapor onay/yayın, veri kapanışı, dışa aktarma, teknik müdahale. Kişi, zaman, kayıt ve değişiklik otomatik saklanır; rutin işlemlerde elle gerekçe zorunlu değildir.

Yedekleme ve geri yükleme testleri; son başarılı kayıt ve kurtarma olayları incelenebilir. Uygulama paylaşımlı hosting için daemon, Redis veya kalıcı worker zorunluluğu olmadan tasarlanır. Teknik ayarların gerçek hostingde doğrulanması gerekir.

## 22. Açık kararlar

Gerçek rubrik; AI sağlayıcısı/modeli; hosting sürüm/limitleri; autosave/heartbeat/hareketsizlik eşikleri; etiketleme tetikleme zamanı; yedek saklama düzeni; rapor öğrenci görünümünün son biçimi. Bu alanlar geliştirme varsayımıyla gizlice kesinleştirilmez.

## 23. Kabul ölçütleri

- [ ] Kalıcı silme yok; arşivleme geçmişi koruyor.
- [ ] Öğrenci numarası değişimi geçmiş ilişkileri bozmuyor.
- [ ] Vaka eklenince vaka seçim listesi güncelleniyor; atama ve aktivasyon birlikte uygulanıyor.
- [ ] Kullanılmış vaka/rubrik sürümü değişmiyor.
- [ ] Ham veri, AI önerisi ve araştırmacı kararı ayrı.
- [ ] Eşzamanlı AI/öğrenci süreleri çift sayılmıyor.
- [ ] Etiket değişikliği AI önerisini silmiyor; yeni etiket eklenebiliyor.
- [ ] Analiz bir öğrenci-vaka ve ayrı adımlarla çalışıyor.
- [ ] Bağımsız rubrik çağrılarında diğer metin/puan yok.
- [ ] Toplam ve güven farkını uygulama hesaplıyor.
- [ ] Yayın kapısı sunucuda kapanış + onay + yayın koşullarını denetliyor.
- [ ] Word/PDF ve xlsx/CSV gerçek dosyalar; Türkçe ve güvenlik testleri geçiyor.
- [ ] Toplu dışa aktarma var; toplu AI analizi yok.

## 0.2.3 — Güncel öğrenci raporu

Öğrenci 7.Son teslim → Yayımlanan raporlar → Raporu görüntüle ile başlangıç/nihai özgün yanıtını, her ölçütün puanı ve geri bildirimini, nihai toplam ve genel geri bildirimini görür; Word/PDF aynı sade içeriktir. Nihai puan × rubrik ağırlıkları toplanır; başlangıç puanı nihai toplama eklenmez. Reddedilmiş/belirsiz puan sıfır değildir. Öğrenci raporunda AI/teknik inceleme/iç notlar gösterilmez; araştırmacı iç kaydı korunur. Araştırmacı Öğrenci raporu hazırla (yeni sürüm) → Öğrenci raporunu önizle → Raporu onayla → Ayrıca öğrenciye yayımla yolunu kullanır. Önizlemede Word indir/PDF indir öğrenci biçimidir; İç inceleme Word/PDF araştırmacıya özeldir. Eski sürümler değiştirilmez; eski raporda yeni feedback yoksa yeni sürüm hazırlanır. Yayın için vaka kapanışı ve kişisel ek sürenin kapalı olması zorunludur. Gerçek AI/kurum rubriği kabulü henüz tamamlanmadı.

## 0.2.4 — Güncel öğrenci-vaka inceleme ekranı

Öğrenci yanıtları ve çalışma süreci, JSON yerine okunabilir alanlar ve listeler sunar. Yanıtların değişiklik geçmişi, otomatik kaydedilmiş yanıtları Önce/Sonra karşılaştırır; bunlar gönderilmiş sohbet mesajları değildir. İşlem çizelgesi arama/bölüm/periyodik durum filtresiyle incelenir. Çalışma süreleri ve eksik ölçümler gözlem sürelerini gösterir; okuma/düşünme çıkarımı yapılmaz. AI yanıtları ve bağlantı sorunları her sohbet isteğinin sonucunu, bekleme süresini ve denemelerini açıklar. Ayrıntılı analiz verisi Ham JSON indir ile alınır; ekranda JSON girişi/gösterimi gerekmez.

AI konuşması ve mesaj etiketleri → AI ile mesajları etiketle otomatik önerileri hazırlar. Analizin 6. adımı da etiket önerisi üretir. Aynı konuşma ve tanımlarda önceki yeni öneri tekrar kullanılır. Etiketleri incele / Değiştir ile araştırmacı değiştirip kaydeder; ilk incelemede AI önerisi, sonraki incelemelerde araştırmacının güncel seçimi işaretlidir. Araştırmacının tüm etiketleri kaldırması da geçerli karardır. Öneri ve insan kararı ayrı saklanır. Disco CSV'de AI ve araştırmacı etiketleri ile güncel kullanılacak seçim ayrı sütunlardadır; inceleme durumu filtrelenebilir.

## 0.2.5 — Güncel ziyaret ve Disco süre tanımı

Bölüm geçişleri ve ziyaret süreleri tüm yedi sekmeyi kapsar. Giriş sonraki sekme/çıkış/başarılı kesinleştirmeye kadar sürer; yeniden ziyaret ayrı satırdır. Vaka 10:00 → Profil 10:02, vaka ziyaretinde 120 saniyedir. Son teslimin başarıyla kaydından sonrası takip edilmez. Geçen süre, görünür/odak ölçümünden ayrıdır; arka plan bekleyişini içerebilir. Açık uç tahmin edilmez.

Disco: geçişler ve süreler kapanan ziyaretlerin başlangıç/bitiş ve Duration Seconds alanlarını, sıra ve önceki/sonraki etkinliği içerir. Disco: ayrıntılı olaylar ve AI ise soru amaçları ve yanıt beklemeleri gibi alt olayları ayrıca verir. Her AI soru/yanıtını ayrı sekme ziyareti gibi zamanlamaz; AI sekmesinin toplam ziyareti ile alt olayların anlamı ayrıdır. Yeni kayıtlar request_key ile doğru ziyarete bağlanır. Dosyalardaki kalite/sekme çakışması bilgileri analizde korunmalıdır. Gerçek Disco import doğrulaması hâlâ bekliyor.
