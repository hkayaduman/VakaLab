# 23 Ekim 2026 etiket kataloğu

2026-10-23.json, kullanıcı tarafından kabul edilen 7 öğrenci ve 9 AI yanıtı etiketinin içe aktarılan tanımlarını içerir. Tanımlar uygulamaya uygulamanın Admin::mutate/definition işlemiyle kaydedildi. Öğrenci tanımları önceki sürümlerin çocuğu olarak v2, AI tanımları yeni v1 kayıtlarıdır. Kaynak SHA-256 değerleri, dosyalara etkinleştirme durum notu eklenmeden önceki kabul edilen metinlere aittir.

İçe aktarmadan önce storage altında SQLite'ın tutarlı VACUUM INTO yedeği alındı. Yedek Git tarafından dışlanır. Çalışma yanıtlarının, mesajların, geçmiş etiket kararlarının ve değerlendirme tablolarının işlem öncesi/sonrası içerik özetleri karşılaştırıldı; değişiklik yok. İşlem dökümü test-evidence/label-catalog-2026-10-23/installation.json içindedir.

Canlı karşılaştırma tests/label-catalog-live.php ile açıkça VAKALAB_RUN_LIVE_PROMPT_TEST=1 verilerek çalıştırılır. Çalışma veritabanından yalnız güncel etiket tanımları salt okunur olarak alınır; öğrenci mesajları sorgulanmaz. Gönderilen 32 mesaj sentetiktir. Beklenen etiketler sağlayıcı çağrısından önce expected-before-call.json dosyasına yazılır; model sonucu bunlara göre karşılaştırılır. Kaydedilmiş vaka veya mesaj etiketlemesi başlatılmaz.


24 Ekim 2026: katalogdaki öğrenci gövdelerine topic_scope=pedagogical eklendi; yerelde açıklamalar değişmeden v3 oluşturuldu. Önceki kurulum manifesti korunur; yeni işlem test-evidence/student-labels-2026-10-24/scope-installation.json içindedir. Canlı öğrenci testinin güncel girişi tests/student-labels-live.php, sonuçları 24 Ekim raporundadır.
