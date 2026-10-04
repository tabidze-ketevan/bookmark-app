@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="form-label">Name</label>
        <input type="text" id="title" name="title" value="{{ old('title', $tag->title) }}" required maxlength="255" autofocus placeholder="e.g. inspiration" class="form-input">
        @error('title')
            <p class="form-error">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex items-center gap-2 border-t border-edge pt-5">
        <button type="submit" class="btn-primary">Save</button>
        <a href="{{ route('tags.index') }}" class="btn-secondary">Cancel</a>
    </div>
</div>
