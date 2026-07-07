<?php

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

// if (!function_exists('echo_content')) {
// 	function echo_content($content) {
// 		// Step 1: Decode HTML entities (including &nbsp;)
// 		$content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// 		// Step 3: Optionally clean up multiple spaces
// 		$content = preg_replace('/\s+/', ' ', $content);
// 		$content = trim($content);
// 		echo $content;
// 	}
// }

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

const ROOT_DIR = __DIR__;
const DS = DIRECTORY_SEPARATOR;
defined('DIR_ROOT') || define('DIR_ROOT', __DIR__ . DS);
defined('DIR_CORE') || define('DIR_CORE', ROOT_DIR . DS . join(DS, ['src', 'Core']) . DS);
defined('DIR_PLUGINS') || define('DIR_PLUGINS', ROOT_DIR . DS . join(DS, ['src', 'Plugins']) . DS);

$storage_dir = DIR_ROOT . 'storage' . DS;

if (! is_dir($storage_dir)) {
    @mkdir($storage_dir);
    @mkdir($storage_dir . 'compiled-templates' . DS);
    @mkdir($storage_dir . 'cache');
    @mkdir($storage_dir . 'model');
    @mkdir($storage_dir . join(DS, ['model', 'admin']) . DS);
    @mkdir($storage_dir . join(DS, ['model/app']) . DS);
    @mkdir($storage_dir . join(DS, ['model/install']) . DS);
}

if (is_writable($storage_dir)) {
    define('DIR_STORAGE', $storage_dir);
}


// Define compiled templates directory constant when storage dir is available
// if (defined('DIR_STORAGE')) {
//     defined('DIR_COMPILED_TEMPLATES') || define('DIR_COMPILED_TEMPLATES', DIR_STORAGE . 'compiled-templates' . DS);
// }
// else {
//     // Fallback path if DIR_STORAGE wasn't defined (ensure compiled templates path exists)
//     defined('DIR_COMPILED_TEMPLATES') || define('DIR_COMPILED_TEMPLATES', ROOT_DIR . DS . 'storage' . DS . 'compiled-templates' . DS);
// }