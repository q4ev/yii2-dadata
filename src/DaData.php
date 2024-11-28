<?php

namespace q4ev\daData;


use yii\base\InvalidConfigException;

class DaData extends \yii\base\BaseObject
{
	protected ?string $_token = null;

	protected function __getContext ($data)
	{
		return \stream_context_create([
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
	}

	protected function __sendRequest ($url, $query, $params)
	{
		$data = [
			'count' => $params['count'] ?: 10,
			'params' => ['status' => ['ACTIVE']],
			'query' => $query,
		];

		$result = \file_get_contents(
			$url,
			false,
			$this->__getContext($data)
		);

		$response = \json_decode($result, true);

		if ($params['raw'])
			return $response;

		return $response['suggestions'];
	}

	public function findByIdParty ($query, $params = null)
	{
		return $this->__sendRequest('https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party', $query, $params);
	}

	public function suggestParty ($query, $params = null)
	{
		return $this->__sendRequest('https://suggestions.dadata.ru/suggestions/api/4_1/rs/suggest/party', $query, $params);
	}

	public function setToken (string $token)
	{
		$this->_token = $token;

		if (!preg_match('/^[a-f\d]{40}$/', $this->_token))
			throw new InvalidConfigException('Proper token for DaData requests needed');
	}
}