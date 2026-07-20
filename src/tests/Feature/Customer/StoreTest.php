<?php

use App\Models\Customer;
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

it('管理者は顧客登録画面を表示できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->get(route('customers.create'));

    $response
        ->assertOk()
        ->assertSee('顧客登録');
});

it('営業担当は顧客登録画面を表示できる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->get(route('customers.create'));

    $response
        ->assertOk()
        ->assertSee('顧客登録');
});

it('未ログインの場合はログイン画面へリダイレクトされる', function () {
    $response = $this->get(route('customers.create'));

    $response->assertRedirect(route('login'));
});

it('管理者は顧客を登録できる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->post(route('customers.store'), [
            'user_id' => $this->sales->id,
            'company_name' => '株式会社テスト',
            'contact_name' => '山田太郎',
            'email' => 'test@example.com',
            'phone' => '09012345678',
            'address' => '東京都新宿区',
            'industry' => 'IT',
            'memo' => 'テストメモ',
        ]);

    $response
        ->assertRedirect(route('customers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('customers', [
        'user_id' => $this->sales->id,
        'company_name' => '株式会社テスト',
        'contact_name' => '山田太郎',
        'email' => 'test@example.com',
    ]);
});

it('営業担当は顧客を登録すると担当営業がログインユーザーとなる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->post(route('customers.store'), [
            'user_id' => $this->sales->id,
            'company_name' => '株式会社テスト',
            'contact_name' => '山田太郎',
            'email' => 'test@example.com',
            'phone' => '09012345678',
            'address' => '東京都新宿区',
            'industry' => 'IT',
            'memo' => 'テストメモ',
        ]);

    $response
        ->assertRedirect(route('customers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('customers', [
        'user_id' => $this->sales->id,
        'company_name' => '株式会社テスト',
    ]);
});

it('営業担当がuser_idを改ざんして登録した場合は担当営業がログインユーザーとなる', function () {
    $response = $this
        ->actingAs($this->sales)
        ->post(route('customers.store'), [
            'user_id' => $this->admin->id,
            'company_name' => '株式会社テスト',
            'contact_name' => '山田太郎',
            'email' => 'test@example.com',
            'phone' => '09012345678',
            'address' => '東京都新宿区',
            'industry' => 'IT',
            'memo' => 'テストメモ',
        ]);

    $response
        ->assertRedirect(route('customers.index'))
        ->assertSessionHas('success');

    $this->assertDatabaseHas('customers', [
        'user_id' => $this->sales->id,
        'company_name' => '株式会社テスト',
    ]);

    $this->assertDatabaseMissing('customers', [
        'user_id' => $this->admin->id,
        'company_name' => '株式会社テスト',
    ]);
});

it('会社名未入力の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('customers.create'))
        ->post(route('customers.store'), [
            'user_id' => $this->sales->id,
            'company_name' => '',
            'contact_name' => '山田太郎',
        ]);

    $response
        ->assertRedirect(route('customers.create'))
        ->assertSessionHasErrors(['company_name']);
});

it('会社名が101文字以上の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('customers.create'))
        ->post(route('customers.store'), [
            'user_id' => $this->sales->id,
            'company_name' => str_repeat('あ', 101),
            'contact_name' => '山田太郎',
        ]);

    $response
        ->assertRedirect(route('customers.create'))
        ->assertSessionHasErrors(['company_name']);
});

it('担当者名未入力の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('customers.create'))
        ->post(route('customers.store'), [
            'user_id' => $this->sales->id,
            'company_name' => '株式会社テスト',
            'contact_name' => '',
        ]);

    $response
        ->assertRedirect(route('customers.create'))
        ->assertSessionHasErrors(['contact_name']);
});

it('担当者名が51文字以上の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('customers.create'))
        ->post(route('customers.store'), [
            'user_id' => $this->sales->id,
            'company_name' => '株式会社テスト',
            'contact_name' => str_repeat('あ', 51),
        ]);

    $response
        ->assertRedirect(route('customers.create'))
        ->assertSessionHasErrors(['contact_name']);
});

it('メールアドレスが不正な形式の場合はバリデーションエラーとなる', function () {
    $response = $this
        ->actingAs($this->admin)
        ->from(route('customers.create'))
        ->post(route('customers.store'), [
            'user_id' => $this->sales->id,
            'company_name' => '株式会社テスト',
            'contact_name' => '山田太郎',
            'email' => 'test',
        ]);

    $response
        ->assertRedirect(route('customers.create'))
        ->assertSessionHasErrors(['email']);
});
