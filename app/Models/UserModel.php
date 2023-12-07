<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = ['name','last_name','phone','email','picture','password','id_user_type','last_login'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

//    public function create()    {
//        $movie = new MovieModel();
//        $category = new CategoryModel();
//        if ($this->validate('movies')) {
//            if (!$this->request->getPost('category_id'))
//                return $this->genericResponse(NULL, array("category_id" => "Categoría no existe"), 500);
//            if (!$category->get($this->request->getPost('category_id'))) {
//                return $this->genericResponse(NULL, array("category_id" => "Categoría no existe"), 500);            }
//            $id = $movie->insert([                'title' => $this->request->getPost('title'),                'description' => $this->request->getPost('description'),                'category_id' => $this->request->getPost('category_id'),            ]);            return $this->genericResponse($this->model->find($id), NULL, 200);        }
//        $validation = \Config\Services::validation();
//        return $this->genericResponse(NULL, $validation->getErrors(), 500);
//    }
}
