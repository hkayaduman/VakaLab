# VakaLab: olaylar, mesaj etiketleri ve süre ölçümü

Bu metin mevcut 0.2.13 uygulamasının ölçüm kurallarını açıklar. Aşağıdaki yöntem metni, araştırmanın gerçek uygulama ve analiz kapsamına göre makaleye uyarlanabilir. Yazılımın kayıt üretmesi, bu kayıtlardan bilimsel analizin yapılmış olduğu anlamına gelmez.

## Makaleye uyarlanabilecek yöntem metni

Katılımcıların uygulama içindeki çalışma süreçleri, zaman damgalı olay kayıtları kullanılarak izlendi. Süreç birimi, bir katılımcının bir vaka üzerindeki çalışması olarak tanımlandı. İzlenen bölümler vaka metni, vaka profili, başlangıç değerlendirmesi, AI sohbeti, nihai değerlendirme, yansıtma ve son teslimdi. Bir bölümün açılması, bölüm değiştirme, alan düzenleme, AI’ya soru gönderme, AI yanıtının tarayıcıya ulaşması, başlangıç yanıtının kesinleştirilmesi ve son teslim gibi olaylar kaydedildi. Aynı bölüme yapılan dönüşler ayrı ziyaret aralıkları olarak korundu.

Olay kayıtları ile mesajların amaç etiketleri birbirinden ayrıldı. Olaylar hangi işlemin ne zaman gerçekleştiğini; amaç etiketleri ise öğrencinin AI’dan ne istediğini temsil etti. Örneğin, soru gönderimi bir olay, “Bilgi istedi” ise ilgili mesajın amaç etiketidir. Her soru ayrı bir olay olarak tutuldu; bir mesaja birden fazla etiket atanabilmesi, bu mesajın birden fazla ardışık soru olarak sayılması anlamına gelmedi. Uygulamada AI etiket önerileri ve araştırmacı seçimleri ayrı saklanmaktadır. Araştırmacı seçimi varsa boş seçim dahil bu seçim, yoksa mevcut AI önerisi etkin etiket olarak kullanılmaktadır. Etiketlenmemiş mesajlar ayrıca belirtilmektedir. Araştırmada kullanılan etiket sürümü, kodlama yönergesi ve araştırmacı incelemesinin kapsamı ayrıca bildirilmelidir.

Bölüm ziyareti, ilgili bölümün uygulama görünür ve pencere odaktayken açılması veya uygulamaya dönüşte yeniden etkinleşmesiyle başlayan bir zaman aralığı olarak ele alındı. Bölüm değiştirme, uygulamadan ayrılma, çalışma ekranından çıkma veya başarılı kesinleştirme/teslim kaydı bu aralığı kapattı. Her aralığın süresi, kaydedilen bitiş zamanından başlangıç zamanının çıkarılmasıyla hesaplandı. Bir bölümün toplam süresi, o bölüme ait süresi hesaplanabilen ziyaret aralıklarının toplamıdır. Uygulama görünür ve pencere odakta kaldığı sürece işlem yapılmadan beklenen zaman bu süreye dahildir; hareketsizlik için süre kesintisi uygulanmamaktadır.

Sayfanın görünmez olması veya pencerenin odağını kaybetmesi, mevcut bölüm aralığını kapattı ve “Uygulama dışında” aralığını başlattı. Uygulamaya dönüşte bu aralık kapatıldı ve ilgili bölüm için yeni aralık açıldı. Böylece uygulama dışındaki zaman bölüm sürelerine eklenmedi. Bu kayıt, başka bir site veya programın hangisinin kullanıldığını belirlemez. Ayrıca “Vakalarım” ekranına dönüş ile aynı vakayı yeniden açma arasında sınırları gözlenen zaman “Vaka dışında” olarak ayrı gösterildi. Bu aralık başka bir vakada çalışmayı da içerebileceğinden, uygulama dışında geçirilen zamanla eş tutulmadı.

