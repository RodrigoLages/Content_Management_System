<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tag;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Responses\PostResponse;

class PostController extends Controller
{

    protected $postResponse;

    public function __construct(PostResponse $postResponse)
    {
        $this->postResponse = $postResponse;
    }

    public function index(Request $request)
    {
        if ($request->has('tag')) {
            $tagName = $request->input('tag');
            $tag = Tag::where('name', strtolower($tagName))->first();
            if (!$tag) return response()->json([], 200);

            $posts = $tag->posts()->get()->load('tags');
        } else {
            $posts = Post::all()->load('tags');
        }
        
        $transformedPosts = $posts->map(function ($post) {
            $postAttributes = $post->getAttributes();
            $postAttributes['tags'] = $post->tags->pluck('name')->toArray();
            return $postAttributes;
        });

        return $this->postResponse->success($transformedPosts, 'Posts retrieved successfully');
    }

    public function store(CreatePostRequest  $request)
    {    
        $post = Post::create($request->all());

        $tagIds = [];
        foreach ($request->input('tags') as $tagName) {
            $tag = Tag::firstOrCreate(['name' => strtolower($tagName)]);
            $tagIds[] = $tag->id;
        }

        $post->tags()->attach($tagIds);

        $post->load('tags');
        $postAttributes = $post->getAttributes();
        $postAttributes['tags'] = $post->tags->pluck('name')->toArray();

        return $this->postResponse->success($postAttributes, 'Post created successfully', 201);
    }

    public function show(string $id)
    {
        $post = Post::find($id);
        if (!$post) return $this->postResponse->notFound();
        $post->load('tags');
        

        $postAttributes = $post->getAttributes();
        $postAttributes['tags'] = $post->tags->pluck('name')->toArray();

        return $this->postResponse->success($postAttributes);
    }

    public function update(UpdatePostRequest $request, string $id)
    {
        $post = Post::find($id);
        if (!$post) return $this->postResponse->notFound();
        $post->update($request->all());
        $post->load('tags');

        $tagIds = [];
        foreach ($request->input('tags') as $tagName) {
            $tag = Tag::firstOrCreate(['name' => strtolower($tagName)]);
            $tagIds[] = $tag->id;
        }

        $post->tags()->sync($tagIds);

        $post->load('tags');
        $postAttributes = $post->getAttributes();
        $postAttributes['tags'] = $post->tags->pluck('name')->toArray();

        return $this->postResponse->success($postAttributes, 'Post updated successfully');
    }

    public function destroy(string $id)
    {
        $post = Post::find($id);
        if (!$post) return $this->postResponse->notFound();
        $post->delete();
        return $this->postResponse->success(null, 'Post deleted successfully');
    }
}
