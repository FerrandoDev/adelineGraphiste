<?php
// Sécurité : s'assurer que le fichier ne peut pas être chargé directement
if (!defined('ABSPATH')) {
    exit;
}

$fields_path = realpath(get_template_directory() . '/../app/Features/Acf/FieldGroups/');

if ($fields_path === false) {
    die('Le chemin spécifié est invalide : ' . get_template_directory() . '/../app/Features/Acf/FieldGroups/');
}

$files = glob($fields_path . '/*.php');

if (!$files) {
    die('Aucun fichier trouvé dans le répertoire : ' . $fields_path);
}
foreach ($files as $file) {
    require_once $file;

    // Récupérer le nom de la classe depuis le fichier inclus
    $className = 'App\\Features\\Acf\\FieldGroups\\' . basename($file, '.php');

    // Vérifier si la classe existe et appelle la méthode register()
    if (class_exists($className) && method_exists($className, 'register')) {
        $className::register();
    }
}
