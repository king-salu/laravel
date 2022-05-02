<?php

Use App\Memories;

Route::get('tpz_memories', function(){
    return Memories::all();
});