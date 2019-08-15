<?php

Route::get('/', function () {
  return redirect('/articles');
});

Route::get('/{router_link?}', function () {
    return view('home');
})->where('router_link', '[\/\w\.-]*');
