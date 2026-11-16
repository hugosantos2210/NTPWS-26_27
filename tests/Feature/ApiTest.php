<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\News;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    private Role $roleAdmin;
    private Role $roleEditor;
    private Role $roleUser;
    private Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->roleAdmin  = Role::create(['name' => 'admin',  'description' => 'Administrator']);
        $this->roleEditor = Role::create(['name' => 'editor', 'description' => 'Editor']);
        $this->roleUser   = Role::create(['name' => 'user',   'description' => 'User']);

        $this->category = Category::create([
            'name'        => 'General',
            'slug'        => 'general',
            'description' => 'General category for tests',
        ]);
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

    private function makeNews(User $author, string $status = 'draft', string $suffix = ''): News
    {
        $title = 'Test Article ' . ($suffix ?: uniqid());
        return News::create([
            'title'       => $title,
            'slug'        => \Illuminate\Support\Str::slug($title) . '-' . uniqid(),
            'summary'     => 'Summary for ' . $title,
            'content'     => '<p>Content for ' . $title . '</p>',
            'category_id' => $this->category->id,
            'author_id'   => $author->id,
            'status'      => $status,
            'published_at'=> $status === 'published' ? now() : null,
        ]);
    }

    // -------------------------------------------------------------------------
    // Public API tests
    // -------------------------------------------------------------------------

    /** @test */
    public function test_api_news_list_returns_only_published(): void
    {
        $admin = $this->makeUser('admin');

        $draft     = $this->makeNews($admin, 'draft',     'Draft');
        $published = $this->makeNews($admin, 'published', 'Published');

        $response = $this->getJson('/api/v1/news');
        $response->assertStatus(200);

        $ids = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertContains($published->id, $ids);
        $this->assertNotContains($draft->id, $ids);
    }

    /** @test */
    public function test_api_news_show_returns_404_for_draft(): void
    {
        $admin = $this->makeUser('admin');
        $draft = $this->makeNews($admin, 'draft');

        $response = $this->getJson('/api/v1/news/' . $draft->id);
        $response->assertStatus(404);
    }

    /** @test */
    public function test_api_create_news_requires_authentication(): void
    {
        $response = $this->postJson('/api/v1/news', [
            'title'       => 'Unauthorized Article',
            'summary'     => 'Summary here.',
            'content'     => '<p>Content here.</p>',
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function test_api_create_news_as_editor(): void
    {
        $editor = $this->makeUser('editor');

        $response = $this->actingAs($editor)->postJson('/api/v1/news', [
            'title'       => 'Editor Created Article',
            'summary'     => 'Summary of the article.',
            'content'     => '<p>Full content goes here.</p>',
            'category_id' => $this->category->id,
            'status'      => 'draft',
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);
        $this->assertDatabaseHas('news', ['title' => 'Editor Created Article']);
    }

    /** @test */
    public function test_api_create_news_validation_fails_missing_fields(): void
    {
        $editor = $this->makeUser('editor');

        // Send request with no body fields
        $response = $this->actingAs($editor)->postJson('/api/v1/news', []);

        $response->assertStatus(400);
        $response->assertJsonPath('success', false);
    }

    /** @test */
    public function test_api_delete_returns_204(): void
    {
        $admin = $this->makeUser('admin');
        $news  = $this->makeNews($admin, 'published');

        $response = $this->actingAs($admin)->deleteJson('/api/v1/news/' . $news->id);

        $response->assertStatus(204);
        $response->assertNoContent();
        $this->assertDatabaseMissing('news', ['id' => $news->id]);
    }

    /** @test */
    public function test_api_categories_returns_list(): void
    {
        $response = $this->getJson('/api/v1/categories');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => ['id', 'name', 'slug'],
            ],
        ]);

        $names = collect($response->json('data'))->pluck('name')->toArray();
        $this->assertContains('General', $names);
    }
}
