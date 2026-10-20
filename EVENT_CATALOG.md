# Olay kataloğu ve süre kuralları

Ölçüm sürümü: `measurement-v1`. Varsayımlar yapılandırılabilir: autosave 1200 ms; heartbeat 15000 ms; idle 60000 ms. Gerçek araştırma öncesi eşikleri araştırmacı doğrulamalı.

| type | Gözlem |
|---|---|
| section_open | bölüm ilk/tekrar açıldı; payload.locked kilit açıklamasını ayırır |
| field_focus | alan odağı; field kimliği |
| text_changed | metin düzenleme; field, metnin kendisi revision tablosunda |
| profile_changed | profil veya nihai güven seçimi |
| question_changed | gönderilmemiş soru taslağı |
| copy | uygulama içi kaynak, vaka sürümü, revision, başlangıç/bitiş aralığı, uzunluk ve en fazla 6000 karakter pasaj |
| paste | uygulama içi yapıştırma ve hedef alan; genel pano okunmaz |
| scroll | sohbet kaydırması |
| visibility / focus | sayfa görünürlüğü / pencere odağı |
| heartbeat | gözlem sınırı; okuma kanıtı değil |
| offline / online | tarayıcı bağlantı gözlemi; sunucu erişilebilirliği garantisi değil |
| recovered | aynı hesaba ait sekme taslağı kurtarıldı |
| response_visible | tam yanıt geldi; in_ai ve visible ayrı; okundu anlamına gelmez |
| page_exit | gözlenebilirse çıkış; ağ teslimi garanti değil |

Her istemci olayında client_key UUID, tab, client_seq, client_at; sunucuda ayrıca session_id, received_at, quality. Olay batch'i en fazla 100; app + client_key unique. sessionStorage kuyruğu kullanıcı + uygulama + vaka sürümüyle ayrılır; başka hesapla açılmaz. Tarayıcı kapanırken gönderim garanti edilmez; son aralık bilinmiyor kalır. Metin/kaydırma olayları 700 ms ortak örnekleme penceresiyle seyreltilir; eksiksiz tuş kaydı iddiası yok.

Sunucu kayıtları: audit.save/initial/submit revisionla eşleşir; messages gönderim sırasını, ai_requests + ai_attempts gerçek sağlayıcı başlangıç/bitiş/hata/kısmi durumunu taşır. Bunlar istemci gözleminden ayrı kulvardır. İlk görünür parça **ölçülmüyor**; sahte zaman/streaming animasyonu yok.

Ziyaret türetimi: session+tab içinde client_seq sırasıyla bitişik iki gözlem arasındaki en fazla 30 saniye ölçülür. Negatif zaman, sıra boşluğu, uzun boşluk ve çıkış sonrası aralık `unknown_gap`, saniye null. Son gözlemden sonra süre uzatılmaz. Bölüm, visible/focused/interactive ayrı tutulur. Görünür+odaklı aralıkların birleşimi toplamda üst üste binmeyi önler. AI üretim saniyeleri buna eklenmez. İstemci saati doğrulanmamıştır; offline sırası ve server alınma zamanı ayrı korunur. Okudu/düşündü/bekledi/benimsedi gibi yorum yok.

Bilinen sınır: seçim aralığı metin alanlarında alan içidir; vaka/mesaj DOM seçiminde kaynak içi karakter sayısıdır. Başlık/içerik karışımı kopyalarda kaynak aralığı null olabilir. Tarayıcının yakalayamadığı kopyalama yok sayılmaz. Gerçek cihaz/tarayıcı doğrulaması bekleniyor.

0.1.2 bölüm güncellemesi: izinli section kodları case, profile, initial, ai, final, reflection, **confirm**. confirm ayrı Son teslim sekmesidir; açılış/heartbeat/odak/süre gözlemleri diğer bölümlerle aynı kuralları izler. Geçmiş olay kodları değiştirilmez.

## 0.1.8 — Türkçe dışa aktarma açıklamaları
`events` çıktısına eklenen alanlar: `type_tr` (Türkçe olay adı), `section_tr` (Türkçe bölüm), `event_category` (Gezinme / Alan etkileşimi / Teknik durum / Yanıt durumu), `event_description` (gözlemin anlamı ve sınırı). Özgün `type`, `section`, UTC zamanları, satırlar ve DB kayıtları değişmez. Excel/CSV/JSON aynı açıklamaları taşır. `dictionary`,16 olay ve7 bölüm kodunu ve temel alanların kullanımını açıklar; veri filtreleri boş sonuç verse de sözlük doludur.
Process mining için süreç örneği `app_id` (öğrenci×vaka çalışması), etkinlik kodu `type`, bölüm bağlamı `section` olarak kullanılabilir. `case_id` tek başına süreç örneği değildir. `client_at` istemci zamanı, `received_at` sunucuya ulaşma zamanıdır; çevrimdışı gecikme ve istemci saatinin doğrulanmamış olması hesaba katılmalıdır. Aynı sekme/oturum içi sıra `client_seq` ile kontrol edilir. `heartbeat` periyodik teknik gözlemdir; kullanıcı eylemi değildir. Analizin amacına göre Teknik durum grubu filtrelenebilir; dışa aktarma otomatik satır silmez. Başka bölümlerde aynı olay türünü ayırmak için `type_tr` ve `section_tr` birlikte okunabilir. Hiçbiri okuma/düşünme kanıtı değildir.
