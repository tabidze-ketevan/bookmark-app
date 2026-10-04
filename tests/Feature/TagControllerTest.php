<?php

use App\Models\Bookmark;
use App\Models\Tag;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('index, create and edit pages render and show redirects to the filtered list', function () {
    $tag = Tag::factory()->for($this->user)->create();
    $bookmark = Bookmark::factory()->for($this->user)->hasAttached($tag, [], 'tags')->create();

    $this->get(route('tags.index'))->assertOk()->assertSee($tag->title);
    $this->get(route('tags.create'))->assertOk();
    $this->get(route('tags.show', $tag))->assertRedirect(route('bookmarks.index', ['tag' => $tag]));
    $this->get(route('tags.edit', $tag))->assertOk()->assertSee($tag->title);
});

test('index only lists the user tags', function () {
    Tag::factory()->create(['title' => 'foreign-tag']);

    $this->get(route('tags.index'))->assertOk()->assertDontSee('foreign-tag');
});

test('store creates a tag owned by the user', function () {
    $this->post(route('tags.store'), ['title' => 'php'])
        ->assertRedirect(route('bookmarks.index', ['tag' => Tag::sole()]));

    expect(Tag::sole())
        ->title->toBe('php')
        ->user_id->toBe($this->user->id);
});

test('store requires a title that is unique for the user', function () {
    Tag::factory()->for($this->user)->create(['title' => 'php']);

    $this->post(route('tags.store'), ['title' => ''])->assertSessionHasErrors('title');
    $this->post(route('tags.store'), ['title' => 'php'])->assertSessionHasErrors('title');
});

test('another user may use the same tag title', function () {
    Tag::factory()->create(['title' => 'php']);

    $this->post(route('tags.store'), ['title' => 'php'])->assertSessionHasNoErrors();
});

test('update renames a tag', function () {
    $tag = Tag::factory()->for($this->user)->create();

    $this->put(route('tags.update', $tag), ['title' => 'laravel'])->assertRedirect(route('bookmarks.index', ['tag' => $tag]));

    expect($tag->refresh()->title)->toBe('laravel');
});

test('destroy deletes the tag and detaches it from bookmarks', function () {
    $tag = Tag::factory()->for($this->user)->create();
    $bookmark = Bookmark::factory()->for($this->user)->hasAttached($tag, [], 'tags')->create();

    $this->delete(route('tags.destroy', $tag))->assertRedirect(route('tags.index'));

    $this->assertModelMissing($tag);
    $this->assertModelExists($bookmark);
    $this->assertDatabaseCount('bookmark_tag', 0);
});

test('users cannot view, edit, update or delete another users tag', function () {
    $tag = Tag::factory()->create(['title' => 'not-mine']);

    $this->get(route('tags.show', $tag))->assertForbidden();
    $this->get(route('tags.edit', $tag))->assertForbidden();
    $this->put(route('tags.update', $tag), ['title' => 'hijacked'])->assertForbidden();
    $this->delete(route('tags.destroy', $tag))->assertForbidden();

    expect($tag->refresh()->title)->toBe('not-mine');
});

test('a tag keeps its pill color when renamed', function () {
    $tag = Tag::factory()->for($this->user)->create(['title' => 'before']);
    $colorBeforeRename = $tag->color();

    $tag->update(['title' => 'after']);

    expect($tag->color())->toBe($colorBeforeRename)->toBeIn(Tag::COLORS);
});

test('bookmark rows render tags as colored pills', function () {
    $tag = Tag::factory()->for($this->user)->create(['title' => 'design']);
    Bookmark::factory()->for($this->user)->hasAttached($tag, [], 'tags')->create();

    $this->get(route('bookmarks.index'))
        ->assertOk()
        ->assertSee('#design')
        ->assertSee('rounded-full', false)
        ->assertSee('bg-'.$tag->color().'-500/10', false);
});
