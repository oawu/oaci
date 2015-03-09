<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

// $route['default_controller'] = "main";
// $route['admin'] = "admin/main";
// $route['404_override'] = '';

// $route['aaa/(:num)/(:num)'] = "main/aaa";
// $route['product/(:num)'] = "products/content/$1";
// $route['portfolio/(:num)'] = "portfolios/content/$1";
// $route['scent/(:num)'] = "scents/content/$1";
// $route['scents/(:any)'] = "scents/index/$1";

Route::root ('main@index');
// Route::get ('/xxx', 'main@xxx');
Route::get ('/aaa/(:num)/(:num)', 'main@aaa($1, $2)');
// Route::delete ('/user/{id}', 'controller@index');

// Route::default ('controller@index');

// Route::get ('/path', 'controller@index');
// Route::post ('/path', 'controller@index');
// Route::put ('/path', 'controller@index');
// Route::delete ('/path', 'controller@index');

// Route::controller ('/path', 'controller@index');
// Route::resorce ('/path', 'controller@index');
// Route::match(['get', 'post'], '/', function()
// {
//     return 'Hello World';
// });
// Route::any('foo', function()
// {
//     return 'Hello World';
// });

/* End of file routes.php */
/* Location: ./application/config/routes.php */