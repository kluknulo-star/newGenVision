<?php

class Translation
{
    const TRANSLATE_YA_URL = 'https://translate.yandex.net/api/v1.5/tr.json/translate';

    /**
     * Check and get API key for translator
     * @return string
     */
    protected static function getKey(): string
    {
        $key = getenv('KEY');
        if (empty($key)) {
            throw new InvalidConfigException("Key is required in .env file");
        }
        return $key;
    }


    /**
     * @param $format string format need to translate
     * @return string
     * @throws Exception
     */
    public static function translateText(string $format = 'text'): string
    {
        $values = [
            'key' => self::getKey(),
            'text' => htmlspecialchars($_GET['text']),
            'lang' => htmlspecialchars($_GET['lang']),
            'format' => $format === 'text' ? 'plain' : $format,
        ];

        $urlEncoded = http_build_query($values);

        $ch = curl_init(self::TRANSLATE_YA_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlEncoded);

        $responseJson = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($responseJson, true);

        if ($response['code'] !== 200) {
            throw new Exception("Response code is {$response['code']}. Message: {$response['message']}");
        }
        return $response['text'];
    }
}