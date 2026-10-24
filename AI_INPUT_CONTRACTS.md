# AI girdi sözleşmeleri · v1

23 Ekim 2026 son kullanıcı kararı: chat-v2 ve message-labels-v2 yerelde etkin; güncel tam metin AI_PROMPTS_REVIEW_TR.md. Sohbet API'sine tek uygulama sistem promptu, user-verisi olarak vaka ve yerel user/assistant rollerindeki tamamlanmış mesajlar gider. Mesajlardan system/developer/tool rolü yükseltilmez. Etiketler her istekte güncel ad, açık description ve ayrıştırılmış body ile okunur; model/prompt/tanım değişikliği cache'i geçersiz kılar, yanıt beklenirken tanım seti değişirse eski sonuç reddedilir. Audit kullanılan tanımları saklar; insan kararı önceliği korunur. Katı etiket JSON şeması eklendi. Gerçek GPT-5 Nano'nun yönlendirmeme ve güncellenen açıklamanın dışlama kapsamına uyma testleri tam geçmedi; açık davranış bulguları BUGS.md içinde. Aşağıdaki “yeni promptlar henüz etkin değil” notu önceki görüşme aşamasıdır.

23 Ekim 2026 güncel ek: Yerel OpenRouter/GPT-5 Nano sohbet/profil/tek soru smoke doğrulaması TEST_RESULTS.md içindedir; aşağıdaki eski “sağlayıcı bilinmiyor” kayıtları tarihçedir. Sohbetin mevcut chat-v1 sistem metni ve mesaj etiketleme sistem metni ile kullanıcının yönlendirmesiz serbest sohbet isteğine göre hazırlanan yeni TASLAKLAR AI_PROMPTS_REVIEW_TR.md içindedir. Yeni promptlar henüz etkinleştirilmedi. Etiketleme mevcut araştırmacının güncel aktif tanımlarını body açıklamalarıyla gönderir; bu testte yedi açıklama da “SENTETİK başlangıç tanımı” olduğundan anlamlı kodlama sınırları ayrıca netleştirilmeli. Kullanıcının mevcut tanımları değiştirilmedi ve vakasında değerlendirme başlatılmadı.

0.2.18 güncellemesi: V2 için tek güncel reports.id vardır; tekrar hazırlama ve tamamlanan insan incelemesi sonrası birleştirme aynı raporu günceller. Güncelleme önceki onay/yayını kaldırır; show işlemi vaka kapanış kapısıyla onay ve yayını birlikte yapar. Öğrenciye yalnız güncel rapor açılır. Araştırmacı geçmiş listeleri kaldırıldı; iç analiz/karar kayıtları silinmez. Öğrenci Word/XLSX çıktısı yalnız güncel puan ve feedbacktir. V1 tarihsel sözleşmesi korunur; şema değişmedi.

## Güncel ek sözleşme · review_version=2 · 0.2.17

Tarayıcının yeni analizleri audit.analysis_run detail.review_version=2 ile ayrılır; sürüm belirtilmeyen eski istemciler v1 olarak kalır. Profil karşılaştırması yalnız başlangıç/nihai ölçüm satırlarını alır, feedback döndürür; score/scores reddedilir. Toplanmamış nihai ölçümden değişim çıkarılmaz. Başlangıç problem/decision/reason, nihai final_decision/final_reason ve reflection her biri ayrı çağrıda yalnız kendi yanıtını sources içinde alır; vaka ve seçili rubrik bağlamı eşlik eder. Her soru rubrik boyutlarıyla doğrulanır, soru puanı ağırlıklı ortalamadır; feedback puan gerekçesi ve geliştirme önerisidir. Evreler arası genel toplam yoktur.

