# AI girdi sözleşmeleri · v1

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
