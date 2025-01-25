<?php
namespace App\Features\Acf\FieldGroups\Modules;

class ImageVideoModule
{
    public static function getLayout(): array
    {
        return [
            'key' => 'layout_image_video',
            'name' => 'block-image-video',
            'label' => __('Bloc image ou vidéo '),
            'display' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_contenu_choice',
                    'label' => __("Choix du type de contenu"),
                    'name' => 'contenu-choice',
                    'type' => 'true_false',
                    'ui' => 1,
                    'ui_on_text' => __('Vidéo'),
                    'ui_off_text' => __('Image'),
                ],
                [
                    'key' => 'field_title',
                    'label' => __('Titre'),
                    'name' => 'title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_video_choice',
                    'label' => __("Hébergeur vidéo"),
                    'name' => 'video-choice',
                    'type' => 'true_false',
                    'ui' => 1,
                    'ui_on_text' => __('Youtube'),
                    'ui_off_text' => __('Dailymotion'),
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_contenu_choice',
                                'operator' => '==',
                                'value' => 1,
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_video',
                    'label' => __('Url de la vidéo'),
                    'name' => 'video',
                    'type' => 'url',
                    'instructions' => "exemple : <br> https://www.youtube.com/watch?v=xxxxxx <br> https://www.dailymotion.com/video/xxxxx",
                    'required' => 1,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_contenu_choice',
                                'operator' => '==',
                                'value' => 1,
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_video_image',
                    'label' => __('Image de couverture'),
                    'name' => 'video_image',
                    'type' => 'image',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_contenu_choice',
                                'operator' => '==',
                                'value' => 1,
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_image',
                    'label' => __('Image'),
                    'name' => 'image',
                    'type' => 'image',
                    'instructions' => __('taille de l\'image 872x480'),
                    'required' => 1,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_contenu_choice',
                                'operator' => '==',
                                'value' => 0,
                            ],
                        ],
                    ],
                ],
                [
                    'key' => 'field_video_legend',
                    'label' => __('Légende de la vidéo'),
                    'name' => 'video_legend',
                    'type' => 'text',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'field_contenu_choice',
                                'operator' => '==',
                                'value' => 1,
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}

// Ancien code commenté pour référence
/*
namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\Url;

class ImageVideoModule
{
    public static function getLayout()
    {
        return Layout::make(__('Bloc image ou vidéo '), 'block-image-video')
            ->layout('block')
            ->fields([
                TrueFalse::make(__("Choix du type de contenu"), 'contenu-choice')
                    ->stylisedUi(__('Vidéo'), __('Image')),
                Text::make(__('Titre'), 'title'),
                TrueFalse::make(__("Hébergeur vidéo"), 'video-choice')
                    ->stylisedUi(__('Youtube'), __('Dailymotion'))
                    ->conditionalLogic([
                        ConditionalLogic::where('contenu-choice', '==', 1)
                    ]),
                Url::make(__('Url de la vidéo'), 'video')
                    ->instructions("exemple : <br> https://www.youtube.com/watch?v=xxxxxx <br> https://www.dailymotion.com/video/xxxxx")
                    ->required()
                    ->conditionalLogic([
                        ConditionalLogic::where('contenu-choice', '==', 1)
                    ]),
                Image::make(__('Image de couverture'), 'video_image')
                    ->conditionalLogic([
                        ConditionalLogic::where('contenu-choice', '==', 1)
                    ]),
                Image::make(__('Image'), 'image')
                    ->required()
                    ->instructions('taille de l\'image 872x480')
                    ->conditionalLogic([
                        ConditionalLogic::where('contenu-choice', '==', 0)
                    ]),
                Text::make(__('Légende de la vidéo'), 'video_legend')
                    ->conditionalLogic([
                        ConditionalLogic::where('contenu-choice', '==', 1)
                    ]),
            ]);
    }
}
*/
