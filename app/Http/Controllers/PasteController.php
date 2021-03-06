<?php

namespace App\Http\Controllers;

use App\Paste;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PasteController extends Controller
{
    /**
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [

            'user' => 'required|string',
            'language' => 'required|string', // TODO set languages,
            'title' => 'required|string',
            'code' => 'required|string|min:10',
            'private' => 'nullable|string',

        ], $this->messages());

        if($validator->fails()) {

            return response()->json([

                'saved' => false,
                'errors' => $validator->errors(),
            ]);
        }

        $slug = $this->findSlug();
        $private = $request->input('private') ? Hash::make($request->input('private')) : null;

        Paste::create([

            'slug' => $slug,
            'user' => $request->input('user'),
            'language' => $request->input('language'),
            'title' => $request->input('title'),
            'code' => $request->input('code'),
            'private' => $private,
        ]);

        return response()->json([

            'saved' => true,
            'slug' => $slug,
        ]);
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'code.required'  => 'You must add at least 10 characters.',
            'code.min'  => 'You must add at least 10 characters.',
        ];
    }

    /**
     * Find a random available slug
     *
     * @return string
     */
    private function findSlug(): string
    {
        $slug = Str::random(8);

        if(Paste::where('slug', $slug)->first() != null)
            return $this->findSlug();

        return $slug;
    }

    /**
     * Get a paste thanks to his slug
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function get(Request $request, string $slug): JsonResponse
    {
        $paste = Paste::where('slug', $slug)->first();

        if($paste) {

            return response()->json([

                'exist' => true,
                'user' => $paste->user,
                'language' => $paste->language,
                'title' => $paste->title,
                'code' => $paste->code,
                'private' => ($paste->private ? true : false),
            ]);
        }

        return response()->json([

            'exist' => false,
        ]);
    }
}
