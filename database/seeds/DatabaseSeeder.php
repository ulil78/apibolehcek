<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(GroupsTableSeeder::class);
        // $this->call(UsersTableSeeder::class);
        // $this->call(AdminsTableSeeder::class);
        // $this->call(SellersTableSeeder::class);

        $this->call(AboutsTableSeeder::class);
        // $this->call(AdvertisesTableSeeder::class);
        $this->call(ContactsTableSeeder::class);
        $this->call(HowToshopsTableSeeder::class);
        $this->call(HowTosellsTableSeeder::class);
        $this->call(OfficialPartnersTableSeeder::class);
        $this->call(OurActivitiesTableSeeder::class);
        $this->call(PaymentsTableSeeder::class);
        $this->call(RefundsTableSeeder::class);
        $this->call(SellerStoriesTableSeeder::class);
        $this->call(WithdrawalsTableSeeder::class);

        // factory(App\User::class,5)->create();
        // factory(App\Model\Product::class,50)->create();
        // factory(App\Model\Review::class,300)->create();
    }
}
