<?php
declare(strict_types=1);
// Fatal PHP errors cannot be caught by the endpoint's Throwable handler.
final class ApiRuntime {
    private static bool $active=false;
    private static ?Db $db=null;
    private static ?string $stepId=null;
    public static function start(): void {
        self::$active=true;
        ini_set('display_errors','0');ini_set('log_errors','1');
        register_shutdown_function([self::class,'shutdown']);
    }
    public static function trackStep(Db $db,string $id): void {
        if(self::$active){self::$db=$db;self::$stepId=$id;}
    }
    public static function providerBudget(int $timeout): void {
        // Reset per provider call: one scoring request may make several calls.
        if(PHP_SAPI!=='cli'&&function_exists('set_time_limit'))set_time_limit(max(30,$timeout+15));
    }
    public static function shutdown(): void {
        $error=error_get_last();
        if(!$error||!in_array($error['type'],[E_ERROR,E_PARSE,E_CORE_ERROR,E_COMPILE_ERROR,E_USER_ERROR],true))return;
        $timeout=str_contains($error['message'],'Maximum execution time');
        try{
            if(self::$db&&self::$stepId){
                self::$db->q("UPDATE analysis_steps SET status='failed',error=? WHERE id=? AND status='running'",[$timeout?'execution_timeout':'request_interrupted',self::$stepId]);
            }
        }catch(Throwable){error_log('VakaLab interrupted analysis cleanup failed');}
        if(!headers_sent()){
            http_response_code(500);header('Content-Type: application/json; charset=utf-8');
            echo js(['error'=>$timeout?'Sunucu işlem süresi doldu. Analize devam et ile yeniden deneyebilirsiniz.':'Sunucu işlemi kesildi. Durumu yenileyip yeniden deneyebilirsiniz.']);
        }
    }
}
