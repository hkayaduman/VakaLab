# Veri sözlüğü · schema 001

0.2.0 ek alanlar: users/cases/definitions.owner_id = sahip araştırmacının değişmez kimliği; cases.collection_closed_at = vaka veri toplama kapanış UTC zamanı (NULL açık); assignments.allow_late = kapalı vakada kişisel ek erişim; definitions.deleted_at = yeni seçimlerden kaldırılma UTC zamanı. study.closed_at yalnız tarihsel global kayıttır, güncel kapılarda kullanılmaz. Tam export cases/case_versions/participants/assignments/reports/research_notes tablolarını da içerir; parola/hash/anahtar içermez.

Tüm iç kimlikler değişmeyen 32 hex karakterdir; test fixture kimlikleri `test-` öneklidir. İlişkiler FK ile korunur; tablo silme API'si yok. LONGTEXT alanlar JSON ya da UTF-8 metindir. UTC zamanlar ISO 8601 (`…Z`, mikrosaniye); tarayıcı saat dilimi `config.timezone`. Kimlikli dışa aktarmadaki serbest metin kişisel veri içerebilir.

| Tablo | Bir satır ve temel alanlar | Kural |
|---|---|---|
| users | kullanıcı: id, login, role, status, password hash, metadata, test | login görünür öğrenci no; id değişmez; hash dışa verilmez |
| study | tek çalışma; closed_at, reason, actor | kapanış ayrı; otomatik yayın yok |
| cases | vaka kimliği, title, position, status, practice, test, current_version, stop_access, rubric_id | closed yalnız yeni başlangıcı kapatır; stop_access devamı da durdurur |
| case_versions | case_id + version; body, instructions, notes | body/yönerge append-only; notes yalnız araştırmacı |
| assignments | user_id + case_id, active | unique; kaldırma active=0; geçmiş audit'te |
| applications | öğrenci × vaka; version_id, state, draft, revision, active_request, initial_at, submitted_at, last_section/activity, excluded/reason | unique; state: initial → working → submitted; son bölüm canlı varlık değildir |
| draft_revisions | app_id + revision, previous_revision, body, created_at | yalnız değişen içerikte yeni sürüm; JSON bütün form snapshotı |
| snapshots | app_id + kind (initial/submit); revision, body | iki atomik kesinleşme, immutable |
| messages | app_id + seq, role, content, status, request_id | immutable gönderilmiş metin; taslak soru burada yok |
| ai_requests | app_id + client_key, attempt, model, directive, payload, status, started/completed/error | tek aktif applications.active_request ile seri; payload yalnız allowlist |
| ai_attempts | request_id, id (attempt fence), status, started/completed/error | tekrar denemeler geçmişi ezmez |
| events | app_id + client_key, session_id, tab_id, client_seq, client_at, received_at, type, section, payload, quality | unique dedup; istemci zamanı güvenilir sunucu zamanı değildir |
| definitions | family (rubric/label), name, version, parent_id, body, archived | düzenleme yeni satır; kullanılan eski sürüm korunur |
| label_reviews | message_id, actor, reason, created_at | araştırmacının yeni etiket seçimi; boş seçim de kayıt |
| message_labels | message_id, definition_id, source (ai/researcher), review_id, actor, reason | AI önerisi ayrı; son researcher review güncel seçim, diğerleri tarihçe |
| analysis_runs | app_id, rubric_id, created_at | tek teslim edilmiş uygulama ve sabit rubrik sürümü |
| analysis_steps | run_id + step + attempt; input/output JSON, model, directive, status/error | sıra zorunlu; başarısız denemeler korunur |
| decisions | step_id, actor, action, body, reason, created_at | accept/edit/reject/uncertain; öneri ezilmez |
| research_notes | app_id, actor, body, reason, created_at | araştırmacı iç notu append-only; öğrenciye verilmez |
| reports | app_id + version, run_id, body, approved/published_at, actor | body snapshot değişmez; onay/yayın ayrı; internal eki öğrenci API/indirmesinden çıkarılır |
| audit | actor, action, target, reason, detail, created_at | hesap/vaka/atama/durum değişimleri ve indirmeler |
| rate_limits | hash(IP + login), count, window_start epoch | 15 dakikada 15 giriş denemesi |

Rubrik `body`: `text`, `dimensions[]`: id/name/min/max/weight/levels[]. Her düzey name/score/description taşır. Ağırlıklı sonuç `Σ(puan × ağırlık) / Σ(ağırlık)`; 4 ondalık. Vaka toplamı/ranking yok. Eksik/reddedilmiş/kararsız puan karşılaştırması null; sıfır gibi gösterilmez. Güven farkı nihai − başlangıç (8→10 = +2).

