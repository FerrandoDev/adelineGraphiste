@php
use App\Walker\CustomNavWalker;
@endphp

<div class="mega-menu">
  <a class="logo-header" href="{{ home_url('/') }}">
    <img src="@asset('images/logo/logo-bg-brown.png')" alt=" {{ get_bloginfo('name', 'display') }}"/>
  </a>
  <nav class="nav-primary">
    @if (has_nav_menu('primary_navigation'))
      {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'walker' => new CustomNavWalker()]) !!}
    @endif
  </nav>
  @include('components.rs')
</div>
