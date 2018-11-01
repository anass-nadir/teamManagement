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

        $users = User::members()->whereHas('teams', function($query) use ($teamId) {
            $query->where('team_id', $teamId);
        })->get();

        return response(['members'=>$users], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }
    public function attachMemberWithTeam(Request $request) {

        $teamId = $request->get('teamId');
        $userId = $request->get('userId');

        $member = User::members()->findOrFail($userId);

        $member->teams()->sync([$teamId], false);

        return response(['message'=>'attached successfully'], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }

}