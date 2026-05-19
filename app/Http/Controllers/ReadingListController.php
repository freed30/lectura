<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Favorite;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReadingListController extends Controller
{
    public function toggleFavorite(Request $request, Book $book): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $favorite = Favorite::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            $active  = false;
            $message = "« {$book->title} » retiré de vos favoris.";
        } else {
            Favorite::query()->create([
                'user_id' => $user->id,
                'book_id' => $book->id,
            ]);
            $active  = true;
            $message = "« {$book->title} » ajouté à vos favoris.";
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'active'  => $active,
                'message' => $message,
                'count'   => Favorite::query()->where('user_id', $user->id)->count(),
            ]);
        }

        return back()->with('status', $message);
    }

    public function toggleWishlist(Request $request, Book $book): JsonResponse|RedirectResponse
    {
        $user = $request->user();

        $wishlist = Wishlist::query()
            ->where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $active  = false;
            $message = "« {$book->title} » retiré de votre liste d'envie.";
        } else {
            Wishlist::query()->create([
                'user_id'  => $user->id,
                'book_id'  => $book->id,
                'priority' => 1,
            ]);
            $active  = true;
            $message = "« {$book->title} » ajouté à votre liste d'envie.";
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'active'  => $active,
                'message' => $message,
                'count'   => Wishlist::query()->where('user_id', $user->id)->count(),
            ]);
        }

        return back()->with('status', $message);
    }
}
