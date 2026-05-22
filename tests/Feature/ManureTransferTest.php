<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Farm;
use App\Models\ManurePile;
use App\Models\ManureTransfer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManureTransferTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $farmStaff;
    private User $pileStaff;
    private User $viewer;
    private Farm $farm;
    private ManurePile $pile;

    protected function setUp(): void
    {
        parent::setUp();

        // Create users with different roles
        $this->admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $this->farmStaff = User::create([
            'name' => 'Farm Staff User',
            'email' => 'farm@test.com',
            'password' => bcrypt('password'),
            'role' => 'farm_staff',
        ]);

        $this->pileStaff = User::create([
            'name' => 'Pile Staff User',
            'email' => 'pile@test.com',
            'password' => bcrypt('password'),
            'role' => 'pile_staff',
        ]);

        $this->viewer = User::create([
            'name' => 'Viewer User',
            'email' => 'viewer@test.com',
            'password' => bcrypt('password'),
            'role' => 'viewer',
        ]);

        // Create sample data
        $this->farm = Farm::create(['name' => 'Test Farm']);
        $this->pile = ManurePile::create(['name' => 'กอง 1']);
    }

    /**
     * Test authentication redirect.
     */
    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /**
     * Test role-based access for Farm Staff.
     */
    public function test_farm_staff_access_permissions(): void
    {
        // Farm staff can access Transfer Out
        $response = $this->actingAs($this->farmStaff)->get(route('transfers.out'));
        $response->assertStatus(200);

        // Farm staff CANNOT access Transfer In
        $response = $this->actingAs($this->farmStaff)->get(route('transfers.in'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    /**
     * Test role-based access for Pile Staff.
     */
    public function test_pile_staff_access_permissions(): void
    {
        // Pile staff CANNOT access Transfer Out
        $response = $this->actingAs($this->pileStaff)->get(route('transfers.out'));
        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');

        // Pile staff can access Transfer In
        $response = $this->actingAs($this->pileStaff)->get(route('transfers.in'));
        $response->assertStatus(200);
    }

    /**
     * Test role-based access for Admin.
     */
    public function test_admin_can_access_everything(): void
    {
        $response = $this->actingAs($this->admin)->get(route('transfers.out'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('transfers.in'));
        $response->assertStatus(200);

        $response = $this->actingAs($this->admin)->get(route('farms.index'));
        $response->assertStatus(200);
    }

    /**
     * Test recording an outbound transfer.
     */
    public function test_create_outbound_transfer_successfully(): void
    {
        $payload = [
            'farm_id' => $this->farm->id,
            'license_plate' => 'กข 1234 กรุงเทพ',
            'weight' => 12500.50,
            'out_datetime' => '2026-05-22 08:30:00',
            // Simple mock file for testing upload if needed, but we can pass text for database checking
        ];

        // We can mock image upload or use an empty state since validation requires image.
        // Let's create a dummy uploaded file.
        $file = \Illuminate\Http\UploadedFile::fake()->create('out_proof.jpg', 100);
        $payload['out_photo'] = $file;

        $response = $this->actingAs($this->farmStaff)->post(route('transfers.out.store'), $payload);

        // Should redirect to success page
        $this->assertCount(1, ManureTransfer::all());
        $transfer = ManureTransfer::first();
        
        $response->assertRedirect(route('transfers.out_success', $transfer->id));
        
        $this->assertEquals($this->farm->id, $transfer->farm_id);
        $this->assertEquals('กข 1234 กรุงเทพ', $transfer->license_plate);
        $this->assertEquals(12500.50, $transfer->weight);
        $this->assertEquals('pending', $transfer->status);
        $this->assertNotNull($transfer->transfer_no);
        $this->assertStringStartsWith('MF-', $transfer->transfer_no);
    }

    /**
     * Test receiving a transfer inbound.
     */
    public function test_receive_inbound_transfer_successfully(): void
    {
        // First create a pending transfer
        $transfer = ManureTransfer::create([
            'transfer_no' => 'MF-20260522-001',
            'farm_id' => $this->farm->id,
            'license_plate' => 'กข 1234 กรุงเทพ',
            'weight' => 12500.50,
            'out_datetime' => '2026-05-22 08:30:00',
            'out_photo' => 'transfers/dummy_out.jpg',
            'out_user_id' => $this->farmStaff->id,
            'status' => 'pending'
        ]);

        $file = \Illuminate\Http\UploadedFile::fake()->create('receive_proof.jpg', 100);
        $payload = [
            'pile_id' => $this->pile->id,
            'received_datetime' => '2026-05-22 09:00:00',
            'receive_photo' => $file,
            'remark' => 'เรียบร้อยดี'
        ];

        $response = $this->actingAs($this->pileStaff)->post(route('transfers.receive', $transfer->id), $payload);

        $response->assertRedirect(route('transfers.in'));
        $response->assertSessionHas('success');

        $transfer->refresh();
        $this->assertEquals('received', $transfer->status);
        $this->assertEquals($this->pile->id, $transfer->pile_id);
        $this->assertEquals($this->pileStaff->id, $transfer->receive_user_id);
    }
}
