<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Campus extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'unique' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
            ],
            'district' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'null' => true,
            ],
            'lat' => [
                'type' => 'DECIMAL',
                'constraint' => '10,8',
            ],
            'lng' => [
                'type' => 'DECIMAL',
                'constraint' => '11,8',
            ],
            'geom' => [
                'type' => 'GEOMETRY',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ];

        $this->db->disableForeignKeyChecks();
        $this->forge->addField($fields);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('campus');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('campus');
    }
}
