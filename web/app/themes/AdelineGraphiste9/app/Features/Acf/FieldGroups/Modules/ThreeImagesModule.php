<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Repeater;

class ThreeImagesModule
{
    public static function getLayout(): array
    {
        return [
            'key' => 'layout_3_images',
            'name' => '3-images',
            'label' => __('3 Images'),
            'display' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_title',
                    'label' => __('Titre'),
                    'name' => 'title',
                    'type' => 'text',
                    'instructions' => __('Ajoutez un titre pour ce module'),
                ],
                [
                    'key' => 'field_images',
                    'label' => __('Images'),
                    'name' => 'images',
                    'type' => 'repeater',
                    'sub_fields' => [
                        [
                            'key' => 'field_image',
                            'label' => __('Image'),
                            'name' => 'image',
                            'type' => 'image',
                            'return_format' => 'array',
                        ],
                        [
                            'key' => 'field_caption',
                            'label' => __('Légende'),
                            'name' => 'caption',
                            'type' => 'text',
                        ],
                    ],
                    'min' => 3,
                    'max' => 3,
                ],
            ],
        ];
    }
}

// Ancien code commenté
/*
return [
    'key' => 'layout_3_images',
    'name' => '3-images',
    'label' => __('3 Images'),
    ...
];
*/


//return [
//    'key' => 'layout_3_images',
//    'name' => '3-images',
//    'label' => __('3 Images'),
//    'display' => 'block',
//    'sub_fields' => [
//        [
//            'key' => 'field_title',
//            'label' => __('Titre'),
//            'name' => 'title',
//            'type' => 'text',
//            'instructions' => __('Ajoutez un titre pour ce module'),
//        ],
//        [
//            'key' => 'field_images',
//            'label' => __('Images'),
//            'name' => 'images',
//            'type' => 'repeater',
//            'sub_fields' => [
//                [
//                    'key' => 'field_image',
//                    'label' => __('Image'),
//                    'name' => 'image',
//                    'type' => 'image',
//                    'return_format' => 'array',
//                ],
//                [
//                    'key' => 'field_caption',
//                    'label' => __('Légende'),
//                    'name' => 'caption',
//                    'type' => 'text',
//                ],
//            ],
//            'min' => 3,
//            'max' => 3,
//        ],
//
//    ],
//];
