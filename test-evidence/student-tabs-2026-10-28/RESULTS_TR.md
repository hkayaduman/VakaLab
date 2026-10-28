# Öğrenci bölüm menüsü — yerel doğrulama

- Yedi bölüm aynı sırada; numara ve kısa etiket birlikte görünür.
- 1440 px: bir satır; 390/768 px: 4+3; 320 px: 3+3+1.
- 28 bölüm/ekran kombinasyonu: yatay taşma yok, tüm sekmeler görünür, mobil yapışkan menü kontrolü başarılı.
- Araştırmacı görünümü öğrenci stil dosyası açık/kapalı karşılaştırmasında aynı.
- Sekme/süre ölçümü: 39/39; tarayıcı iş akışı: 115/115; eksik alan/teslim: 50/50 başarılı.
- İki mevcut test seçicisi yeni sunuma uyarlandı: başlık yeni kapsayıcıdan, kilit durumu aria-label üzerinden okunuyor. İlk doğrulama koşusunda eski metin seçicisi başarısız oldu; davranış kontrolleri korunarak tekrar başarılı çalıştırıldı.
- Testler ayrı sentetik veritabanları kullanır. Bu değişiklik için tam regresyon paketi yeniden çalıştırılmadı; önceki tam koşu ayrı raporda saklıdır.
- Yerel önizleme: http://127.0.0.1:8088/
