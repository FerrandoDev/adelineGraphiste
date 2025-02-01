<?php

namespace App\Features\Cpt;

class ProjetPostType
{
    public function __construct()
    {
        add_action('init', [$this, 'register_post_type']);
    }

    /**
     * Enregistre le Custom Post Type "Projet".
     */
    public function register_post_type(): void
    {
        $labels = [
            'name'               => __('Projets', 'sage'),
            'singular_name'      => __('Projet', 'sage'),
            'menu_name'          => __('Projets', 'sage'),
            'name_admin_bar'     => __('Projet', 'sage'),
            'add_new'            => __('Ajouter un projet', 'sage'),
            'add_new_item'       => __('Ajouter un nouveau projet', 'sage'),
            'new_item'           => __('Nouveau projet', 'sage'),
            'edit_item'          => __('Modifier le projet', 'sage'),
            'view_item'          => __('Voir le projet', 'sage'),
            'all_items'          => __('Tous les projets', 'sage'),
            'search_items'       => __('Rechercher un projet', 'sage'),
            'not_found'          => __('Aucun projet trouvé', 'sage'),
            'not_found_in_trash' => __('Aucun projet trouvé dans la corbeille', 'sage'),
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'projets'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 5,
            'menu_icon'          => 'dashicons-portfolio',
            'supports'           => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields'],
            'show_in_rest'       => false, // Active l'éditeur Gutenberg
        ];

        register_post_type('projet', $args);
    }
}
