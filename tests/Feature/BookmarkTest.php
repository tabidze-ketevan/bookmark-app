<?php

use App\Models\Bookmark;

test('domain strips the www prefix from the url host', function () {
    expect(new Bookmark(['url' => 'https://www.cloudinary.com/docs'])->domain)->toBe('cloudinary.com')
        ->and(new Bookmark(['url' => 'https://drawdb.app'])->domain)->toBe('drawdb.app');
});

test('added at label is relative for recent bookmarks and a time or date for older ones', function () {
    $this->travelTo(now()->setDate(2026, 10, 4)->setTime(23, 50));

    $bookmark = new Bookmark;

    $bookmark->created_at = now()->subHour();
    expect($bookmark->addedAtLabel())->toBe('1 hour ago');

    $bookmark->created_at = now()->setTime(10, 48);
    expect($bookmark->addedAtLabel())->toBe('10:48 AM');

    $bookmark->created_at = now()->setDate(2026, 3, 2);
    expect($bookmark->addedAtLabel())->toBe('Mar 2');

    $bookmark->created_at = now()->setDate(2025, 3, 2);
    expect($bookmark->addedAtLabel())->toBe('Mar 2, 2025');
});
