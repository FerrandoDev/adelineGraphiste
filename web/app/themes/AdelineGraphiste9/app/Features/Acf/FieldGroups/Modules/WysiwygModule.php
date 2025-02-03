<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WysiwygEditor;
use Extended\ACF\Fields\Layout;

class WysiwygModule
{
    public static function getLayout(): Layout
    {
        return Layout::make(__('Éditeur WYSIWYG'), 'wysiwyg')
            ->fields([
                Text::make(__('Titre'), 'title')->required(),
                WysiwygEditor::make(__('Contenu'), 'content')
                    ->mediaUpload(true)
                    ->toolbar('full')
                    ->required(),
            ]);
    }
}