Sohbet adımı deterministiktir: mesaj sayıları ve kilitsiz AI bölümündeki bilinen görünür/odaklı ziyaret süresi; AI çağrısı, yorum, puan ve insan kararı yok. Dört insan kararı sonrası rapor birleştirilir. student-v2 üretimi ek AI çağrısı yapmaz; kabul/düzenlenen puanlar ve feedbackler aynen projekte edilir. İç kaynaklar öğrenciye gönderilmez. Aşağıdaki v1 kuralları yalnız tarihsel sözleşmeye aittir.

0.2.16: Başlangıç/nihai rubrik çıktısındaki her ölçütte score/level/source/quote/reason yanında öğrenciye yönelik feedback beklenir. Feedback metni kısa Türkçe,10–2500karakter ve teknik üretim dili içermeyen açıklamadır; eski feedback alanı bulunmayan sonuçlar korunur. Araştırmacının kabul ettiği/düzenlediği feedback varsa rapor üretiminde aynı metin kullanılır. Diğer AI adımları ayrı çağrı ve girdi allowlistlerini korur. Açık tekrar isteği yalnız seçili3–7adımında yeni attempt üretir; eski çıktı silinmez, yeni başarılı çıktı insan incelemesi olmadan onaylanmış sayılmaz.

0.2.15 kullanıcı kararı: analiz adımları tek başlatma sonrası sırayla tetiklenir; her AI adımı ayrı çağrıdır ve girdi ayrılığı korunur. İnsan kararları otomatik verilmez. Öğrenci raporunda AI yazdı / araştırmacı düzenledi kaynak etiketleri gösterilmez; yalnız puanlar ve feedback sunulur. Araştırmacı AI önerisini öğrenci yanıtıyla karşılaştırır, düzenler veya onaylar. Onaysız rapor feedback metni yeni sürüm olarak düzeltilebilir. İç kaynak/karar geçmişi korunur, öğrenciye verilmez. Serbest geri bildirim metninde teknik model/prompt/JSON dili kullanmama kuralı sürer. Toplamlar sistem hesabıdır; eski rapor snapshotları yeniden yazılmaz.

Sağlayıcı bilinmiyor. `mock` yalnız env=test; `compatible` HTTPS Chat Completions uyumlu sunucu adapter örneğidir. Gerçek sağlayıcı/model/anahtar doğrulanmadı. Model sessiz değişmez; model + directive her istek/adımda saklanır. Gizli anahtar payload veya browser'a eklenmez. Öğrenci/analiz metni güvenilmeyen veri olarak sistem yönergesinden ayrılır; JSON doğrulaması ve insan incelemesi yine zorunlu.

| İşlem | Allowlist |
|---|---|
| Öğrenci sohbeti | directive chat-v1; sabit vaka version/body/instructions; aynı uygulamanın tamamlanmış user/assistant mesajları |
| 1 Bütünlük | Uygulama hesaplar; AI çağrısı yok |
| 2 Profil | Uygulama güven farkını hesaplar; AI çağrısı yok |
| 3 Başlangıç rubriği | vaka; sabit rubrik sürümü; yalnız problem/decision/reason kaynak metni; beklenen JSON şeması |
| 4 Nihai rubriği | vaka; aynı rubrik; yalnız final_decision/final_reason kaynak metni; beklenen JSON şeması |
| 5 Dönüşüm | vaka; başlangıç ve nihai metin; kanıt şeması |
| 6 Etkileşim | vaka; mesajlar/ID/sıra/durum; kaynak metinler; draft revisionları; etiket tanımları; kanıt/etiket şeması |
| 7 Yansıtma | vaka; reflection ve mesaj kaynakları; kanıt şeması |
| 8 Birleştirme | Uygulama araştırmacı kararlarını birleştirir; yeni AI çağrısı yok |

Sohbete profil/başlangıç/nihai/yansıtma/rubrik/etiket/araştırmacı notu/diğer vaka **otomatik gönderilmez**. Öğrenci kendi metnini mesaja yazarsa o mesajda bulunur. 3 ve 4 çağrıları birbirlerinin metin/puanını ve güven puanlarını içermez. Adım 6'da süreç taslakları kapsam gereği bulunur; bağımsız rubrik çağrılarıyla karıştırılmaz.

