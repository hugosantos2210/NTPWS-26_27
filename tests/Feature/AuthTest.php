<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\News;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private Role $roleAdmin;
    private Role $roleEditor;
    private Role $roleUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->roleAdmin  = Role::create(['name' => 'admin',  'description' => 'Administrator']);
        $this->roleEditor = Role::create(['name' => 'editor', 'description' => 'Editor']);
        $this->roleUser   = Role::create(['name' => 'user',   'description' => 'User']);
    }

    private function makeUser(string $role = 'user', bool $active = true): User
    {
        $roleId = match ($role) {
            'admin'  => $this->roleAdmin->id,
            'editor' => $this->roleEditor->id,
            default  => $this->roleUser->id,
        };

        return User::create([
            'name'      => 'Test User',
            'email'     => uniqid() . '@test.com',
            'password'  => bcrypt('password123'),
            'role_id'   => $roleId,
            'is_active' => $active,
        ]);
    }

    // -------------------------------------------------------------------------
    // Registration tests
    // -------------------------------------------------------------------------

    /** @test */
    public function test_registration_with_valid_data(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'New User',
            'email'                 => 'newuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'newuser@example.com',
        ]);

        // The newly registered user should have the 'user' role
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('user', $user->role->name);
    }

    /** @test */
    public function test_registration_with_duplicate_email(): void
    {
        // Create a user first
        $this->makeUser('user');
        $existingUser = User::first();

        $response = $this->post('/register', [
            'name'                  => 'Another User',
            'email'                 => $existingUser->email,
            'password'              => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // Should redirect back with errors (422 for validation or redirect with errors)
        $response->assertStatus(302);
        $response->assertSessionHasErrors('email');
    }

    /** @test */
    public function test_registration_with_invalid_password_confirmation(): void
    {
        $response = $this->post('/register', [
            'name'                  => 'New User',
            'email'                 => 'newuser@example.com',
            'password'              => 'password123',
            'password_confirmation' => 'differentpassword',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['email' => 'newuser@example.com']);
    }

    // -------------------------------------------------------------------------
    // Login tests
    // -------------------------------------------------------------------------

    /** @test */
    public function test_login_with_valid_credentials(): void
    {
        $user = $this->makeUser('user');
        $email = $user->email;

        $response = $this->post('/login', [
            'email'    => $email,
            'password' => 'password123',
        ]);

        $response->assertRedirect();
        $this->assertAuthenticated();
    }

    /** @test */
    public function test_login_with_invalid_credentials(): void
    {
        $this->makeUser('user');
        $user = User::first();

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    /** @test */
    public function test_login_with_inactive_account(): void
    {
        $user = $this->makeUser('user', false); // inactive

        $response = $this->post('/login', [
            'email'    => $user->email,
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // -------------------------------------------------------------------------
    // Authorization tests
    // -------------------------------------------------------------------------

    /** @test */
    public function test_admin_panel_redirects_unauthenticated(): void
    {
        $response = $this->get('/admin');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_regular_user_cannot_access_admin(): void
    {
        $user = $this->makeUser('user');

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }

    /** @test */
    public function test_editor_can_access_admin_news(): void
    {
        $editor = $this->makeUser('editor');

        $response = $this->actingAs($editor)->get('/admin/news');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_editor_cannot_edit_another_editors_news(): void
    {
        // Create category required for News
        $category = Category::create([
            'name'        => 'Test Category',
            'slug'        => 'test-category',
            'description' => 'A test category',
        ]);

        $editor1 = $this->makeUser('editor');
        $editor2 = $this->makeUser('editor');

        // Create a news article owned by editor2
        $news = News::create([
            'title'      => 'Editor 2 Article',
            'slug'       => 'editor-2-article',
            'summary'    => 'Summary of editor 2 article.',
            'content'    => '<p>Content by editor 2.</p>',
            'category_id'=> $category->id,
            'author_id'  => $editor2->id,
            'status'     => 'draft',
        ]);

        // Editor1 tries to edit editor2's article
        $response = $this->actingAs($editor1)->get('/admin/news/' . $news->id . '/edit');
        $response->assertStatus(403);
    }
}
