<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Room;
use App\Models\User;
use Database\Seeders\RoomSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/** Catatan: UploadedFile::fake()->image() membutuhkan ekstensi PHP GD. */
class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoomSeeder::class);
        Storage::fake('public');
    }

    private function admin(): User
    {
        return User::factory()->create(['password' => Hash::make('rahasia-123')]);
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
        $this->get('/admin/kamar/tambah')->assertRedirect(route('admin.login'));
        $this->post('/admin/kamar', [])->assertRedirect(route('admin.login'));
        $this->get('/admin/login')->assertOk()->assertSee('Login');
    }

    public function test_admin_can_login_and_logout(): void
    {
        $user = $this->admin();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'salah'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();

        $this->post('/admin/login', ['email' => $user->email, 'password' => 'rahasia-123'])
            ->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/admin/logout')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_dashboard_lists_rooms_and_inquiries(): void
    {
        Inquiry::create(['room_id' => Room::first()->id, 'name' => 'Siti Nurhaliza', 'whatsapp' => '081234567890', 'type' => 'pemesanan', 'message' => 'Halo']);

        $this->actingAs($this->admin())->get('/admin')->assertOk()
            ->assertSee('Dasbor Admin')->assertSee('Kamar 1')->assertSee('Kamar 6')
            ->assertSee('Siti Nurhaliza')->assertSee('0812-3456-7890');
    }

    public function test_admin_can_create_room_with_photos(): void
    {
        $this->actingAs($this->admin())->post('/admin/kamar', [
            'name' => 'Kamar 7',
            'price' => '800.000',
            'status' => 'tersedia',
            'description' => 'Kamar baru.',
            'facilities' => ['wifi', 'listrik'],
            'photos' => [UploadedFile::fake()->image('a.jpg', 1200, 900), UploadedFile::fake()->image('b.png', 1200, 900)],
        ])->assertRedirect(route('admin.dashboard'));

        $room = Room::where('name', 'Kamar 7')->firstOrFail();
        $this->assertSame(800000, $room->price);
        $this->assertSame(7, $room->number);
        $this->assertSame('kamar-7', $room->slug);
        $this->assertSame(['wifi', 'listrik'], $room->facilities);
        $this->assertCount(2, $room->photos);
        Storage::disk('public')->assertExists($room->photos->first()->path);

        $this->get('/kamar/kamar-7')->assertOk()->assertSee('Rp800.000');
    }

    public function test_room_validation(): void
    {
        $this->actingAs($this->admin())->post('/admin/kamar', [
            'name' => 'Kamar 1', 'price' => '', 'status' => 'x',
            'photos' => [UploadedFile::fake()->create('doc.pdf', 10, 'application/pdf')],
        ])->assertSessionHasErrors(['name', 'price', 'status', 'photos.0']);
    }

    public function test_admin_can_update_room_change_cover_and_delete_photo(): void
    {
        $admin = $this->admin();
        $room = Room::where('slug', 'kamar-1')->firstOrFail(); // punya 3 foto dari seeder
        [$first, $second, $third] = $room->photos->all();

        $this->actingAs($admin)->put("/admin/kamar/{$room->id}", [
            'name' => 'Kamar 1',
            'price' => '900000',
            'status' => 'terisi',
            'facilities' => ['wifi'],
            'cover_photo' => $second->id,
            'delete_photos' => [$third->id],
            'captions' => [$second->id => 'Sudut Baru'],
        ])->assertRedirect(route('admin.dashboard'));

        $room->refresh()->load('photos');
        $this->assertSame(900000, $room->price);
        $this->assertSame('terisi', $room->status);
        $this->assertCount(2, $room->photos);
        $this->assertSame($second->id, $room->photos->first()->id);
        $this->assertSame('Sudut Baru', $room->photos->first()->caption);
        $this->assertDatabaseMissing('room_photos', ['id' => $third->id]);
    }

    public function test_admin_can_toggle_status_and_delete_room(): void
    {
        $admin = $this->admin();
        $room = Room::where('slug', 'kamar-1')->firstOrFail();

        $this->actingAs($admin)->patch("/admin/kamar/{$room->id}/status")->assertRedirect();
        $this->assertSame('terisi', $room->fresh()->status);

        $this->actingAs($admin)->delete("/admin/kamar/{$room->id}")->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
        $this->assertDatabaseMissing('room_photos', ['room_id' => $room->id]);
    }

    public function test_admin_can_manage_inquiries(): void
    {
        $admin = $this->admin();
        $inq = Inquiry::create(['name' => 'Amanda', 'whatsapp' => '082198765432', 'type' => 'pertanyaan', 'message' => 'Halo']);

        $this->actingAs($admin)->patch("/admin/pesan/{$inq->id}/baca")->assertRedirect();
        $this->assertTrue($inq->fresh()->is_read);

        $this->actingAs($admin)->delete("/admin/pesan/{$inq->id}")->assertRedirect();
        $this->assertDatabaseMissing('inquiries', ['id' => $inq->id]);
    }
}
