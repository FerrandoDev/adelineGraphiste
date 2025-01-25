<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\TrueFalse;
use Extended\ACF\Fields\WysiwygEditor;

class WysiwygModule
{
    public static function getLayout(): array
    {
        return [
            'key' => 'layout_wysiwyg',
            'name' => 'block-wysiwyg',
            'label' => __('Bloc texte'),
            'display' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_background',
                    'label' => __('Arrière plan du bloc'),
                    'name' => 'background',
                    'type' => 'true_false',
                    'ui' => 1,
                    'ui_on_text' => __('Moutarde'),
                    'ui_off_text' => __('Blanc'),
                ],
                [
                    'key' => 'field_text',
                    'label' => __('Texte'),
                    'name' => 'text',
                    'type' => 'wysiwyg',
                    'media_upload' => false,
                    'required' => true,
                ],
            ],
        ];
    }
}

// Ancien code commenté pour référence
//
//namespace App\Features\Acf\FieldGroups\Modules;
//
//use Extended\ACF\Fields\Layout;
//use Extended\ACF\Fields\TrueFalse;
//use Extended\ACF\Fields\WysiwygEditor;
//
//class WysiwygModule
//{
//    public static function getLayout()
//    {
//        return Layout::make(__('Bloc texte'), 'block-wysiwyg')
//            ->layout('block')
//            ->fields([
//                TrueFalse::make(__('Arrière plan du bloc'), 'background')
//                    ->stylisedUi(__('Moutarde'), __('Blanc')),
//                WysiwygEditor::make(__('Texte'), 'text')
//                    ->mediaUpload(false)
//                    ->required(),
//            ]);
//    }
//}