Ziyaretler, ilgili aralıkta alan değişikliği kaydı bulunmamasına göre “görüntüleme”, en az bir alan değişikliği kaydı bulunmasına göre “güncelleme” olarak adlandırıldı. Bu adlandırma ziyaret aralığının bütününe ilişkindir; güncelleme süresi, kesintisiz yazma süresi olarak yorumlanmadı. Güncelleme, yanıtın kesinleştirildiği anlamına gelmez. AI sohbetinde gönderilmemiş soru taslağı görüntüleme olarak kalmaktadır. Kilitli bölüm erişimleri ayrıca adlandırıldı; bunlar bölüm içeriğinin kullanılması olarak değerlendirilmedi.

AI soru gönderimi ve yanıt gelişi, süresi sıfır olan anlık olaylar olarak kaydedildi. AI yanıtının hazırlanması ayrı bir bekleme aralığı olarak gösterildi. Bu ölçüm, gönderimden yanıtın tarayıcıya ulaşmasına kadar geçen zamanın uygulama içindeki kısmını kapsar; uygulama dışındaki zaman çıkarılır. Katılımcı başka bir uygulama bölümünde çalışırken AI beklemesi devam edebildiğinden, bekleme süresi bölüm sürelerine ayrıca eklenmedi. AI sohbeti bölümünün toplam süresi, o bölümde uygulama içindeyken geçirilen zamanın tamamını kapsar; bu sırada bekleme varsa o zaman zaten bölüm toplamının içindedir. Yanıt başka bölümdeyken veya uygulama dışındayken gelirse geliş olayı korunur. Yanıtın ulaşması ya da sohbet bölümünün görüntülenmesi, yanıtın okunduğuna ilişkin doğrudan kanıt olarak yorumlanmadı.

Başlangıç yanıtının kesinleştirilmesi ve son teslim, sunucuda başarılı kaydın doğrulandığı ayrı olaylar olarak tutuldu; yalnız düğmeye basılması başarı ölçütü kabul edilmedi. İzleme başarılı son teslimle sona erdi ve teslim sonrası gezinmeler araştırma sürecine eklenmedi. Başlangıç ve bitiş sınırı bulunmayan veya saat sırası geçersiz olan aralıklar için süre tahmin edilmedi. Süreler tarayıcı tarafından bildirilen zaman damgalarına dayandığından cihaz saatinin doğruluğu ve kayıt eksiklikleri ölçümün sınırlarıdır. Elde edilen süreler dikkat, okuma, düşünme veya öğrenme süresi değil, kaydedilen uygulama kullanım süreleri olarak ele alındı.

## Etkinlik sözlüğü

| Etkinlik | Anlamı | Süre |
|---|---|---|
| Vaka / profil / başlangıç / nihai / yansıtma / son teslim görüntüleme | Aralıkta alan değişikliği kaydı yoktur. | Ziyaretin bitişi − başlangıcı. |
| İlgili bölümde güncelleme | Aralıkta en az bir alan değişikliği kaydı vardır. | Ziyaretin tamamı; yalnız yazma süresi değildir. |
| Kilitli … bölümünü görüntüleme | Kilit ekranına erişim; içerik kullanımı değildir. | Kilit ekranında kaydedilen aralık. |
| AI sohbeti görüntüleme | Sohbetin görüntüleme aralığı; gönderilmemiş taslak da bu adla kalır. | Ayrıntılı akışta beklemeyle çakışan kısımlar ayrılır. |
| AI sohbeti · [mesajın amaç etiketi/etiketleri] | Sunucu mesajıyla eşleştirilmiş tek soru gönderimi. | 0 saniye. |
| AI sohbeti · Mesaj gönderme denemesi | Sunucu mesajıyla eşleşmesi bulunmayan gönderim girişimi; başarılı soru olduğu varsayılmaz. | Anlık girişim. |
| AI yanıtı hazırlanıyor | Gönderimden tarayıcıya gelişe kadar beklemenin uygulama içindeki kısmı. | Ayrı bekleme kulvarı; çalışma toplamına yeniden eklenmez. |
| AI yanıtı geldi | Tam yanıtın tarayıcıya geliş kaydı; okuma kanıtı değildir. | 0 saniye. |
| Uygulama dışında | Görünürlük veya pencere odağının kaybı ile dönüş arası. | Bölüm toplamından ayrı. |
| Vaka dışında | Vakalarım’a dönüş ile aynı vakanın yeniden açılması arası, sınırları biliniyorsa. | Bölüm toplamından ayrı. |
| Başlangıcı kesinleştirme | Başarılı başlangıç kaydının doğrulanması. | 0 saniye; ayrı sınır olayı. |
| Teslim | Başarılı son teslimin doğrulanması. | 0 saniye; izleme sonu. |

