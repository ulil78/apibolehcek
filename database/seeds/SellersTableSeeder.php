<?php

use Illuminate\Database\Seeder;

class SellersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('sellers')->insert([
          'name'              => 'Seller',
          'email'             => 'seller@gmail.com',
          'password'          => bcrypt('1234567890'),
          'pic_name'          => 'PIC Seller',
          'address'           => 'Jakarta Barat',
          'provinces_id'      => 1,
          'cities_id'         => 1,
          'disctrics_id'       => 1,
          'postcode'          => '10260',
          'phone'             => '08128167890',
          'fax'               => '-',
          'bank'              => 'BCA',
          'bank_account'     => '1222008779',
          'account_holder'    => 'Seller',
          'corporate_name'    => 'PT. Seller',
          'corporate_address' => 'Corporate Address',
          'created_at'        => date('Y-m-d H:i:s'),
          'updated_at'        => date('Y-m-d H:i:s'),
          'remember_token'    => bcrypt('seller@gmail.com')
      ]);



      DB::table('sellers')->insert([
          'name'              => 'Seller 2',
          'email'             => 'seller2@gmail.com',
          'password'          => bcrypt('1234567890'),
          'pic_name'          => 'PIC Seller',
          'address'           => 'Jakarta Barat',
          'provinces_id'      => 1,
          'cities_id'         => 1,
          'disctrics_id'       => 1,
          'postcode'          => '10260',
          'phone'             => '08128167890',
          'fax'               => '-',
          'bank'              => 'BCA',
          'bank_account'     => '1222008779',
          'account_holder'    => 'Seller',
          'corporate_name'    => 'PT. Seller',
          'corporate_address' => 'Corporate Address',
          'created_at'        => date('Y-m-d H:i:s'),
          'updated_at'        => date('Y-m-d H:i:s'),
          'remember_token'    => bcrypt('seller2@gmail.com')
      ]);
    }
}
