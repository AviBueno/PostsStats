<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Utils.php';

use GuzzleHttp\Client;

class PostsFetcher {
	private $tokenUrl;
	private $postsUrl;
	private $tokenRequestParams;
	private $httpClient;
	private $token;

	public function __construct($clientId, $email, $name, $tokenUrl, $postsUrl) {
		if (!$clientId || !$email || !$name || !$tokenUrl || !$postsUrl) {
			throw new Exception("Params may not be empty");
		}

		$this->tokenUrl = $tokenUrl;
		$this->postsUrl = $postsUrl;

		$this->tokenRequestParams = [
			'client_id' => $clientId,
			'email' => $email,
			'name' => $name,
		];
	}

	public function fetchPosts($pageNum) {
		$response = $this->getHttpClient()->request('GET', $this->postsUrl, [
			'query' => [
				'sl_token' => $this->getToken(),
				'page' => $pageNum,
			]
		]);

		if ($response->getStatusCode() !== 200) {
			throw new Exception("Error {$response->getStatusCode()} getting posts data: {$response->getReasonPhrase()}");
		}

		$result = json_decode($response->getBody(), true);
		$data = Utils::arrayValueOrException($result, 'data');
		$posts = Utils::arrayValueOrException($data, 'posts');
		return $posts;
	}

	private function getHttpClient() {
		if (!$this->httpClient) {
			$this->httpClient = new Client();
		}

		return $this->httpClient;
	}

	private function getToken() {
		if (!$this->token) {
			$response = $this->getHttpClient()->request('POST', $this->tokenUrl, [
				'form_params' => $this->tokenRequestParams,
			]);

			if ($response->getStatusCode() === 200) {
				$result = json_decode($response->getBody(), true);

				if (isset($result['data']) && isset($result['data']['sl_token'])) {
					$this->token = $result['data']['sl_token'];
				} else {
					throw new Exception("Couldn't extract sl_token from response data");
				}
			} else {
				throw new Exception("Error {$response->getStatusCode()} getting token response: {$response->getReasonPhrase()}");
			}
		}

		return $this->token;
	}
}
