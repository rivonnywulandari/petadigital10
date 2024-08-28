<?php

namespace App\Controllers\Api;

use App\Models\GalleryBangunanModel;
use App\Models\BangunanModel;
use App\Models\CategoryModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Bangunan extends ResourceController
{
    use ResponseTrait;

    protected $bangunanModel;
    protected $categoryModel;
    protected $galleryBangunanModel;

    public function __construct()
    {
        $this->bangunanModel = new BangunanModel();
        $this->categoryModel = new CategoryModel();
        $this->galleryBangunanModel = new GalleryBangunanModel();
    }

    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $bangunan = array();
        $contents = $this->bangunanModel->get_list_bg_api()->getResult();
        foreach ($contents as $content){
            $list_gallery = $this->galleryBangunanModel->get_gallery_api($content->id)->getResultArray();
            $galleries = array();
            foreach ($list_gallery as $gallery) {
                $galleries[] = $gallery['url'];
            }
            $content->gallery = $galleries[0];
            $bangunan[] = $content;
        }
        $response = [
            'data' => $bangunan,
            'status' => 200,
            'message' => [
                "Success get list of Bangunan"
            ]
        ];
        return $this->respond($response);
    }

    /**
     * Return the properties of a resource object
     *
     * @return mixed
     */
    public function show($id = null)
    {
        $bangunan = $this->bangunanModel->get_bg_by_id_api($id)->getRowArray();

        $list_gallery = $this->galleryBangunanModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }
      
        $bangunan['gallery'] = $galleries;
      
        $response = [
            'data' => $bangunan,
            'status' => 200,
            'message' => [
                "Success display detail information of Bangunan"
            ]
        ];
        return $this->respond($response);

    }

    /**
     * Return a new resource object, with default properties
     *
     * @return mixed
     */
    public function new()
    {
        //
    }

    /**
     * Create a new resource object, from "posted" parameters
     *
     * @return mixed
     */
    public function create()
    {
        $request = $this->request->getJSON(true);
        $id = $this->bangunanModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'name' => $request['name'],
            'address' => $request['address'],           
            'category_id' => $request['category'],
            'recom' => $request['recom'],
            'description' => $request['description'],
            'video_url' => $request['video_url'],
        ];
        foreach ($requestData as $key => $value) {
            if(empty($value)) {
                unset($requestData[$key]);
            }
        }
        $geojson = $request['geojson'];
        $addBG = $this->bangunanModel->add_bg_api($requestData, $geojson);       
        $gallery = $request['gallery'];
        $addGallery = $this->galleryBangunanModel->add_gallery_api($id, $gallery);
        if($addBG && $addGallery) {
            $response = [
                'status' => 201,
                'message' => [
                    "Success create new Bangunan"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail create new Bangunan",
                    "Add Bangunan: {$addBG}",
                    "Add Gallery: {$addGallery}",
                ]
            ];
            return $this->respond($response, 400);
        }
    }

    /**
     * Return the editable properties of a resource object
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        //
    }

    /**
     * Add or update a model resource, from "posted" properties
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $request = $this->request->getJSON(true);
        $requestData = [
            'name' => $request['name'],
            'address' => $request['address'],           
            'category_id' => $request['category'],
            'recom' => $request['recom'],
            'description' => $request['description'],
            'video_url' => $request['video_url'],
        ];
        $geojson = $request['geojson'];
        $updateBG = $this->bangunanModel->update_bg_api($id, $requestData, $geojson);       
        $gallery = $request['gallery'];
        $updateGallery = $this->galleryBangunanModel->update_gallery_api($id, $gallery);
        if($updateBG && $updateGallery) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success update Bangunan"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update Bangunan",
                    "Update Bangunan: {$updateBG}",
                    "Update Gallery: {$updateGallery}",
                ]
            ];
            return $this->respond($response, 400);
        }
    }

    /**
     * Delete the designated resource object from the model
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $deleteBG = $this->bangunanModel->delete(['id' => $id]);
        if($deleteBG) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete Bangunan"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "Bangunan not found"
                ]
            ];
            return $this->failNotFound($response);
        }
    }

    public function recommendation()
    {

        $contents = $this->bangunanModel->get_recommendation_api()->getResultArray();
        for ($index = 0; $index < count($contents); $index++) {
            $list_gallery = $this->galleryBangunanModel->get_gallery_api($contents[$index]['id'])->getResultArray();
            $galleries = array();
            foreach ($list_gallery as $gallery) {
                $galleries[] = $gallery['url'];
            }
            $contents[$index]['gallery'] = $galleries;
        }

        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of recommended Bangunan"
            ]
        ];
        return $this->respond($response);
    }

 
    
    public function recommendationList()
    {
        $contents = $this->bangunanModel->get_recommendation_data_api()->getResultArray();
        
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of recommendation"
            ]
        ];
        return $this->respond($response);
    }

    public function updateRecommendation() {
        $request = $this->request->getPost();
        $requestData = [
            'id' => $request['id'],
            'recom' => $request['recom']
        ];
        $updateRecom = $this->bangunanModel->update_recom_api($requestData);
        if($updateRecom) {
            $response = [
                'status' => 201,
                'message' => [
                    "Success update Bangunan Recommendation"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update Bangunan Recommendation",
                    "Update Bangunan Recommendation: {$updateRecom}",
                ]
            ];
            return $this->respond($response, 400);
        }
    }

    public function findByName()
    {
        $request = $this->request->getPost();
        $name = $request['name'];
        $contents = $this->bangunanModel->get_bg_by_name_api($name)->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Bangunan by name"
            ]
        ];
        return $this->respond($response);
    }

   
    
    public function findByRadius()
    {
        $request = $this->request->getPost();
        $contents = $this->bangunanModel->get_bg_by_radius_api($request)->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find Bangunan by radius"
            ]
        ];
        return $this->respond($response);
    }
    
    public function findByCategory()
    {
        $request = $this->request->getPost();
        $category = $request['category'];
        $contents = $this->bangunanModel->get_bg_by_category_api($category)->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success find bangunan by category"
            ]
        ];
        return $this->respond($response);
    }

   
    public function category() {
        $contents = $this->categoryModel->get_list_cat_api()->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of category or category"
            ]
        ];
        return $this->respond($response);
    }

    public function listByAdmin() {
        $request = $this->request->getPost();
        $contents = $this->bangunanModel->list_by_admin_api($request['id'])->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of Bangunan"
            ]
        ];
        return $this->respond($response);
    }
}
