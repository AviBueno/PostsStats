<?php

require_once __DIR__ . '/PostsStats.php';
require_once __DIR__ . '/PostsFetcher.php';

function main() {
	$clientId = getenv('CLIENT_ID');
	$email = getenv('EMAIL');
	$name = getenv('NAME');
	$tokenUrl = getenv('TOKEN_URL');
	$postsUrl = getenv('POSTS_URL');

	$postsStats = new PostsStats();
	$pf = new PostsFetcher($clientId, $email, $name, $tokenUrl, $postsUrl);

	for ($pageNum = 1; $pageNum <= 10; $pageNum++) {
		$posts = $pf->fetchPosts($pageNum);
		$postsStats->processPosts($posts);
	}

	$data = $postsStats->exportStats();
	echo json_encode($data, JSON_PRETTY_PRINT);
}

main();
