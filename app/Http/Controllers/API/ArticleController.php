<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::latest()->get();
        
        return response()->json([
            'articles' => $articles,
            'articlesCount' => $articles->count()
        ]);
    }

    public function store(StoreArticleRequest $request)
    {
        // create slug
        $slug = Str::slug($request->input('article.title'));
        // check if slug exists
        $slugCount = Article::where('slug', $slug)->count();
        // if exists, add count to slug
        if ($slugCount > 0) {
            // random number between 1 and 9999
            $slugCount = rand(1, 9999);
            $slug = $slug . '-' . $slugCount;
        }

        // tagList convert to string
        $tagList = implode(',', $request->input('article.tagList'));

        $article = Article::create([
            'user_id' => auth()->user()->id,
            'title' => $request->input('article.title'),
            'slug' => $slug,
            'description' => $request->input('article.description'),
            'body' => $request->input('article.body'),
            'tagList' => $tagList,
        ]);

        // get article after created
        $article = Article::where('slug', $slug)->first();

        return response()->json([
            'article' => $article
        ]);
    }

    public function show($slug)
    {
        $article = Article::select('articles.*', DB::raw('COUNT(favorites.id) as favoritesCount'))
            ->leftJoin('favorites', 'favorites.article_id', '=', 'articles.id')
            ->where('articles.slug', '=', $slug)
            ->groupBy('articles.id')
            ->first();

        $article->tagList = $article->getTagAttribute($article->tagList);
        $article->author = $article->getAuthor($article->user_id);

        return response()->json([
            'article' => $article
        ]);
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
