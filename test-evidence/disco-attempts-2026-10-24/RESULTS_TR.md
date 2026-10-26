# Disco deneme ayrımı — 24 Ekim 2026

Son tam koşu: **38/38 yerel test paketi**, **78/78 sözdizimi ve diff kontrolü** başarılı. Toplam **116/116**, sıfır hata.

Makine çıktısı: [results.json](20261024T194310Z/results.json). Her paketin ayrı günlüğü aynı klasördedir. Test edilen kaynak dosyalarının SHA-256 özetleri [source-sha256.json](20261024T194310Z/source-sha256.json) içindedir. Önceki başarısız koşular silinmedi.

## Disco davranışı

- Geçiş/süre ve ayrıntılı olay CSV'lerinin her satırında **Deneme numarası** bulunur. Kesinleştirme/teslim satırları ve boş CSV başlıkları da kapsanır.
- Aynı öğrencinin iki denemesi farklı **Case ID**, aynı **Participant** ve **Vaka ID** taşır. Toplu indirmede tek dosya içinde iki bağımsız süreçtir. Deneme içinden indirmede yalnız seçilen süreç çıkar.
- Sıra numarası ve önceki/sonraki etkinlik bağlantıları her Case ID için yeniden başlar. Her denemenin teslim sınırı ayrıdır. Denemeler arasındaki bir günlük bekleme çalışma süresine eklenmez.
- Dışa aktarmanın uygulama, snapshot, sohbet, olay, analiz veya rapor tablolarını değiştirmediği doğrulandı.
- Disco içe aktarımında **Case ID → Case**; **Deneme numarası → ek özellik/filtre** seçilir. Bu koşuda Disco masaüstü uygulaması açılmadı; üretilen CSV'ler, zamanlar, kimlikler, süreler ve gerçek HTTP indirmeleri doğrulandı.

## Kapsam

| Grup | Sonuç |
|---|---|
| PHP yerel test paketleri | 17/17 |
| JavaScript/tarayıcı/HTTP test paketleri | 18/18 |
| Python HTTP, erişim/yayın ve MariaDB paketleri | 3/3 |
| PHP, JavaScript, Python sözdizimi + diff | 78/78 |
| İki denemeli Disco kontrolleri | SQLite 35/35, MariaDB 35/35 |
| Yeni deneme sunucu kontrolleri | SQLite 39/39, MariaDB 38/38 |
| Yeni deneme ve gerçek CSV indirme tarayıcı kontrolleri | 15/15 |
| Tam Chrome senaryosu | 142/142 |
| Karma gezinme/süre/CSV karşılaştırması | 27/27 |
| Bağımsız DOM gözlemiyle odak/süre senaryosu | 24/24 |
| Değerlendirme tarayıcısı | 45/45 |
| Erişim/yayın HTTP senaryoları | 119/119 |

MariaDB testleri üç ayrı test veritabanında temel akış, yeni deneme ve Disco ayrımını çalıştırır; yedek/geri yüklemede sekiz tablo sayısı ayrıca karşılaştırılır. Yerel paketler sentetik veriler ve mock AI kullanır. Çalışma veritabanında öğrenci adına işlem yapılmadı.

## Tam koşuda bulunan ve giderilenler

- Değerlendirme düzenleyicisi, kayıt sonrası güncel görünüm yüklenmeden kapanabiliyordu. Artık görünüm yenilendikten sonra kapanır; hemen yeniden açıldığında son puan ve yorum görünür.
- Eski testlerdeki odak olay adları ve rapor göster/geri al düğmesi beklentileri güncel arayüzle eşitlendi. Etiket test verilerine zorunlu rol/kapsam alanları eklendi; üretim yetki kuralları değiştirilmedi.
- Pencere geri yüklemesi ek gerçek odak değişimleri üretebildiği için testteki sabit aralık sayısı varsayımı kaldırıldı. Bağımsız DOM odak gözlemcisiyle her dışarıda kalma başlangıç/bitişi karşılaştırılır; uygulamanın süre hesaplama kuralları ve ölçülen aralıklar değiştirilmedi.

## Çalıştırılamayan canlı sağlayıcı testleri

Altı canlı sağlayıcı test dosyası bu sonucun kapsamına dahil değildir: `openrouter-prompts.php`, `label-catalog-live.php`, `student-labels-live.php`, `student-label-overrides-live.php`, `scoring-live.php`, `live-workflow.php`.

Canlı testleri başlatma komutu **otomatik onay denetimi tarafından reddedildi**. Belirtilen gerekçe: sentetik soruların ve yerel etiket tanımlarının yapılandırılmış harici AI sağlayıcısına gönderilmesi ve ücret oluşabilmesi için kullanıcının hedef/payload açısından ayrıca açık onayı gerekiyor. Bu komut çalışmadı; bu koşuda gerçek AI çağrısı yapılmadı. Canlı testler başarılı sayılmadı ve ret başka bir yolla aşılmadı.

## Yeniden çalıştırma

`python tests/run-regression.py` bütün yerel paketleri sırayla çalıştırır; her yeni koşu için ayrı kanıt klasörü ve kaynak özeti üretir. Windows'ta `PHP_BIN`, `PLAYWRIGHT_PATH`, `CHROME_BIN`, `MARIADB_HOME` ortam değişkenleri kurulu araçlara göre ayarlanmalıdır. Canlı sağlayıcı bayrakları bu çalıştırıcıda bilerek kaldırılır.
