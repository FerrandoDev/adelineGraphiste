<?php

namespace App\Features\Acf\FieldGroups\Modules;

use App\Features\Acf\FieldGroups\Modules\ThreeImagesModule;
use App\Features\Acf\FieldGroups\Modules\CarouselModule;
use App\Features\Acf\FieldGroups\Modules\CitationModule;
use App\Features\Acf\FieldGroups\Modules\ImageVideoModule;
use App\Features\Acf\FieldGroups\Modules\IntroductionModule;
use App\Features\Acf\FieldGroups\Modules\MeaModule;
use App\Features\Acf\FieldGroups\Modules\UpSellChildPage;
use App\Features\Acf\FieldGroups\Modules\WysiwygModule;
use App\Features\Acf\FieldGroups\Modules\TextImageModule;

class _GlobalFlexible
{
//    public static function getLayouts(): array
//    {
//        return [
//
//                'key' => 'layout_3_images',
//                'name' => '3-images',
//                'label' => __('3 Images'),
//                'display' => 'block',
//                'sub_fields' => [
//                    [
//                        'key' => 'field_title',
//                        'label' => __('Titre'),
//                        'name' => 'title',
//                        'type' => 'text',
//                        'instructions' => __('Ajoutez un titre pour ce module'),
//                    ],
//                    [
//                        'key' => 'field_images',
//                        'label' => __('Images'),
//                        'name' => 'images',
//                        'type' => 'repeater',
//                        'sub_fields' => [
//                            [
//                                'key' => 'field_image',
//                                'label' => __('Image'),
//                                'name' => 'image',
//                                'type' => 'image',
//                                'return_format' => 'array',
//                            ],
//                            [
//                                'key' => 'field_caption',
//                                'label' => __('Légende'),
//                                'name' => 'caption',
//                                'type' => 'text',
//                            ],
//                        ],
//                        'min' => 3,
//                        'max' => 3,
//                    ],
//                ],
//        ];
//    }

    public static function getLayouts(): array
    {
        return [
            ThreeImagesModule::getLayout(),
            CarouselModule::getLayout(),
            CitationModule::getLayout(),
            ImageVideoModule::getLayout(),
            IntroductionModule::getLayout(),
            MeaModule::getLayout(),
            UpSellChildPage::getLayout(),
            WysiwygModule::getLayout(),
            TextImageModule::getLayout(),
        ];
    }
}