Başlıca dışa aktarım kodları: `CASE_VIEW`, `PROFILE_VIEW`, `INITIAL_VIEW`, `INITIAL_EDIT`, `FINAL_VIEW`, `FINAL_EDIT`, `REFLECTION_VIEW`, `REFLECTION_EDIT`, `CONFIRM_VIEW`; kilitli bölümlerde `_LOCKED`; ayrıca `AI_VIEW`, `AI_MESSAGE`, `AI_SEND_ATTEMPT`, `AI_WAIT`, `AI_RESPONSE`, `APP_OUTSIDE`, `CASE_AWAY`, `INITIAL_CONFIRMED`, `SUBMITTED`. Bunlar örnek kodlardır; ilgili bölümde alan değişikliği kaydına göre `_VIEW` veya `_EDIT` üretilir. Hata ve eksik geçmiş kayıtlarının durumları ayrıca korunur.

## Mevcut mesaj etiketi adları

Yerel ortamda etkin adlar: **Bilgi istedi, Açıklama istedi, Metin düzenletmek istedi, Eleştiri istedi, Alternatif fikir istedi, Karar istedi, Diğer, Belirsiz.**

Adların anlaşılması için aşağıdaki kısa karşılıklar kullanılabilir. Bunlar bu belgede önerilen açıklamalardır; uygulamadaki etiket tanımlarına yazılmamıştır ve araştırma için tamamlanacak kodlama yönergesinin yerine geçmez.

| Mesaj etiketi | Kısa karşılığı |
|---|---|
| Bilgi istedi | Konuya ilişkin bilgi veya içerik talebi. |
| Açıklama istedi | Bir kavramın, gerekçenin veya yanıtın açıklanması talebi. |
| Metin düzenletmek istedi | Mevcut metnin düzeltilmesi veya yeniden yazılması talebi. |
| Eleştiri istedi | Karar, gerekçe veya yaklaşımın değerlendirilmesi ve eksiklerinin belirtilmesi talebi. |
| Alternatif fikir istedi | Farklı seçenek, çözüm veya bakış açısı talebi. |
| Karar istedi | AI’dan bir seçenek veya eylem yönünde karar/öneri talebi. |
| Diğer | Amacı anlaşılabilen, diğer tanımlı kategorilere girmeyen talep. |
| Belirsiz | Eldeki mesaj ve bağlamla amacı güvenilir biçimde belirlenemeyen talep. |

Önemli hazırlık notu: Bu ortamda söz konusu etiketlerin açıklama alanları hâlâ “SENTETİK başlangıç tanımı” içermektedir. Yukarıdaki adlar doğrulanmış bir bilimsel kod kitabı değildir. Veri toplama/analiz öncesinde her etiketin dahil etme ve dışlama ölçütleri, örnekleri, çoklu etiketleme kuralı ve Diğer/Belirsiz ayrımı araştırmacı tarafından tanımlanmalıdır. Uygulamanın AI önerisini kullanabilmesi, uzman incelemesi veya kodlayıcılar arası güvenirlik çalışması yapılmış olduğu anlamına gelmez. Etiketler bu düzenlemede değiştirilmemiştir.

## Basit süre örneği