Export tabloları: applications, events, server_events (audit kaynaklı), visits (hesaplanan), messages, labels, label_reviews, drafts, snapshots, ai_requests, ai_attempts, analyses (run/adım), decisions, definitions, dictionary. `labels` olaylarla join edilmez; çoklu etiket olay çoğaltmaz. Kodlanmış çıktı login/ad/eşleme, ham metin, AI payload ve karar metinlerini çıkarır. Uygulama kimliği bağlantıları korunur; bu çıktı anonimlik garantisi değildir.

## 0.1.8 — Türkçe dışa aktarma açıklamaları
`events` çıktısına eklenen alanlar: `type_tr` (Türkçe olay adı), `section_tr` (Türkçe bölüm), `event_category` (Gezinme / Alan etkileşimi / Teknik durum / Yanıt durumu), `event_description` (gözlemin anlamı ve sınırı). Özgün `type`, `section`, UTC zamanları, satırlar ve DB kayıtları değişmez. Excel/CSV/JSON aynı açıklamaları taşır. `dictionary`,16 olay ve7 bölüm kodunu ve temel alanların kullanımını açıklar; veri filtreleri boş sonuç verse de sözlük doludur.
Process mining için süreç örneği `app_id` (öğrenci×vaka çalışması), etkinlik kodu `type`, bölüm bağlamı `section` olarak kullanılabilir. `case_id` tek başına süreç örneği değildir. `client_at` istemci zamanı, `received_at` sunucuya ulaşma zamanıdır; çevrimdışı gecikme ve istemci saatinin doğrulanmamış olması hesaba katılmalıdır. Aynı sekme/oturum içi sıra `client_seq` ile kontrol edilir. `heartbeat` periyodik teknik gözlemdir; kullanıcı eylemi değildir. Analizin amacına göre Teknik durum grubu filtrelenebilir; dışa aktarma otomatik satır silmez. Başka bölümlerde aynı olay türünü ayırmak için `type_tr` ve `section_tr` birlikte okunabilir. Hiçbiri okuma/düşünme kanıtı değildir.

## 0.2.4 — Mesaj etiketleri ve Disco sütunları

label_batches: audit içindeki message_labels_ai üretim gruplarının id/app_id/created_at/detail çıktısıdır. detail; batch_id, labels (mesaj kimliği → etiket kimlikleri; boş dizi eşleşme yok), hash (mesaj/tanım sürümü), origin, model ve directive içerir. message_labels.review_id bu grubu, araştırmacı kaydında label_reviews.id'yi gösterir. Önceki kayıtlar silinmez.

Disco'da Message ID/Message Role mesajı tanımlar. AI Labels son öneri; Researcher Labels son araştırmacı seçimi; Effective Labels araştırmacı kararı varsa o seçim, yoksa AI önerisidir. Araştırmacının açık boş seçimi AI önerisine geri dönmez. Label Source researcher/ai/none, Label Status ise incelendi/inceleme bekliyor/etiketleme yapılmadı ayrımını verir. Birden fazla etiket adı aynı hücrede | ile ayrılır; kimlikler ve tam inceleme geçmişi Excel/JSON tablolarında korunur. Mesaj olaylarının başlangıç/bitişi aynı sunucu kayıt zamanıdır; mesajın okunduğunu göstermez. Mevcut AI yanıt aralıkları ayrı kaynakta kalır.

## 0.2.5 — navigation_visits ve navigation_milestones

navigation_visits: app_id, visit_id/start_event_id/end_event_id, trace_id (oturum/sekme/açılış örneğinden tek yönlü kod), visit_number, section, start_at/end_at, duration_seconds, activity/activity_code, edited_fields, request_keys, end_reason, quality, background_seen, read_only_tab, missing_events, sequence ve overlapping_tab. duration_seconds bölüm açılışı ile gözlenen kapanış farkıdır; bilinmiyorsa null. Aynı sekmeye dönüş yeni visit_id'dir. Geçen süre görünür/odak veya dikkat süresi değildir. Tek trace içi sıra client_seq ile, CSV süreç sırası başlangıç zamanı ve sınır olay sırasıyla belirlenir; aynı anda açık sekmeler tek doğrusal davranış varsayımıyla yorumlanmamalıdır.

navigation_milestones: app_id/id/kind/at/source/trace_id/sequence. kind initial/submit, source client_confirmation; eski kayıtta istemci doğrulaması yoksa CSV'de server_snapshot nokta olayı kullanılabilir. Eski kaydın eksik ziyaret kapanışı sunucu zamanından uydurulmaz.

