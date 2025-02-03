<?php

namespace App\Features\Acf;

if (!defined('ABSPATH')) {
    exit;
}

$posttype_path = dirname(get_theme_file_path())  . '/' . THEME_NAME . '/app/Features/Acf/FieldGroups/PostType/';

// Vérifier si le chemin est valide
if (!is_dir($posttype_path)) {
    die('❌ Le chemin des PostTypes est invalide : ' . $posttype_path);
}

// Récupérer les fichiers FieldGroups pour PostTypes
$files = glob($posttype_path . '/*.php') ?: [];

foreach ($files as $file) {
    require_once $file;

    $className = 'App\\Features\\Acf\\FieldGroups\\PostType\\' . pathinfo($file, PATHINFO_FILENAME);

    if (class_exists($className) && method_exists($className, 'register')) {
        $className::register();
    }
}
