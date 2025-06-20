<?php

namespace App\Http\Controllers\api;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\FollowingResource;
use App\Http\Resources\UserResource;
use App\Models\Follow;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function follow(Request $req, $username) {
        $user = Auth::user();
        $followUser = User::whereName($username)->first();
        $isExists = Follow::where([
            "follower_id" => $user?->id,
            "following_id" => $followUser?->id,
        ])->first();
        
        if(!$followUser) {
            return Controller::message("User not found", 404);
        }

        if($followUser->id === $user->id) {
            return Controller::message("You are not allowed to follow yourself", 422);
        }

        if($isExists) {
            return Controller::json([
                "message" => "You are already followed",
                "status" => $isExists->is_accepted ? "following" : "requested" 
            ], 422);
        }

        $data = Follow::create([
            "follower_id" => $user->id,
            "following_id" => $followUser->id,
            "is_accepted" => $followUser->is_private ? false : true
        ]);

        return Controller::json([
            "message" => "Follow Success",
            "status" => $data->is_accepted ? "following" : "requested"
        ], 200);
    }

    public function unfollow(Request $req, $username) {
        $user = Auth::user();
        $followUser = User::whereName($username)->first();
        $followData = Follow::where([
            "follower_id" => $user?->id,
            "following_id" => $followUser?->id,
        ])->first();

        if(!$followUser) {
            return Controller::message("User not found", 404);
        }

        if(!$followData) {
            return Controller::message("You are not following the user", 422);
        }

        $followData->delete();
        return Controller::message('', 204);
    }

    public function getFollowing(Request $req, $username) {
        $currentUser = Auth::user();
        $user = User::whereName($username)->first();

        if (!$user) {
            return Controller::message("User not found", 404);
        }

        if ($user->is_private && $currentUser->id !== $user->id) {
            $isFollowing = Follow::where([
                'follower_id' => $currentUser->id,
                'following_id' => $user->id,
                'is_accepted' => true
            ])->exists();

            if (!$isFollowing) {
                return Controller::message("This account is private", 403);
            }
        }

        return Controller::json([
            "following" => FollowingResource::collection($user->load('following')->following)
        ]);
    }

    public function getFollowers(Request $req, $username) {
        $currentUser = Auth::user();
        $user = User::whereName($username)->first();

        if (!$user) {
            return Controller::message("User not found", 404);
        }

        if ($user->is_private && $currentUser->id !== $user->id) {
            $isFollowing = Follow::where([
                'follower_id' => $currentUser->id,
                'following_id' => $user->id,
                'is_accepted' => true
            ])->exists();

            if (!$isFollowing) {
                return Controller::message("This account is private", 403);
            }
        }

        return Controller::json([
            "followers" => FollowingResource::collection($user->load('followers')->followers)
        ]);
    }

    public function accept(Request $req, $username) {
        $currentUser = Auth::user();
        $user = User::whereName($username)->first();
        $isFollowing = Follow::where([
            "follower_id" => $user?->id,
            "following_id" => $currentUser?->id,
        ])->first();

        if(!$user) {
            return Controller::message("User not found", 404);
        }
        
        if(!$isFollowing) {
            return Controller::message("The user is not following you", 422);
        }

        
        if($isFollowing?->is_accepted) {
            return Controller::message("Follow request is already accepted", 422);
        }

        $isFollowing->is_accepted = true;
        $isFollowing->save();

        return Controller::message("Follow request accepted");
    }

    public function getUser(Request $req) {
        $currentUser = Auth::user();
        $followingIds = Follow::where('follower_id', $currentUser->id)->pluck('following_id');
        $user = User::where('id', '!=', $currentUser->id)
                ->whereNotIn('id', $followingIds)
                ->get();

        return Controller::json([
            "users" => $user
        ]);
    }

    public function getDetailUser(Request $req, $username) {
        $user = User::whereName($username)
            ->with('posts.attachments')
            ->withCount(['following', 'followers', 'posts', 'posts'])
            ->first();

        if(!$user) {
            return Controller::message("User not found", 404);
        }

        return Controller::json(new UserResource($user));
    }
}
