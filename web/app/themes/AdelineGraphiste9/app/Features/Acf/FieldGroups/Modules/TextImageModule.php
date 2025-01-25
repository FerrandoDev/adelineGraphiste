<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\ButtonGroup;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\WysiwygEditor;

class TextImageModule
{
    public static function getLayout(): array
    {
        return [
            'key' => 'layout_text_image',
            'name' => 'block-text-image',
            'label' => __('Bloc texte + image'),
            'display' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_title',
                    'label' => __('Titre'),
                    'name' => 'title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_text',
                    'label' => __('Texte'),
                    'name' => 'text',
                    'type' => 'wysiwyg',
                    'media_upload' => false,
                    'required' => true,
                ],
                [
                    'key' => 'field_text_image_position',
                    'label' => __("Position de l'image"),
                    'name' => 'text-image-position',
                    'type' => 'true_false',
                    'ui' => 1,
                    'ui_on_text' => __('À droite'),
                    'ui_off_text' => __('À gauche'),
                ],
                [
                    'key' => 'field_image_text_image_module',
                    'label' => __('Image'),
                    'name' => 'image',
                    'type' => 'image',
                    'return_format' => 'array',
                    'required' => true, // Corrige ici : utiliser un booléen, pas une chaîne
                ],
                [
                    'key' => 'field_link',
                    'label' => __('Lien'),
                    'name' => 'link',
                    'type' => 'link',
                ],
            ],
        ];
    }
}
//
//            $fields = [
//                TrueFalse::make(__("Position de l'image"), 'text-image-position')
//                    ->stylisedUi(__('À droite'), __('À gauche')),
//                ButtonGroup::make(__("Ratio de l'image"), 'ratio')
//                    ->choices([ __('image carrée : 500x500'), __('image horizontale : 500x350') , __('image verticale : 390x500') ]),
//                Image::make(__('Image'), 'image')
//                    ->required(),
//                Text::make(__('Titre'), 'title'),
//                WysiwygEditor::make(__('Texte'), 'text')
//                    ->mediaUpload(false)
//                    ->required(),
//                Link::make(__('Lien'), 'link'),
//            ];
//
//        return Layout::make(__('Bloc texte + image'), 'block-text-image')
//            ->layout('block')
//            ->fields($fields);


