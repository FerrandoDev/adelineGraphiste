<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Gallery;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Text;

class CarouselModule
{
    public static function getLayout(): array
    {
        return [
            'key' => 'layout_carousel',
            'name' => 'block-carousel',
            'label' => __('Bloc carrousel'),
            'display' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_title',
                    'label' => __('Titre'),
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => __('Ajoutez un titre pour le carrousel'),
                ],
                [
                    'key' => 'field_slides',
                    'label' => __('Slides'),
                    'name' => 'slides',
                    'type' => 'gallery',
                    'instructions' => __('Ajoutez un minimum de 2 images pour le carrousel'),
                    'min' => 2,
                ],
            ],
        ];
    }
}


//
///**
//     * Retourne la configuration du layout pour le carrousel.
//     *
//     * @return Layout
//     */
//    public static function getLayout(): Layout
//    {
//        $fields = [
//            Text::make(__('Titre'), 'title')
//                ->instructions(__('Ajoutez un titre pour le carrousel'))
//                ->required(), // Rend le champ obligatoire
//            Gallery::make(__('Slides'), 'slides')
//                ->instructions(__('Ajoutez un minimum de 2 images pour le carrousel'))
//                ->min(2) // Définit un minimum de 2 images
//                ->required(), // Oblige l'utilisateur à ajouter des images
//        ];
//
//        return Layout::make(__('Bloc carrousel'), 'block-carousel')
//            ->layout('block')
//            ->fields($fields);
//    }
//}
