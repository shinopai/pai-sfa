@extends('layouts.app')

@section('title', 'ユーザー登録')

@section('content')
  <div class="users">

    <div class="users__header u-flex">
      <h1 class="users__title">ユーザー登録</h1>
    </div>

    <div class="users__form u-flex">
      @include('users._form')
    </div>

  </div>
@endsection
