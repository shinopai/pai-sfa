<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    /** @var \App\Models\User $this->admin */
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    /** @var \App\Models\User $this->sales */
    $this->sales = User::factory()->create([
        'role' => 'sales',
    ]);
});

it('管理者はユーザー一覧画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('users.index'));

    $response
        ->assertOk()
        ->assertSee('ユーザー管理');
});

it('営業担当はユーザー一覧画面へアクセスできない', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('users.index'));

    $response->assertForbidden();
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('users.index'));

    $response->assertRedirect(route('login'));
});
