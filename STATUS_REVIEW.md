# VakaLab durum incelemesi — 21 Ekim 2026

Çalışan bir yerel araştırma pilotu mevcut. Öğrenci akışı, araştırmacı yönetimi, süreç ölçümü, analiz, rapor ve dışa aktarma uygulanmış durumda. Gerçek katılımcıyla veri toplamaya hazır kabul edilmemeli: aşağıdaki iki kod bulgusu ve dış ortam kabulleri açık.

Bu incelemede ürün kodu ve mevcut çalışma veritabanı değiştirilmedi. Doğrulamalar bellek içi veya ayrı sentetik veritabanlarında yapıldı. Mevcut kullanıcı değişiklikleri korundu. Commit/push/dağıtım yapılmadı.

## Sürüm ve teslim

- Yerel HEAD ve yereldeki origin/main referansı: `44aa91e1e3511259819e2066b92b2327dc270ce6` — araştırmacı ekranları ve gezinme/süre düzeltmeleri. Bu incelemede uzak sunucudan fetch yapılmadı; uzak dalın canlı durumu ayrıca doğrulanmadı.
- Çalışma ağacında 0.2.13 ölçüm açıklamaları, `METHODS_EVENTS_DURATIONS_TR.md` ve yerleşim kanıtı henüz commit edilmemişti.
- README ve RELEASE_READINESS hâlâ 0.2.10'u son teslim olarak anlatıyor. Context ve plandaki eski “commit/push yok” cümleleri kendi tarihsel aşamalarına ait; güncel Git durumuyla karıştırılmamalı.

## Yeniden üretilen kod bulguları

### AUDIT-01 · P1 · Eski teslim analizin ilk adımında duruyor

`app/Analysis.php:30`, bütünlük adımında yeni teslim doğrulaması olan `Rules::final()` işlevini çağırıyor. `app/Rules.php:17` ise final_severity alanını koşulsuz zorunlu tutuyor. Bu alan eklenmeden teslim edilmiş değişmez bir kayıt artık bütünlük adımını geçemiyor; sonraki analiz adımları kilitli kalıyor.

Yeniden üretim: yalnız bellek içi SQLite/sentetik seed içinde teslim edilmiş kaydın draft'ından final_severity kaldırıldı; yeni analiz sürümünde 1. adım çalıştırıldı. Sonuç: `state=submitted`, `step1=failed`, `error=invalid_or_failed_output`. Mevcut çalışma verisine dokunulmadı.

Gereken: yeni teslim zorunlulukları ile geçmiş teslimlerin bütünlük denetimini ayırmak; eski kayıtta ölçülmeyen alanı eksik olarak raporlamak. Eski yanıta tahmini puan yazılmamalı, teslim kilidi açılmamalı. Regresyon eski kayıt ve yeni eksik teslim reddini birlikte kapsamalı.

### AUDIT-02 · P2 · Rubrik düzeyi, puanı ve açıklaması birlikte doğrulanmıyor

`app/Rules.php:33`, puanın boyut aralığında ve düzey adının tanımlar arasında olmasını ayrı ayrı kontrol ediyor. Seçilen düzeyin tanımlı puanıyla eşleşmeyi ve puan gerekçesinin dolu metin olmasını kontrol etmiyor. Hem AI çıktısı hem araştırmacının puan düzenlemesi bu doğrulamadan geçiyor.

Yeniden üretim: sentetik rubrikte `Yetersiz=0` iken `score=4`, `level=Yetersiz`, `reason=""` ve kaynakta bulunan geçerli pasaj verildi. Doğrulama reddetmedi; ağırlıklı ortalama 4 döndü. Böylece aynı ölçütte en yüksek puan ve en düşük düzey adı birlikte raporlanabilir.

Gereken: puan-düzey ilişkisinin rubrikteki tanımla tutarlı olması ve açıklamanın dolu metin olması. Ara puan kullanılacaksa bu kural açıkça modellenmeli. AI ve insan düzenleme yollarına aynı negatif regresyon eklenmeli.

İki bulgunun çıktısı: `test-evidence/review-2026-10-21-probes.txt`. Yeniden üretim betiği yerelde `tests/tmp/review-probes.php`; Git dışıdır. Bulgular bu incelemede düzeltilmedi.

## Bu incelemede yeniden çalıştırılan doğrulamalar

| Grup | Sonuç |
|---|---|
| PHP genel alan/SQLite + gerçek dosya paketleri | 81 geçti |
| Navigation | 82 geçti |
| Presence | 57 geçti |
| Süreç/mesaj etiketleri | 28 geçti |
| Export/olay etiketleri | 39 geçti |
| Öğrenci raporu | 15 geçti |
| Rubrik tanımı | 14 geçti |
| JS zaman / etiket / süreç / analiz | 10 / 11 / 11 / 12 geçti |
| Genel Chromium, masaüstü ve mobil emülasyon | 127 geçti |
| İki PHP worker ile HTTP/session/eşzamanlılık | 42 geçti |
| Sahiplik, erişim, vaka kapanışı, ek süre, yayın | 119 geçti |
| Yerel MariaDB | 81 geçti; ayrı restore DB'de 8 tablo sayısı eşit |
| Sözdizimi | 28 PHP ve 7 ürün JS dosyası geçti |

