<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagRequest;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $tags = $request->user()->tags()
            ->withCount('bookmarks')
            ->orderBy('title')
            ->paginate(50);

        return view('tags.index', ['tags' => $tags]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('tags.create', ['tag' => new Tag]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TagRequest $request): RedirectResponse
    {
        $tag = $request->user()->tags()->create($request->validated());

        return redirect()->route('bookmarks.index', ['tag' => $tag])->with('status', 'Tag added.');
    }

    /**
     * Show the tag's bookmarks in the main bookmark list.
     */
    public function show(Tag $tag): RedirectResponse
    {
        Gate::authorize('view', $tag);

        return redirect()->route('bookmarks.index', ['tag' => $tag]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tag $tag): View
    {
        Gate::authorize('update', $tag);

        return view('tags.edit', ['tag' => $tag]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TagRequest $request, Tag $tag): RedirectResponse
    {
        $tag->update($request->validated());

        return redirect()->route('bookmarks.index', ['tag' => $tag])->with('status', 'Tag updated.');
    }

    /**
     * Remove the specified resource from storage. The tag is detached from its bookmarks first.
     */
    public function destroy(Tag $tag): RedirectResponse
    {
        Gate::authorize('delete', $tag);

        DB::transaction(function () use ($tag): void {
            $tag->bookmarks()->detach();
            $tag->delete();
        });

        return redirect()->route('tags.index')->with('status', 'Tag deleted.');
    }
}
