@php
use App\Controllers\App;
use App\Controllers\Flexible;

    $flexible = App::flexible();

@endphp
@if (is_array($flexible) && count($flexible) > 0)
    @foreach ($flexible as $key => $module)
        @if (isset($module['acf_fc_layout']))
            @switch($module['acf_fc_layout'])
                @case('block-carousel')
                    @include('modules.carousel', ['module' => $module])
                    @break
                @case('block-3-images')
                    @include('modules.3-images', ['module' => $module])
                    @break
                @case('block-intro')
                    @include('modules.intro', ['module' => $module])
                    @break
                @case('module-upsell-actu')
                    @include('modules.actualites-home', ['module' => $module])
                    @break
                @case('block-wysiwyg')
                    @include('modules.wysiwyg', ['module' => $module])
                    @break
                @case('block-text-image')
                    @include('modules.text-image', ['module' => $module])
                    @break
                @case('block-image-video')
                    @include('modules.image-video', ['module' => Flexible::videoImageModule($module)])
                    @break
                @case('module-citation')
                    @include('modules.citation', ['module' => $module])
                    @break
            @endswitch
        @endif
    @endforeach
@endif

