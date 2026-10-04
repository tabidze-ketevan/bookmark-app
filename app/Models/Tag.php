<?php

namespace App\Models;

use Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['title'])]
class Tag extends Model
{
    /** @use HasFactory<TagFactory> */
    use HasFactory;

    /**
     * Colors a tag pill can take, see components/tag-pill.blade.php.
     *
     * @var list<string>
     */
    public const array COLORS = ['blue', 'violet', 'emerald', 'amber', 'rose', 'cyan', 'fuchsia', 'lime'];

    /**
     * A stable pill color for this tag, derived from its id so renaming keeps the color.
     */
    public function color(): string
    {
        return self::COLORS[((int) $this->getKey()) % count(self::COLORS)];
    }

    /**
     * The user who owns this tag.
     *
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return BelongsToMany<Bookmark, $this>
     */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Bookmark::class, 'bookmark_tag');
    }
}