Disco varsayılan view=visits: UTC başlangıç/bitiş; Duration Seconds tam hassasiyeti, Activity Code, Sequence, Previous Activity/Next Activity, Trace ID, Edited Fields ve Excluded Unclosed Visits. AI Label Match Quality=request_key_matched yalnız gerçek istek anahtarıyla eşleşmede kullanılır; eski kayıtta anahtar yoksa ziyaret amaçları zaman yakınlığından uydurulmaz. AI alt olayları view=events ile ayrıca indirilir. Post-submission olayları araştırma akışı dışında bırakılır; eski ham kayıtlar silinmez. Yeni olay tipleri section_leave, workflow_confirmed ve message_send; kabul edilen toplam tip sayısı 19'dur.

## 0.2.6 — Ziyaret adı, hedef bölüm ve kilit

navigation_visits activity = gerçek sekme adı + görüntüleme/güncelleme. Güncelleme, o ziyarette text_changed/profile_changed/question_changed gözlemiyle en az bir alan değişiminin kaydedilmesidir; kesinleştirme veya sunucuya kalıcı kayıt başarısı anlamına gelmez. Kilitli ziyaret gerçek bölüm adıyla görüntüleme olarak kalır; locked ayrıca true olur. Teknik activity_code mevcut _VIEW/_EDIT/_LOCKED kodlarını korur. next_section ve next_section_name yalnız next_section kapanışında gerçek hedef bölümü gösterir; diğer kapanışlarda null. CSV karşılıkları Next Section, Next Section Name, Locked (yes/no). Previous Activity/Next Activity sıralı CSV komşusudur; Next Section doğrudan aynı ziyaretin kapanışındaki hedeftir. background_seen en az bir görünmez sayfa gözlemidir; nedenini veya görünmezlik süresini kanıtlamaz ve geçen süreden çıkarılmaz. Ham olaylar ve eski rapor snapshotları değiştirilmez.

## 0.2.7 — AI ziyaret etkinliği ve güncel amaç etiketleri

AI ziyaretinde soru taslağını değiştirmek mesaj göndermek sayılmaz: AI sohbeti görüntüleme / AI_VIEW. Gerçek mesajlar request_keys → ai_requests.client_key → messages.request_id ile eşleştirilir; yalnız user rolü, ilgili uygulama ve ilgili ziyaret alınır. Araştırmacı seçimi (boş dahil) AI önerisine önceliklidir. activity = AI sohbeti · güncel etiket adları; birden fazla ad mesaj sırasıyla, tekilleştirilip + ile ayrılır. activity_code=AI_MESSAGE. ai_question_count, ai_labels, researcher_labels, effective_labels, label_sources türetilmiş ziyaret alanlarıdır. Etiket henüz yok, eşleşen etiket yok ve araştırmacının etiket seçmemesi ayrı adlandırılır; gönderme olayı gerçek mesaja eşleşmezse AI_SEND_ATTEMPT / Mesaj gönderme denemesi olur. Eski kayıtlarda request_key eksikse zaman yakınlığından amaç uydurulmaz. Ziyaret süresi bölünmez/çoğaltılmaz; ayrıntılı mesaj sırası Disco view=events içinde korunur. Aynı türetim UI/Excel/JSON/Disco ziyaret Activity alanında kullanılır; etiket değişince sonraki görüntüleme/indirmede ad da değişir. Ham olaylar ve rapor snapshotları korunur.

## 0.2.8 — navigation_activities: soru, yanıt ve görüntüleme aralıkları

Bu kural 0.2.7'deki ziyarette amaçları tekilleştirme/birleştirme davranışının yerini alır. navigation_visits özgün bölüm ziyaretini ve toplam süresini korur. message_sends (request_key/at/event_id/sequence) ve response_events (request_id/status/at/event_id/sequence) ham istemci kayıtlarından türetilir. message_activities mesaj sırasını ve tekrarları korur; tek mesajda birden fazla kategori olabilir. UI navigation.activities ve Excel/JSON navigation_activities, AI ziyareti içindeki soru/yanıt/görüntüleme sınırlarını ayrı satırlara açar. Diğer bölümler özgün aralık olarak kalır.

AI_VIEW ilk soruya kadar ve yanıt geldikten sonraki görüntüleme aralığıdır. AI_MESSAGE gönderimden kaydedilen yanıt gelişine kadar geçen süredir; sağlayıcının sunucu işlem süresi değildir, ağ ve istemcide tamamlanmayı gözleme gecikmesini de içerir. AI_RESPONSE sıfır süreli yanıt geliş olayıdır; completed/partial/failed durumları farklı adlandırılır. Yanıtın gelmesi, okunduğunu kanıtlamaz. Öğrenci başka bölümdeyse o bölüm korunur; yanıt yalnız sıfır süreli bildirim olayı olur, yapay AI_VIEW eklenmez. Son teslimden sonraki izleme kapalı kalır.

