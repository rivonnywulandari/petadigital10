<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\I18n\Time;

class CampusSeeder extends Seeder
{
    public function run()
    {
        $rows = array_map('str_getcsv', file(WRITEPATH . 'seeds/' . 'campus.csv'));
        $header = array_shift($rows);

        foreach ($rows as $row) {
            $data = [
                'id' => $row[0],
                'name' => $row[1],
                'district' => $row[2],
                'lat' => $row[3],
                'lng' => $row[4],
                'created_at' => Time::now(),
                'updated_at' => Time::now(),
            ];

            $this->db->table('campus')->insert($data);
            $this->db->table('campus')->set('geom', $row[5], false)->where('id', $row[0])->update();
        }
    }
}
