<?php

namespace App\Features\Cpt;

class ClientPostType
{
    public function __construct()
    {
        add_action('init', [$this, 'register_post_type']);
    }

    /**
     * Enregistre le Custom Post Type "Client".
     */
    public function register_post_type(): void
    {
        $labels = [
            'name'               => __('Clients', 'sage'),
            'singular_name'      => __('Client', 'sage'),
            'menu_name'          => __('Clients', 'sage'),
            'name_admin_bar'     => __('Client', 'sage'),
            'add_new'            => __('Ajouter un client', 'sage'),
            'add_new_item'       => __('Ajouter un nouveau client', 'sage'),
            'new_item'           => __('Nouveau client', 'sage'),
            'edit_item'          => __('Modifier le client', 'sage'),
            'view_item'          => __('Voir le client', 'sage'),
            'all_items'          => __('Tous les clients', 'sage'),
            'search_items'       => __('Rechercher un client', 'sage'),
            'not_found'          => __('Aucun client trouvé', 'sage'),
            'not_found_in_trash' => __('Aucun client trouvé dans la corbeille', 'sage'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'clients'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-id',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'show_in_rest'       => false, // Active l'éditeur Gutenberg
        ];

        register_post_type('client', $args);
    }
}
