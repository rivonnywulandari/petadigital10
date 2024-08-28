<?php

namespace App\Controllers\Web;

use App\Models\CategoryModel;
use CodeIgniter\RESTful\ResourcePresenter;

class Category extends ResourcePresenter
{
    protected $categoryModel;
    
    protected $helpers = ['auth', 'url', 'filesystem'];
    
    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }
    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    public function index()
    {
        //
    }

    /**
     * Present a view to present a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function show($id = null)
    {
       $category = $this->categoryModel->get_cat_by_id_api($id)->getRowArray();
        if (empty($category)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }
        
        $data = [
            'title' =>$category['category'],
            'data' =>$category,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_category', $data);
        }
        return view('web/detail_category', $data);
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $id = $this->categoryModel->get_new_id_api();
        $data = [
            'title' => 'New category',
            'id' => $id
        ];
        return view('dashboard/category_form', $data);
    }

    /**
     * Process the creation/insertion of a new resource object.
     * This should be a POST.
     *
     * @return mixed
     */
    public function create()
    {
        $request = $this->request->getPost();
        $requestData = [
            'id' => $request['id'],
            'category' => $request['category'],
        ];
        $addCAT = $this->categoryModel->add_cat_api($requestData);
        if ($addCAT) {
            return redirect()->to(base_url('dashboard/category'));
        } else {
            return redirect()->back()->withInput();
        }
    }

    /**
     * Present a view to edit the properties of a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        $category = $this->categoryModel->get_cat_by_id_api($id)->getRowArray();
        $data = [
            'title' => 'Edit category',
            'data' => $category
        ];
        return view('dashboard/category_form', $data);
        
    }

    /**
     * Process the updating, full or partial, of a specific resource object.
     * This should be a POST.
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function update($id = null)
    {
        $request = $this->request->getPost();
        $requestData = [
            'category' => $request['category'],
        ];
        $updateCAT = $this->categoryModel->update_cat_api($id, $requestData);
        if ($updateCAT) {
            return redirect()->to(base_url('dashboard/category'));
        } else {
            return redirect()->back()->withInput();
        }
    }

    /**
     * Present a view to confirm the deletion of a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    public function remove($id = null)
    {
        //
    }

    /**
     * Process the deletion of a specific resource object
     *
     * @param mixed $id
     *
     * @return mixed
     */
    // public function delete($id = null)
    // {
    //     $deleteCAT = $this->categoryModel->delete(['id' => $id]);
        
    //     $contents = [];
    //     if (in_groups('admin')) {
    //         $contents = $this->categoryModel->get_list_cat_api()->getResultArray();
    //     } elseif (in_groups('owner')) {
    //         $contents = $this->categoryModel->list_by_admin_api(user()->id)->getResultArray();
    //         // $contents = $this->bangunanModel->get_list_bg_api()->getResultArray();

    //     }
        
    //     $data = [
    //         'title' => 'Manage Category',
    //         'category' => 'Category',
    //         'data' => $contents,
    //     ];
    //     return view('dashboard/managedata', $data);
       
    // }
}
