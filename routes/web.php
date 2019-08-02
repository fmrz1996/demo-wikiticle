<?php

Route::get('/{router_link?}', function () {
    return view('home');
})->where('router_link', '[\/\w\.-]*');
