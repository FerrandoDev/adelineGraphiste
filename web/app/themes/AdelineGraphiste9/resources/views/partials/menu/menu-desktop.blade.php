{{--@php--}}
{{--use App\Walker\CustomNavWalker;--}}
{{--@endphp--}}

{{--<div class="mega-menu">--}}
{{--  <a class="logo-header" href="{{ home_url('/') }}">--}}
{{--    <img src="@asset('images/logo/logo-bg-brown.png')" alt=" {{ get_bloginfo('name', 'display') }}"/>--}}
{{--  </a>--}}
{{--  <nav role="navigation" aria-label="" class="nav-primary">--}}
{{--    @if (has_nav_menu('primary_navigation'))--}}
{{--      {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'walker' => new CustomNavWalker()]) !!}--}}
{{--    @endif--}}
{{--  </nav>--}}
{{--  @include('components.rs')--}}
{{--</div>--}}

@php
  use App\Walker\CustomNavWalker;
      if (has_nav_menu('primary_navigation')) {
        $nav = ['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'walker' => new CustomNavWalker()];
      }
@endphp

<div class="mega-menu">
  <a class="logo-header" href="{{ home_url('/') }}">
    <img src="@asset('images/logo/logo-bg-brown.png')" alt=" {{ get_bloginfo('name', 'display') }}"/>
  </a>
  @if( isset($nav) )

    <div class="burger-menu">
      <div class="burger-click-region">
        <span class="burger-menu-piece top"></span>
        <span class="burger-menu-piece middle"></span>
        <span class="burger-menu-piece bottom"></span>
      </div>
    </div>


    <div class="sidebar">
      <nav role="navigation" aria-label="Navigation principale" class="nav-primary sidemenu">
        {!! wp_nav_menu($nav) !!}
      </nav>
    </div>
  @endif
  @include('components.rs')
</div>
