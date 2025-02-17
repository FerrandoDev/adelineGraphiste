<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\ButtonGroup;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;

class TwoImagesModule
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('2 Images'), 'two-images')
            ->fields([
                ButtonGroup::make(__('Placement de la plus grande image de départ'), 'placement')
                    ->choices(['Gauche', 'Droite']),
                Repeater::make(__('Images'), 'images')
                    ->fields([
                        Image::make(__('Image de gauche'), 'image-left')
                            ->required(),
                        Image::make(__('Image de droite'), 'image-right')
                            ->required(),
                    ])
                    ->buttonLabel(__('Ajouter un groupe d\'images')),
            ]);
    }
}