Puan JSON: `scores[dimension_id] = {score, level, source, quote, reason}`. Boyutların sayısı/tamlığı, sayı aralığı, tanımlı düzey, boş olmayan ve kaynakta aynen bulunan quote kontrol edilir. `weighted_mean` sağlayıcının iddiasından alınmaz; PHP yeniden hesaplar. Diğer AI adımları summary + en az bir geçerli evidence gerektirir. Geçersiz/kanıtsız çıktı `failed`; onaylı puana dönüşmez. Öğrenci dashboard'u ve report_data endpointi iç analiz/payloadları döndürmez.

AI kilidi: kısa transaction ile öğrenci mesajı + ai_request + ai_attempt ve active_request yazılır. Session kilidi api.php'de bırakılır; sağlayıcı çağrısında DB kilidi yok. Aynı client_key ikinci mesaj üretmez. `failed` istekte açık kullanıcı isteğiyle aynı mesajın yeni attempt'i üretilebilir; eski deneme korunur. Ağ belirsizliği otomatik yeni çağrı değildir. Timeout sonrası araştırmacı gerekçeli kapatma yapabilir; attempt fence geç yanıtı yazdırmaz. Bu işlem sağlayıcıdaki isteği/maliyeti iptal etmez.

Etiketleme sohbet yolunda yok; araştırmacı tek uygulamada 6. adımı çalıştırır. AI önerileri message_labels source=ai; insan seçimi source=researcher ve yeni review. Etiket değişimi konuşma modeline geri gitmez.

Mock modları config.ai.mode: normal (deterministik), delay (6 s), partial (kısmi işaretli metin), error (başarısız), invalid_json (analiz yapısı geçersiz). Gerçek API bağlantı testi: ayrı test kurulumunda compatible/model/key ayarla; test vakasında bir mesaj gönder; uygulama dosyasında status/model/payload allowlistini ve hatada genel hata kodunu denetle. Anahtarı kanıta koyma. Bu test henüz yapılmadı.


## 23 Ekim 2026 — öğrenci / AI etiket grupları ve prompt v3

Etiket tanımları artık body.target_role=user veya assistant ile açıkça grupludur. Oluşturma/düzenleme ekranı, manuel seçim API/UI, AI JSON şeması ve sonuç doğrulaması aynı rol kuralını uygular. Eski grubu olmayan tanımlar tahminle atanmaz; yeni AI etiketlemesinden önce araştırmacı düzenlemelidir. Tarihsel kararlar korunur. Güncel tanımların tamamı her etiketleme isteğine eklenir; grup da önbellek anahtarının parçasıdır.

Sohbet chat-v3 ve etiketleme message-labels-v3 promptlarının tam metni ve birlikte odak testi adımları AI_PROMPTS_REVIEW_TR.md içinde. İlk prompt başarısızlıkları tarihsel bulgudur; son sürüm sonuçları test-evidence/label-groups-2026-10-23 altında ayrıca tutulur. Genel model doğruluğu iddiası yoktur. Kullanıcının mevcut çalışmasında değerlendirme veya yeniden etiketleme başlatılmadı.


## 23 Ekim 2026 — kabul edilen etiketlerin yerelde etkinleştirilmesi

7 öğrenci etiketi v2, 9 AI yanıtı etiketi v1 olarak Admin::mutate/definition üzerinden kaydedildi. SQLite yedeği alındı; yanıt/mesaj/geçmiş etiket/değerlendirme tabloları değişmedi. Katalog label-definitions/2026-10-23.json içinde. Model testi güncel tanımları salt okunur aldı ve yalnız sentetik mesaj gönderdi.

