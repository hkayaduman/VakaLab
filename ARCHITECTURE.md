# VakaLab mimarisi — aşama P01

PHP 8.3+, PDO MySQL / MySQL 8 veya MariaDB 10.6+, InnoDB, utf8mb4. Vanilla JS/CSS; build, daemon, cron, Redis zorunlu değil. Yalnız public/ web kökü. CLI kurulum ve seed; web kurucusu yok. Testlerde SQLite ayrı geçici dosyada kullanılabilir; bu MySQL doğrulaması yerine geçmez.

Katmanlar: public/api.php → Auth → App/domain → Db. AI ayrı adapter; istek önce kısa transaction ile rezerve edilir, transaction ve PHP session kilidi bırakılır, sonra sağlayıcı çağrılır. Tek aktif istek uygulama satırında tutulur. Attempt ID geç gelen yanıtı sınırlar; otomatik stale yeniden çağırma yok. Kesinti araştırmacının gerekçeli kapatma işlemini gerektirir. Gerçek streaming henüz yok; tam yanıt aktarımı ve ilk parça ölçümü yok bilgisi kullanılır.

Taslak revision karşılaştırması ve uygulama satırı kilidi bütün yazmalarda ortak. Başlangıç ve teslim aynı transaction içinde snapshot üretir. Her uygulama başladığı vaka sürümünü korur. Oturum cookie HttpOnly/SameSite, HTTPS üretimde zorunlu; CSRF her yazmada. Giriş rate limit DB'de. Parolalar hash. AI metni plain text render edilir.

Analiz tek teslim edilmiş uygulamada sekiz sıralı adım. Her deneme append-only; karar ve rapor ayrı. Onay snapshot üretir; yayın global kapanış gerektirir; öğrenci indirmesinde de kapı tekrar kontrol edilir. Test ve deneme veri çıktısı varsayılan dışarıda. Kodlanmış export serbest metni de içermez: metindeki kimliği güvenle otomatik anonimleştirmek mümkün değildir.

Varsayımlar: autosave 1200 ms, heartbeat 15 s, idle 60 s (measurement-v1); araştırmacı tetiklemeli etiketleme; tek çalışma için birden çok araştırmacı hesabına rol desteği. Gerçek kurum onamı/rubrik/AI ve hosting henüz doğrulanmadı.
