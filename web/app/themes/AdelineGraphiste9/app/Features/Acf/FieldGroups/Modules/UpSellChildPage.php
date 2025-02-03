<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;

class UpSellChildPage
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('UpSell Child Page'), 'upsell-child-page')
            ->fields([
                Text::make(__('Titre'), 'title')->required(),
                Text::make(__('Sous-titre'), 'subtitle'),
                Image::make(__('Image'), 'image')->required(),
                Link::make(__('Lien vers la page'), 'link')->required(),
            ]);
    }
}
