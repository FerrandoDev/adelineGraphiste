<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Textarea;
class IntroductionModule
{
    public static function getLayout(): array
    {
        return [
            'key' => 'layout_introduction',
            'name' => 'block-intro',
            'label' => __('Bloc introduction'),
            'display' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_text',
                    'label' => __('Texte'),
                    'name' => 'text',
                    'type' => 'textarea',
                    'required' => 1,
                    'rows' => 4,
                ],
            ],
        ];
    }
}

// Ancien code commenté pour référence
/*
namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Textarea;

class IntroductionModule
{
    public static function getLayout()
    {
        return Layout::make(__('Bloc introduction'), 'block-intro')
            ->layout('block')
            ->fields([
                Textarea::make(__('Texte'), 'text')
                    ->required()
                    ->rows(4),
            ]);
    }
}
*/

