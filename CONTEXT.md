# Proje Devam Bağlamı

## Güncel GitHub teslimi — 24 Ekim 2026

Son özellikler, düzeltmeler ve sentetik test kanıtları 376 dosyada `f924b16` commit'iyle origin/main dalına başarıyla gönderildi (önceki HEAD: `32fef8e`). Commit öncesi index'teki 703 dosya, gönderilecek main geçmişi, Word/Excel arşiv içerikleri ve PDF metni gerçek yapılandırılmış anahtarla çıktıya dökülmeden tarandı; eşleşme yok. `config/local.php`, çalışma DB/yedekleri, `.tools`, `tests/tmp` ve yerel Codex checkpoint refleri gönderilmedi. Yalnız main dalı gönderildi. Sentetik ekran görüntüsü ayrıca incelendi.

Son kontroller: Chrome142, V2 değerlendirme94, ana87, eski teslim21; etiket sözleşmesi22. Sekiz yeni log/helper dosyasında yalnız satır sonu boşlukları düzeltildi, test sonuçları değiştirilmedi. Git kimliği global değiştirilmeden önceki doğrulanmış noreply kimliği komuta özel kullanıldı. Bu teslim kaydı ayrı belge commit'iyle gönderilir. Hosting dağıtımı yapılmadı; AI puanları araştırmacının inceleyeceği ön önerilerdir.

## Onaylanan sohbet / güncel etiketler / odak incelemesi — 2026-10-23

Kullanıcı sohbet promptunu dikte etti: yalnız vaka/pedagoji, Türkçe, kişisel veri isteme, sabotaj/rol değiştirmeyi izleme, finansal/hukuki danışmanlık verme, istenmeyen yönlendirme/Sokratik akış/takip sorusu/istersen daveti yok; öğrenci açıkça isterse karar/alternatif/gerekçe verilebilir. Etiketleri ve öğrenci/AI yanıtına özgü açıklamaları kendisi daha sonra hazırlayacak; her istekte güncel ad ve açıklamalar birlikte kullanılacak. Ai::CHAT_SYSTEM_PROMPT / LABEL_SYSTEM_PROMPT yerelde etkin. Tam güncel metin AI_PROMPTS_REVIEW_TR.md; eski taslak bölümleri güncel metinle değiştirildi. chat-v2 ve message-labels-v2 sürümleri kullanılır; eski kayıtlar değiştirilmez.

Sohbet vaka bağlamı user-verisi, konuşma ise gerçek user/assistant API rolleriyle aktarılır; konuşmadan system/developer/tool rolü yükseltmesi reddedilir. Etiket açıklaması açık description ve ayrıştırılmış body ile gönderilir; audit kullanılan tanımları saklar. Model/prompt/ad/açıklama/listede değişim cache'i geçersiz kılar. AI beklenirken tanım listesine yeni etiket eklenmesi dahil değişimde eski yanıt kaydedilmez; yarış testi geçti. Eski v1 analiz etiketi aynı tanım/hash kurallarını kullanır. Destekleyen uçta etiket çıktısı da katı JSON şemasına bağlandı; insanın boş dahil seçimi önceliği korunur. Kullanıcının gerçek tanımları değiştirilmedi ve mevcut vakada etiketleme/değerlendirme başlatılmadı.

Gerçek sentetik GPT-5 Nano testleri KARIŞIK: kısa vaka sorusu gerçek rol aktarımından sonra kısa/yönlendirmesiz yanıtlandı. Öğrenci gerekçe sorusu ve AI gerekçeli yanıtı ayrı doğru etiketlendi. Fakat kapsam dışı finans/hukuk talebini reddettikten sonra istenmeyen pedagojik plan eklemesi ve adı aynı kalıp açıklaması değişen etiketlerde eski seçimleri sürdürmesi tekrarlandı. Bunlar açık semantik hatalardır; tüm davranışlar geçti veya araştırmaya hazır denmez. Bazı ilk helper passed alanları yalnız zayıf biçim kontrolleriydi; insan incelemesinde başarısızlık kaydedildi. Çıktılar kesilip değiştirilmedi, model değiştirilmedi. Sonraki öncelik bu davranış açıklarını kullanıcıyla ele almak; değerlendirmeyi otomatik başlatma. Kanıt test-evidence/prompts-presence-2026-10-23/ ve outcome-summary.json.

Yerel teknik kontroller: 14 etiket sözleşmesi (tanım değişirken yarış dahil), 9 sohbet rol/allowlist, 28 süreç, 60 presence, 82 navigation, 81 genel SQLite, 74 v2, 21 recalc, 17 review, 39 export; PHP8.4 ilk etiket testi 12 (yarış eklenmeden önce); 131 genel Chrome, 23 gerçek presence Chrome, 11 süreç JS geçti. İlk screenshot testinin öğrenci API'sinden researcher navigation beklemesi düzeltildi; ayrı test DB salt okunur incelendi. Gerçek Windows Snipping Tool/Win+Shift+S testi YAPILMADI; tarayıcının kendi screenshot işlemi odak/görünürlük korunurken sahte dışarı üretmedi. Sekme geçişi ve minimize gerçek Chrome'da doğrulandı.

Kullanıcının 38.470 sn aralıklarının ARA olayları da PRAGMA query_only=ON ile incelendi. 35.502 sn visible=false; 2.968 sn visible=true/focused=false. Uzun aralıkta 13:33:46.162 ve 13:34:01.156 İstanbul saati heartbeat de gizli/odaksız. Ekran görüntüsü veya başka pencere kaynaklı olduğu kesin söylenemez. Ölçüm tüm VakaLab'dan çıkışı değil, öğrenci vaka sekmesinin görünür/odaklı olmamasını ölçer; aynı VakaLab'ın başka penceresi de bu koşulu oluşturabilir. Arayüz açıklaması düzeltildi; süre matematiği/ham kayıtlar korunur. Yeni değişiklikler henüz commit/push edilmedi.

## Mesaj taslağı / prompt görüşmesi / 38 sn incelemesi — 2026-10-23

Kullanıcı altı ekran görüntüsüyle gönderilen mesajın kutuda kaldığını, sohbet ve mesaj etiketleme sistem promptlarını görmek/düzenlemek istediğini ve 38 sn uygulama dışı sürenin kaynağını sordu. Kullanıcı kararı: öğrenci istediğini sorsun, AI prompta göre yanıtlasın; yönlendirme veya zorunlu Sokratik akış yok. AI_PROMPTS_REVIEW_TR.md mevcut iki promptun tam metnini, önerilen iki TASLAĞI ve salt okunur bulguları içerir. Taslaklar henüz etkinleştirilmedi; gerçek yeniden etiketleme/AI çağrısı veya kullanıcının vakasında değerlendirme başlatılmadı.

TEST-OGR-03 / SENTETİK · Akranlar arası anlaşmazlık mevcut SQLite üzerinde PRAGMA query_only=ON ile incelendi. Kayıtlı draft.question sonunda satır sonu var, mesaj metni trim edilmiş. sendChat bu metinleri karşılaştırdığı için temizlik atlanıyordu. Gönderim anındaki özgün taslakla karşılaştırma düzeltildi; yanıt beklerken yazılan yeni taslak korunur. Araştırmacıda “Gönderilmemiş soru taslağı” yerine yalnız “Mesaj kutusu taslağı” denir; geçmiş bir taslak tek başına gönderilmediğini kanıtlamaz. app.js/process.js cache 0.2.20. Eski teslim edilmiş draft/snapshot düzeltilmedi/yazılmadı.

Yeni chat-draft-browser.cjs önce hatayı üretti; son 11 hedefli Chrome, 131 genel Chrome ve 11 süreç JS kontrolü geçti (ayrı SQLite/gecikmeli mock). İlk düzeltme sonrası ağ-hatası test beklemesi erken sonuç okuyordu; hata bildirimi beklendikten sonra tümü geçti. Kanıt test-evidence/chat-prompts-2026-10-23/. Bu tur gerçek API maliyeti oluşturulmadı. Çalışma DB yazılmadı, migration/seed/reset/push yok.

38.470 sn = İstanbul saatiyle 13:32:41.361–13:32:44.124 (2.763), 13:33:05.845–13:33:07.859 (2.014), 13:33:36.921–13:34:10.614 (33.693). Başlangıçlarda visible=true/focused=false/app_active=false; dönüşte hepsi true. Gidilen uygulama/site bilinmez. Bu vakada analysis_runs=0; kullanıcının başlattığı 1 etiket batch'i mevcut. Aktif yedi etiketin body.description alanı aynı “SENTETİK başlangıç tanımı”; açıklamaya dayalı kodlama için gerçek kapsamlar netleştirilmeli, kayıtlı tanımlar değiştirilmedi. Sonraki adım prompt taslaklarını ve etiket açıklamalarını kullanıcıyla netleştirmek; değerlendirmeyi otomatik başlatma.

## Yerelde gerçek OpenRouter/GPT-5 Nano — 2026-10-23

Kullanıcı OpenRouter üzerinden GPT-5 Nano seçti ve gerçek anahtarı özel config/local.php'ye kendisi girdi. Anahtar gösterilmedi/loglanmadı. provider compatible, endpoint OpenRouter /api/v1/chat/completions, model openai/gpt-5-nano. İlk TLS cURL60 hatası yerel Windows trusted roots (39 sertifika) CA paketiyle giderildi: .tools/php/cacert-windows.pem; php.ini curl.cainfo/openssl.cafile güncellendi, eski ini php.ini-before-local-ca-20261023. SSL doğrulaması kapatılmadı. Çalışan PHP sunucusu varsa ini değişikliği için yeniden başlatılmalı.

Gerçek sentetik testlerde serbest JSON nesnesi yanıtı bazen feedback yerine response döndürdü; bir yanıt feedback içinde JSON kodladı, bir profil ölçülmeyen alan için varsayılan sayı uydurdu. Bunlar başarılı değerlendirme sayılmaz. Ai::responseFormat ile opt-in strict JSON Schema (profil ve v2 soru), OpenRouter require_parameters, açık metin/eksik-ölçüm talimatları ve Review::text JSON metni reddi eklendi. config/example.php varsayılan structured_outputs=false ile eski sağlayıcı uyumluluğunu korur; özel local.php true ve timeout90. Son başarılı sohbet/profil/tek soru örnekleri ve önceki hatalar test-evidence/openrouter-2026-10-23/ içinde; kapsam TEST_RESULTS.md. 74+21+17+15+20+81 sentetik PHP kontrolü, ayrıca PHP8.4 v2 74 geçti. Chrome/HTTP/MariaDB bu tur tekrar edilmedi.

Bu yalnız ilk gerçek sağlayıcı smoke doğrulamasıdır; altı sorulu tam UI akışı, etiketleme, gerçek rubrik/kalite/güvenirlik/hosting kabulü tamamlanmadı. Profilde eksik ölçümün ham değeri hiçbir zaman değiştirilmez; yorumların anlamsal doğruluğu insan incelemesine bağlıdır. Mevcut çalışma DB'si ve öğrenci kayıtları okunmadı/yazılmadı. Yeni ürün/test/belge değişiklikleri henüz commit/push edilmedi; önceki GitHub teslimi aşağıda. Tekrar test için tests/tmp/openrouter-smoke.php yerel yardımcıdır, Git dışı; gerçek ücretli çağrı yapar.

## GitHub teslimi tamamlandı — 2026-10-23

Kullanıcı testler tamamsa GitHub'a push edilmesini açıkça istedi. Fetch ile yerel HEAD ve origin/main `9098ff7` eşitliği doğrulandı. Kod, belgeler, yeni eski-teslim regresyonu ve PHP güvenlik/8.4.27/yerel hazırlık kanıtları (62 dosya) `ee31172` commit'iyle origin/main'e başarıyla gönderildi. 62 adayda sır örüntüsü yok; kurulum belgesindeki açık parola yer tutucusu gerçek sır değildir. config/local.php, çalışma DB, PEM ve MariaDB çalışma dosyaları, vendor, .tools ve tests/tmp gönderilmedi. Beş yeni test logunda yalnız satır sonu boşlukları temizlendi; test sonuçları değiştirilmedi. Git kimliği eksikti; önceki commit'in Halil Kayaduman/GitHub noreply kimliği yalnız komuta özel kullanıldı, global ayar değiştirilmedi. Testler gereksiz tekrarlanmadı; son geçerli kapsam TEST_RESULTS.md içindedir. Bu teslim kaydı ayrı belge commit'iyle gönderilir. Hosting dağıtımı ve çalışma DB yazımı yok; gerçek AI/hosting/kurum/Disco/hedef cihaz-kesinti kabulü açık. Aşağıdaki commit/push yok kayıtları tarihçedir.

## Yerel hazırlık — 2026-10-23

Kullanıcı önce yerelde yapılabilecek işleri sırayla tamamlama ve gerekli testleri çalıştırma talimatı verdi. AUDIT-02'nin `9098ff7` içinde zaten düzeltildiği doğrulandı; hata belgeleri gerideydi. AUDIT-01 v1 bütünlük adımında gerçekten sürüyordu: yeni `tests/legacy-submission.php` önce hatayı üretti. `Rules::submitted` geçmiş yanıtların profil/metin/aralık bütünlüğünü denetler, eksik `final_severity` için `uncollected_fields` döndürür. `Analysis` v1 adım1 bu yolu kullanır; yeni teslimin `Rules::final` zorunlulukları değişmez. v2 sözleşmesi korunur. Eski yanıt/snapshot/teslim kilidi yazılmaz. 20 yeni regresyon her iki PHP sürümünde geçti. Güncel kanıtlar TEST_RESULTS.md ve `test-evidence/local-readiness-2026-10-23/` içinde.

README/BUGS/STATUS_REVIEW/RELEASE_READINESS/plan güncellendi; eski kayıtlar tarihçe olarak korunur. Yerel HEAD `9098ff7`, uzak dal fetch edilmedi. Çalışma ağacındaki önceki PHP güvenlik/8.4.27 değişiklikleri korundu. Kök dizine taşmış MariaDB çalışma dosyaları ve PEM anahtarları yalnız .gitignore ile teslim dışında tutuldu; silinmedi/okunmadı. Çalışma DB'sine migration/seed/reset yok; commit/push/dağıtım yok. Sonraki dış işler: ayrı test hostingi ve gerçek AI, kurum rubriği/kod kitabı/onam/saklama, gerçek Disco import ve hedef cihaz/kesinti kabulü. Yeni manuel batch isteme.

## PHP güvenlik güncellemesi — 2026-10-23

Yerel `.tools/php/php.exe` artık PHP 8.3.36 (NTS VS16 x64). Resmî ZIP SHA256: `3c9242622c7402b47c38d2ec2ed779cef3a290fd6f2bde6c35f9c482699e7900`. CVE-2026-109618 / CVE-2026-109619 için mevcut 8.3 dalı korundu; 8.4'e geçilmedi. Eski runtime `.tools/php-8.3.35-backup-20261023` altında geri dönüş yedeğidir; güvenlik açığı içerdiğinden web sunucusu olarak başlatılmamalı. Mevcut php.ini ve yerel VC runtime DLL'leri korundu; yeni PHP'nin eklentileri kendi paketinden geldi. Geçiş anında eski runtime'dan çalışan PHP süreci yoktu.

Composer PHP koşulu `~8.3.36 || ~8.4.27 || ^8.5.12`; lock content hash/platform güncel, kütüphane sürümleri değişmedi. `bin/console.php check` eski yamasız sürümleri reddeder. Ortam/Composer kontrolleri, 81 SQLite, 68 review-v2, 39 export, 42 HTTP/iki worker ve 81 MariaDB kontrolü geçti; ayrı restore DB'de 8 tablo sayısı eşleşti. Kanıtlar `test-evidence/php-security-2026-10-23/`. İlk sandbox ortam kontrolü storage erişim kısıtına takıldı; normal izinli son koşu geçti. Testler ayrı sentetik SQLite/MariaDB/mock ortamında; çalışma DB'sine migration/seed/reset yok. Canlı hosting güncellenmedi/doğrulanmadı; bu işlem yalnız yerel PHP'yi kapsar. Commit/push yok.

## Güncel kullanıcı kararı — 0.2.18 · 2026-10-21

Araştırmacı ekranından önceki yorum/kararlar, önceki değerlendirmeler ve Rapor geçmişi · Onay ve yayın listeleri kaldırıldı. Tek güncel rapor alanında yalnız Öğrenci raporunu hazırla / Word / Excel / Öğrenciye göster var. Word ve Excel doğrudan öğrenci projeksiyonunu indirir. Excel gerçek XLSX dosyasıdır; profil yorumu ve altı soru puanı/feedbacki içerir, iç karar veya geçmiş içermez. PDF backend desteği korunur fakat bu alanda PDF/önizleme düğmesi yoktur (kullanıcının son tercihi).

V2 raporu yeniden hazırlamak aynı reports.id satırını günceller; yeni rapor listesi/snapshot satırı oluşturmaz. Yeniden hesaplanan başarılı sonuç veya yeni karar önceki öğrenci görünürlüğünü kapatır; dört insan kararı yeniden tamamlandığında birleştirme mevcut raporu otomatik günceller. Güncellenen rapor yeniden Öğrenciye göster gerektirir. Tek düğme onay ve yayını birlikte yapar; mevcut vaka veri toplama kapanış/kişisel ek erişim kapısı korunur. Yeni v2 analiz başlatmak da önceki yayını kapatır. Öğrenci güncel v2 rapor dışında eski rapor kimliklerine erişemez. Araştırmacı ekranında tarihi satırlar gösterilmez; eski analiz/karar kayıtları silinmez. Eski v1 rapor üretim sözleşmesi korunur. Yeni v2 feedback API düzenlemesi de aynı raporu günceller. Sürüm etiketi v2 öğrenci ekranı/Word dosyasından kaldırıldı.

Şema/migration ve çalışma verisine doğrudan yazma yok; yalnız uygulama davranışı değişti. Testler ayrı sentetik SQLite/mock ortamında. 53 PHP ve 36 Chrome kontrolü; mobil/masaüstü yerleşim, aynı rapora yazma, otomatik güncelleme, Word/XLSX ve tek tık gösterme doğrulandı. Gerçek AI/hosting veya kullanıcı manuel kabulü sayılmaz. Kanıtlar test-evidence/current-report-*. Commit/push/dağıtım yok. Önceki 0.2.17 snapshot koruma açıklaması yeni v2 raporların üzerine yazılması için artık geçerli değildir.

## Güncel kullanıcı kararı — 0.2.17 · 2026-10-21

