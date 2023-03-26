<?php

namespace App\Models;

use CodeIgniter\I18n\Time;
use CodeIgniter\Model;

class EventModel extends Model
{
    protected $DBGroup          = 'default';
    protected $table            = 'event';
    protected $primaryKey       = 'id';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'name', 'date_start', 'date_end', 'recurs', 'max_recurs', 'description', 'ticket_price', 'contact_person', 'category_id', 'owner', 'geom', 'video_url', 'date_next', 'calendar'];

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
    public function get_list_ev_api() {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.date_start,{$this->table}.date_end,{$this->table}.recurs,{$this->table}.max_recurs,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.contact_person,{$this->table}.category_id,{$this->table}.owner,{$this->table}.video_url";
        $vilGeom = "village.id = '1' AND ST_Contains(village.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng, category_event.category")
            ->from('village')
            ->where($vilGeom)
            ->join('category_event', 'event.category_id = category_event.id')
            ->get();
        return $query;
    }

    public function list_by_owner_api($id = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.date_start,{$this->table}.date_end,{$this->table}.recurs,{$this->table}.max_recurs,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.contact_person,{$this->table}.category_id,{$this->table}.owner,{$this->table}.video_url";
        $vilGeom = "village.id = '1' AND ST_Contains(village.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('village')
            ->where($vilGeom)
            ->where('owner', $id)
            ->get();
        return $query;
    }

    public function get_ev_by_id_api($id = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.date_start,{$this->table}.date_end,{$this->table}.recurs,{$this->table}.max_recurs,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.contact_person,{$this->table}.category_id,{$this->table}.owner,{$this->table}.video_url";
        $geoJson = "ST_AsGeoJSON({$this->table}.geom) AS geoJson";
        $vilGeom = "village.id = '1' AND ST_Contains(village.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng, {$geoJson}, category_event.category")
            ->from('village')
            ->where('event.id', $id)
            ->where($vilGeom)
            ->join('category_event', 'event.category_id = category_event.id')
            ->get();
        return $query;
    }

    public function get_ev_by_name_api($name = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.date_start,{$this->table}.date_end,{$this->table}.recurs,{$this->table}.max_recurs,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.contact_person,{$this->table}.category_id,{$this->table}.owner,{$this->table}.video_url";
        $vilGeom = "village.id = '1' AND ST_Contains(village.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('village')
            ->like("{$this->table}.name", $name)
            ->where($vilGeom)
            ->get();
        return $query;
    }
    
    public function get_ev_by_radius_api($data = null) {
        $radius = (int)$data['radius'] / 1000;
        $lat = $data['lat'];
        $long = $data['long'];
        $jarak = "(6371 * acos(cos(radians({$lat})) * cos(radians({$this->table}.lat)) * cos(radians({$this->table}.lng) - radians({$long})) + sin(radians({$lat}))* sin(radians({$this->table}.lat))))";
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.date_start,{$this->table}.date_end,{$this->table}.recurs,{$this->table}.max_recurs,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.contact_person,{$this->table}.category_id,{$this->table}.owner,{$this->table}.video_url";
        $vilGeom = "village.id = '1' AND ST_Contains(village.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng, {$jarak} as jarak")
            ->from('village')
            ->where($vilGeom)
            ->having(['jarak <=' => $radius])
            ->get();
        return $query;
    }
    
    public function get_ev_by_category_api($category = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.date_start,{$this->table}.date_end,{$this->table}.recurs,{$this->table}.max_recurs,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.contact_person,{$this->table}.category_id,{$this->table}.owner,{$this->table}.video_url";
        $vilGeom = "village.id = '1' AND ST_Contains(village.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('village')
            ->where("{$this->table}.category_id", $category)
            ->where($vilGeom)
            ->get();
        return $query;
    }
    
    public function get_ev_by_date_api($date = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.date_start,{$this->table}.date_end,{$this->table}.recurs,{$this->table}.max_recurs,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.contact_person,{$this->table}.category_id,{$this->table}.owner,{$this->table}.video_url";
        $vilGeom = "village.id = '1' AND ST_Contains(village.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('village')
            ->where('date_start <=', $date)
            ->where($vilGeom)
            ->get();
        return $query;
    }
    
    public function get_ev_in_id_api($id = null) {
        // $coords = "ST_Y(ST_Centroid({$this->table}.geom)) AS lat, ST_X(ST_Centroid({$this->table}.geom)) AS lng";
        $columns = "{$this->table}.id,{$this->table}.name,{$this->table}.date_start,{$this->table}.date_end,{$this->table}.recurs,{$this->table}.max_recurs,{$this->table}.description,{$this->table}.ticket_price,{$this->table}.contact_person,{$this->table}.category_id,{$this->table}.owner,{$this->table}.video_url";
        $vilGeom = "village.id = '1' AND ST_Contains(village.geom, {$this->table}.geom)";
        $query = $this->db->table($this->table)
            ->select("{$columns}, event.lat, event.lng")
            ->from('village')
            ->whereIn('event.id', $id)
            ->where($vilGeom)
            ->get();
        return $query;
    }

    public function get_new_id_api() {
        $lastId = $this->db->table($this->table)->select('id')->orderBy('id', 'ASC')->get()->getLastRow('array');
        $count = (int)substr($lastId['id'], 1);
        $id = sprintf('E%02d', $count + 1);
        return $id;
    }

    public function add_ev_api($event = null, $geojson = null) {
        $event['created_at'] = Time::now();
        $event['updated_at'] = Time::now();
        $insert = $this->db->table($this->table)
            ->insert($event);
        $update = $this->db->table($this->table)
            ->set('geom', "ST_GeomFromGeoJSON('{$geojson}')", false)
            ->where('id', $event['id'])
            ->update();
        return $insert && $update;
    }

    public function update_ev_api($id = null, $event = null) {
        foreach ($event as $key => $value) {
            if(empty($value)) {
                unset($event[$key]);
            }
        }
        $event['updated_at'] = Time::now();
        $query = $this->db->table($this->table)
            ->where('id', $id)
            ->update($event);
        return $query;
    }
    
}
