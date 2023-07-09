<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Article;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArticleCommentController extends Controller
{
    // get id article
    public function getIdArticle($slug)
    {
        $article = Article::where('slug', $slug)->first();
        return $article->id;
    }

    public function index(Request $request, $slug)
    {
        $article_id = $this->getIdArticle($slug);

        if ($request->user) {
            $my_account = $request->user->id;
        } else {
            $my_account = null;
        }

        if (!$article_id) {
            return response()->json([
                'message' => 'Article not found!',
            ], 404);
        }
        $comments = Comment::where('article_id', $article_id)->get();
        foreach ($comments as $comment) {
            $comment->author = $comment->getAuthor($my_account);
        }
        return response()->json([
            'comments' => $comments,
        ]);
    }

    public function store(StoreCommentRequest $request, $slug)
    {
        $article_id = $this->getIdArticle($slug);
        if (!$article_id) {
            return response()->json([
                'message' => 'Article not found!',
            ], 404);
        }
        $comment = Comment::create([
            'body' => $request->comment['body'],
            'article_id' => $article_id,
            'user_id' => Auth::user()->id,
        ]);

        // get comment
        $comment = Comment::where('id', $comment->id)->first();
        $comment->author = Auth::user();
        unset($comment->author->email);
        $comment->author->following = true;

        return response()->json([
            'comment' => $comment,
        ]);
    }

    public function destroy($slug, $comment_id)
    {
        $article_id = $this->getIdArticle($slug);
        if (!$article_id) {
            return response()->json([
                'message' => 'Article not found!',
            ], 404);
        }
        $comment = Comment::where('id', $comment_id)->where('article_id', $article_id)->delete();
        return response()->json([
            'message' => 'Comment deleted!',
        ]);
    }
}
