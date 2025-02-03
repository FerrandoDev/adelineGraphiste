<?php

namespace App\Features\Acf;

if (!defined('ABSPATH')) {
    exit;
}

$fields_path = dirname(get_theme_file_path())  . '/' . THEME_NAME . '/app/Features/Acf/FieldGroups/';

// Vérifier si le chemin est valide
if (!is_dir($fields_path)) {
    die('❌ Le chemin des FieldGroups est invalide : ' . $fields_path);
}

// Récupérer les fichiers FieldGroups généraux
$files = glob($fields_path . '/*.php') ?: [];

foreach ($files as $file) {
    require_once $file;

    $className = 'App\\Features\\Acf\\FieldGroups\\' . pathinfo($file, PATHINFO_FILENAME);

    if (class_exists($className) && method_exists($className, 'register')) {
        $className::register();
    }
}
