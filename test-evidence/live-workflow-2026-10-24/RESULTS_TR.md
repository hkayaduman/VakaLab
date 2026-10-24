# Gerçek model ile sentetik iş akışı testi

24 Ekim 2026. Model OpenRouter üzerinden `openai/gpt-5-nano`. Test veritabanı yalnız `tests/tmp/live_workflow_7f1145161d9f5901cc30a216a1aa806b.sqlite` dosyasıdır. Yapılandırılmış çalışma veritabanı açılmadı; mevcut öğrenci yanıtları, etiketleri ve değerlendirmeleri değiştirilmedi. API anahtarı kanıtlara yazılmadı.

Teknik sohbet, etiketleme, teslim, araştırmacı düzeltmesi, rapor erişimi ve dışa aktarım akışı geçti. **AI puanlarının anlam doğruluğu onaylanmış değildir. AI-SCORE-04 açıktır.** Raporun araştırmacı adımları sentetik testte simüle edildi; gerçek bir uzmanın akademik onayı değildir.

## Doğrulanan davranışlar

- İlk yanıtlar kesinleşmeden sohbet engellendi. Gerçek model, “Öğretmen tartışmaya doğrudan tanık olmuş mu?” sorusuna “Hayır; öğretmen doğrudan tanık olmamıştır.” yanıtını verdi. Ek görev veya devam daveti eklemedi.
- Öğrenci sorusu yalnız güncel “Bilgi istedi” etiketiyle eşleşti; isteğe bağlı AI yanıtı etiketlemesi yapılmadı. Aynı içerik/tanımlarda önbellek kullanıldı.
- İlk snapshot korundu, son teslimden sonra yanıt düzenleme engellendi. Başlangıçtaki üç, nihai iki ve yansıtma bir soru ayrı işlendi.
- Profilde güven 5→6, ciddiyet 6→7 karşılaştırıldı. Ölçülmeyen nihai ön bilgi/zorluk değerleri uydurulmadı. Sohbet istatistiği yalnız sistem hesabıyla bir öğrenci ve bir AI mesajı olarak üretildi.
- Araştırmacı kararları olmadan rapor birleştirilemedi. Onaysız rapora öğrenci erişemedi. Veri toplama kapanmadan öğrenciye yayın engellendi. Test vakası kapatılıp rapor gösterilince yalnız öğrenci projeksiyonu açıldı.
- Özel vaka notu ve araştırmacı iç notu öğrenci projeksiyonuna taşınmadı. Yeniden hazırlama aynı rapor kimliğini korudu.
- PDF, DOCX, XLSX ve Disco CSV üretildi. CSV öğrenci etiketini içeriyor. Altı özgün öğrenci yanıtı PDF ve DOCX metninde; altı soru puanı ve geri bildirim XLSX ve DOCX içinde eşleşti. PDF toplamı 23/24, bölümler 12/12, 8/8, 3/4. Bunlar düzeltme simülasyonu sonrası teknik test puanlarıdır; öğrenme etkisi kanıtı değildir.

## Canlı modelde bulunan hatalar

Başlangıç puanlamasının ilk üç denemesi sırasıyla genel çıktı doğrulaması, düz metin yerine kodlanmış JSON ve kaynakta bulunmayan kanıt nedeniyle başarısız oldu. Geçersiz sonuçlar tamamlanmış değerlendirme olarak kaydedilmedi. İlk denemede ayrıntılı tanı yoktu; sonraki tanılar güvenli audit kaydıyla saklandı.

Dördüncü deneme biçim doğrulamasından geçti; buna rağmen güçlü gerekçeye sıfır puan verdi ve kök geri bildirimde tırnaklı alan parçası üretti. Beşinci denemede güncellenmiş prompt gerekçeyi doğru değerlendirdi fakat bu kez temel problem yanıtındaki açık vaka ilişkisini yok sayarak sıfır verdi. Bu sonuç otomatik kabul edilmedi. Testte yalnız bu sorunun iki ölçütü, kendi yanıtındaki alıntılarla 4/4 olarak düzeltildi; özgün AI çıktısı ve düzeltme birlikte korundu.

Sabit iki karşılaştırma örneğinde yeni prompt, açık gerekçeyi her iki ölçütte 4, gerekçesiz yanıtı her iki ölçütte 0 puanladı. **2/2 sonucu daha geniş iş akışındaki tutarsızlığı kapatmaz.** Daha geniş, araştırmacının önceden puanladığı ve tekrar koşular içeren bir örnek kümesi gerekir. Yeni soru puanlama sistem metni [prompt inceleme dosyasında](../../AI_PROMPTS_REVIEW_TR.md).

## Yapılan düzeltmeler

