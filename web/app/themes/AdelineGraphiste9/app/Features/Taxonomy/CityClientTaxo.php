<?php

namespace App\Features\Taxonomy;

class CityClientTaxo
{
    public function __construct()
    {
        add_action('init', [$this, 'register_taxonomy']);;
    }

    /**
     * Enregistre la Taxonomy "Ville" pour le CPT "client".
     */
    public function register_taxonomy(): void
    {
        $labels = [
            'name' => __('Ville', 'sage'),
            'singular_name' => __('Ville', 'sage'),
            'search_items' => __('Rechercher des villes', 'sage'),
            'all_items' => __('Toutes les villes', 'sage'),
            'parent_item' => __('Ville parente', 'sage'),
            'parent_item_colon' => __('Ville parente :', 'sage'),
            'edit_item' => __('Modifier la ville', 'sage'),
            'update_item' => __('Mettre à jour la ville', 'sage'),
            'add_new_item' => __('Ajouter une nouvelle ville', 'sage'),
            'new_item_name' => __('Nom de la nouvelle ville', 'sage'),
            'menu_name' => __('Villes', 'sage'),
        ];

        $args = [
            'labels' => $labels,
            'hierarchical' => true,
            'public' => true,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => ['slug' => 'ville'],
        ];

        register_taxonomy('city', ['client'], $args);
    }
}
