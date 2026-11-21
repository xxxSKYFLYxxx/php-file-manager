<?php
declare(strict_types=1);
$keys = ['file_uploads','upload_max_filesize','post_max_size','max_file_uploads','memory_limit','max_input_time','max_execution_time'];
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
?>
<!doctype html><html lang="ru"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Настройки PHP</title><link rel="stylesheet" href="style.css"></head><body><main class="container">
<header><div class="eyebrow">PHP · КОНФИГУРАЦИЯ</div><h1>Настройки загрузки</h1><p class="muted">Текущие ограничения PHP для этого сервера.</p></header>
<section class="card"><div class="heading"><h2>Параметры php.ini</h2><a href="index.php">← К менеджеру</a></div>
<div class="table-wrap"><table><thead><tr><th>Директива</th><th>Текущее значение</th></tr></thead><tbody>
<?php foreach ($keys as $key): ?><tr><td><code><?=e($key)?></code></td><td><?=e((string)ini_get($key))?></td></tr><?php endforeach; ?>
</tbody></table></div><p class="muted small">Файл настроек: <code><?=e((string)(php_ini_loaded_file() ?: 'не найден'))?></code></p>
<p class="muted small">После изменения php.ini перезапустите сервер.</p></section></main></body></html>
