<?php

namespace App\Http\Controllers;

use App\Exceptions\DuplicateEntryException;
use App\Helpers\CamelCaseResponse;
use App\Helpers\HandleHttpException;
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

    /**
     * Adds a pet to users' favorites.
     *
     * @param Illuminate\Http\Request $request
     * @return Json Illuminate\Http\Response
     */
    public function addToFavorites(Request $request)
    {
        $validData = $request->validate([
            'petId' => ['required', 'numeric']
        ]);

        $petId = $validData['petId'];

        $user = $request->user();
        $favorites = collect($user->favorites);

        $idExists = $favorites->contains($petId);
        if ($idExists) throw new DuplicateEntryException('This pet is favorite already.', 409);

        $favorites->push($validData['petId']);
        $user->favorites = $favorites;
        $user->save();

        return response()->json([
            'user' => $user
        ]);
    }
}
