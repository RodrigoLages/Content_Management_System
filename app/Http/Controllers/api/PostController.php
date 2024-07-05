<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Tag;

use App\Http\Requests\CreatePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Responses\PostResponse;


/**
 * @OA\Info(title="API de Posts", version="1.0")
 */
class PostController extends Controller
{

    protected $postResponse;

    public function __construct(PostResponse $postResponse)
    {
        $this->postResponse = $postResponse;
    }

    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="Lista todos os posts ou filtra todos por tag",
     *     @OA\Parameter(
     *         name="tag",
     *         in="query",
     *         description="Nome da tag para filtrar posts",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Response(response="200", description="Sucesso")
     * )
     */
    public function index(Request $request)
    {
        if ($request->has('tag')) {
            $tagName = $request->input('tag');
            $tag = Tag::where('name', strtolower($tagName))->first();
            if (!$tag) return response()->json([], 200);

            $posts = $tag->posts()->get();
        } else {
            $posts = Post::all();
        }
        
        $posts->load('tags');
        $transformedPosts = $posts->map(function ($post) {
            $postAttributes = $post->getAttributes();
            $postAttributes['tags'] = $post->tags->pluck('name')->toArray();
            return $postAttributes;
        });

        return $this->postResponse->success($transformedPosts);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Cria um novo post",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","author","content","tags"},
     *             @OA\Property(property="title", type="string", example="Título do Post"),
     *             @OA\Property(property="author", type="string", example="Autor do Post"),
     *             @OA\Property(property="content", type="string", example="Conteúdo do Post"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"tags", "do", "post"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="201", description="Post criado com sucesso"),
     *     @OA\Response(response="422", description="Erro de validação")
     * )
     */
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

        return $this->postResponse->success($postAttributes, 201);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{id}",
     *     summary="Exibe um post específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="200", description="Sucesso"),
     *     @OA\Response(response="404", description="Post não encontrado")
     * )
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        if (!$post) return $this->postResponse->notFound();
        $post->load('tags');
        

        $postAttributes = $post->getAttributes();
        $postAttributes['tags'] = $post->tags->pluck('name')->toArray();

        return $this->postResponse->success($postAttributes);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Atualiza um post específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","author","content","tags"},
     *             @OA\Property(property="title", type="string", example="Título do Post Atualizado"),
     *             @OA\Property(property="author", type="string", example="Autor do Post Atualizado"),
     *             @OA\Property(property="content", type="string", example="Conteúdo do Post Atualizado"),
     *             @OA\Property(
     *                 property="tags",
     *                 type="array",
     *                 @OA\Items(type="string"),
     *                 example={"tags", "do", "post", "atualizadas"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response="200", description="Post atualizado com sucesso"),
     *     @OA\Response(response="404", description="Post não encontrado"),
     *     @OA\Response(response="422", description="Erro de validação")
     * )
     */
    public function update(UpdatePostRequest $request, string $id)
    {
        $post = Post::find($id);
        if (!$post) return $this->postResponse->notFound();
        $post->update($request->all());

        if ($request->input('tags')) {
            $tagIds = [];
            foreach ($request->input('tags') as $tagName) {
                $tag = Tag::firstOrCreate(['name' => strtolower($tagName)]);
                $tagIds[] = $tag->id;
            }
            
            $post->tags()->sync($tagIds);
        }

        $post->load('tags');
        $postAttributes = $post->getAttributes();
        $postAttributes['tags'] = $post->tags->pluck('name')->toArray();

        return $this->postResponse->success($postAttributes);
    }

     /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Deleta um post específico",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response="204", description="Post deletado com sucesso"),
     *     @OA\Response(response="404", description="Post não encontrado")
     * )
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if (!$post) return $this->postResponse->notFound();
        $post->delete();
        return $this->postResponse->success(null, 204);
    }
}
