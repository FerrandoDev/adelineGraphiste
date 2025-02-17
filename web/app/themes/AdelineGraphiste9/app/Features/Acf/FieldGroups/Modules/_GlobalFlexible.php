<?php

namespace App\Features\Acf\FieldGroups\Modules;
class _GlobalFlexible
{
    public static function getLayouts(): array
    {
        return [
            ThreeImagesModule::getLayout(),
            TwoImagesModule::getLayout(),
            CarouselModule::getLayout(),
            CitationModule::getLayout(),
            ImageVideoModule::getLayout(),
            IntroductionModule::getLayout(),
            MeaModule::getLayout(),
            UpSellChildPage::getLayout(),
            WysiwygModule::getLayout(),
            TextImageModule::getLayout(),
        ];
    }
}
