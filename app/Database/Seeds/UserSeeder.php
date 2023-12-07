<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use App\Models\UserModel;
use CodeIgniter\Files\File;
class UserSeeder extends Seeder
{
    public function run()
    {
        $user = new UserModel();
        $insertdata['name'] = 'Jesus';
        $insertdata['last_name'] = 'Sanchez Arellano';
        $insertdata['phone'] = '551111111';
        $insertdata['email'] = 'usuario@correo.com';
        $insertdata['password'] = password_hash('password', PASSWORD_DEFAULT);
        $insertdata['picture'] = '/images/user.png';
        $insertdata['id_user_type'] = 'user';
        $user->insert($insertdata);

        $insertdata['name'] = 'Jose de Jesus';
        $insertdata['last_name'] = 'Sanchez Arellano';
        $insertdata['phone'] = '551111112';
        $insertdata['email'] = 'admin@correo.com';
        $insertdata['password'] = password_hash('password', PASSWORD_DEFAULT);
        $insertdata['picture'] ='/images/user2.png';
        $insertdata['id_user_type'] = 'admin';
        $user->insert($insertdata);
    }
}
