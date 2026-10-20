# Veri sözlüğü · schema 001

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
