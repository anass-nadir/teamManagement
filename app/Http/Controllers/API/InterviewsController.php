<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interview;
use Illuminate\Http\Request;
use Validator;

class InterviewsController extends Controller
{
    public function __construct()
    {
        $this->middleware('jwt.auth');
    }

    public function index(Request $request,Interview $interview)
    {
        $interview = $interview->newQuery();


        $limit = $request->get('limit');
        $sort = $request->get('sort');
        $subject = $request->get('subject');
        $place = $request->get('place');
        $synthesis = $request->get('synthesis');


        if($subject!=null) {

            $interview->where('subject','LIKE',"%{$subject}%");
        }

        if($place!=null) {

            $interview->where('place','LIKE',"%{$place}%");
        }

        if($synthesis!=null) {

            $interview->where('synthesis','LIKE',"%{$synthesis}%");
        }

        if ($sort=='-id') {

            $interviews = $interview->with('users')->orderBy('id', 'desc')->withTrashed()->paginate($limit);

            return response(['interviews'=>$interviews], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }

        $interviews = $interview->with('users')->withTrashed()->paginate($limit);

        return response(['interviews'=>$interviews], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }
    public function destroy(Request $request)
    {
        $id = $request->get('interviewId');
        $interview = Interview::onlyTrashed()->findOrFail($id);
        $interview->forceDelete();

        return response(['message'=>'success'], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function trash(Request $request)
    {
        $id = $request->get('interviewId');
        $interview = Interview::findOrFail($id);
        $interview->delete();

        return  response($interview->deleted_at, 200)->withHeaders([
        'Content-Type' => 'application/json',
    ]);
    }

    public function publish(Request $request)
    {
        $id = $request->get('interviewId');
        $interview = Interview::onlyTrashed()->findOrFail($id);
        $interview->restore();

        return response(['message'=>'success'], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }

    public function addInterview(Request $request) {

        $rules = [
            'subject' => 'required',
            'place' => 'required',
            'synthesis' =>'required',
            "users"    => "required|array|min:1",
            "users.*"  => "required|array|distinct|min:1"
        ];
        $inputs = $request->all();

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            return response()->json(['error' => $errors], 400);
        }
        $subject = $request->get('subject');
        $place = $request->get('place');
        $synthesis = $request->get('synthesis');
        $members = $request->get('users');

        $interview = new Interview([
            'subject' => $subject,
            'synthesis' => $synthesis,
            'place' => $place
        ]);
        $interview->save();

        foreach ($members as $member) {

            $interview->users()->sync([$member['id']], false);

        }


        return response(['message'=>'success','interview'=>$interview->id], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }
    public function updateInterview(Request $request) {

        $rules = [
            'subject' => 'required',
            'place' => 'required',
            'synthesis' =>'required',
            "users"    => "required|array|min:1",
            "users.*"  => "required|array|distinct|min:1"
        ];
        $inputs = $request->all();

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            $errors = $validator->messages();
            return response()->json(['error' => $errors], 400);
        }
        $subject = $request->get('subject');
        $place = $request->get('place');
        $synthesis = $request->get('synthesis');
        $members = $request->get('users');
        $id = $request->get('id');


        $interview = Interview::findOrFail($id);

        $interview->subject = $subject;
        $interview->synthesis = $synthesis;
        $interview->place = $place;


        $interview->save();

        foreach ($members as $member) {

            $interview->users()->sync([$member['id']], false);

        }


        return response(['message'=>'success','interview'=>$interview->id], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);

    }
    public function getByMember(Request $request, Interview $interview)
    {

        $interviews = $interview->newQuery();

        $limit = $request->get('limit');
        $sort = $request->get('sort');
        $subject = $request->get('subject');
        $place = $request->get('place');
        $synthesis = $request->get('synthesis');
        $id = $request->get('id');


        $interviews = $interviews->whereHas('users', function($query) use ($id) {
                $query->whereUserId($id);
            });



        if($subject!=null) {

            $interviews->where('subject','LIKE',"%{$subject}%");
        }

        if($place!=null) {

            $interviews->where('place','LIKE',"%{$place}%");
        }

        if($synthesis!=null) {

            $interviews->where('synthesis','LIKE',"%{$synthesis}%");
        }

        if ($sort=='-id') {

            $interviews = $interviews->orderBy('id', 'desc')->withTrashed()->paginate($limit);

            return response(['interviews'=>$interviews], 200)->withHeaders([
                'Content-Type' => 'application/json'
            ]);
        }


       $interviews = $interviews->withTrashed()->paginate($limit);


        return response(['interviews'=>$interviews], 200)->withHeaders([
            'Content-Type' => 'application/json'
        ]);
    }


}
