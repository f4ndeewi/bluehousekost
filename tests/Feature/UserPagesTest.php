<?php

namespace Tests\Feature;

use App\Models\Inquiry;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_home_shows_availability_counter(): void
    {
        $this->get('/')->assertOk()->assertSee('Tersisa')->assertSee('4')->assertSee('Rp750.000');
    }

    public function test_room_list_shows_six_rooms_with_status(): void
    {
        $this->get('/kamar')->assertOk()
            ->assertSee('Kamar 1')->assertSee('Kamar 6')
            ->assertSee('Tersedia')->assertSee('Sudah Terisi');
    }

    public function test_room_detail_page(): void
    {
        $this->get('/kamar/kamar-1')->assertOk()->assertSee('Rp750.000')->assertSee('Dapur bersama dengan kulkas');
        $this->get('/kamar/tidak-ada')->assertNotFound();
    }

    public function test_inquiry_can_be_submitted(): void
    {
        $room = Room::where('slug', 'kamar-1')->firstOrFail();

        $this->post("/kamar/{$room->slug}/pesan", [
            'nama' => 'Siti Nurhaliza',
            'whatsapp' => '0812-3456-7890',
            'email' => 'siti@example.com',
            'jenis_pesan' => 'pemesanan',
            'isi_pesan' => 'Ingin survei besok.',
        ])->assertRedirect()->assertSessionHas('inquiry_sent');

        $this->assertDatabaseHas('inquiries', [
            'room_id' => $room->id,
            'name' => 'Siti Nurhaliza',
            'whatsapp' => '081234567890',
            'type' => Inquiry::PEMESANAN,
        ]);
    }

    public function test_inquiry_validation(): void
    {
        $this->post('/kamar/kamar-1/pesan', ['nama' => '', 'whatsapp' => 'abc', 'email' => 'salah', 'jenis_pesan' => 'x'])
            ->assertSessionHasErrors(['nama', 'whatsapp', 'email', 'jenis_pesan']);

        $this->assertDatabaseCount('inquiries', 0);
    }

    public function test_honeypot_blocks_bots(): void
    {
        $this->post('/kamar/kamar-1/pesan', [
            'nama' => 'Bot',
            'whatsapp' => '081234567890',
            'jenis_pesan' => 'pertanyaan',
            'website' => 'http://spam',
        ])->assertSessionHasErrors('website');
    }

    public function test_admin_email_is_sent_when_configured(): void
    {
        \Illuminate\Support\Facades\Mail::fake();
        config(['kos.admin_email' => 'pemilik@example.com']);

        $this->post('/kamar/kamar-1/pesan', [
            'nama' => 'Dewi',
            'whatsapp' => '081355667788',
            'jenis_pesan' => 'pertanyaan',
            'isi_pesan' => 'Ada jam malam?',
        ])->assertRedirect();

        \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InquiryReceived::class, fn($m) => $m->hasTo('pemilik@example.com'));
        $this->assertDatabaseHas('inquiries', ['name' => 'Dewi', 'email_sent' => true]);
    }

    public function test_inquiry_is_saved_even_if_email_fails(): void
    {
        config([
            'kos.admin_email' => 'pemilik@example.com',
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => '127.0.0.1',
            'mail.mailers.smtp.port' => 1,
        ]);

        $this->post('/kamar/kamar-1/pesan', [
            'nama' => 'Dewi',
            'whatsapp' => '081355667788',
            'jenis_pesan' => 'pertanyaan',
        ])->assertRedirect()->assertSessionHas('inquiry_sent');

        $this->assertDatabaseHas('inquiries', ['name' => 'Dewi', 'email_sent' => false]);
    }

    public function test_landing_page_has_all_sections(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Kamar Pilihan')->assertSee('Galeri Foto')->assertSee('id="cf-stage"', false)->assertSee('data-slide', false)->assertSee('Cara Memesan')
            ->assertSee('FAQ')->assertSee('Lokasi')->assertSee('google.com/maps')
            ->assertSee('Rp750.000')                       // placeholder {harga} terisi
            ->assertDontSee('{harga}')->assertDontSee('{pembayaran}')->assertDontSee('{whatsapp}')
            ->assertDontSee('Testimoni Penghuni');          // kosong = bagian disembunyikan
    }

    public function test_testimonials_show_when_configured(): void
    {
        config(['kos.testimonials' => [['nama' => 'Siti', 'peran' => 'Mahasiswi', 'isi' => 'Kosnya tenang dan bersih.']]]);

        $this->get('/')->assertOk()->assertSee('Testimoni Penghuni')->assertSee('Kosnya tenang dan bersih.');
    }

    public function test_admin_link_is_hidden_from_visitors_but_shown_to_logged_in_admin(): void
    {
        foreach (['/', '/kamar', '/kamar/kamar-1'] as $url) {
            $this->get($url)->assertOk()->assertDontSee('Login Admin')->assertDontSee('/admin/login')->assertDontSee('Dasbor Admin');
        }

        $admin = \App\Models\User::factory()->create();
        $this->actingAs($admin)->get('/')->assertOk()->assertSee('Dasbor Admin');
    }
}