Önceki 0.2.16 kart sözleşmesinin yerine beş bölüm uygulandı: (1) Başlangıç/nihai vaka profili ve yalnız düzenlenebilir yorum; puan yok. (2) Başlangıçtaki üç soru ayrı rubrik puanı ve puanın gerekçesi/geliştirme feedbacki. (3) AI sohbetinde yalnız mesaj sayıları ve bilinen süre; yorum, puan veya insan karar düğmesi yok. (4) Nihai iki soru ayrı puan ve feedback. (5) Yansıtma rubrik puanı ve feedback. Dört inceleme bölümünde Kabul et / Düzenle / Reddet / yalnız o bölümü yeniden yazdır-hesaplat var. Profil düzenlemesi yalnız metin; diğerlerinde her sorunun puanı ve yorumu ayrı düzenlenir.

Yeni değerlendirme başlat tarayıcıdan review_version=2 oluşturur. Sürüm audit.analysis_run detail içinde; şema değişmedi. Tarihsel v1 analiz/raporlar kapalı geçmişte korunur, yeniden yorumlanmaz. Yeni UI için Ctrl+F5 ve bir kez Yeni değerlendirme başlat gerekir. Tek başlatma sonrası [1,2,3,5,4,6] sırası ayrı isteklerle ilerler; dört insan kararı sonrası 8 birleştirme otomatik. Her puanlanan soru kendi yanıtını içeren ayrı AI çağrısıdır. Seçilen rubriğin ağırlıklı ortalaması soru puanıdır; altı soru vardır, evreler arasında yeni genel puan uydurulmaz. Profil yalnız gerçekten toplanan ölçümleri karşılaştırır; nihai bilgi/zorluk ölçülmediyse açıkça belirtilir. Sohbet süresi kilitsiz AI bölümü ziyaretlerinin bilinen görünür/odaklı aralık birleşimidir; bilinmeyen süre sıfır gösterilmez.

student-v2 öğrenci raporunda kabul edilen profil yorumu, altı soru puanı/feedbacki ve bölüm toplamları bulunur; AI yazdı/araştırmacı düzenledi etiketi, iç karar geçmişi, sohbet yorumu yoktur. Rapor hazırlama yeni AI çağrısı yapmaz, onaylanan metinleri aynen taşır. Onaysız feedback düzenlemesi yansıtma dahil yeni rapor sürümü oluşturur; puanlar/eski snapshot korunur. Onay/yayın ve veri toplama kapanış kapıları korunur.

48 yeni PHP, 13 yeni JS ve 28 yeni Chrome kontrolü geçti (SQLite/mock, ayrı sentetik veritabanları). 130 genel/tarihsel Chrome, 81 genel PHP, 21 tekrar, 17 inceleme, 15 eski rapor ve 27 eski analiz JS kontrolü de geçti. 1440/768/390 görseller incelendi. test-evidence/review-v2-* kanıtları. Çalışma DB'sine yazılmadı; migration, commit/push veya dağıtım yok. Gerçek sağlayıcı/hosting ve kullanıcı kabulü açık. AUDIT-01/02 eski v1 sözleşmesi için açık kalır; v2 eski kayıtta toplanmamış nihai profil ölçümünü zorunlu tutmaz.

## Son kullanıcı düzenlemesi — 0.2.16 · 2026-10-21

Kullanıcı eski araştırmacı ekranını karışık buldu; açık puan/feedback ve doğrudan Kabul et / Düzenle / yalnız seçili adımı tekrar hesaplat istedi. Ana alan beş açık karta dönüştü: Başlangıç yanıtı, Nihai yanıt, Karardaki değişim, AI ile etkileşim, Yansıtma. İlk ikisinde ölçüt puanları, ağırlıklı toplam ve feedback görünür; diğer üçü nitel değerlendirmedir. Teknik 1/2/8, eski analizler, denemeler ve kararlar kapalı ayrıntılardadır. Öğrenci yanıtı karttan açılabilir. İlk AI çıktısı ve önceki kararlar korunur. Kabul et doğrudan insan tıklamasıyla gerekçeli karar kaydeder; otomatik insan kararı yoktur. Düzenle puan ve öğrenci feedbackini açar; iç dayanak/not ayrıntıdadır. Onaylı ölçüt feedbacki rapora aynı metinle geçer. Rapor üretiminin özgün AI feedback çıktısı ayrıca iç kayıtta korunur. Eski sonuçta feedback yoksa yalnız o kartı yenileme veya düzenleme önerilir.

Tamamlanan 3–7. adımlar repeat=true ile aynı run içinde yeni attempt satırı olarak yenilenebilir. Diğer adımlar tekrarlanmaz. Güncel sonuç en son completed attempt'tir. Yeni başarılı sonuç yeni insan kararı ister; eski onayı devralmaz. Başarısız tekrarda önceki başarılı sonuç korunur. Eski step_id'ye yeni karar reddedilir. Run kilidi ve tek çalışan istek kuralı korunur. 8. birleştirme, güncel karar kimlikleri değişince yeni satır olarak yenilenir; eski raporlar snapshot kalır. Yeniden hesaplama sırasında karar ve rapor hazırlama bekler.

Şema değişmedi; çalışma DB'sine yazılmadı. 130 Chrome, 81 SQLite, 21 yeniden hesaplama, 17 inceleme, 15 öğrenci raporu, 27 analiz JS, 28 süreç PHP ve 11 süreç JS kontrolü geçti. Masaüstü/mobil görseller incelendi. İlk Chrome koşusu 107 kontrolden sonra kapalı teknik 8. satırını görünür bekleyen test seçicisinde durdu; attached beklemesiyle tam koşu geçti. Kanıtlar test-evidence/review-cards-* ve review-card-*.png. Gerçek AI/hosting ve yeni UI için kullanıcı kabulü açık. Commit/push/dağıtım yok. AUDIT-01/02 açık.

## Son kullanıcı onayı — 0.2.15 · 2026-10-21

Kullanıcı tek tıklamayla1–7adımlarının ayrı çağrılarla sırayla ilerlemesini onayladı;3–7karar/düzenleme hakkı korunur, son karar sonrası8birleştirme otomatik. Hata/ağ belirsizliğinde akış durur; Analize devam et tamamlananları tekrarlamaz. Bu ekran açıkken ilerler; ayrılınca yeni adım başlatılmaz, çalışan sunucu isteği tamamlanabilir. Tek-adım kontrolleri isteğe bağlı açılır alanda korunur. Son edit kararı tekrar düzenlemede başlangıç değeridir; ilk AI çıktısı/geçmiş değişmez.

Kullanıcı düzeltmesi: öğrenci raporunda AI yazdı / araştırmacı düzenledi kaynak açıklaması OLMAYACAK. Bu tur önce eklenen öğrenci kaynak etiketleri kaldırıldı. Kaynak/karar geçmişi yalnız araştırmacı iç kaydındadır. Öğrenci ekranı ve Word/PDF puanlar/feedback odaklı; yanıt metinleri raporda tekrarlanmaz, araştırmacı dosyasında korunur. Rapor hazırlama/onay/yayın ayrı. Onaylanmamış raporda Geri bildirimleri düzenle ile genel ve ölçüt açıklamaları düzeltilebilir; puanlar değişmez, eski rapor korunarak yeni onaysız sürüm oluşur. Puan düzeltmesi3/4kararında yapılıp yeni rapor hazırlanır. Onaylı rapor üzerine yazılamaz. Öğrenci raporunda model/prompt/JSON/iç karar gerekçesi yoktur. Çalışma DB/migration/commit/push/dağıtım yok. Testlerin son kapsamı TEST_RESULTS.md; AUDIT-01/02 hâlâ açık.

## Son kullanıcı düzenlemesi — 0.2.14 · 2026-10-21

Kullanıcının anlaşılmayan iki araştırmacı bölümü sadeleştirildi: 6.Süre özeti uygulama içi/dışı/vaka dışı bilinen süreleri ayrı açıklamalı kutularda gösterir; eksik süre sayısı ve toplamların eksik olabileceği açık. 7.AI yanıt durumu dört sonuç sayacı ve kontrol gerektiren istekleri gösterir. Tüm istek geçmişi, payload/deneme/sunucu süresi ve takılı isteğe müdahale varsayılan kapalı açılır alanlarda korunur. Hiçbir kayıt/işlev silinmedi; backend ölçümü ve DB değişmedi. 130genelChrome,11süreçJS;1440/768/390 yerleşim,boş kayıt/başarılı/sorunlu durumlar geçti; masaüstü ve mobil görseller incelendi. İlk Chrome koşusu eski testin her zaman bulunan eksik-süre etiketini beklemesiyle durdu; sıfır eksik kaydın da açık gösterilmesi sonrası130tamgeçti. Kanıt test-evidence/readable-panels-*; ürün asset0.2.14. Commit/push/dağıtım yok; yeni manuel batch yok. AUDIT-01/02 bu arayüz talebi kapsamında düzeltilmedi, açık.

## En son durum incelemesi — 2026-10-21

Güncel denetim: STATUS_REVIEW.md. HEAD ve yereldeki origin/main 44aa91e; canlı uzak dal fetch edilmedi. 0.2.13 ölçüm kılavuzu değişiklikleri çalışma ağacında. Genel SQLite81, navigation82, presence57, süreç28, export39, rapor15, rubrik14, JS10/11/11/12, Chromium127, HTTP42, erişim/yayın119 ve MariaDB81 yeniden geçti; ayrı restore8tablo sayısı eşit. Buna rağmen iki yeni sentetik olumsuz senaryo hata üretti: AUDIT-01 eski final_severity alanı olmayan teslim analizin1.adımında failed; AUDIT-02 rubrikte Yetersiz=0 iken score4/düzeyYetersiz/boş gerekçe kabul ediliyor. Bulgular düzeltilmedi; sonraki ilk iş bu iki regresyonu gidermek. Ürün kodu/çalışma DB değişmedi, commit/push/dağıtım yok. Gerçek AI/hosting/rubrik-kod kitabı/kurum ve hedef cihaz-kesinti/Disco kabulü açık. Önceki commit/push yok ve manuel bekleniyor kayıtları kendi tarihsel aşamalarına aittir; güncel durumla karıştırma. Yeni manuel batch yok.

Bu dosya Codex'in yeni oturumda kaldığı yerden devam etmesi içindir. Sır, gerçek parola, API anahtarı veya gerçek öğrenci metni ekleme.

## Proje

Öğretmen adaylarının beş pedagojik vakada AI öncesi muhakemesi, GenAI etkileşimi, nihai muhakemesi ve yansıtmasını toplayan araştırma uygulaması. Yaklaşık 6–7 katılımcı; veri modeli bu sayıya sabitlenmez. HTML/CSS/JS/PHP/MySQL, shared hosting, Türkçe, responsive.

## Şu anda gerçekten yapılan

2026-10-21 · 0.2.13 yerel: Kullanıcı için METHODS_EVENTS_DURATIONS_TR.md yöntem taslağı/etkinlik sözlüğü/örnekler hazırlandı. Araştırmacı bölüm geçişleri altında açılır Etkinlikler ve süreler nasıl yorumlanır? özeti eklendi; olay/mesaj etiketi, görüntüleme/güncelleme/kilit, görünür+odak koşulu, hareketsizliğin dahil olması, uygulama/vaka dışı ayrımı, AI beklemesinde çift saymama, teslim ve eksik süre kuralları açıklanır. Ölçüm mantığı/DB/etiketler değişmedi. Yerel sekiz etiketin açıklamaları sentetik başlangıç tanımı olduğundan bilimsel kodlama yönergesinin tamamlanması gerektiği belgede açıklandı; güvenirlik çalışması yapılmış gibi sunulmadı. Chrome 1440/768/390 genişlikte kılavuz taşmıyor; mevcut sentetik yol verisiyle render, DB erişimi yok. JS/PHP syntax ve diff kontrolü geçti. Önceki fonksiyonel testler bu tur yeniden koşulmadı. Bu yeni düzenleme henüz commit/push edilmedi.

2026-10-21 GitHub teslim hazırlığı: Kullanıcı son değişikliklerin tamamının gönderilmesini istedi. Hedef origin/main; başlangıç f555aa6 ve uzak dal aynı doğrulandı. 0.2.12 araştırmacı tabloları/nihai ciddiyet/geçiş ve süre düzeltmeleri, testler ve kanıtları birlikte teslim ediliyor. Son manuel uygulamadan ayrılmadan testte sıfır dışarı kaydı doğrulandı (MANUAL_TEST_RESULTS.md). 75 aday dosyada sır örüntüsü yok, 21 PHP/JS syntax ve diff whitespace geçti; config/local.php, storage, vendor, .tools ve tests/tmp dışarıda. Bu tur önceki fonksiyonel testler yeniden çalıştırılmış sayılmaz. Commit/push sonucu kullanıcıya ayrıca bildirilecek; üretime dağıtım yok.

2026-10-21: Kullanıcının ek test isteği için tests/journey-browser.cjs ile ayrı SQLite ve gerçek Chrome profilinde farklı karma yol denendi. Vaka A → Vakalarım → Vaka B'de iki bölüm → A'ya dönüş → gecikmeli AI sorusu → nihai bölüme geçiş → başka Chrome sekmesindeyken yanıt gelişi → uygulamaya dönüş → AI/yansıtma → Vakalarım → A'ya dönüş → teslim onayını iptal → yeniden onay/teslim → teslim sonrası gezinme. Eski kapanmamış trace fixture'ı da vardı. 27 kontrol geçti: bilinen aralıklarda sıfır boşluk/çakışma, her sürenin ham zaman farkına eşitliği, bölüm/uygulama dışı/vaka dışı ayrımı, tek yeni soru/yanıt, onayda sahte ayrılış olmaması ve teslim sonrası takibin durması. Gerçek indirilen CSV'de süreler, sıra, önceki/sonraki etkinlik ve benzersiz kimlikler; araştırmacı UI/API toplamları karşılaştırıldı. Gözlenen A yolunda 5.394sn bölüm + 6.266sn uygulama dışında + 2.853sn vaka dışında = 14.513sn; AI bekleme kulvarı bu toplama yeniden eklenmedi. Eski açık uç null kaldı. Kanıt: test-evidence/journey-mixed-final.txt; sentetik ekran tests/tmp/journey-mixed.png görsel incelendi. Çalışma DB'si ve ürün kodu bu tur değiştirilmedi; yeni manuel test istenmiyor. Gerçek AI yerine 6sn gecikmeli mock kullanıldı; gerçek hosting/sağlayıcı/Disco import kabulü sayılmaz. Commit/push yok.

2026-10-21 0.2.12 kullanıcı yeni ekran incelemesi: TEST-OGR-01 / Sınıfta dijital araç kullanımı, son onay 13:38:06.747–13:38:17.978 (11.231sn) kesintisiz submit_confirmed ile kapandı; sahte native ayrılış yok. Önceki 13.384 ve8.273sn dışarı aralıklarında visible=false gözlemi var. Vakalarım13:36:49.989→yeniden açılış13:37:24.941 arası34.952sn ilk hesapta görünmüyordu: eski günden kalma end=null trace, sonsuza kadar açık paralel pencere varsayılıp boşluğu engelliyordu. Düzeltildi: sadece bitişi bilinen ve gerçekten çakışan aralık bu boşluğu engeller. Eski açık uçlar bilinmiyor kalır; kapanış uydurulmaz. Salt okunur gerçek DB yeniden hesabı Vaka dışında34.952sn doğruladı. 57 presence,82navigation,16 gerçek Chrome onay/boşluk testi geçti; Chrome fixture eski açık uç kaydını da içerir, CSV/UI doğrulandı. Çalışma DB yazılmadı; yeni manuel tekrar gerekmiyor. Kullanıcının düzeltme sonrası ekran teyidi henüz yok. Commit/push yok.

**2026-10-21 · 0.2.12 yerel:** Native confirm kaynaklı sahte uygulamadan ayrılma düzeltildi: öğrenci başlangıç/teslim/kurtarma/yeniden deneme/kirli çıkış onayları await edilen sayfa içi dialog kullanır. Onayla, Vazgeç ve Escape destekli; gerçek başka pencereye geçiş onay açıkken de kaydedilir. Eski ham kayıtlar silinmedi/yeniden yazılmadı; geçmiş native confirm artefaktları olduğu gibi korunur. Vakalarım’a dönüş ile aynı vakayı yeniden açma arasında CASE_AWAY / Vaka dışında türetilir; APP_OUTSIDE ile karıştırılmaz, bölüm/app içi süreye eklenmez. Geri açılış gözlenmeden süre uydurulmaz; paralel/salt okunur sekmede sahte boşluk oluşturulmaz. UI/API/Excel-JSON/Disco aynı kaynağı kullanır. Hiç ziyaret edilmeyen bölümlerde Ziyaret yok gösterilir. 55 presence, 82 navigation, 15 gerçek Chrome onay/boşluk, 127 genel Chrome, 50 doğrulama, 81 SQLite ve39 export kontrolü geçti. Çalışma DB değişmedi; commit/push yok. Yeni tek manuel senaryo kullanıcıya verilecek; 0.2.12 kullanıcı kabulü bekleniyor.

2026-10-21 yeni kullanıcı bildirimi incelemesi (yalnız teşhis): Son teslimden önceki dışarı satırı 13:20:39.882–39.883, yalnız1ms; visible=true ve focused=false→true. Gerçek Chrome’da native confirm kabulü ayrı sentetik DB’de aynı hatayı 2ms olarak yeniden üretti (test-evidence/native-confirm-investigation.txt). Başlangıç kesinleştirmede de 0ms aynı iz var. Bu, gerçek uygulamadan ayrılış değil; native onay penceresinin odak etkisi. HATA AÇIK, uygulama kodu bu incelemede değiştirilmedi. Ayrıca Vakalarım’a dönüş13:14:08.481 ile yeniden vaka açılışı13:19:27.989 arasındaki5dk19.508sn mevcut kapsam gereği izlenmiyor; açık vaka içindeki dışarı toplamına dahil değil. Bu boşluk için nerede zaman geçirildiği çıkarılamaz. Kullanıcıya iki durum ayrı açıklanacak; eski geçti kabulü önceki gerçek pencere geçişi senaryosuyla sınırlıdır. Çalışma DB salt okunur incelendi.

2026-10-21 kullanıcı onayı: Kullanıcı kısa ayrılışların Codex talimatlarına bakarken yaptığı pencere geçişleri olduğunu doğruladı ve bu manuel senaryonun geçti sayılmasını istedi. TEST-OGR-01 / SENTETİK TEST · Vaka sürümü bölüm→uygulama dışı→dönüş manuel kontrolü GEÇTİ. Önceki salt okunur süre doğrulamasıyla birlikte beş ayrılışın beklenen davranış olduğu kabul edildi. Bu kabul yalnız bu senaryoya aittir; diğer açık kabul maddelerini kapatmaz. Kod/DB değişikliği yok.

