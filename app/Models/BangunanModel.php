<?php

namespace App\Models;

use CodeIgniter\Database\ConnectionInterface;
use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class BangunanModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table = 'bangunan';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields = ['id', 'name', 'address', 'geom', 'category_id', 'recom', 'description', 'video_url', 'lat', 'lng'];

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
    public function get_recommendation_api()
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $query = $this->db->table($this->table)
            ->select("bangunan.id, bangunan.category_id, bangunan.name, bangunan.lat, bangunan.lng")
            ->from('campus')
            ->where('recom', '1')
            ->get();
        return $query;
    }

    public function get_list_recommendation_api()
    {
        //$coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $query = $this->db->table($this->table)
            ->select("bangunan.id, bangunan.category_id, bangunan.name, recom, recommendation.name as recommendation, bangunan.lat, bangunan.lng")
            ->from('campus')
            ->join('recommendation', 'bangunan.recom = recommendation.id')
            ->get();
        return $query;
    }

    public function get_recommendation_data_api()
    {
        $query = $this->db->table('recommendation')
            ->select("recommendation.id, recommendation.name,")
            ->get();
        return $query;
    }

    public function recommendation_by_owner_api($id = null)
    {
        //$coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $query = $this->db->table($this->table)
            ->select("bangunan.id, bangunan.category_id, bangunan.name, recom, recommendation.name as recommendation, bangunan.lat, bangunan.lng")
            ->from('campus')
            ->join('recommendation', 'bangunan.recom = recommendation.id')
            ->get();
        return $query;
    }

    public function get_list_bg_api()
    {
        //$coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.address,{$this->table}.description,{$this->table}.category_id,{$this->table}.video_url";
        $query = $this->db->table($this->table)
            ->select("{$columns}, bangunan.lat, bangunan.lng")
            ->from('campus')
            ->get();
        return $query;
    }

    public function list_by_admin_api($id = null)
    {
        //$coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.address,{$this->table}.description,{$this->table}.category_id,{$this->table}.video_url";
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $query = $this->db->table($this->table)
            ->select("{$columns}, bangunan.lat, bangunan.lng, {$geoJson}")
            ->from('campus')
            ->get();
        return $query;
    }

    public function list_by_category_api()
    {
        //$coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.address,{$this->table}.description,{$this->table}.category_id,{$this->table}.video_url";
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $query = $this->db->table($this->table)
            ->select("{$columns}, bangunan.lat, bangunan.lng, {$geoJson}")
            ->from('campus')
            ->get();
        return
         $query;
    }

    
    public function get_bg_by_id_api($id = null)
    {
        //$coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.address,{$this->table}.description,{$this->table}.category_id,{$this->table}.video_url";
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $query = $this->db->table($this->table)
            ->select("{$columns}, bangunan.lat, bangunan.lng, {$geoJson}, category.category")
            ->from('campus')
            ->where('bangunan.id', $id)
            ->join('category', 'bangunan.category_id = category.id')
            ->get();
        return $query;
    }

    public function get_bg_by_name_api($name = null)
    {
        //$coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.address,{$this->table}.description,{$this->table}.category_id,{$this->table}.video_url";
        $query = $this->db->table($this->table)
            ->select("{$columns}, bangunan.lat, bangunan.lng")
            ->from('campus')
            ->like("{$this->table}.name", $name)
            ->get();
        return $query;
    }

    public function get_bg_by_category_api($category = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.category_id,{$this->table}.name,{$this->table}.address,{$this->table}.description,{$this->table}.category_id,{$this->table}.video_url";
        $query = $this->db->table($this->table)
            ->select("{$columns}, bangunan.lat, bangunan.lng")
            ->from('campus')
            ->where("{$this->table}.category_id", $category)
            ->get();
        return $query;
    }

   
    public function get_bg_in_id_api($id = null)
    {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.address,{$this->table}.description,{$this->table}.category_id,{$this->table}.video_url";
        $query = $this->db->table($this->table)
            ->select("{$columns}, bangunan.lat, bangunan.lng")
            ->from('campus')
            ->whereIn('bangunan.id', $id)
            ->get();
        return $query;
    }

    public function get_new_id_api()
    {
        $lastId = $this->db->table($this->table)->select('id')->orderBy('id', 'ASC')->get()->getLastRow('array');
        if (!$lastId) {
            $id = 'B0001';
        } else {
            $count = (int)substr($lastId['id'], 1);
            $id = sprintf('B%04d', $count + 1);
        }
        return $id;
    }

    public function add_bg_api($bangunan = null, $geojson = null)
    {
        $bangunan['created_at'] = Time::now();
        $bangunan['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($bangunan);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id', $bangunan['id'])
            ->update();
        return $insert && $update;
    }

    public function update_bg_api($id = null, $bangunan = null, $geojson = null)
    {
        $bangunan['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id', $id)
            ->update($bangunan);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id', $id)
            ->update();
        return $query && $update;
    }

    public function update_recom_api($data = null)
    {
        $query = false;
        $bangunan['recom'] = $data['recom'];
        $bangunan['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id', $data['id'])
            ->update($bangunan);
        return $query;
    }
}
