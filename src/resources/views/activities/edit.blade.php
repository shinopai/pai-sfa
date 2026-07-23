@extends('layouts.app')

@section('title', '営業活動編集')

@section('content')
  <div class="activities">

    <div class="activities__header u-flex">
      <h1 class="activities__title">営業活動編集</h1>
    </div>

    <div class="activities__form u-flex">
      @include('activities._form')
    </div>

  </div>
@endsection
