# Öğrenci mesajı etiketleri — inceleme taslağı

23 Ekim 2026. Kullanıcının “tamam devam” talimatıyla aşağıdaki yedi tanım yerel uygulamaya Öğrenci mesajı (user) grubunda v2 olarak kaydedildi. Önceki v1 sürümleri ve geçmiş kararlar korundu.24 Ekim 2026’da açıklamalar aynı kalırken konu kapsamı Pedagojik olarak belirlenip v3 kaydedildi. AI yanıtı tanımları AI_RESPONSE_LABELS_DRAFT_TR.md dosyasında ve uygulamada ayrı gruptadır. Bu dosya ileride yeniden düzenlenebilir.

## Kodlama birimi ve ortak kurallar

Birim öğrencinin tek sohbet mesajıdır. Yakın konuşma bağlamı, örneğin “bir alternatif daha” gibi göndermeleri çözmek için kullanılabilir. Öğrencinin istemediği ancak AI'ın kendiliğinden verdiği içerik, öğrenci niyetine kanıt sayılmaz.

Bir mesaj birden fazla açık talep içeriyorsa birden fazla etiket verilebilir. Aynı talebe yalnız adları benziyor diye birden fazla etiket verilmez. Uygun etiket yoksa [] geçerlidir. Belirsiz, bütün boş eşleşmelerin yerine geçen zorunlu bir kategori değildir.

## Önerilen tanımlar

### Bilgi istedi

Öğrencinin vaka metnindeki bir olguyu, verilen bilgiyi veya pedagojik bir kavramın temel tanımını sorduğu mesajlarda kullanılır. “Kim, ne, ne zaman, var mı?” türü bilgi taleplerini kapsar. Gerekçe, neden-sonuç ilişkisi veya bir önerinin nasıl işlediğini açıklama taleplerini; metin düzenleme, eleştiri, alternatif ve karar taleplerini tek başına kapsamaz.

Dahil: “Öğretmen olayı görmüş mü?”, “Onarıcı yaklaşım nedir?”
Dışla: “Bu yaklaşım neden uygun?” (Açıklama istedi).

### Açıklama istedi

Öğrencinin bir ifadenin, olayın veya pedagojik yaklaşımın gerekçesini, neden-sonuç ilişkisini, işleyişini veya somut bir örnekle anlamını açmayı istediği mesajlarda kullanılır. Bir yanıtı daha anlaşılır anlatma talepleri de kapsamdadır. Yalnız temel tanım/olgu sormayı, öğrencinin kendi metnini yeniden yazdırmayı ve doğrudan hangi eylemi seçmesi gerektiğini sormayı kapsamaz.

Dahil: “Öğrencileri ayrı ayrı dinlemek neden önemli?”, “Bu yaklaşım bu olayda nasıl işler?”
Dışla: “Öğretmen şimdi ne yapmalı?” (Karar istedi).
Sınır: “Bu yöntem nedir?” temel tanım ister; “Bu yöntem nasıl ve neden işe yarar?” açıklama ister.

### Metin düzenletmek istedi

Öğrencinin kendi verdiği metnin anlatımını, dilini, yapısını veya uzunluğunu değiştirmeyi istediği mesajlarda kullanılır. Yeniden yazma, kısaltma, dilbilgisi düzeltme ve daha anlaşılır ifade etme taleplerini kapsar. Yeni karar/çözüm üretmeyi ve mevcut metnin yeterliliğini yalnız değerlendirmeyi kapsamaz.

Dahil: “Yazdığım gerekçeyi anlamını değiştirmeden kısalt.”
Dışla: “Gerekçemde eksik olan ne?” (Eleştiri istedi).
Sınır: “Yanıtını daha basit açıkla.” kendi metnini düzenletme değil, Açıklama istedi olarak ele alınır.

### Eleştiri istedi

Öğrencinin sunduğu karar, gerekçe veya metnin eksiklerini, tutarlılığını, güçlü/zayıf yönlerini ya da vaka kanıtlarıyla uyumunu değerlendirmeyi istediği mesajlarda kullanılır. Yalnız biçimsel düzeltmeyi, açıklama istemeyi ve açıkça talep edilmeyen yeni karar üretimini kapsamaz.

Dahil: “Kararımın zayıf yönleri neler?”, “Bu gerekçede çelişki var mı?”
Dışla: “Bu paragrafın yazımını düzelt.” (Metin düzenletmek istedi).

### Alternatif fikir istedi

