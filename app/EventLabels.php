<?php
declare(strict_types=1);
// Export descriptions only: original event codes and measurements remain unchanged.
final class EventLabels {
    public const TYPES = [
        'section_open'=>['Bölüm açıldı','Gezinme','Bir çalışma bölümü açıldı; içeriğin okunduğunu göstermez.'],
        'field_focus'=>['Giriş alanına odaklanıldı','Alan etkileşimi','Bir metin veya seçim alanı odak aldı; veri değişmiş olmayabilir.'],
        'text_changed'=>['Yanıt metni değişti','Alan etkileşimi','Yanıt alanında metin düzenleme gözlendi.'],
        'profile_changed'=>['Profil veya güven seçimi değişti','Alan etkileşimi','Profil, nihai güven ya da nihai algılanan ciddiyet alanındaki seçim değişti.'],
        'question_changed'=>['Soru taslağı değişti','Alan etkileşimi','AI için yazılan soru taslağı değişti; mesajın gönderildiği anlamına gelmez.'],
        'copy'=>['Kopyalama gözlendi','Alan etkileşimi','Uygulama içinde kopyalama olayı gözlendi.'],
        'paste'=>['Yapıştırma gözlendi','Alan etkileşimi','Uygulama içinde yapıştırma olayı gözlendi.'],
        'scroll'=>['Sohbet kaydırıldı','Gezinme','Sohbet alanı kaydırıldı; mesajın okunduğunu göstermez.'],
        'visibility'=>['Sayfa görünürlüğü değişti','Teknik durum','Sayfanın görünür veya arka planda olma durumu değişti.'],
        'focus'=>['Pencere odağı değişti','Teknik durum','Pencere odak aldı veya kaybetti; dikkatin ölçümü değildir.'],
        'heartbeat'=>['Periyodik durum kaydı','Teknik durum','Yapılandırılan aralıkla alınan durum gözlemi; kullanıcı eylemi veya okuma kanıtı değildir.'],
        'offline'=>['Çevrimdışı durumu bildirildi','Teknik durum','Tarayıcı çevrimdışı durumu bildirdi.'],
        'online'=>['Çevrimiçi durumu bildirildi','Teknik durum','Tarayıcı çevrimiçi durumu bildirdi; sunucuya erişim garantisi değildir.'],
        'recovered'=>['Taslak kurtarıldı','Teknik durum','Aynı hesap ve çalışma için saklanan sekme taslağı kurtarıldı.'],
        'response_visible'=>['AI yanıtının gelişi kaydedildi','Yanıt durumu','Tam AI yanıtı geldi; ilgili bölüm ve sayfa görünürlüğü ayrıca değerlendirilir, okunma kanıtı değildir.'],
        'page_exit'=>['Sayfadan çıkış gözlendi','Teknik durum','Tarayıcının bildirebildiği sayfadan çıkış olayı; tüm çıkışların kaydı garanti edilmez.'],
        'section_leave'=>['Çalışma ekranından ayrıldı','Gezinme','Vakalarım ekranına dönüş veya çıkışla mevcut bölüm ziyareti kapandı.'],
        'workflow_confirmed'=>['Yanıtlar kesinleştirildi','Teslim durumu','Başlangıcın kesinleşmesi veya son teslimin sunucuda başarıyla kaydedilmesi doğrulandı.'],
        'message_send'=>['AI mesajı gönderimi başlatıldı','Mesaj durumu','AI isteğinin gönderilme girişimi kaydedildi; başarı ve soru amacı sunucu isteğiyle eşleştirilerek belirlenir.'],
        'app_presence'=>['Uygulama durumu değişti','Gezinme','Uygulamadan ayrılma veya uygulamaya dönüş kaydı.'],
    ];
    public const SECTIONS = ['case'=>'Vaka metni','profile'=>'Vaka profili','initial'=>'Başlangıç değerlendirmesi','ai'=>'AI sohbeti','final'=>'Nihai değerlendirme','reflection'=>'Yansıtma','confirm'=>'Son teslim','outside'=>'Vaka sekmesi etkin değil','away'=>'Vaka dışında'];
    public static function describe(array $event): array {
        [$name,$category,$description] = self::TYPES[$event['type']] ?? ['Tanımlanmamış olay','Tanımlanmamış','Özgün olay kodunu inceleyin.'];
        $p=(is_array($event['payload']??null)?$event['payload']:(json_decode($event['payload']??'{}',true)?:[]));
        if($event['type']==='app_presence'){$name=($p['app_active']??false)?'Vaka sekmesi yeniden etkin':'Vaka sekmesi etkinliğini kaybetti';$description=($p['app_active']??false)?'Bölüm süresi yeniden başladı.':'Sayfa gizli veya pencere odaksız; vaka sekmesi yeniden etkin olana kadar bölüm süresi durur.';}
        if($event['type']==='section_open'&&!empty($p['locked'])){$name=$event['section']==='ai'?'Kilitli AI bölümünü görüntüleme':'Kilitli bölüm görüntüleme';$description='İstenen bölüm kilitliydi; erişim denemesi kaydedildi.';}
        return $event + ['type_tr'=>$name,'section_tr'=>self::SECTIONS[$event['section']] ?? 'Tanımlanmamış bölüm','event_category'=>$category,'event_description'=>$description];
    }
    public static function dictionary(): array {
        $rows = [
            ['field'=>'events.app_id','meaning'=>'Süreç örneği: bir öğrencinin bir vaka çalışması. Aynı app_id olayları aynı süreçte birleştirir.'],
            ['field'=>'events.type / type_tr','meaning'=>'type değişmeyen olay kodu; type_tr Türkçe olay adı. Teknik durum kayıtları ayrı filtrelenebilir; tüm satırlar kullanıcı eylemi değildir.'],
            ['field'=>'events.section / section_tr','meaning'=>'section bölüm kodu; section_tr Türkçe bölüm adı.'],
            ['field'=>'events.event_category / event_description','meaning'=>'Olay grubu ve Türkçe açıklaması. Açıklamalar gözlem sınırlarını belirtir, düşünme/okuma çıkarımı yapmaz.'],
            ['field'=>'events.client_at / received_at / client_seq','meaning'=>'client_at istemci UTC zamanı, received_at sunucuya ulaşma UTC zamanı. Gecikmeli gönderim olabilir; client_seq aynı sekme/oturumda sıra kontrolüdür. İstemci saatinin doğruluğu garanti edilmez.'],
        ];
        foreach (self::TYPES as $code=>[$name,$category,$description]) $rows[] = ['field'=>'events.type.'.$code,'meaning'=>$name.' · '.$category.' · '.$description];
        foreach (self::SECTIONS as $code=>$name) $rows[] = ['field'=>'events.section.'.$code,'meaning'=>$name];
        return $rows;
    }
}
