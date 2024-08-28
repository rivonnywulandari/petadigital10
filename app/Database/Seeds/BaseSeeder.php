<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class BaseSeeder extends Seeder
{
    public function run()
    {
        // Account Seed
        $this->call('RoleSeeder');
        $this->call('AccountSeeder');
        $this->call('GroupSeeder');
        $this->call('UserGroupSeeder');
        $this->call('CampusSeeder');

        // Bangunan Seed
        $this->call('CategorySeeder');
        $this->call('RecommendationSeeder');
        $this->call('BangunanSeeder');
        $this->call('GalleryBangunanSeeder');

        // // Culinary Place Seed
        // $this->call('CulinaryPlaceSeeder');
        // $this->call('GalleryCulinaryPlaceSeeder');

        // // Worship Place Seed
        // $this->call('WorshipPlaceSeeder');
        // $this->call('GalleryWorshipPlaceSeeder');

        // // Souvenir Place Seed
        // $this->call('SouvenirPlaceSeeder');
        // $this->call('GallerySouvenirPlaceSeeder');

        // Event Seed
        // $this->call('CategoryEventSeeder');
        // $this->call('EventSeeder');
        // $this->call('GalleryEventSeeder');

        // Other Seed
        // $this->call('VisitHistorySeeder');
        // $this->call('ReviewSeeder');
    }
}
