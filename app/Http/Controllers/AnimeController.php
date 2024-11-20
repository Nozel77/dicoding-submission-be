<?php

namespace App\Http\Controllers;

use App\Http\Requests\AnimeRequest;
use App\Http\Resources\AnimeResource;
use App\Http\Resources\FavoriteResource;
use App\Models\Anime;
use App\Models\Favorite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AnimeController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $page = $request->input('page', 1);
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Anime::orderBy('created_at', 'desc');

        if ($search) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        $data = $query->paginate($perPage, ['*'], 'page', $page);

        return $this->resShowData(AnimeResource::collection($data));
    }


    public function show($id): JsonResponse
    {
        $ongoingAnime = Anime::all()->find($id);

        if (! $ongoingAnime) {
            return $this->resDataNotFound('Anime');
        }

        return $this->resShowData(new AnimeResource($ongoingAnime));
    }

    public function store(AnimeRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $ongoingAnime = new Anime();
        $ongoingAnime->fill($data);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->storePublicly('anime', 'public');
            $ongoingAnime->image = url(Storage::url($image));
        } else {
            return $this->resDataNotFound('image');
        }

        $ongoingAnime->save();

        return $this->resAddData(new AnimeResource($ongoingAnime));
    }

    public function update(AnimeRequest $request, $id): JsonResponse
    {
        $data = $request->validated();

        $ongoingAnime = Anime::all()->find($id);

        if (! $ongoingAnime) {
            return $this->resDataNotFound('Ongoing Anime');
        }

        if ($request->hasFile('image')) {
            Storage::delete('public/'.$ongoingAnime->image);
            $image = $request->file('image')->storePublicly('anime', 'public');
            $data['image'] = url(Storage::url($image));
        }

        $ongoingAnime->fill($data);
        $ongoingAnime->save();

        return $this->resUpdatedData(new AnimeResource($ongoingAnime));
    }

    public function destroy($id): JsonResponse
    {
        $ongoingAnime = Anime::all()->find($id);

        if (! $ongoingAnime) {
            return $this->resDataNotFound('Ongoing Anime');
        }

        Storage::delete('public/'.$ongoingAnime->image);
        $ongoingAnime->delete();

        return $this->resDataDeleted();
    }

    public function addToFavorite(Request $request, $id) : JsonResponse
    {
        $userId = Auth::id();

        if (! $userId) {
            return $this->resUserNotFound();
        }

        $anime = Anime::all()->find($id);

        if (! $anime) {
            return $this->resDataNotFound('Anime');
        }

        $favorite = Favorite::all()->where('user_id', $userId)->where('anime_id', $id)->first();

        if ($favorite) {
            $favorite->delete();
            $isliked = false;
            $message = 'Anime removed from favorite';
        } else {
            $favorite = new Favorite();
            $favorite->user_id = $userId;
            $favorite->anime_id = $id;
            $favorite->save();
            $isliked = true;
        }

        return $this->resShowData(new AnimeResource($anime));
    }

    public function getFavoriteAnime(Request $request) : JsonResponse
    {
        $userId = Auth::id();

        if (! $userId) {
            return $this->resUserNotFound();
        }

        $search = $request->input('search');

        $favoritesQuery = Favorite::where('user_id', $userId)
            ->with('anime');

        if ($search) {
            $favoritesQuery->whereHas('anime', function ($query) use ($search) {
                $query->where('title', 'like', '%' . $search . '%');
            });
        }

        $favorites = $favoritesQuery->get();

        if ($favorites->isEmpty()) {
            return response()->json([
                'status_code' => 404,
                'message' => 'No favorite anime found',
                'data' => [],
            ]);
        }

        $favoriteAnimes = $favorites->map(function ($favorite) {
            return new AnimeResource($favorite->anime);
        });

        return response()->json([
            'status_code' => 200,
            'message' => 'Favorite anime retrieved successfully',
            'data' => $favoriteAnimes,
        ]);
    }


    public function getOngoingAnime()
    {
        $data = Anime::where('status', 'Ongoing')->get();

        if ($data->isEmpty()) {
            return $this->resDataNotFound('Ongoing Anime');
        }

        return $this->resShowData(AnimeResource::collection($data));
    }

    public function getPopularAnime()
    {
        $data = Anime::whereBetween('rating', [8, 10])->get();

        if ($data->isEmpty()) {
            return $this->resDataNotFound('Completed Anime with rating between 8 and 10');
        }

        return $this->resShowData(AnimeResource::collection($data));
    }

}
