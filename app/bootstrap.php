<?php
declare(strict_types=1);
date_default_timezone_set('UTC');
spl_autoload_register(function(string $name): void {
    $path = __DIR__ . '/' . $name . '.php';
    if (preg_match('/^[A-Za-z]+$/', $name) && is_file($path)) require $path;
});
if (is_file(__DIR__.'/../vendor/autoload.php')) require __DIR__.'/../vendor/autoload.php';
function cfg(): array {
    static $config;
    return $config ??= require (getenv('VAKALAB_CONFIG') ?: __DIR__.'/../config/local.php');
}
function uid(): string { return bin2hex(random_bytes(16)); }
function now(): string { return (new DateTimeImmutable('now',new DateTimeZone('UTC')))->format('Y-m-d\TH:i:s.u\Z'); }
function js(mixed $v): string { return json_encode($v, JSON_UNESCAPED_UNICODE|JSON_THROW_ON_ERROR); }
function obj(string $v): array { return json_decode($v, true, 128, JSON_THROW_ON_ERROR); }
function demand(bool $ok, string $message, int $code=422): void { if (!$ok) throw new Fault($message,$code); }
class Fault extends RuntimeException {}
