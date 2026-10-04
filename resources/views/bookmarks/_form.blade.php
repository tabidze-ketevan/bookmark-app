@csrf

@php
    $selectedTagIds = array_map('intval', old('tags', $bookmark->relationLoaded('tags') ? $bookmark->tags->pluck('id')->all() : []));
@endphp

<div class="space-y-5">
    <div>
        <label for="url" class="form-label">URL</label>
        <input type="url" id="url" name="url" value="{{ old('url', $bookmark->url) }}" required maxlength="255" placeholder="https://" autofocus class="form-input">
        @error('url')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="title" class="form-label">Title</label>
        <input type="text" id="title" name="title" value="{{ old('title', $bookmark->title) }}" required maxlength="255" class="form-input">
        @error('title')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="collection_id" class="form-label">Collection</label>
        <select id="collection_id" name="collection_id" class="form-input">
            <option value="">Unsorted</option>
            @foreach ($collections as $collection)
                <option value="{{ $collection->id }}" @selected((string) old('collection_id', $bookmark->collection_id) === (string) $collection->id)>{{ $collection->title }}</option>
            @endforeach
        </select>
        @error('collection_id')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <span class="form-label">Tags</span>
        @if ($tags->isEmpty())
            <p class="text-sm text-fg-subtle">No tags yet. <a href="{{ route('tags.create') }}" class="text-accent hover:text-accent-fg">Create a tag</a></p>
        @else
            <div class="flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    <label class="cursor-pointer">
                        <input type="checkbox" name="tags[]" value="{{ $tag->id }}" @checked(in_array($tag->id, $selectedTagIds, true)) class="peer sr-only">
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-edge px-3 py-1 text-sm text-fg-muted transition-colors peer-checked:border-accent peer-checked:bg-accent-soft peer-checked:text-fg peer-focus-visible:ring-2 peer-focus-visible:ring-accent/40 hover:border-edge-strong">
                            <x-tag-dot :tag="$tag" />{{ $tag->title }}
                        </span>
                    </label>
                @endforeach
            </div>
        @endif
        @error('tags')
            <p class="form-error">{{ $message }}</p>
        @enderror
        @error('tags.*')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="note" class="form-label">Note</label>
        <textarea id="note" name="note" rows="4" class="form-input" placeholder="Add a note…">{{ old('note', $bookmark->note) }}</textarea>
        @error('note')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <label class="inline-flex cursor-pointer items-center gap-2 text-sm text-fg-soft">
        <input type="hidden" name="is_favorite" value="0">
        <input type="checkbox" name="is_favorite" value="1" @checked(old('is_favorite', $bookmark->is_favorite)) class="size-4 rounded border-edge bg-canvas accent-accent">
        Add to favorites
    </label>

    <div class="flex items-center gap-2 border-t border-edge pt-5">
        <button type="submit" class="btn-primary">Save</button>
        <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('bookmarks.index') }}" class="btn-secondary">Cancel</a>
    </div>
</div>
