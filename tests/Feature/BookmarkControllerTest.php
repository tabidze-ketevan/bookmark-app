<?php

use App\Models\Bookmark;
use App\Models\Collection;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('guests are redirected to the login page', function () {
    auth()->logout();

    $this->get(route('bookmarks.index'))->assertRedirect(route('login'));
    $this->post(route('bookmarks.store'))->assertRedirect(route('login'));
});

test('index lists the user bookmarks with their collection and tags', function () {
    $tag = Tag::factory()->for($this->user)->create();
    $bookmark = Bookmark::factory()->for($this->user)->hasAttached($tag, [], 'tags')->create();

    $this->get(route('bookmarks.index'))
        ->assertOk()
        ->assertSee($bookmark->title)
        ->assertSee($bookmark->collection->title)
        ->assertSee($tag->title);
});

test('index and sidebar do not show other users data', function () {
    $otherUser = User::factory()->create();
    Bookmark::factory()->for($otherUser)->create(['title' => 'Someone elses link']);
    Collection::factory()->for($otherUser)->create(['title' => 'Someone elses collection']);
    Tag::factory()->for($otherUser)->create(['title' => 'someone-elses-tag']);

    $this->get(route('bookmarks.index'))
        ->assertOk()
        ->assertDontSee('Someone elses link')
        ->assertDontSee('Someone elses collection')
        ->assertDontSee('someone-elses-tag');
});

test('create and edit forms render with only the user collections and tags', function () {
    $bookmark = Bookmark::factory()->for($this->user)->create();
    Collection::factory()->create(['title' => 'Foreign collection']);
    Tag::factory()->create(['title' => 'foreign-tag']);

    $this->get(route('bookmarks.create'))->assertOk()->assertDontSee('Foreign collection')->assertDontSee('foreign-tag');
    $this->get(route('bookmarks.edit', $bookmark))->assertOk()->assertSee($bookmark->url);
});

test('show displays a bookmark', function () {
    $bookmark = Bookmark::factory()->for($this->user)->create(['note' => 'My private note']);

    $this->get(route('bookmarks.show', $bookmark))->assertOk()->assertSee('My private note');
});

test('store creates a bookmark owned by the user', function () {
    $collection = Collection::factory()->for($this->user)->create();
    $tags = Tag::factory()->for($this->user)->count(2)->create();

    $response = $this->post(route('bookmarks.store'), [
        'title' => 'Laravel',
        'url' => 'https://laravel.com',
        'note' => 'Docs',
        'collection_id' => $collection->id,
        'is_favorite' => '1',
        'tags' => $tags->pluck('id')->all(),
    ]);

    $bookmark = Bookmark::sole();
    $response->assertRedirect(route('bookmarks.show', $bookmark));

    expect($bookmark)
        ->user_id->toBe($this->user->id)
        ->title->toBe('Laravel')
        ->collection_id->toBe($collection->id)
        ->is_favorite->toBeTrue()
        ->and($bookmark->tags->pluck('id')->sort()->values()->all())->toBe($tags->pluck('id')->sort()->values()->all());
});

test('store validates input', function () {
    $this->post(route('bookmarks.store'), [
        'title' => '',
        'url' => 'not-a-url',
        'collection_id' => 999,
        'tags' => [999],
    ])->assertSessionHasErrors(['title', 'url', 'collection_id', 'tags.0']);

    expect(Bookmark::count())->toBe(0);
});

test('store rejects collections and tags owned by another user', function () {
    $foreignCollection = Collection::factory()->create();
    $foreignTag = Tag::factory()->create();

    $this->post(route('bookmarks.store'), [
        'title' => 'Sneaky',
        'url' => 'https://example.com',
        'collection_id' => $foreignCollection->id,
        'tags' => [$foreignTag->id],
    ])->assertSessionHasErrors(['collection_id', 'tags.0']);

    expect(Bookmark::count())->toBe(0);
});

