# Codex İçin Geliştirme Promptu

Aşağıdaki metni proje dosyalarıyla birlikte Codex'e ver. Bu dosya geliştirme talimatıdır; uygulamanın şu anda hazır olduğu anlamına gelmez.

---

## Görevin

Bu depodaki şartnamelere göre öğretmen adayları için araştırma veri toplama uygulamasını geliştir. Teknolojiler: HTML, CSS, JavaScript, PHP ve MySQL. Hedef paylaşımlı PHP hostingdir. Türkçe, masaüstü öncelikli ve mobil uyumlu arayüz üret. Yalnızca tasarım, sahte ekran veya öneri verme; çalışan uygulama, veritabanı kurulumları, testler ve dağıtım belgeleri oluştur.

Önce şu dosyaları oku:
1. `CONTEXT.md`
2. `plan.md`
3. `01_OGRENCI_PANELI.md`
4. `02_ARASTIRMACI_PANELI.md`
5. `04_MANUEL_TEST_REHBERI.md`

Son kullanıcı kararları şartnamelerde birleştirilmiştir. Önceki fikirleri geri getirme. Belirsiz teknik tercihlerde geri alınabilir varsayım seç, kaydet ve ilerle; gerçek API anahtarı, gerçek rubrik veya hosting özelliği uydurma.

## Değiştirilemez ürün kuralları

1. Kalıcı silme yok. Arşiv/dondurma/analiz dışı ve sürüm geçmişi var.
2. Öğrenci numarası görünür kimlik; değişmeyen iç kimlik ayrı.
3. Atama ve aktivasyon olmadan vaka açılmaz. Kullanılmış vaka sürümü değişmez.
4. Vaka sürekli açık/sabit panelde değil; tek çalışma sayfasında bölüm gezinmesiyle açılır.
5. Profil ve başlangıç yanıtı sunucuda atomik kesinleşmeden AI açılmaz. Kesinleşen veri değişmez.
6. AI vaka metnini bilir; profil/başlangıç/nihai/yansıtma/rubrik/etiket/başka vaka verisi otomatik verilmez.
7. AI bitirme aşaması yok. Nihai ve yansıtma yazılırken AI'a dönülebilir.
8. AI yanıtı sürerken ikinci mesaj gönderilmez/kuyruklanmaz. Soru taslağı yazılabilir. Nihai/yansıtma autosave çalışmaya devam eder.
9. Nihai ve yansıtma tek teslimle kilitlenir. Yanıt üretimi sürerken teslim olmaz.
10. AI ve araştırmacı etiketleri ayrı; araştırmacı yeni etiket ekleyebilir ve geçmişi koruyarak değiştirebilir.
11. Analiz bir öğrenci-vaka ve ayrı sıralı adımlardır. Toplu AI analizi yok. Toplu veri indirme var.
12. Başlangıç/nihai rubrik çağrıları bağımsız. Diğer metin/puan ve güven puanları puanlama çağrısına verilmez.
13. Sayısal fark, toplam ve ağırlık uygulamada hesaplanır.
14. Ham veri, AI önerisi ve araştırmacı kararı ayrıdır. Kabul/düzenle/reddet/karar verilemedi vardır.
15. Araştırma genelindeki beş vakanın veri toplaması kapanmadan rapor/puan öğrenciye açılmaz. Kapanış + onay + ayrı yayın gerekir.
16. Bölüm açılması okuma; görünür yanıt okuma; kopyalama benimseme; hareketsizlik düşünmeme değildir.
17. AI işlem süresi ve öğrenci bölüm süreleri ayrı kulvarlardır; çakışan süreler toplanmaz.
18. Hazır prompt, prompt puanı, yeniden üretme, dallandırma, öğrenci sıralaması yok.

## Mimari öneri ve hosting koşulları

