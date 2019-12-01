<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\User;
use App\Http\Requests\UpdateUser;

class UserController extends Controller
{
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        $users = $this->user->get();
     
        return response()->json([
            'message' => 'Success',
            'data' => $users
        ], 201); 
    }

    public function edit(Request $request)
    {
        $user = $this->user->where('id',$request->id)->get();

        return response()->json([
            'message' => 'Success, user activated!'], 201);
    }

    public function update(UpdateUser $request)
    {
        if(!$request->validated()){
            dd('not');
        }
        $user = $this->user->where('id',$request->id)->update(['email' => $request->email]);

        return response()->json([
            'message' => 'Success, user updated!'], 201);
    }

    public function delete(Request $request)
    {
        $this->user->where('id',$request->id)->update(['active' => 0]);

        return response()->json([
            'message' => 'Success, user deactivated!'], 201);
    }

    public function active(Request $request)
    {
        $this->user->where('id',$request->id)->update(['active' => 1]);

        return response()->json([
            'message' => 'Success, user activated!'], 201);
    }
    

}
