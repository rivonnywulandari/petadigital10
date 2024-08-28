<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class BangunanSeeder extends Seeder
{
    public function run()
    {

        $rows = array_map('str_getcsv', file(WRITEPATH.'seeds/'. 'bangunan.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id' => $row[0],
                'name' => $row[1],
                'address' => $row[2],
                'lat' => $row[3],
                'lng' => $row[4],
                'category_id' => $row[6],
                'recom' => $row[7],
                'description' => $row[8],
                'video_url' => $row[9],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('bangunan')->insert($data);
            $this->db->table('bangunan')->set('geom', $row[5], false)->where('id', $row[0])->update();
        }

    }
}
