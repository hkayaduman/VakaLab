# Öğrenci UI sonrası tam yerel regresyon — 28 Ekim 2026

Son tam koşu **135/135 başarılı**: **46 işlevsel test paketi** ve **89 sözdizimi/diff kontrolü**. Son koşuda başarısız paket yoktur. Kaynak manifestindeki 88 dosya koşu sonunda yeniden karşılaştırıldı; içerikleri aynıdır. Öğrenci stil dosyası dahil ek kaynak özetleri `additional-source-sha256.json` içindedir.

Tam koşu ve tüm paket günlükleri: [20261028T155714Z](../disco-attempts-2026-10-24/20261028T155714Z/results.json). Bu dizinin 24 Ekim adlı üst klasörü mevcut çalıştırıcının sabit çıktı konumudur; koşunun kendisi 28 Ekim tarihlidir.

## Sekmeler ve süreler

- `navigation-timing-browser.cjs`: 39/39. Gerçek Chrome tıklamalarıyla yedi sekmenin her biri için başlangıç, bitiş ve sonraki hedef kontrol edildi. Uygulama hesabından bağımsız `performance.now()` farklarıyla karşılaştırıldı. Bağımsız toplam 3,0218 sn, kaydedilen toplam 3,021 sn; toplam fark 0,8 ms. Tek aralıktaki en büyük fark 10,2 ms, test toleransı 150 ms. UI toplamları API değerlerinin beklenen yuvarlamalarıyla eşleşti. Kısa tarayıcı aralıklarına ek olarak uzun aralıklar deterministik alan testlerinde kontrol edilir.
- `navigation.php`: 82/82; `presence.php`: 60/60. Geri dönüşler, kilitli bölümler, eksik kapanış, geri giden saat, eşzamanlı AI beklemesi ve ölçülemeyen süreler.
- `presence-browser.cjs`: 24/24. Başka tarayıcı sekmesine geçiş, dönüş, AI yanıtının dışarıdayken gelmesi ve pencereyi küçültme. Odak dışı süre bölüm süresine eklenmedi.
- `journey-browser.cjs`: 27/27. İki vaka arasında gidip gelme, başka sekmeye geçiş, AI beklemesi, teslim iptali ve başarılı teslim. **15,016 sn = 5,590 sn bölüm içi + 6,439 sn odak dışı + 2,987 sn vaka dışı**. Kapalı aralıklarda boşluk/çakışma yok. AI bekleme kulvarı öğrenci toplamına yeniden eklenmedi. Teslim sonrası takip durdu.
- Karma yolculukta gerçekten indirilen 20 satırlı CSV'nin süreleri ham zaman farklarıyla, benzersiz kimlikleri, sıra ve önceki/sonraki bağlantılarıyla ve UI/API toplamlarıyla eşleşti. Denemelerin ayrı Case ID değerleri ve deneme numaraları `disco-attempts.php` ile SQLite ve MariaDB üzerinde doğrulandı.

Hassas ölçümler `navigation-summary.json` ve `journey-summary.json`; sentetik indirme `journey.csv` içindedir.

## İndirilen veriler

Yeni `tests/export-roundtrip.php` tam koşuya dahildir ve **267/267 kontrol** geçti. Bellek içi SQLite ve mock ile oluşturulan 25 veri tablosunun ham/kodlanmış sürümleri CSV, JSON ve Excel olarak üretildi ve yeniden açıldı:

- Her CSV'nin başlıkları, satır sayısı ve bütün hücreleri kaynak veriyle karşılaştırıldı; UTF-8 BOM doğrulandı.
- JSON yeniden okunduğunda tüm tablolar ve değerler korundu.
- Excel'de **4.857 veri hücresi** ile sütun adları ve satır sayıları kaynaktan karşılaştırıldı. Boş tabloların boş sayfaları da kontrol edildi.
- Yedi sekmeli sabit 70 saniyelik yolculukta 4 saniyelik odak dışı aralık ve iki kesinleştirme/teslim işareti ayrı kaldı.
- Türkçe karakterler, tırnak, virgül/noktalı virgül, satır sonları ve gerçek sıfır ölçümleri korundu. CSV formül kaçışları kontrol edildi.
- Kodlanmış çıktıda kimlik, öğrenci yanıtı ve özel not bulunmadı; ham çıktı ilgili sentetik metinleri korudu.
- Export işlemi uygulama, snapshot, mesaj, olay, rapor, taslak sürümü ve iç not tablolarını değiştirmedi.

