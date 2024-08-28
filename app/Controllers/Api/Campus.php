<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\CampusModel;
use CodeIgniter\API\ResponseTrait;

class Campus extends BaseController
{
    use ResponseTrait;
    protected $campusModel;
    public function __construct()
    {
        $this->campusModel = new CampusModel();
    }
    
    public function getData()
    {
        $request = $this->request->getPost();
        $campus = $request['campus'];
        if ($campus == '1') {
            $camProperty = $this->campusModel->get_limau_manis_api()->getRowArray();
            $geoJson = json_decode($this->campusModel->get_geoJson_api($campus)->getRowArray()['geoJson']);
            $content = [
                'type' => 'Feature',
                'geometry' => $geoJson,
                'properties' => [
                    'id' => $camProperty['id'],
                    'name' => $camProperty['name'],
                ]
            ];
            $response = [
                'data' => $content,
                'status' => 200,
                'message' => [
                    "Success display data of Limau Manis "
                ]
            ];
            return $this->respond($response);
        } 
    }
}
