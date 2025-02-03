<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\Layout;

class IntroductionModule
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('Introduction'), 'introduction')
            ->fields([
                Text::make(__('Titre'), 'title')->required(),
                Textarea::make(__('Texte d’introduction'), 'intro_text')
                    ->rows(4)
                    ->required(),
            ]);
    }
}
