# AI yanıtı etiketleri — inceleme taslağı

23 Ekim 2026. Öğrenci etiketleri taslağını tamamlayan önerilerdir. Kullanıcının “tamam devam” talimatıyla aşağıdaki dokuz tanım yerel uygulamaya **AI yanıtı (assistant)** grubunda v1 olarak kaydedildi. Bu dosya ileride yeniden düzenlenebilir.

## Ortak kodlama kuralları

Kodlama birimi tek AI mesajıdır. AI'ın mesajda yaptığı işlem etiketlenir. Öğrencinin önceki isteği, örneğin düzenlenecek metni veya gönderme yapılan öneriyi belirlemek için bağlam olarak okunabilir. Öğrencinin talep ettiği ama AI'ın yapmadığı işlem, AI yanıtına etiket olarak verilmez.

Birden fazla açık davranış varsa birden fazla etiket verilebilir. Mesajın uzunluğu, ilgili davranışın varlığını tek başına göstermez. Hiçbir tanım karşılanmıyorsa [] kullanılır. Etiketler yanıtın doğru, yararlı veya pedagojik açıdan başarılı olduğunu göstermez.

Öğrencinin talebi ile AI davranışı bağımsız kodlanır. Öğrenci yalnız bilgi sorduğunda AI kendiliğinden plan verirse öğrenciye Karar istedi etiketi eklenmez; AI yanıtına Karar veya eylem önerdi etiketi verilir. Davranışın istenmiş olup olmadığı bu etiketlerden tek başına çıkarılmaz; bunun için iki mesajın içeriği karşılaştırılır.

## Önerilen etiketler

### Bilgi verdi

Vaka olgusu, temel kavram tanımı veya kısa bilgi aktaran AI yanıtlarında kullanılır. Vaka metnini özetleme de kapsamdadır. Yalnız yanıt verebileceğini söylemek veya selamlaşmak bilgi verme sayılmaz. Neden-sonuç, gerekçe veya işleyiş açıklaması tek başına bu etiketi gerektirmez.

Dahil: “Vaka metnine göre öğretmen olaya tanık olmamış.”
Dışla: “Evet, sana yardımcı olabilirim.”

### Açıklama yaptı

Bir olayın, yaklaşımın veya önerinin nedenini, gerekçesini, işleyişini ya da kavramlar arasındaki ilişkiyi açan AI yanıtlarında kullanılır. Açıklayıcı örnekler kapsamdadır. Yalnız temel tanım vermeyi veya yapılacakları sıralamayı kapsamaz.

Dahil: “Ayrı görüşmeler, öğrencilerin akran baskısı olmadan anlatım sunmasını sağlayabilir.”
Dışla: “Önce ayrı görüşmeler yap.” (Karar veya eylem önerdi.)

### Metin düzenledi

Öğrencinin sağladığı metni anlam, dil, yapı veya uzunluk bakımından yeniden düzenleyen AI yanıtlarında kullanılır. Düzenlenmiş metin ya da somut düzeltme bulunmalıdır. Yalnız düzenlemeyi teklif etmek veya metnin eksiklerini yorumlamak yeterli değildir.

Dahil: Öğrencinin paragrafını daha kısa biçimde yeniden yazan yanıt.
Dışla: “İstersen paragrafını kısaltabilirim.” (Devam daveti ekledi.)

### Eleştirel değerlendirme yaptı

Öğrencinin sunduğu karar, gerekçe veya metnin güçlü/zayıf yönlerini, tutarlılığını, eksiklerini veya kanıtla uyumunu değerlendiren AI yanıtlarında kullanılır. Yalnız genel övgü, metni tekrar etme veya yeni çözüm sunma bu etiketi gerektirmez.

Dahil: “Gerekçende iki öğrencinin anlatımlarını nasıl karşılaştıracağın belirtilmemiş.”
Dışla: “Harika bir cevap!”

### Alternatif sundu

Mevcut yaklaşımdan farklı bir seçenek veya birden fazla olası yaklaşım sunan AI yanıtlarında kullanılır. Aynı planın ardışık adımları ayrı alternatifler değildir. Seçeneklerden birini ayrıca öneriyorsa Karar veya eylem önerdi etiketi de verilebilir.

Dahil: “Bireysel görüşme veya ortak arabuluculuk görüşmesi düşünülebilir.”
Dışla: “Önce dinle, sonra karşılaştır, ardından görüşme yap.” (Tek planın adımları.)

