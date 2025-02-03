<?php

namespace App\Features\Acf;

if (!defined('ABSPATH')) {
    exit;
}

$taxonomy_path = dirname(get_theme_file_path()) . '/' . THEME_NAME . '/app/Features/Acf/FieldGroups/Taxonomy/';

// Vérifier si le chemin est valide
if (!is_dir($taxonomy_path)) {
    die('❌ Le chemin des Taxonomies est invalide : ' . $taxonomy_path);
}

// Récupérer les fichiers FieldGroups pour Taxonomies
$files = glob($taxonomy_path . '/*.php') ?: [];

foreach ($files as $file) {
    require_once $file;

    $className = 'App\\Features\\Acf\\FieldGroups\\Taxonomy\\' . pathinfo($file, PATHINFO_FILENAME);

    if (class_exists($className) && method_exists($className, 'register')) {
        $className::register();
    }
}
