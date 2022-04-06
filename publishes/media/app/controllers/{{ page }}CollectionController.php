<?php

namespace {{ namespace }}\Http\Controllers;

use {{ namespace }}\Http\Indexes\{{ page }}Index;
use {{ namespace }}\Http\Resources\{{ page }}CollectionResource;
use {{ namespace }}\Ui\Page;
use App\Models\{{ file_model }};
use App\Models\{{ page }}Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class {{ page }}CollectionController
{
    /**
     * Ship index page.
     *
     * @param  Page $page
     * @return Page
     */
    public function items(
        Request $request,
        {{ page }}Collection $collection,
        {{ page }}Index $index
    ) {
        return $index->items($request, $collection->files());
    }

    /**
     * Show the file.
     *
     * @param  Page $page
     * @return Page
     */
    public function show(Page $page, {{ page }}Collection $collection)
    {
        $collections = {{ page }}Collection::withCount('files')->get();

        return $page
            ->page('{{ page }}/Index')
            ->with('collection', new {{ page }}CollectionResource($collection))
            ->with('collections', {{ page }}CollectionResource::collection($collections));
    }

    /**
     * Create a new {{ page }}Collection.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        {{ page }}Collection::create([
            'title' => $request->title,
            'key'   => Str::slug($request->title),
        ]);

        return redirect()->back();
    }

    /**
     * Add a list of files to the collection.
     *
     * @param Request $request
     * @param {{ page }}Collection $collection
     * @return RedirectResponse
     */
    public function add(Request $request, {{ page }}Collection $collection)
    {
        $request->validate([
            'files' => 'required|array'
        ]);

        {{ file_model }}::whereIn('id', $request->files)->get()
            ->each(function ({{ file_model }} $file) use ($collection) {
                if ($collection->isAttachedTo($file)) {
                    return;
                }

                $file->attach($collection);
            });

        return redirect()->back();
    }

    /**
     * Remove a list of files from the collection.
     *
     * @param Request $request
     * @param {{ page }}Collection $collection
     * @return RedirectResponse
     */
    public function remove(Request $request, {{ page }}Collection $collection)
    {
        $request->validate([
            'files' => 'required|array'
        ]);

        {{ file_model }}::whereIn('id', $request->files)->get()
            ->each(function ({{ file_model }} $file) use ($collection) {
                if (! $collection->isAttachedTo($file)) {
                    return;
                }

                $file->detach($collection);
            });

        return redirect()->back();
    }

    /**
     * Upload files.
     *
     * @param Request $request
     * @param {{ page }}Collection $collection
     * @return void
     */
    public function upload(Request $request, {{ page }}Collection $collection)
    {
        collect($request->files->get('images'))
            ->each(function (UploadedFile $file) use ($collection) {
                $file = {{ file_model }}::createFromUploadedFile($file);

                $file->save();

                $file->attach($collection);
            });
    }
}
