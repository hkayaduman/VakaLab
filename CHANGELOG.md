# Değişiklik günlüğü

## 0.1.9 — 2026-10-20 — Çıkış sonrası sekme kilidi

- Başarılı çıkışta ve Vakalarım dönüşünde yalnız mevcut sekmenin kendi lease kaydı temizlenir. Hızlı yeniden girişteki sahte başka sekme uyarısı giderildi; gerçek ikinci sekme koruması sürer.
- Hata ayrı sentetik Chrome koşusunda yeniden üretildi; düzeltme sonrası69kontrol geçti. Asset0.1.9. Kullanıcıverisi/şema değişmedi; manuel tekrar bekliyor.

## 0.1.8 — 2026-10-20 — Türkçe olay açıklamaları

- events Excel/CSV/JSON çıktılarında type_tr,section_tr,event_category,event_description alanları; özgün kodlar ve zamanlar korunur.
- dictionary tüm16olay/7bölüm kodunu ve temel process mining alanlarını açıklar. Veri indirme ekranı yeni sütunları tanıtır.
- Heartbeat teknik durum gözlemi olarak açıklanır; otomatik olay filtreleme veya okuma/düşünme çıkarımı yok.
- 30hedefli export ve69SQLite kontrolü geçti. Şema/mevcutveri değişmedi;asset0.1.8.

## 0.1.7 — 2026-10-20 — Görünür analiz adımları

- Sekiz adım baştan görünür; tamamlanan, sıradaki, çalışan ve kilitli durumları Türkçe gösterilir. Yalnız sıradaki adım çalıştırılabilir; her adımın sonuçları kendi altında açılır.
- Araştırmacı karar durumu görünür; sekizinci adım eksik3–7 kararlarını açıklar. Başarısız denemeler/yeniden deneme ve geçmiş korunur.
- 12 model ve67 Chrome kontrolü geçti; asset0.1.7. Backend/şema/veriler değişmedi.

## 0.1.6 — 2026-10-20 — Modal kayıt hataları

- Kayıt hatası açık pencerenin içinde gösterilir; boş gerekçeye odaklanılır, seçimler/metin korunur. Etiket kaydı başarısı güncel seçimin adını bildirir.
- Başlangıçta yarım kalan testler0.1.7 koşusunda tamamlandı: eksik gerekçe,503 ve Eleştiri etiketi kaydı/refresh geçti.

## 0.1.5 — 2026-10-20 — Anlaşılır mesaj etiketi geçmişi

- Güncel araştırmacı etiketleri mesajda görünür; düzenleme ekranında işaretli açılır. AI önerileri ayrı gösterilir.
- Yeni seçimde önce/sonra, eklenen/kaldırılan etiketler adlarıyla canlı gösterilir. Karar geçmişi tarih, araştırmacı ve gerekçeyle okunur; ham JSON kapalı ayrıntıya taşındı.
- Etiketsiz karar dahil son review güncel seçimdir; eski kararlar korunur. Şema/API yazımı değişmedi.
- 8 model ve 35 Chrome kontrolü geçti. Asset0.1.5.

## 0.1.4 — 2026-10-20 — Okunaklı tarihler

- Matris, süreç özeti, öğrenci teslimi ve global kapanış tarihleri gün.ay.yıl saat:dakika:saniye ve açık UTC offset ile gösterilir. Tarayıcı saat diliminden bağımsız yapılandırılmış timezone kullanılır.
- Özgün UTC değeri tarih üzerine gelince korunur. Ham JSON, kayıtlar ve indirmeler değişmedi; ham veri bölümünde UTC açıklaması var.
- 10 biçimlendirme ve 22 Chrome kontrolü geçti; asset sürümü 0.1.4. Mevcut kullanıcı verisi korunur.

## 0.1.3 — 2026-10-20 — Eksik listesini Son teslim ile sınırla

