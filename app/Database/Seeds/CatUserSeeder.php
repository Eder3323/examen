<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\CatUserTypeModel;
class CatUserSeeder extends Seeder
{
    public function run()
    {
        $user_type = new CatUserTypeModel();
        $insertdata['user_type'] = 'admin';
        $insertdata['status'] = 1;
        $insertdata['permissions'] = 'login,index,create,update,delete,download-pdf';
        $user_type->insert($insertdata);
        $insertdata['user_type'] = 'user';
        $insertdata['status'] = 1;
        $insertdata['permissions'] = 'login,update,index';
        $user_type->insert($insertdata);
    }
}
