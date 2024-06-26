<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\PatientRecordController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\mailController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// rota para guardar info de contato
Route::post('/contacts', [ContactController::class, 'store']);
Route::post('/contacts/{id}', [ContactController::class,'destroy']);
Route::get('/contacts', [ContactController::class, 'index']);

Route::post('/appointments', [AppointmentController::class,'store']);
Route::get('/appointments', [AppointmentController::class, 'show']);
Route::get('/userfutureappointments/{id}', [AppointmentController::class,'show_future_patient_appointments']);
Route::get('/userpastappointments/{id}', [AppointmentController::class,'show_past_patient_appointments']);

Route::get('/appointments/today', [AppointmentController::class,'show_today_appointments']);
Route::get('/appointments/today/{id}', [AppointmentController::class,'show_today_appointments_psychologist']);
Route::post('/appointments/update/{id}', [AppointmentController::class,'update']);
Route::put('/appointments/update/{id}',[AppointmentController::class,'setObservation']);

Route::get('/usuarios/nome/{id}', [RegisteredUserController::class,'showName']);
Route::get('/usuarios/{id}', [RegisteredUserController::class,'showUserInfo']);
Route::get('/usuarios', [RegisteredUserController::class,'show']);

Route::post('/records',[PatientRecordController::class,'store']);
Route::put('/records/{id}',[PatientRecordController::class,'update']);
Route::get('/records/{id}',[PatientRecordController::class,'show']);

// Rotas de notificação; Usadas nas telas de Psicologo & Secretaria
Route::post('/notify/{id}', [NotificationController::class,'notify']);
Route::get('/notifications/{id}', [NotificationController::class,'show']);
Route::post('/notifications/{id}', [NotificationController::class,'markRead']);

// rota utilizada para coletar a lista de psicologos atualmente cadastrada no banco de dados
Route::get('/psychologists', [RegisteredUserController::class, 'getPsicologos']);
Route::get('/users', [RegisteredUserController::class, 'getUsers']);


Route::put('/mail', [mailController::class, 'sendEmail']);
Route::put('/mail/userInfo', [mailController::class,'sendLoginInfo']);
