<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;

class MeaModule
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('Mise en avant'), 'mea')
            ->fields([
                Text::make(__('Titre'), 'title')->required(),
                Textarea::make(__('Description'), 'description')
                    ->rows(4)
                    ->required(),
                Image::make(__('Image de mise en avant'), 'featured_image')->required(),
            ]);
    }
}
