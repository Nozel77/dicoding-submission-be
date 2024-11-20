<?php

namespace App\Http\Controllers;

use App\Http\Requests\BannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index() :JsonResponse
    {
        $data = Banner::all();
        return $this->resShowData(BannerResource::collection($data));
    }

    public function show($id) :JsonResponse
    {
        $banner = Banner::find($id);
        if (! $banner) {
            return $this->resDataNotFound('Banner');
        }
        return $this->resShowData(new BannerResource($banner));
    }

    public function store(BannerRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $banner = new Banner();
        $banner->fill($data);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->storePublicly('banner', 'public');
            $banner->image = url(Storage::url($image));
        } else {
            return $this->resDataNotFound('image');
        }

        $banner->save();
        return $this->resAddData(new BannerResource($banner));
    }

    public function update(BannerRequest $request, $id) : JsonResponse
    {
        $data = $request->validated();

        $banner = Banner::find($id);
        if (! $banner) {
            return $this->resDataNotFound('Banner');
        }

        $banner->fill($data);

        if ($request->hasFile('image')) {
            $image = $request->file('image')->storePublicly('banner', 'public');
            $banner->image = url(Storage::url($image));
        }

        $banner->save();
        return $this->resUpdatedData(new BannerResource($banner));
    }

    public function destroy($id) : JsonResponse
    {
        $banner = Banner::find($id);
        if (! $banner) {
            return $this->resDataNotFound('Banner');
        }

        $banner->delete();
        return $this->resDataDeleted();
    }
}
