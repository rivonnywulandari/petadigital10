<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class GalleryBangunanSeeder extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'gallery_bangunan.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id' => $row[0],
                'bangunan_id' => $row[1],
                'url' => $row[2],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('gallery_bangunan')->insert($data);
        }
    }
}
