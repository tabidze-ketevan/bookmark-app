<?php

use App\Models\Bookmark;
use App\Models\Collection;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('index, create and edit pages render and show redirects to the filtered list', function () {
    $collection = Collection::factory()->for($this->user)->create();
    $bookmark = Bookmark::factory()->for($this->user)->for($collection)->create();

    $this->get(route('collections.index'))->assertOk()->assertSee($collection->title);
    $this->get(route('collections.create'))->assertOk();
    $this->get(route('collections.show', $collection))->assertRedirect(route('bookmarks.index', ['collection' => $collection]));
    $this->get(route('collections.edit', $collection))->assertOk()->assertSee($collection->title);
});

test('index only lists the user collections', function () {
    Collection::factory()->create(['title' => 'Foreign collection']);

    $this->get(route('collections.index'))->assertOk()->assertDontSee('Foreign collection');
});

test('store creates a collection owned by the user', function () {
    $this->post(route('collections.store'), ['title' => 'Work'])
        ->assertRedirect(route('bookmarks.index', ['collection' => Collection::sole()]));

    expect(Collection::sole())
        ->title->toBe('Work')
        ->user_id->toBe($this->user->id);
});

test('store requires a title that is unique for the user', function () {
    Collection::factory()->for($this->user)->create(['title' => 'Work']);

    $this->post(route('collections.store'), ['title' => ''])->assertSessionHasErrors('title');
    $this->post(route('collections.store'), ['title' => 'Work'])->assertSessionHasErrors('title');
});

test('another user may use the same collection title', function () {
    Collection::factory()->create(['title' => 'Work']);

    $this->post(route('collections.store'), ['title' => 'Work'])->assertSessionHasNoErrors();
});

test('update renames a collection and allows keeping the same title', function () {
    $collection = Collection::factory()->for($this->user)->create(['title' => 'Work']);

    $this->put(route('collections.update', $collection), ['title' => 'Work'])->assertSessionHasNoErrors();
    $this->put(route('collections.update', $collection), ['title' => 'Personal'])
        ->assertRedirect(route('bookmarks.index', ['collection' => $collection]));

    expect($collection->refresh()->title)->toBe('Personal');
});

test('destroy deletes the collection and keeps its bookmarks uncategorized', function () {
    $collection = Collection::factory()->for($this->user)->create();
    $bookmark = Bookmark::factory()->for($this->user)->for($collection)->create();

    $this->delete(route('collections.destroy', $collection))->assertRedirect(route('collections.index'));

    $this->assertModelMissing($collection);
    expect($bookmark->refresh()->collection_id)->toBeNull();
});

test('users cannot view, edit, update or delete another users collection', function () {
    $collection = Collection::factory()->create(['title' => 'Not mine']);

    $this->get(route('collections.show', $collection))->assertForbidden();
    $this->get(route('collections.edit', $collection))->assertForbidden();
    $this->put(route('collections.update', $collection), ['title' => 'Hijacked'])->assertForbidden();
    $this->delete(route('collections.destroy', $collection))->assertForbidden();

    expect($collection->refresh()->title)->toBe('Not mine');
});
