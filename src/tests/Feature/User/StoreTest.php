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
});

it('管理者はユーザー登録画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('users.create'));

    $response
        ->assertOk()
        ->assertSee('ユーザー登録');
});

it('営業担当はユーザー登録画面へアクセスできない', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('users.create'));

    $response->assertForbidden();
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('users.create'));

    $response->assertRedirect(route('login'));
});

it('管理者はユーザーを登録できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->post(route('users.store'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'role' => 'sales',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'role' => 'sales',
    ]);
});

it('氏名未入力の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('users.create'))
        ->post(route('users.store'), [
            'name' => '',
            'email' => 'test@example.com',
            'role' => 'sales',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response
        ->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['name']);

    $this->assertDatabaseMissing('users', [
        'email' => 'test@example.com',
    ]);
});

it('氏名が51文字以上の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('users.create'))
        ->post(route('users.store'), [
            'name' => str_repeat('あ', 51),
            'email' => 'test@example.com',
            'role' => 'sales',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response
        ->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['name']);
});

it('メールアドレス未入力の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('users.create'))
        ->post(route('users.store'), [
            'name' => 'テストユーザー',
            'email' => '',
            'role' => 'sales',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response
        ->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['email']);
});

it('メールアドレスが不正な形式の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('users.create'))
        ->post(route('users.store'), [
            'name' => 'テストユーザー',
            'email' => 'test',
            'role' => 'sales',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response
        ->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['email']);
});

it('メールアドレスが重複している場合はバリデーションエラーとなる', function () {
    User::factory()->create([
        'email' => 'test@example.com',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('users.create'))
        ->post(route('users.store'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'role' => 'sales',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response
        ->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['email']);
});

it('権限未選択の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('users.create'))
        ->post(route('users.store'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'role' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

    $response
        ->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['role']);
});

it('パスワード確認が一致しない場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('users.create'))
        ->post(route('users.store'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'role' => 'sales',
            'password' => 'password',
            'password_confirmation' => 'different-password',
        ]);

    $response
        ->assertRedirect(route('users.create'))
        ->assertSessionHasErrors(['password']);
});
