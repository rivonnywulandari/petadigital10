<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
//$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/api/dbCheck', 'Home::dbCheck');
$routes->get('/', 'Web\Dashboard::index');
$routes->get('/403', 'Home::error403');
$routes->get('/login', 'Web\Profile::login');
$routes->get('/register', 'Web\Profile::register');

// Upload files
$routes->group('upload', ['namespace' => 'App\Controllers\Web'], function($routes) {
    $routes->post('photo', 'Upload::photo');
    $routes->post('video', 'Upload::video');
    $routes->post('avatar', 'Upload::avatar');
    $routes->delete('avatar', 'Upload::remove');
    $routes->delete('photo', 'Upload::remove');
    $routes->delete('video', 'Upload::remove');
});

// App
$routes->group('web', ['namespace' => 'App\Controllers\Web'], function($routes) {
     
    $routes->presenter('bangunan');
    $routes->get('bangunan/maps', 'Bangunan::maps');
    $routes->get('bangunan/detail/(:segment)', 'Bangunan::detail/$1');
    $routes->get('/', 'Bangunan::recommendation');
    
    $routes->presenter('category');
    $routes->get('category/detail/(:segment)', 'Category::detail/$1');

    
    // Profile
    $routes->group('profile', function ($routes) {
        $routes->get('/', 'Profile::profile', ['filter' => 'login']);
        $routes->get('changePassword', 'Profile::changePassword', ['filter' => 'login']);
        $routes->post('changePassword', 'Profile::changePassword', ['filter' => 'login']);
        $routes->get('update', 'Profile::updateProfile', ['filter' => 'login']);
        $routes->post('update', 'Profile::update', ['filter' => 'login']);
    });
});

// Dashboard
$routes->group('dashboard', ['namespace' => 'App\Controllers\Web', 'filter' => 'role:admin,owner'], function($routes) {
    $routes->get('/', 'Dashboard::index');

    $routes->get('bangunan', 'Dashboard::bangunan',  ['filter' => 'role:admin']);
    $routes->delete('bangunan/delete/(:segment)', 'Bangunan::delete/$1', ['filter' => 'role:admin']);
    

    $routes->get('campus', 'Dashboard::campus',  ['filter' => 'role:admin']);
    $routes->delete('campus/delete/(:segment)', 'Campus::delete/$1', ['filter' => 'role:admin']);

   
    $routes->get('category', 'Dashboard::category', ['filter' => 'role:admin']);
    $routes->post('category/edit/(:segment)', 'Category::update/$1', ['filter' => 'role:admin']);
    $routes->delete('category/delete/(:segment)', 'Category::delete/$1', ['filter' => 'role:admin']);

    $routes->get('recommendation', 'Dashboard::recommendation',  ['filter' => 'role:admin']);
    $routes->get('users', 'Dashboard::users', ['filter' => 'role:admin']);
    
    $routes->presenter('bangunan',  ['filter' => 'role:admin']);
    $routes->presenter('campus',  ['filter' => 'role:admin']);

    $routes->presenter('category', ['filter' => 'role:admin']);
    $routes->presenter('users', ['filter' => 'role:admin']);
});

// API
$routes->group('api', ['namespace' => 'App\Controllers\Api'], function($routes) {
 
    $routes->get('bangunan/category', 'Bangunan::category');
    $routes->resource('bangunan');
    $routes->get('bangunan/maps', 'Bangunan::maps');
    $routes->post('bangunanAdmin', 'Bangunan::listByAdmin');
    $routes->post('bangunan/findByName', 'Bangunan::findByName');
    $routes->post('bangunan/findByCategory', 'Bangunan::findByCategory');
    $routes->post('recommendation', 'Bangunan::updateRecommendation');

    $routes->resource('account');
    $routes->post('account/profile', 'Account::profile');
    $routes->post('account/changePassword', 'Account::changePassword');
    $routes->post('account/visitHistory', 'Account::visitHistory');
    $routes->post('account/newVisitHistory', 'Account::newVisitHistory');
    $routes->post('account/(:num)', 'Account::update/$1');
    $routes->resource('review');
    $routes->resource('user');
    $routes->get('owner', 'User::owner');
    $routes->resource('facility');
    $routes->resource('category');
    $routes->post('village', 'Village::getData');
    $routes->post('campus', 'Campus::getData');
    $routes->post('login', 'Profile::attemptLogin');
    $routes->post('profile', 'Profile::profile');
    $routes->get('logout', 'Profile::logout');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
