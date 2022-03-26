<?php

namespace App\Http\Controllers;

use App\Helpers\CamelCaseResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Gets the authenticated user.
     *
     * @param Illuminate\Http\Request $request
     * @throws App\Models\User $user
     * @return Json Illuminate\Http\Response
     */
    public function getUser(Request $request)
    {
        $userSnakeCase = collect($request->user());
        $userCamelCase = CamelCaseResponse::convert($userSnakeCase);

        return response()->json($userCamelCase, 200);
    }
}
