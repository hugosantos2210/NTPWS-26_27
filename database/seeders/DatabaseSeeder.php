<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with demo data.
     * Creates roles, users (admin, editor, user), categories, news articles,
     * pets, and adoption requests for testing all features.
     */
    public function run(): void
    {
        // ── Roles ──────────────────────────────────────────────────
        $roles = [
            ['name' => 'admin', 'description' => 'Full system administrator', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'editor', 'description' => 'Can create and edit news articles', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'user', 'description' => 'Registered user with basic access', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('roles')->insert($roles);

        $adminRoleId = DB::table('roles')->where('name', 'admin')->value('id');
        $editorRoleId = DB::table('roles')->where('name', 'editor')->value('id');
        $userRoleId = DB::table('roles')->where('name', 'user')->value('id');

        // ── Users ──────────────────────────────────────────────────
        $users = [
            [
                'name' => 'Admin User',
                'email' => 'admin@pawsafe.com',
                'password' => Hash::make('password123'),
                'phone' => '+351 912 345 678',
                'role_id' => $adminRoleId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Maria Santos',
                'email' => 'editor@pawsafe.com',
                'password' => Hash::make('password123'),
                'phone' => '+351 923 456 789',
                'role_id' => $editorRoleId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'João Silva',
                'email' => 'user@pawsafe.com',
                'password' => Hash::make('password123'),
                'phone' => '+351 934 567 890',
                'role_id' => $userRoleId,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Inactive User',
                'email' => 'inactive@pawsafe.com',
                'password' => Hash::make('password123'),
                'phone' => null,
                'role_id' => $userRoleId,
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];
        DB::table('users')->insert($users);

        $adminId = DB::table('users')->where('email', 'admin@pawsafe.com')->value('id');
        $editorId = DB::table('users')->where('email', 'editor@pawsafe.com')->value('id');
        $userId = DB::table('users')->where('email', 'user@pawsafe.com')->value('id');

        // ── Categories ─────────────────────────────────────────────
        $categories = [
            ['name' => 'Adoption Stories', 'slug' => 'adoption-stories', 'description' => 'Happy tails and successful adoption stories', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Health & Care', 'slug' => 'health-care', 'description' => 'Pet health tips and veterinary care advice', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Events', 'slug' => 'events', 'description' => 'Upcoming shelter events and community activities', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Volunteer', 'slug' => 'volunteer', 'description' => 'Volunteering opportunities and stories', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tips & Training', 'slug' => 'tips-training', 'description' => 'Training advice and pet care tips', 'created_at' => now(), 'updated_at' => now()],
        ];
        DB::table('categories')->insert($categories);

        $catAdoption = DB::table('categories')->where('slug', 'adoption-stories')->value('id');
        $catHealth = DB::table('categories')->where('slug', 'health-care')->value('id');
        $catEvents = DB::table('categories')->where('slug', 'events')->value('id');
        $catVolunteer = DB::table('categories')->where('slug', 'volunteer')->value('id');
        $catTips = DB::table('categories')->where('slug', 'tips-training')->value('id');

        // ── News ────────────────────────────────────────────────────
        $news = [
            [
                'title' => 'Max Found His Forever Home!',
                'slug' => 'max-found-his-forever-home',
                'summary' => 'After 6 months at our shelter, Max the Golden Retriever has finally been adopted by a loving family.',
                'content' => '<p>We are thrilled to announce that <strong>Max</strong>, our beloved Golden Retriever, has found his forever home! After spending 6 months at PawSafe Shelter, Max was adopted by the Ferreira family from Lisbon.</p><p>Max came to us as a shy and scared pup, but with love and care from our volunteers, he transformed into the most affectionate and playful dog. His new family was instantly smitten when they met him during our open adoption day.</p><p>"The moment Max put his head on my lap, I knew he was coming home with us," said Ana Ferreira, his new owner.</p><p>We wish Max and the Ferreira family all the happiness in the world!</p>',
                'category_id' => $catAdoption,
                'author_id' => $editorId,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(2),
            ],
            [
                'title' => 'Spring Vaccination Campaign',
                'slug' => 'spring-vaccination-campaign',
                'summary' => 'Free vaccinations for shelter animals this spring. Learn about our partnership with local veterinary clinics.',
                'content' => '<p>PawSafe Shelter is excited to announce our <strong>Spring Vaccination Campaign</strong> in partnership with Clínica Veterinária do Porto.</p><p>All shelter animals will receive their annual vaccinations free of charge, including:</p><ul><li>Rabies vaccine</li><li>DHPP (Distemper, Hepatitis, Parainfluenza, Parvovirus)</li><li>Bordetella</li><li>FeLV for cats</li></ul><p>This campaign ensures all our animals are healthy and ready for their forever homes. We thank our partners for their generous support.</p>',
                'category_id' => $catHealth,
                'author_id' => $editorId,
                'status' => 'published',
                'published_at' => now()->subDays(5),
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(5),
            ],
            [
                'title' => 'Annual Adoption Fair - Save the Date!',
                'slug' => 'annual-adoption-fair-save-the-date',
                'summary' => 'Join us for our biggest adoption event of the year on March 15th at Parque da Cidade.',
                'content' => '<p>Mark your calendars! PawSafe Shelter\'s <strong>Annual Adoption Fair</strong> is happening on March 15th at Parque da Cidade.</p><p>This year\'s event will feature:</p><ul><li>Meet and greet with all available pets</li><li>Reduced adoption fees</li><li>Free pet microchipping</li><li>Live music and food trucks</li><li>Kids\' corner with pet education activities</li></ul><p>Last year, we helped 45 animals find their forever homes during this event. Let\'s make this year even better!</p>',
                'category_id' => $catEvents,
                'author_id' => $adminId,
                'status' => 'published',
                'published_at' => now()->subDays(1),
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(1),
            ],
            [
                'title' => 'Volunteer Spotlight: Pedro\'s Story',
                'slug' => 'volunteer-spotlight-pedros-story',
                'summary' => 'Meet Pedro, who has been volunteering at PawSafe for over 2 years and has helped train dozens of dogs.',
                'content' => '<p>This month\'s <strong>Volunteer Spotlight</strong> features Pedro Almeida, who has been an invaluable member of our PawSafe family for over two years.</p><p>Pedro joined our team as a weekend dog walker and quickly became our go-to person for behavioral training. He has helped rehabilitate over 30 dogs, making them more adoptable.</p><p>"Every dog deserves a chance," says Pedro. "When I see a scared dog transform into a confident, happy companion, that\'s my reward."</p><p>Want to volunteer? Visit our volunteer page to learn more!</p>',
                'category_id' => $catVolunteer,
                'author_id' => $editorId,
                'status' => 'published',
                'published_at' => now()->subDays(10),
                'created_at' => now()->subDays(12),
                'updated_at' => now()->subDays(10),
            ],
            [
                'title' => '5 Tips for First-Time Pet Owners',
                'slug' => '5-tips-for-first-time-pet-owners',
                'summary' => 'Essential advice for those bringing home their first furry friend from the shelter.',
                'content' => '<p>Adopting your first pet is an exciting experience! Here are our <strong>top 5 tips</strong> for new pet owners:</p><ol><li><strong>Be patient</strong> - Your new pet needs time to adjust to their new environment.</li><li><strong>Create a safe space</strong> - Set up a quiet area with their bed, food, and water.</li><li><strong>Stick to a routine</strong> - Regular feeding and walking times help pets feel secure.</li><li><strong>Visit your vet</strong> - Schedule a check-up within the first week.</li><li><strong>Socialize gradually</strong> - Introduce new people and animals slowly.</li></ol><p>Remember, adoption is a lifelong commitment. We\'re here to support you every step of the way!</p>',
                'category_id' => $catTips,
                'author_id' => $adminId,
                'status' => 'published',
                'published_at' => now()->subDays(15),
                'created_at' => now()->subDays(18),
                'updated_at' => now()->subDays(15),
            ],
            [
                'title' => 'New Cat Wing Opening Soon',
                'slug' => 'new-cat-wing-opening-soon',
                'summary' => 'We are expanding our shelter with a dedicated wing for our feline friends.',
                'content' => '<p>Exciting news! Thanks to generous donations, PawSafe Shelter is opening a brand new <strong>Cat Wing</strong> next month.</p><p>The new wing will feature individual cat rooms with climbing structures, play areas, and large windows for natural light. This will provide a much more comfortable environment for our cat residents.</p><p>Stay tuned for the grand opening date!</p>',
                'category_id' => $catEvents,
                'author_id' => $editorId,
                'status' => 'draft',
                'published_at' => null,
                'created_at' => now()->subDays(1),
                'updated_at' => now()->subDays(1),
            ],
            [
                'title' => 'Winter Preparedness for Your Pets',
                'slug' => 'winter-preparedness-for-your-pets',
                'summary' => 'How to keep your pets warm and safe during the cold winter months.',
                'content' => '<p>As winter approaches, it\'s important to ensure your pets stay warm and comfortable.</p><p>Key tips include providing warm bedding, limiting outdoor time in extreme cold, and watching for signs of hypothermia.</p>',
                'category_id' => $catTips,
                'author_id' => $editorId,
                'status' => 'withdrawn',
                'published_at' => now()->subDays(30),
                'created_at' => now()->subDays(35),
                'updated_at' => now()->subDays(3),
            ],
            [
                'title' => 'Luna\'s Amazing Recovery',
                'slug' => 'lunas-amazing-recovery',
                'summary' => 'How Luna the tabby cat recovered from a serious injury and found her forever home.',
                'content' => '<p><strong>Luna</strong>, a beautiful tabby cat, arrived at PawSafe with a broken leg after being found on the streets. Thanks to our veterinary partners and dedicated foster families, Luna made a full recovery.</p><p>After three months of rehabilitation, Luna was adopted by the Costa family. She now enjoys chasing sunbeams and cuddling on the couch.</p>',
                'category_id' => $catAdoption,
                'author_id' => $adminId,
                'status' => 'published',
                'published_at' => now()->subDays(8),
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(8),
            ],
        ];
        DB::table('news')->insert($news);

        // ── Pets ────────────────────────────────────────────────────
        $pets = [
            [
                'name' => 'Buddy',
                'species' => 'dog',
                'breed' => 'Labrador Retriever',
                'age_years' => 3,
                'age_months' => 6,
                'gender' => 'male',
                'size' => 'large',
                'description' => 'Buddy is a friendly and energetic Labrador who loves playing fetch and swimming. He\'s great with kids and other dogs. Fully vaccinated and neutered.',
                'status' => 'available',
                'created_at' => now()->subDays(20),
                'updated_at' => now()->subDays(20),
            ],
            [
                'name' => 'Mia',
                'species' => 'cat',
                'breed' => 'Siamese',
                'age_years' => 2,
                'age_months' => 0,
                'gender' => 'female',
                'size' => 'small',
                'description' => 'Mia is a graceful Siamese with stunning blue eyes. She loves being the center of attention and enjoys lap time. Spayed and up to date on all vaccinations.',
                'status' => 'available',
                'created_at' => now()->subDays(15),
                'updated_at' => now()->subDays(15),
            ],
            [
                'name' => 'Rocky',
                'species' => 'dog',
                'breed' => 'German Shepherd',
                'age_years' => 5,
                'age_months' => 0,
                'gender' => 'male',
                'size' => 'large',
                'description' => 'Rocky is a loyal and intelligent German Shepherd. He\'s well-trained, knows basic commands, and would do best in a home with an experienced owner. Neutered.',
                'status' => 'available',
                'created_at' => now()->subDays(30),
                'updated_at' => now()->subDays(30),
            ],
            [
                'name' => 'Whiskers',
                'species' => 'cat',
                'breed' => 'Maine Coon',
                'age_years' => 4,
                'age_months' => 3,
                'gender' => 'male',
                'size' => 'large',
                'description' => 'Whiskers is a gentle giant Maine Coon with a magnificent fluffy coat. He\'s extremely affectionate and gets along well with other cats. Neutered.',
                'status' => 'pending',
                'created_at' => now()->subDays(25),
                'updated_at' => now()->subDays(5),
            ],
            [
                'name' => 'Bella',
                'species' => 'dog',
                'breed' => 'Beagle',
                'age_years' => 1,
                'age_months' => 8,
                'gender' => 'female',
                'size' => 'medium',
                'description' => 'Bella is a playful young Beagle with endless curiosity. She loves sniffing around the garden and going for long walks. Great with children. Spayed.',
                'status' => 'available',
                'created_at' => now()->subDays(10),
                'updated_at' => now()->subDays(10),
            ],
            [
                'name' => 'Shadow',
                'species' => 'cat',
                'breed' => 'Domestic Shorthair',
                'age_years' => 1,
                'age_months' => 0,
                'gender' => 'female',
                'size' => 'small',
                'description' => 'Shadow is a sleek black cat with bright green eyes. She\'s a bit shy at first but warms up quickly. Loves to play with feather toys. Spayed.',
                'status' => 'available',
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'name' => 'Charlie',
                'species' => 'dog',
                'breed' => 'Golden Retriever',
                'age_years' => 6,
                'age_months' => 0,
                'gender' => 'male',
                'size' => 'large',
                'description' => 'Charlie is a calm and gentle Golden Retriever who was surrendered when his owner could no longer care for him. He\'s well-behaved and loves children.',
                'status' => 'adopted',
                'created_at' => now()->subDays(45),
                'updated_at' => now()->subDays(2),
            ],
            [
                'name' => 'Nala',
                'species' => 'cat',
                'breed' => 'Bengal',
                'age_years' => 3,
                'age_months' => 0,
                'gender' => 'female',
                'size' => 'medium',
                'description' => 'Nala is an exotic Bengal with beautiful spotted markings. She\'s very active and needs a home with plenty of space to explore. Spayed.',
                'status' => 'available',
                'created_at' => now()->subDays(5),
                'updated_at' => now()->subDays(5),
            ],
        ];
        DB::table('pets')->insert($pets);

        // ── Adoption Requests ───────────────────────────────────────
        $buddyId = DB::table('pets')->where('name', 'Buddy')->value('id');
        $whiskersId = DB::table('pets')->where('name', 'Whiskers')->value('id');

        $adoptionRequests = [
            [
                'pet_id' => $buddyId,
                'user_id' => $userId,
                'message' => 'I have a large backyard and two kids who would love to play with Buddy. We have experience with Labradors.',
                'status' => 'pending',
                'admin_notes' => null,
                'created_at' => now()->subDays(3),
                'updated_at' => now()->subDays(3),
            ],
            [
                'pet_id' => $whiskersId,
                'user_id' => $userId,
                'message' => 'We already have one cat and are looking for a companion. Whiskers seems perfect!',
                'status' => 'approved',
                'admin_notes' => 'Good match. Home visit completed successfully.',
                'created_at' => now()->subDays(7),
                'updated_at' => now()->subDays(5),
            ],
        ];
        DB::table('adoption_requests')->insert($adoptionRequests);
    }
}
