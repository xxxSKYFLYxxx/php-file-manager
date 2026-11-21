<?php
declare(strict_types=1);
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

function formatSize(int $bytes): string {
    $units = ['Б', 'КБ', 'МБ', 'ГБ']; $size = (float)$bytes; $i = 0;
    while ($size >= 1024 && $i < count($units)-1) { $size /= 1024; $i++; }
    return round($size, 1) . ' ' . $units[$i];
}
function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

$msg = (string)($_GET['msg'] ?? '');
$error = (string)($_GET['error'] ?? '');
$maxFile = ini_get('upload_max_filesize') ?: 'не задано';
$maxPost = ini_get('post_max_size') ?: 'не задано';
$files = []; $total = 0;
foreach (array_diff(scandir($uploadDir) ?: [], ['.', '..']) as $name) {
    if (is_file($uploadDir . $name)) { $files[] = $name; $total += (int)filesize($uploadDir . $name); }
}
sort($files, SORT_NATURAL | SORT_FLAG_CASE);
?>
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Файловый менеджер</title><link rel="stylesheet" href="style.css">
</head>
<body><main class="container">
<header><div class="eyebrow">PHP · ПРАКТИЧЕСКАЯ РАБОТА</div>
<h1>Файловый менеджер</h1><p class="muted">Загружайте, храните и управляйте файлами в одном месте.</p></header>
<?php if ($msg !== ''): ?><div class="alert success"><?=e($msg)?></div><?php endif; ?>
<?php if ($error !== ''): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
<section class="card">
<div class="heading"><h2>Загрузка файла</h2><a href="info.php">Настройки PHP ↗</a></div>
<p class="limits">Ограничения сервера: файл — <b><?=e($maxFile)?></b>, запрос — <b><?=e($maxPost)?></b></p>
<form action="upload.php" method="post" enctype="multipart/form-data" class="upload">
<input type="file" name="userfile" required><button type="submit">Загрузить файл →</button>
</form><p class="muted small">Разрешены JPG, PNG, GIF, PDF, TXT, DOCX, XLSX, ZIP. Максимум 5 МБ.</p>
</section>
<section class="card">
<div class="heading"><h2>Загруженные файлы</h2><span class="pill"><?=count($files)?> файлов</span></div>
<div class="table-wrap"><table><thead><tr><th>№</th><th>Имя файла</th><th>Размер</th><th>Дата загрузки</th><th>Действие</th></tr></thead><tbody>
<?php if (!$files): ?><tr><td colspan="5" class="empty">Файлов пока нет. Загрузите первый файл выше.</td></tr>
<?php else: foreach ($files as $i => $name): $path = $uploadDir . $name; ?>
<tr><td><?=$i+1?></td><td><a class="filename" href="uploads/<?=rawurlencode($name)?>" download><?=e($name)?></a></td>
<td><?=formatSize((int)filesize($path))?></td><td><?=date('d.m.Y H:i',(int)filemtime($path))?></td>
<td><a class="delete" href="delete.php?file=<?=rawurlencode($name)?>" onclick="return confirm('Удалить этот файл?')">Удалить</a></td></tr>
<?php endforeach; endif; ?></tbody></table></div>
<div class="summary"><span>Всего файлов: <b><?=count($files)?></b></span><span>Общий размер: <b><?=formatSize($total)?></b></span></div>
</section><footer>Учебный проект на PHP</footer></main></body></html>
