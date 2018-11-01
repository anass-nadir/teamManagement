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

    public function index(Request $request)
    {
        $limit = $request->get('limit');

        $sort = $request->get('sort');
        if ($sort=='-id')
            return Interview::orderBy('id', 'desc')->withTrashed()->paginate($limit);

        return Interview::withTrashed()->paginate($limit);
    }
    public function destroy(Request $request)
    {
        $id = $request->get('id');
        $interview = Interview::onlyTrashed()->findOrFail($id);
        $interview->forceDelete();

        return 'deleted';
    }

    public function trash(Request $request)
    {
        $id = $request->get('id');
        $interview = Interview::findOrFail($id);
        $interview->delete();

        return 'deleted';
    }

}
