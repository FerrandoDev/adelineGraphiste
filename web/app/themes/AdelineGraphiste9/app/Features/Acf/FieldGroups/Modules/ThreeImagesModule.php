<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\ConditionalLogic;
use Extended\ACF\Fields\ButtonGroup;
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
                ButtonGroup::make(__('Placement de la plus grande image de départ'), 'placement')
                    ->choices(['Gauche', 'Droite', 'Haut'])
                    ->required(),
                Repeater::make(__('Images'), 'images')
                    ->fields([
                        Image::make(__('Grande image'), 'big-image')->required(),
                        Image::make(__('Image du haut'), 'image-top')
                            ->conditionalLogic([
                                ConditionalLogic::where('placement', '!=', 2),
                            ])
                            ->required(),
                        Image::make(__('Image du bas'), 'image-bottom')
                            ->conditionalLogic([
                                ConditionalLogic::where('placement', '!=', 2),
                            ])
                            ->required(),
                        Image::make(__('Image de gauche'), 'image-left')
                            ->conditionalLogic([
                                ConditionalLogic::where('placement', '==', 2),
                            ])
                            ->required(),
                        Image::make(__('Image de droite'), 'image-right')
                            ->conditionalLogic([
                                ConditionalLogic::where('placement', '==', 2),
                            ])
                            ->required(),

                    ])
                    ->buttonLabel(__('Ajouter un groupe d\'images')),
            ]);
    }
}