Öğrencinin mevcut öneriden farklı bir yaklaşım, başka olasılık veya birden fazla seçenek istediği mesajlarda kullanılır. Bir seçenek belirleme talebi bulunmadan olası yolları sıralamayı kapsar. Yalnız bir yöntemin gerekçesini sormayı veya doğrudan tek bir eylem önerisi istemeyi kapsamaz.

Dahil: “Bunun dışında başka hangi yollar var?”, “Üç farklı yaklaşım söyle.”
Dışla: “Bu durumda hangisini uygulamalıyım?” (Karar istedi).
Sınır: “Seçenekleri ver ve hangisini önerdiğini söyle.” hem Alternatif fikir istedi hem Karar istedi alır.

### Karar istedi

Öğrencinin vakada ne yapılması gerektiğine dair bir karar/eylem önerisi, seçenekler arasında tercih, öncelik sırası veya uygulanacak adımları istediği mesajlarda kullanılır. Müdahale planı isteme de kapsamdadır. Yalnız mevcut seçenekleri listelemeyi, bir kavramı açıklamayı ve öğrencinin kendi kararını değerlendirmeyi tek başına kapsamaz.

Dahil: “Öğretmen bu durumda ne yapmalı?”, “Hangi yaklaşımı seçmeliyim?”, “Bu vaka için bir müdahale planı öner.”
Dışla: “Başka hangi seçenekler var?” (Alternatif fikir istedi).

### Belirsiz

Vaka veya pedagojik görevle ilgili bir yardım talebi bulunduğu hâlde, mesaj ve yakın bağlamdan talebin yukarıdaki amaçlardan hangisine ait olduğu belirlenemiyorsa kullanılır. Diğer açık amaç etiketleriyle aynı çözümlenmiş talebe birlikte verilmez. Selamlaşma, teşekkür, yalnız görüş bildirme ve etiket kapsamına girmeyen açık talepler otomatik olarak Belirsiz sayılmaz; bunlar [] kalabilir. Bu sınır araştırmacı tarafından özellikle netleştirilmelidir.

Dahil: Önceki bağlamı olmayan “Şunu bir halletsene.”
Dışla: “Teşekkür ederim.” ([]); “Bir alternatif daha ver.” (bağlam uygunsa Alternatif fikir istedi).

## Araştırmacıyla gözden geçirilecek örnekler

Bunlar model sonucu değil, yukarıdaki taslağın tutarlılığını değerlendirmek için önerilen beklenen etiketlerdir. Açıklamalar değişirse beklenen sonuçlar da yeniden belirlenir.

| Öğrenci mesajı | Taslağa göre beklenen etiket |
|---|---|
| Öğretmen olayı kendi gözleriyle görmüş mü? | Bilgi istedi |
| Onarıcı yaklaşım nedir? | Bilgi istedi |
| Bu yaklaşım neden uygun? | Açıklama istedi |
| Bu yaklaşımın bu vakada nasıl işlediğini bir örnekle açıkla. | Açıklama istedi |
| Kendi yazdığım gerekçeyi daha kısa ve düzgün yaz. | Metin düzenletmek istedi |
| Gerekçemde hangi noktalar eksik? | Eleştiri istedi |
| Bu çözümün yerine başka hangi yollar var? | Alternatif fikir istedi |
| Öğretmen şimdi ne yapmalı? | Karar istedi |
| Seçenekleri ver ve hangisini uygulamam gerektiğini söyle. | Alternatif fikir istedi + Karar istedi |
| Kararımı eleştir ve gerekçemin dilini düzelt. | Eleştiri istedi + Metin düzenletmek istedi |
| Teşekkür ederim. | [] |
| Şunu bir halletsene. (önceki bağlam yok) | Belirsiz |

İlk netleştirilecek noktalar: temel kavram tanımı ile ayrıntılı açıklama sınırı; müdahale planlarının Karar istedi altında mı ayrı etikette mi tutulacağı; Belirsiz etiketinin kapsamı. Bu kararlar verildikten sonra tanımlar uygulamada sürüm oluşturularak kaydedilir ve güncel tanımlarla canlı model testi yapılır.


## 24 Ekim — konu kapsamı

Öğrenci tanımlarında pedagojik, pedagoji dışı veya her iki konu kapsamı seçilir. Mevcut yedi etiket pedagojik kapsamda. Konu dışı talepler için daha sonra ayrı etiket açılabilir; alanı pedagoji dışı olarak seçilir. Güncel açıklamalar ve kapsam birlikte uygulanır. Son test raporu test-evidence/student-labels-2026-10-24/RESULTS_TR.md içindedir.
