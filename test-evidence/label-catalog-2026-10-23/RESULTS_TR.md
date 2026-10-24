# Kaydedilen etiketler — canlı karşılaştırma sonucu

23 Ekim 2026. Yerel uygulamaya 7 öğrenci etiketi önceki sürümleri korunarak v2, 9 AI yanıtı etiketi yeni v1 olarak kaydedildi. İşlemden önce tutarlı SQLite yedeği alındı. Öğrenci yanıtları, mesajları, eski etiket kararları ve değerlendirme tabloları değiştirilmedi.

## Sonuç

OpenRouter openai/gpt-5-nano, message-labels-v4: **32 sentetik mesajda 27 tam etiket kümesi eşleşmesi**. Öğrenci mesajları 13/16, AI yanıtları 14/16. Yanlış rol grubundan etiket seçimi ve tekrarlı etiket v4 denemesinde görülmedi. Bu sayı kontrollü, küçük örnek kümesinin sonucudur; genel doğruluk oranı değildir. Araştırmacı incelemesi gereklidir.

## Kalan beş uyuşmazlık

| Mesaj | Beklenen | Modelin seçimi |
|---|---|---|
| Gerekçemde hangi noktalar eksik? | Eleştiri istedi | Açıklama istedi |
| Seçenekleri ver ve hangisini uygulamam gerektiğini söyle. | Alternatif fikir istedi, Karar istedi | Alternatif fikir istedi, Eleştiri istedi, Karar istedi |
| Bana hangi hisseyi almam gerektiğini söyle. | [] | Eleştiri istedi, Karar istedi |
| Sence öğretmen ne yapmalı? Kararının gerekçesini de yaz. | Soru sordu | Kapsam sınırı belirtti, Metin düzenledi, Soru sordu |
| Birlikte başka bir çözüm geliştirmek ister misin? | Devam daveti ekledi, Soru sordu | Soru sordu |

## Testin kapsamı

Beklenen etiketler çağrıdan önce yazıldı. Modele beklenen cevaplar gönderilmedi; yalnız sentetik mesajlar ve güncel etiket tanımlarının tamamı gönderildi. Grup ve açıklama eşleşmesi katalogla birebir doğrulandı. Çalışma veritabanından yalnız etiket tanımları salt okunur sorgulandı. Vaka mesajlarına etiket kaydedilmedi; öğrenci puanlaması veya değerlendirmesi başlatılmadı.

32 mesajın tek çağrıda işlendiği ilk denemede sağlayıcı yanıtı alınamadı. Ardından mesajlar dörder konuşma çifti içeren 8 mesajlık gruplarla denendi. v3 denemesinde tekrarlı etiket nedeniyle bir grup sunucu doğrulamasında reddedildi; diğer gruplarda anlam eşleşmesi sorunları vardı. Bu dosyalar kök kanıt klasöründe saklandı. v4 promptu tekrar yasağını, açıklama/eleştiri ayrımını ve bir mesajdaki birden fazla davranışın ayrı kodlanmasını netleştirdi. Tanımlar veya beklenen cevaplar sonuca uydurulmadı. Yeni sonuçlar message-labels-v4 klasöründe tutuldu.

## Yeniden çalıştırma

`VAKALAB_RUN_LIVE_PROMPT_TEST=1` ortam değişkeniyle `tests/label-catalog-live.php 0` (diğer gruplar: 1, 2, 3). Açık değişken verilmeden API çağrısı yapılmaz. Bu test kurulum kaydından araştırmacının etiket kümesini bulur; güncel tanımlar katalogdan farklıysa sessizce eski örneklerle test etmek yerine durur. Grup argümanı yoksa tam 32 mesaj gönderilir; yerel timeout sınırına takılabilir.

Etiket sözleşmesinin 22 yerel kontrolü de geçti. Sonuç, etiketi otomatik kesinleştirme veya öğrenci performansını değerlendirme onayı değildir. Özellikle eleştiri/açıklama sınırı, kapsam dışı isteği boş bırakma, çoklu etiketlerde fazla/eksik seçim için daha fazla bağımsız örnekle çalışma gerekir.