2026-10-21 kullanıcı ekranı + salt okunur log kontrolü: TEST-OGR-01 / SENTETİK TEST · Vaka sürümü için 13:13:08.172–13:14:08.481 (UTC+03) aralığı incelendi. Vaka 5 aralık 22.694 sn (ekranda23), Başlangıç 2 aralık6.416sn(6), dışarı5aralık31.199sn(31); toplam60.309sn başlangıç/bitiş farkıyla aynı. Ana dışarı aralığı17.920sn. Beş dışarı aralığının tamamında tarayıcı visible=false gözlemi mevcut; tek görünürlük geçişinin çift sayılması saptanmadı. Kullanıcının ekrandaki kısa ayrılışları hangi amaçla yaptığı çıkarılmadı. Bu kanıt yalnız paylaşılan yolun sürelerinin tutarlılığını doğrular; diğer gerçek kullanıcı senaryoları veya gerçek AI kabulü değildir. Veritabanı salt okunur sorgulandı, değişiklik yapılmadı.

2026-10-21 süre/geçiş yeniden doğrulaması: 49 hesaplama/presence, 82 navigation, 22 gerçek Chrome geçiş/AI/minimize, 127 genel Chrome, 81 SQLite, 39 export ve 28 süreç kontrolü yeniden geçti. Gerçek Chrome: kilitli AI sekmesinden ayrılma/dönüş; 6 sn gecikmeli mock yanıtın dışarıdayken gelişi; ikinci soruda yanıt gelmeden dönüş; Nihai bölümünde pencereyi küçültme/geri açma; gerçek CSV indirme. Zaman fixtureları: iki ayrılma, 3 saat dışarıda, aynı zaman damgası, eski görünürlük kayıtları, eksik dönüş, aradaki eksik olaylar, teslim sonrası izlememe, kısmi/hatalı yanıt, farklı bölümde yanıt. Yeni testte bilgisayar saati geri giderse AI beklemesinin fazla süre üretebildiği bulundu ve düzeltildi: etkilenen aralıklar invalid_clock/null olur, süre CSV’sine sayısal süre olarak çıkmaz. Normal akış kuralları değişmedi. Mevcut çalışma DB’sine dokunulmadı. Kullanıcıya tek kısa manuel Vaka→dışarı→Vaka→Başlangıç senaryosu verilecek; kullanıcı kabulü henüz bekleniyor. Commit/push yok.

**2026-10-21 · 0.2.11 yerel:** Onaylanan uygulama içi/dışı süre modeli uygulandı. Başka Chrome sekmesine/uygulamaya geçildiğinde bölüm aralığı kapanır; dönüşe kadar APP_OUTSIDE / Uygulama dışında aralığı oluşur, dönüşte yeni bölüm aralığı başlar. Yeni istemci navigation_version=2, app_active, pending_request ve app_presence kaydeder (görünür + odakta); eski kayıtlarda yalnız kayıtlı visible durumları kullanılır, eksik sınırlar tahmin edilmez. Kilitli AI erişimi açık adlandırılır. Aynı AI sorusunun beklemesi dışarıda sayılmaz, dönüşte gerekirse sürer; yanıt dışarıda geldiyse gerçek anlık kayıt korunur, sahte görüntüleme/soru oluşturulmaz. API/araştırmacı/Excel-JSON/Disco aynı navigation hesaplarını kullanır; CSV Measurement Version ve Presence Source içerir. Süre özet ekranı aynı navigation kaynağına bağlandı; eski heartbeat/30 sn odak hesabı ana ekran ölçümü değil. Teknik sunucu istek süresi ayrı adlandırıldı. 26 presence, 82 navigation, 127 genel Chrome, 9 gerçek Chrome sekme geçişi, 81 SQLite, 39 export, 28 süreç ve 11 süreç UI kontrolü geçti. Mevcut çalışma veritabanına yazma/seed/reset yok; commit/push yok.

2026-10-21 yerel: Öğrencinin işlem çizelgesi Zaman, Bölüm, İşlem, İlgili alan, Açıklama ve ayrıntılar sütunlarıyla tabloya dönüştürüldü. Bölüm/arama filtreleri, varsayılan gizli periyodik kayıtlar ve 50 satırlık kademeli gösterim korunur. Teknik bilgiler ve kopyalanan metin satır içinde açılır. Sunucuya ulaşma sırası korunur. 127 Chrome ve 30 uzun metin yerleşim kontrolü geçti; tablo görseli incelendi. İlk Chrome koşusu 4 kontrolden sonra giriş gezinmesinde zaman aşımına uğradı; aynı test tekrarında tamamı geçti. Kanıtlar test-evidence/event-table-browser.txt, event-table-browser-retry.txt ve event-table-long-text.txt. Syntax/diff kontrolü geçti. Çalışma DB değişmedi; commit/push yok.

2026-10-21 yerel: Kullanıcı isteğiyle yanıt değişiklik geçmişi ilk kayıttan son kayda (artan revizyon) sıralandı; ekran açıklaması güncellendi. JS syntax ve diff whitespace kontrolü geçti. Commit/push yok.

2026-10-21 yerel: Yanıt değişiklik geçmişindeki kayıt açılır kutuları yerine Zaman/kayıt, Değişen alan, Önceki yanıt, Yeni yanıt tablosu eklendi. En yeni kayıt önce, alan filtresi, 30 satırlık kademeli gösterim ve uzun metinde tam metni aç seçeneği var. Önceki revizyon eksikse karşılaştırılamıyor açıklaması korunur. Alan filtresi aynı dosya yeniden çizildiğinde korunur. 125 Chrome ve uzun metinlerle 30 yerleşim kontrolü geçti; tablo görseli incelendi. Kanıtlar test-evidence/revision-table-{browser,long-text}.txt. İlk test denemesi test dosyasında değişken adı çakışması nedeniyle başlamadı; düzeltme sonrası tam koşu geçti. DB verileri değişmedi; commit/push yok.

2026-10-21 uzun metin yerleşimi: Karşılaştırma hücrelerinde açık pre-wrap, başlık/puan hücrelerinde normal satır kırma ve sabit sütunlar tanımlandı; genel tablo nowrap kuralı bu tablolarda geçersiz kılındı. 1440/768/390 px genişliklerde uzun paragraflar ve 60.000 boşluksuz karakterle 30 Chrome yerleşim kontrolü geçti: hücre taşması yok, sayfa yatay taşmıyor, metin bütünü korunuyor, satırlar uzuyor ve mobilde yatay kaydırma yalnız tablo kapsayıcısında. Kanıt: test-evidence/table-long-text.txt; test: tests/table-layout.cjs. JS syntax/diff kontrolü geçti. DB erişimi yok; commit/push yok.

2026-10-21 yerel: Kesinleşmiş başlangıç ve son teslim yanıtları tek karşılaştırma tablosunda yan yana gösteriliyor. Sekiz satır: ön bilgi, güven, zorluk, ciddiyet, temel problem, karar, gerekçe, yansıtma. Yalnız immutable snapshotlar kullanılır; taslakla doldurulmaz. Aşamada sorulmayan, henüz kesinleşmeyen ve eski kayıtta yanıtı bulunmayan alanlar ayrı açıklanır. 123 Chrome kontrolü, JS syntax ve diff whitespace geçti; tablo görseli incelendi. İlk Chrome denemesinde iki tabloyu birlikte seçen eski test seçicisi daraltıldı, tam koşu tekrar geçti. Çalışma DB değişmedi; commit/push yok.

2026-10-21 yerel düzenleme: Araştırmacı yanıtlarında altı puan tek tabloda (ön bilgi, başlangıç güveni, zorluk, başlangıç ciddiyeti, nihai güven, nihai ciddiyet). Öğrencinin nihai değerlendirmesine zorunlu 0–10 final_severity alanı eklendi; autosave, başlangıç kilidi, teslim doğrulaması, teslim snapshotı ve mevcut ham JSON/Excel veri akışında korunur. Eski teslimler değiştirilmez; eksik eski değer Yanıt yok gösterilir. Şema değişikliği yok. Ayrı sentetik ortamda 81 domain/SQLite, 50 doğrulama, 119 Chrome ve 11 süreç UI kontrolü geçti; puan tablosu ekran görüntüsü incelendi. Çalışma veritabanına yazma/seed/reset yok. Commit/push yok.

2026-10-21 yerel arayüz düzenlemesi: “İç araştırmacı notu ekle” düğmesi üst başlıktan alınarak “İç not geçmişi (öğrenciye gösterilmez)” açılır bölümünde başlığın altına taşındı. Not ekleme işleyicisi korundu. JS sözdizimi ve git diff --check geçti; tarayıcı kontrolü yapılmadı. Commit/push yok.

**Güncel — 2026-10-21, 0.2.10 (GitHub main):** Kullanıcının onayladığı akış: etiketli soru gönderimi (anlık) → AI yanıtı hazırlanıyor (ayrı bekleme süresi) → yanıt geldi (anlık) → AI sekmesindeyse görüntüleme. Bekleme bölüm değişse de aynı trace/request eşleşmesiyle devam eder; timeline_lane=ai_wait olarak öğrenci bölüm ziyaretlerinden ayrı tutulur. Disco Timeline Lane ve Response Section alanlarını taşır. Üstteki bölüm toplamlarına bekleme ayrıca eklenmez; eksik yanıt kaydında süre null kalır. Eski soru noktaları/araştırmacı etiketleri ve teslimde takibin durması korunur. 82 navigation, 115 Chrome, 38 export, 28 etiket/süreç ve 75 SQLite kontrolü geçti; sözdizimi geçti. 40 saniye senaryosu sentetik zaman fixture'ıdır; gerçek sağlayıcı gecikme ölçümü değildir. Mevcut çalışma DB'sinde yazma/seed/reset/yayın/etiket değişikliği yok. Gerçek AI/hosting/Disco import kabulü açık. Kod GitHub main dalına 5f8a34964654bd3cbc94dc469447328ee7aab243 commit'iyle gönderildi.

**Önceki aşama — 2026-10-21, 0.2.9 (yerel):** Kullanıcının ekranında eski iki sorunun kaybolduğu saptandı. Salt okunur çalışma DB incelemesinde 2 gerçek öğrenci mesajı ve 2 response_visible var, message_send yok; ilk soru zaten Bilgi istedi etiketliydi. Kullanıcı sonradan ikinciyi Eleştiri istedi olarak etiketledi; iki güncel araştırmacı seçimi salt okunur doğrulandı. Eksik eski gönderim kaydı artık soruyu gizlemez: sunucu mesaj kaydından ayrı soru noktası gösterilir, kaynağı belirtilir, bekleme süresi null/Ölçülemedi kalır. Disco noktasında Duration Seconds boş ve Time Source server_message; istemci saatinden süre uydurulmaz. Yeni kayıtların soru/yanıt/görüntüleme aralıkları korunur, çift soru eklenmez. 77 navigation, 114 Chrome, 38 export, 28 etiket/süreç, 75 SQLite ve sözdizimi kontrolleri geçti. Kullanıcı yeni öğrenciyle denemeyi seçti; sonuç henüz bildirilmedi. Gerçek DB yazma/seed/reset/yayın/etiket değişikliği yapılmadı; yeni otomatik manuel batch dayatılmadı; commit/push yok.

**Önceki aşama — 2026-10-21, 0.2.8 (yerel):** Kullanıcının son ölçüm kuralı: AI sorusu gönderildi → yanıtın tarayıcıya gelişi → öğrenci AI sekmesinde kalıyorsa görüntüleme → sonraki soru/yanıt/görüntüleme → bölüm geçişi. Soru/yanıt bekleme ve görüntüleme ayrı aralıklardır; yanıt gelişi sıfır süreli nokta olayıdır. Aynı türden tekrar sorular ayrı kalır. navigation.activities ve export navigation_activities bu sıralı aralıkları içerir; Disco varsayılanında bu satırlar kullanılır. Parent Visit ID ile özgün ziyaret korunur; süresi tekrar eklenmez. Başka bölümde gelen yanıt öğrenciyi AI sekmesine geçmiş göstermez. Eksik geliş kaydı/saat bozukluğu açık belirtilir, zaman tahmin edilmez. 69 navigation, 113 Chrome, 38 export, 28 etiket/süreç ve 75 SQLite kontrolü geçti; sözdizimi kontrolleri geçti. Mevcut çalışma DB'sinde yazma/seed/reset/yayın yok; manuel batch/commit/push yok. Gerçek AI/hosting/Disco import kabulü açık.

**Önceki aşama — 2026-10-21, 0.2.7 (yerel):** AI ziyaretlerinde yalnız taslak yazma/görüntüleme = AI sohbeti görüntüleme; gerçekten gönderilen öğrenci mesajları istek anahtarıyla eşleştirilerek etkinlik adı güncel etiketlerle oluşturulur (AI sohbeti · Bilgi istedi vb.). Araştırmacı seçimi, boş seçim dahil önceliklidir. Bir ziyarette çoklu soru/etiket + ile birleştirilir; süre çoğaltılmaz. Etiket bekliyor/eşleşme yok/araştırmacı boş seçimi ve eşleşmeyen gönderme denemesi ayrılır. Aynı adlar araştırmacı API/ekranı, navigation_visits Excel/JSON ve Disco Activity alanında kullanılır. 49 navigation, 109 Chrome, 38 export, 28 etiket/süreç, 75 SQLite kontrolü ve PHP/JS sözdizimi geçti. Gerçek DB değişmedi; yeni manuel batch, commit/push yok. Gerçek AI/hosting/Disco import kabulü bekliyor.

**Önceki aşama — 2026-10-21, 0.2.6 (yerel):** Ziyaret etkinliği yedi gerçek sekme adı + görüntüleme/güncelleme biçiminde. Kilit durumu etkinlik yerine ayrı Ek bilgi alanında; Nasıl bitti? gerçek hedef sekmeyi gösterir. Sayfanın görünmez olduğu gözlemi açık dille açıklanır; neden/süre tahmin edilmez. CSV aynı etkinlik adlarını ve ayrı Next Section, Next Section Name, Locked sütunlarını taşır. Süre hesabı ve son teslimde takibin durması korundu. Ayrı sentetik ortam: 36 navigation, 105 Chrome, 38 export, 11 süreç JS kontrolü; PHP/JS sözdizimi geçti. Gerçek çalışma veritabanına müdahale, yeni manuel batch, commit/push yok. Gerçek hosting/AI/Disco import kabulü bekliyor.

**Önceki aşama — 2026-10-20, 0.2.2:** Kullanıcı isteğiyle öğrenci Vakalarım ekranındaki Katılım bilgilerim düğmesi ve bağlı istemci formu kaldırıldı. Mevcut metadata/araştırmacı Program-Sınıf alanları/API/DB korundu. JS syntax/indexPHP lint ve ayrı sentetik DBde69Chrome kontrolü geçti (test-evidence/student-0.2.2-browser.txt). Diğer test grupları bu küçük UI değişikliğinde yeniden çalıştırılmadı;0.2.1deki12grup547kontrol tarihsel kanıttır. Gerçek ortam kabulü açık; yeni manuel batch yok. Commit/push yok.

2026-10-20: 0.1.0 sentetik pilot uygulama kodlandı. PHP/MySQL iki panel, revision/kilitler, mock/compatible adapter, olaylar, sürümlü tanımlar, sıralı analiz, karar/rapor/yayın ve gerçek format indirme var. Yerel SQLite/MariaDB/HTTP/Chromium testleri çalıştırıldı; ayrıntı TEST_RESULTS.md. Gerçek AI/hosting/kurum rubriği/onam ve kullanıcı kabulü henüz yok.

## Okuma sırası

1. Bu dosya.
2. plan.md.
3. 01_OGRENCI_PANELI.md.
4. 02_ARASTIRMACI_PANELI.md.
5. 03_CODEX_GELISTIRME_PROMPTU.md.
6. 04_MANUEL_TEST_REHBERI.md.

## Son kabul edilmiş kararlar

- Vaka sürekli açık/sabit panel değil. Tek çalışma sayfasında bölüm gezinmesi: Vaka, Profil, Başlangıç, AI, Nihai, Yansıtma.
- Profil 0–10 dört alan; başlangıçta boş. Başlangıçla birlikte kilitlenir.
- Başlangıç üç alan; sunucuda kesinleşmeden AI açılmaz. Kesinleşmiş metin değişmez.
- AI vaka sürümünü bilir; profil/başlangıç/nihai/yansıtma/rubrik/etiket otomatik verilmez.
- AI bitirme düğmesi yok. Nihai/yansıtma ve AI arasında serbest geçiş.
- Yanıt sürerken ikinci AI mesajı veya kuyruk yok. Soru taslağı yazılabilir; diğer bölümler çalışır.
- Nihai/yansıtma tek teslimle kilitlenir; süreç salt okunur görünür kalır.
- Kalıcı silme yok. Arşiv/dondurma/analiz dışı ve tüm geçmiş sürümleri korunur.
- Öğrenci numarası panel kimliği; iç teknik kimlik değişmez. Her araştırmacının ayrı çalışma alanı vardır, ortak vaka üyeliği yoktur. Kodlanmış export ayrı.
- Araştırmacı katılımcı/vaka/atama yönetir; Genel durum vaka seçimi ve aranabilir öğrenci listesidir, geniş matris kaldırıldı.
- Olay, taslak ve süreler process mining için tutulur. Kopyalama kaynak metin/sürüm/aralıkla kaydedilir.
- Görünürlük/odak/etkileşimsizlik okuma veya düşünme diye yorumlanmaz.
- AI üretimi ve öğrenci etkinliği ayrı kulvar; süreler çift sayılmaz.
- Etiketler sade, çoklu ve araştırmacı tarafından değiştirilebilir/eklenebilir. AI önerisi ayrı korunur.
- Rubrik serbest metin + yapılandırılmış boyut/puan/ağırlık; gerçek rubrik henüz yok.
- Bir öğrenci-vaka, ayrı sıralı analiz adımları. Toplu AI analizi yok.
- Başlangıç ve nihai rubrik çağrıları bağımsız; diğer metin/puan gösterilmez.
- Sayısal fark/toplam uygulamada; AI yalnızca öneri ve kanıt sağlar.
- Kabul/düzenle/reddet/karar verilemedi. Araştırmacı kararı ham veri/AI önerisini ezmez.
- Rapor onayı ve yayını ayrı. İlgili vakada veri toplama ve öğrencinin kişisel ek süresi kapalı değilse rapor/puan yok. Vaka yeniden açılır/kapanır; global beş vaka koşulu kaldırıldı.
- Öğrenci yalnızca onaylı yayımlanmış karar/puan/rapor görür; iç AI taslakları görünmez.
- Word/PDF rapor, gerçek XLSX/CSV süreç çıktısı ve JSON yapısal çıktı. Toplu indirme var.
- Kullanıcı yeni manuel batch istemiyor; yerel otomatik kontroller önce yürütülür. Yalnız otomatik doğrulanamayan kritik dış kabul adımları kısa ve açık istenir.

