<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCatUserType extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_type' => [
                'type' => 'VARCHAR',
                'unique' => true,
                'constraint' => '255',
            ],
            'status' => [
                'type' => 'VARCHAR',
                'constraint' => '2',
            ],
            'permissions' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true
            ]
        ]);
        $this->forge->addPrimaryKey('user_type');
        $this->forge->createTable('catusertypes');
    }

    public function down()
    {
        $this->forge->dropTable('catusertypes');
    }
}
