<?php

namespace App\Controllers\Web;

use App\Models\GalleryBangunanModel;
use App\Models\BangunanModel;
use App\Models\CategoryModel;
use CodeIgniter\Files\File;
use CodeIgniter\RESTful\ResourcePresenter;

class Bangunan extends ResourcePresenter
{
    protected $bangunanModel;
    protected $categoryModel;
    protected $galleryBangunanModel;

    protected $helpers = ['auth', 'url', 'filesystem'];

    public function __construct()
    {
        $this->bangunanModel = new BangunanModel();
        $this->categoryModel = new CategoryModel();
        $this->galleryBangunanModel = new GalleryBangunanModel();
    }

    /**
     * Present a view of resource objects
     *
     * @return mixed
     */
    public function index()
    {
        $contents = $this->bangunanModel->get_list_bg_api()->getResultArray();
        $data = [
            'title' => 'Bangunan',
            'data' => $contents,
        ];

        return view('web/list_bangunan', $data);
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
       $bangunan = $this->bangunanModel->get_bg_by_id_api($id)->getRowArray();
        if (empty($bangunan)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }

        $list_gallery = $this->galleryBangunanModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }
       
       $bangunan['gallery'] = $galleries;

        $data = [
            'title' =>$bangunan['name'],
            'data' =>$bangunan,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_bangunan', $data);
        }
        return view('web/detail_bangunan', $data);
    }

    /**
     * Present a view to present a new single resource object
     *
     * @return mixed
     */
    public function new()
    {
        $categories = $this->categoryModel->get_list_cat_api()->getResultArray();
        $data = [
            'title' => 'New Bangunan',
            'categories' => $categories
        ];
        return view('dashboard/bangunan_form', $data);
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
        $id = $this->bangunanModel->get_new_id_api();
        $requestData = [
            'id' => $id,
            'name' => $request['name'],
            'address' => $request['address'],            
            'category_id' => $request['category'],
            'description' => $request['description'],
            'lat' => $request['lat'],
            'lng' => $request['lng'],
        ];
        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }
        $geojson = $request['geo-json'];
        if (isset($request['video'])) {
            $folder = $request['video'];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filenames = get_filenames($filepath);
            $vidFile = new File($filepath . '/' . $filenames[0]);
            $vidFile->move(FCPATH . 'media/videos');
            delete_files($filepath);
            rmdir($filepath);
            $requestData['video_url'] = $vidFile->getFilename();
        }
        $addBG = $this->bangunanModel->add_bg_api($requestData, $geojson);

      
        if (isset($request['gallery'])) {
            $folders = $request['gallery'];
            $gallery = array();
            foreach ($folders as $folder) {
                $filepath = WRITEPATH . 'uploads/' . $folder;
                $filenames = get_filenames($filepath);
                $fileImg = new File($filepath . '/' . $filenames[0]);
                $fileImg->move(FCPATH . 'media/photos');
                delete_files($filepath);
                rmdir($filepath);
                $gallery[] = $fileImg->getFilename();
            }
            $this->galleryBangunanModel->add_gallery_api($id, $gallery);
        }

        if ($addBG) {
            return redirect()->to(base_url('dashboard/bangunan') . '/' . $id);
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
       $bangunan = $this->bangunanModel->get_bg_by_id_api($id)->getRowArray();
        if (empty($bangunan)) {
            return redirect()->to('dashboard/bangunan');
        }
       

        $list_gallery = $this->galleryBangunanModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }

        $categories = $this->categoryModel->get_list_cat_api()->getResultArray();


       $bangunan['gallery'] = $galleries;
        $data = [
            'title' => 'Edit Bangunan',
            'data' =>$bangunan,
            'categories' => $categories

        ];
        return view('dashboard/bangunan_form', $data);
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
            'address' => $request['address'],            
            'category_id' => $request['category'],
            'description' => $request['description'],
            'lat' => $request['lat'],
            'lng' => $request['lng'],
        ];
        foreach ($requestData as $key => $value) {
            if (empty($value)) {
                unset($requestData[$key]);
            }
        }
        $geojson = $request['geo-json'];
        if (isset($request['video'])) {
            $folder = $request['video'];
            $filepath = WRITEPATH . 'uploads/' . $folder;
            $filenames = get_filenames($filepath);
            $vidFile = new File($filepath . '/' . $filenames[0]);
            $vidFile->move(FCPATH . 'media/videos');
            delete_files($filepath);
            rmdir($filepath);
            $requestData['video_url'] = $vidFile->getFilename();
        } else {
            $requestData['video_url'] = null;
        }
        $updateBG = $this->bangunanModel->update_bg_api($id, $requestData, $geojson);

       
        if (isset($request['gallery'])) {
            $folders = $request['gallery'];
            $gallery = array();
            foreach ($folders as $folder) {
                $filepath = WRITEPATH . 'uploads/' . $folder;
                $filenames = get_filenames($filepath);
                $fileImg = new File($filepath . '/' . $filenames[0]);
                $fileImg->move(FCPATH . 'media/photos');
                delete_files($filepath);
                rmdir($filepath);
                $gallery[] = $fileImg->getFilename();
            }
            $this->galleryBangunanModel->update_gallery_api($id, $gallery);
        } else {
            $this->galleryBangunanModel->delete_gallery_api($id);
        }

        if ($updateBG) {
            return redirect()->to(base_url('dashboard/bangunan') . '/' . $id);
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
        $deleteBG = $this->bangunanModel->delete(['id' => $id]);
        
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->bangunanModel->get_list_bg_api()->getResultArray();
        } elseif (in_groups('owner')) {
            $contents = $this->bangunanModel->list_by_admin_api(user()->id)->getResultArray();
            // $contents = $this->bangunanModel->get_list_bg_api()->getResultArray();

        }
        
        $data = [
            'title' => 'Manage Bangunan',
            'category' => 'Bangunan',
            'data' => $contents,
        ];
        return view('dashboard/managedata', $data);
       
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
        $data = [
            'title' => 'Home',
            'data' => $contents,
        ];

        return view('web/recommendation', $data);
    }

    public function maps()
    {
        $contents = $this->bangunanModel->get_list_bg_api()->getResultArray();
        $data = [
            'title' => 'Bangunan',
            'data' => $contents,
        ];

        return view('maps/bangunan', $data);
    }

    public function detail($id = null)
    {
       $bangunan = $this->bangunanModel->get_bg_by_id_api($id)->getRowArray();
        if (empty($bangunan)) {
            return redirect()->to(substr(current_url(), 0, -strlen($id)));
        }

        $list_gallery = $this->galleryBangunanModel->get_gallery_api($id)->getResultArray();
        $galleries = array();
        foreach ($list_gallery as $gallery) {
            $galleries[] = $gallery['url'];
        }
   
       $bangunan['gallery'] = $galleries;

        $data = [
            'title' =>$bangunan['name'],
            'data' =>$bangunan,
        ];

        if (url_is('*dashboard*')) {
            return view('dashboard/detail_bangunan', $data);
        }
        return view('maps/detail_bangunan', $data);
    }
}