Gruplar örtüşen kapsamlar içerir; sayıları bağımsız senaryo sayısı olarak toplamayın. Mevcut testlerin geçmesi yukarıdaki yeni olumsuz senaryoların doğru çalıştığı anlamına gelmiyor.

İlk sandbox koşusunda PHP XLSX yeniden açma kontrolü dosya okunabilirliği nedeniyle durdu; HTTP ve tarayıcı localhost erişimi de tamamlanmadı. Aynı testlerin normal yerel izinle tekrarları geçti. İlk koşular geçti sayılmadı. Ayrıntılı yerel loglar `tests/tmp/review-*.txt` içindedir. Son başarılı logların kalıcı kopyası `test-evidence/review-2026-10-21/` altındadır.

Bu tur confirmation/presence/journey özel tarayıcı betikleri, hedefli validation/researcher-ui ve uzun metin yerleşim betikleri yeniden çalıştırılmadı; önceki kanıtları tarihsel olarak mevcut. Gerçek sağlayıcı, hosting, fiziksel cihaz veya Disco masaüstü importu test edilmedi. Restore kontrolü tablo sayısı karşılaştırmasıdır; bütün içeriğin hash eşitliği testi olarak sunulmaz.

## Araştırmaya geçiş için eksikler

1. Gerçek AI sağlayıcısı/modeli: sohbet, bağımsız rubrik değerlendirmeleri, mesaj etiketleri, öğrenci feedback'i ve hata/timeout senaryoları henüz mock dışında doğrulanmadı.
2. Gerçek hosting: public kökü, HTTPS/session, özel dosya erişimleri, iki eşzamanlı istek, timeout, kaynak limitleri ve hosting yedeğinin ayrı DB'ye geri yüklenmesi doğrulanmalı.
3. Araştırma içeriği: gerçek vaka/rubrik sürümleri, etiket kod kitabı, kurum onam/saklama/çekilme kararları tamamlanmalı. Sekiz etiketin seed açıklamaları “SENTETİK başlangıç tanımı”; etiket adları bilimsel kodlama yönergesi yerine geçmez.
4. Hedef kullanım kabulü: gerçek cihaz/klavye/erişilebilirlik, gerçek kesinti/crash kurtarması ve Disco'da gerçek import. sessionStorage sekme kapatılınca kaybolabilir; sıfır kayıp garantisi mevcut değil.
5. Hedef hacim: küçük sentetik veriyle çalışan exportların hosting bellek ve dosya boyutu limitlerinde doğrulanması açık.

## Bağlam ve bakım borcu

- CONTEXT ve plan aynı gelişmeleri tekrar eden uzun tarihçelere dönmüş. Güncel durum ve açık işler üstte tek bir özet olmalı; tarihsel kayıtlar silinmeden ayrı tutulmalı.
- RELEASE_READINESS, README ve ARCHITECTURE başlıkları güncel kodun gerisinde. AI_INPUT_CONTRACTS etiketlemeyi yalnız analiz 6 olarak anlatıyor; kodda bağımsız message_labels işlemi de var.
- INSTALL_SHARED_HOSTING son bölümünde “yalnız migration 001” ifadesi bulunuyor; aynı dosyanın başındaki doğru 001+002 gereksinimiyle çelişiyor.
- PROCESS_MINING_REVIEW eski eksik/henüz uygulanmadı listesini koruyor; sonradan uygulanan gezinme, mesaj etkinlikleri ve süre ayrımıyla güncel durum açıkça ayrılmalı. Ham teknik measurement-v1 ile güncel presence-v2 karıştırılmamalı.
- Testler var; ancak Composer test komutu yalnız tests/run.php çalıştırıyor. Tüm gruplar için tek komutlu, hata kodlarını koruyan bir regresyon çalıştırıcısı yararlı. Bu incelemede eklenmedi.
- Domain, UI ve test dosyalarında yoğun tek satırlı kod bakım ve incelemeyi zorlaştırıyor. Büyük yeniden yazım yerine değişen alanları küçük adımlarla sadeleştirmek uygun.

## Önerilen sıra

1. AUDIT-01 ve AUDIT-02'yi mevcut ham veriyi değiştirmeden düzelt; iki negatif senaryoyu kalıcı regresyonlara ekle.
2. Güncel durum/sürüm ve kurulum belgelerini eşitle; 0.2.13 değişiklikleriyle birlikte teslim kapsamını netleştir.
3. Gerçek rubrik ve etiket yönergelerini tamamla; ayrı test hostinginde gerçek AI ile uçtan uca pilot yap.
4. Disco importu, hedef cihaz/kesinti ve yedek geri yükleme kabulünü tamamlayıp araştırmaya başlama kararını ver.

Yeni genel manuel test batch'i başlatılmadı; mevcut kullanıcı tercihi korundu.
