<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
        'name' => '更新前ユーザー',
        'email' => 'before@example.com',
        'role' => 'sales',
        'password' => bcrypt('password'),
    ]);
});

it('管理者はユーザー編集画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('users.edit', $this->user));

    $response
        ->assertOk()
        ->assertSee('ユーザー編集');
});

it('営業担当はユーザー編集画面へアクセスできない', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('users.edit', $this->user));

    $response->assertForbidden();
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('users.edit', $this->user));

    $response->assertRedirect(route('login'));
});

it('氏名を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('users.update', $this->user), [
            'name' => '更新後ユーザー',
            'email' => $this->user->email,
            'role' => $this->user->role,
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'name' => '更新後ユーザー',
    ]);
});

it('メールアドレスを変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('users.update', $this->user), [
            'name' => $this->user->name,
            'email' => 'after@example.com',
            'role' => $this->user->role,
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'email' => 'after@example.com',
    ]);
});

it('権限を変更して更新できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('users.update', $this->user), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'role' => 'admin',
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('users', [
        'id' => $this->user->id,
        'role' => 'admin',
    ]);
});

it('パスワード未入力の場合は変更されない', function () {
    $oldPassword = $this->user->password;

    $response = $this
        ->actingAs($this->admin)
        ->patch(route('users.update', $this->user), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'role' => $this->user->role,
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    $this->user->refresh();

    expect($this->user->password)->toBe($oldPassword);
});

it('パスワードを入力した場合は更新される', function () {
    $response = $this
        ->actingAs($this->admin)
        ->patch(route('users.update', $this->user), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'role' => $this->user->role,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

    $response
        ->assertRedirect(route('users.index'))
        ->assertSessionHas('success');

    $this->user->refresh();

    expect(Hash::check('new-password', $this->user->password))->toBeTrue();
});

it('他ユーザーと重複するメールアドレスへ変更した場合はバリデーションエラーとなる', function () {
    $otherUser = User::factory()->create([
        'email' => 'other@example.com',
    ]);

    $response = $this
        ->actingAs($this->admin)
        ->from(route('users.edit', $this->user))
        ->patch(route('users.update', $this->user), [
            'name' => $this->user->name,
            'email' => $otherUser->email,
            'role' => $this->user->role,
            'password' => '',
            'password_confirmation' => '',
        ]);

    $response
        ->assertRedirect(route('users.edit', $this->user))
        ->assertSessionHasErrors(['email']);
});

it('パスワード確認が一致しない場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('users.edit', $this->user))
        ->patch(route('users.update', $this->user), [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'role' => $this->user->role,
            'password' => 'new-password',
            'password_confirmation' => 'different-password',
        ]);

    $response
        ->assertRedirect(route('users.edit', $this->user))
        ->assertSessionHasErrors(['password']);
});
