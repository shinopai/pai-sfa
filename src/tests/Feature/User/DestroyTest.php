<?php

use App\Models\User;

beforeEach(function () {
    /** @var \App\Models\User $this->admin */
    $this->admin = User::factory()->create([
        'role' => 'admin',
    ]);

    /** @var \App\Models\User $this->sales */
    $this->sales = User::factory()->create([
        'role' => 'sales',
    ]);

    /** @var \App\Models\User $this->user */
    $this->user = User::factory()->create([
        'role' => 'sales',
    ]);
});

it('管理者は他ユーザーを削除できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->delete(route('users.destroy', $this->user));

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseMissing('users', [
        'id' => $this->user->id,
    ]);
});

it('管理者は自分自身を削除できない', function () {
    $response = $this
        ->actingAs($this->admin)
        ->delete(route('users.destroy', $this->admin));

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('error');

    $this->assertDatabaseHas('users', [
        'id' => $this->admin->id,
    ]);
});

it('営業担当はユーザーを削除できない', function () {
    $response = $this
        ->actingAs($this->sales)
        ->delete(route('users.destroy', $this->user));

    $response->assertForbidden();

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
    ]);
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this
        ->delete(route('users.destroy', $this->user));

    $response->assertRedirect(route('login'));

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
    ]);
});
