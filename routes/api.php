<?php

use Illuminate\Http\Request;

Route::apiResources(['article' => 'API\ArticleController']);

Route::get('stats/1', 'API\ArticleStatsController@mostViewedArticles');
Route::get('stats/2', 'API\ArticleStatsController@shortestArticles');
Route::get('stats/3', 'API\ArticleStatsController@longestArticles');
Route::get('stats/4', 'API\ArticleStatsController@activityArticles');
