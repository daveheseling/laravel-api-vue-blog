<?php

namespace App\Services;

use App\Models\Movie;
use App\Models\Post;
use App\Models\Tag;
use Carbon\Carbon;

class PostService
{
    public function createPost($data, $userId)
    {
        $slug = array_get($data, 'slug');
        $title =  array_get($data, 'title');
        $published = array_get($data, 'published', false);
        $movieId = array_get($data, 'movieId');
        $categoryIds = array_get($data, 'categoryIds', []);
        $tagNames = array_get($data, 'tags', []);
        $movie = Movie::find($movieId);
        $post = Post::create([
            'user_id' => $userId,
            'title' => $title,
            'body' => array_get($data, 'body'),
            'image' => array_get($data, 'image'),
            'meta_title' => array_get($data, 'metaTitle'),
            'meta_description' => array_get($data, 'metaDescription'),
            'summary' => array_get($data, 'summary'),
            'slug' => $slug ?? str_slug($title),
            'published' => $published,
            'published_at' => $published ? Carbon::now() : null
        ]);

        if ($movie) {
            $post->movie()->associate($movie);
            $post->save();
        }

        $post->categories()->sync(array_wrap($categoryIds));

        if (!empty(array_wrap($tagNames))) {
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                $tag = Tag::firstOrCreate(['name' => ucwords(strtolower($tagName))]);
                array_push($tagIds, $tag->id);
            }
            $post->tags()->sync($tagIds);
        }

        return $post;
    }

    public function updatePost($data, $userId, Post $post)
    {
        $slug = array_get($data, 'slug');
        $title =  array_get($data, 'title');
        $published = array_get($data, 'published', false);
        $movieId = array_get($data, 'movieId');
        $categoryIds = array_get($data, 'categoryIds', []);
        $tagNames = array_get($data, 'tags', []);
        if (optional($post->movie)->id != $movieId) {
            $movie = Movie::find($movieId);
        } else {
            $movie = null;
        }

        $post->update([
            'user_id' => $userId,
            'title' => $title,
            'body' => array_get($data, 'body'),
            'image' => array_get($data, 'image'),
            'meta_title' => array_get($data, 'metaTitle'),
            'meta_description' => array_get($data, 'metaDescription'),
            'summary' => array_get($data, 'summary'),
            'slug' => $slug ?? str_slug($title),
            'published' => $published,
            'published_at' => $published ? Carbon::now() : null
        ]);

        if ($movie) {
            $post->movie()->dissociate();
            $post->movie()->associate($movie);
            $post->save();
        }

        $post->categories()->sync(array_wrap($categoryIds));

        if (!empty(array_wrap($tagNames))) {
            $tagIds = [];
            foreach ($tagNames as $tagName) {
                $tag = Tag::firstOrCreate(['name' => ucwords(strtolower($tagName))]);
                array_push($tagIds, $tag->id);
            }
            $post->tags()->sync($tagIds);
        }

        return $post;
    }
}