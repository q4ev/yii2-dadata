<?php

namespace q4ev\daData;


use yii\base\InvalidConfigException;

class DaData extends \yii\base\BaseObject
{
	protected ?string $_token = null;

	public function suggestParty ($query, $params = null)
	{
		$data = [
			'count' => $params['count'] ?: 10,
			'params' => [ 'status' => [ 'ACTIVE' ] ],
			'query' => $query,
		];

		$context = \stream_context_create([
			'http' => [
				'timeout' => 1,
				'method'  => 'POST',
				'header'  => [
					'Content-type: application/json',
					'Accept: application/json',
					'Authorization: Token ' . $this->_token,
				],
				'content' => \json_encode($data, JSON_UNESCAPED_UNICODE),
			],
		]);

		$result = \file_get_contents(
			'https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party',
			false,
			$context
		);

		$response = \json_decode($result, true);

		if ($params['raw'])
			return $response;

		return $response['suggestions'];
	}

	public function setToken (string $token)
	{
		$this->_token = $token;

		if (!preg_match('/^[a-f\d]{40}$/', $this->_token))
			throw new InvalidConfigException('Proper token for DaData requests needed');
	}
}