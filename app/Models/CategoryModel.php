<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'category';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'category'];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

       // API
    public function get_list_cat_api() {
        $query = $this->db->table($this->table)
            ->select('id, category')
            ->get();
        return $query;
    }
    
    public function get_cat_by_id_api($id = null) {
        $query = $this->db->table($this->table)
            ->select('id, category')
            ->where('id', $id)
            ->get();
        return $query;
    }
    
    public function get_new_id_api() {
        $lastId = $this->db->table($this->table)->select('id')->orderBy('id', 'ASC')->get()->getLastRow('array');
        if (!$lastId) {
            $id = '01';
        } else {
            $count = (int)substr($lastId['id'], 1);
            $id = sprintf('%02d', $count + 1);
        }       
        return $id;
    }
    
    public function add_cat_api($category = null) {
        foreach ($category as $key => $value) {
            if(empty($value)) {
                unset($category[$key]);
            }
        }
        $category['created_at'] = Time::now();
        $category['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($category);
        return $insert;
    }
    
    public function update_cat_api($id = null, $category = null) {
        foreach ($category as $key => $value) {
            if(empty($value)) {
                unset($category[$key]);
            }
        }
        $category['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id', $id)
            ->update($category);
        return $query;
    }
}
