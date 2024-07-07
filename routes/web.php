<?php

use App\Http\Controllers\BuyerAssignController;
use App\Http\Controllers\BuyerController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DesignationController;
use App\Http\Controllers\DivisionController;
use App\Http\Controllers\MarchentSOPController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SOPController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\TNAController;
use App\Http\Controllers\UserController;
use App\Models\Notification;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');

// });

Route::get('/', function () {
    return view('auth.login');
});

Route::get('/s', function () {
    return view('search');
});

// Route::get('/search',  [DivisionController::class, 'search'])->name('search');
Route::get('/user-of-supervisor', function () {
    return view('backend.users.superindex');
})->name('superindex');

//New registration ajax route

Route::get('/get-company-designation/{divisionId}', [CompanyController::class, 'getCompanyDesignations'])->name('get_company_designation');


Route::get('/get-department/{company_id}', [CompanyController::class, 'getdepartments'])->name('get_departments');


Route::middleware('auth')->group(function () {
    // Route::get('/check', function () {
    //     return "Hello world";
    // });

    Route::get('/home', function () {
        return view('backend.home');
    })->name('home');

    //role

    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');


    //user

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get(
        '/users/{user}/edit',
        [UserController::class, 'edit']
    )->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::get('/online-user', [UserController::class, 'onlineuserlist'])->name('online_user');

    Route::post('/users/{user}/users_active', [UserController::class, 'user_active'])->name('users.active');

    //divisions

    Route::get('/divisions', [DivisionController::class, 'index'])->name('divisions.index');
    Route::get('/divisions/create', [DivisionController::class, 'create'])->name('divisions.create');
    Route::post('/divisions', [DivisionController::class, 'store'])->name('divisions.store');
    Route::get('/divisions/{division}', [DivisionController::class, 'show'])->name('divisions.show');
    Route::get('/divisions/{division}/edit', [DivisionController::class, 'edit'])->name('divisions.edit');
    Route::put('/divisions/{division}', [DivisionController::class, 'update'])->name('divisions.update');
    Route::delete('/divisions/{division}', [DivisionController::class, 'destroy'])->name('divisions.destroy');

    // companies
    Route::resource('companies', CompanyController::class);

    //departments
    Route::resource('departments', DepartmentController::class);

    // designations
    Route::resource('designations', DesignationController::class);

    ///buyers
    Route::get('/buyers', [BuyerController::class, 'index'])->name('buyers.index');
    Route::get('/buyers/create', [BuyerController::class, 'create'])->name('buyers.create');
    Route::post('/buyers', [BuyerController::class, 'store'])->name('buyers.store');
    Route::get('/buyers/{buyer}', [BuyerController::class, 'show'])->name('buyers.show');
    Route::get('/buyers/{buyer}/edit', [BuyerController::class, 'edit'])->name('buyers.edit');
    Route::put('/buyers/{buyer}', [BuyerController::class, 'update'])->name('buyers.update');
    Route::delete('/buyers/{buyer}', [BuyerController::class, 'destroy'])->name('buyers.destroy');
    Route::post('/buyers/{buyer}/buyers_active', [BuyerController::class, 'buyer_active'])->name('buyers.active');
    Route::get('/get_buyer', [BuyerController::class, 'get_buyer'])->name('get_buyer');

    ///suppliers
    Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
    Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    Route::post('/suppliers/{supplier}/suppliers_active', [SupplierController::class, 'supplier_active'])->name('suppliers.active');
    Route::get('/get_supplier', [SupplierController::class, 'get_supplier'])->name('get_supplier');

    //buyer_assigns
    Route::get('/buyer_assigns', [BuyerAssignController::class, 'index'])->name('buyer_assigns.index');
    Route::get('/buyer_assigns/create', [BuyerAssignController::class, 'create'])->name('buyer_assigns.create');
    Route::post('/buyer_assigns', [BuyerAssignController::class, 'store'])->name('buyer_assigns.store');
    Route::get('/buyer_assigns/{buyer_assign}', [BuyerAssignController::class, 'show'])->name('buyer_assigns.show');
    Route::get('/buyer_assigns/{buyer_assign}/edit', [BuyerAssignController::class, 'edit'])->name('buyer_assigns.edit');
    Route::put('/buyer_assigns/{buyer_assign}', [BuyerAssignController::class, 'update'])->name('buyer_assigns.update');
    Route::delete('/buyer_assigns/{buyer_assign}', [BuyerAssignController::class, 'destroy'])->name('buyer_assigns.destroy');
    Route::post('/buyer_assigns/{buyer_assign}/buyer_assigns_active', [BuyerAssignController::class, 'buyer_assign_active'])->name('buyer_assigns.active');

    //sops
    Route::resource('sops', SOPController::class);

    //marchent_sops
    Route::get('/marchent_sops', [MarchentSOPController::class, 'index'])->name('marchent_sops.index');
    Route::get('/marchent_sops/create', [MarchentSOPController::class, 'create'])->name('marchent_sops.create');
    Route::post('/marchent_sops', [MarchentSOPController::class, 'store'])->name('marchent_sops.store');
    Route::get('/marchent_sops/{marchent_sop}', [MarchentSOPController::class, 'show'])->name('marchent_sops.show');
    Route::get('/marchent_sops/{marchent_sop}/edit', [MarchentSOPController::class, 'edit'])->name('marchent_sops.edit');
    Route::put('/marchent_sops/{marchent_sop}', [MarchentSOPController::class, 'update'])->name('marchent_sops.update');
    Route::delete('/marchent_sops/{marchent_sop}', [MarchentSOPController::class, 'destroy'])->name('marchent_sops.destroy');

    //tnas
    Route::get('/tnas', [TNAController::class, 'index'])->name('tnas.index');
    Route::get('/tnas/create', [TNAController::class, 'create'])->name('tnas.create');
    Route::post('/tnas', [TNAController::class, 'store'])->name('tnas.store');
    Route::get('/tnas/{tna}', [TNAController::class, 'show'])->name('tnas.show');
    Route::get('/tnas/{tna}/edit', [TNAController::class, 'edit'])->name('tnas.edit');
    Route::put('/tnas/{tna}', [TNAController::class, 'update'])->name('tnas.update');
    Route::delete('/tnas/{tna}', [TNAController::class, 'destroy'])->name('tnas.destroy');

    Route::post('/update-tna-date', [TNAController::class, 'updateDate'])->name('tnas.updateDate');

    //tnas_dashboard
    Route::get('/tnas_dashboard', [TNAController::class, 'tnas_dashboard'])->name('tnas_dashboard');
    // tnas_dashboard_update 
    Route::get('/tnas_dashboard_update', [TNAController::class, 'tnas_dashboard_update'])->name('tnas_dashboard_update');
    //tnas_close
    Route::post('/tnas_close/{tna}', [TNAController::class, 'tnas_close'])->name('tnas_close');
    // archives
    Route::get('/archives', [TNAController::class, 'archives'])->name('archives');
    // archives_dashboard
    Route::get('/archives_dashboard', [TNAController::class, 'archives_dashboard'])->name('archives_dashboard');
    // archives_dashboard_update
    Route::get('/archives_dashboard_update', [TNAController::class, 'archives_dashboard_update'])->name('archives_dashboard_update');
});



























