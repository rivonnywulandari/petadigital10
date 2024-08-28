<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;
use CodeIgniter\I18n\Time;

class Bangunan extends Migration
{
    public function up()
    {
        $fields = [
            'id' => [
                'type' => 'VARCHAR',
                'constraint' => 5,
                'unique' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 225,
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 225,
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
            'category_id' => [
                'type' => 'VARCHAR',
                'constraint' => 2,
                'null' => true,
            ],
            'recom' => [
                'type' => 'VARCHAR',
                'constraint' => 1,
                'default' => '2',
                'null' => true,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'video_url' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->addForeignKey('category_id', 'category', 'id', 'CASCADE', 'SET NULL');
        $this->forge->addForeignKey('recom', 'recommendation', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('bangunan');
        $this->db->enableForeignKeyChecks();
    }

    public function down()
    {
        $this->forge->dropTable('bangunan');
    }
}
