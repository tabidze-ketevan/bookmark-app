<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookmarkRequest;
use App\Models\Bookmark;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookmarkController extends Controller
{
    /**
     * Display the user's bookmarks, narrowed by the search query and sidebar filters.
     *
     * Supported query parameters: search, filter (unsorted|favorites), collection, tag, sort and view.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $collection = $request->filled('collection')
            ? $user->collections()->findOrFail($request->integer('collection'))
            : null;

        $tag = $request->filled('tag')
            ? $user->tags()->findOrFail($request->integer('tag'))
            : null;

        $filters = $this->listFilters($request);

        $bookmarks = $user->bookmarks()
            ->with(['collection', 'tags'])
            ->filter($filters)
            ->sortBy($request->string('sort')->toString())
            ->paginate(30)
            ->withQueryString();

        [$heading, $icon] = match (true) {
            $collection !== null => [$collection->title, 'folder'],
            $tag !== null => ['#'.$tag->title, 'hashtag'],
            $filters['unsorted'] => ['Unsorted', 'inbox'],
            $filters['favorites'] => ['Favorites', 'star'],
            default => ['All bookmarks', 'bookmark'],
        };

        return view('bookmarks.index', [
            'bookmarks' => $bookmarks,
            'heading' => $heading,
            'headingIcon' => $icon,
            'search' => $filters['search'],
            'currentCollection' => $collection,
            'currentTag' => $tag,
            'exportQuery' => array_filter($request->only(['search', 'filter', 'collection', 'tag', 'sort'])),
        ]);
    }

    /**
     * Download the filtered bookmarks as a CSV file.
     */
    public function export(Request $request): StreamedResponse
    {
        $bookmarks = $request->user()->bookmarks()
            ->with(['collection', 'tags'])
            ->filter($this->listFilters($request))
            ->sortBy($request->string('sort')->toString());

        return response()->streamDownload(function () use ($bookmarks): void {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['title', 'url', 'note', 'collection', 'tags', 'favorite', 'created_at'], escape: '');

            $bookmarks->lazy()->each(function (Bookmark $bookmark) use ($output): void {
                fputcsv($output, [
                    $bookmark->title,
                    $bookmark->url,
                    $bookmark->note,
                    $bookmark->collection?->title,
                    $bookmark->tags->pluck('title')->implode(', '),
                    $bookmark->is_favorite ? 'yes' : 'no',
                    $bookmark->created_at?->toDateTimeString(),
                ], escape: '');
            });

            fclose($output);
        }, 'bookmarks-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): View
    {
        return view('bookmarks.create', [
            'bookmark' => new Bookmark(['collection_id' => $request->integer('collection') ?: null]),
            'collections' => $request->user()->collections()->orderBy('title')->get(),
            'tags' => $request->user()->tags()->orderBy('title')->get(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BookmarkRequest $request): RedirectResponse
    {
        $bookmark = DB::transaction(function () use ($request): Bookmark {
            $bookmark = $request->user()->bookmarks()->create($request->safe()->except('tags'));
            $bookmark->tags()->sync($request->validated('tags', []));

            return $bookmark;
        });

        return redirect()->route('bookmarks.show', $bookmark)->with('status', 'Bookmark added.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Bookmark $bookmark): View
    {
        Gate::authorize('view', $bookmark);

        $bookmark->load(['collection', 'tags']);

        return view('bookmarks.show', ['bookmark' => $bookmark]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Bookmark $bookmark): View
    {
        Gate::authorize('update', $bookmark);

        $bookmark->load('tags');

        return view('bookmarks.edit', [
            'bookmark' => $bookmark,
            'collections' => $request->user()->collections()->orderBy('title')->get(),
            'tags' => $request->user()->tags()->orderBy('title')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BookmarkRequest $request, Bookmark $bookmark): RedirectResponse
    {
        DB::transaction(function () use ($request, $bookmark): void {
            $bookmark->update($request->safe()->except('tags'));
            $bookmark->tags()->sync($request->validated('tags', []));
        });

        return redirect()->route('bookmarks.show', $bookmark)->with('status', 'Bookmark updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Bookmark $bookmark): RedirectResponse
    {
        Gate::authorize('delete', $bookmark);

        DB::transaction(function () use ($bookmark): void {
            $bookmark->tags()->detach();
            $bookmark->delete();
        });

        return redirect()->route('bookmarks.index')->with('status', 'Bookmark deleted.');
    }

    /**
     * The list filters shared by the index page and the export.
     *
     * @return array{search: string|null, collection: int|null, tag: int|null, unsorted: bool, favorites: bool}
     */
    private function listFilters(Request $request): array
    {
        return [
            'search' => $request->string('search')->trim()->limit(255, '')->toString() ?: null,
            'collection' => $request->integer('collection') ?: null,
            'tag' => $request->integer('tag') ?: null,
            'unsorted' => $request->query('filter') === 'unsorted',
            'favorites' => $request->query('filter') === 'favorites',
        ];
    }
}
