<?php
Use App\Memories;
Use App\venues;
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
    //return 'Hello World';
    //return Memories::all();
});
Route::get('/basic_response', function () {
    return 'Hello World';
 });



//Route::get('Memories/{memory}','MemoriesControl@show');
Route::get('Memories/getMemory','MemoriesControl@getMemories');
//Route::get('Memories','MemoriesControl@index');
//Route::get('Memories/NewMemo','MemoriesControl@NewMemo');

//Route::get('venues/explore_v','MemoriesControl@index');

//events
Route::get('events','eventscontroller@index');

Route::get('Inbox/getInbox/{user}','inboxControl@getInbox');
