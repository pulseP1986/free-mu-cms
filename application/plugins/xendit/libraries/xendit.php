<?php

class Xendit {
    /**
     * Xendit API Endpoint
     */
    const ENDPOINT = 'https://tpi.xendit.co';
    const PLUGIN_NAME = 'DMNMUCMS';
    const DEFAULT_STORE_NAME = 'DMNMUCMS_STORE';

    const METHOD_POST = 'POST';
    const METHOD_GET = 'GET';
    /**
     * Secret API Key.
     * @var string
     */
    private static $secret_key = '';
    /**
     * Set secret API Key.
     * @param string $key
     */
    public static function set_secret_key($secret_key) {
        self::$secret_key = $secret_key;
    }
    /**
     * Get secret key.
     * @return string
     */
    public static function get_secret_key() {
        return self::$secret_key;
    }
    /**
     * Public API Key.
     * @var string
     */
    private static $public_key = '';
    /**
     * Set public API Key.
     * @param string $key
     */
    public static function set_public_key($public_key) {
        self::$public_key = $public_key;
    }
    /**
     * Get public key.
     * @return string
     */
    public static function get_public_key() {
        return self::$public_key;
    }
    /**
     * Generates header for API request
     *
     * @since 1.2.3
     * @version 1.2.3
     */
    public static function get_headers($options)
    {
        $headers = array();
        $headers[] = 'x-plugin-name: ' . self::PLUGIN_NAME;
        $headers[] = 'x-plugin-version: 1.0';
        $headers[] = 'x-plugin-store-name: ' . isset($options['store_name']) ? $options['store_name'] : self::DEFAULT_STORE_NAME;
        $headers[] = 'Content-Type: application/json';

        return $headers;
    }
    /**
     * Send the request to Xendit's API
     *
     * @param array $request
     * @param string $api
     * @return array|WP_Error
     */
    public static function request($url, $method, $payload = array(), $options = array())
    {
        $ch = curl_init();
        $header = self::get_headers($options);

        $api_key = self::get_secret_key();

        if (isset($options['should_use_public_key']) && $options['should_use_public_key']) {
            $api_key = self::get_public_key();
        }
        $curl_options = array(
            CURLOPT_URL => self::ENDPOINT . $url,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERPWD => $api_key . ':'
        );

        if ($method === self::METHOD_POST) {
            $curl_options[CURLOPT_POST] = true;
            $curl_options[CURLOPT_POSTFIELDS] = json_encode($payload);
        }

        curl_setopt_array($ch, $curl_options);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) {
            throw new Exception('Xendit cURL Error, error code: ' . curl_error($ch), curl_errno($ch));
        }

        $json_response = json_decode($response, true);

        return $json_response;
    }
}
