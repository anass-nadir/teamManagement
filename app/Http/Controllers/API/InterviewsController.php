<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Interview;
use Illuminate\Http\Request;

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

}
