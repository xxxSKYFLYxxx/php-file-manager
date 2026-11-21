<?php
declare(strict_types=1);
function back(string $key, string $text): never {
    header('Location: index.php?' . $key . '=' . urlencode($text)); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') back('error', 'Отправьте файл через форму.');
if (empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    back('error', 'Превышен post_max_size (' . ini_get('post_max_size') . ').');
}
$file = $_FILES['userfile'] ?? null;
if (!is_array($file)) back('error', 'Файл не выбран.');
$code = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
if ($code !== UPLOAD_ERR_OK) {
    $message = match ($code) {
        UPLOAD_ERR_INI_SIZE => 'Файл превышает upload_max_filesize (' . ini_get('upload_max_filesize') . ').',
        UPLOAD_ERR_FORM_SIZE => 'Файл превышает ограничение формы.',
        UPLOAD_ERR_PARTIAL => 'Файл загружен частично.',
        UPLOAD_ERR_NO_FILE => 'Файл не выбран.',
        UPLOAD_ERR_NO_TMP_DIR => 'На сервере отсутствует временная папка.',
        UPLOAD_ERR_CANT_WRITE => 'Не удалось записать файл на диск.',
        UPLOAD_ERR_EXTENSION => 'Загрузку остановило расширение PHP.',
        default => 'Неизвестная ошибка загрузки.'
    };
    back('error', $message);
}
if ((int)$file['size'] > 5 * 1024 * 1024) back('error', 'Размер файла не должен превышать 5 МБ.');
$original = basename((string)$file['name']);
$ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
$allowed = ['jpg','jpeg','png','gif','pdf','txt','docx','xlsx','zip'];
if (!in_array($ext, $allowed, true)) back('error', 'Недопустимое расширение. Разрешены: ' . implode(', ', $allowed) . '.');
$name = preg_replace('/[^\p{L}\p{N}._-]+/u', '_', $original) ?? '';
$name = trim($name, '._-');
if ($name === '' || $name === '.' || $name === '..') back('error', 'Некорректное имя файла.');
$dir = __DIR__ . '/uploads/';
if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) back('error', 'Не удалось создать uploads.');
$dest = $dir . $name;
if (file_exists($dest)) {
    $base = pathinfo($name, PATHINFO_FILENAME); $suffix = pathinfo($name, PATHINFO_EXTENSION); $stamp = date('Ymd_His');
    $name = $base . '_' . $stamp . '.' . $suffix; $dest = $dir . $name; $n = 1;
    while (file_exists($dest)) { $name = $base . '_' . $stamp . '_' . $n++ . '.' . $suffix; $dest = $dir . $name; }
}
if (!is_uploaded_file((string)$file['tmp_name']) || !move_uploaded_file((string)$file['tmp_name'], $dest)) {
    back('error', 'Не удалось сохранить файл.');
}
back('msg', 'Файл «' . $name . '» загружен.');
