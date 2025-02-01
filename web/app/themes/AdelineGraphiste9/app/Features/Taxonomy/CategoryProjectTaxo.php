<?php

namespace App\Features\Taxonomy;

class CategoryProjectTaxo
{
    public function __construct()
    {
        add_action('init', [$this, 'register_taxonomy']);;
    }

    /**
     * Enregistre la Taxonomy "Catégorie de projet" pour le CPT "projet".
     */
    public function register_taxonomy(): void
    {
        $labels = [
            'name' => __('Catégories de projet', 'sage'),
            'singular_name' => __('Catégorie de projet', 'sage'),
            'search_items' => __('Rechercher des catégories de projet', 'sage'),
            'all_items' => __('Toutes les catégories de projet', 'sage'),
            'parent_item' => __('Catégorie de projet parente', 'sage'),
            'parent_item_colon' => __('Catégorie de projet parente :', 'sage'),
            'edit_item' => __('Modifier la catégorie de projet', 'sage'),
            'update_item' => __('Mettre à jour la catégorie de projet', 'sage'),
            'add_new_item' => __('Ajouter une nouvelle catégorie de projet', 'sage'),
            'new_item_name' => __('Nom de la nouvelle catégorie de projet', 'sage'),
            'menu_name' => __('Catégorie de projets', 'sage'),
        ];

        $args = [
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'cat-project'],
        ];

        register_taxonomy('cat-project', ['projet'], $args);
    }
}
