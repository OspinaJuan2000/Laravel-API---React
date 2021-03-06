<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function login(Request $request)
    {

        $user = User::whereEmail($request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $token = $user->createToken('api')->accessToken;

            return response()->json([
                'response' => true,
                'message' => 'Welcome to the system, you\'re now authenticaded',
                'token' => $token,
            ]);
        }

        return response()->json([
            'response' => false,
            'message' => 'User and/or password incorrect',
        ]);
    }

    public function logout()
    {

        $user = auth()->user();
        $user->tokens->each(function($token, $key) {
            $token->delete();
        });

        return response()->json([
            'response' => true,
            'message' => 'Logout succesfully, goodbye',
        ]);
    }

    public function index(Request $request)
    {
        if ($request->has('txtSearch')) {
            $users = User::where('name', 'like', '%' . $request->txtSearch . '%')
                ->orWhere('email', 'like', '%' . $request->txtSearch . '%')
                ->get();

            return $users;
        }
        return User::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateUserRequest $request)
    {
        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        User::create($data);

        return response()->json([
            'response' => true,
            'message' => 'The user has been created',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return $user;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->all();

        $user->update($data);

        return response()->json([
            'response' => true,
            'message' => 'The user has been updated',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'response' => true,
            'message' => "The user {$user->name} has been deleted",
        ]);
    }
}
