# Öğrenci etiketlemesi — odaklanmış test

23 Ekim 2026. Kullanıcı AI yanıtı etiketlemesini ikinci önceliğe aldı; öğrenci etiketlemesinin doğruluğu öncelikli.

## Sonuç

OpenRouter GPT-5 Nano, message-labels-v6: **28 sentetik öğrenci mesajında 25 tam etiket kümesi eşleşmesi**. Önceki 16 mesajın 15'i, yeni 12 mesajın 10'u eşleşti. Testlerin tamamı geçmedi; genel doğruluk garantisi değildir. Aşağıdaki üç sorun açık kalıyor.

| Öğrenci mesajı | Beklenen | Modelin seçimi |
|---|---|---|
| Bana hangi hisseyi almam gerektiğini söyle. | [] | Karar istedi |
| Arabuluculuk kavramını bir cümleyle tanımla. | Bilgi istedi | Açıklama istedi |
| En kârlı yatırım için hangi fonu alayım? | [] | Bilgi istedi |

## Uygulamadaki değişiklik

Ana işlem “Öğrenci mesajlarını etiketle” oldu. Bu işlem yalnız öğrenci mesajlarını ve öğrenci grubundaki güncel etiketlerin tamamını sınıflandırır. AI mesajları yalnız konuşma bağlamındadır; AI'ın kendiliğinden verdiği yanıtlar öğrenci isteği kabul edilmez. “AI yanıtlarını etiketle (isteğe bağlı)” ayrı işlemdir. Bir gruptaki işlem, diğer grubu etiketlenmiş veya etiketsiz ilan etmez; geçmiş öneri ve araştırmacı kararlarını silmez.

Öğrenciye özel sistem promptu kullanılır. Model her uygun etiket için true/false kararı verir; sunucu yalnız true olanları benzersiz etiket listesine dönüştürür. Eksik karar, bilinmeyen kimlik, yanlış rol ve boolean olmayan değer reddedilir. Bütün etiketler false ise boş eşleşme [] korunur. Öğrenci tanımları değişmedi; ad ve açıklamalar her istekte güncel olarak iletilir. Aynı açıklama JSON içinde iki kez gönderilmez; orijinal tanım gövdesi denetim kaydında korunur.

Uzun konuşmada her çağrı en çok dört öğrenci mesajını hedefler. Aynı konuşma bağlamı ve güncel tanımlar her parçada bulunur. Bütün parçalar doğrulanmadan sonuç veritabanına yazılmaz. Tanımlar veya konuşma işlem sürerken değişirse sonuç reddedilir. Öğrenci ve AI önbellekleri ayrıdır; AI bağlamı değişirse öğrenci önerisi yeniden üretilir. Sonuç yine AI önerisidir; araştırmacı seçimi önceliklidir.

## Denemeler ve sınırlar

Önceki karma etiketleme testinde öğrenci sonuçları 13/16 idi. Yalnız öğrenciye ayrılan ilk liste seçimi denemesi de 13/16 verdi. Etiket başına boolean karar denemesi 15/16 verdi. Son promptla, eski ve yeni mesajlar yeniden denenerek yukarıdaki 25/28 sonucu elde edildi. Beklenen sonuçlar her çağrıdan önce dosyaya yazıldı; modele gönderilmedi. Kaydedilmiş öğrenci mesajları test girdisi yapılmadı ve kullanıcının vakalarında yeniden etiketleme veya puanlama başlatılmadı.

Tek çağrıda 16 hedefli ilk öğrenci denemesinde sağlayıcı yanıtı alınamadı; dört hedefli çağrılara bölündü. Önceki denemeler silinmedi. Hata örneklerine göre tanımları veya beklenen sonuçları değiştirip testi geçmiş gibi göstermedik; yukarıdaki üç uyuşmazlık açık tutuluyor.

Yeni 20 yerel kontrol; öğrenci hedefi/AI bağlamı ayrımını, şema/decoder doğrulamasını, çoklu ve boş etiketlemeyi, parçalı işlemde hedeflerin korunmasını, ayrı önbellekleri ve araştırmacı seçiminin önceliğini doğruladı. Bunlar modelin anlam doğruluğunu kanıtlayan testler değildir.

Canlı test: tests/student-labels-live.php. VAKALAB_RUN_LIVE_PROMPT_TEST=1 verilmeden API çağrısı yapılmaz. Grup argümanları 0–3 önceki 16 mesajı, 4–6 yeni 12 mesajı çalıştırır. Çalışma veritabanından yalnız güncel tanımlar salt okunur alınır. Sonuçlar message-labels-v6 klasöründedir.


Son regresyon kontrolleri: yeni öğrenci sözleşmesi 20, genel etiket sözleşmesi 22, süreç 28, sohbet sözleşmesi9, süreç JavaScript11, değerlendirme74 ve gerçek Chrome masaüstü/mobil görünüm134 kontrolü geçti. Bunlar yukarıdaki üç anlamsal uyuşmazlığı kapatmaz.
