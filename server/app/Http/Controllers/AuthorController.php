<?php

namespace App\Http\Controllers;

use App\Models\ManuscriptContributor;
use App\Models\User;
use Illuminate\Http\Request;

class AuthorController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input("query");

        $users = User::where("name", "like", "%{$query}%")
            // ->orWhere('last_name', 'like', "%{$query}%")
            ->orWhere("email", "like", "%{$query}%")
            ->get();

        $contributors = ManuscriptContributor::where(
            "first_name",
            "like",
            "%{$query}%",
        )
            ->orWhere("last_name", "like", "%{$query}%")
            ->orWhere("email", "like", "%{$query}%")
            ->get();

        $combined = $users->map(function ($user) {
            return [
                "id" => $user->id,
                "name" => $user->name,
                // "last_name" => $user->last_name,
                "email" => $user->email,
                "affiliation" => $user->affiliation,
                "source" => "user",
            ];
        });

        foreach ($contributors as $contributor) {
            // Avoid adding duplicates if a user with the same email already exists
            if (!$combined->contains("email", $contributor->email)) {
                $combined->push([
                    "id" => $contributor->id,
                    "first_name" => $contributor->first_name,
                    "last_name" => $contributor->last_name,
                    "email" => $contributor->email,
                    "affiliation" => $contributor->affiliation,
                    "source" => "contributor",
                ]);
            }
        }

        return response()->json($combined);
    }
}