| Başlangıç–bitiş | Etkinlik | Süre |
|---|---|---:|
| 10:00–10:01 | Başlangıç değerlendirmesi görüntüleme | 60 sn |
| 10:01–10:04 | Uygulama dışında | 180 sn |
| 10:04–10:06 | Başlangıç değerlendirmesi güncelleme | 120 sn |
| 10:06 | Nihai değerlendirmeye geçiş | Yeni aralık başlar |

Başlangıç değerlendirmesi toplamı **180 sn**, uygulama dışında toplamı **180 sn**. İkinci aralığın güncelleme olması için bu aralıkta en az bir alan değişikliği gözlenmiş olmalıdır; tüm 120 saniye boyunca yazıldığı çıkarılamaz.

AI örneği: 10:00:00 sohbet açılır, 10:00:20 soru gönderilir, 10:00:25 nihai bölüme geçilir, 10:00:30 yanıt gelir. AI bölümü toplamı 25 sn, AI beklemesi 10 sn'dir. Beklemenin 5 saniyesi nihai bölümdeki zamanla çakışır. Bu nedenle 25 + 10 şeklinde bir AI bölümü süresi üretilmez. 10:00:25–30 arası nihai bölüm süresinde kalır; AI beklemesi ayrı teknik süreçtir.

## Veri analizi ve dışa aktarım ayrıntıları

- Süreç kimliği öğrenci × vaka çalışmasıdır (`Case ID`). Tekrar ziyaretler korunur; teknik periyodik durum kayıtları ayrı kullanıcı etkinlikleri gibi sayılmamalıdır.
- Ham olaylar ile bunlardan türetilen ziyaret/etkinlik aralıkları farklı ayrıntı düzeyleridir. “Öğrencinin işlem çizelgesi” ham gözlemleri; “Bölüm geçişleri ve ziyaret süreleri” türetilen aralıkları gösterir. Alan değişikliği gözlemleri her tuş vuruşunun eksiksiz kaydı değildir. Sunucuda saklanan yanıt revizyonları ayrıca bulunur.
- Süre analizi için varsayılan geçiş/süre CSV'si kullanılır. Bölüm toplamı temel ziyaret aralıklarından hesaplanır; tüm etkinlik satırlarının süreleri körlemesine toplanmaz. Özellikle `Timeline Lane=ai_wait` ayrı değerlendirilir. Anlık sorular, yanıtlar ve kesinleştirmeler sıfır sürelidir.
- CSV, `Duration Seconds`, ham UTC başlangıç/bitiş zamanları, `Sequence`, önceki/sonraki etkinlik, ölçüm kaynağı ve kalite alanlarını taşır. Ekranda saniyeler yuvarlanabilir; analizde hassas CSV değerleri kullanılır. Aynı ana düşen kayıtların sırası dışa aktarımın sıra alanıyla korunur; yapay süre eklenmez.
- Kapanışı bilinmeyen/geçersiz saatli aralıklar süre CSV'sine alınmaz; eksiklik sayısı belirtilir ve ham kayıtlar korunur. Eski sürümde gönderim olayı olmayan sorular istisna olarak sunucu mesaj zamanıyla, süre alanı boş bırakılarak gösterilir; istemci ve sunucu saatleri arasında süre çıkarılmaz.
- Yeni ölçümde görünürlük ve pencere odağı birlikte kullanılır. Eski kayıtlarda yalnız bulunan gözlemler kullanılabilir; sürüm ve kaynak alanları dikkate alınmalıdır. Geçmiş test verileri güncel katılımcı verisi gibi birleştirilmemelidir.
- Eşzamanlı sekmelerde çakışma işareti korunur. Uygulama içi genel toplam, bilinen bölüm aralıklarının zaman birleşimidir; aynı an bir kez sayılır. Bölüm bazındaki toplamlar ziyaret sürelerinin toplamıdır. Paralel sekmeli kayıtlar analizde ayrıca incelenmelidir.
- Başlangıcı kesinleştirme olayı AI erişim sınırını taşır; ayrı AI öncesi/sonrası günlükler bu düzenlemede üretilmemiştir. İleride bu sınırla analiz ayrımı yapılabilir; kilitli AI erişimleri gerçek AI kullanımı sayılmaz.
