<?php

namespace App\Controllers\Api;

use App\Models\CategoryModel;
use CodeIgniter\API\ResponseTrait;
use CodeIgniter\RESTful\ResourceController;

class Category extends ResourceController
{
    use ResponseTrait;
    
    protected $categoryModel;
    
    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }
    
    /**
     * Return an array of resource objects, themselves in array format
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->categoryModel->get_list_cat_api()->getResult();
        $response = [
            'data' => $contents,
            'status' => 200,
            'message' => [
                "Success get list of category"
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
        //
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
        $request = $this->request->getPost();
        $id = $this->categoryModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'category' => $request['category'],
        ];
        $addCAT = $this->categoryModel->add_cat_api($requestData);
        if ($addCAT) {
            $response = [
                'status' => 201,
                'message' => [
                    "Success create new category"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail create new category",
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
        $request = $this->request->getRawInput();
        $requestData = [
            'category' => $request['category'],
        ];
        $updateCAT = $this->categoryModel->update_cat_api($id, $requestData);
        if ($updateCAT) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success update category"
                ]
            ];
            return $this->respondCreated($response);
        } else {
            $response = [
                'status' => 400,
                'message' => [
                    "Fail update category",
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
        $deleteCAT = $this->categoryModel->delete(['id' => $id]);
        if($deleteCAT) {
            $response = [
                'status' => 200,
                'message' => [
                    "Success delete category"
                ]
            ];
            return $this->respondDeleted($response);
        } else {
            $response = [
                'status' => 404,
                'message' => [
                    "type not found"
                ]
            ];
            return $this->failNotFound($response);
        }
    }
}
