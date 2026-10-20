# VakaLab mimarisi — aşama P01

0.2.1: Genel durumda matris yerine vaka/öğrenci listesi, vaka kartında atanan öğrenci diyaloğu; JSON gerektirmeyen rubrik boyut/düzey formu ve okunaklı/aranabilir/JSON indirilebilir işlem geçmişi. Şema değişikliği yok. Güncel koşular TEST_RESULTS.md, kullanım RELEASE_READINESS.md.

0.2.0: Migration 002 users/cases/definitions.owner_id, cases.collection_closed_at, assignments.allow_late, definitions.deleted_at ekler. Scope her araştırmacı isteğinde kayıt sahipliğini kontrol eder; alt analiz/karar/rapor/mesaj işlemleri application → case sahibine gider. Mevcut kayıtlar ilk araştırmacıya bağlanır; eski içerik ve kimlikler korunur. Etiket/rubrik Sil tüm tanım zincirini yeni seçimlerden kaldırır, tarihsel başvuruları korur. Vaka kapanışını açma/kapama kişisel ek süreleri sıfırlar.

PHP 8.3+, PDO MySQL / MySQL 8 veya MariaDB 10.6+, InnoDB, utf8mb4. Vanilla JS/CSS; build, daemon, cron, Redis zorunlu değil. Yalnız public/ web kökü. CLI kurulum ve seed; web kurucusu yok. Testlerde SQLite ayrı geçici dosyada kullanılabilir; bu MySQL doğrulaması yerine geçmez.

Katmanlar: public/api.php → Auth → App/domain → Db. AI ayrı adapter; istek önce kısa transaction ile rezerve edilir, transaction ve PHP session kilidi bırakılır, sonra sağlayıcı çağrılır. Tek aktif istek uygulama satırında tutulur. Attempt ID geç gelen yanıtı sınırlar; otomatik stale yeniden çağırma yok. Kesinti araştırmacının gerekçeli kapatma işlemini gerektirir. Gerçek streaming henüz yok; tam yanıt aktarımı ve ilk parça ölçümü yok bilgisi kullanılır.

Taslak revision karşılaştırması ve uygulama satırı kilidi bütün yazmalarda ortak. Başlangıç ve teslim aynı transaction içinde snapshot üretir. Her uygulama başladığı vaka sürümünü korur. Oturum cookie HttpOnly/SameSite, HTTPS üretimde zorunlu; CSRF her yazmada. Giriş rate limit DB'de. Parolalar hash. AI metni plain text render edilir.

Analiz tek teslim edilmiş uygulamada sekiz sıralı adım. Her deneme append-only; karar ve rapor ayrı. Onay snapshot üretir; yayın ilgili vaka kapanışı ve kişisel ek sürenin kapalı olmasını gerektirir; öğrenci indirmesinde de kapı tekrar kontrol edilir. Sade indirme ekranı seçilen vaka veya çalışma alanındaki tüm veriyi (test/deneme/analiz dışı dahil) açıkça indirir. Eski düşük seviyeli export filtreleri API uyumluluğu için korunur. Kodlanmış export serbest metni de içermez: metindeki kimliği güvenle otomatik anonimleştirmek mümkün değildir.

Varsayımlar: autosave 1200 ms, heartbeat 15 s, idle 60 s (measurement-v1); araştırmacı tetiklemeli etiketleme; her araştırmacıya ayrı sahiplik kapsamı; ortak vaka üyeliği yok. Gerçek kurum onamı/rubrik/AI ve hosting henüz doğrulanmadı.

## 0.2.4 — Okunabilir süreç ve etiketler
public/assets/process.js gösterim/alan değişikliği/süre özetlerini üretir; özgün kayıtlar değişmez. app/MessageLabels.php bağımsız ve analiz6 etiket önerilerini doğrular, üretim gruplarını mevcut audit tablosunda saklar ve ortak güncel seçim kuralını Export için uygular. Sağlayıcı çağrısı transaction dışında, sonuç kaydı sahiplik/konuşma/tanım kontrolünden sonra yapılır. Yeni şema gerekmez.

## 0.2.5 — Navigation ve teslim sınırı
Navigation.php click-to-click ziyaretlerini ham telemetriyi değiştirmeden türetir; Telemetry::visits foreground ölçümü ayrı kalır. Yeni istemci view_id/section_leave/workflow_confirmed/message_send olayları, çalışma-özel kuyruklar ve pagehide keepalive ile kapanış gözlemini destekler. Son teslimde istemci heartbeat ve olay üretimi durur; backend gecikmiş teslim-öncesi kayıtlar dışında yeni gezinmeyi kaydetmez. Export::disco varsayılan ziyaret, view=events ayrıntılı olay profilidir. Yeni migration yoktur.
