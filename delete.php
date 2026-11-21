<?php
declare(strict_types=1);
$name = basename((string)($_GET['file'] ?? ''));
$path = __DIR__ . '/uploads/' . $name;
if ($name === '' || $name === '.' || $name === '..' || !is_file($path)) {
    header('Location: index.php?error=' . urlencode('Файл не найден.')); exit;
}
if (!unlink($path)) { header('Location: index.php?error=' . urlencode('Не удалось удалить файл.')); exit; }
header('Location: index.php?msg=' . urlencode('Файл удалён.')); exit;
