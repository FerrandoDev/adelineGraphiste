<?php

namespace App\Features\Taxonomy;

class YearProjectTaxo
{
    public function __construct()
    {
        add_action('init', [$this, 'register_taxonomy']);;
    }

    /**
     * Enregistre la Taxonomy "Année de projet" pour le CPT "projet".
     */
    public function register_taxonomy(): void
    {
        $labels = [
            'name' => __('Années de projet', 'sage'),
            'singular_name' => __('Année de projet', 'sage'),
            'search_items' => __('Rechercher des Années de projet', 'sage'),
            'all_items' => __('Toutes les années de projet', 'sage'),
            'parent_item' => __('Année de projet parente', 'sage'),
            'parent_item_colon' => __('Année de projet parente :', 'sage'),
            'edit_item' => __('Modifier l\'année du projet', 'sage'),
            'update_item' => __('Mettre à jour l\'année du projet', 'sage'),
            'add_new_item' => __('Ajouter une nouvelle année de projet', 'sage'),
            'new_item_name' => __('Nom de la nouvelle années de projet', 'sage'),
            'menu_name' => __('Année de projets', 'sage'),
        ];

        $args = [
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'year-project'],
        ];

        register_taxonomy('year-project', ['projet'], $args);
    }
}
