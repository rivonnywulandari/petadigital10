<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class GalleryRumahGadangSeeder extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'gallery_rumah_gadang.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id' => $row[0],
                'rumah_gadang_id' => $row[1],
                'url' => $row[2],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('gallery_rumah_gadang')->insert($data);
        }
    }
}