## Geçersiz kalan önceki fikirler

Sabit vaka yan paneli; AI etkileşimini bitir düğmesi; başlangıcın nihai yanında otomatik gösterimi; kalıcı silme; veri toplama sürerken öğrenci raporu; tüm öğrencileri tek AI çağrısıyla değerlendirme. Bunları geri getirme.

## Kritik teknik riskler

PHP session lock uzun AI isteğinde autosave'i engelleyebilir. Oturum kilidini güvenli serbest bırak; kısa DB transaction ve ayrı AI işlem kilidi kullan. Hosting streaming tamponlayabilir; sahte streaming zamanı üretme. Yeniden denemeler çift mesaj/yanıt oluşturmamalı. Offline olay sırası ve süre belirsizliği korunmalı. Yerel kurtarma ortak cihazda başka öğrenciye sızmamalı. Üretim verisi test seed/reset ile temizlenmemeli.

## Bilinmeyenler

Hosting sürüm/limitleri; AI sağlayıcısı/modeli; gerçek rubrik; kurum onam/saklama süreci; autosave/heartbeat/idle eşikleri; etiketleme tetikleme zamanı; yedek düzeni. Çoklu araştırmacı ayrı alanlar olarak uygulandı. Teknik varsayımı belgele; kullanıcı kararı gibi gösterme.

## Oturum sonu güncelleme şablonu

- Son çalışma özeti:
- Gerçekten tamamlanan aşamalar ve dosyalar:
- Çalıştırılan testler/sonuçları:
- Çalıştırılamayan testler:
- Açık hatalar ve yeniden üretim:
- Sonraki ilk somut adım:
- Ortam/kurulum notları (sır içermez):
- Kullanıcıdan beklenen test dönütü:

İlk başlangıç kaydı tarihçedir. Güncel devam noktası aşağıdaki son oturum kaydıdır; uygulamayı yeniden üretme.

## Geliştirme başladı — P01
Şartnameler okundu; mimari ve güvenli public kökü seçildi. Yerel PHP/MySQL henüz yok. Gerçek AI yerine test adapter kullanılacak; sağlayıcı/hosting doğrulaması yapılmadı.

P02–P07 kodu eklendi; henüz doğrulanmadı. PHP yerel .tools içine indirildi; Windows VC runtime sorunu araştırılıyor. Config ve veriler public dışında tutuluyor.

P08–P13 ilk uygulaması yazıldı. PHP 8.3.35 taşınabilir runtime çalışıyor; Composer bağımlılıkları kuruluyor. Henüz MySQL yok. Tüm test durumları doğrulama bekliyor; sonraki iş seed ve otomatik kontroller.

Sentetik seed ve test hesap haritası kodlandı. tests/run.php 67 kontrol geçti (SQLite, PHP 8.3.35; vendor kurulu). HTTP/session concurrency çalışıyor; domain sonuçları gerçek sağlayıcı/hosting kanıtı değildir. Sonraki: tarayıcı, belge ve MySQL doğrulama.

## SON OTURUM KAYDI — 2026-10-20 · 0.1.0

