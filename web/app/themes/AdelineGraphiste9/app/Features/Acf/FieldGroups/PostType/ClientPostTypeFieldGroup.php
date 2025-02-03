<?php

namespace App\Features\Acf\FieldGroups\PostType;

use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\WysiwygEditor;
use Extended\ACF\Location;

if (!defined('ABSPATH')) {
    exit;
}

class ClientPostTypeFieldGroup
{
    public static function register()
    {
        if (!function_exists('register_extended_field_group')) {
            return;
        }

        register_extended_field_group([
            'key'    => 'group_client_info',
            'title'  => __('Informations du client'),
            'fields' => self::getFields(),
            'location' => [
                Location::where('post_type', 'client'),
            ],
            'position' => 'normal',
            'priority' => 'high',
        ]);
    }

    private static function getFields(): array
    {
        return [
            Text::make(__('Nom du client'), 'client_name')
                ->instructions(__('Saisissez le nom du client.'))
                ->required(),

            WysiwygEditor::make(__('Présentation du client'), 'client_presentation')
                ->instructions(__('Ajoutez une description détaillée du client.'))
                ->mediaUpload(true)
                ->tabs('all')
                ->toolbar('full'),
        ];
    }
}
