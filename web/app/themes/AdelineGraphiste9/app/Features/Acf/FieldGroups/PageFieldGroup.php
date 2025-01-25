<?php
namespace App\Features\Acf\FieldGroups;

use App\Features\Acf\FieldGroups\Modules\_GlobalFlexible;
use Extended\ACF\Fields\FlexibleContent;

class PageFieldGroup
{
    public static function register(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

//        echo '<pre>';
//        var_dump((array)FlexibleContent::make(__('Contenu flexible'), 'flexible')
//            ->layouts(_GlobalFlexible::getLayouts()));
//        die();
        acf_add_local_field_group([
            'key' => 'page_group',
            'title' => __('Contenus'),
            'fields' => [
                (array)FlexibleContent::make(__('Contenu flexible'), 'flexible')
                    ->layouts(_GlobalFlexible::getLayouts())
            ],
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'page',
                    ],
                ],
            ],
            'style' => 'default',
            'position' => 'normal',
            'hide_on_screen' => ['the_content'],
        ]);
    }
}