- Son çalışma özeti: Çalışan PHP/MySQL sentetik pilot, iki responsive panel, schema 001 ve CLI, mock + compatible AI adapter, rapor/export ve tüm teslim belgeleri eklendi. Mevcut özgün şartnameler korundu.
- Dosyalar: app/{Auth,Db,Rules,App,Ai,Telemetry,Admin,Analysis,Export}.php; public/api.php/index.php/assets; bin/console.php; migrations/001.sql; composer.json/lock + yerel vendor; tests/*; README/INSTALL_SHARED_HOSTING/TEST_DATA/MANUAL_TESTS/TEST_RESULTS/DATA_DICTIONARY/EVENT_CATALOG/ARCHITECTURE/AI_INPUT_CONTRACTS/BUGS/CHANGELOG.
- Gerçek testler: PHP lint/JS syntax; CLI check; Composer validate/audit; SQLite 69 assertion; MariaDB 11.4.12/PDO mysql 69 assertion; iki yerel PHP worker ortak session ile 42 HTTP assertion; Google Chrome headless masaüstü/390px emülasyonda 18 kontrol. Yerel MariaDB yedeği ayrı restore DB'ye alındı, sekiz tablo sayısı eşit. PDF altı sayfa render/metin çıkarımıyla Türkçe incelendi; DOCX/XLSX gerçek paket, XLSX yeniden açıldı. Kanıt test-evidence/.
- Test komutları: php tests/run.php; python tests/http_checks.py; node tests/browser.cjs; MARIADB_HOME ayarlı python tests/mysql_checks.py. PHP_BIN/PLAYWRIGHT_PATH/CHROME_BIN gerektirebilir. Windows sandbox is_readable yanlış negatif verdiği için gerçek dosya testleri normal izinli yerel süreçte tekrar çalıştırıldı ve geçti. Sisteme global kurulum yapılmadı.
- Ortam: taşınabilir PHP .tools/php/php.exe (8.3.35); .tools/composer.phar; .tools/mariadb/mariadb-11.4.12-winx64. Bunları üretime yükleme. Config/local.php git dışı yerel SQLite, storage/vakalab_test.sqlite sentetik fixture. Parola yalnız TEST_DATA.md'de; buraya sır eklenmedi.
- Önizleme http://127.0.0.1:8088/ üzerinde PHP dev server. Çalışmıyorsa `.tools/php/php.exe -S 127.0.0.1:8088 -t public`. Windows dev server tek worker; AI gecikmesinde concurrency testini iki worker/gerçek hostingde yap.
- Açık kontroller: gerçek AI/API/model; gerçek hosting HTTPS/alt klasör/worker/izin/restore; kurum rubriği ve onam/saklama; fiziksel mobil klavye/ekran okuyucu; Word görsel açılışı; gerçek kesinti/crash ve ortak cihaz kurtarması; büyük veri. Hiçbiri geçti sayılmadı. BUGS.md OPEN-01…06.
- Ürün sınırları: non-stream fallback, ilk parça ölçülmez. sessionStorage kurtarma aynı sekme/hesap/vaka içindir, çıkış temizler, sekme kapanışı kayıp yaratabilir. localStorage yalnız advisory lease; asıl koruma sunucu revision/aktif AI kilidi. Aynı veri boşuna yeniden seed edilmez; seed geçmişi/parolayı sıfırlamaz.
- Sonraki ilk somut adım: Kullanıcı TEST-OGR-01 ile manuel başlangıç→AI→nihai/yansıtma→teslim akışını denesin; TEST-ARASTIRMACI matristen süreci incelesin. Yayın kapısını ana akıştan sonra ayrı test DB'de dene; global kapanış yazmaları kapatır.
- Kullanıcıdan beklenen dönüt: MANUAL_TESTS.md test ID, cihaz/tarayıcı, sentetik hesap/vaka, adımlar, beklenen/gerçekleşen ve hata kodu. Gerçek parola/anahtar/katılımcı metni isteme.
- P17 kullanıcı manuel testi ve P18 son kabul BEKLİYOR. Uygulama üretime hazır ilan edilmedi. Gerçek ortam erişimi verilmediği için hosting kurulumunu yapılmış gibi göstermeyin.

Son teslim kontrolü: yerel önizleme normal yerel PHP süreciyle 127.0.0.1:8088 üzerinde açık; araştırmacı girişi ve sentetik PDF/DOCX indirme HTTP 200 doğrulandı. Windows sandbox ağ kısıtı nedeniyle preview sunucusu normal izinli süreçte başlatıldı. Plan/sonuç belgeleri HTTP 42 son sayısına güncellendi.

## Güncel devam noktası: kullanıcıyla adım adım manuel test
Kullanıcı PC'yi yeniden başlattı; yerel sunucu başlatma yönergesi verildi. Ekran görüntüsü/çıktıyla her adımı birlikte değerlendirmeyi ve gerektiğinde düzeltmeyi istedi. MANUAL_TEST_RESULTS.md kaydı açıldı. Aktif adım M01: TEST-OGR-01 giriş ve Vakalarım ekranı. Kanıt bekleniyor; manuel pass yok. Sonraki adımı kanıtı gördükten sonra ver; bütün test listesini birden yükleme. Parola yalnız TEST_DATA.md içinde, ekran görüntüsünde parola isteme.

Manuel test güncellemesi: M01 görünür ana sayfa kontrolleri GEÇTİ. Kanıt kullanıcı Chrome/Windows 1920x1080 ekran görüntüsü; hesap TEST-OGR-01, mock banner, 4 atama sayacı. İlk vaka Başlangıç aşamasında/Devam et; onu sıfırlama. Aktif M02: ikinci vaka SENTETİK · Sınıfta dijital araç kullanımı → Başla; ilk çalışma ekranı screenshot bekleniyor. Sonraki kontroller altı bölüm ve AI/nihai/yansıtma kilitleri; henüz geçmedi.

Güncel manuel devam: M02 ilk vaka ekranı/görünür kilit göstergeleri GEÇTİ (de8c1b45 ekran görüntüsü). Aynı TEST-OGR-01 ikinci vaka sürüm 1; metin girişi yapılmadı. Aktif M03: 4. AI sohbeti · Kilitli tabına tıkla; kilit açıklaması ve mesaj kutusu/Gönder olmaması screenshot bekleniyor. Sunucu engeli bu screenshotla doğrulanmış sayılmayacak.

Güncel aktif manuel adım M04. M03 GEÇTİ: 6c6df1c8 screenshot AI kilit açıklaması ve mesaj kutusu/Gönder yokluğu. M04 için kullanıcıya 2. Vaka profili tabına tıklayıp hiçbir değer seçmeden dört alanın ekran görüntüsünü paylaşması söylenecek. Beklenen dört Seçilmedi. Aynı TEST-OGR-01 ikinci vaka sürüm 1; veri girilmedi.

Güncel kullanıcı tercihi: tek ekranlık adımlar çok yavaş; birkaç kontrolü aynı batch halinde ver. M04 GEÇTİ (20d89333 screenshot dört Seçilmedi). Aktif batch M05–M08, aynı ikinci vaka: profil 5/8/6/ciddiyet boş + üç başlangıç metni; eksik kesinleştirme engeli; sonra ciddiyet7, sunucu kaydı/refresh; onay iptal kilitlememeli; son onay AI açmalı ve başlangıç/profil kilitlenmeli. Kullanıcı sonuçlarını/screenshotlarını bekle; şimdiden pass yazma.

## SON DEVAM — 0.1.1 kullanıcı dönütü
Kullanıcı screenshotlarla eksik alanın kolay bulunmasını ve final/reflection tamamlanmadan Tek son teslim düğmesinin kapalı olmasını istedi. İkisi düzeltildi: validation.js kalıcı linkli hata özeti/alan/sekme vurgusu/focus, canSubmit dört final gereksinimi + sunucu kaydı + AI/online durumunu izler. Asset query sürümü0.1.1; Ctrl+F5 gerekir. Mevcut veri sıfırlanmadı, başlangıç açılmadı. Salt okunur SQLite durum: TEST-OGR-01/test-case-2 working, initial_locked true, submitted false, dört final alanı boş. Kullanıcının ekranı erken aktif düğmeyi doğruluyor; gerçekten eksik teslim edilmiş değil. 30 hedefli Chrome + 18 mevcut browser kontrolü geçti; ilk link/blur timeout FIX-11 ile düzeldi. Kanıt test-evidence/validation-results.txt ve browser-validation-regression.txt. M05/M08 kullanıcı kanıtıyla geçti; M06/M07 kısmi. Aktif batch M09–M11: Ctrl+F5, ikinci vakaya dön, teslim boşken kapalı; final karar/gerekçe/güven tamamken reflection eksik kapalı; reflection ve kayıttan sonra açık; alan silince kapalı/geri doldurup kayıtta açık. Henüz teslim yapmasın; sonraki batch AI sohbeti testleri. Eksik profil highlight manuel tekrar istenirse üçüncü açılmamış vaka kullan, kilitli ikinci başlangıcı açma. Kullanıcı daha hızlı birkaç adımlık batch tercih ediyor.

## SON DEVAM — 0.1.2
Kullanıcı son teslim kartını her sayfa altında istemedi; ayrı en son tab istedi. Artık 7. Son teslim (section=confirm) var. Diğer altı bölümde kart/düğme yok; rapor linkleri/teslim zamanı da son tabda. Başlangıçtan önce kilitli; eksik alan/kayıt/AI koşulları aynı. Telemetry confirm kabul ediyor, DB migration yok. Assetv0.1.2, Ctrl+F5 gerekir. 40 hedefli + 18 mevcut browser kontrolü ayrı sentetik veride geçti; test-evidence/submit-tab-*. Mevcut kullanıcının verisi değiştirilmedi. d35a305b screenshot eski footer'da dört eksik link ve disabled submit gösteriyor; M09 görünen kapı geçti, M10–11 hâlâ bekliyor. Yeni aktif M12: Ctrl+F5 → ikinci vaka → altı bölümde teslim yok, 7.Son teslim'de eksik alanlar ve kapalı düğme; screenshot bekleniyor. Kullanıcı birkaç adımlık kısa batch istiyor; henüz teslim yaptırma, sonraki sohbet testleri bekliyor.

## SON DEVAM — 0.1.3
Kullanıcı genel Eksik alanları tamamlayın kutusunun AI/Nihai/Yansıtma vb. sayfalarda görünmesini istemedi; yalnız Son teslim'de istedi. Artık validation-summary sadece section=confirm iken render edilir; tek eksik listesi orada, linkle hedef alana taşınmaz. Alan içi hata/focus ve sekme işaretleri korunur. Footer'da mükerrer liste yok. Assetv0.1.3; Ctrl+F5. Mobil kirli alandan sekmeye geçişte blur/autosave tıklamayı düşürüyordu: pointerdown erken blur'u engeller, asıl kayıt mevcut click akışında yapılır (FIX-13). İlk timeoutlar sonrası 50 hedefli kontrol geçti; kanıt test-evidence/summary-only-validation.txt. Eski genel/backend suite yeniden çalıştırılmadı. Mevcut kullanıcı verisi değiştirilmedi. Aktif manuel M13: Ctrl+F5, aynı ikinci vaka, diğer bölümlerde genel kutu yok, Son teslim'de liste var, linkle alana gidince kutu yok. Kullanıcı onayı bekleniyor; henüz teslim yaptırma. Kullanıcı birkaç adımlık kısa batch tercih ediyor.

## AKTİF DEVAM — yeni test turu R02, batch yaklaşımı
Kullanıcı test yönergelerini baştan, birkaç adımı bir arada batch halinde istedi. Salt okunur yerel DB kontrolü: TEST-OGR-01 üçüncü Akranlar arası anlaşmazlık (test-case-3) ve dördüncü vaka hiç açılmamış. Yeni tur üçüncü vakada başlayacak, ilk/ikinci eski veriyi bozma; seed/reset yok. Aktif R02-B01: Ctrl+F5, çıkış/giriş TEST-OGR-01; Vakalarım + 4 atama; üçüncü vaka Başla; doğru metin/7tab; AI/Nihai/Yansıtma/Son teslim kilit açıklaması; profil dörtSeçilmedi; çalışma sekmelerinde ortak eksik/teslim kartı yok. Değer doldurmayacak/kesinleştirmeyecek/teslim etmeyecek. İki screenshot (ilk vaka ekranı + boş profil), kilitler için kısa kullanıcı sonucu bekle. Altı kontrol BEKLİYOR, geçmiş sonuçları yeniden pass sayma. Sonraki batch başlangıç doğrulama/kayıt/kilit olacak; bütün test listesini birden verme.

## AKTİF DEVAM — R02-B02
Kullanıcı R02-B01 için hepsi tamam dedi; altı kontrol kullanıcı beyanıyla geçti, yeni screenshot yok. Aynı TEST-OGR-01 / üçüncü vaka Akranlar arası anlaşmazlıkta devam. Batch2: profil 5/8/6+ciddiyet boş; üç başlangıç metni doldurup kesinleştirmeyi dene → ciddiyet alanına focus/yan hata, AI kilitli. Ciddiyet7 ve sunucu kaydı sonrası refresh → metin/profil korunmalı. Başlangıç onayını önce iptal → düzenleme devam/AI kilitli. Sonra kabul → profil/başlangıç salt okunur/AI açık. Son teslim tabı nihai/yansıtma boşken eksikler/disabled submit. Henüz AI mesajı/son teslim yok. B02 altı kontrol bekliyor. Kullanıcı toplu birkaç adımlık batch ve kısa tamam bildirimi tercih ediyor; sorun olursa screenshot/çıktı al ve düzelt.

## AKTİF DEVAM — R02-B03
Kullanıcı Batch2 için hepsi tamam dedi; altı kontrol kullanıcı beyanıyla geçti. Aynı TEST-OGR-01 / üçüncü vaka başlangıcı kilitli, AI açık. Batch3 normal mock: ilk soru gönder ve tek soru/tek mock yanıtı kontrol et; nihai karar/gerekçe/güven10 doldur/kaydet; AI'a dön ikinci soru gönder; nihai taslak korunmalı; kayıt sonrası refresh ile sohbet iki soru/iki yanıt ve nihai taslak korunmalı; Yansıtma boşken Son teslim'de yalnız yansıtma eksik/disabled; linkle yansıtmaya git, doldur/kaydet, Son teslim'de eksik yok/button enabled. Henüz teslim yaptırma. Normal hızlı mock testi concurrency kanıtı değildir; Windows devserver tek worker uyarısını unutma. Kullanıcı kısa batch'ler ve tamam bildirimi tercih ediyor; hata halinde screenshot alıp düzelt.

## AKTİF DEVAM — R02-B04
Kullanıcı Batch3 hepsi tamam dedi; altı kontrol beyanıyla geçti, d671177d screenshot ilk usermessage ve açık SENTETİK MOCK cevabını gösteriyor (ikinci mesaj/nihai/reflection screenshotta doğrulanmadı). Aynı TEST-OGR-01 üçüncü vaka nihai/yansıtma dolu ve kaydedilmiş varsayımı kullanıcı raporu. Batch4: nihai gerekçe sil→son teslim disabled/tek eksik; geri yaz/save→enabled. İkinci normalsekmede aynı vakayı aç→readonlywarning/disabled, kapat. Ana sekmede son teslim confirm önceCancel→veri açık; sonraaccept→gerçek sentetik teslim. Bölümler/refresh tümreadonly/messageskorundu. Vakalarım üçüncü kart Teslim edildi/Görüntüle, tekraraynıkilit; öğrenci rapor/puan yok. B04 altı adım henüz bekliyor. Kullanıcıya iki screenshot öner: ikinci sekme uyarısı ve teslim sonrası Son teslim zaman/durum. Global studykapatma yok, sonraki araştırmacı incelemesi. Kısa batch yaklaşımı sürüyor.

## AKTİF DEVAM — R02-B05
Kullanıcı Batch4 hepsi tamam dedi; altı kontrol kullanıcı beyanıyla geçti. f3512ebe screenshot üçüncü vaka Akranlar arası anlaşmazlık Teslim edildi, salt okunur tablar ve 20.10.2026 19:29:55 gösteriyor; ikinci sekme/cancel/reopen ayrı screenshotta yok. Öğrenci akışı sentetik teslimle tamamlandı. Batch5: çıkış TEST-ARASTIRMACI login→matris TEST-OGR-01 × üçüncü vaka Teslim edildi hücresi→doğru dosya. Hamprofil5/8/6/7, başlangıç/nihai/reflection metinleri, finalconf10 ve snapshotları kontrol; iki soru/iki mockcevap; taslak/olay/süre kalitesi ve çıkarım yokluğu. İç araştırmacı notu ekle + gerekçe + refreshkalıcılık. Henüz analiz/yayın/global kapanış başlatma. B05 altı adım bekliyor; sonraki batch tekvaka analiz/etiket/karar sürecine hazırlanacak. Parola yalnız TEST_DATA.md; kullanıcı birkaç adımlık batch tercih ediyor.

## Güncel devam — 0.1.4 ve R02-B06
R02-B05 altı adım kullanıcı “Tüm maddeler TAMAM” beyanıyla geçti; screenshotlar matris/doğru dosya, profil/yanıt/snapshot, dört mesaj, taslak/olay/süreyi destekliyor. İç not kalıcılığı yalnız kullanıcı beyanı. Tarihler için time.js eklendi: matris son etkinliği, süreç başlangıç/teslimi, öğrenci teslimi ve global kapanış yapılandırılmış timezone ile dd.MM.yyyy HH:mm:ss (UTC+03:00) gösterilir. time datetime/title tam UTC hassasiyetini korur; ham JSON/DB/export değişmedi, ham veri ekranında UTC açıklaması var. Asset0.1.4, Ctrl+F5 gerekir. 10 tarih kontrolü + 22 Chrome masaüstü/mobil emülasyon kontrolü ayrı sentetik DB'de geçti. İlk UTC sıfır offset beklentisi testte farklı çıktı; çıktı UTC+00:00 olarak normalize edilip tekrar geçti. Backend/MySQL/restore/50validation suite yeniden çalıştırılmadı. Kullanıcı DB'si sıfırlanmadı/değiştirilmedi.
Aktif R02-B06 altı adım BEKLİYOR: tarih manuel kabulü, ilk öğrenci mesajına iki etiket/gerekçe ve refresh geçmişi; aynı mesaja tek etiket/gerekçeyle ikinci sürüm eskiyi korumalı; test rubriğiyle 0/8 yeni analiz; adım1 bütünlük completed/çıktı-girdi; adım2 güven8→10 difference2 ve 2/8. Sonra dur; adım3–8/rapor/yayın/global kapanış henüz yok. Kısa batch yaklaşımı sürüyor. Sonuç ayrıntıları MANUAL_TEST_RESULTS.md.

## Güncel devam — 0.1.5 etiket sunumu ve R02-B07
R02-B06 altı kontrol kullanıcı “tüm maddeler TAMAM” bildirimiyle geçti. Screenshotlar iki etiket review (önce Bilgi istedi+Açıklama istedi, sonra Metin düzenletmek istedi), bütünlük submitted/revision19/warnings[], güven8→10/fark2 ve mevcut analiz f2e4ae74 2/8 gösteriyor. Tarih/refresh/girdi ayrıntıları kullanıcı beyanı. Ham JSON etiket sunumu kullanıcıya anlaşılmaz geldi: 0.1.5'te labels.js model + güncel seçim, işaretli checkboxlar, mesaj bağlamı, canlı önce/sonra/eklenen/kaldırılan önizlemesi, isimli/tarihli/gerekçeli/aktörlü karar geçmişi var. Geçmiş JSON kapalı ayrıntıda. AI önerileri güncel araştırmacı kararıyla karışmaz; son boş review önceki dolu seçimi geçersiz kılar. DB/schema/API yazımı değişmedi; mevcut veriye dokunulmadı. 8 model testi ve ayrı sentetik DB'de 35 Chrome kontrolü geçti (test-evidence/label-history-*.txt); desktop ekran görsel incelendi, 390px dialog taşmıyor. JS syntax/index PHP lint geçti. Backend/MySQL/restore/validation suite bu UI değişikliğinde yeniden çalıştırılmadı. Asset0.1.5, Ctrl+F5.
Aktif R02-B07 BEKLİYOR: yeni etiket ekranını kontrol/değiştir-iptal; aynı 2/8 analizde step3 çalıştır/yalnız initial girdisi/kabul+gerekçe; step4 çalıştır/yalnız final girdisi/kabul+gerekçe ve refresh. 4/8'de dur; yeni analiz/global kapanış/rapor yok. Mock puanlar gerçek AI/kurumsal rubrik kabulü değildir. Manuel ayrıntılar MANUAL_TEST_RESULTS.md.

## Güncel açıklama — Batch7 tekrar bekliyor
Kullanıcı etiketin güncellenmemesinin gerekçeyi boş bırakmasından kaynaklandığını doğruladı; kayıt hatası iddiasını geri çekti. Salt okunur DB'de ilk iki review vardı, yeni Eleştiri kaydı yoktu. Gerekçe zorunluluğu beklenen davranış. Hata bildiriminin dialog arkasında kalması ayrı UX kusuru olarak yeniden üretildi (label-save-error-before.txt: 26 kontrol sonrası görünür dialog-error beklerken timeout). Görünür modal hata/odak ve başarı bildirimi için 0.1.6 kodu yazıldı; genişletilmiş test komutu kullanıcı tarafından kesildi, son koşu tamamlandı/geçti sayılmayacak. Önceki 0.1.5 35+8 sonucu korunuyor. Mevcut kullanıcı DB'sine yazılmadı. Kullanıcı şimdi Batch7 yönergelerini tekrar istiyor: aynı TEST-OGR-01 üçüncü vaka ve mevcut 2/8 analiz, etiket görünümü/iptal, step3 initial girdisi/kabul+zorunlu gerekçe, step4 final girdisi/kabul+zorunlu gerekçe/refresh; 4/8'de dur. Tüm B07 maddeleri bekliyor. 0.1.6 doğrulamasını sonraki geliştirme turunda tamamla; henüz başarı ilan etme.

## Güncel devam — 0.1.7 ve R02-B08
R02-B07 altı kontrol kullanıcı “adımlar tamam” bildirimiyle geçti. Screenshotlar initial/final mock çıktılarını ve mevcut f2e4ae74 4/8 gösteriyor; kabul kararları/girdi ayrıntıları/refresh kullanıcı beyanı (screenshotlar henüz [] karar gösteriyor). Kullanıcı sekiz adımın baştan görünmesini istedi. analysis.js + renderRun sekiz görünür kart, Tamamlandı/Sıradaki/Kilitli/Çalışıyor durumları, her adım altında sonuçlar ve inceleme, geçmiş denemeler, araştırmacı karar durumu ekler. Yalnız sıradaki adım çalışır; istek sürerken tüm düğmeler kapanır. Adım8 önce3–7 kararlarını ister, eksikleri adları/numaralarıyla açıklar. Backend/DB/şema değişmedi; kullanıcı verisi sıfırlanmadı/yazılmadı. Asset0.1.7 Ctrl+F5.
12 ilerleme modeli +67 Chrome kontrolü geçti (test-evidence/analysis-progress-*.txt), desktop4/8 screenshot görsel incelendi;390px taşma yok. İlk browser koşusunda56 kontrol sonrası reload sayımı ekranın async yüklenmesini beklemeden yapıldığı için başarısızdı; test beklemesi düzeltildi ve67 tamamlandı. İlk kayıt analysis-progress-first-run.txt. 0.1.6 yarım kalan modal hata/gerekçe odağı/Eleştiri kaydı/503 hata koruması da aynı67 içinde geçti; artık doğrulandı. JS syntax ve index PHP lint geçti; backend/MySQL/restore/50validation tekrar çalıştırılmadı.
Aktif R02-B08 BEKLİYOR: mevcut4/8'de tüm8 kart görünür/5aktif;5çalıştır/kabul+gerekçe;6çalıştır/kabul+gerekçe;7çalıştır/kabul+gerekçe;refresh7/8 kararlar ve8aktif. 8'i henüz çalıştırma, rapor/global kapanış yok. Kullanıcı kısa batch istiyor. Ayrıntılar MANUAL_TEST_RESULTS.md.

## Güncel devam — R02-B09
R02-B08 altı kontrol kullanıcı “hepsi tamam” bildirimiyle geçti. Screenshotlar kartlı görünümü,adım5/6 tamamlanan mock çıktıları,mevcut f2e4ae74 7/8 ve adım7Kabul edildi/8aktif gösteriyor.5/6 karar ayrıntısı ve refresh kullanıcı beyanı; screenshotlarda o kararlar öncesi Bekliyor var. Kod değişmedi/test suite yeniden koşulmadı. Aktif R02-B09 altı kontrol BEKLİYOR:8çalıştır→8/8;Yeni rapor snapshotı oluştur yalnızbirkez→İncelemede;doğru öğrenci/vaka/sürüm,güven8/10/fark2 ve sentetik sınırlamalar;Wordindir/aç;PDFindir/aç;Raporu onayla→refreshOnaylı/Yayımlanmadı. Ayrıca öğrenciye yayımla/globalstudyclose bu batch'te yapılmayacak. Word uygulaması yoksa açılışkontrolünü geçti sayma. Mevcut kullanıcı sentetik verisini sıfırlama; aynıüçüncüvaka/analizden devam. MANUAL_TEST_RESULTS.md güncellendi.

## Güncel devam — R02-B10
R02-B09 altı kontrol kullanıcı “hepsi tamam” bildirimiyle geçti. Screenshotlar8Tamamlandı,snapshotSürüm1,doğruöğrenci/vaka/profil/güven8→10/fark2,WorduygulamasındaDOCXaçılışı,ChromePDF5sayfa/Türkçe veOnaylı/Yayımlanmadı gösteriyor. Tüm belge sayfaları/refresh kullanıcı beyanı; görünmeyensayfalar görseltestedilmiș sayılmadı. Kod değişmedi/otomatiktestlertekrarlanmadı. AktifR02-B10 altı kontrolBEKLİYOR: araştırmacıVeriindirmeTEST-OGR-01kapsamı testdahilkapalı/kodlanmışaçıkExcelboşapplications;testdahilaçıkExcelçoklusayfalar/kodluPkimlik/metinlerçıkarılmış;eventsCSV;codedJSON;rawJSONkimlik/metin/UTC;öğrenciyeçıkış-girişüçüncüvakaSon teslim'de onaylıyayımlanmamışraporyokluğu. Yayın/globalstudyclose yapılmayacak. Sonraki muhtemelen karar alternatifleri/snapshotdeğişmezliği ve ensonayrısenaryodayayınkapısı. MANUAL_TEST_RESULTS.md güncellendi.

## R02-B10.1 sonucu ve devam
Kullanıcı Excelde tüm veri sayfalarının boş, yalnız dictionary dolu olduğunu bildirdi.9a8fe65a boş applications sayfasını,7cd480b6 dictionary alanlarını/Türkçe açıklamalarını doğruluyor. Testdahilkapalı ilk kontrol için beklenen davranış; B10.1 GEÇTİ. Diğer B10.2–6 henüz BEKLİYOR. Sonraki: aynı Veri indirme ekranında KapsamTEST-OGR-01,Sentetik test verisini dahil et açık,Kodlanmış çıktı açık;Excel yenidenindir/eski boşdosyayla karıştırma;applications P-kodlu satırlar ve messages çıkarılmışmetinler beklenir. Hâlâboşsa indirmeekranıseçimlerini gösteren screenshot iste. Yayın/globalkapanış yok. Kod değişmedi/otomatiktestkoşulmadı.

## R02-B10.2 sonucu — kodlanmış Excel
7f904cb4 applications3satır/P-17061f920b41/üçüncüsubmitted;2388d357 ve59590b55 events/type/UTC/payloadçıkarılmış;f607332d messages4satır/userassistant/metinçıkarılmış gösteriyor. B10.2 GEÇTİ. B10.3–6 BEKLİYOR; Excelde events görülmesi ayrı CSVindirme kanıtı değil. Kullanıcı heartbeat/type anlamını soruyor; EVENT_CATALOG.md veapp.js/config doğrulandı: heartbeat15000msperiyodikdurumgözlemi,okuma/düşünmekanıtıdeğil. section_open bölüm,focus pencereodağıdeğişimi,visibilitysayfagörünürlüğü,field_focusalanodağı,text_changedmetindüzenleme,profile_changedseçimdeğişimi,copy/paste. Kod değişmedi; sonrakiCSV,ikiJSON,öğrencigörünmezrapor adımları sürüyor.

## Güncel devam — 0.1.8 olay açıklamaları
Kullanıcı type kodlarının process mining kullanımı ve daha anlaşılır görünmesini istedi; sonra kalan testlere geçilecek. EventLabels.php16olay/7bölüm için Türkçe ad/grup/açıklama ve sözlük sağlar. Export events satırlarına type_tr/section_tr/event_category/event_description eklenir; özgünkodlar/UTC/satırlar/veritabanı değişmez. Dictionary tüm eşlemeleri ve app_id süreçörneği,istemci/sunucu zaman farkını açıklar. heartbeat teknik gözlem,okuma/düşünmeeylemi değil; teknik grup analizamacınagöre filtrelenebilir,otomatik olay silinmez. VeriindirmeUI açıklaması,asset0.1.8. Ctrl+F5 ve yeni dosyaindirme gerekir; eskiExcel değişmez. KullanıcıDB'sine yazılmadı.
PHP tests/export-labels.php30kontrol +tests/run.php69SQLite kontrolü geçti. ÜretilenXLSXPhpSpreadsheetile yeniden açıldı;CSV/JSONTürkçe eşlemeleri,kodlanmışmetinçıkarma,hamverikoruma,tüm16kodunmappingi veDBolaydeğişmezliği doğrulandı. Kanıt test-evidence/event-labels-export.txt veevent-labels-regression.txt. PHP lint/JSsyntax geçti. Chrome/MySQL/hosting/restore bu değişiklikte tekrarlanmadı. DATA_DICTIONARY/EVENT_CATALOG güncellendi.
ManuelB10.1 veB10.2geçti; yeniTürkçesütunlarınmanuelkontrolü veB10.3–6 BEKLİYOR. Sonraki kısa batch: Ctrl+F5 testdahilaçık kodlanmışExcel yenidenindir/events sağsondakiyeni4alan/dictionary;eventsCSV;codedJSON;rawJSON;öğrenciTEST-OGR-01üçüncüvakaSon teslim raporyok/saltokunur. Globalstudyclose/yayın yok.

## R02-B10 ekran kanıtları —0.1.8
Kullanıcı5screenshot paylaştı, ayrıca tümü geçti beyanı yok. a3dbbb12Excel events dörtTürkçe sütunu,heartbeatTeknikdurum veözgün kodları gösteriyor: yeniTürkçesunum GEÇTİ. f4381d73CSVExceldeyanlışayrılmış (başlıklartekalan, açıklamalı satırlarda bazımetinlerayrısütun):B10.3KISMİ;CSVindirildi/Türkçeokunuyorama doğru14sütuniçeaktarma bekliyor. Bu screenshot dosyayı bozuk ilan etmeye yetmez;yerelayırıcıetkisiolabilir, UTF-8/virgül/çifttırnak ile temizdosyadaniçeaktar.
1239afe7kodlanmışJSON P-kodluapplications veçıkarılmışevents payload/Türkçeaçıklamalar: B10.4KISMİ,messages.content çıkarılmış kontrolüscreenshotta görünmüyor.010d345bhamJSON login/testmetinleri/UTC: B10.5GEÇTİ. JSONgörüntüleritümkatılımcıkapsamıiçeriyor;yalnızTEST-OGR-01filtresibuJSONlargöstermez,APIhatasıçıkarımıyok. b1cda681öğrenciüçüncüvakaSon teslimsaltokunur/raporyok: B10.6GEÇTİ. Yayımlanmışrapor/CSVtümsütun/JSONmessages görünmüşsayılmadı.
Aktifsonikikontrol: CSV'yiyeniboşExceldeVeri>Metinden/CSVdenUTF-8virgülçifttırnak ileiçeaktar→14kolon(id…event_description);kodlanmışJSONda[kodlanmış çıktıda metin çıkarıldı]aranmalı. Sonuçbekleniyor. Kod değişmedi/testlertekrarkoşulmadı;globalstudyclose/yayın yok.

## Güncel devam — R02-B11
B10sonikikontrol screenshotlarlaGEÇTİ:eb776866CSVvirgül/çifttırnak,767d4009doğrusütunlar;ef90c246codedJSONmessagesmetniçıkarılmış. Batch10tamam. Koddeğişmedi/testlertekrarkoşulmadı. AktifB11altıkontrolBEKLİYOR:aynıf2e4ae74üçüncüvaka8/8;5DüzenleJSONdayalnızsummary='SENTETİK TEST: Araştırmacı tarafından düzenlenen dönüşüm açıklaması.'gerekçelikayıt;eskiAIçıktısı/kararlarveSürüm1değişmez;5Reddet6Kararverilemedigerekçeleri;yenirapor snapshotSürüm2İncelemede;partsstep5reject6uncertain/Sürüm1acceptkarşılaştır;5ve6Kabuletgeridönüş+gerekçe/refresh,Sürüm2değişmez. İki raporuonay/yayınyok,globalstudycloseyok,yenianalizyok. Sonrakiuygunbatchyayınöncesikapı,globalkapanıştestiniayrısenaryoyap. MANUAL_TEST_RESULTS.mdgüncellendi.

## R02-B11.1 açıklama — henüz düzenleme testi geçmedi
Kullanıcı1.adımdaKabulet seçtiğini belirtti.2b872866 screenshot5Dönüşümadımıiçin ikiacceptkararı,sonundaSENTETİK TEST:Düzenleme kontrolü gerekçesi veözgünAIbody gösteriyor. Sistemacceptiçin özgünAIçıktısını kullanır;editseçilmediği için düzenlenmişsummaryuygulanmaz,bukayıphatasıdeğil. B11.1düzenlemebeklenenisonuçHENÜZTAMAMLANMADI;diğerB11adımlarınıpasssayma. Kullanıcıekrandakibutonisimleriyleaçıkbatchtalimatıistiyor:5.Dönüşüm>Sonuçlar ve inceleme>Kabul / Düzenle / Reddet / Karar verilemedi düğmesi>dialogKararaçılırlistesiDüzenle>JSONsummarydeğeri>Karargerekçesi>Kararı ayrı kaydet. KabulvarsayılanınınbilerekDüzenleyedeğiştirilmesivurgulanmalı. Eskiacceptkararısilinmez,yenieditkararıeklenir;Sürüm1değişmez. Koddeğişmedi/testkoşulmadı;B11altıkontrolbekliyor.

## R02-B11 kanıt incelemesi — son kabul dönüşü eksik
5e4e9436 screenshot5Dönüşümde eskiacceptler+yeniedit/düzenlenensummary/gerekçe gösteriyor.5502d1ac Sürüm1Onaylı/Yayımlanmadı veSürüm2İncelemede. ÜçekmetinJSONolarakokundu:3fc7e273ve75bf6173raporgövdeleri normalizeJSONolaraktamamenaynı;ikisininstep5/6accept. b955c62fmetnindestep5reject,step6uncertainvar;buüçüncümetinDBSürüm2bodyilebirebireşit. B11.1–5GEÇTİ. SaltokunurDBkontrolü:5geçmişiaccept/accept/edit/reject,sonacceptyok;6accept/uncertain/accept/accept,sonkararkabul. DolayısıylaB11.6KISMİ,5DönüşümüKabuletgeridönüşüeksik;6ikiayrıacceptkaydıkorunmuş,verikaybıyok. Sürüm2hâlâreject/uncertain,son6kabulüeski raporudeğiştirmemiş. KullanıcıDB'sineyazılmadı/koddeğişmedi/testsuitekoşulmadı.
Sonraki:yalnız5.Dönüşüm>Sonuçlar ve inceleme>Kabul / Düzenle / Reddet / Karar verilemedi>KararKabulet>gerekçeSENTETİK TEST: Kabul durumuna dönüş.>Kararı ayrı kaydet;refresh5ve6Kabuledildi,raporSürüm2aynıkalmalı. Yenianaliz/snapshot/onay/yayın/globalcloseyok. BuadımkabulgelmedenB11tamamlandıdeme.

## R02-B11 tamamlandı ve aktif R02-B12
1301bc7c screenshot ve kullanıcımetni5Dönüşümün sonacceptkararını eaa079c4...,17:58:30UTC veKabul durumuna dönüş gerekçesiyle doğruluyor. SaltokunurDB:5accept,6accept;raporSürüm2parts5reject/6uncertainolarakkorunmuş. B11.6GEÇTİ veBatch11tamamlandı. Kod/testsuite değişmedi/koşulmadı;DByazılmadı.
AktifB12ikiayrısenaryoyuhafifbatchtebirleştirir:globalveritoplamaaçıkkenSürüm1yayındenemesiengeli;ikincihalençalışılanvakadayalnıznihaitaslakileChromeçevrimdışıkayıt. Üçüncüteslimedilmişvaka/rapor2değiştirilmeyecek.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B12.1 | AraştırmacıüçüncüvakaRaporSürüm1>Ayrıca öğrenciye yayımla | Araştırma genelinde veri toplama açık hatası;yayınolmaz | BEKLİYOR |
| R02-B12.2 | Refreshaynıraporvesistemdurumu | Onaylı/Yayımlanmadı;SistemveişlemgeçmişiGlobaldurumAçık | BEKLİYOR |
| R02-B12.3 | ÖğrencigirişiüçüncüvakaSon teslim | Başarısızyayındenemesinden sonra raporhâlâyok | BEKLİYOR |
| R02-B12.4 | İkinciSınıfta dijital araç kullanımıNihai değerlendirme;F12NetworkOffline;kararmetniyaz | Sunucuyakaydedildidenmez;çevrimdışıuyarısı | BEKLİYOR |
| R02-B12.5 | NetworkNo throttling/Online | Aynıtaslakkaydedilir;sunucukayıtonayı | BEKLİYOR |
| R02-B12.6 | F12kapat,refreshikinciNihai değerlendirme | Testkararı korunur;son teslimyapılmaz | BEKLİYOR |

Kullanılacakikinci-vakanihaitestmetni:SENTETİK TEST: Bağlantı kesintisinde yazılan karar. Offlineikenrefresh/çıkışyapma;yalnızOnlinekayıtonayısonrasırefresh. Gerçekgüçkesintisitestideğildir. SistemdeGlobalveritoplamayıkapatdüğmesinebasılmayacak.

## R02-B12.1 kanıtı ve talimat açıklaması
A4036a63 screenshotSürüm1Onaylı/Yayımlanmadı veAraştırma genelinde veri toplama açık hatası gösteriyor:B12.1GEÇTİ. B12.2refresh/sistemdurumu ayrıca doğrulanmış değil. Kullanıcı2.adımdansonrasınıanlamadı;öğrencihesabınageçiş/üçüncüraporyokluğu/ikincihenüzteslimedilmeyenvakadanNetworkOffline→metin→Online→kayıtonayı→refresh açıkça tekrarlanacak. B12.3–6bekliyor,henüzpassyok. Kod/testdeğişmedi.

## R02-B12 tamamlandı ve aktif R02-B13
Kullanıcı “hepsi tamam” bildirdi;B12altıkontrolGEÇTİ.79356063üçüncüvakaöğrencisaltokunur/raporyok;1abd92dbOfflineveBağlantı yok,sunucuya gönderilemedi,başarısızsave/events;82d24e11No throttling,sunucukayıtonayıveHTTP200save/events gösteriyor. Refreshmetinkalıcılığı/sistemAçık kullanıcıbeyanı. BuChromeçevrimdışısınamasıdır;gerçekelektrik/PCcrashkurtarmasısayılmaz. İkincivakaçalışmayadevam,nihaitestkararıeklendi,teslimedilmedi. Koddeğişmedi/testlerkoşulmadı.
AktifB13katılımcıyönetimiayrıTEST-YONETIM-01hesabıyla yapılacak. NormalChromearaştırmacı,gizlipencereöğrenci. HerformgerekçesiSENTETİK TEST: Katılımcı yönetimi. ParolayalnızTEST_DATA.mddekitestparolası;paylaşılmayacak.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B13.1 | Katılımcılar>+Katılımcı ekle,TEST-YONETIM-01/Sentetik Yönetim Testi/Etkin/gerekçe | Hesaplistedeactive | BEKLİYOR |
| R02-B13.2 | Vakalar ve atamalar>Atama yönet,ilkGrupçalışmasındaadaletvakasınıyenihesabaata | Öğrenciyegörünenatanmışaktifvaka | BEKLİYOR |
| R02-B13.3 | Gizlipencereyenihesapgirişi,vakabaşlat,başlangıçproblemtaslağıkaydet | Sunucukayıtonayı;kesinleştirmeyok | BEKLİYOR |
| R02-B13.4 | AraştırmacıDüzenle/Dondur/Arşivle>Hesap durumuDondurulmuş,kaydet;öğrenciçıkış/giriş | Girişengellenir | BEKLİYOR |
| R02-B13.5 | HesapEtkin/öğrencinoTEST-YONETIM-02/parolaboş;kaydet;yenilogingiriş | Atama/taslakaynı;parola/değişmezkimlikkorunur | BEKLİYOR |
| R02-B13.6 | HesapArşiv,kaydet/listedearşivlenmiş;tekrarEtkin,kaydet;öğrencigiriş | Geriaktifleştirmedetaslak/atamakorunur | BEKLİYOR |

Problemtaslağı:SENTETİK TEST: Hesap yönetiminde korunacak taslak. Yeniöğrencibaşlangıcıhenüzkesinleştirmeyecek;mevcutTEST-OGR-01verisine dokunma. Global kapanış/yayın yok.

## R02-B13 açıklama — donma henüz uygulanmamış
Kullanıcı4.adımdansonrasınıanlamadığınıbildirdi.52fbb8ddlistedeTEST-YONETIM-01active,255d541dtekatananaktifvakayıgösteriyor (screenshotvakabaşlamadanönce). SaltokunurDBgüncel:hesapactive,id a56e4d952346041cd7526dbfffb86ce3;test-case-1initialproblemtaslağıSENTETİK TEST: Hesap yönetiminde korunacak taslak.kaydedilmiş. Audittekparticipantoluşturma;dondurma/yenidenadlandırmahenüzyok. B13.1–3GEÇTİ;B13.4–6BEKLİYOR. Sonrakitalimat4tenbaşlayacak,normalpencerearaştırmacı/gizliöğrenciayrımıherişlemdebelirtilecek;yalnızTEST-YONETIM-01hedeflenecek,TEST-OGR-07zatenfrozensatırınadokunma. 4dondur/girişengeli→EtkinveTEST-YONETIM-02yenino/parolaboş→yenilogingiriş/taslak→Arşiv→Etkingeridönüş/taslak. KullanıcıDB'siyazılmadı/koddeğişmedi/testkoşulmadı.

## R02-B13 sonucu ve0.1.9sekme düzeltmesi
E3ee722agirişengeli,7fba4864TEST-YONETIM-02giriş/atananvaka,63619574taslakkorunması,c191d0acbaşkasekmeuyarısısaltokunur. SaltokunurDBauditaynıteknikid a56e4d952346041cd7526dbfffb86ce3içinactive→frozen→active+TEST-YONETIM-02→archived→activeveproblemtaslağıkorunmuş;B13.4–6GEÇTİ,katılımcıyönetimikontrolleritamam. AyrıUXregresyonu:güvenliçıkışsessionStoragetabkimliğinitemizliyoramalocalStorageeskiownerkilidini20saniyebırakıyordu;hızlıyenigirişbaşkasekmeuyarısıverebiliyor. İzolesentetikChrometestindeyenidenüretildi(logout-lease-before.txt4kontrolsonrasıFAIL). 0.1.9releaseLeaseyalnızsahibiolduğukilidihome/başarılılogouttatemizler;başkasekmekilidinitemizlemez. Kodpublic/assets/app.js,indexassets0.1.9.69Chrome kontrolüGEÇTİ(logout-lease-after.txt),gerçekikincisekmehalenreadonly. JSsyntax/indexPHPlintgeçti. Backend/MySQL/export/restoreyenidenkoşulmadı;mevcutDB'yeyazılmadı.
Yeni0.1.9manuelkontrolBEKLİYOR:diğeröğrencisekmelerinikapat,Ctrl+F5,TEST-YONETIM-02Grupçalışmasıvakasınıaç;gerekiyorsa20saniyesonrayenile,uyarıyok/düzenlenebilir. Problemtaslağınakısasentetikekyaz/sunucukayıtbekle;çıkışvehemenyenigiriş/açılıştasahtebaşkasekmeuyarısıyok/metinkorunmuş. Buretesthenüzpasssayılmayacak;sonrakibatchvaka/atama/sürümyönetimiolacak. Globalstudyclose/yayınyok.

## 0.1.9 manuel tekrar tamamlandı — R02-B14
Kullanıcı üçkontrol için “hepsi tamam” bildirdi.0.1.9sahteleaseuyarısınınkalkması,düzenlenebilirtaslak/sunucukaydı,hızlıçıkış-girişmetinkorunması kullanıcıbeyanıylaGEÇTİ;yeniscreenshotyok. B13veleasekullanıcıtekrarıtamam. Koddeğişmedi/testsuitekoşulmadı.
AktifB14vaka/atama/sürümyönetimiBEKLİYOR. AyrıSENTETİK TEST · Vaka sürümü adlıDenemevakasıoluşturulacak;mevcutvakalardüzenlenmeyecek. Normalpencerearaştırmacı,gizlipencereöğrenci.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B14.1 | Vakalar ve atamalar>+Vaka ekle;Taslak,Denemevakası;İLK METİN | YeniTaslak;Öğrenciönizlemesiaynımetin | BEKLİYOR |
| R02-B14.2 | Yeni kartDüzenle/Yeni sürüm>DurumAktif;öğrenciTEST-YONETIM-02giriş/refresh | Atanmadığıiçingörünmez | BEKLİYOR |
| R02-B14.3 | Atama yönet TEST-YONETIM-02yenivakaata;öğrenciBaşla | İlkmetinvesürümnumarasıyüklenir;başlatılır | BEKLİYOR |
| R02-B14.4 | YalnızyenitestvakasımetniGÜNCEL METİNileyenisürümkaydet | Yeniaktifvakasürümüoluşur | BEKLİYOR |
| R02-B14.5 | TEST-YONETIM-02mevcutçalışmayenile | İLK METİNveöncekisürümkorunur | BEKLİYOR |
| R02-B14.6 | TEST-OGR-02yenivakaata;öğrenciçıkış/girişBaşla | GÜNCEL METİNvedahayenisürüm | BEKLİYOR |

Başlık:SENTETİK TEST · Vaka sürümü;Sıra7;Vakametni:[İLK METİN] Öğrenciler grup görevi için farklı çalışma yöntemleri öneriyor.;Yönerge:Kararınızı ve gerekçenizi yazın.;RubrikTESTRUBRİĞİ;Denemevakasıişaretli;Devam eden erişimi de durdurişaretsiz;hergerekçeSENTETİK TEST: Vaka sürümü kontrolü. Güncelmetin:[GÜNCEL METİN] Öğrenciler grup görevinde görev paylaşımı konusunda anlaşamıyor. Aktifyaparken de sürümartacağından İLK METİN/GÜNCEL METİN işaretleri gerçekv1/v2numarasıdeğildir;ekrandakigerçeksürümnumaralarınıkarşılaştır. Yeniöğrenciyanıtıkesinleştirme/teslimyok,globalclose/yayınyok.

## R02-B14 ekran kanıtı ve aktifB15
6aa6b2c6taslakv1önizlemesi;7028becfTEST-YONETIM-02sürüm2İLK METİN;563c4241aynıöğrenci/sürüm2ilkmetin;89f8a191TEST-OGR-02sürüm3GÜNCEL METİN. SaltokunurDBvaka2311909712f51cb7c2702554b488906dactive/practice1;ikibaşlangıçsürümü2/3ayrımetinlerde. B14.1,3,4,5,6GEÇTİ.B14.2atamaöncesigörünmezlikscreenshot/beyanyok,BEKLİYOR. SonrakiB15.1aynısenaryoyuhenüzatanmamışTEST-OGR-03iletekrarlayıpkapatacak. Koddeğişmedi/DB'yeyazılmadı/testsuitekoşulmadı.

| Batch / adım | Kontrol | Beklenen | Durum |
|---|---|---|---|
| R02-B15.1 | TEST-OGR-03Vakalarım'dayenitestvakasıyok;sonraaraştırmacıata,öğrenciBaşlabasma | Atamasızgörünmezlikveaçılmamışatamahazırlığı | BEKLİYOR |
| R02-B15.2 | YalnızyenitestvakasıDurumYeni başlangıçlara kapalı,stopkapalı;kaydet;TEST-OGR-03refresh | Henüzbaşlamadığıiçinvakagörünmez | BEKLİYOR |
| R02-B15.3 | TEST-YONETIM-02giriş/mevcutyenivakaDevam et | Başlanmışçalışmaerişilebilir;İLK METİNsürüm2 | BEKLİYOR |
| R02-B15.4 | YalnızyenitestvakasıDevam eden erişimi de durdurişaretli,kaydet;öğrencirefresh | Başlanmışvaka daVakalarım'dagörünmez/erişimkapalı | BEKLİYOR |
| R02-B15.5 | DurumAktif,stopişaretsiz,kaydet;TEST-YONETIM-02refresh | Vakaeskiçalışma/sürüm2ilegerigelir | BEKLİYOR |
| R02-B15.6 | TEST-YONETIM-02yenitestvakasıatamasınıkaldır>öğrencirefreshyok;yenidenata>refreshDevamet | Atamaerişimdenetimi;eskiuygulama/sürüm2korunur | BEKLİYOR |

HerişlemgerekçesiSENTETİK TEST: Vaka erişimi kontrolü.YalnızSENTETİK TEST · Vaka sürümüdenemevakası;orijinalaraştırmavakalarınıdeğiştirme. TEST-OGR-03bu batchvakayıbaşlatmayacak. Kapanışglobaldeğilvakadüzeyinde;globalstudyclose/yayın/teslimyok.

## Son kullanıcı yönlendirmesi — GitHub teslimi
Kullanıcı testlerin uzaması nedeniyle yorulduğunu bildirdi; yeni manuel batch istemeden yalnız gerekli kontrolleri önceliklendir. R02-B15 çalıştırılmadı ve BEKLİYOR; B14.2 de yeni kanıt bekliyor. Üretim/hosting/gerçekAI/kalanmanuelkontroller geçti sayılmayacak. Son istek son sürümü https://github.com/hkayaduman/VakaLab deposuna push etmek. Yerel Git geçmişi yok ve remote HEAD/main/master ref sorgusu boş döndü; ilkmaincommiti hazırlanıyor. config/local.php,storageDBleri,vendor,.tools,tests/tmp,.log dışarıda;TEST_DATA.mddekiörnekparolayalnızaçıkçasentetikseedörneği. Yeni kod değişikliği yok;songeçerli sonuçlar0.1.9Chrome69,0.1.8export30veSQLite69. Push sonucu ayrıca kaydedilecek.

GitHub teslim kontrolü: otomatik onay denetimi TEST_DATA.mdde kullanılan sabit test parolası nedeniyle ilk commit/push isteğini çalıştırmadan reddetti. Güvenli düzeltme: belgedeki parola yer tutucuya çevrildi; yerel hesaplar/config/DB değişmedi. Commit/push yeniden doğrulama bekliyor.

## GitHub teslimi tamamlandı — 2026-10-20
VakaLab0.1.9 kaynakları,belgeleri ve sentetik test kanıtları https://github.com/hkayaduman/VakaLab main dalına gönderildi. İlk kaynak commit4df25e0. Sabit test parolası commitöncesinde yer tutucuyla değiştirildi;hiçbircommitparolayıiçermiyor. Yerelconfig/DB/vendor/.tools/tests/tmp dışarıda. Sonrakiiletideyeni manuelbatchotomatikbaşlatma;kullanıcıyorulduğunuveotomasyontercihiniiletmişti. KalanB14.2/B15vegerçekhosting/AItestleribekliyor;üretimhazırilanedilmedi. Bu teslimdeuygulamakodudeğişmedi,eskigerçektestsonuçlarıkorundu,yenitestkoşulmadı.

## SON DEVAM — 2026-10-20 · 0.1.9 otomatik erişim ve yayın doğrulaması

- Kullanıcının yorulma/otomasyon tercihi uygulandı; yeni manuel batch istenmedi.
- tests/access_publication_checks.py eklendi: B14.2/B15 karşılıkları 46, ayrı global kapanış/yayın 34; toplam **80 HTTP kontrolü geçti**, SQLite/mock. Kanıt test-evidence/access-publication-0.1.9.txt. Kullanıcı bildirimi veya yeni ekran kanıtı değildir; tarihsel manuel BEKLİYOR satırları korunur, tekrar istenmez.
- Sadece benzersiz tests/tmp fixture oluşturuldu/seed edildi/kapatıldı/yayımlandı; config/local.php, mevcut çalışma DB'si ve 8088 kullanılmadı. Kullanıcının teslimleri, devam eden ikinci vaka, analiz/rapor sürümleri ve TEST-YONETIM-02 korunur. Ürün kodu/şema/sürüm değişmedi.
- RELEASE_READINESS.md: üçlü durum özeti, gerçek hosting/AI/kurum için gereken bilgiler ve gerçek düğme adlarıyla ayrı kapanış/yayın senaryosu. Test/manuel/plan kayıtları güncellendi.
- Çalıştırılmayanlar: eski Chrome/MariaDB/42HTTP/export/genelSQLite/restore suite'leri, gerçek hosting/AI ve fiziksel cihaz/kesinti. Önceki sonuçlar yeniden koşulmuş sayılmayacak.
- Sonraki somut adım: hosting ve AI yapılandırma bilgileri/erişimi, gerçek kurum rubriği/onam sağlanınca ayrı test ortamında doğrula. Şu anda yeni manuel batch veya gereksiz özellik yok; üretime hazır ilan edilmez. Bu oturumun test/belge değişiklikleri henüz commit/push edilmedi.

## SON DEVAM — 0.2.0 araştırmacı sadeleştirmesi · 2026-10-20

- Kullanıcının son kararı her araştırmacı ayrı çalışma alanı; kısa süre gündeme gelen ortak vaka erişimi iptal edildi, koda/şemaya eklenmedi.
- Kod: Migration/Scope, 002.sql, sade researcher.js; Admin/App/Analysis/Export/api yetki ve vaka kapıları; bin/upgrade-sqlite.php, CLI/Seed ve hedefli testler. Ürün asset sürümü 0.2.0.
- Yeni araştırmacı eklenir ve boş alanla başlar; sahiplik tüm alt uçlarda denetlenir. Basit kullanıcı formundan onam/deneyim/araç/çekilme kalktı, eski metadata korunur. Rutin işlemlerde gerekçe yok; inceleme/değerlendirme ve teknik müdahale gerekçeli kalır.
- Toplu checkbox atama ve kişisel ek süre; vakayı aç/kapat, raporu yalnız vaka/kişisel süre kapalıyken ayrı yayınla. Eski global kapı geçersiz. Teslim/başlangıç kilitleri korunur. Etiket/rubrik Sil geçmiş başvuruları yok etmez.
- Tam export tek/tüm vakalar için kapsamlı Excel/JSON; kodlanmış Disco CSV. Kimlikli/metinli tam paket sadece yetkili araştırmacıya. Disco'da gerçek import henüz yok.
- Gerçek sonuçlar: 119 HTTP, 42 iki-worker HTTP, 69 eski Chrome +24 yeni UI, 69 SQLite, 69 MariaDB11.4.12, 8 tablo ayrı restore sayımı, 35 export. Kanıt test-evidence/researcher-0.2.0-*. İlk test-harness hataları TEST_RESULTS'de açıklandı.
- Mevcut yerel DB özel yedeklendi; migration 002 sonrası 22 tablonun eski sütun/satır hashleri eşit, tekrar migration değişmedi. Yeni sahiplik mevcut ilk araştırmacıya verildi. TEST-OGR-01 teslimleri/ikinci vaka taslağı/analiz f2e4.../rapor1ve2/TEST-YONETIM-02 aynı. Mevcut veride seed/reset/kapanış/yayın yok.
- Şartnameler, kurulum, mimari, veri sözlüğü, plan ve sonuçlar güncel. Yeni manuel batch istenmedi. Gerçek hosting/AI/rubrik/onam/cihaz/kesinti kabulü açık; üretime hazır beyanı yok. Commit/push yapılmadı; önceki oturumun 0.1.9 test/belge değişiklikleri de çalışma ağacında korunuyor.

## SON DEVAM — 0.2.1 · 2026-10-20

- Kullanıcı ek süre testini tekrar istedi; ayrı fixture'da yeniden yapıldı. 50 hedefli Chrome kontrolünde seçili TEST-OGR-05 açılıyor, seçilmeyen TEST-OGR-04 kapalı kalıyor, ek süre kaldırılınca05tekrar kapalı. 119 HTTP'de yeni başlangıca ek süre, diğer vaka bağımsızlığı, teslim kilidi, rapor gizleme/geri gelme de geçti. Kullanıcıdan manuel test istenmedi.
- Vaka kartına Atanan öğrencileri göster (sayılı) eklendi. Yalnız aktif atamalar; öğrenci adı/hesap/çalışma ve ek süre durumu. Bu vakanın atamalarını yönet doğru vakayı önseçer.
- Genel durumdaki geniş matris kaldırıldı: Vaka ve öğrenci seç → vaka dropdown, Öğrenci ara, Çalışma durumu, öğrenci satırları/Çalışmayı aç. Başlamamış çalışmayı açmaz; kaldırılan atamaların geçmiş uygulaması görünür. Geri dönüşte vaka/arama/durum korunur. Eski filterMatrix kodu kaldırıldı.
- Rubrik adı/yönerge, ekle-kaldır boyutlar, min/max/ağırlık ve ekle-kaldır düzey adı/puan/açıklama formu var. JSON kullanıcıdan istenmez. Eski sürüm ve boyut kimlikleri korunur. Client/server doğrulaması ve alan odaklı hata; tanım görüntüsü okunaklı.
- İşlem geçmişi Türkçe işlem/hesap/vaka/öğrenci/zaman/gerekçe satırları, arama,20şerDaha fazla göster ve son100yetkili kaydın JSON indirmesi. Ham JSON ekranda yok.
- Gerçek yeni sonuç: 119 HTTP,69Chrome,50hedefliUI,69SQLite,14rubrik; test-evidence/researcher-0.2.1-*. İlk UI testinde test değişkeni Node process adını gölgeledi; düzeltildi ve50tamamlandı,ilk başarısız çıktı korunur. Genel durum/rubrik/işlem geçmişi sentetik ekranları görsel incelendi.
- Ürün asset0.2.1. Şema/çalışma DB'si değişmedi, seed/reset/kapanış/yayın yapılmadı. Gerçek rubrik/onam/hosting/AI ve kalan dış kabul hâlâ bekliyor. Commit/push yok. Yeni manuel batch isteme.

## SON DEVAM — 0.2.1 kapsam ve bağlam denetimi

- Kullanıcının tüm testler/context isteği üzerine CONTEXT/plan/TEST_RESULTS/MANUAL_TEST_RESULTS/BUGS/RELEASE_READINESS,iki manuel rehber ve şartname bağlantıları karşılaştırıldı. Güncel kararlarda kalan matris ifadesi ve eski manuel ekran haritası düzeltildi; önceki test kanıtları silinmedi.
- Çalıştırılabilir12testgrubunun tamamı bu tur güncel0.2.1'de geçti: time10,labels8,analysis-progress12,rubric14,export35,SQLite69,validation50,browser69,researcherUI50,access119,iki-workerHTTP42,MariaDB69;ayrırestore8tablosayısıeşit. Kanıtlar audit-0.2.1-*;547bildirilen kontrol örtüşür ve bağımsız547senaryo değildir.
- Ürün kodu/şema/mevcut DB değişmedi. bin/upgrade-sqlite.php tekrar çalıştırılmadı; önceki22tabloyadokunmayan migration kanıtı0.2.0tarihçesidir. Mevcut DB'de seed/reset/kapanış/yayın yok.
- Otomatik suite tamlığı gerçekAI/hosting/kurumrubriği/onam/fizikselcihaz/Wordgörselaçılış/gerçekkesinti/stres/Discoimport kabulü değildir. Bunlar açıktır; kullanıcıdan yeni manuel batch istenmedi. Commit/push henüz yok.

## 0.2.2 — Katılım bilgilerim kaldırıldı

Öğrenci ana sayfasındaki düğme, olay bağlantısı ve kullanılmayan onboarding istemci fonksiyonu kaldırıldı. Önceden kaydedilmiş veriler, araştırmacının isteğe bağlı Program/Sınıf alanları ve backend değişmedi.69Chrome/JSsyntax/PHPlint geçti; şema/mevcut DBdeyazma yok. Yeni manuel test istenmedi. Test sonucu TEST_RESULTS.md; bu tur eski12grup yeniden koşulmadı.

## Rapor öğrenci gösterimi — 2026-10-20 · 0.2.2

Kullanıcı bir öğrenci raporunu görmek istedi. Mevcut8088veri toplama/yayın durumuna dokunmadan tests/tmp altında ayrı SQLite kopyası ve ayrı PHP session dizini oluşturuldu; önizleme **http://localhost:8089/**. localhost kullanımı8088deki127.0.0.1oturum çerezinden ayrıdır. TEST-OGR-01 mevcut parolasıyla giriş yapabilir; hiçbir parola değiştirilmedi veya Git'e yazılmadı.

Yalnız kopyada test-case-3 Akranlar arası anlaşmazlık erişimi/ataması görünür yapıldı, veri toplama kapatıldı ve onaylı Rapor Sürüm1ayrıca yayımlandı. Sürüm2yayımlanmadı;rapor gövdeleri değişmedi. Asıl vaka ve rapor alanları kurulumda karşılaştırılarak değişmez doğrulandı. İlk kopya denemesinde devralınmış vaka erişimi kapalıydı; yalnız yeni gösterim kopyasında erişim açılarak hazırlandı.

Öğrenci yolu: TEST-OGR-01 → Vakalarım → Akranlar arası anlaşmazlık → Görüntüle →7.Son teslim → Yayımlanan raporlar →Sürüm1Word/PDF.6ayrıSmoke geçti: sentetik öğrenci oturumunda doğru vaka, yalnızv1ikiindirme,teslimkilidi,içekçıkarma,gerçekPDFveDOCXHTTP200. Kanıt test-evidence/student-report-demo.txt;sentetik görüntü tests/tmp/student-report-demo.png. Doğrulamada yalnız kopyaya ait CLIfixture oturumu kullanıldı; kullanıcının parolasıyla yeni login testi yapılmadı. Sunucu geçicidir; PCyenidenbaşlarsa tekrar hazırlanır. Gerçekhosting/AIkurumkabulü değildir. Yeni manuel batch yok.

## 0.2.3 — Sade öğrenci raporu (yerel geliştirme)

Öğrenciye başlangıç/nihai özgün yanıtlar, her ölçütte puan/düzey/kısa geri bildirim, nihai toplam ve genel geri bildirim sunulur. Toplam = nihai ölçüt puanı × ağırlık toplamı; başlangıç ayrı karşılaştırmadır. Reddedilen/belirsiz puan null kalır. Üretim geri bildirimi onaylı puan/rubrik ve özgün yanıtlardan hazırlanır; puanları uygulama hesaplar. Sağlayıcı çağrısı transaction dışında, karar değişikliği kontrolüyle yeni değişmez rapora eklenir. Eksik/geçersiz/teknik üretim ifadeli geri bildirim kaydedilmez. Araştırmacı Öğrenci raporunu önizle ile inceleyip ayrı onay/yayın yapar. Öğrenci raporunda AI/teknik inceleme/iç notlar yok; iç üretim kaydı araştırmacı snapshotında korunur. Öğrenci API/Word/PDF aynı sade içeriktir. Yeni Raporu görüntüle düğmesi var. Eski gövdeler değiştirilmez, eski sürümde yeni feedback yoksa açıkça henüz hazırlanmadı gösterilir; yeni rapor sürümü oluşturulur.

Ayrı localhost:8089 gösterim kopyasında TEST-OGR-01 / Akranlar arası anlaşmazlık için Sürüm3 oluşturulup onay/yayın yapıldı. Eski kopya rapor gövdeleri ve asıl cases/reports/applications/snapshots/decisions hashleri eşit. Asıl8088veri toplama/yayın/yanıtlar değişmedi. Testler yalnız sentetik mock; gerçek sağlayıcı geri bildirim kalitesi henüz doğrulanmadı. Word görsel renderer LibreOffice bulunamadığından tamamlanmadı; paket/metin ve PDF görsel kontrolü ayrıdır. Son test dökümü TEST_RESULTS.md: 15birim/75SQLite/81Chrome/119HTTP/35export/75MariaDB ve8restoretablosayısı, ayrıdemo6smoke; hepsi sentetik. Commit/push yok.

## 0.2.4 — Okunabilir öğrenci-vaka dosyası (yerelde doğrulandı)

Ham veriler ve süreç yerine Öğrenci yanıtları ve çalışma süreci: alan etiketli yanıtlar/kesinleşmiş kayıtlar, Önce/Sonra yanıt geçmişi, Türkçe işlem çizelgesi (bölüm/arama/periyodik kayıt filtresi ve50şer), görünür-odak süre tablosu ve bilinmeyen aralıklar, AI istek sonucu/süre/denemeler/gönderilen bağlam açıklaması. Analiz sonuçları ve karar geçmişi de okunabilir; puan/özet düzenleme alanlarla, JSON girişi yok. Ham JSON ve Disco indirmeleri ayrı düğmelerdir. Yeni AI ile mesajları etiketle bağımsız öneri çağrısıdır; aynı mesaj/tanımda cache, sahiplik/rol/çıktı/konuşma değişikliği denetimi var. Eski etiketler ve araştırmacı boş dahil kararları silinmez. Analiz6etiketleme de aynı kayıt/validasyon yoluna bağlandı. Disco'ya mesaj başına sunucu zamanlı nokta olayı; AI Labels/Researcher Labels/Effective Labels/Label Source/Label Status eklendi. Araştırmacı seçimi, boş seçimi de kapsayarak önceliklidir. Gerçek veride seed/reset/şema/veri toplama/yayın değişikliği yok. Testler ayrı sentetik DB; sonuçlar TEST_RESULTS.md içinde: 97 Chrome, 28 etiket/Disco PHP, 11 süreç JS, 11 etiket JS, 75 SQLite, 35 export, 119 HTTP, 75 MariaDB ve 8 restore tablo sayısı. Gerçek sağlayıcı/Discoimport/hosting/kurum kabulü bekliyor.

## Process mining hedefi denetimi — 0.2.4

Kullanıcı bölüm dönüşleri, AI soru amaçları, ziyaret süreleri ve darboğaz/sequence analysis hedefini ayrıntılandırdı. Mevcut CSV temel geçiş ve mesaj verisini taşır; ancak visits süreleri Disco'ya aktarılmıyor, mesaj amaçları Activity yerine etiket özelliğinde, bazı alan/oturum/sekme ayrıntıları kodlanmış CSV'de yok. Önceki Disco hazır ifadesi ayrıntılı süre/darboğaz analizi için yeterli doğrulama sayılmamalı. PROCESS_MINING_REVIEW.md mevcut/eksik/sonraki kapsamı kaydeder. Bu tur yalnız kod + resmî Disco belgesi incelemesi ve ayrı bellekte SQLite/mock tanı çalıştırması yapıldı (test-evidence/process-mining-review-0.2.4.txt); gerçek Disco import ve yeni ürün geliştirmesi yok. Mevcut DB/kod/raporlar değişmedi; eski testler tekrar geçmiş gibi yazılmadı. Sonraki somut ihtiyaç: davranış kodları, ayrı ziyaret/süre çıktısı ve açık sequence hazırlama kuralları; kullanıcının örnek yoluyla doğrulama. Mevcut arayüz korunacak.

## 0.2.5 — Sekme giriş/çıkış süreleri ve teslimde durma (yerelde doğrulandı)

Kullanıcı süreyi açıkça sekmeye girişten sonraki sekmeye geçişe kadar geçen zaman olarak tanımladı; tüm 7 sekme için aynı kural, her geri dönüş ayrı ziyaret. Son teslimin başarıyla kaydından sonra izleme gerekmiyor; yeni öğrenci telemetrisi istemci/sunucuda bu sınırla durduruluyor. Gecikmiş teslim-öncesi kayıtlar korunuyor, ilk istemci teslim doğrulaması kabul ediliyor. Önceki görünür/odak ölçümü ayrı kalıyor. Navigation sınıfı ham kayıtlardan ziyaret/başlangıç/bitiş/süre/sonuç üretir; açık uçlar tahmin edilmez. Varsayılan Disco geçiş/süre görünümü; ayrıntılı AI/olay görünümü ayrıca korunur. Excel/JSON'a navigation_visits ve navigation_milestones eklendi. Ürün asset 0.2.5; şema/çalışma DB'si değişmedi. Güncel sonuç: 30 Navigation, 103 Chrome, 50 validation, 119 erişim HTTP, 42 iki-worker HTTP, 75 SQLite, 75 MariaDB + 8 restore tablo sayısı, 38 export, 28 etiket kontrolü geçti; kanıt TEST_RESULTS.md ve navigation-0.2.5-* dosyalarında. Eski tüm-suite sonuçları bu tur tekrar koşulmadı. Gerçek Disco import/hosting/AI/kurum/fiziksel cihaz kabulü hâlâ açık. Commit/push yok.

Önemli kullanıcı ölçüm kararı: Son teslim başarıyla kaydedilince araştırma süreci tamamlanır; sonradan sekmelerde/raporda dolaşma süreye ve davranış akışına katılmaz. Ölçülemeyen kapanışlar tahmin edilmez.

## GitHub teslimi — 2026-10-21

Kullanıcı tüm biriken düzenlemelerin GitHub'a gönderilmesini istedi. Hedef origin/main (hkayaduman/VakaLab), başlangıç commit'i 194f9d0f5b4be549c1921b027a46204ed6fc7960; uzak dal bu commit ile aynı doğrulandı. 0.2.10 kod/belge/test kanıtları 5f8a34964654bd3cbc94dc469447328ee7aab243 commit'iyle oluşturuldu ve origin/main dalına başarıyla gönderildi. 198 aday dosyada sır örüntüsü ve yapılandırılmış anahtar eşleşmesi bulunmadı; config/local.php, storage DB, vendor, .tools ve tests/tmp dışarıda. 41 PHP/JS dosyasında sözdizimi geçti; mevcut son testler yeniden yapılmış gibi yazılmadı. Gerçek ortam kabulü ayrı kalır. İlk push başarılı: 194f9d0 → 5f8a34964654bd3cbc94dc469447328ee7aab243. Bu teslim sonucu ayrı belge commit'iyle kayda geçiriliyor; üretime dağıtım yapılmadı.

## PHP 8.4.27 uyumluluk doğrulaması · 2026-10-23

Hostingini PHP 8.4.27'ye aldığını belirten kullanıcının uyumluluk sorusu üzerine resmî Windows NTS VS17 x64 paketi ayrı `.tools/php-8.4.27-test` klasöründe test edildi. SHA256 `93bd8a815a52156606fb906d0573437908c61f96e2cc52c03a51e36a41bf5236` doğrulandı. Varsayılan yerel PHP 8.3.36 olarak kaldı.

Composer platform gereksinimleri ve CLI ortam kontrolü geçti. 81 SQLite genel (Word/PDF/XLSX dahil), 68 review-v2, 39 export, 42 gerçek HTTP/oturum/iki worker ve 81 MariaDB kontrolü: toplam 311 geçti. Ayrı restore DB'de 8 tablo sayısı eşleşti. Üç doğrudan PHP grubu E_ALL (32767) ile çalıştı; çıktılarda PHP Warning/Deprecated/Fatal görülmedi. Kanıtlar `test-evidence/php-8.4.27-compatibility/`. Ürün kodunda uyumluluk düzeltmesi gerekmedi. Çalışma verisine migration/seed/reset yok; canlı hosting/gerçek AI/fiziksel cihaz doğrulaması değildir.


## 23 Ekim 2026 — öğrenci / AI etiket grupları ve prompt v3

Etiket tanımları artık body.target_role=user veya assistant ile açıkça grupludur. Oluşturma/düzenleme ekranı, manuel seçim API/UI, AI JSON şeması ve sonuç doğrulaması aynı rol kuralını uygular. Eski grubu olmayan tanımlar tahminle atanmaz; yeni AI etiketlemesinden önce araştırmacı düzenlemelidir. Tarihsel kararlar korunur. Güncel tanımların tamamı her etiketleme isteğine eklenir; grup da önbellek anahtarının parçasıdır.

Sohbet chat-v3 ve etiketleme message-labels-v3 promptlarının tam metni ve birlikte odak testi adımları AI_PROMPTS_REVIEW_TR.md içinde. İlk prompt başarısızlıkları tarihsel bulgudur; son sürüm sonuçları test-evidence/label-groups-2026-10-23 altında ayrıca tutulur. Genel model doğruluğu iddiası yoktur. Kullanıcının mevcut çalışmasında değerlendirme veya yeniden etiketleme başlatılmadı.


## 23 Ekim 2026 — odak ölçümü adlandırması

Kullanıcıyla TEST-OGR-04 üzerinde yapılan denemede sayfa görünürken 3,440 sn odak kaybı gözlendi; ekran yakalama aracı kullanımıyla uyumlu. Bu gözlem hedef uygulamayı belirlemez. “Uygulama dışında” başlığı, süreç ekranı, olay adları ve yeni üretilen gezinme/dışa aktarım açıklamalarında “Vaka sekmesi etkin değil” olarak düzeltildi. Hesaplama, ham olaylar, outside/APP_OUTSIDE teknik kodları değişmedi. Süre 60, gezinme 82 ve süreç JavaScript 11 kontrolü geçti.

Mevcut yedi öğrenci etiketi için dahil etme/dışlama ölçütleri ve 12 örnekten oluşan STUDENT_LABELS_DRAFT_TR.md taslağı hazırlandı. Kullanıcının etiket tanımları veritabanında değiştirilmedi; taslak model çıktısı veya onaylı araştırma kodlama şeması değildir.


## 23 Ekim 2026 — kabul edilen etiketlerin yerelde etkinleştirilmesi

7 öğrenci etiketi v2, 9 AI yanıtı etiketi v1 olarak Admin::mutate/definition üzerinden kaydedildi. SQLite yedeği alındı; yanıt/mesaj/geçmiş etiket/değerlendirme tabloları değişmedi. Katalog label-definitions/2026-10-23.json içinde. Model testi güncel tanımları salt okunur aldı ve yalnız sentetik mesaj gönderdi.

message-labels-v4 promptu tekrarları yasaklar, eleştiri/açıklama sınırını ve çoklu davranışı netleştirir; önbellek sürümü yenilendi. Gerçek GPT-5 Nano karşılaştırmasında 32 mesajdan 27 tam küme eşleşmesi (öğrenci 13/16; AI 14/16). Beş uyuşmazlık giderilmiş sayılmaz. İlk 32 mesajlık çağrı başarısız; v3 gruplarında tekrarlı etiket ve anlam hataları vardı. Tüm denemeler test-evidence/label-catalog-2026-10-23 altında saklandı. Detay RESULTS_TR.md. 22 yerel sözleşme kontrolü geçti. Kullanıcının vakalarında etiketleme veya değerlendirme başlatılmadı.


## 23 Ekim 2026 — öğrenci etiketlemesi önceliği / message-labels-v6

Kullanıcının isteğiyle öğrenci etiketlemesi ana işlem, AI yanıtı etiketlemesi ayrı ve isteğe bağlı oldu. Öğrenci API hedefi user, AI bağlamı context_messages içindedir. Yalnız seçilen grubun güncel ad/açıklamaları iletilir. Öğrenci için etiket başına boolean şema ve katı decoder kullanılır; sonuç benzersiz etiket listesine çevrilir. Açıklama wire JSON içinde yinelenmez. Öğrenci çağrıları dört hedeflik parçalara ayrılır ve tamamı doğrulanmadan yazılmaz. Grup bazlı önbellek ayrıdır; araştırmacı seçimleri korunur. Eski karma analiz çağrıları all uyumluluğunu sürdürür.

Gerçek GPT-5 Nano: önceki öğrenci örnekleri 15/16, yeni örnekler 10/12; toplam25/28 tam etiket kümesi eşleşmesi. İki kapsam dışı finansal istek ve bir temel kavram tanımı hâlâ yanlış etiketleniyor. Öğrenci etiketlemesi tamamen doğrulanmış sayılmaz. Testler yalnız sentetik mesajlarla ve salt okunur güncel tanımlarla yapıldı; kullanıcının vakalarında yeniden etiketleme/değerlendirme yok. Rapor test-evidence/student-labels-2026-10-23/RESULTS_TR.md. Yeni20 yerel kontrol geçti.

Öğrenci önceliği son regresyon: Chrome134, öğrenci sözleşmesi20, etiket sözleşmesi22, süreç28, sohbet9, süreç JavaScript11, değerlendirme74 kontrolü geçti. Canlı modelin 3/28 uyuşmazlığı açık.


## 24 Ekim 2026 — öğrenci etiketleme hataları / message-labels-v11

Önceki üç hata son kontrollü koşuda giderildi. Gerçek GPT-5 Nano:44/44 regresyon + uygulama/prompt sabitken ilk kez denenen 8/8; toplam 52/52. Değiştirilmiş ad/açıklama ve açık konu dışı etiket kontrolü 4/4. Önceki denemeler korunur. Bu bir genel %100 doğruluk garantisi değildir. Tam rapor test-evidence/student-labels-2026-10-24/RESULTS_TR.md; kaynak/fixture sabitleme özetleri message-labels-v11/frozen-holdout-manifest.json.

Öğrenci hedefi kendi context_before alanındaki önceki konuşmayla kodlanır. Yeni topic_scope alanı araştırmacı kontrolündedir:pedagogical/outside/any. Kabul edilmiş7 öğrenci tanımı açıklamaları korunarak v3 pedagojik kapsamla kaydedildi; SQLite yedeği alındı, yanıt/geçmiş karar/değerlendirme tabloları değişmedi. Model message_scope döndürür; sunucu uygun olmayan kapsamı dışlar ve konu kararını audite yazar. Güncel Dahil/Dışla örnekleri tam açıklamanın yanında ayrı ipuçları olarak iletilir. Wire kimlikleri kısaM/L kodlarıdır; doğrulama sonrasında kalıcı kimliklere eşlenir. Önbellek v11 ile yenilendi. Hiçbir kullanıcı vakasında yeniden etiketleme veya puanlama başlatılmadı.

Kontroller: öğrenci33, genel etiket22, süreç28, değerlendirme74, ana81, dışa aktarım39, gezinme82, süre60; Chrome ana135 ve grup/kapsam11 geçti. Güncel tam prompt AI_PROMPTS_REVIEW_TR.md içinde.

## 24 Ekim 2026 gerçek model ile sentetik uçtan uca test

İzole test DB üzerinde sohbet → öğrenci etiketi → teslim → V2 değerlendirme → araştırmacı düzeltmesi simülasyonu → öğrenci raporu → PDF/DOCX/XLSX/Disco akışı tamamlandı. Çalışma DB açılmadı, mevcut kullanıcı verisi değişmedi. Yerel439 ve Chrome etiket grubu/kapsam11 kontrolü geçti. PDF son üç sayfa render/görsel ve altı yanıt/toplam metin kontrolü geçti; DOCX paket/metin geçti, LibreOffice bulunmadığından görsel kontrol tamamlanamadı.

AI-SCORE-04 AÇIK: GPT-5 Nano gerçek vaka ilişkisini içeren yanıtı yok sayıp sıfır verebiliyor. Yeni review-v2.1 promptu ve iki sabit karşılaştırma2/2 bu geniş akıştaki hatayı kapatmıyor. Sentetik rapor yalnız araştırmacı düzeltmesi simülasyonuyla tamamlandı, akademik uzman onayı değildir. Geçersiz JSON/kanıt çıktıları için şema, düz metin doğrulaması ve güvenli hata audit kaydı güçlendirildi. Puanlamada gerçek alıntı cümlesi seçimi ve gerekçe-önce üretimi var; insan inceleme kapısı sürer. PDF soru başlıkları yanıttan ayrılmayacak şekilde düzenlendi. Ayrıntı test-evidence/live-workflow-2026-10-24/RESULTS_TR.md. GitHub gönderimi yapılmadı. Sonraki iş: araştırmacının önceden puanladığı farklı düzeylerde örneklerle tekrarlı puanlama kalibrasyonu; öğrenci etiketleme önceliği korunur.

## 24 Ekim 2026 kısa ön değerlendirme promptu review-v2.2

Kullanıcı değerlendirmeyi araştırmacının inceleyeceği bir ön öneri olarak kullanacağını netleştirdi. Soru sistem promptu1579 karakterden808 karaktere indirildi (%49). Rubrik açıklamaları, sorunun kapsamı, gerçek kanıt, düzey-puan tutarlılığı, kısa geri bildirim ve araştırmacının son kararı korundu. Teknik şema/doğrulama kuralları, sohbet ve etiketleme promptları değişmedi. Önceden sabitlenmiş güçlü gerekçe ve gerekçesiz yanıtın gerçek GPT-5 Nano kontrolü2/2, yerel değerlendirme kontrolleri85/85 geçti. Kanıt: test-evidence/live-workflow-2026-10-24/scoring-20261024-182653. Bu küçük kontrol önceki anlamsal tutarsızlık kaydını kapatmaz; araştırmacı incelemesi mevcut iş akışının parçasıdır. Güncel metin AI_PROMPTS_REVIEW_TR.md son bölümünde.

## 24 Ekim 2026 nihai algılanan zorluk ölçümü

Kullanıcı isteğiyle nihai değerlendirmeye final_difficulty alanı eklendi. Yeni teslimlerde boş başlar ve0–10 tam sayı seçimi zorunludur;0 geçerli yanıttır. Kaydetme, teslim snapshotı, araştırma veri dışa aktarımı, araştırmacı puan tablosu ve başlangıç/nihai karşılaştırması alanı korur. V2 profil ön değerlendirmesine zorluk değişimi de iletilir. Örnek tarayıcı kontrolünde başlangıç6, nihai0, fark−6 birebir doğrulandı. Eski teslimlerde eksik nihai zorluk uydurulmaz; eski kayıtlar ve rapor snapshotları otomatik değiştirilmez. Şema migrasyonu gerekmez; veri mevcut JSON gövdelerinde tutulur. İstemci dosyalarının önbellek sürümleri0.2.26 olarak yenilendi.

Kontroller: ana87, V2 değerlendirme86, eski teslim uyumluluğu21, dışa aktarım39, süreç28; Chrome doğrulama50 ve tam tarayıcı137 geçti. JavaScript/PHP sözdizimleri ve diff kontrolü geçti. Testler izole sentetik veriler/mock sağlayıcıyla yapıldı; çalışma DB değiştirilmedi. Canlı modele çağrı veya GitHub gönderimi yapılmadı.

## 24 Ekim 2026 yerel analiz süre kesilmesi düzeltmesi

Kullanıcı ekranındaki Unexpected token '<' hatasında çalışan8088 PHP cli-server üzerinden max_execution_time=30, display_errors=1 ve log_errors=0 ölçüldü; sağlayıcı timeout90 saniye. Çok sorulu adım PHP sınırında kesilip HTML hata çıktısıyla JSON yanıtını bozuyor ve analysis_steps satırını running bırakıyordu. Gerçek HTTP sentetik testinde aynı hata yeniden üretildi.

ApiRuntime: sağlayıcı çağrısı öncesi PHP bütçesi timeout+15 saniye olarak yenilenir; cURL timeoutu korunur. API ekran hata çıktısını kapatıp günlüklemeyi açar. Fatal kapanışta yalnız bu isteğe bağlı çalışan analiz satırı failed olur ve güvenli JSON hata yanıtı döner. Başka/tamamlanmış sonuçlar değiştirilmez. İstemci JSON dışı hata çıktısında okunabilir Türkçe mesaj verir. Profil promptu kısa doğal dil ister, final_collected vb teknik alan adları ayrıca yorum doğrulamasında reddedilir. Soru promptu değişmedi; değerlendirme directive review-v2.3, app.js önbelleği0.2.27.

Çalışma SQLite için backup-before-analysis-timeout-20261024-184738-cec2bb0e28ae289be8f53f15999b396e.sqlite yedeği alındı. Gözlenen iki kesilmiş adım ea9d7b5969d04834cb1f03da9e51d0cd ve18205466b515f379b6cc725234db939b yalnız failed/request_interrupted durumuna alındı, audit kaydı eklendi. Öğrenci taslakları/revizyonları/teslim kilitleri, snapshotlar, mesajlar, insan kararları, raporlar ve tamamlanmış adımların hashleri aynı kaldı. AI değerlendirmesi otomatik yeniden başlatılmadı.

Kontroller: tests/api-runtime.cjs gerçek HTTP'de1 saniyelik sunucu sınırı, ardışık iki uzun beklemede bütçe yenileme, fatal/timeout JSON yanıtları, yalnız ilgili satırın kurtarılması ve tamamlanmış sonucun korunmasını doğruladı. Chrome138, ana87, V2 değerlendirme87, eski teslim21 kontrolü geçti. Tanı endpointi kaldırıldı. Testlerde gerçek sağlayıcı çağrısı yok. Kullanıcı Ctrl+F5 ardından Analize devam et ile ilerleyebilir. GitHub'a gönderim yapılmadı.

## 24 Ekim 2026 öğrenci raporu görünürlüğünü geri alma

Kullanıcı isteğiyle V2 rapor kartına ayrı Göstermeyi geri al düğmesi eklendi. Raporun durumu metinli ve renkli etiketle Öğrenciye açık/Öğrenciye kapalı gösterilir. Açık raporda göster düğmesi pasif, geri al düğmesi etkindir; geri alındığında ve yayın koşulları sağlandığında tersi olur. Yayın koşulları sağlanmıyorsa mevcut kapanış uyarısı ve engeli sürer. Pasif düğmeler nötr gri, etkin göster yeşil, etkin geri al kırmızı tonludur. Görünürlük eylemleri hazırlama/indirme düğmelerinden ayrı alandadır.

Yetkili araştırmacının report/hide işlemi yalnız published_at değerini temizler. Rapor onayı, içeriği ve kimliği korunur; işlem audit kaydına yazılır. Öğrenci rapor listesi ve indirme erişimi kapanır. Aynı rapor mevcut veri toplama ve güncel değerlendirme kapılarıyla yeniden gösterilebilir. Öğrenci bu işlemi yapamaz. Mevcut çalışma raporlarının görünürlüğü otomatik değiştirilmedi.

Kontroller: V2 değerlendirme94, ana87, eski teslim21, tam Chrome142 geçti. Testler göster→geri al→yeniden göster, düğmelerin karşılıklı etkinliği, durum etiketleri, öğrenci erişiminin kapanması, içerik/onay korunması, tekrarlı geri alma ve audit kaydını kapsar. Son masaüstü kapalı ve mobil açık ekran görüntüleri görsel incelendi. Cache sürümü app/review/CSS0.2.28. Gerçek sağlayıcı çağrısı veya GitHub gönderimi yok. Kullanıcı Ctrl+F5 ile yenileyebilir.

## 24 Ekim 2026 GitHub teslim hazırlığı

Kullanıcı tüm son değişikliklerin GitHub'a gönderilmesini ve OpenRouter anahtarının paylaşılmamasını açıkça istedi. origin/main ile yerel HEAD32fef8e eşitliği fetch ile doğrulandı. Gerçek yapılandırılmış anahtar çıktı verilmeden aday dosyalarda, main commit geçmişinde, Word/Excel arşiv içeriklerinde ve PDF metninde karşılaştırıldı; eşleşme ve geçerli token örüntüsü yok. config/local.php Git dışında; çalışma veritabanı, yedekler, .tools ve tests/tmp dışarıda. Sentetik ekran görüntüsü ayrıca incelendi. Yalnız HEAD:main gönderilecek; yerel Codex checkpoint refleri gönderilmeyecek. Son içerik taraması stage sonrası yinelenecek.
