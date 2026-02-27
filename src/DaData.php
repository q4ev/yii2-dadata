<?php

namespace q4ev\daData;


use yii\base\InvalidConfigException;

/**
 * @property-write string|null $token
 */
class DaData extends \yii\base\BaseObject
{
	protected ?string $_token = null;

	public function findByIdParty ($query, $params = null)
	{
		$postBody = [
			'count' => $params['count'] ?: 10,
			'params' => ['status' => ['ACTIVE']],
			'query' => $query,
		];

		return $this->sendRequest($this->getUrl('findById/party'), $postBody, $params['raw'] ?? false);
	}

	public function suggestAddress (array $postBody, $params = null)
	{
		return $this->sendRequest($this->getUrl('suggest/address'), $postBody, $params['raw'] ?? false);
	}

	public function suggestFmsUnit ($query, $params = null)
	{
		$postBody = [
			'query' => $query,
		];

		return $this->sendRequest($this->getUrl('suggest/fms_unit'), $postBody, $params['raw'] ?? false);
	}

	public function suggestParty ($query, $params = null)
	{
		$postBody = [
			'count' => $params['count'] ?: 10,
			'params' => ['status' => ['ACTIVE']],
			'query' => $query,
		];

		return $this->sendRequest($this->getUrl('suggest/party'), $postBody, $params['raw'] ?? false);
	}

	public function setToken (string $token)
	{
		$this->_token = $token;

		if (!preg_match('/^[a-f\d]{40}$/', $this->_token)) {
			throw new InvalidConfigException('Proper token for DaData requests needed');
		}
	}

	protected function getContext ($data)
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

	protected function getUrl (string $endingPart)
	{
		return 'https://suggestions.dadata.ru/suggestions/api/4_1/rs/'.trim($endingPart, '/');
	}

	protected function sendRequest (string $url, array $postBody, bool $returnRaw = false)
	{
		$result = \file_get_contents(
			$url,
			false,
			$this->getContext($postBody)
		);

		$response = \json_decode($result, true);

		if ($returnRaw) {
			return $response;
		}

		return $response['suggestions'] ?? null;
	}
}
