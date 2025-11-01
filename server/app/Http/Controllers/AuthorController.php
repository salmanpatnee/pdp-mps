<?php

namespace App\Http\Controllers;

use App\Models\CoAuthor;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        $coAuthor = CoAuthor::where('email', 'like', "%{$query}%")->first();

        if ($coAuthor) {
            return response()->json([
                'id' => $coAuthor->user_id, // The frontend expects the user_id
                'user_id' => $coAuthor->user_id,
                'name' => $coAuthor->name,
                'email' => $coAuthor->email,
                'affiliation' => $coAuthor->affiliation,
                'country' => $coAuthor->country,
            ]);
        }

        return response()->json(null);
    }
}