Mevcut HTTP/tarayıcı testleri gerçek indirme endpointlerini de denetledi. Word/PDF/Excel rapor paketleri, soru–yanıt–geri bildirim eşleşmeleri, yayın/onay/geri alma, özel alanların öğrenciye gitmemesi ve yetkisiz indirme engelleri geçti. Bu kontroller biçim/içerik doğrulamasıdır; tüm sayfaların fiziksel Word uygulamasında görsel kabulü anlamına gelmez.

## Diğer kontroller

- Ana tarayıcı 115, zorunlu alan/teslim 50 kontrol; sohbet kurtarma, taslak, tekrar deneme, eski teslim ve rapor uyumluluğu dahil bütün yerel paketler geçti.
- MariaDB 11.4.12: bağlantı boşta kalma, ana iş akışı, yeni deneme, Disco, bütüncül rapor ve inceleme regresyonları ayrı veritabanlarında geçti. Yedek ayrı veritabanına geri yüklendi; sekiz tablonun satır sayıları eşleşti. Bu, bütün içeriklerin hash karşılaştırması olarak sunulmaz.
- Ek görsel kontrol: 320/390/768/1440 pikselde yedi bölüm, toplam 28 görünüm. Sayfa taşması yok, aktif sekmeler görünür, alanlar/düğmeler ekrana sığıyor. Öğrenci stili kapatılıp açılarak araştırmacı ekranının seçilen hesaplanmış stillerinin aynı kaldığı doğrulandı (`responsive.txt`).

## İlk koşu ve tekrar

İlk tam koşu [20261028T155331Z](../disco-attempts-2026-10-24/20261028T155331Z/results.json) **başarılı sayılmadı**; iki hata korunur:

1. MariaDB araç yolu üst klasöre işaret ediyordu; `mariadb-11.4.12-winx64` alt klasörüne düzeltildi. Ürün kodu değişmedi.
2. Odak testinde ikinci AI beklemesi sırasında beklenen tek ayrılmaya ek 100 ms odak dışı kayıt oluştu. Test tam iki bekleme parçası beklediği için durdu; kayıtta üç parça vardı. Ayrılmaları çıkardıktan sonra beklenen iç bekleme **5,263 sn**, kayıtlı üç parçanın toplamı da **5,263 sn**: süre kaybı veya çift sayım bulunmadı. İlk hata anındaki sentetik kayıt `failed-presence-fixture.json`, hesap `first-run-presence-diagnosis.json` içinde korunur. Ek odak geçişinin hangi dış uygulamadan geldiği belirlenmedi. Son tam koşuda ek tarayıcı kontrolleri paralel başlatılmadı ve test değiştirilmeden 24/24 geçti. Gerçek odak testleri dış pencere/odak hareketlerine duyarlıdır.

İlk hataları gizlemek veya testleri geçirmek için ürün davranışı ya da mevcut test beklentileri değiştirilmedi. Ek indirme testi yeni eklendi; son tam koşu bunu da içerir.

## Veri ve kapsam sınırı

Testler sentetik, ayrı SQLite/MariaDB verileriyle çalıştı. Çalışma veritabanı açılmadı; `storage/vakalab_test.sqlite` dosyasının testler sırasındaki ilk ve son SHA-256 değeri aynı: `3c92abb6953864f8dc9b855d46f408f44ea512b228740ad530df253c78abed74`.

Gerçek AI sağlayıcısına çağrı yapılmadı; altı isteğe bağlı canlı sağlayıcı betiği yalnız sözdizimi kontrolünden geçti, canlı test başarısı olarak sayılmadı. Canlı hosting, fiziksel mobil klavye/cihaz, Disco masaüstü importu ve AI puanlarının akademik doğruluğu bu koşunun kapsamında değildir. Yerel başarı bütün olası verilerde hatasızlık garantisi değildir. Commit/push/dağıtım yapılmadı.
