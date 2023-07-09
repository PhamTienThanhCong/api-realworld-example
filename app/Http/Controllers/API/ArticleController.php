<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleController extends Controller
{   
    public function show_Article($slug, $my_id_user)
    {
        $article = Article::select('articles.*', DB::raw('COUNT(favorites.id) as favoritesCount'))
            ->leftJoin('favorites', 'favorites.article_id', '=', 'articles.id')
            ->where('articles.slug', '=', $slug)
            ->groupBy('articles.id')
            ->first();
        
        $article->tagList = $article->getTagAttribute($article->tagList);
        $article->author = $article->getAuthor($my_id_user);
        $article->favorited = $article->isFavorited($my_id_user);

        return response()->json([
            'article' => $article
        ]);
    }
    public function index(Request $request)
    {
        if ($request->user) {
            $my_account = $request->user->id;
        } else {
            $my_account = null;
        }

        $articles = Article::select('articles.*', DB::raw('COUNT(favorites.id) as favoritesCount'))
            ->leftJoin('favorites', 'favorites.article_id', '=', 'articles.id');
        
        // filter by tag
        if ($request->input('tag')) {
            $articles = $articles->where('tagList', 'like', '%' . $request->input('tag') . '%');
        }
        // filter by author
        if ($request->input('author')) {
            // get author id
            $author = DB::table('users')->where('username', $request->input('author'))->first();
            $articles = $articles->where('articles.user_id', $author->id);
        }
        // filter by favorited
        if ($request->input('favorited')) {
            // get favorited id
            $favorited = DB::table('users')->where('username', $request->input('favorited'))->first();
            // get article id
            $articles = $articles->where('favorites.user_id', $favorited->id);
        }
        // filter by limit
        if ($request->input('limit')) {
            $articles = $articles->limit($request->input('limit'));
        }
        // filter by offset
        if ($request->input('offset')) {
            $articles = $articles->offset($request->input('offset'));
        }

        $articles = $articles->groupBy('articles.id')->get();

        foreach ($articles as $article) {
            $article->tagList = $article->getTagAttribute($article->tagList);
            $article->author = $article->getAuthor($my_account);
            $article->favorited = $article->isFavorited($my_account);
        }

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

        return $this->show_Article($article->slug, Auth::user()->id);
    }

    public function show(Request $request, $slug)
    {
        $my_account = $request->user->id;

        return $this->show_Article($slug, $my_account);
    }

    public function update(Request $request, $slug)
    {
        // get article by slug and user_id
        $user_id =  Auth::user()->id;
        $article = Article::where('slug', $slug)->where('user_id', $user_id)->first();
        if (!$article) {
            return response()->json([
                'message' => 'Article not found'
            ], 404);
        }
        // update only title, description, body 
        $article->update([
            'title' => $request->input('article.title') ?? $article->title,
            'description' => $request->input('article.description') ?? $article->description,
            'body' => $request->input('article.body') ?? $article->body,
        ]);
        if ($request->input('article.title')) {
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
            $article->update([
                'slug' => $slug
            ]);
        }

        return $this->show_Article($slug, Auth::user()->id);
    }

    public function destroy($slug)
    {
        // get article by slug and user_id
        $user_id =  Auth::user()->id;
        $article = Article::where('slug', $slug)->where('user_id', $user_id)->first();
        if (!$article) {
            return response()->json([
                'message' => 'Article not found'
            ], 404);
        }
        $article->delete();

        return response()->json([
            'message' => 'Article deleted'
        ]);
    }

    public function Favorite($slug){
        $user_id =  Auth::user()->id;
        $article = Article::where('slug', $slug)->first();
        if (!$article) {
            return response()->json([
                'message' => 'Article not found'
            ], 404);
        }
        // check if article is already favorited
        $isFavorited = DB::table('favorites')->where('user_id', $user_id)->where('article_id', $article->id)->count();
        if ($isFavorited == 0) {
            // create favorite
            DB::table('favorites')->insert([
                'user_id' => $user_id,
                'article_id' => $article->id,
            ]);
        }

        return $this->show_Article($slug, Auth::user()->id);
    }

    public function Unfavorite($slug){
        $user_id =  Auth::user()->id;
        $article = Article::where('slug', $slug)->first();
        if (!$article) {
            return response()->json([
                'message' => 'Article not found'
            ], 404);
        }
        // check if article is already favorited
        $isFavorited = DB::table('favorites')->where('user_id', $user_id)->where('article_id', $article->id)->count();
        if ($isFavorited > 0) {
            // delete favorite
            DB::table('favorites')->where('user_id', $user_id)->where('article_id', $article->id)->delete();
        }
        return $this->show_Article($slug, Auth::user()->id);

    }
}
