<?php

require_once __DIR__ . '/MonthlyPostsStats.php';
require_once __DIR__ . '/Utils.php';

class PostsStats {
	private $postsByMonthStats = [];
	private $postsPerWeek = [];
	private $postsPerUser = [];

	public function exportStats() {
		$byMonth = [];
		ksort($this->postsByMonthStats);
		foreach($this->postsByMonthStats as $key => $stats) {
			$byMonth[$key] = $stats->exportStats();
		}

		ksort($this->postsPerWeek);

		// Add the average post rate to each user's data
		$nMonths = count($this->postsByMonthStats);
		foreach($this->postsPerUser as $userId => &$userData) {
			$userData['avg'] = number_format(Utils::arrayValueOrException($userData, 'total') / $nMonths, 2);
		}

		return [
			'by_month' => $byMonth,
			'by_week' => $this->postsPerWeek,
			'by_user' => $this->postsPerUser,
		];
	}

	public function processPosts($posts) {
		foreach ($posts as $post) {
			$this->processPost($post);
		}
	}

	private function processPost($post) {
		$dateTime = new DateTime(Utils::arrayValueOrException($post, 'created_time'));

		$this->incPostsCountPerWeek($post, $dateTime);
		$this->incPostsCountPerUser($post, $dateTime);

		$this->getMonthlyPostsStats($dateTime)->processPost($post, $dateTime);
	}

	private function getMonthlyPostsStats($dateTime) {
		$monthKey = Utils::getMonthKey($dateTime);

		if (!isset($this->postsByMonthStats[$monthKey])) {
			$this->postsByMonthStats[$monthKey] = new MonthlyPostsStats();
		}

		return $this->postsByMonthStats[$monthKey];
	}

	private function incPostsCountPerWeek($post, $dateTime) {
		$weekKey = Utils::getWeekKey($dateTime);

		if (!isset($this->postsPerWeek[$weekKey])) {
			$this->postsPerWeek[$weekKey] = 0;
		}

		$this->postsPerWeek[$weekKey]++;
	}

	private function incPostsCountPerUser($post, $dateTime) {
		$userId = Utils::arrayValueOrException($post, 'from_id');

		if (!isset($this->postsPerUser[$userId])) {
			$userName = Utils::arrayValueOrException($post, 'from_name');
			$this->postsPerUser[$userId] = [
				'total' => 0,
				'id' => $userId,
				'name' => $userName,
			];
		}

		$this->postsPerUser[$userId]['total']++;
	}
}
