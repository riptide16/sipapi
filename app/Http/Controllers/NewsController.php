<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Http\Resources\NewsCollection;
use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $news = News::with('author')->published()->orderBy('published_date', 'desc');
        $news = $news->paginate($request->per_page ?? 20)->withQueryString();

        return new NewsCollection($news);
    }

    public function show($id)
    {
        $news = News::with('author')->findOrFail($id);

        return new NewsResource($news);
    }
}
