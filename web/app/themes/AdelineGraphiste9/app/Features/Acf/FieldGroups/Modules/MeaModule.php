<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WysiwygEditor;

class MeaModule
{
    public static function getLayout(): array
    {
        return [
            'key' => 'layout_mea',
            'name' => 'module-mea',
            'label' => __('Bloc mise en avant'),
            'display' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_title',
                    'label' => __('Titre'),
                    'name' => 'title',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_desc',
                    'label' => __('Description'),
                    'name' => 'desc',
                    'type' => 'wysiwyg',
                    'media_upload' => false,
                ],
                [
                    'key' => 'field_image',
                    'label' => __('Image'),
                    'name' => 'image',
                    'type' => 'image',
                ],
                [
                    'key' => 'field_cta',
                    'label' => __('Lien vers la page'),
                    'name' => 'cta',
                    'type' => 'link',
                    'instructions' => __('Indiquez le lien de la page de redirection'),
                ],
            ],
        ];
    }
}

// Ancien code commenté pour référence
/*
namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WysiwygEditor;

class MeaModule
{
    public static function getLayout()
    {
        return Layout::make(__('Bloc mise en avant'), 'module-mea')
            ->layout('block')
            ->fields([
                Text::make(__('Titre'), 'title'),
                WysiwygEditor::make(__('Description'), 'desc')
                    ->mediaUpload(false),
                Image::make(__('Image'), 'image'),
                Link::make(__('Lien vers la page'), 'cta')
                    ->instructions("Indiquez le lien de la page de redirection"),
            ]);
    }
}
*/
