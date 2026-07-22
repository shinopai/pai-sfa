@extends('layouts.app')

@section('title', '商談編集')

@section('content')
  <div class="deals">

    <div class="deals__header u-flex">
      <h1 class="deals__title">商談編集</h1>
    </div>

    <div class="deals__form u-flex">
      @include('deals._form')
    </div>

  </div>
@endsection
