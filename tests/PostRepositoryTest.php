<?php

require_once __DIR__ . '/../src/Repositories/PostRepository.php';
require_once __DIR__ . '/../src/Models/Post.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;

use src\Repositories\PostRepository;

class PostRepositoryTest extends TestCase
{
	private PostRepository $postRepository;

	public function __construct(?string $name = null, array $data = [], $dataName = '')
	{
		parent::__construct($name, $data, $dataName);
	}

	/**
	 * Runs before each test
	 */
	protected function setUp(): void
	{
		parent::setUp();
		$this->postRepository = new PostRepository();
	}

	/**
	 * Runs after each test
	 */
	protected function tearDown(): void
	{
		parent::tearDown();

		// TODO: Read the username and host from the .env file

		$dsn = "mysql:host=localhost;";
		$options = [
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
			PDO::ATTR_EMULATE_PREPARES   => false,
		];
		try {
			$pdo = new PDO($dsn, 'root', '', $options);
		} catch (PDOException $e) {
			throw new PDOException($e->getMessage(), (int)$e->getCode());
		}
		$commands = file_get_contents(__DIR__ . '/../database/test_schema.sql', TRUE);
		$pdo->exec($commands);
	}

	public function testPostCreation()
	{
		$postRepository = new PostRepository;
		$post = $postRepository->savePost('test', 'body');
		$postId = $post->id;

		$this->assertEquals('test', $post->title);
		$this->assertEquals('body', $post->body);

		$deletedPost = $postRepository->getPostById($postId);
	}

	public function testPostRetrieval()
	{
        $postRepository = new PostRepository;
        $post = $postRepository->savePost('retrieval-test', 'retrieval-body');
        $postId = $post->id;

        $retrievedPost = $postRepository->getPostById($postId);

        $this->assertEquals('retrieval-test', $retrievedPost->title);
        $this->assertEquals('retrieval-body', $retrievedPost->body);

		$deletedPost = $postRepository->getPostById($postId);
	}

	public function testPostUpdate()
	{
		$postRepository = new PostRepository;
        $post = $postRepository->savePost('update-test', 'update-body');
        $postId = $post->id;

        $postRepository->updatePost($postId, 'updated-test', 'updated-body');

        $updatedPost = $postRepository->getPostById($postId);

        $this->assertEquals('updated-test', $updatedPost->title);
        $this->assertEquals('updated-body', $updatedPost->body);

		$deletedPost = $postRepository->getPostById($postId);
	}

	public function testPostDeletion()
	{
		$postRepository = new PostRepository;
        $post = $postRepository->savePost('delete-test', 'delete-body');
        $postId = $post->id;

        $postRepository->deletePostById($postId);

        $deletedPost = $postRepository->getPostById($postId);

        $this->assertEquals(false, $deletedPost);
	}
}
