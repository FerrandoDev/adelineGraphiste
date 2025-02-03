<?php

namespace App\Features\Acf\FieldGroups;

use App\Features\Acf\FieldGroups\Modules\_GlobalFlexible;
use Extended\ACF\Fields\FlexibleContent;
use Extended\ACF\Location;

class PageFieldGroup
{
    public static function register(): void
    {
        if (!function_exists('register_extended_field_group')) {
            return;
        }

        register_extended_field_group([
            'key' => 'page_group',
            'title' => __('Contenus'),
            'fields' => [
                FlexibleContent::make(__('Contenu flexible'), 'flexible')
                    ->layouts(_GlobalFlexible::getLayouts())
            ],
            'location' => [
                Location::where('post_type', 'page'),
            ],
            'style' => 'default',
            'position' => 'normal',
            'hide_on_screen' => ['the_content'],
        ]);
    }
}