Bunlar uygulama önerileridir; gerçek hostingde doğrula ve seçimini belgele:
- Sunucuda PHP; PDO ile MySQL, transaction destekli tablolar, utf8mb4.
- Sade katmanlı yapı: controller/route, authorization, service/domain, repository, view, export, AI adapter.
- Tarayıcıda vanilla JS veya küçük bağımlılıklar; zorunlu Node sunucusu yok. Geliştirmede build kullanılırsa derlenmiş dosyaları teslim et.
- WebSocket, Redis, Docker, root, kalıcı worker, Supervisor veya zorunlu cron gerektirme.
- Composer gerekiyorsa yerelde kurulmuş vendor ile dağıtım yolunu belgele; hostingde SSH/Composer var sayma.
- PHP minimum sürümünü ve gereken eklentileri gerçek seçtiğin kütüphanelere göre belirt. Bilinmeyen sürümü kullanıcı adına kesinleştirme.
- Ana dizin ve alt klasör kurulumunu destekle; base URL yapılandırılabilir olsun.
- Public olmayan yapılandırma, log, yedek ve çıktı depoları kullan. Hosting document root değiştirilemiyorsa alternatif güvenli yerleşim ve erişim engelleme testi sağla.
- HTTPS, güvenli oturum çerezleri, parola hash/verify, CSRF, her istekte rol + sahiplik kontrolü, hazırlanmış SQL, HTML kaçışlama ve AI Markdown sanitizasyonu uygula.
- Anahtarları tarayıcıya, hata ekranına veya loga yazma. Rate limit ve istek boyutu sınırları ekle.
- Kurulum/seed araçlarını yetkisiz web erişimine kapat; kurulum sonrası kilitle.

## Kritik eşzamanlılık şartı

PHP oturum kilidini uzun AI isteği boyunca tutma. Kimlik/yetkiyi doğruladıktan sonra oturum yazma kilidini güvenli biçimde serbest bırak; aksi halde AI yanıt verirken nihai autosave bloke olabilir. Bu davranışı otomatik ve manuel test et.

AI çağrısı boyunca uzun veritabanı transaction/row lock tutma. Kısa transaction ile yanıt işlem kaydı ve tek aktif istek koşulu oluştur; sonra sağlayıcı çağrısı yap. Durum geçişlerini atomik ve idempotent uygula. Stale istekleri sağlayıcı yanıtı hâlâ gelebilirken körlemesine yeniden başlatma; attempt/fencing kimliğiyle geç gelen yanıtı ayırt et. Çift yanıt ve çift maliyet riskini belgele. Tarayıcı kapanınca PHP isteğinin devam edeceğini varsayma.

Akış için hosting destekliyorsa fetch/stream veya uygun aktarım kullan; autosave ve bölüm gezinmesi bloklanmasın. Tamponlama/timeout varsa dürüst non-stream fallback ve eksik ölçüm işaretleri sağla. Sahte streaming zamanı üretme. Akış ve eşzamanlı kayıt gerçek hostingde doğrulanmadan destekleniyor deme.

## Veri modeli ve durum makinesi

Şema/migration ve veri sözlüğü üret. En az şu kavramları kapsa:
- Kullanıcılar/roller, öğrenci profili ve hesap durum geçmişi.
- Araştırma ve global veri toplama durumu.
- Vakalar, sürümler, atamalar ve atama geçmişi.
- Öğrenci-vaka uygulamaları ve durum geçişleri.
- Profil, başlangıç, nihai, yansıtma ve kesinleşmiş snapshotlar.
- Taslak sürümleri ve revision/optimistic concurrency kontrolü.
- Sohbet, mesaj, AI istek/attempt/yanıt durumları.
- Olaylar, bölüm ziyaretleri, görünürlük/odak/heartbeat, ölçüm kalitesi.
- Etiket tanımları/sürümleri, AI atamaları, araştırmacı atamaları/geçmişi.
- Rubrik/sürüm/boyut/düzey/ağırlık.
- Analiz run/adım/girdi snapshot/çıktı/kanıt.
- Araştırmacı kararları, rapor snapshot/sürüm/onay/yayın.
- Denetim günlüğü, dışa aktarma kayıtları.

İlişkileri FK ve uygun unique/indexlerle koru. Vaka/model/rubrik değişimi geçmişi yeniden yazmasın. Yedekleme/migration üretim verisini sıfırlamasın. Tarihleri tutarlı UTC sakla; arayüzde yapılandırılabilir saat dilimi, dışa aktarmada açık ofset/ISO zaman kullan. İstemci zamanı ile sunucu alınma zamanı ayrı; sıra ve belirsizlik kaybolmasın.

