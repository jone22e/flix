<?
function AutoLoaderFlix($class)
{
    $prefix = 'Flix\\';
    $base_dir = __DIR__ . "/";
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relative_class = substr($class, $len);
    $relative_class = str_replace('\\', '/', $relative_class) . '.php';
    $relative_class = substr_replace($relative_class, '/src', strpos($relative_class, '/'), 0);
    $basename = basename($base_dir . $relative_class);
    $file = str_replace($basename, "", $base_dir . $relative_class);
    $file = strtolower($file);
    $file = $file . $basename;
    // Verifica se o arquivo existe, se existir então inclui ele
    if (is_file($file)) {
        include_once $file;
    }
}

spl_autoload_register('AutoLoaderFlix');