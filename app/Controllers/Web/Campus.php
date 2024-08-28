<?php

namespace App\Controllers\Web;

use App\Models\CampusModel;
use CodeIgniter\Files\File;
use CodeIgniter\RESTful\ResourcePresenter;

class Campus extends ResourcePresenter
{
    protected $campusModel;
  
    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->campusModel = new CampusModel();
      
    }

    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->campusModel->get_list_cp_api()->getResultArray();
        $data = [
            'title' => 'Campus',
            'data' => $contents,
        ];

        return view('web/list_campus', $data);
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
       $campus = $this->campusModel->get_cp_by_id_api($id)->getRowArray();
        if (empty($campus)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }

        $data = [
            'title' =>$campus['name'],
            'data' =>$campus,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_campus', $data);
        }
        return view('web/detail_campus', $data);
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $data = [
            'title' => 'New Campus'
        ];
        return view('dashboard/campus_form', $data);
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
        $id = $this->campusModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'name' => $request['name'],
            'district' => $request['district'],
            'lat' => $request['lat'],
            'lng' => $request['lng'],
        ];
        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }
        $geojson = $request['geo-json'];
      
        $addCP = $this->campusModel->add_cp_api($requestData, $geojson);
     
       

        if ($addCP) {
            return redirect()->to(base_url('dashboard/campus') . '/' . $id);
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
       $campus = $this->campusModel->get_cp_by_id_api($id)->getRowArray();
        if (empty($campus)) {
            return redirect()->to('dashboard/campus');
        }
            

        $data = [
            'title' => 'Edit Campus',
            'data' =>$campus

        ];
        return view('dashboard/campus_form', $data);
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
            'id' => $id,
            'name' => $request['name'],
            'district' => $request['district'],            
            'lat' => $request['lat'],
            'lng' => $request['lng'],
        ];
        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }
        $geojson = $request['geo-json'];
       
        $updateCP = $this->campusModel->update_cp_api($id, $requestData, $geojson);

            

        if ($updateCP) {
            return redirect()->to(base_url('dashboard/campus') . '/' . $id);
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
    //     //
    // }

    public function delete($id = null)
    {
        $deleteCP = $this->campusModel->delete(['id' => $id]);
        
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->campusModel->get_list_cp_api()->getResultArray();
        } 
        
        $data = [
            'title' => 'Manage Bangunan',
            'category' => 'Bangunan',
            'data' => $contents,
        ];
        return view('dashboard/managedata', $data);
       
    }

    

    public function maps()
    {
        $contents = $this->campusModel->get_list_cp_api()->getResultArray();
        $data = [
            'title' => 'Campus',
            'data' => $contents,
        ];

        return view('maps/campus', $data);
    }

    public function detail($id = null)
    {
       $campus = $this->campusModel->get_cp_by_id_api($id)->getRowArray();
        if (empty($campus)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }

      
        $data = [
            'title' =>$campus['name'],
            'data' =>$campus,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_campus', $data);
        }
        return view('maps/detail_campus', $data);
    }
}