## Olay toplama ve autosave

Her olayın benzersiz idempotency kimliği, uygulama/oturum/sekme bağlantısı ve sırası olsun. İstemci tekrar gönderimleri çoğaltmasın. Çevrimdışı olay kuyruğu hesabı/vakayı karıştırmasın. Heartbeat ile kesin okuma süresi uydurma. Eksik kapanışta son güvenilir gözlem ve belirsizlik kullan. Kopyalama kaynak metin/sürüm/aralıkla bağlansın; genel pano okunmasın. Çoklu etiket event tablosunda olay çoğaltmasın.

Autosave revision kontrolüyle çalışsın; eski yanıt yeni metni ezmesin. Başlangıç kesinleştirme ve son teslim en son sürümü atomik kaydetsin. Yerel kurtarma yalnızca aynı kullanıcı yeniden doğrulanınca gösterilsin; çıkış/ortak cihaz politikasını belgele. Sıfır veri kaybı garantisi verme.

## AI adapter ve analiz

Gerçek sağlayıcı bilgisi verilmedi. Adapter arayüzü ve yalnızca test ortamında açıkça TEST/SENTETİK olarak işaretlenen deterministik mock sağlayıcı oluştur. Gerçek sağlayıcı için ayar/bağlantı testi ve örnek yapılandırma sağla. Mock'u gerçek AI veya araştırma verisi gibi gösterme. Gerçek API yoksa bunu eksik entegrasyon olarak plan/context dosyalarına yaz.

Analiz adımları ayrı çağrı ve girdi allowlistleriyle çalışsın. Başlangıç/nihai bağımsızlığını giden payload testleriyle doğrula. AI çıktısını JSON şema/alan/aralık kontrolünden geçir. Geçersiz veya kanıtsız çıktıyı puana dönüştürme. Kanıt referansları gerçekten var olan metin/mesaj/sürümle eşleşsin. Öğrenci metnindeki talimatları veri say; prompt injection ile puan veya rapor kuralları değişmesin. API anahtarları/sistem sırları çıktıya girmesin.

Mesaj etiketlemesi sohbet yanıt yolunu bloklamasın. Paylaşımlı hostingde ayrı kısa, yetkili işlemler veya araştırmacı tetiklemesiyle çalışabilir; seçimini belgele. Tek öğrenci-vaka analizi kaldığı adımdan devam etsin; otomatik tüm öğrenciler kuyruğu oluşturma.

## Dosya çıktıları

Gerçek DOCX, PDF ve XLSX üret; uzantı değiştirerek taklit etme. PHP tabanlı uygun kütüphaneleri seç, sürüm/lisans/eklenti gereksinimlerini belgele. Sunucuda Python/LibreOffice/Chromium zorunlu kılma. Türkçe font, uzun içerik, tablo ve sayfa taşmasını test et. CSV UTF-8, güvenli formül kaçışlama ve açık veri sözlüğüyle verilsin. Kimliksiz çıktıda eşleme tablosu veya iç not sızmasın. Öğrenci raporu iç AI/araştırmacı taslaklarını içermesin. İndirme endpointleri her seferinde yetki ve yayın kapısını denetlesin; tahmin edilebilir public dosya URL'si verme.

## Test verisi: zorunlu, yalnızca sentetik

Geliştirme sonunda çalıştırılabilir, tekrar çalıştırıldığında kopya üretmeyen test seed'i sağla. Üretim veritabanını temizleme veya TRUNCATE etme. Ayrı test veritabanı/ortamı kullan. Test reset gerekiyorsa yalnızca ayrı disposable test ortamında, üretimden açık ayrımla yapılabilir; ürünün kalıcı silme kuralını aşan admin özelliği ekleme.

