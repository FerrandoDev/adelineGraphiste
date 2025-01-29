<?php

namespace App\Walker;
use Walker_Nav_Menu;

class CustomNavWalker extends Walker_Nav_Menu {
    // Modifier uniquement les balises <a>
    public function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? [] : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));

        // Ajoute une classe personnalisée aux balises <a>
        $class_names = 'nav-links icon-underline ' . esc_attr($class_names);

        $output .= sprintf(
            '<li class="%s"><a href="%s" class="%s">%s</a>',
            esc_attr(join(' ', $classes)), // Classes <li>
            esc_url($item->url),          // URL
            $class_names,                 // Classes <a>
            apply_filters('the_title', $item->title, $item->ID) // Texte du lien
        );
    }
}
