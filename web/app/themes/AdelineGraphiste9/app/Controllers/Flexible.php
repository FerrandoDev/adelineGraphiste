<?php

namespace App\Controllers;


use App\Helpers\ArrayHelper;
use Sober\Controller\Controller;

use function App\container;
use App\Helpers\MediaHelper;
use App\Helpers\FileHelper;
use App\Helpers\TextHelper;

class Flexible extends Controller
{

    public static function videoImageModule($module)
    {

        $contenu = !$module['contenu-choice'] ? '--image' : '--video';

        if ($contenu === '--video') {
            $video_type = $module['video-choice'];

            if ($video_type) {
                $videoID = !empty($module['video']) ? MediaHelper::getYoutubeId($module['video']) : null;
                $module['videoURL'] = $videoID ? 'https://www.youtube.com/embed/' . $videoID : null;
                $module['name_video_type'] = 'youtube';
            } else {
                $videoID = !empty($module['video']) ? MediaHelper::getDailymotionId($module['video']) : null;
                $module['videoURL'] = $videoID ? 'https://geo.dailymotion.com/player.html?video=' . $videoID : null;
                $module['name_video_type'] = 'dailymotion';
            }

        }

        $module['alt_image'] = !empty($module['image']['alt']) ? $module['image']['alt'] : (!empty($module['title']) ? $module['title'] : '');
        $caption_image = !empty($module['image']['caption']) ? $module['image']['caption'] : '';
        $module['alt_image_video'] = is_array($module['video_image']) && !empty($module['video_image']['alt']) ? $module['video_image']['alt'] : (!empty($module['title']) ? $module['title'] : '');

        $module['caption'] = !empty($caption_image) ? $caption_image : (!empty($module['video_legend']) ? $module['video_legend'] : '');

        return $module;
    }


}
