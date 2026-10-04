<?php

namespace App\Models;

use Database\Factories\BookmarkFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

#[Fillable(['collection_id', 'title', 'note', 'url', 'is_favorite'])]
class Bookmark extends Model
{
    /** @use HasFactory<BookmarkFactory> */
    use HasFactory;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_favorite' => 'boolean',
        ];
    }

    /**
     * The bookmark URL's host without the "www." prefix, e.g. "cloudinary.com".
     *
     * @return Attribute<string, never>
     */
    protected function domain(): Attribute
    {
        return Attribute::get(
            fn (): string => Str::of((string) parse_url((string) $this->url, PHP_URL_HOST))->after('www.')->toString(),
        );
    }

    /**
     * A short, human-friendly label for when the bookmark was added.
     *
     * Recent items read "1 hour ago", items from today read "10:48 PM", older items show the date.
     */
    public function addedAtLabel(): string
    {
        $createdAt = $this->created_at;

        if ($createdAt === null) {
            return '';
        }

        return match (true) {
            $createdAt->gt(now()->subHours(3)) => $createdAt->diffForHumans(),
            $createdAt->isToday() => $createdAt->format('g:i A'),
            $createdAt->isCurrentYear() => $createdAt->format('M j'),
            default => $createdAt->format('M j, Y'),
        };
    }

    /**
     * Apply the list filters used by the bookmark pages and export.
     *
     * @param  Builder<Bookmark>  $query
     * @param  array{search?: string|null, collection?: int|string|null, tag?: int|string|null, unsorted?: bool, favorites?: bool}  $filters
     */
    #[Scope]
    protected function filter(Builder $query, array $filters): void
    {
        $query
            ->when($filters['search'] ?? null, function (Builder $query, string $search): void {
                $query->where(function (Builder $query) use ($search): void {
                    $query->whereLike('title', "%{$search}%")
                        ->orWhereLike('url', "%{$search}%")
                        ->orWhereLike('note', "%{$search}%");
                });
            })
            ->when($filters['collection'] ?? null, fn (Builder $query, int|string $collectionId) => $query->where('collection_id', $collectionId))
            ->when($filters['tag'] ?? null, fn (Builder $query, int|string $tagId) => $query->whereRelation('tags', 'tags.id', $tagId))
            ->when($filters['unsorted'] ?? false, fn (Builder $query) => $query->whereNull('collection_id'))
            ->when($filters['favorites'] ?? false, fn (Builder $query) => $query->where('is_favorite', true));
    }

    /**
     * Order bookmarks by one of the supported sort keys: "newest", "oldest" or "title".
     *
     * @param  Builder<Bookmark>  $query
     */
    #[Scope]
    protected function sortBy(Builder $query, ?string $sort): void
    {
        match ($sort) {
            'oldest' => $query->oldest()->oldest('id'),
            'title' => $query->orderBy('title'),
            default => $query->latest()->latest('id'),
        };
    }

    /**
     * The user who owns this bookmark.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsTo<Collection, $this>
     */
    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'bookmark_tag');
    }
}
