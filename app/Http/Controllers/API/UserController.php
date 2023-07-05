<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        return response()->json(['user' => $user]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreUserRequest $request)
    {
        // create
        $user = User::create([
            'username' => $request->user['username'],
            'email' => $request->user['email'],
            'password' => bcrypt($request->user['password']), // encrypt
        ]);

        // get user after created
        $user = User::find($user->id);
        $token = $user->createToken('api_token')->plainTextToken;

        $user->token = $token;
        // response
        return response()->json([
            'user' => $user,
        ], 201);
    }

    // login
    public function login(Request $request)
    {
        // check email
        $user = User::where('email', $request->user['email'])->first();

        // check password
        if (!$user || !\Hash::check($request->user['password'], $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }

        // create token
        $token = $user->createToken('api_token')->plainTextToken;

        $user->token = $token;

        // response
        return response()->json([
            'user' => $user,
        ], 200);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $user = User::find($user->id);

        // update
        $user->update($request->user);

        // if password
        if (isset($request->user['password'])) {
            $user->password = bcrypt($request->user['password']);
            $user->save();
        }

        // response
        return response()->json([
            'user' => $user,
        ], 200);
    }

    // show
    public function show(Request $request, $user)
    {

        $user = User::select('id','username', 'bio', 'image')->where('username', $user)->first();
        
        // check follow
        if ($request->user) {
            // Người dùng đã xác thực
            $auth = $request->user;
            $check_follow = Follow::where('user_id', $auth->id)->where('following_user_id', $user->id)->first();
            if ($check_follow) {
                $check_follow = true;
            } else {
                $check_follow = false;
            }
        } else {
            // Người dùng chưa xác thực
            $check_follow = false;
        }
        $user->following = $check_follow;

        // unset id
        unset($user->id);

        // if không có
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        return response()->json([
            'profile' => $user,
        ], 200);
    }

    // follow user
    public function follow($user)
    {
        $user = User::where('username', $user)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        $auth = auth()->user();
        $check_follow = Follow::where('user_id', $auth->id)->where('following_user_id', $user->id)->first();
        if ($check_follow) {
            $check_follow = true;
        } else {
            $check_follow = Follow::create([
                'user_id' => $auth->id,
                'following_user_id' => $user->id,
            ]);
            $check_follow = true;
        }
        $user->following = $check_follow;
        return response()->json([
            'profile' => $user,
        ], 200);
    }

    public function unfollow($user){
        $user = User::where('username', $user)->first();
        if (!$user) {
            return response()->json([
                'message' => 'User not found',
            ], 404);
        }
        $auth = auth()->user();
        $check_follow = Follow::where('user_id', $auth->id)->where('following_user_id', $user->id)->first();
        if ($check_follow) {
            $check_follow->delete();
            $check_follow = false;
        } else {
            $check_follow = false;
        }
        $user->following = $check_follow;
        return response()->json([
            'profile' => $user,
        ], 200);
    }
}
