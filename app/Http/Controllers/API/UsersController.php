<?php
/**
 * Created by PhpStorm.
 * User: nadir
 * Date: 11/1/18
 * Time: 2:28 PM
 */

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\Team;
use App\User;
use Illuminate\Http\Request;
use Validator;

class UsersController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function getMembers(Request $request, User $user) {

        $user = $user->newQuery();


        $limit = $request->get('limit');
        $sort = $request->get('sort');
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $email = $request->get('email');


        if($firstName!=null) {

            $user->where('firstName','LIKE',"%{$firstName}%");
        }

        if($lastName!=null) {

            $user->where('lastName','LIKE',"%{$lastName}%");
        }

        if($email!=null) {

            $user->where('email','LIKE',"%{$email}%");
        }

        if ($sort=='-id') {

            $members = $user->members()->orderBy('id', 'desc')->withTrashed()->paginate($limit);

            return response(['members'=>$members], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }

           $members = $user->members()->withTrashed()->paginate($limit);

        return response(['members'=>$members], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function destroy(Request $request)
    {
        $id = $request->get('userId');
        $user = User::members()->onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return response(['message'=>'success'], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function trash(Request $request)
    {
        $id = $request->get('userId');
        $user = User::members()->findOrFail($id);
        $user->delete();

        return  response($user->deleted_at, 200)->withHeaders([
            'Content-Type' => 'application/json',
        ]);
    }

    public function publish(Request $request)
    {
        $id = $request->get('userId');
        $user = User::members()->onlyTrashed()->findOrFail($id);
        $user->restore();

        return response(['message'=>'success'], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function getTeams() {

        $teams = Team::all();

        return response(['teams'=>$teams], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function getMemberByTeam(Request $request) {

        $teamId = $request->get('teamId');

        $users = User::members()->where('team_id', $teamId)->get();

        return response(['members'=>$users], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    public function getAll() {

        $members = User::members()->get();

        return response(['members'=>$members], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }
    public function updateAvatar(Request $request) {

        $userId = $request->get('userId');
        $img = $request->get('img');

        $member = User::findOrFail($userId);

        $member->avatar = $img;
        $member->update();

        return response(['user'=>$member], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function addMember(Request $request) {

        $rules = [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' =>'required|email|unique:users',
            'password' => 'required|min:6',
        ];

        $inputs = $request->all();

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            return response()->json(['error' => $errors], 400);
        }
        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $email = $request->get('email');
        $avatar = $request->get('avatar');
        $team = $request->get('team_id');
        $password =$request->get('password');

        $user = new User([
            'firstName' => $firstName,
            'lastName' => $lastName,
            'email' => $email,
            'password'=>bcrypt($password),
            'avatar' => $avatar,
            'team_id' => $team
        ]);
        $user->save();


        return response(['message'=>'success','user'=>$user->id], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

    public function editMember(Request $request) {

        $id =$request->get('id');
        $email = $request->get('email');
        $user = User::findOrFail($id);

        $rules = [
            'firstName' => 'required',
            'lastName' => 'required',
            'email' =>'required|email',
            'password' => 'required|min:6',
        ];

        if($user->email != $email)
            $rules['email']='required|email|unique:users';

        $inputs = $request->all();

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            return response()->json(['error' => $errors], 400);
        }

        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $avatar = $request->get('avatar');
        $team = $request->get('team_id');
        $password =$request->get('password');

        $user->firstName = $firstName;
        $user->lastName = $lastName;
        if($user->email != $email)
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->avatar = $avatar;
        $user->team_id = $team;
        $user->save();


        return response(['message'=>'success','user'=>$user->id], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

}