Önerilen fixture kapsamı:
- Bir araştırmacı test hesabı ve farklı durumlarda yedi sentetik öğrenci; gerçek öğrenci no/kimlik kullanma.
- Beş sentetik pedagojik vaka ve bir deneme vakası; taslak/aktif/kapalı ve sürüm örnekleri.
- Atanmamış, açmamış, başlangıç taslağı, başlangıcı kilitli, AI aşaması, nihai taslak, teslim, dondurulmuş hesap örnekleri.
- AI üretirken nihai metin yazma, sohbet ekranında etkileşimsiz kalma, sayfa arka planı, kopyalama, tekrar açma, kesinti/kurtarma olayları.
- AI önerisi/araştırmacı etiketi farkı, çoklu etiket ve yeni etiket örneği.
- Açıkça TEST RUBRİĞİ etiketli örnek rubrik; 8 → 10 güven farkı örneği; ağırlıklı hesap testleri.
- İncelenmemiş/onaylı/yayımlanmamış raporlar; yayın testi ayrı senaryoda global kapanışla.
- Gecikme, kısmi yanıt, hata ve geçersiz analiz JSON'u üreten mock modları.

Seed hesaplarını ve geçici parolaları yalnızca test belgesinde belirt; üretimde varsayılan ortak parola bırakma. Test kayıtlarını ve sentetik zamanları gerçek gözlem gibi gösterme. Üretim dışa aktarması test verisini varsayılan dışlasın.

## Test ve teslim belgeleri

Şunları üret/güncelle:
- `README.md`: proje, yerel çalıştırma, dizin yapısı.
- `INSTALL_SHARED_HOSTING.md`: gerçek dosya yolları, veritabanı oluşturma/import, yapılandırma, vendor yükleme, izinler, HTTPS, alt klasör, kurulum kilidi, yedek/geri yükleme, güncelleme/rollback, streaming ve API testi.
- `TEST_DATA.md`: seed komutu/yolu, hesaplar, fixture haritası, mock modları, sentetik veri uyarısı.
- `MANUAL_TESTS.md`: mevcut rehberi gerçek ekran/düğme adlarıyla güncelle; adım, beklenen sonuç, kanıt ve geri bildirim alanı.
- `TEST_RESULTS.md`: gerçekten çalıştırılan testler, komut/ortam/sonuç; çalıştırılmayanlar açıkça işaretli.
- `DATA_DICTIONARY.md`, `EVENT_CATALOG.md`, `ARCHITECTURE.md`, `AI_INPUT_CONTRACTS.md`.
- `BUGS.md`, `CHANGELOG.md`, `plan.md`, `CONTEXT.md`.

Otomatik testler: yetki/IDOR, CSRF, durum kilitleri, idempotency, autosave revision, AI sırasında eşzamanlı autosave, bağımsız analiz payloadları, sayısal hesaplar, etiket sürümleri, olay dedup, süre çakışması, yayın kapısı, dosya biçimleri ve dışa aktarma sızıntıları. Araç yoksa geçildi yazma; çalıştırılamadı ve gerekçesini yaz.

## Çalışma biçimi ve devamlılık

Önce depoyu incele; mevcut işi silip yeniden başlama. `plan.md` içinde küçük, test edilebilir aşamalar oluştur. Her aşamada kod + test + dokümantasyon ilerlesin. Bir aşamayı yalnızca dosya var diye tamamlandı sayma; kanıt ekle. İş bitiminde `CONTEXT.md` içine mevcut durum, son değişiklik, sonraki adım, açık hata ve test komutlarını yaz. Gerçek sır/kişisel veri ekleme.

Kullanıcı manuel test yapıp dönüt verecek. Her hata için yeniden üretme, beklenen/gerçekleşen, ortam, önem, kök neden, düzeltme ve regresyon testi kaydet. Hata düzeltmesinde eski veriyi bozma. Tüm sistemi yeniden üretmek yerine ilgili değişikliği yap. Kullanıcı testi onaylanmadan üretime hazır deme.

Son yanıtında: ne geliştirildi, ne test edildi, ne test edilemedi, hosting kurulumu için hangi dosyalar kullanılacak, kullanıcının ilk manuel test adımları ve bilinen sınırlılıklar. Gerçek AI/hosting doğrulaması yapılmadıysa açıkça söyle.
