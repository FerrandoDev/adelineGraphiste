
@extends('layouts.app')
{{--  @php--}}
{{--      echo '<pre>';--}}
{{--      var_dump(get_fields($post->id));--}}
{{--      die();--}}
{{--  @endphp--}}
{{--    @include('layouts.flexibles')--}}
@section('content')
  @include('module.bandeau')
  @include('module.rebond')
  @include('module.projets')
  @include('module.entreprise')
  @include('module.form')


@endsection
