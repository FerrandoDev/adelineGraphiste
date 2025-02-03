<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;

class TextImageModule
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('Texte + Image'), 'text-image')
            ->fields([
                Text::make(__('Titre'), 'title')->required(),
                Textarea::make(__('Texte associé'), 'text')
                    ->rows(4)
                    ->required(),
                Image::make(__('Image associée'), 'image')->required(),
            ]);
    }
}
