<?php

require_once __DIR__ . '/Utils.php';

class MonthlyPostsStats {
	private $year;
	private $month;
	private $numPosts = 0;
	private $avgPostLen = 0;
	private $longestPostId = 'n/a';
	private $longestPostLen = 0;
	private $postsPerUser = [];

	public function exportStats() {
		return [
			'year' => $this->year,
			'month' => $this->month,
			'num_posts' => $this->numPosts,
			'avg_post_len' => number_format($this->avgPostLen, 2),
			'longest_post_id' => $this->longestPostId,
			'longest_post_len' => $this->longestPostLen,
			// 'per_user' => $this->postsPerUser, // Uncomment in order to get per-user posts count
		];
	}

	public function processPost($post, $dateTime) {
		$this->numPosts++;

		$this->year = $dateTime->format('Y');
		$this->month = $dateTime->format('m');

		$postLen = strlen(Utils::arrayValueOrException($post, 'message'));

		// Add post length to average calculation
		$this->avgPostLen = $this->avgPostLen + ($postLen - $this->avgPostLen) / $this->numPosts;

		// Update longest post (if accplicable)
		if ($postLen > $this->longestPostLen) {
			$this->longestPostLen = $postLen;
			$this->longestPostId = Utils::arrayValueOrException($post, 'id');
		}

		$this->incUserPostsCount($post);
	}

	private function incUserPostsCount($post) {
		$user = Utils::arrayValueOrException($post, 'from_id');

		if (!isset($this->postsPerUser[$user])) {
			$this->postsPerUser[$user] = 0;
		}

		$this->postsPerUser[$user]++;
	}
}
