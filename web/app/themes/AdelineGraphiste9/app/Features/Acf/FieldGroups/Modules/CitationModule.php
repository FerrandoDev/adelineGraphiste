<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Fields\Layout;

class CitationModule
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('Citation'), 'citation')
            ->fields([
                Text::make(__('Auteur'), 'author')->required(),
                Textarea::make(__('Citation'), 'quote')
                    ->rows(3)
                    ->required(),
            ]);
    }
}
