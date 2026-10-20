# Process mining değerlendirmesi — 0.2.4

Bu bölüm 0.2.4 için tarihsel incelemedir. 0.2.5 güncellemesi: kullanıcının click-to-click süre tanımı tüm yedi sekmede uygulandı; navigation_visits ve varsayılan Disco geçiş/süre CSV eklendi, son teslimde izleme durur. Ayrıntılı AI olay CSV ayrı kalır. Yeni çıktı gerçek Disco uygulamasında henüz içe aktarılmadı. Güncel kanıt TEST_RESULTS.md 0.2.5 bölümündedir.

Kullanıcının hedefi: öğrencinin vaka/profil/başlangıç/AI/nihai/yansıtma arasında izlediği yolu, geri dönüşleri, soru amaçlarını, ziyaret sürelerini ve beklemeleri analiz etmek; daha sonra sequence analysis yapmak. Bu tur değerlendirme yapıldı, uygulama kodu değiştirilmedi.

## Mevcut çıktı

Her öğrenci × vaka uygulaması ayrı Case ID'dir. Bölüm açma, metin/profil/soru taslağı değişikliği, kopyalama/yapıştırma, başlangıcın kesinleşmesi, son teslim, mesaj gönderimi ve AI mesajının kaydı dışa aktarılır. Mesajlarda son AI önerisi, son araştırmacı seçimi ve öncelik kuralıyla seçilen etiket ayrı sütunlardadır. AI attempt başlangıç/bitişi ayrı kaynakla taşınır.

## Hedef açısından eksikler

1. Öğrenci olayları ve mesajlar nokta olaydır: Start Timestamp = Complete Timestamp. Bölümde kalış süresi bunların kendi süresi değildir. Noktalar arası geçen süre bazı geçiş analizlerinde kullanılabilir, fakat görünür/odaktaki süre, arka plan/ara verme ve AI beklemesiyle aynı değildir.
2. Telemetry::visits görünür/odak gözlemlerini ve bilinmeyen boşlukları hesaplar. visits tam Excel/JSON'da vardır, Disco CSV'ye aktarılmaz. Bölüm süreleri ve darboğaz yorumu için ziyaret/ölçüm kapsamı çıktısı gerekir. Uzun süre tek başına sorun veya düşünme kanıtı sayılmaz.
3. Mesaj Activity alanı Öğrenci mesajı gönderildi veya AI mesajı kaydedildi'dir. Bilgi/eleştiri vb. amaçlar Effective Labels içindedir. Disco'da Activity ve Effective Labels birlikte etkinlik olarak seçilerek kısmen ayrım yapılabilir; standart, doğrudan kullanılabilir davranış kodları henüz üretilmiyor.
4. Metin/profil değişikliklerinin hangi alana ait olduğu ham payload ve draft geçmişinde vardır; mevcut kodlanmış Disco çıktısında bu ayrıntı atılır. Bu yüzden karar yazımı ile gerekçe yazımı, profil seçimi ile nihai güven seçimi aynı genel olay adı altında kalabilir.
5. Tekrarlanan yazım olayları, AI attempt aralığı, mesaj kaydı ve response_visible aynı ham akışta karışır. Davranış haritası için hangi olay düzeyinin seçildiği açıkça tanımlanmalıdır; aynı AI yanıtı farklı teknik kayıtlarla farklı davranışlar gibi sayılmamalıdır.
6. İstemci ve sunucu saatleri karışıktır. Sequence alanı tek bir küresel sıra değildir (istemci sekme sırası, mesaj sırası veya 0 olabilir); sekme/oturum kimlikleri mevcut Disco'da yoktur. Eşzamanlı sekmeler, saat sapması ve aynı milisaniyeye düşen olaylar için sıralama/kalite kuralları gerekir. Kodlanmış çıktı üretirken kilitli bölüme geçme denemesi bilgisi de atılır; erişilemeyen bölümü görüntüleme gibi kodlamamak gerekir.
7. AI yanıtının gelmesi, ekranda görülmesi ve okunması farklı kavramlardır; okunma çıkarımı yapılamaz. Yazım telemetrisi 700ms ortak throttle içerir; her tuş/alan değişikliğinin eksiksiz kaydı değildir. Sunucuya kaydedilmiş revizyonlar ayrı kanıttır. Kapanış/çökme sırasında gönderilemeyen son olaylar için eksiklik olasılığı vardır.

## Uygun sonraki kapsam (henüz uygulanmadı)

- Davranış olay günlüğü: araştırma sorusuna uygun tek kod sözlüğü; örneğin vaka görüntüleme, profil düzenleme, başlangıç kararı/gerekçesi düzenleme, başlangıç kesinleştirme, AI bilgi/eleştiri/alternatif üretme isteği, AI yanıtının kaydı, nihai karar/gerekçe/güven düzenleme, yansıtma ve son teslim. Birden fazla mesaj etiketi sahte ardışık olaylara bölünmemeli; çoklu amaç ve inceleme durumu ayrıca korunmalı.
- Süre/ziyaret çıktısı: her gerçek ziyaret veya sınırları bilinen gözlem aralığı için başlangıç/bitiş, gözlenen görünür/odakta süre, eksik kapsam, ziyaret/oturum/sekme bağı; AI bekleme aralıkları ayrı. Bölüme giriş ile bir sonraki olay arasındaki sürenin tamamı aktif süre sayılmaz; bilinmeyen boşluklar uydurulmaz.
- Sequence analysis görünümü: aynı kod sözlüğü, süreç başına güvenilir sıralama ve zaman; tekrarlı yazım gözlemlerinin birleştirilmesi kuralı açık. Gerçek geri dönüşler korunur; olay temelli ve sabit zaman dilimli sequence analysis farklı hazırlık gerektirir.
- Sentetik kullanıcının verdiği örnek yol fixture'ı ile beklenen geçiş/geri dönüş/süre doğrulaması; gerçek Disco import ve frekans/süre haritası karşılaştırması.

## Bu tur kanıt

Ayrı bellekteki SQLite/mock fixture'ında mevcut Export::disco çalıştırıldı: 9 CSV satırı; tam veri kümesinde 8 visits aralığı; CSV'de 0 ziyaret aralığı; öğrenci/tarayıcıya ait 7 nokta olayında başlangıç=bitiş; mesaj Activity genel, etiket ayrı sütunda. Kanıt: test-evidence/process-mining-review-0.2.4.txt. Bu sayılar sentetik örneğe aittir; gerçek katılımcı sonucu veya Disco import testi değildir. Önceki 0.2.4 kontrolleri yeniden çalıştırılmadı. Mevcut çalışma DB'sine yazılmadı.

## Resmî Disco kaynağı

https://fluxicon.com/book/read/import/ — Combining Multiple Case ID, Activity, or Resource Columns; Including Multiple Timestamp Columns. Etkinlik adının seçimi haritanın ayrıntısını belirler; tek zaman damgası akış/geçiş sürelerini, ayrı başlangıç-bitiş ise etkinlik aralığını analiz etmeyi sağlar. Tarayıcı gözleminin neyi temsil ettiğini ayrıca uygulamanın ölçüm tanımı belirler.