activity_id alt aralık için benzersizdir; visit_id/Parent Visit ID ortak özgün ziyaret kimliğidir. activity_type = ai_question/ai_response/ai_view (diğerleri visit); message_id/message_sequence, start_at/end_at, duration_seconds, start_sequence, boundary_order ve response_observed bulunur. Aynı zamanda yanıt önce, görüntüleme sonra sıralanır. end_reason=response_arrived veya next_question gerçek ara sınırı belirtir. Eksik yanıt gelişinde süre sonraki gözlenen soru/bölüm/çıkış sınırına kadar kalır; Response Arrival Observed=no ve ekran açıklaması bunu belirtir. Açık uçta null, ters/bozuk ara saatte AI_UNSPLIT olur; yanıt zamanı sunucu saatinden uydurulmaz. Görünmezlik gözlemi özgün ziyaret düzeyinde korunur; her soruya kopyalanmaz.

Disco varsayılanı navigation_activities kullanır. Her aralığın gerçek başlangıç/bitiş ve Duration Seconds alanı vardır; tam AI ziyaret süresi ayrıca CSV'ye eklenmez. Row Type, Parent Visit ID, Message ID/Sequence, Response Arrival Observed ek açıklama alanlarıdır. Yanıt noktaları sıfırdır, toplamı artırmaz. Ayrıntılı ham olay/AI CSV'si ayrı view=events olarak korunur. Şema değişikliği ve eski kayıt/rapor gövdesi değişikliği yoktur.

## 0.2.9 — ai_question_legacy

Gerçek öğrenci mesajı mevcut fakat eşleşen istemci message_send olayı yoksa soru artık gizlenmez. navigation_activities içine ai_question_legacy / AI_MESSAGE satırı eklenir. Güncel araştırmacı seçimi, boş seçim dahil, AI önerisine önceliklidir; etiketlenmemiş mesaj da görünür. start_at = messages.created_at (sunucu kayıt zamanı), timestamp_source=server_message, duration_seconds=null, end_at=null, quality=server_message_only, end_reason=missing_client_send. Özgün bölüm ziyareti, bekleme süresi veya istemci gönderim saati tahmin edilmez. Farklı sunucu/tarayıcı saat kaynaklarından süre hesaplanmaz. Ekran bu ayrımı açıklar.

Disco'da aynı soru tek nokta olayı olarak Start=Complete=messages.created_at, Duration Seconds boş, Row Type=ai_question_legacy ve Time Source=server_message ile bulunur. Sıfır bekleme süresi iddiası yoktur; nokta zamanı bilinir, bekleme süresi bilinmez. Parent Visit ID/Trace ID boş bırakılır; bir tarayıcı sekmesine atanmaz. Eşleşen modern gönderim kaydı zaten varsa eski soru noktası eklenmez. Veri dönüştürme salt türetimdir; DB güncelleme/backfill yoktur.

## 0.2.10 — Anlık soru gönderimi ve ayrı AI hazırlık kulvarı

Bu sürüm, 0.2.8'deki etiketi soru bekleme süresine verme davranışını değiştirir. ai_question / AI_MESSAGE artık etiketli gönderim noktasıdır; Start=Complete ve duration_seconds=0. ai_wait / AI_WAIT = AI yanıtı hazırlanıyor aralığıdır; soru gönderiminden aynı trace/request kimliğine ait ilk geçerli yanıt gözlemine kadar sürer. Anlık yanıt AI_RESPONSE, ardından yalnız öğrenci AI bölümündeyse AI_VIEW oluşur. İstemci yanıt gözlemi yoksa wait end_at/duration_seconds=null; bölümden ayrılma veya yeni soru gerçek yanıt zamanı yerine kullanılmaz. Yanıt işleme hatası/kısmi yanıt başarı gibi adlandırılmaz.

