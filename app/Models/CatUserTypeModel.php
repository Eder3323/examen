<?php

namespace App\Models;

use CodeIgniter\Model;

class CatUserTypeModel extends Model
{
    protected $table            = 'catusertypes';
    protected $primaryKey       = 'user_type';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['user_type','status','permissions'];
    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

}
