# VakaLab 0.3.1 · Tek rapor güncellemesi

Bu paket mevcut `vakalab.techdocsolution.com` kurulumunuz içindir. Tek AI değerlendirme isteği, soru–yanıt–geri bildirim–puan düzeninde tek rapor, her soruya ait rubrik puanları, manuel değerlendirme ve profil karşılaştırması içerir. Hazırlanan sonuç otomatik görünür.

1. DirectAdmin üzerinden mevcut uygulama klasörü ve veritabanı yedeğini public dışında alın.
2. `vakalab-soru-raporu-0.3.1.zip` dosyasını `/domains/vakalab.techdocsolution.com/vakalab` klasörüne yükleyin.
3. ZIP'i **aynı klasöre** çıkarın. İçindeki `app` ve `public` mevcut klasörlerle birleşsin; dosyaların üzerine yazılmasına izin verin. Ayrıca bir `vakalab` alt klasörü oluşmamalı.
4. Mevcut `config/local.php`, `storage` ve `vendor` dizinlerini koruyun. Yeni SQL içe aktarmayın. Mevcut şema 001–003 yeterli; seed/reset çalıştırılmaz. Site kökü `/domains/vakalab.techdocsolution.com/vakalab/public` kalır.
5. Güncelleme bitince uygulama sekmesini bir kez yenileyin. Öğrenci-vaka dosyasında **Değerlendirme ve rapor** başlığı ve her soru için yanıt, geri bildirim ve puan alanları görünmeli.

Deneme vakasında rubriği seçip **AI ile rapor ve puanları hazırla** düğmesine bir kez basın. Vaka, seçilen rubrik ve her soruyla eşleştirilmiş öğrenci yanıtları tek istekte gönderilir. Bittiğinde rapor ve puanlar F5 gerektirmeden görünür. Her sorunun kendi yanıtı, geri bildirimi, puanı ve puan gerekçesi aynı bölümde görünür. Geri bildirimleri ve ölçüt düzeylerini değiştirebilirsiniz. AI kullanmadan da her soruya geri bildirim yazıp ölçüt düzeylerini seçerek **Raporu ve puanları taslak olarak kaydet** düğmesiyle kaydedebilirsiniz.

**Raporu onayla** ve **Öğrenciye göster** ayrı işlemlerdir. Öğrenciye gösterme için veri toplama kapalı olmalıdır. Rapor değişirse eski onay ve yayın geri alınır. Önceki ayrı-adım değerlendirmeleri otomatik dönüştürülmez; yeni alanda soru bazında bir rapor hazırlayın. Eski öğrenci yanıtları ve değerlendirme kayıtları korunur.

Word/PDF/Excel indirmeleri aynı raporun ölçüt puanlarını ve profil karşılaştırmasını içerir. Her sorunun puanı rubrik ağırlıklarıyla uygulamada hesaplanır. Genel toplam soru puanlarının toplamıdır. Profil değişimi başarı veya nedensellik iddiası değildir.

Yerel Chrome, SQLite ve MariaDB testleri geçti. Bu güncellemede gerçek sağlayıcıya ücretli test çağrısı yapılmadı; canlı hosting kontrolü yükleme sonrasında deneme vakasında yapılmalıdır.

Sorun çıkarsa yeni bir AI isteği başlatmadan hata metnini ve sunucu kaydını kontrol edin. Paket kod dosyaları içerir; SQL, parola, API anahtarı veya öğrenci verisi içermez. Geri dönüş gerekiyorsa güncelleme öncesi kod yedeğini kullanın; üretim veritabanını sıfırlamayın.
