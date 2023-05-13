<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\CamelCaseResponse;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Exceptions\DuplicateEntryException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    /**
     * Gets the authenticated user.
     *
     * @param Illuminate\Http\Request $request
     * @return Json Illuminate\Http\Response
     */
    public function getUser(Request $request)
    {
        $userCamelCase = $this->userCamelCase($request->user());

        return response()->json($userCamelCase, 200);
    }

    /**
     * Adds a pet to users' favorites.
     *
     * @param Illuminate\Http\Request $request
     * @throws App\Exceptions\DuplicateEntryException
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

        $userCamelCase = $this->userCamelCase($user);

        return response()->json([
            'user' => $userCamelCase
        ]);
    }

    /**
     * Removes a pet to users' favorites.
     *
     * @param Illuminate\Http\Request $request
     * @param Integer $id
     * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     * @return Json Illuminate\Http\Response
     */
    public function removeFromFavorites(Request $request, $id)
    {
        $user = $request->user();
        $favorites = collect($user->favorites);

        if (!$favorites->contains($id)) throw new NotFoundHttpException('This pet was not found in favorites.');

        $newFavorites = $favorites->reject(fn ($value) => $value == $id);

        $user->favorites = $newFavorites->flatten();
        $user->save();

        $userCamelCase = $this->userCamelCase($user);

        return response()->json([
            'user' => $userCamelCase
        ]);
    }

    public function getSearch(User $user)
    {
        return response()->json([
            'data' => $user->search,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $valid = collect($request->validated());

        $user->search = $valid;
        $user->save();

        return new UserResource($user);
    }

    /**
     * Converts user to camelCase.
     *
     * @param Illuminate\Support\Collection $user
     * @return Illuminate\Support\Collection
     */
    private function userCamelCase($user)
    {
        $userSnakeCase = collect($user);
        return CamelCaseResponse::convert($userSnakeCase);
    }
}
