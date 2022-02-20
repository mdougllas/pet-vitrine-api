<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use PhpParser\Node\Expr\Cast\Object_;
use stdClass;

class RecaptchaController extends Controller
{
    public function checkToken(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required'
        ]);

        $data = [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $validated['token']
        ];

        $response = Http::asForm()->post(config('services.recaptcha.url'), $data);

        if (!$response['success']) {
            $errors = [];

            foreach ($response['error-codes'] as $error) {
                switch ($error) {
                    case 'missing-input-secret':
                        $errors['secret'] = ["Missing secret."];
                        break;

                    case 'invalid-input-secret':
                        $errors['secret'] = ["Secret invalid or malformed."];
                        break;

                    case 'missing-input-response':
                        $errors['secret'] = ["Missing token."];
                        break;

                    case 'invalid-input-response':
                        $errors['token'] = ["Token invalid or malformed."];
                        break;

                    case 'bad-request':
                        $errors['token'] = ["Request invalid or malformed."];
                        break;

                    case 'timeout-or-duplicate':
                        $errors['token'] = ["Token expired or duplicated."];
                        break;

                    default:
                        $errors['token'] = ["Google reCaptcha is not responding."];
                }
            }

            return response()->json(['errors' => $errors], 403);
        }

        return response($response, 200);
    }
}