### Karar veya eylem önerdi

Vakada ne yapılması gerektiğine ilişkin bir tercih, karar, müdahale, öncelik veya uygulanacak adım öneren AI yanıtlarında kullanılır. Eylem planı kapsamdadır. Öğrenci bunu istememiş olsa da mesajda öneri varsa etiket verilir. Bir yaklaşımın yalnız ne olduğunu açıklamak veya seçenekleri tercih belirtmeden sıralamak yeterli değildir.

Dahil: “Bu durumda önce öğrencileri ayrı ayrı dinlemeni öneririm.”
Dışla: “Bireysel görüşme ve arabuluculuk iki farklı yaklaşımdır.”

### Kapsam sınırı belirtti

Bir talebin sohbetin pedagojik kapsamı veya rol sınırları nedeniyle karşılanamayacağını belirten AI yanıtlarında kullanılır. Finansal/hukuki danışmanlık talebini reddetme kapsamdadır. Vaka bilgisinin eksik olduğunu söylemek veya teknik bağlantı hatası tek başına kapsam sınırı değildir.

Dahil: “Bu sohbet yalnız vaka ve pedagojik sorular içindir; finansal danışmanlık veremem.”
Dışla: “Vaka metninde öğrencilerin yaşı belirtilmemiş.” (Bilgi verdi.)

### Soru sordu

AI'ın öğrenciye bir soru yönelttiği veya öğrenciden cevap, gerekçe, puan ya da yansıtma istediği mesajlarda kullanılır. Soru işareti bulunması zorunlu değildir. Başkasının sorusunu alıntılamak veya yanıt içinde retorik bir soruyu kendisinin cevaplaması yeterli değildir.

Dahil: “Sence öğretmen ne yapmalı?”, “Kararının gerekçesini yaz.”
Dışla: “Öğrenci ‘Neden?’ diye sormuş.”

### Devam daveti ekledi

Mevcut yanıtın ardından ek bir çalışma, yeni içerik üretimi veya sohbeti sürdürme teklifinde bulunan AI yanıtlarında kullanılır. “İstersen…” ve “Birlikte … yapabiliriz” ifadeleri, devam teklifi içeriyorsa kapsamdadır. Öğrencinin zaten istediği işi yerine getirmek tek başına devam daveti değildir. İfade ayrıca öğrenciye soru yöneltiyorsa Soru sordu etiketi de verilebilir.

Dahil: “İstersen bu vaka için bir görüşme planı da hazırlayabilirim.”
Dışla: Öğrencinin istediği görüşme planını doğrudan veren yanıt.

## Birlikte kontrol edilecek örnekler

Beklenen etiketler taslağa göre önerilmiştir; bunlar model çıktısı değildir.

| AI mesajı | Beklenen etiket |
|---|---|
| Öğretmen olaya tanık olmamış. | Bilgi verdi |
| Ayrı dinlemek, öğrencilerin akran baskısı olmadan anlatım sunmasına yardımcı olabilir. | Açıklama yaptı |
| Önce öğrencileri ayrı ayrı dinlemeni öneririm. | Karar veya eylem önerdi |
| Bireysel görüşme veya ortak görüşme düşünülebilir. | Alternatif sundu |
| İki yol var: bireysel ya da ortak görüşme. Önce bireysel görüşmeyi öneririm. | Alternatif sundu + Karar veya eylem önerdi |
| Gerekçende farklı anlatımları nasıl karşılaştıracağın belirtilmemiş. | Eleştirel değerlendirme yaptı |
| Bu sohbet yalnız vaka ve pedagojik sorular içindir; finansal danışmanlık veremem. | Kapsam sınırı belirtti |
| Senin kararın ne? Gerekçeni de yaz. | Soru sordu |
| İstersen bir plan da hazırlayabilirim. | Devam daveti ekledi |
| Birlikte başka bir çözüm geliştirmek ister misin? | Soru sordu + Devam daveti ekledi |
| Rica ederim. | [] |

## Sonraki kontrol

Öğrenci ve AI taslakları kesinleştikten sonra her tanım kendi grubuyla sürüm olarak kaydedilir. Model testi öncesinde bu örneklerin beklenen etiketleri araştırmacı tarafından netleştirilir. Ardından modele promptta bulunmayan yeni mesajlar da verilerek beklenen ve önerilen etiketler karşılaştırılır. Bu aşama öğrenci performansını değerlendirme veya puanlama işlemi değildir.