test('update changes fields and syncs tags', function () {
    $oldTag = Tag::factory()->for($this->user)->create();
    $newTag = Tag::factory()->for($this->user)->create();
    $bookmark = Bookmark::factory()->for($this->user)->hasAttached($oldTag, [], 'tags')->create(['is_favorite' => true]);

    $this->put(route('bookmarks.update', $bookmark), [
        'title' => 'Updated',
        'url' => 'https://example.com',
        'collection_id' => '',
        'is_favorite' => '0',
        'tags' => [$newTag->id],
    ])->assertRedirect(route('bookmarks.show', $bookmark));

    $bookmark->refresh();

    expect($bookmark)
        ->title->toBe('Updated')
        ->collection_id->toBeNull()
        ->is_favorite->toBeFalse()
        ->and($bookmark->tags->pluck('id')->all())->toBe([$newTag->id]);
});

test('destroy deletes the bookmark and its tag links', function () {
    $tag = Tag::factory()->for($this->user)->create();
    $bookmark = Bookmark::factory()->for($this->user)->hasAttached($tag, [], 'tags')->create();

    $this->delete(route('bookmarks.destroy', $bookmark))->assertRedirect(route('bookmarks.index'));

    $this->assertModelMissing($bookmark);
    $this->assertModelExists($tag);
    $this->assertDatabaseCount('bookmark_tag', 0);
});

test('users cannot view, edit, update or delete another users bookmark', function () {
    $bookmark = Bookmark::factory()->create(['title' => 'Not mine']);

    $this->get(route('bookmarks.show', $bookmark))->assertForbidden();
    $this->get(route('bookmarks.edit', $bookmark))->assertForbidden();
    $this->put(route('bookmarks.update', $bookmark), ['title' => 'Hijacked', 'url' => 'https://evil.test'])->assertForbidden();
    $this->delete(route('bookmarks.destroy', $bookmark))->assertForbidden();

    $this->assertModelExists($bookmark);
    expect($bookmark->refresh()->title)->toBe('Not mine');
});

test('unsorted filter only lists bookmarks without a collection', function () {
    Bookmark::factory()->for($this->user)->create(['collection_id' => null, 'title' => 'Loose link']);
    Bookmark::factory()->for($this->user)->create(['title' => 'Filed link']);

    $this->get(route('bookmarks.index', ['filter' => 'unsorted']))
        ->assertOk()
        ->assertSee('Loose link')
        ->assertDontSee('Filed link');
});

test('favorites filter only lists favorite bookmarks', function () {
    Bookmark::factory()->for($this->user)->create(['is_favorite' => true, 'title' => 'Loved link']);
    Bookmark::factory()->for($this->user)->create(['is_favorite' => false, 'title' => 'Plain link']);

    $this->get(route('bookmarks.index', ['filter' => 'favorites']))
        ->assertOk()
        ->assertSee('Loved link')
        ->assertDontSee('Plain link');
});

test('search matches title, url and note', function () {
    Bookmark::factory()->for($this->user)->create(['title' => 'Image CDN', 'url' => 'https://cloudinary.com']);
    Bookmark::factory()->for($this->user)->create(['title' => 'Schema tool', 'url' => 'https://drawdb.app', 'note' => null]);

    Bookmark::factory()->for($this->user)->create(['title' => 'Notes app', 'url' => 'https://example.com', 'note' => 'Great for meeting notes']);

    $this->get(route('bookmarks.index', ['search' => 'cloudinary']))
        ->assertOk()
        ->assertSee('Image CDN')
        ->assertDontSee('Schema tool')
        ->assertSee('1 result');

    $this->get(route('bookmarks.index', ['search' => 'schema']))->assertSee('Schema tool')->assertDontSee('Image CDN');
    $this->get(route('bookmarks.index', ['search' => 'meeting']))->assertSee('Notes app')->assertDontSee('Schema tool');
});

test('search never returns another users bookmarks', function () {
    Bookmark::factory()->create(['title' => 'Cloudinary for someone else', 'url' => 'https://cloudinary.com']);

    $this->get(route('bookmarks.index', ['search' => 'cloudinary']))
        ->assertOk()
        ->assertDontSee('Cloudinary for someone else')
        ->assertSee('No bookmarks match your search');
});

