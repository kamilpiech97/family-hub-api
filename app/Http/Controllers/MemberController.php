<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\User;
use App\Family;
use App\Member;
use App\Notifications\UserInvite;
use App\Http\Requests\StoreMember;
use App\Http\Requests\UpdateMember;
use App\Http\Requests\StoreDeceased;
use App\Notifications\PasswordResetRequest;
use App\Notifications\PasswordResetSuccess;
use App\PasswordReset;
use App\Services\StoreMemberService;
use App\Services\StoreMemberDeceasedService;
use App\Services\ActivateMemberService;

class MemberController extends Controller
{
    public function __construct(Member $member)
    {
        $this->member = $member;
    }

    public function index()
    {
        $this->member->setTable(Auth::User()->prefix.'_members');
        $member = $this->member->get();

        return response()->json(['message' => 'Success','data' => $member], 201); 
        
    }

    public function store(StoreMember $request,StoreMemberService $storeMember)
    {
        $storeMember->store($request);

        return response()->json(['message' => 'Success, member added'], 201);
    }


    public function storeDeceased(StoreDeceased $request,StoreMemberDeceasedService $storeMember)
    {
        $storeMember->store($request);
        
        return response()->json(['message' => 'Success'], 201);
    }


    public function activate($token, ActivateMemberService $activateMember)
    {
        $user = User::where('activation_token', $token)->first();
        if (!$user) {
            return response()->json(['message' => 'This activation token is invalid.'], 404);
        }
        $data = $activateMember->active($user);

        return response()->json($data, 201);
    }


    public function edit(Request $request)
    {
        $this->member->setTable(Auth::User()->prefix.'_members');
        $member = $this->member->get()->where('id',$request->id);
        return response()->json([
            'message' => 'Success, found data!','data' => $member], 201);  
    }


    public function update(UpdateMember $request)
    {   
        $this->member->setTable(Auth::User()->prefix.'_members');
        $this->member->where('user_id',Auth::User()->id)->update($request->validated());

        return response()->json(['message' => 'Success, data updated!'], 201);
    }


    public function delete(Request $request)
    {
        $this->member->setTable(Auth::User()->prefix.'_members');
        $this->member->where('id',$request->id)->delete();
        return response()->json(['message' => 'Success, data deleted'], 201);
    }

}
