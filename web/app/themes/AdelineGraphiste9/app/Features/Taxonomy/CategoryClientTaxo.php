<?php

namespace App\Features\Taxonomy;

class CategoryClientTaxo
{
    public function __construct()
    {
        add_action('init', [$this, 'register_taxonomy']);;
    }

    /**
     * Enregistre la Taxonomy "Catégorie de client" pour le CPT "client".
     */
    public function register_taxonomy(): void
    {
        $labels = [
            'name' => __('Catégories de client', 'sage'),
            'singular_name' => __('Catégorie de client', 'sage'),
            'search_items' => __('Rechercher des catégories de client', 'sage'),
            'all_items' => __('Toutes les catégories de client', 'sage'),
            'parent_item' => __('Catégorie de client parente', 'sage'),
            'parent_item_colon' => __('Catégorie de client parente :', 'sage'),
            'edit_item' => __('Modifier la catégorie de client', 'sage'),
            'update_item' => __('Mettre à jour la catégorie de client', 'sage'),
            'add_new_item' => __('Ajouter une nouvelle catégorie de client', 'sage'),
            'new_item_name' => __('Nom de la nouvelle catégorie de client', 'sage'),
            'menu_name' => __('Catégorie de clients', 'sage'),
        ];

        $args = [
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'cat-client'],
        ];

        register_taxonomy('cat-client', ['client'], $args);
    }
}