timeline_lane=ai_wait hazırlık beklemesini, student bölüm ziyaretlerini/eylem noktalarını ayırır. Bekleme başka bölümde çalışma sürerken de devam edebilir. Bu iki kulvarın süreleri toplanmaz; üst bölüm özetleri özgün ziyaretlerden hesaplanır ve AI_WAIT ayrıca eklenmez. response_section yanıt geldiğinde açık bölümdür. Disco CSV'de Timeline Lane ve Response Section alanları vardır; bekleme satırının etiket/metin amacı ilgili sorudan korunur fakat AI Question Count=0, gerçek gönderim noktası AI Question Count=1 olur. AI Label Match Quality legacy soruda server_message_matched, istemci isteğiyle eşleşen gönderim/beklemede request_key_matched olarak ayrılır. Bekleme ağ ve tarayıcı gözlem gecikmesini içerir, yalnız sağlayıcının işlem süresi değildir. Eski gönderim telemetrisi olmayan mesajlara bekleme süresi uydurulmaz. Son teslim sınırı ve ham kayıtlar değişmez.

## Nihai algılanan ciddiyet — 2026-10-21

`final_severity`: öğrencinin nihai değerlendirmede yanlış kararın sonuçları için bildirdiği 0–10 tam sayı. Başlangıç `severity` alanından bağımsızdır; yeni son teslimlerde zorunludur ve 0 geçerlidir. applications.draft, draft_revisions.body ve submit snapshots.body içinde saklanır. Eski kayıtta bulunmayabilir; yanıt yoktur, başlangıç değeri kopyalanmaz ve sıfır varsayılmaz. Eski kesinleşmiş kayıtlar değiştirilmez. Mevcut JSON/Excel veri çıktıları taslak/snapshot JSON alanlarında değeri korur; AI rubrik puanına veya güven farkı hesabına katılmaz.

## presence-v2 — bölüm ve uygulama dışı süreler

Yeni olay: app_presence. Payload app_active = visible && focused; navigation_version=2; pending_request başlangıçta bekleyen gerçek isteği bağlar. Bölüm olaylarının section alanı yedi özgün bölümden biridir. Türetilmiş navigation_visits / navigation_activities içinde outside bölümü ve APP_OUTSIDE kodu ayrıca bulunur. Böylece bölüm süresi dışarıdaki zamanı içermez. Dönüş yeni aralıktır; aynı sorunun yeniden gönderimi değildir. Ham focus/visibility olayları ve istemci/sunucu zamanları korunur.

Measurement Version=presence-v2; Presence Source=app_active (yeni istemci), legacy_visibility (eski kayıtlı görünürlük), unobserved (gözlem yok). Eski kayıtların odağı varsayılmaz. Eksik kapanış/geri dönüşte duration_seconds=null; kapanışı olmayan aralık süre CSV’sine alınmaz. Eski mesaj fallback’ı legacy_message olarak ayrılır.

AI_WAIT, aynı trace/request ile eşleşen yanıt sınırı ve dışarıdaki aralıklar kullanılarak bölünür. Aralığın Duration Seconds değeri Start/Complete farkıdır. Dönüşte hâlâ bekleyen istek AI_VIEW olarak gösterilmez; AI_RESPONSE dışarıda da tek anlık olaydır. Başka uygulama bölümünde çalışma ile eşzamanlı bekleme ayrı ai_wait kulvarında kalır, bölüm toplamlarına yeniden eklenmez. Teknik sunucu istek süresi bu ölçümden ayrıdır. Ham teknik visits tablosu eski bounded heartbeat ölçümünü korur; bu modelin ana analiz tabloları navigation_visits ve navigation_activities, ana CSV ise Disco geçiş/süre çıktısıdır.

Saat sırası koruması: aynı trace içinde client_seq ilerlerken client_at geriye giderse o noktadan etkilenen aralıklar invalid_clock=true ve duration_seconds=null olur. Bu kayıtlardan geçen AI beklemesi de ölçülemez; dışarıdaki aralığı bölerek sahte pozitif süre üretilmez. Ham zamanlar değişmez.

## 0.2.12 — CASE_AWAY / Vaka dışında

Türetilmiş away bölümü, case_away satır türü ve CASE_AWAY etkinlik kodu: kaydedilmiş section_leave(reason=home) zamanı ile aynı app_id için sonraki çalışma açılışı arasındaki süre. Başka uygulama/site kullanımı çıkarılmaz. Kaynak home_and_reopen, kalite observed_case_gap. Başlangıç ve bitiş gözlenmeli; arada aynı vaka üzerinde başka açık çalışma/salt okunur paralel pencere varsa boşluk üretilmez. Bölüm ve uygulama içi toplamına eklenmez. APP_OUTSIDE, açık bir vaka içindeki uygulamadan ayrılış süresidir; iki kategori birbirinin yerine kullanılamaz. Geçmiş ham olaylara dokunulmadan mevcut home/reopen sınırlarından da üretilebilir. Öğrenci onayının kendisi artık sayfa içinde gerçekleşir; dönemsel eski native odak artefaktları silinmez.
