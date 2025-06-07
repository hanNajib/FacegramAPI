<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Posts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class PostController extends Controller
{
    public function create(Request $req) {
        $req->validate([
            "caption" => "required",
            "attachments" => "required|array",
            "attachments.*" => "required|image|mimes:webp,jpg,jpeg,png,gif"
        ]);

        try {
            DB::beginTransaction();
            $user = Auth::user();

            $post = $user->posts()->create([
                "caption" => $req->caption
            ]);

            foreach($req->file('attachments') as $file) {
                $filename = $file->getClientOriginalName();
                $filepath = $file->storeAs('posts', $filename, 'public');
                $post->attachments()->create([
                    "storage_path" => $filepath
                ]);
            }

            DB::commit();
            return Controller::message("Create Post Success", 201);
        } catch(Throwable $e) {
            DB::rollBack();
            return $e->getMessage();
            return Controller::message("Create Post Failed", 400);
        }
    }

    public function delete(Request $req, $id) {
        $post = Posts::find($id);
        if(!$post) {
            return Controller::message("Posts Not Found", 404);
        }

        if($post->user_id !== Auth::user()->id) {
            return Controller::message("Forbidden Access", 403);
        }

        $post->delete();
        return Controller::message('', 204);
    }

    public function get(Request $req) {
        $req->validate([
            "page" => "nullable|integer|min:0",
            "size" => "nullable|integer|min:1"
        ]);
        $page = $req->query('page') ?? 0;
        $size = $req->query('size') ?? 10;
        
        $post = Posts::skip($page * $size)->take($size)->get();
        return Controller::json([
            "page" => $page,
            "size" => $size,
            "posts" => PostResource::collection($post)
        ], 200);
    }
}
