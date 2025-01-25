<?php

namespace App\Features\Acf\FieldGroups\Modules;

use Extended\ACF\Fields\Layout;
use Extended\ACF\Fields\Message;

class UpSellChildPage
{
    public static function getLayout(): array
    {
        return [
            'key' => 'layout_upsell_child_page',
            'name' => 'module-upsell-child-page',
            'label' => __('Bloc remontées des pages enfants'),
            'display' => 'block',
            'sub_fields' => [
                [
                    'key' => 'field_info_msg',
                    'label' => __('Information'),
                    'name' => 'info_msg',
                    'type' => 'message',
                    'message' => __('Ce bloc remonte les pages enfants s\'il y en a.'),
                ],
            ],
        ];
    }
}


//        return Layout::make(__('Bloc remontées des pages enfants'), 'module-upsell-child-page')
//            ->layout('block')
//            ->fields([
//            Message::make(__("Information"), 'info_msg')
//                ->message(__("Ce bloc remonte les pages enfants s'il y en a."))
//        ]);

