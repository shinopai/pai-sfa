@extends('layouts.app')

@section('title', '顧客登録')

@section('content')
  <div class="customers">

    <div class="customers__header u-flex">
      <h1 class="customers__title">顧客登録</h1>
    </div>

    <div class="customers__form u-flex">
      @include('customers._form')
    </div>

  </div>
@endsection
