<?php

namespace App\Http\Controllers;

use App\Http\Resources\FaqCollection;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = Faq::sort()->get();

        return new FaqCollection($faqs);
    }
}