Route::get('/read/{notification}', [NotificationController::class, 'read'])->name('notification.read');


require __DIR__ . '/auth.php';

//php artisan command

Route::get('/foo', function () {
    Artisan::call('storage:link');
});

Route::get('/cleareverything', function () {
    $clearcache = Artisan::call('cache:clear');
    echo "Cache cleared<br>";

    $clearview = Artisan::call('view:clear');
    echo "View cleared<br>";

    $clearconfig = Artisan::call('config:cache');
    echo "Config cleared<br>";
});

Route::get('/key =', function () {
    $key =  Artisan::call('key:generate');
    echo "key:generate<br>";
});

Route::get('/migrate', function () {
    $migrate = Artisan::call('migrate');
    echo "migration create<br>";
});

Route::get('/migrate-fresh', function () {
    $fresh = Artisan::call('migrate:fresh --seed');
    echo "migrate:fresh --seed create<br>";
});

Route::get('/optimize', function () {
    $optimize = Artisan::call('optimize:clear');
    echo "optimize cleared<br>";
});
Route::get('/route-clear', function () {
    $route_clear = Artisan::call('route:clear');
    echo "route cleared<br>";
});

Route::get('/route-cache', function () {
    $route_cache = Artisan::call('route:cache');
    echo "route cache<br>";
});

Route::get('/updateapp', function () {
    $dump_autoload = Artisan::call('dump-autoload');
    echo 'dump-autoload complete';
});
