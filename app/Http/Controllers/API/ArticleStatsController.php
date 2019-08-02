<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Article;
use Carbon\Carbon;

class ArticleStatsController extends Controller
{
    public function mostViewedArticles() {
      return Article::orderBy('pageviews', 'desc')->take(5)->get();
    }

    public function shortestArticles() {
      return Article::orderByRaw('CHAR_LENGTH(body)')->take(5)->get();
    }

    public function longestArticles() {
      return Article::orderByRaw('CHAR_LENGTH(body) desc')->take(5)->get();
    }

    public function activityArticles() {
      return Article::select(DB::raw('count(*) as total'), DB::raw('date(created_at) as day'))
                    ->where('created_at', '>=', Carbon::now()->subDays(6))
                    ->groupBy('day')->get();

    }
}
