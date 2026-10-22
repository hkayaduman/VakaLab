# AI girdi sözleşmeleri · v1

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
