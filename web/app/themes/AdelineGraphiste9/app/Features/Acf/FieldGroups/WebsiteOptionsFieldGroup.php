<?php

namespace App\Features\Acf\FieldGroups;

use App\Features\Cpt\ClientPostType;
use Extended\ACF\Fields\File;
use Extended\ACF\Fields\Group;
use Extended\ACF\Fields\Image;
use Extended\ACF\Fields\Link;
use Extended\ACF\Fields\Repeater;
use Extended\ACF\Fields\Select;
use Extended\ACF\Fields\Tab;
use Extended\ACF\Fields\Text;
use Extended\ACF\Fields\Textarea;
use Extended\ACF\Location;

class WebsiteOptionsFieldGroup
{
    public static function register()
    {
        if (!function_exists('register_extended_field_group')) {
            return;
        }

        if (function_exists('acf_add_options_page')) {
            acf_add_options_page([
                'page_title'  => 'Options du site',
                'menu_title'  => 'Options du site',
                'menu_slug'   => 'acf-options-settings',
                'capability'  => 'edit_posts',
                'redirect'    => false
            ]);
        }


        register_extended_field_group([
            'key' => 'group_website_options',
            'title' => __('Options du site'),
            'style' => 'default',
            'hide_on_screen' => ['the_content'],
            'location' => [
                Location::where('options_page', '==', 'acf-options-settings'),
            ],
            'fields' => self::getFields(),
        ]);
    }

    private static function getFields(): array
    {
        return [
            // Logos Tab
            Tab::make(__('Logos'), 'logos-tab')->placement('left'),
            File::make(__('Logo header'), 'header-logo')
                ->mimeTypes(['svg'])
                ->required(),

            // Quick Access Tab
            Tab::make(__('Accès rapide'), 'quick_access_tab')->placement('left'),
            Repeater::make(__('Liste des accès rapide'), 'quick_access_list')
                ->buttonLabel(__('Ajouter un lien'))
                ->layout('block')
                ->max(4)
                ->fields([
                    Select::make(__('Icône'), 'icon')
                        ->choices([
                            'icon-1' => __('Icône 1'),
                            'icon-2' => __('Icône 2'),
                        ])
                        ->required(),
                    Link::make(__('Lien'), 'link')->required(),
                ]),

            // Sticky Menu Tab
            Tab::make(__('Menu sticky'), 'sticky-menu-tab')->placement('left'),
            Link::make(__('Lien sticky'), 'sticky-menu-link'),

            // Footer Tab
            Tab::make(__('Footer'), 'footer-tab')->placement('left'),
            Group::make(__('Bloc nous contacter'), 'contact-block')
                ->fields([
                    Text::make(__('Titre'), 'title')->required(),
                    Text::make(__('Numéro de téléphone'), 'phone'),
                    Link::make(__('Lien vers la page du formulaire de contact'), 'contact-link'),
                ]),
            Group::make(__('Bloc siège'), 'siege-block')
                ->fields([
                    Text::make(__('Titre'), 'title')->required(),
                    Textarea::make(__('Adresse'), 'address')->newLines('br'),
                ]),
            Group::make(__('Bloc réseaux sociaux'), 'socials-block')
                ->fields([
                    Repeater::make(__('Réseaux sociaux'), 'socials')
                        ->fields([
                            Select::make(__('Réseau social'), 'social-network')
                                ->choices([
                                    'youtube' => __('Youtube'),
                                    'linkedin' => __('Linkedin'),
                                    'facebook' => __('Facebook'),
                                ]),
                            Link::make(__('Lien'), 'link')->required(),
                        ]),
                ]),

            // 404 Page Tab
            Tab::make(__('Page 404'), '404-tab')->placement('left'),
            Text::make(__('Texte'), '404-text'),

            // Images Tab
            Tab::make(__('Images'), 'images-tab')->placement('left'),
            Image::make(__('Image de remplacement'), 'replacement_image'),
        ];
    }
}
