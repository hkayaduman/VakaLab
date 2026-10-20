# Sentetik veri ve test hesapları

**Yalnız ayrı test ortamında. Gerçek öğrenci, gerçek rubrik ve gerçek gözlem yoktur.** Seed üretim kaydı temizlemez, TRUNCATE/DELETE kullanmaz. Aynı seed yeniden çalışınca mevcut yanıt/parola/sürüm değiştirilmez. Yeni fixture senaryosu için ayrı yeni test DB oluşturun; üretim DB'sini yeniden kullanmayın.

Önce config/local.php: `env=test`, DSN'deki MySQL veritabanı adı `_test` içersin; mock provider, mode=normal. SQLite yalnız yerel deneme seçeneğidir.

Örnekteki `BURAYA_KENDI_TEST_PAROLANIZI_YAZIN` yer tutucusunu kendi belirleyeceğiniz en az 12 karakterlik test parolasıyla değiştirin. Gerçek kullanılan test/üretim parolasını Git deposuna yazmayın.

PowerShell (yalnız geçici test parolası):

```powershell
$env:VAKALAB_SEED_PASSWORD='BURAYA_KENDI_TEST_PAROLANIZI_YAZIN'
php bin/console.php migrate
php bin/console.php seed
Remove-Item Env:\VAKALAB_SEED_PASSWORD
```

Linux/macOS: `VAKALAB_SEED_PASSWORD='BURAYA_KENDI_TEST_PAROLANIZI_YAZIN' php bin/console.php seed` (önce migrate).

Bu belgede verilen geçici parola yalnız yukarıdaki seed çalıştırmasında bütün test hesapları için kullanılır. Üretimde bu hesapları taşımayın. Otomatik test runner'ları rastgele geçici parola üretir; bu hesaplarla aynı DB'yi kullanmaz.

| Giriş | İlk seed durumu (ilk vaka) |
|---|---|
| TEST-ARASTIRMACI | Araştırmacı, matris/yönetim/analiz/rapor |
| TEST-OGR-01 | Açmamış; 5. vakaya atanmamış; ilk manuel akış için |
| TEST-OGR-02 | Profil + başlangıç taslağı; kesinleşmemiş |
| TEST-OGR-03 | Başlangıç kesinleşmiş; henüz mesaj yok |
| TEST-OGR-04 | Başlangıç kilitli + mock konuşması |
| TEST-OGR-05 | Nihai taslak + yansıtma; teslim yok |
| TEST-OGR-06 | Teslim edilmiş; güven 8→10; sıralı analiz/kararlar; onaylı yayımlanmamış ve incelemede rapor |
| TEST-OGR-07 | Dondurulmuş; giriş reddedilir |

Beş araştırma vakası ve taslak durumda bir deneme vakası. Vaka metinleri gerçek olay iddiası taşımayan pedagojik örneklerdir. Vaka sayısı/öğrenci sayısı sabit kodlanmış ürün sınırı değildir. Deneme vakasını önizleme/atama/aktivasyonla ayrıca deneyin; sürüm ve kapanış durumunu yönetimden değiştirin.

TEST-OGR-06 olayları **SENTETİK** olarak işaretlidir: AI bölümünü açma, heartbeat, arka plan, nihai bölüm, kopyalama, offline/online, kurtarma, çıkış. Zamanları seed anından türetilmiştir; gerçek öğrenci davranışı sayılmaz. AI ilk etiket önerisi Bilgi istedi; araştırmacı çoklu kararı Açıklama istedi + Alternatif fikir istedi. Geçmiş ayrı korunur.

Mock ayarı `config/local.php` → `ai.mode`:

| Mod | Davranış |
|---|---|
| normal | Açıkça işaretli deterministik metin; sentetik en alt rubrik puanı |
| delay | Her AI çağrısında 6 saniye gecikme; eşzamanlı autosave testi |
| partial | Kısmi işaretli yanıt; tam yanıt gibi sayılmaz |
| error | Başarısız istek; aynı mesaj için açık yeniden deneme mümkün |
| invalid_json | Analizde beklenen yapı yok; adım failed; puan yok |

Seed'i **normal** modda çalıştırın; sonra modu değiştirin. Mock üretimde çalışmaz. Hiçbir ilk parça zamanı uydurulmaz. Mock sonucu gerçek API doğrulaması değildir.

Yayın senaryosu ayrı disposable test DB'de: Sistem ve işlem geçmişi → Global kapanış (eksik fixtureları gerekçede belirt) → onaylı raporu ayrıca yayımla → TEST-OGR-06 ile indir. Kapanış sonrasında veri toplama yazmaları engellenir; ana manuel akış testinden önce kapatmayın.

Veri indirme ekranında varsayılan export test verisini dışlar. Seed verisini görmek için **Sentetik test verisini dahil et** seçin. Kodlanmış seçiliyken metin/kimlik/eşleme çıkarılır; kimlikli metin arşivi için bu seçimi bilinçli kapatın.
