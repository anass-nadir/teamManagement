<?php
namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;

class AuthController extends Controller
{
    /**
     * @api {post} /user/signIn
     * @apiName  autheticate users
     *
     *
     * @apiParam  {String} email
     * @apiParam  {String} password
     *
     *
     * @apiSuccess (200) token
     *
     */
    public function authenticate(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        // grab credentials from the request
        $credentials = $request->only(['email', 'password']);
        $validator = Validator::make($credentials, $rules);
        if ($validator->fails()) {
            $errors = $validator->messages()->toJson();
            return response()->json(['error' => $errors], 400);
        }
        try {
            // attempt to verify the credentials and create a token for the user
            if (!$token = JWTAuth::attempt($credentials)) {
                return response([
                    'status' => 'error',
                    'error' => 'Invalid Credentials.',
                ], 400);
            }
        } catch (JWTException $e) {
            // something went wrong while attempting to encode the token
            return response()->json(['error' => 'could_not_create_token'], 500);
        }
        // all good so return the token
        return response()->json(['token' => $token], 200);
    }
    /**
     * @api {post} /user/logout
     *
     * @apiName logout user
     */
    public function logout()
    {
        // kill token
        JWTAuth::invalidate();
        return response([
            'status' => 'success',
            'message' => 'Logged out Successfully.',
        ], 200);
    }
    /**
     * @api {post} /user/auth/refreshToken
     *
     * @apiName refresh token
     * @apiDescription refresh token after expiration
     *
     *
     */
    public function refreshedToken()
    {
        $newToken = JWTAuth::parseToken()->refresh();
        return response([
            'status' => 'success',
            'token' => $newToken,
        ], 200);
    }

    public function getUserData(Request $request)
    {
        $token = $request->get('token');

        $user  = JWTAuth::toUser($token);
        return response([
            'status' => 'success',
            'user' => $user
        ], 200);
    }

}
