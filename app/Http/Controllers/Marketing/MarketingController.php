<?php

namespace App\Http\Controllers\Marketing;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketingController extends Controller {
    
    public function store (Request $request) {
       
        $post           = new Post();
        $post->uuid     = Str::uuid();
        $post->title    = $request->input('title');
        $post->content  = $request->input('content');
        $post->is_fixed = $request->input('is_fixed', 0);
        $post->access   = $request->input('access');
        $post->user_id  = $request->input('user_id', null);
        $post->access   = $request->input('access');
        $post->url      = $request->input('url');
        if ($post->save()) {
            return redirect()->back()->with('success', 'Publicação criada com sucesso!');
        }

        return redirect()->back()->with('error', 'Erro ao criar a publicação, verifique os dados e tente novamente!');
    }

    public function destroy ($uuid) {
       
        $post = Post::where('uuid', $uuid)->first();
        if ($post && $post->delete()) {
            return redirect()->back()->with('success', 'Publicação deletada com sucesso!');
        }

        return redirect()->back()->with('error', 'Erro ao deletar a publicação, verifique os dados e tente novamente!');
    }
}
