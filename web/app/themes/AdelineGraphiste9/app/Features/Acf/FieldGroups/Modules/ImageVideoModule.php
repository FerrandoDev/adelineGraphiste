<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Url;
use Extended\ACF\Fields\Layout;

class ImageVideoModule
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('Image + Vidéo'), 'image-video')
            ->fields([
                Text::make(__('Titre'), 'title')->required(),
                Image::make(__('Image'), 'image')->required(),
                Url::make(__('Lien de la vidéo'), 'video_url')
                    ->instructions(__('Ajoutez l’URL de la vidéo (YouTube, Vimeo, etc.).')),
            ]);
    }
}