- Kullanıcı isteğiyle genel Eksik alanları tamamlayın kutusu yalnız Son teslim sekmesinde oluşturulur. Vaka/profil/başlangıç/AI/nihai/yansıtma ekranlarında bu kutu yoktur; bağlantıyla o alanlara geçince de taşınmaz.
- Son teslimde eksikler tek listede gösterilir; footer'daki mükerrer bağlantı listesi kaldırıldı. Alan içi hata/focus ve teslim güvenlik koşulları korunur.
- Regresyonda tespit edilen mobil geçiş: kaydedilmemiş alanı bırakıp sekmeye basarken blur/autosave üst çubuk düzenini değiştirip tıklamayı düşürebiliyordu. Pointerdown sırasında erken blur önlendi; sekmenin mevcut click işlemi kaydı tamamlayıp geçişi yapar. Klavye click davranışı değişmedi.
- 50 hedefli Chrome kontrolü ayrı sentetik DB'de geçti. Asset sürümü 0.1.3; mevcut veri ve şema değişmedi. Kullanıcı tekrar kontrolü bekliyor.

## 0.1.2 — 2026-10-20 — Ayrı son teslim sekmesi

- Kullanıcı isteğiyle son teslim kartı ortak sayfa altından kaldırılıp son sıradaki 7. Son teslim sekmesine taşındı.
- Diğer altı bölümde teslim düğmesi/kartı yok. Başlangıç kilidi, dört final gereksinimi, kayıt/AI/online kontrolleri ve eksik alan bağlantıları korundu.
- Yayımlanmış rapor bağlantıları ve teslim zamanı da Son teslim bölümünde. Yeni confirm olay bölümü sunucuda kabul edilir; mevcut kayıt/snapshotlara müdahale yok.
- Yatay gezinmede seçili sekme görünür alana alınır. Asset sürümü 0.1.2.
- Ayrı sentetik veride 40 hedefli ve 18 mevcut browser kontrolü geçti. Kullanıcının yeni sekmeyi manuel doğrulaması bekliyor.

## 0.1.1 — 2026-10-20 — Kullanıcı test dönütü

- Eksik alanlar için kalıcı linkli özet, kırmızı kenarlık/yan açıklama, sekme bazında eksik sayısı ve otomatik odak.
- Nihai karar/gerekçe/güven/yansıtma dolmadan ve sunucu kaydı bitmeden son teslim düğmesi açılmaz. AI sürerken/çevrimdışıyken de kapalıdır; alan silinirse anında kapanır.
- Son teslim kartında eksik alana doğrudan bağlantılar; yalnız beyaz boşluk geçerli içerik sayılmaz, 0 geçerli puandır.
- Blur/autosave sırasında tıklanacak linkin DOM'dan kaldırılması önlendi. Mobilde yönlendirilen alan sabit gezinmenin altında kalır.
- Asset sürümü 0.1.1. Sunucu yanıt kilitleri/şema ve mevcut kayıtlar değiştirilmedi.
- Ayrı sentetik DB'de 30 hedefli Chrome kontrolü ve mevcut 18 browser regresyonu geçti. Yeni manuel kabul M09–M11 bekliyor.

## 0.1.0 — 2026-10-20 — Sentetik pilot sürümü

- PHP/MySQL uygulaması, güvenli public kökü, migration ve CLI kurulum.
- Öğrenci bölüm gezinmesi, profil/başlangıç atomik kilidi, autosave/revision, kurtarma, tek AI isteği ve tek teslim.
- Araştırmacı katılımcı/vaka/atama yönetimi, matris ve filtreler, sürümlü rubrik/etiket, özel notlar ve audit.
- Olay dedup, revision geçmişi, belirsiz süre aralıkları ve ayrı AI kulvarı.
- Mock ve compatible AI adapter; explicit retry/attempt/fencing; gerçek API henüz doğrulanmadı.
- Tek öğrenci-vaka için ayrı sekiz analiz adımı, bağımsız rubrik girdileri, kanıt kontrolü, uygulamada sayısal hesap ve ayrı insan kararları.
- Rapor snapshot, onay, global kapanış ve ayrı yayın; özel araştırmacı eki öğrenciden çıkarılır.
- Gerçek DOCX/PDF/XLSX, güvenli CSV/JSON; toplu ve kodlanmış çıktılar.
- Yedi sentetik öğrenci, beş vaka + deneme, örnek olay/etiket/rubrik/rapor fixtureları.
- SQLite/MariaDB alan kontrolleri, iki worker HTTP concurrency, Chromium mobil/masaüstü, yerel backup restore.
- Kurulum, test verisi, manuel kabul, veri sözlüğü ve devam belgeleri.

Bu sürüm gerçek araştırma/hosting/AI ve kullanıcı kabulünü tamamlamış sayılmaz; açık kontroller BUGS.md ve plan.md içindedir.
