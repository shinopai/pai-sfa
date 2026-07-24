@extends('layouts.app')

@section('title', 'タスク編集')

@section('content')
  <div class="tasks">

    <div class="tasks__header u-flex">
      <h1 class="tasks__title">タスク編集</h1>
    </div>

    <div class="tasks__form u-flex">
      @include('tasks._form')
    </div>

  </div>
@endsection
