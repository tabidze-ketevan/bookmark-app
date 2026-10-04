<?php

namespace App\Http\Controllers;

use App\Http\Requests\CollectionRequest;
use App\Models\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class CollectionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $collections = $request->user()->collections()
            ->withCount('bookmarks')
            ->orderBy('title')
            ->paginate(50);

        return view('collections.index', ['collections' => $collections]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('collections.create', ['collection' => new Collection]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CollectionRequest $request): RedirectResponse
    {
        $collection = $request->user()->collections()->create($request->validated());

        return redirect()->route('bookmarks.index', ['collection' => $collection])->with('status', 'Collection added.');
    }

    /**
     * Show the collection's bookmarks in the main bookmark list.
     */
    public function show(Collection $collection): RedirectResponse
    {
        Gate::authorize('view', $collection);

        return redirect()->route('bookmarks.index', ['collection' => $collection]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Collection $collection): View
    {
        Gate::authorize('update', $collection);

        return view('collections.edit', ['collection' => $collection]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CollectionRequest $request, Collection $collection): RedirectResponse
    {
        $collection->update($request->validated());

        return redirect()->route('bookmarks.index', ['collection' => $collection])->with('status', 'Collection updated.');
    }

    /**
     * Remove the specified resource from storage. Bookmarks are kept and become uncategorized.
     */
    public function destroy(Collection $collection): RedirectResponse
    {
        Gate::authorize('delete', $collection);

        DB::transaction(function () use ($collection): void {
            $collection->bookmarks()->update(['collection_id' => null]);
            $collection->delete();
        });

        return redirect()->route('collections.index')->with('status', 'Collection deleted.');
    }
}
