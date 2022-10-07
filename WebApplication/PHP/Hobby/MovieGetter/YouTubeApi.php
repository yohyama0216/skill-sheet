<?php

class YouTubeApi
{
    private const BASE_URL = 'https://www.googleapis.com/youtube/v3/playlistItems';
	private const PLAYLIST_ID = 'PL9fO9qH93LFijVKbqaDBWPjlRVLUOSb1a';
    private $params = [
		'key' => DoNotUpload::API_KEY,
		'part' => 'id,contentDetails',
		'maxResults' => 50,
		'playlistId' => self::PLAYLIST_ID,
		'pageToken' => ''
	];

    public function __construct()
    {

    }

	public function sendRepeat()
	{
		$fileNumber = 1;
		$result = [];
		do {
			$result = $this->sendRequest();
			$fileName = $this->createFileName($fileNumber);
			$this->generateFile($fileName,$result);
			$this->updateParam($result);
			$fileNumber++;
			echo $result['nextPageToken'].PHP_EOL;
		}
		while (!empty($this->params['pageToken']));
	}

	private function sendRequest()
	{
		$apiUrl = $this->createApiUrl();
		$response = $this->requestApi($apiUrl);
		return json_decode($response,true);
	}

	private function requestApi($apiUrl)
	{
        $ch = curl_init(); // はじめ
        curl_setopt($ch, CURLOPT_URL, $apiUrl); 
        //curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17');
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        return curl_exec($ch);
	}

	private function createFileName($fileNumber)
	{
		return 'resultJson'.$fileNumber.'.json';
	}

	private function generateFile($fileName,$result)
	{
		return file_put_contents($fileName,json_encode($result));
	}

	private function updateParam($data)
	{
		if (!array_key_exists('nextPageToken',$data)) {
			echo '終了'.PHP_EOL;
			exit();
		}
		$this->params['pageToken'] = $data['nextPageToken'];
	}

	private function createApiUrl()
	{
		return self::BASE_URL."?".http_build_query($this->params);
	}
}
