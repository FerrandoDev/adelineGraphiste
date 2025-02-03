<?php

namespace App\Features\Acf\Options;

if (!defined('ABSPATH')) {
    exit; // Empêche l'accès direct
}

class WebsiteOptionsPage
{
    public static function register()
    {
        if (function_exists('acf_add_options_page')) {
            acf_add_options_page([
                'page_title'  => 'Options du site',
                'menu_title'  => 'Options du site',
                'menu_slug'   => 'acf-options-settings',
                'capability'  => 'edit_posts',
                'redirect'    => false,
                'position'    => 10, // Place le menu plus haut
                'icon_url'    => 'dashicons-admin-generic', // Icône WP
            ]);
        }
    }
}

// Enregistre la page d’options au moment du chargement d’ACF
add_action('acf/init', [WebsiteOptionsPage::class, 'register']);
