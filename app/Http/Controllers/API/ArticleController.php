<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Article;

class ArticleController extends Controller
{
    public function index()
    {
        return Article::orderBy('id', 'desc')->paginate(15);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
          'title' => 'required|string|max:255',
          'image' => 'required|string|max:255',
          'body' => 'required'
        ]);

        return Article::create([
          'title' => $request['title'],
          'image' => $request['image'],
          'body' => $request['body'],
          'pageviews' => $request['pageviews'],
          'source' => $request['source'],
        ]);
    }

    public function show($id)
    {
        $article = Article::find($id);
        $article["prev_article"] = Article::where('id', '<', $id)->max('id');
        $article["next_article"] = Article::where('id', '>', $id)->min('id');
        return $article;
    }

    public function update(Request $request, $id)
    {
      $this->validate($request, [
        'title' => 'required|string|max:255',
        'image' => 'required|string|max:255',
        'body' => 'required'
      ]);

      return Article::where('id', $id)->update($request->all());
    }

    public function destroy($id)
    {
        return Article::where('id', $id)->delete();
    }

    public function random()
    {
      return Article::inRandomOrder()->pluck('id')->first();
    }
}
