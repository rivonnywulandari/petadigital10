<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;
use App\Models\AccountModel;
use App\Models\BangunanModel;
use App\Models\CampusModel;
use App\Models\CategoryModel;

class Dashboard extends BaseController
{
    protected $bangunanModel;
    protected $campusModel;
    protected $categoryModel;
  
    protected $accountModel;
    protected $helpers = ['auth'];
    
    public function __construct()
    {
        $this->bangunanModel = new BangunanModel();
        $this->campusModel = new CampusModel();
        $this->categoryModel = new CategoryModel();
     
        $this->accountModel = new AccountModel();
    }
    public function index()
    {
        if (in_groups("admin")) {
            return redirect()->to(base_url('/dashboard/users'));
        }
        return redirect()->to(base_url('/web'));
    }
    
    public function campus()
    {
   
        $contents = [];
        if (in_groups('admin')) {      
            $contents = $this->campusModel->get_list_cp_api()->getResultArray();
        }
        
        $data = [
            'title' => 'Manage Campus',
            'category' => 'Campus',
            'data' => $contents,
        ];
        return view('dashboard/managedata', $data);
    }

    public function bangunan()
    {
       
        $contents = [];
        if (in_groups('admin')) {      
            // $contents = $this->bangunanModel->list_by_admin_api(user()->id)->getResultArray();
            $contents = $this->bangunanModel->get_list_bg_api()->getResultArray();

        }
        
        $data = [
            'title' => 'Manage Bangunan',
            'category' => 'Bangunan',
            'data' => $contents,
        ];
        return view('dashboard/managedata', $data);
    }
  
    
    
    public function category()
    {
        $contents = $this->categoryModel->get_list_cat_api()->getResultArray();
        $data = [
            'title' => 'Manage Category',
            'category' => 'Category',
            'data' => $contents,
        ];
        return view('dashboard/managedata', $data);
    }
    
    public function users()
    {
        $contents = $this->accountModel->get_list_user_api()->getResultArray();
        $data = [
            'title' => 'Manage Users',
            'category' => 'Users',
            'data' => $contents,
        ];
        return view('dashboard/managedata', $data);
    }
    
    public function recommendation()
    {
        $contents = [];
        if (in_groups('admin')) {
            $contents = $this->bangunanModel->get_list_recommendation_api()->getResultArray();
        
        }
        
        $recommendations = $this->bangunanModel->get_recommendation_data_api()->getResultArray();
        $data = [
            'title' => 'Manage Recommendation',
            'category' => 'Recommendation',
            'data' => $contents,
            'recommendations' => $recommendations,
        ];
        return view('dashboard/recommendation', $data);
    }
}
