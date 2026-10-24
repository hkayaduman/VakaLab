# Öğrenci etiketlemesi — 24 Ekim 2026 sonucu

Model OpenRouter üzerinden openai/gpt-5-nano olarak korundu. Aktif etiketleme sürümü message-labels-v11.

## Sonuç

- Önceki ve genişletilmiş regresyon kümesi: **44/44** tam etiket kümesi eşleşmesi.
- Uygulama kodu/promptu sabitlendikten sonra ilk kez denenen ek küme: **8/8**.
- Toplam öğrenci mesajı: **52/52**.
- Etiket adları sabitken açıklamaların değiştirildiği ve açık konu dışı etiketin tanımlandığı ayrı kontrol: **4/4**.

Bu sayılar bu koşudaki küçük sentetik test kümesinin sonucudur; gelecekteki bütün mesajların hatasız kodlanacağı veya genel doğruluğun %100 olduğu anlamına gelmez. Araştırmacı incelemesi ve düzeltmesi mevcut akışın parçasıdır. Kod/prompt, bağımsız son sekiz mesajın sonucuna göre değiştirilmedi; kaynak ve test dosyası özetleri frozen-holdout-manifest.json ile doğrulandı.

## Önceki üç hatanın son durumu

| Öğrenci mesajı | Beklenen | Son sonuç |
|---|---|---|
| Bana hangi hisseyi almam gerektiğini söyle. | [] | [] |
| En kârlı yatırım için hangi fonu alayım? | [] | [] |
| Arabuluculuk kavramını bir cümleyle tanımla. | Bilgi istedi | Bilgi istedi |

Kavram tanımı/gerekçe/eleştiri ayrımları, çoklu talepler, teşekkür ve talep içermeyen bildirimler, kişisel hukuk/finans talepleri ve finans/hukuk sözcüklerini içeren gerçek pedagojik sorular da test edildi. Sonuçların tümü message-labels-v11/summary.json içinde.

## Uygulamadaki değişiklikler

1. Her öğrenci hedefinin context_before alanı yalnız kendisinden önceki konuşmayı içerir. Hedefler ayrı kodlama birimleridir; bir başka hedefin bağlamı kullanılamaz. Sonraki AI yanıtı kendi öğrencisinin bağlamına eklenmez.
2. Etiketlere araştırmacının seçtiği topic_scope alanı eklendi: pedagogical (pedagojik vaka/kavram), outside (pedagoji dışı talepler), any (her ikisi). Mevcut yedi öğrenci tanımı açıklamaları değiştirilmeden pedagojik kapsamla v3 olarak kaydedildi. Önceki v1/v2 ve geçmiş kararlar korundu. Bu alan yeni öğrenci etiketi oluştururken veya düzenlerken zorunludur.
3. Model her mesajın konusunu ve etiket eşleşmelerini döndürür. Sunucu, konu ile araştırmacının belirlediği kapsam uyuşmuyorsa olumlu etiketi dışlar. Model kapsamı geçersiz kılamaz. Özel konu dışı/iki kapsamlı etiketler tanımlanabilir. Modelin konu kararı denetim kaydına eklenir.
4. Güncel ad/açıklama/kapsam gönderilir. Açıklamadaki Dahil ve Dışla satırlarının tırnaklı örnekleri ayrıca positive_examples/negative_examples olarak çıkarılır. Tam açıklama asıl kaynaktır; örnek formatı zorunlu değildir. Tanım değişince eski örnekler taşınmaz.
5. Öğrenci etiketlemesinde mesaj ve etiket kimlikleri sağlayıcıya M1/L1 gibi kısa geçici kodlarla iletilir. Yanıt doğrulanıp gerçek kalıcı kimliklere çevrilir. Bilinmeyen kodlar reddedilir. Kalıcı kayıt kimlikleri değişmez. İlk denemeler bu değişikliğin gerekçesidir; tek başına evrensel bir kalite artışı iddiası kurulmaz.
6. Önceki şema/doğrulama, boş ve çoklu etiket, parça tamamlanmadan yazmama, konuşma/tanım değişince eski sonucu reddetme, grup bazlı önbellek ve araştırmacı seçiminin önceliği korunur. AI yanıtı etiketlemesi ayrı ve isteğe bağlıdır. Eski karma analiz çağrıları uyumluluk yolunu kullanır; öğrenci ana işlemi yukarıdaki yeni yoldur.

## Veri ve tarihçe

Kapsam alanı eklenmeden önce tutarlı SQLite yedeği alındı. Açıklamalar değişmedi. Öğrenci yanıtları, mesajlar, geçmiş etiketler ve değerlendirme tablolarının değişmediği işlem öncesi/sonrası özetlerle doğrulandı (scope-installation.json). Bu testler kullanıcının vakalarına etiket kaydetmedi, puanlama veya değerlendirme başlatmadı.

Önceki başarısız denemeler silinmedi. v7/v8 prompt, talep özeti ve iki koşul denemeleri yeterli tutarlılık sağlamadı. v9 kapsam kontrolünü ekledi; v10 açıklama örneklerini ayrıştırdı. Bunlarda kalan uyuşmazlıklar kendi kayıtlarında bulunur. v11 kısa geçici kimlikleri ekledikten sonra regresyon ve sabit kodla ayrılmış son testler yukarıdaki sonuçları verdi. Beklenen etiketler veya açıklamalar model sonucuna göre değiştirilmedi; yalnız kapsam politikası açıklamalara uygun yapılandırılmış alan olarak eklendi.

## Teknik kontroller

- Öğrenci sözleşmesi: 33 kontrol.
- Genel etiket sözleşmesi: 22; süreç: 28; değerlendirme: 74.
- Ana işlevler: 81; dışa aktarım: 39; gezinme: 82; süre hesabı: 60.
- Chrome masaüstü/mobil görünüm ana akış: 135.
- Etiket grubu ve yeni kapsam alanı tarayıcı kontrolleri: 11.

Bu kontrollerin tamamı geçti; yerel kontroller anlam doğruluğunun yerine geçmez. Tarayıcı akışı sentetik ve mock sağlayıcılıdır; gerçek model sonucu yukarıdaki ayrı canlı karşılaştırmadır.

## Yeniden çalıştırma

VAKALAB_RUN_LIVE_PROMPT_TEST=1 ile tests/student-labels-live.php 0–12: 0–10 regresyon, 11–12 ilk koşuda ayrılmış son kümedir. Daha sonra tekrar çalıştırıldıklarında bu sekiz örnek artık görülmemiş sayılmaz. tests/student-label-overrides-live.php değiştirilmiş tanım kontrolüdür. Açık ortam değişkeni olmadan API çağrısı yapılmaz. Çalışma veritabanından yalnız güncel etiket tanımları salt okunur okunur; öğrenci test metinleri sentetiktir. Tanım/kapsam değiştirildiğinde beklenen sonuçlar araştırmacı tarafından yeniden gözden geçirilmelidir.

Prompt düzenlemesinde kullanılan resmi kaynak: [OpenAI GPT-5 prompting guide](https://developers.openai.com/cookbook/examples/gpt-5/gpt-5_prompting_guide). Bu kaynak uygulamaya özel 52/52 sonucu kanıtlamaz; sonuç yerel test kayıtlarına dayanır.
