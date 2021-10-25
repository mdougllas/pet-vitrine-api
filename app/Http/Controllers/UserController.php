<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use SebastianBergmann\GlobalState\Snapshot;

class UserController extends Controller
{
    public function getUser(Request $request, User $user)
    {
        $userSnakeCase = collect($request->user());

        $userCamelCase = $this->toCamelCase($userSnakeCase, $user);

        return $userCamelCase;
    }

    private function toCamelCase($arr, $user)
    {
        foreach ($arr as $key => $value) {
            $camel = Str::contains($key, '_')
                ? Str::camel($key)
                : $key;

            $user->{$camel} = $value;
        }

        return $user;
    }
}