test('collection filter lists only that collections bookmarks', function () {
    $design = Collection::factory()->for($this->user)->create(['title' => 'Design']);
    Bookmark::factory()->for($this->user)->for($design)->create(['title' => 'Dribbble']);
    Bookmark::factory()->for($this->user)->create(['title' => 'Laravel docs']);

    $this->get(route('bookmarks.index', ['collection' => $design->id]))
        ->assertOk()
        ->assertSee(['Design', 'Dribbble', route('collections.edit', $design)])
        ->assertDontSee('Laravel docs');
});

test('tag filter lists only bookmarks with that tag', function () {
    $tag = Tag::factory()->for($this->user)->create(['title' => 'inspiration']);
    Bookmark::factory()->for($this->user)->hasAttached($tag, [], 'tags')->create(['title' => 'Tagged link']);
    Bookmark::factory()->for($this->user)->create(['title' => 'Untagged link']);

    $this->get(route('bookmarks.index', ['tag' => $tag->id]))
        ->assertOk()
        ->assertSee(['#inspiration', 'Tagged link'])
        ->assertDontSee('Untagged link');
});

test('search can be combined with a sidebar filter', function () {
    $design = Collection::factory()->for($this->user)->create(['title' => 'Design']);
    Bookmark::factory()->for($this->user)->for($design)->create(['title' => 'Figma tips']);
    Bookmark::factory()->for($this->user)->for($design)->create(['title' => 'Color palettes']);
    Bookmark::factory()->for($this->user)->create(['title' => 'Figma API docs']);

    $this->get(route('bookmarks.index', ['collection' => $design->id, 'search' => 'figma']))
        ->assertOk()
        ->assertSee(['Figma tips', 'in Design', 'Search all bookmarks', 'Search in Design'])
        ->assertDontSee('Color palettes')
        ->assertDontSee('Figma API docs');
});

test('filtering by another users collection or tag returns not found', function () {
    $this->get(route('bookmarks.index', ['collection' => Collection::factory()->create()->id]))->assertNotFound();
    $this->get(route('bookmarks.index', ['tag' => Tag::factory()->create()->id]))->assertNotFound();
});

test('bookmarks can be sorted oldest first and shown as a grid', function () {
    Bookmark::factory()->for($this->user)->create(['title' => 'Older one', 'created_at' => now()->subDay()]);
    Bookmark::factory()->for($this->user)->create(['title' => 'Newer one']);

    $this->get(route('bookmarks.index'))->assertSeeInOrder(['Newer one', 'Older one']);
    $this->get(route('bookmarks.index', ['sort' => 'oldest', 'view' => 'grid']))
        ->assertOk()
        ->assertSeeInOrder(['Older one', 'Newer one']);
});

test('list shows the domain and the unsorted label', function () {
    Bookmark::factory()->for($this->user)->create(['collection_id' => null, 'url' => 'https://www.drawdb.app/editor']);

    $this->get(route('bookmarks.index'))
        ->assertOk()
        ->assertSee('drawdb.app')
        ->assertSee('Unsorted');
});

test('sidebar lists the user collections and tags', function () {
    $collection = Collection::factory()->for($this->user)->create(['title' => 'Design']);
    $tag = Tag::factory()->for($this->user)->create(['title' => 'inspiration']);

    $this->get(route('bookmarks.index'))
        ->assertOk()
        ->assertSee(['All bookmarks', 'Collections', 'Filters', $this->user->name])
        ->assertSee(route('bookmarks.index', ['collection' => $collection]))
        ->assertSee(route('bookmarks.index', ['tag' => $tag]));
});

test('export downloads only the user filtered bookmarks as csv', function () {
    $collection = Collection::factory()->for($this->user)->create(['title' => 'Design']);
    Bookmark::factory()->for($this->user)->for($collection)->create(['title' => 'In collection', 'url' => 'https://cloudinary.com']);
    Bookmark::factory()->for($this->user)->create(['title' => 'Elsewhere']);
    Bookmark::factory()->create(['title' => 'Other users link']);

    $response = $this->get(route('bookmarks.export', ['collection' => $collection->id]));

    $response->assertOk()->assertDownload();
    expect($response->streamedContent())
        ->toContain('title,url,note,collection,tags,favorite,created_at')
        ->toContain('"In collection",https://cloudinary.com')
        ->not->toContain('Elsewhere');

    expect($this->get(route('bookmarks.export'))->streamedContent())->not->toContain('Other users link');
});
