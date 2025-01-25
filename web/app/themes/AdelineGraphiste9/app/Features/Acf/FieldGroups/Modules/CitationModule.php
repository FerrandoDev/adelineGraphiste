<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;

class CitationModule
{
    public static function getLayout(): array
    {
        return [
            'key' => 'layout_citation',
            'name' => 'module-citation',
            'label' => __('Bloc citation'),
            'display' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_text',
                    'label' => __('Texte Citation'),
                    'name' => 'text',
                    'type' => 'textarea',
                    'rows' => 3,
                ],
                [
                    'key' => 'field_image',
                    'label' => __('Image'),
                    'name' => 'image',
                    'type' => 'image',
                ],
                [
                    'key' => 'field_name',
                    'label' => __('Prénom'),
                    'name' => 'name',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_firstname',
                    'label' => __('Nom'),
                    'name' => 'firstname',
                    'type' => 'text',
                ],
                [
                    'key' => 'field_poste',
                    'label' => __('Nom du poste occupé'),
                    'name' => 'poste',
                    'type' => 'text',
                ],
            ],
        ];
    }
}

//
//        $fields = [
//            // Champ pour le texte de la citation
//            Textarea::make(__('Texte Citation'), 'text')
//                ->rows(3)
//                ->required()
//                ->instructions(__('Ajoutez le texte de la citation.')),
//
//            // Choix entre une personne de l'équipe ou non
//
//            // Image de la personne (affichée si "Personne de l'équipe" est Oui)
//            Image::make(__('Image'), 'image')
//                ->required()
//                ->instructions(__('Ajoutez une image pour la personne citée.')),
//
//            Text::make(__('Prénom'), 'name')
//
//                ->required()
//                ->instructions(__('Ajoutez le prénom de la personne citée.')),
//
//            Text::make(__('Nom'), 'firstname')
//                ->required()
//                ->instructions(__('Ajoutez le nom de la personne citée.')),
//
//            Text::make(__('Nom du poste occupé'), 'poste')
//                ->required()
//                ->instructions(__('Ajoutez le poste de la personne citée.')),
//
//
//        ];
//
//        return Layout::make(__('Bloc citation'), 'module-citation')
//            ->layout('block')
//            ->fields($fields);
//
