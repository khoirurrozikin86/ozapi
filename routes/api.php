<?php



use Illuminate\Support\Facades\Route;


use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PaketController;
use App\Http\Controllers\ServerController;
use App\Http\Controllers\PelangganController;
use App\Http\Controllers\BulanController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\MonitoringController;



Route::post('/login', [AuthController::class, 'login']);

// Route for monitoring all servers and their associated pelanggan


// Route for monitoring a specific server and its associated pelanggan
// Route::get('/monitoring/{id}', [MonitoringController::class, 'show']);


// Route::get('/monitoring/testrouteros', [MonitoringController::class, 'testRouteros']);
Route::get('/monitoring/{id}', [MonitoringController::class, 'index']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('pakets', PaketController::class);







    Route::apiResource('servers', ServerController::class);

    //pelanggan
    Route::get('/pelanggan/indexfull', [PelangganController::class, 'indexfull']);
    Route::get('/pelanggan/untuk-tagihan', [PelangganController::class, 'pelangganUntukTagihan']);
    Route::apiResource('pelanggan', PelangganController::class);




    //bulan
    Route::get('/bulans', [BulanController::class, 'index']);

    //tagihan
    Route::post('/tagihan/bayar', [TagihanController::class, 'bayarTagihan']);
    // Route::get('/tagihan/belum-lunas/{id_pelanggan}', [TagihanController::class, 'getBelumLunasByPelanggan']);


    Route::get('/tagihan/belum-lunas-pelanggan', [TagihanController::class, 'getBelumLunasByPelanggan']);

    Route::get('/penghasilan', [TagihanController::class, 'getPenghasilanByTanggal']);

    Route::get('/tagihan/belum-lunas', [TagihanController::class, 'getTagihanBelumLunas']);
    Route::get('/tagihan/lunas', [TagihanController::class, 'getTagihanLunas']);
    Route::get('/tagihan/filter', [TagihanController::class, 'getByBulanTahun']);

    Route::post('/tagihan/buat-massal', [TagihanController::class, 'buatTagihanMassal']);
    Route::apiResource('tagihan', TagihanController::class);
});
