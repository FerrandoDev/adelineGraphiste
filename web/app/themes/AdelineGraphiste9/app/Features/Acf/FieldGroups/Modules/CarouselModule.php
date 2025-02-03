<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Layout;

class CarouselModule
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('Carousel'), 'carousel')
            ->fields([
                Text::make(__('Titre'), 'title')->required(),
                Repeater::make(__('Images du carousel'), 'carousel_images')
                    ->fields([
                        Image::make(__('Image'), 'image')->required(),
                        Text::make(__('Légende'), 'caption'),
                    ])
                    ->buttonLabel(__('Ajouter une image'))
                    ->min(2),
            ]);
    }
}
