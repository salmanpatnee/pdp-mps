<?php

namespace App\Http\Controllers;

use App\Models\ManuscriptCoAuthor;
use App\Models\User;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('query');

        // Search only in manuscript_co_authors table by email
        $coAuthor = ManuscriptCoAuthor::where('email', 'like', "%{$query}%")->first();

        if ($coAuthor) {

            return response()->json([
            'id' => $coAuthor->id,
            'name' => $coAuthor->name,
            'email' => $coAuthor->email,
            'affiliation' => $coAuthor->affiliation,
            'country' => $coAuthor->country,
            ]);
        }

        return response()->json(null);
        }
}