message-labels-v4 promptu tekrarları yasaklar, eleştiri/açıklama sınırını ve çoklu davranışı netleştirir; önbellek sürümü yenilendi. Gerçek GPT-5 Nano karşılaştırmasında 32 mesajdan 27 tam küme eşleşmesi (öğrenci 13/16; AI 14/16). Beş uyuşmazlık giderilmiş sayılmaz. İlk 32 mesajlık çağrı başarısız; v3 gruplarında tekrarlı etiket ve anlam hataları vardı. Tüm denemeler test-evidence/label-catalog-2026-10-23 altında saklandı. Detay RESULTS_TR.md. 22 yerel sözleşme kontrolü geçti. Kullanıcının vakalarında etiketleme veya değerlendirme başlatılmadı.


## 23 Ekim 2026 — öğrenci etiketlemesi önceliği / message-labels-v6

Kullanıcının isteğiyle öğrenci etiketlemesi ana işlem, AI yanıtı etiketlemesi ayrı ve isteğe bağlı oldu. Öğrenci API hedefi user, AI bağlamı context_messages içindedir. Yalnız seçilen grubun güncel ad/açıklamaları iletilir. Öğrenci için etiket başına boolean şema ve katı decoder kullanılır; sonuç benzersiz etiket listesine çevrilir. Açıklama wire JSON içinde yinelenmez. Öğrenci çağrıları dört hedeflik parçalara ayrılır ve tamamı doğrulanmadan yazılmaz. Grup bazlı önbellek ayrıdır; araştırmacı seçimleri korunur. Eski karma analiz çağrıları all uyumluluğunu sürdürür.

Gerçek GPT-5 Nano: önceki öğrenci örnekleri 15/16, yeni örnekler 10/12; toplam25/28 tam etiket kümesi eşleşmesi. İki kapsam dışı finansal istek ve bir temel kavram tanımı hâlâ yanlış etiketleniyor. Öğrenci etiketlemesi tamamen doğrulanmış sayılmaz. Testler yalnız sentetik mesajlarla ve salt okunur güncel tanımlarla yapıldı; kullanıcının vakalarında yeniden etiketleme/değerlendirme yok. Rapor test-evidence/student-labels-2026-10-23/RESULTS_TR.md. Yeni20 yerel kontrol geçti.


## 24 Ekim 2026 — öğrenci etiketleme hataları / message-labels-v11

Önceki üç hata son kontrollü koşuda giderildi. Gerçek GPT-5 Nano:44/44 regresyon + uygulama/prompt sabitken ilk kez denenen 8/8; toplam 52/52. Değiştirilmiş ad/açıklama ve açık konu dışı etiket kontrolü 4/4. Önceki denemeler korunur. Bu bir genel %100 doğruluk garantisi değildir. Tam rapor test-evidence/student-labels-2026-10-24/RESULTS_TR.md; kaynak/fixture sabitleme özetleri message-labels-v11/frozen-holdout-manifest.json.

Öğrenci hedefi kendi context_before alanındaki önceki konuşmayla kodlanır. Yeni topic_scope alanı araştırmacı kontrolündedir:pedagogical/outside/any. Kabul edilmiş7 öğrenci tanımı açıklamaları korunarak v3 pedagojik kapsamla kaydedildi; SQLite yedeği alındı, yanıt/geçmiş karar/değerlendirme tabloları değişmedi. Model message_scope döndürür; sunucu uygun olmayan kapsamı dışlar ve konu kararını audite yazar. Güncel Dahil/Dışla örnekleri tam açıklamanın yanında ayrı ipuçları olarak iletilir. Wire kimlikleri kısaM/L kodlarıdır; doğrulama sonrasında kalıcı kimliklere eşlenir. Önbellek v11 ile yenilendi. Hiçbir kullanıcı vakasında yeniden etiketleme veya puanlama başlatılmadı.

Kontroller: öğrenci33, genel etiket22, süreç28, değerlendirme74, ana81, dışa aktarım39, gezinme82, süre60; Chrome ana135 ve grup/kapsam11 geçti. Güncel tam prompt AI_PROMPTS_REVIEW_TR.md içinde.
