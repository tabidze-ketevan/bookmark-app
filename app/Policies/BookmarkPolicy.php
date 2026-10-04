<?php

namespace App\Policies;

use App\Models\Bookmark;
use App\Models\User;

class BookmarkPolicy
{
    /**
     * Determine whether the user can view the bookmark.
     */
    public function view(User $user, Bookmark $bookmark): bool
    {
        return $bookmark->user()->is($user);
    }

    /**
     * Determine whether the user can update the bookmark.
     */
    public function update(User $user, Bookmark $bookmark): bool
    {
        return $bookmark->user()->is($user);
    }

    /**
     * Determine whether the user can delete the bookmark.
     */
    public function delete(User $user, Bookmark $bookmark): bool
    {
        return $bookmark->user()->is($user);
    }
}
