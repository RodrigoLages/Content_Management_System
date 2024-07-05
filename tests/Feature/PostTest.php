<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Post;
use App\Models\Tag;

class PostTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa a criação de um post com tags.
     *
     * @return void
     */
    public function test_store_success()
    {
        $data = [
            'title' => 'Novo Post',
            'author' => 'Autor',
            'content' => 'Conteúdo do post',
            'tags' => ['tag1', 'tag2'],
        ];

        $response = $this->postJson('/api/posts', $data);

        $response->assertStatus(201)
                 ->assertJson([
                     'title' => 'Novo Post',
                     'author' => 'Autor',
                     'content' => 'Conteúdo do post',
                     'tags' => ['tag1', 'tag2'],
                 ]);

        $post = Post::where('title', 'Novo Post')->first();
        $this->assertNotNull($post);

        $this->assertCount(2, $post->tags);
        $this->assertEquals(['tag1', 'tag2'], $post->tags->pluck('name')->toArray());
    }

     /**
     * Testa a validação na criação de um post.
     *
     * @return void
     */
    public function test_store_validation()
    {
        $invalidData = [
            'title' => '',        
            'author' => '',       
            'content' => '',      
            'tags' => [] 
        ];

        $response = $this->postJson('/api/posts', $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'author', 'content', 'tags']);
    }

    
     /**
     * Testa a listagem de todos os posts.
     *
     * @return void
     */
    public function test_index_returns_all_posts()
    {
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();

        $tag1 = Tag::factory()->create(['name' => 'tag1']);
        $tag2 = Tag::factory()->create(['name' => 'tag2']);

        $post1->tags()->attach($tag1->id);
        $post2->tags()->attach($tag2->id);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
                 ->assertJsonCount(2)
                 ->assertJsonFragment(['tags' => ['tag1']])
                 ->assertJsonFragment(['tags' => ['tag2']]);
    }

    /**
     * Testa a listagem de posts filtrados por tag.
     *
     * @return void
     */
    public function test_index_returns_filtered_posts()
    {
        $post1 = Post::factory()->create();
        $post2 = Post::factory()->create();

        $tag1 = Tag::factory()->create(['name' => 'tag1']);
        $tag2 = Tag::factory()->create(['name' => 'tag2']);

        $post1->tags()->attach($tag1->id);
        $post2->tags()->attach($tag2->id);

        $response = $this->getJson('/api/posts?tag=tag1');

        $response->assertStatus(200)
                 ->assertJsonCount(1)
                 ->assertJsonFragment(['tags' => ['tag1']]);

        $response = $this->getJson('/api/posts?tag=tag2');

        $response->assertStatus(200)
                 ->assertJsonCount(1)
                 ->assertJsonFragment(['tags' => ['tag2']]);
    }


    /**
     * Testa a listagem de um post específico.
     *
     * @return void
     */
    public function test_show_success()
    {
        $post = Post::factory()->create();
    
        $tag1 = Tag::factory()->create(['name' => 'tag1']);
        $tag2 = Tag::factory()->create(['name' => 'tag2']);
    
        $post->tags()->attach([$tag1->id, $tag2->id]);
    
        $response = $this->getJson("/api/posts/{$post->id}");
    
        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => $post->title])
                 ->assertJsonFragment(['tags' => ['tag1', 'tag2']]);
    }
    

    /**
     * Testa a busca de um post inexistente.
     *
     * @return void
     */
    public function test_show_not_found()
    {
        $response = $this->getJson('/api/posts/106');
    
        $response->assertStatus(404)
                 ->assertJsonFragment(['message' => 'Post not found'])
                 ->assertJsonFragment(['data' => null]);
    }


    /**
     * Testa a atualização de um post.
     *
     * @return void
     */
    public function test_update_success()
    {
        $post = Post::factory()->create();

        $newData = [
            'title' => 'Novo Título',
            'author' => 'Novo Autor',
            'content' => 'Novo Conteúdo',
            'tags' => ['novatag1', 'novatag2']
        ];

        $response = $this->putJson("/api/posts/{$post->id}", $newData);

        $response->assertStatus(200)
                ->assertJsonFragment(['title' => $newData['title']])
                ->assertJsonFragment(['author' => $newData['author']])
                ->assertJsonFragment(['content' => $newData['content']])
                ->assertJsonFragment(['tags' => $newData['tags']]);
    }


    /**
     * Testa a atualização com campos opicionais.
     *
     * @return void
     */
    public function test_update_with_optional_fields()
    {
        $post = Post::factory()->create();
        $updateData = [
            'title' => 'Novo Título',
        ];
    
        $response = $this->putJson("/api/posts/{$post->id}", $updateData);
    
        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => $updateData['title']]);
    }


    /**
     * Testa a validação na atualização de um post.
     *
     * @return void
     */
    public function test_update_validation()
    {
        $post = Post::factory()->create();

        $invalidData = [
            'title' => '',        
            'author' => '',       
            'content' => '',      
        ];

        $response = $this->putJson("/api/posts/{$post->id}", $invalidData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'author', 'content']);
    }


    /**
     * Testa a atualização de um post inexistente.
     *
     * @return void
     */
    public function test_update_not_found()
    {
        $response = $this->putJson('/api/posts/106', []);

        $response->assertStatus(404)
                 ->assertJsonFragment(['message' => 'Post not found'])
                 ->assertJsonFragment(['data' => null]);
    }


    /**
     * Testa a deleção de um post.
     *
     * @return void
     */
    public function test_destroy_success()
    {
        $post = Post::factory()->create();
        $response = $this->deleteJson("/api/posts/{$post->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }


    /**
     * Testa a deleção de um post inexistente.
     *
     * @return void
     */
    public function test_destroy_not_found()
    {
        $response = $this->deleteJson('/api/posts/106');
        $response->assertStatus(404);
    }
}
