<?php

use App\Http\Controllers\PermissionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/roles', [PermissionController::class,'Permission']);

Route::group(['middleware' => 'role:user'], function() {

    Route::get('/user', function() {

        return 'Welcome...!!';

    });

});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/color', function () {
    return view('color-picker');
});

Route::post('/fireEvent', function (Request $request) {

    PublicEvent::dispatch($request->color);
})->name('fire.public.event');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');



Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get(
        '/private/fireEvent',
        function () {
            // faking file upload
            sleep(3);
            PrivateEvent::dispatch('Profile picutre has been updated');
        }
    )->name('fire.private.event');


    Route::get('/dashboard', function () {
        $groups = Group::where('id', auth()->user()->group_id)->get();
        return view('dashboard', compact('groups'));
    }
    )->name('dashboard');

    Route::get('/dashboard/{group}', function (Request $request, Group $group) {

        abort_unless($request->user()->canJoinGroup($group->id), 401);
        return view('group', compact('group'));
    }
    )->name('group');

    Route::get('/presence/fireEvent/{message}', fn() => PresenceEvent::dispatch())->name('fire.presence.event');
});
