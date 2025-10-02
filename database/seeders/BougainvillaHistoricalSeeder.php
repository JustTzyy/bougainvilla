<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Level;
use App\Models\Room;
use App\Models\Accommodation;
use App\Models\Rate;
use App\Models\RateAccommodation;
use App\Models\Guest;
use App\Models\Address;
use App\Models\Stay;
use App\Models\GuestStay;
use App\Models\Payment;
use App\Models\Receipt;
use App\Models\History;
use App\Models\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class BougainvillaHistoricalSeeder extends Seeder
{
    /**
     * Run the database seeds for a 1-year-old hotel system
     */
    public function run(): void
    {
        $startDate = Carbon::now()->subYear();
        
        echo "🏨 Seeding Bougainvilla Lodge - 1 Year Historical Data\n";
        echo "📅 Start Date: {$startDate->format('Y-m-d')}\n\n";

        $this->seedRoles();
        $this->seedUsers();
        $this->seedLevels();
        $this->seedAccommodations();
        $this->seedRates();
        $this->seedRooms();
        $this->seedHistoricalData($startDate);
        $this->seedArchivedData($startDate);
        $this->seedCleanupGuests($startDate);
        
        echo "\n✅ Bougainvilla Lodge Historical Data Seeding Complete!\n";
    }

    private function seedRoles()
    {
        echo "👥 Creating Roles...\n";
        Role::firstOrCreate(['id' => 1], [
            'role' => 'Admin',
            'description' => 'System Administrator with full access'
        ]);
        Role::firstOrCreate(['id' => 2], [
            'role' => 'Front Desk',
            'description' => 'Front desk staff with limited access'
        ]);
        echo "   ✓ Admin and Front Desk roles created\n";
    }

    private function seedUsers()
    {
        echo "👤 Creating Users...\n";
        
        // Admin Users
        User::firstOrCreate(['email' => 'admin@bougainvilla.com'], [
            'firstName' => 'Maria', 'middleName' => 'Santos', 'lastName' => 'Rodriguez',
            'name' => 'Maria Santos Rodriguez', 'password' => Hash::make('admin123'),
            'password_changed' => true, 'contactNumber' => '09171234567',
            'roleID' => 1, 'birthday' => '1985-06-15', 'age' => 38,
            'sex' => 'Female', 'status' => 'Active',
        ]);

        // Front Desk Staff
        $frontDeskStaff = [
            ['Ana Marie Dela Cruz', 'ana@bougainvilla.com', '09191234567'],
            ['John Paul Reyes', 'john@bougainvilla.com', '09201234567'],
            ['Catherine Joy Flores', 'catherine@bougainvilla.com', '09211234567'],
        ];

        foreach ($frontDeskStaff as [$name, $email, $phone]) {
            $nameParts = explode(' ', $name);
            User::firstOrCreate(['email' => $email], [
                'firstName' => $nameParts[0], 'middleName' => $nameParts[1] ?? '',
                'lastName' => implode(' ', array_slice($nameParts, 2)),
                'name' => $name, 'password' => Hash::make('frontdesk123'),
                'password_changed' => false, 'contactNumber' => $phone,
                'roleID' => 2, 'birthday' => Carbon::now()->subYears(rand(22, 35))->format('Y-m-d'),
                'age' => rand(22, 35), 'sex' => in_array($nameParts[0], ['Ana', 'Catherine']) ? 'Female' : 'Male',
                'status' => 'Active',
            ]);
        }
        echo "   ✓ Admin and Front Desk staff created\n";
    }

    private function seedLevels()
    {
        echo "🏢 Creating Hotel Levels...\n";
        $levels = [
            'Ground Floor - Main lobby and ground floor rooms',
            '2nd Floor - Standard rooms with city view',
            '3rd Floor - Premium rooms with balcony',
        ];

        foreach ($levels as $description) {
            Level::firstOrCreate(['description' => $description], [
                'status' => 'Active'
            ]);
        }
        echo "   ✓ 3 hotel levels created\n";
    }

    private function seedAccommodations()
    {
        echo "🛏️ Creating Accommodations...\n";
        $accommodations = [
            ['Single Room', 1, 'Cozy room for solo travelers'],
            ['Double Room', 2, 'Comfortable room for couples'],
            ['Triple Room', 3, 'Spacious room for small families'],
            ['Quad Room', 4, 'Large room for families'],
        ];

        foreach ($accommodations as [$name, $capacity, $description]) {
            Accommodation::firstOrCreate(['name' => $name], [
                'capacity' => $capacity, 'description' => $description
            ]);
        }
        echo "   ✓ 4 accommodation types created\n";
    }

    private function seedRates()
    {
        echo "💰 Creating Rates...\n";
        $rates = [
            ['3 Hours', 450.00], ['6 Hours', 750.00], ['12 Hours', 1200.00],
            ['1 Day', 1800.00], ['2 Days', 3200.00],
        ];

        foreach ($rates as [$duration, $price]) {
            // Create rate without accommodation_id (removed by migration)
            $rate = Rate::firstOrCreate(['duration' => $duration], [
                'price' => $price, 'status' => 'Extending/Standard'
            ]);

            // Link to all accommodations via pivot table
            foreach (Accommodation::all() as $accommodation) {
                RateAccommodation::firstOrCreate([
                    'rate_id' => $rate->id, 
                    'accommodation_id' => $accommodation->id,
                ]);
            }
        }
        echo "   ✓ 5 rates created and linked\n";
    }

    private function seedRooms()
    {
        echo "🚪 Creating Rooms...\n";
        $roomCount = 0;
        foreach (Level::all() as $level) {
            $roomsPerLevel = 8;
            for ($i = 1; $i <= $roomsPerLevel; $i++) {
                $roomNumber = $level->id . sprintf('%02d', $i);
                Room::firstOrCreate(['room' => $roomNumber], [
                    'status' => 'Available', 'type' => 'Standard',
                    'level_id' => $level->id,
                ]);
                $roomCount++;
            }
        }
        echo "   ✓ {$roomCount} rooms created\n";
    }

    private function seedHistoricalData($startDate)
    {
        echo "📊 Creating Historical Data...\n";
        
        $users = User::where('roleID', 2)->get();
        $rooms = Room::all();
        $rates = Rate::all();
        $totalStays = 0;
        
        // Create 200 historical bookings over the past year
        for ($i = 0; $i < 200; $i++) {
            $bookingDate = $startDate->copy()->addDays(rand(0, 365));
            
            // Create guest
            $guestData = $this->generateGuestData();
            $address = Address::create([
                'street' => $guestData['street'], 'city' => $guestData['city'],
                'province' => $guestData['province'], 'zipcode' => $guestData['zipcode'],
                'userID' => $users->random()->id,
            ]);

            $guest = Guest::create([
                'firstName' => $guestData['firstName'],
                'middleName' => $guestData['middleName'] ?? null,
                'lastName' => $guestData['lastName'],
                'number' => $guestData['number'],
                'addressID' => $address->id,
            ]);

            // Create stay
            $room = $rooms->random();
            $initialRate = $rates->random();
            $checkIn = $bookingDate->copy()->setHour(rand(10, 20));
            $checkOut = $checkIn->copy()->addHours($this->parseDurationToHours($initialRate->duration));
            
            $stay = Stay::create([
                'checkIn' => $checkIn, 'checkOut' => $checkOut,
                'status' => 'Standard', 'rateID' => $initialRate->id, 'roomID' => $room->id,
            ]);

            GuestStay::create(['guestID' => $guest->id, 'stayID' => $stay->id]);

            // Create initial payment (check-in payment)
            $subtotal = $initialRate->price;
            $tax = $subtotal * 0.12;
            $total = $subtotal + $tax;

            $initialPayment = Payment::create([
                'amount' => $total, 'tax' => $tax, 'subtotal' => $subtotal,
                'status' => 'Completed', 'change' => 0, 'stayID' => $stay->id,
            ]);

            Receipt::create([
                'status' => 'Issued', 
                'status_type' => 'Standard',
                'paymentID' => $initialPayment->id, 
                'userID' => $users->random()->id,
            ]);

            // 30% chance of having extensions (following your business logic)
            $hasExtensions = rand(1, 100) <= 30;
            if ($hasExtensions) {
                $extensionCount = rand(1, 3); // 1-3 extensions
                $currentCheckOut = $checkOut;
                
                for ($ext = 0; $ext < $extensionCount; $ext++) {
                    $extensionRate = $rates->random();
                    $newCheckOut = $currentCheckOut->copy()->addHours($this->parseDurationToHours($extensionRate->duration));
                    
                    // Update stay checkout and status
                    $stay->update([
                        'checkOut' => $newCheckOut,
                        'status' => 'Extend'
                    ]);
                    
                    // Create extension payment
                    $extSubtotal = $extensionRate->price;
                    $extTax = $extSubtotal * 0.12;
                    $extTotal = $extSubtotal + $extTax;

                    $extensionPayment = Payment::create([
                        'amount' => $extTotal, 'tax' => $extTax, 'subtotal' => $extSubtotal,
                        'status' => 'Completed', 'change' => 0, 'stayID' => $stay->id,
                    ]);

                    Receipt::create([
                        'status' => 'Issued', 
                        'status_type' => 'Extend',
                        'paymentID' => $extensionPayment->id, 
                        'userID' => $users->random()->id,
                    ]);
                    
                    $currentCheckOut = $newCheckOut;
                }
            }

            $totalStays++;
        }
        
        echo "   ✓ {$totalStays} historical stays created\n";
    }

    private function generateGuestData()
    {
        $firstNames = ['Juan', 'Maria', 'Jose', 'Ana', 'Carlos', 'Rosa', 'Manuel', 'Carmen'];
        $middleNames = ['Santos', 'Cruz', 'Reyes', 'Garcia', 'Flores', 'Torres', null]; // null for some guests without middle name
        $lastNames = ['Santos', 'Reyes', 'Cruz', 'Garcia', 'Flores', 'Torres', 'Mendoza'];
        $provinces = ['Metro Manila', 'Cebu', 'Laguna', 'Cavite'];
        $cities = ['Manila', 'Quezon City', 'Cebu City', 'Calamba'];
        
        return [
            'firstName' => $firstNames[array_rand($firstNames)],
            'middleName' => $middleNames[array_rand($middleNames)],
            'lastName' => $lastNames[array_rand($lastNames)],
            'number' => '09' . rand(100000000, 999999999),
            'street' => rand(1, 999) . ' Main Street, Barangay Centro',
            'city' => $cities[array_rand($cities)],
            'province' => $provinces[array_rand($provinces)],
            'zipcode' => rand(1000, 9999),
        ];
    }

    private function parseDurationToHours($durationStr)
    {
        if (preg_match('/(\d+)\s*hour/i', $durationStr, $matches)) return (int)$matches[1];
        if (preg_match('/(\d+)\s*day/i', $durationStr, $matches)) return (int)$matches[1] * 24;
        return 24;
    }

    private function seedArchivedData($startDate)
    {
        echo "🗃️ Creating Archived Data...\n";
        
        $users = User::where('roleID', 2)->get();
        $rooms = Room::all();
        $rates = Rate::all();
        $archivedCount = 0;
        
        // Create 80 archived stays (stays that were checked out and then archived)
        for ($i = 0; $i < 80; $i++) {
            $archiveDate = Carbon::now(); // Archive date is current timestamp
            
            // Create guest for archived stay
            $guestData = $this->generateGuestData();
            $address = Address::create([
                'street' => $guestData['street'], 'city' => $guestData['city'],
                'province' => $guestData['province'], 'zipcode' => $guestData['zipcode'],
                'userID' => $users->random()->id,
            ]);

            $guest = Guest::create([
                'firstName' => $guestData['firstName'],
                'middleName' => $guestData['middleName'] ?? null,
                'lastName' => $guestData['lastName'],
                'number' => $guestData['number'],
                'addressID' => $address->id,
            ]);

            // Create the stay
            $room = $rooms->random();
            $rate = $rates->random();
            $checkIn = $archiveDate->copy()->subDays(rand(1, 3))->setHour(rand(10, 20));
            $checkOut = $checkIn->copy()->addHours($this->parseDurationToHours($rate->duration));
            
            $stay = Stay::create([
                'checkIn' => $checkIn,
                'checkOut' => $checkOut,
                'status' => 'Standard',
                'rateID' => $rate->id,
                'roomID' => $room->id,
            ]);

            GuestStay::create(['guestID' => $guest->id, 'stayID' => $stay->id]);

            // Create payment and receipt
            $subtotal = $rate->price;
            $tax = $subtotal * 0.12;
            $total = $subtotal + $tax;

            $payment = Payment::create([
                'amount' => $total, 'tax' => $tax, 'subtotal' => $subtotal,
                'status' => 'Completed', 'change' => 0, 'stayID' => $stay->id,
            ]);

            Receipt::create([
                'status' => 'Issued', 
                'status_type' => 'Standard',
                'paymentID' => $payment->id, 
                'userID' => $users->random()->id,
            ]);

            // 30% chance of having extensions for archived stays to create more transaction data
            $hasExtensions = rand(1, 100) <= 30;
            if ($hasExtensions) {
                $extensionCount = rand(1, 2); // 1-2 extensions for archived stays
                $currentCheckOut = $checkOut;
                
                for ($ext = 0; $ext < $extensionCount; $ext++) {
                    $extensionRate = $rates->random();
                    $newCheckOut = $currentCheckOut->copy()->addHours($this->parseDurationToHours($extensionRate->duration));
                    
                    // Update stay checkout and status
                    $stay->update([
                        'checkOut' => $newCheckOut,
                        'status' => 'Extend'
                    ]);
                    
                    // Create extension payment
                    $extSubtotal = $extensionRate->price;
                    $extTax = $extSubtotal * 0.12;
                    $extTotal = $extSubtotal + $extTax;

                    $extensionPayment = Payment::create([
                        'amount' => $extTotal, 'tax' => $extTax, 'subtotal' => $extSubtotal,
                        'status' => 'Completed', 'change' => 0, 'stayID' => $stay->id,
                    ]);

                    Receipt::create([
                        'status' => 'Issued', 
                        'status_type' => 'Extend',
                        'paymentID' => $extensionPayment->id, 
                        'userID' => $users->random()->id,
                    ]);
                    
                    $currentCheckOut = $newCheckOut;
                }
            }

            // Archive the stay (soft delete) with the archive date
            $stay->delete();
            // Manually set the deleted_at timestamp to the archive date
            \DB::table('stays')->where('id', $stay->id)->update(['deleted_at' => $archiveDate]);

            // Create history entry for the archive
            History::create([
                'userID' => $users->random()->id,
                'status' => 'Archived stay for room ' . $room->room . ' - Guest: ' . $guest->firstName . ' ' . $guest->lastName,
                'created_at' => $archiveDate,
                'updated_at' => $archiveDate,
            ]);

            $archivedCount++;
        }
        
        echo "   ✓ {$archivedCount} archived stays created\n";
    }

    private function seedCleanupGuests($startDate)
    {
        echo "🧹 Creating Guests for Cleanup Testing...\n";
        
        $users = User::where('roleID', 2)->get();
        $now = Carbon::now();
        
        // 1. Create guests ready for soft delete (4+ months old, not deleted)
        for ($i = 0; $i < 15; $i++) {
            $oldDate = $now->copy()->subMonths(rand(4, 8)); // 4-8 months old
            
            $guestData = $this->generateGuestData();
            $address = Address::create([
                'street' => $guestData['street'], 'city' => $guestData['city'],
                'province' => $guestData['province'], 'zipcode' => $guestData['zipcode'],
                'userID' => $users->random()->id,
                'created_at' => $oldDate,
                'updated_at' => $oldDate,
            ]);

            Guest::create([
                'firstName' => $guestData['firstName'],
                'middleName' => $guestData['middleName'] ?? null,
                'lastName' => $guestData['lastName'],
                'number' => $guestData['number'],
                'addressID' => $address->id,
                'created_at' => $oldDate,
                'updated_at' => $oldDate,
                'last_cleanup_check' => null,
                'cleanup_notified' => false,
            ]);
        }

        // 2. Create guests ready for hard delete (soft deleted 4+ months ago)
        for ($i = 0; $i < 25; $i++) {
            $createdDate = $now->copy()->subMonths(rand(8, 12)); // Created 8-12 months ago
            $deletedDate = $now->copy()->subMonths(rand(4, 6));   // Soft deleted 4-6 months ago
            
            $guestData = $this->generateGuestData();
            $address = Address::create([
                'street' => $guestData['street'], 'city' => $guestData['city'],
                'province' => $guestData['province'], 'zipcode' => $guestData['zipcode'],
                'userID' => $users->random()->id,
                'created_at' => $createdDate,
                'updated_at' => $createdDate,
            ]);

            $guest = Guest::create([
                'firstName' => $guestData['firstName'],
                'middleName' => $guestData['middleName'] ?? null,
                'lastName' => $guestData['lastName'],
                'number' => $guestData['number'],
                'addressID' => $address->id,
                'created_at' => $createdDate,
                'updated_at' => $createdDate,
                'last_cleanup_check' => $deletedDate,
                'cleanup_notified' => true,
            ]);

            // Soft delete the guest with the specific deleted date
            $guest->delete();
            \DB::table('guests')->where('id', $guest->id)->update(['deleted_at' => $deletedDate]);
        }

        // 3. Create recent guests (safe from cleanup)
        for ($i = 0; $i < 20; $i++) {
            $recentDate = $now->copy()->subDays(rand(1, 60)); // 1-60 days old
            
            $guestData = $this->generateGuestData();
            $address = Address::create([
                'street' => $guestData['street'], 'city' => $guestData['city'],
                'province' => $guestData['province'], 'zipcode' => $guestData['zipcode'],
                'userID' => $users->random()->id,
                'created_at' => $recentDate,
                'updated_at' => $recentDate,
            ]);

            Guest::create([
                'firstName' => $guestData['firstName'],
                'middleName' => $guestData['middleName'] ?? null,
                'lastName' => $guestData['lastName'],
                'number' => $guestData['number'],
                'addressID' => $address->id,
                'created_at' => $recentDate,
                'updated_at' => $recentDate,
                'last_cleanup_check' => null,
                'cleanup_notified' => false,
            ]);
        }
        
        echo "   ✓ 60 cleanup test guests created (15 ready for soft delete, 25 ready for hard delete, 20 recent)\n";
    }
}