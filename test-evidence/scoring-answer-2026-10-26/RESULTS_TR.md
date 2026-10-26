# Canlı hosting puanlama çelişkisi — 26 Ekim 2026

İncelenen kurgusal deneme dışa aktarımında üç başlangıç yanıtı analiz girdisinde dolu. question_score alt girdilerinde de sources tam metni taşıyor. Model gerçek yanıt cümlelerini quote içinde aynen seçmesine rağmen üç soruya sıfır verdi; karar ve gerekçede yanıtın paylaşılmadığını iddia etti. Sağlıkla ilişkili kanıt gibi vaka dışı ölçüt de kullandı. Bu veri kaybı kanıtı değildir; gözlenen sonuç kendi girdisi/alıntısıyla çelişir.

Değişiklik review-v2.4: Ai::questionMessages gerçek soruyu, student_answer.source_id/text, güncel rubriği ve vaka metnini açık alanlarda gönderir. Öğrenci iş akışı yönergeleri ve örnek çıktı yer tutucuları puanlama girdisinden çıkarılır. Tek dolu kaynak koşulu ve sistem/user ayrımı korunur; görev değiştiren metinler veri kalır. Prompt soru kapsamı, yanıt varlığı/kalitesi ayrımı ve gerçek alıntı-gerekçe tutarlılığını açıklar. Model veya sağlayıcı değiştirilmez.

Review::questionResult mevcut aralık/düzey/kanıt kontrollerine ek olarak dolu kaynakla çelişen açık Türkçe yanıt-yok iddialarını genel geri bildirim, ölçüt gerekçesi ve ölçüt geri bildiriminde reddeder. Üretim sonucu tamamlanmış olarak kaydedilmez; önceki sonuçlar kabul/edit kapısında da kontrol edilir. Yeni sağlayıcı denemesi otomatik yapılmaz. Puan yükseltilmez. Bu dar metin kontrolü genel anlamsal doğruluk denetimi değildir: ilgisizlik iddiası veya başka sözcüklerle kurulmuş çelişkiler kaçabilir. Öğrencinin yanıtını veya modelin bütün değerlendirmelerini doğru sayma garantisi vermez.

Kanıt fixture: tests/fixtures/scoring-hosting-contradictions.json yalnız bu sentetik denemenin üç soru girdisi ve sonuçlarını içerir. Model çağrısı yok.

Geçen kontroller: scoring-presence 26/26; review-v2 96/96 (eski çelişkili çıktının kabulünün engellenmesi ve karar yazılmaması dahil); ana 87/87; eski teslim 21/21; sohbet sözleşmesi 9/9; etiket sözleşmesi 22/22. PHP syntax ve diff kontrolü geçti. İzole testlerdir; gerçek sağlayıcıyla yeni sonuç doğrulanmadı. İlk hedefli testte beklenen Fault kodunun 0 yerine 422 olması düzeltildi; üretim doğrulaması değiştirilmedi.

Db.php sabit SET SESSION wait_timeout=600 halinde tutulur; kullanıcı bu sürümle canlı araştırmacı panelinin açıldığını gösterdi. public/.htaccess, kullanıcının zaten canlıya uyguladığı public erişim izni ve yalnız op=step için LiteSpeed noconntimeout kuralıyla kaynakta eşitlendi. Bunlar bütün canlı doğrulamaların tamamlandığı anlamına gelmez.

Yükleme: storage/releases/scoring-answer-fix içindeki yalnız Ai.php ve Review.php sunucudaki vakalab/app içine konulur. Veritabanı geçişi, seed, anahtar yükleme veya mevcut değerlendirme yeniden yazımı yok. Sonra mevcut başlangıç bölümünde Yeniden hesaplat kullanılarak yeni canlı sonuç incelenmelidir. Diğer bölümlerin puanları da insan incelemesinde kalır.