- `review-v2.1` soru promptu kaynak/kanıt/gerekçeyi düzey ve puandan önce değerlendirir; yalnız ilgili sorunun tüm yanıtını ve rubrik açıklamalarını esas alır.
- Katı soru şemasında kanıt, mevcut öğrenci yanıtından çıkarılan gerçek cümlelerden seçilir. Kaynak/alinti ve düzey/puan eşleşmesi sunucuda ayrıca denetlenir.
- Geri bildirim şeması harfle başlayan düz Türkçe paragraf ister. Sunucu kodlanmış nesne/dizi, tırnaklı alan parçası ve kod bloğunu reddeder. Katı şema kullanmayan sağlayıcılar için mevcut JSON uyumluluğu sürer.
- Hatalı değerlendirme için soru kimliği ve doğrulama nedeni audit kaydına eklenir; sağlayıcının ham yanıtı veya kimlik bilgileri eklenmez.
- PDF'de soru/yanıt etiketleri takip eden paragrafla tutulur; paragraf aralığı düzenlenerek tek satırlık artık sayfa önlendi.

## Kontroller ve sınırlar

Yerel izole kontroller: ana81, öğrenci etiket33, etiket sözleşmesi22, süreç28, değerlendirme85, dışa aktarım39, gezinme82, süre60, sohbet9; toplam439. Chrome etiket grubu/kapsam11. Önceki öğrenci etiketleme 52/52 ve değişen tanım4/4 canlı testleri ayrı tarihsel kanıttır; bu çalışmada etiketleme mantığı değiştirilmedi. `Ai.php` içindeki puanlama değişiklikleri nedeniyle önceki dosya sabitleme özeti güncel dosyayla eşit değildir.

PDF son sürümü üç sayfa olarak render edilip tamamı görsel incelendi; altı yanıt ve toplam puan metin çıkarımıyla da doğrulandı. DOCX paketi ve metinleri kontrol edildi fakat `render_docx.py` bu bilgisayarda LibreOffice bulunmadığı için görsel kontrolü tamamlayamadı. DOCX görsel olarak onaylanmış sayılmaz. XLSX puanları güvenli metin hücreleri olarak saklanır; sayısal karşılaştırmada dönüştürülerek kontrol edildi.

Ziyaret olayları test kodunun ürettiği sentetik telemetridir; gerçek kullanıcı çalışma süresi veya dikkat ölçümü değildir. Test yardımcı kodunda bool dönüşümü ve istatistik metodu adı hataları düzeltildi; bunlar uygulama hatası değildi. Sandbox'ın XLSX arşivi, mPDF önbelleği ve localhost Chrome erişimini sınırladığı kontroller uygun izinle yeniden çalıştırıldı. GitHub'a gönderim yapılmadı.

## Kanıtlar

- [İş akışı aşamaları](7f1145161d9f5901cc30a216a1aa806b/phase-results.json)
- [Denemeler ve model kayıtları](7f1145161d9f5901cc30a216a1aa806b/workflow-statistics.json)
- [AI sonucu ve düzeltme simülasyonu](7f1145161d9f5901cc30a216a1aa806b/correct-3.json)
- [Sabit karşılaştırma sonuçları](scoring-20261024-181336/summary.json)
- [Dosya içerik ve hash kontrolleri](7f1145161d9f5901cc30a216a1aa806b/artifact-qa.json)

Eski başarısız `review-3-attempt-*.json` kayıtları ve eski nihai değerlendirme denemesi korunmuştur. Son `phase-results` değerleri teknik aşamaların son durumudur; puanların anlam doğruluğu sonucunu temsil etmez.

## 24 Ekim 2026 kısa ön değerlendirme promptu review-v2.2

Kullanıcı değerlendirmeyi araştırmacının inceleyeceği bir ön öneri olarak kullanacağını netleştirdi. Soru sistem promptu1579 karakterden808 karaktere indirildi (%49). Rubrik açıklamaları, sorunun kapsamı, gerçek kanıt, düzey-puan tutarlılığı, kısa geri bildirim ve araştırmacının son kararı korundu. Teknik şema/doğrulama kuralları, sohbet ve etiketleme promptları değişmedi. Önceden sabitlenmiş güçlü gerekçe ve gerekçesiz yanıtın gerçek GPT-5 Nano kontrolü2/2, yerel değerlendirme kontrolleri85/85 geçti. Kanıt: test-evidence/live-workflow-2026-10-24/scoring-20261024-182653. Bu küçük kontrol önceki anlamsal tutarsızlık kaydını kapatmaz; araştırmacı incelemesi mevcut iş akışının parçasıdır. Güncel metin AI_PROMPTS_REVIEW_TR.md son bölümünde.
