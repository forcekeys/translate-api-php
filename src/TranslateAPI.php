<?php
/**
 * TranslateAPI PHP SDK - Main Entry Point
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI;

use ForceKeys\TranslateAPI\Client\TranslateAPIClient;

/**
 * TranslateAPI PHP SDK
 * 
 * This is the main entry point for the TranslateAPI PHP SDK.
 * It provides a simple interface to create a client instance.
 * 
 * Example:
 * ```php
 * use ForceKeys\TranslateAPI\TranslateAPI;
 * 
 * $api = new TranslateAPI('your_api_key');
 * $result = $api->translate('Hello, world!', 'en', 'fr');
 * echo $result->translatedText;
 * ```
 */
class TranslateAPI extends TranslateAPIClient
{
    /**
     * Initialize TranslateAPI client
     * 
     * @param string $apiKey Your API key (or set TRANSLATE_API_KEY environment variable)
     * @param string $baseUrl API base URL
     * @param int $timeout Request timeout in seconds
     */
    public function __construct(
        ?string $apiKey = null,
        string $baseUrl = 'https://api.translate.forcekeys.com/api/v1',
        int $timeout = 30
    ) {
        parent::__construct($apiKey, $baseUrl, $timeout);
    }
}