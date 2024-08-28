<?php

namespace App\Models;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class CampusModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'campus';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'name', 'district', 'geom', 'lat', 'lng'];

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

    // // API
    // public function get_sumpur_api() {
    //     // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
    //     $query = $this->db->table($this->table)
    //         ->select("id, name")
    //         ->where('id', '1')
    //         ->get();
    //     return $query;
    // }
    
    // public function get_desa_wisata_api() {
    //     // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
    //     $query = $this->db->table($this->table)
    //         ->select("id, name")
    //         ->where('id', '2')
    //         ->get();
    //     return $query;
    // }

     public function get_limau_manis_api() {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $query = $this->db->table($this->table)
            ->select("id, name")
            ->where('id', '1')
            ->get();
        return $query;
    }
    
    public function get_list_cp_api()
    {
        // $query = $this->db->table($this->table)
        //         ->select("id, name")
        //         ->get();

        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.district";
        $query = $this->db->table($this->table)
            ->select("{$columns}, {$this->table}.lat, {$this->table}.lng")
            ->get();

        return $query;
    }

    public function get_cp_by_id_api($id = null)
    {
        //$coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.district";
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $query = $this->db->table($this->table)
            ->select("{$columns}, {$this->table}.lat, {$this->table}.lng,{$geoJson}")
            ->where("{$this->table}.id", $id)
            ->get();
        return $query;
    }

    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id')->orderBy('id', 'ASC')->get()->getLastRow('array');
        if (!$lastId) {
            $id = '1';
        } else {
            $count = (int)substr($lastId['id'], 1);
            $id = sprintf('%2d', $count + 1);
        }
        return $id;
    }

    public function add_cp_api($campus = null, $geojson = null)
    {
        $campus['created_at'] = Time::now();
        $campus['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($campus);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id', $campus['id'])
            ->update();
        return $insert && $update;
    }

    public function update_cp_api($id = null, $campus = null, $geojson = null)
    {
        $campus['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id', $id)
            ->update($campus);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id', $id)
            ->update();
        return $query && $update;
    }


    public function get_geoJson_api($id = null) {
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $query = $this->db->table($this->table)
            ->select("{$geoJson}")
            ->where('id', $id)
            ->get();
        return $query;
    }
}
