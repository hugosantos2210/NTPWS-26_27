<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Role;
use App\Models\User;
use App\Models\Category;
use App\Models\News;
use App\Models\Pet;

class PawSafePagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $roleAdmin = Role::create(['name' => 'admin', 'description' => 'Administrator']);
        $roleEditor = Role::create(['name' => 'editor', 'description' => 'Editor']);
        $roleUser = Role::create(['name' => 'user', 'description' => 'User']);

        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role_id' => $roleAdmin->id,
            'is_active' => true,
        ]);

        $cat = Category::create([
            'name' => 'Adoption Stories',
            'slug' => 'adoption-stories',
            'description' => 'Test Category',
        ]);

        News::create([
            'title' => 'Sample News Article',
            'slug' => 'sample-news-article',
            'summary' => 'This is a sample summary for testing purposes.',
            'content' => '<p>Full content of the test article.</p>',
            'category_id' => $cat->id,
            'author_id' => $admin->id,
            'status' => 'published',
            'published_at' => now(),
        ]);

        Pet::create([
            'name' => 'Rex',
            'species' => 'dog',
            'breed' => 'German Shepherd',
            'age_years' => 2,
            'age_months' => 3,
            'gender' => 'male',
            'size' => 'large',
            'description' => 'Friendly companion',
            'status' => 'available',
        ]);
    }

    public function test_home_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('PawSafe');
        $response->assertSee('Sample News Article');
        $response->assertSee('Rex');
    }

    public function test_about_page_loads(): void
    {
        $response = $this->get('/about');
        $response->assertStatus(200);
        $response->assertSee('Missão PawSafe');
    }

    public function test_news_index_loads(): void
    {
        $response = $this->get('/news');
        $response->assertStatus(200);
        $response->assertSee('Sample News Article');
    }

    public function test_news_show_loads(): void
    {
        $response = $this->get('/news/sample-news-article');
        $response->assertStatus(200);
        $response->assertSee('Sample News Article');
    }

    public function test_pets_index_loads(): void
    {
        $response = $this->get('/pets');
        $response->assertStatus(200);
        $response->assertSee('Rex');
    }

    public function test_api_v1_news_returns_json(): void
    {
        $response = $this->getJson('/api/v1/news');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta' => ['current_page', 'last_page', 'per_page', 'total'],
        ]);
    }

    public function test_api_v1_schema_returns_json_schema(): void
    {
        $response = $this->get('/api/v1/schema');
        $response->assertStatus(200);
        $response->assertSee('PawSafe News Article');
    }
}
