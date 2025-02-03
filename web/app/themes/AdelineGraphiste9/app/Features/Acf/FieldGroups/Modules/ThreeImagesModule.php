<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;

class ThreeImagesModule
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('3 Images'), 'three-images')
            ->fields([
                Text::make(__('Titre'), 'title')->required(),
                Repeater::make(__('Images'), 'images')
                    ->fields([
                        Image::make(__('Image'), 'image')->required(),
                        Text::make(__('Légende'), 'caption'),
                    ])
                    ->min(3)
                    ->max(3)
                    ->buttonLabel(__('Ajouter une image')),
            ]);
    }
}
