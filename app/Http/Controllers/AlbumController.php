<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAlbumRequest;
use App\Http\Requests\UpdateAlbumRequest;
use App\Models\Album;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;

class AlbumController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function index(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('Frontend.pages.album.show');
    }

    /**
     * Show the form for creating a new resource.
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function create(): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('Backend.pages.album.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param StoreAlbumRequest $request
     * @return RedirectResponse
     */
    public function store(StoreAlbumRequest $request): RedirectResponse
    {
        $albumImage = time() . 'album' . '.' . $request->file('image')->getClientOriginalExtension();
        $request->image->move('images/album', $albumImage);

        if (auth()->check() && auth()->user()->role == 1)
        {
            $album = new Album([
                'user_id' => auth()->user()->id,
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'image' => $albumImage,
                'is_status' => $request->get('is_status')
            ]);
        }else{
            $album = new Album([
                'user_id' => auth()->user()->id,
                'title' => $request->get('title'),
                'description' => $request->get('description'),
                'image' => $albumImage,
            ]);
        }

        $album->save();
        return redirect()->route('index');
    }

    /**
     * Display the specified resource.
     * @param Album $album
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function show(Album $album): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('Frontend.pages.album.show', compact('album'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param Album $album
     * @return Factory|Application|View|\Illuminate\Contracts\Foundation\Application
     */
    public function edit(Album $album): Factory|Application|View|\Illuminate\Contracts\Foundation\Application
    {
        return view('Backend.pages.album.edit', compact('album'));
    }

    /**
     * Update the specified resource in storage.
     * @param UpdateAlbumRequest $request
     * @param Album $album
     * @return RedirectResponse
     */
    public function update(UpdateAlbumRequest $request, Album $album): RedirectResponse
    {
        if (!is_null($request->file('image'))) {
            $fileName = 'images/album/' . $album->image;
            unlink($fileName);
            $albumImage = time() . 'album' . '.' . $request->file('image')->getClientOriginalExtension();
            $request->image->move('images/album', $albumImage);
            $album->image = $albumImage;
        }

        $album->description = $request->get('description');
        $album->slug = null;
        $album->title = $request->get('title');

        if (auth()->check() && auth()->user()->role == 1)
        {
            $album->is_status = $request->get('is_status');
        }


        $album->update();
        return redirect()->route('album.show', $album->slug);
    }

    /**
     * Remove the specified resource from storage.
     * @param Album $album
     * @return RedirectResponse
     */
    public function destroy(Album $album): RedirectResponse
    {
        $album->delete();
        return redirect()->route('index');
    }
}
