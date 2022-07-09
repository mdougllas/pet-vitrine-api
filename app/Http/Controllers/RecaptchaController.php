<?php

namespace App\Http\Controllers;

use stdClass;
use Illuminate\Http\Request;
use App\Helpers\HandleHttpException;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Expr\Cast\Object_;

class RecaptchaController extends Controller
{
    /**
     * TODO - finish this docblock
     *
     * @param  Illuminate\Http\Request $request
     * @return string
     */
    public function checkToken(Request $request)
    {
        $validated = $request->validate(['token' => 'required']);

        $data = [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $validated['token']
        ];

        $response = Http::asForm()->post(config('services.recaptcha.url'), $data);
        $response->onError(fn ($err) => HandleHttpException::throw($err));

        return !$response['success']
            ? $this->translateGoogleErrors($response['error-codes'])
            : response($response, 200);
    }

    /**
     * TODO - finish this docblock
     *
     * @param  Illuminate\Http\Request $request
     * @return string
     */
    private function translateGoogleErrors($errors)
    {
        $errorsCollection = collect([
            ['key' => 'bad-request', 'value' => 'Request invalid or malformed.'],
            ['key' => 'invalid-input-response', 'value' => "Google reCaptcha token invalid or malformed."],
            ['key' => 'invalid-input-secret', 'value' => "Google reCaptcha secret invalid or malformed."],
            ['key' => 'missing-input-response', 'value' => "Missing Google reCaptcha token."],
            ['key' => 'missing-input-secret', 'value' => "Missing Google reCaptcha secret."],
            ['key' => 'timeout-or-duplicate', 'value' => "Google reCaptcha token expired or duplicated."],
        ]);

        $errorsFlattened = implode('', $errors);

        $message = $errorsCollection->firstWhere('key', $errorsFlattened);

        return response()->json([
            'errors' => [
                'token' => [
                    $message['value']
                ]
            ]
        ], 403);
    }
}
