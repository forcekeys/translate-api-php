<?php
/**
 * TranslateAPI PHP SDK - Main Client
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI\Client;

use ForceKeys\TranslateAPI\Exceptions\APIException;
use ForceKeys\TranslateAPI\Models\TranslationResult;
use ForceKeys\TranslateAPI\Models\DocumentTranslationResult;
use ForceKeys\TranslateAPI\Models\OCRResult;
use ForceKeys\TranslateAPI\Models\LanguageDetectionResult;
use ForceKeys\TranslateAPI\Models\BatchTranslationResult;
use ForceKeys\TranslateAPI\Models\AccountInfo;
use ForceKeys\TranslateAPI\Models\Language;
use InvalidArgumentException;
use RuntimeException;

/**
 * TranslateAPI PHP client
 * 
 * Example:
 * ```php
 * use ForceKeys\TranslateAPI\Client\TranslateAPIClient;
 * 
 * $api = new TranslateAPIClient('your_api_key');
 * $result = $api->translate('Hello, world!', 'en', 'fr');
 * echo $result->translatedText;
 * ```
 */
class TranslateAPIClient
{
    private string $apiKey;
    private string $baseUrl;
    private int $timeout;
    
    private const BASE_URL = 'https://api.deeptranslate.online/api/v1';
    private const DEFAULT_TIMEOUT = 30;
    
    /**
     * Initialize TranslateAPI client
     * 
     * @param string $apiKey Your API key (or set TRANSLATE_API_KEY environment variable)
     * @param string $baseUrl API base URL
     * @param int $timeout Request timeout in seconds
     */
    public function __construct(
        ?string $apiKey = null,
        string $baseUrl = self::BASE_URL,
        int $timeout = self::DEFAULT_TIMEOUT
    ) {
        $this->apiKey = $apiKey ?? getenv('TRANSLATE_API_KEY') ?: '';
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->timeout = $timeout;
        
        if (empty($this->apiKey)) {
            throw new InvalidArgumentException(
                'API key is required. Provide it as argument or set ' .
                'TRANSLATE_API_KEY environment variable.'
            );
        }
    }
    
    /**
     * Make API request
     * 
     * @param string $endpoint API endpoint
     * @param string $method HTTP method
     * @param array|null $data Request data
     * @param array|null $files Files to upload
     * @return array API response
     * @throws APIException If API returns an error
     * @throws RuntimeException If network error occurs
     */
    private function makeRequest(
        string $endpoint,
        string $method = 'GET',
        ?array $data = null,
        ?array $files = null
    ): array {
        $url = $this->baseUrl . '/' . ltrim($endpoint, '/');
        
        $headers = [
            'Authorization: Bearer ' . $this->apiKey,
            'User-Agent: TranslateAPI-PHP/1.0.0'
        ];
        
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            
            if ($files) {
                // Handle file uploads
                $postData = [];
                
                if ($data) {
                    foreach ($data as $key => $value) {
                        $postData[$key] = $value;
                    }
                }
                
                foreach ($files as $key => $file) {
                    if (is_array($file) && isset($file['path'])) {
                        $postData[$key] = new \CURLFile(
                            $file['path'],
                            $file['mime'] ?? mime_content_type($file['path']),
                            $file['name'] ?? basename($file['path'])
                        );
                    } else {
                        $postData[$key] = $file;
                    }
                }
                
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            } elseif ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new RuntimeException("cURL error: $error");
        }
        
        $result = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException('Invalid JSON response from API');
        }
        
        if ($httpCode >= 400) {
            $errorCode = $result['code'] ?? 'http_error';
            $errorMsg = $result['message'] ?? "HTTP error: $httpCode";
            $retryAfter = $result['retry_after'] ?? null;
            
            throw new APIException($errorMsg, $errorCode, $httpCode, $retryAfter);
        }
        
        if (isset($result['status']) && $result['status'] === 'error') {
            $errorCode = $result['code'] ?? 'api_error';
            $errorMsg = $result['message'] ?? 'Unknown API error';
            $retryAfter = $result['retry_after'] ?? null;
            
            throw new APIException($errorMsg, $errorCode, $httpCode, $retryAfter);
        }
        
        return $result;
    }
    
    /**
     * Translate text
     * 
     * @param string $text Text to translate
     * @param string $targetLang Target language code (e.g., 'fr')
     * @param string|null $sourceLang Source language code (optional, auto-detect if null)
     * @param string|null $formality Formality level: 'formal' or 'informal' (optional)
     * @return TranslationResult Translation result
     */
    public function translate(
        string $text,
        string $targetLang,
        ?string $sourceLang = null,
        ?string $formality = null
    ): TranslationResult {
        $data = [
            'text' => $text,
            'target_lang' => $targetLang
        ];
        
        if ($sourceLang !== null) {
            $data['source_lang'] = $sourceLang;
        }
        
        if ($formality !== null) {
            $data['formality'] = $formality;
        }
        
        $response = $this->makeRequest('translate', 'POST', $data);
        
        return new TranslationResult(
            $response['translated_text'],
            $response['source_lang'],
            $response['target_lang'],
            $response['detected_lang'] ?? null,
            $response['characters_used'] ?? 0,
            $response['processing_time_ms'] ?? 0
        );
    }
    
    /**
     * Translate document
     * 
     * @param string $filePath Path to document file (PDF, DOCX, TXT)
     * @param string $targetLang Target language code
     * @param string|null $sourceLang Source language code (optional)
     * @return DocumentTranslationResult Document translation result
     */
    public function translateDocument(
        string $filePath,
        string $targetLang,
        ?string $sourceLang = null
    ): DocumentTranslationResult {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File not found: $filePath");
        }
        
        $files = [
            'file' => [
                'path' => $filePath,
                'name' => basename($filePath)
            ]
        ];
        
        $data = [
            'target_lang' => $targetLang
        ];
        
        if ($sourceLang !== null) {
            $data['source_lang'] = $sourceLang;
        }
        
        $response = $this->makeRequest('translate/document', 'POST', $data, $files);
        
        return new DocumentTranslationResult(
            $response['translated_text'],
            $response['source_lang'],
            $response['target_lang'],
            $response['pages'] ?? 0,
            $response['characters_used'] ?? 0,
            $response['processing_time_ms'] ?? 0
        );
    }
    
    /**
     * Extract text from image (OCR)
     * 
     * @param string $filePath Path to image file (PNG, JPG, WEBP, BMP)
     * @param string|null $lang Expected language (optional, improves accuracy)
     * @param bool $enhance Apply image enhancement (optional)
     * @return OCRResult OCR result
     */
    public function ocr(
        string $filePath,
        ?string $lang = null,
        bool $enhance = false
    ): OCRResult {
        if (!file_exists($filePath)) {
            throw new InvalidArgumentException("File not found: $filePath");
        }
        
        $files = [
            'image' => [
                'path' => $filePath,
                'name' => basename($filePath)
            ]
        ];
        
        $data = [];
        
        if ($lang !== null) {
            $data['lang'] = $lang;
        }
        
        if ($enhance) {
            $data['enhance'] = 'true';
        }
        
        $response = $this->makeRequest('ocr', 'POST', $data, $files);
        
        return new OCRResult(
            $response['text'],
            $response['confidence'] ?? 0.0,
            $response['language_detected'] ?? '',
            $response['processing_time_ms'] ?? 0
        );
    }
    
    /**
     * Detect language of text
     * 
     * @param string $text Text to analyze
     * @return LanguageDetectionResult Language detection result
     */
    public function detectLanguage(string $text): LanguageDetectionResult
    {
        $data = ['text' => $text];
        $response = $this->makeRequest('detect', 'POST', $data);
        
        $alternatives = [];
        if (isset($response['alternatives'])) {
            foreach ($response['alternatives'] as $alt) {
                $alternatives[] = [
                    'language' => $alt['language'],
                    'confidence' => $alt['confidence'],
                    'language_name' => $alt['language_name'] ?? ''
                ];
            }
        }
        
        return new LanguageDetectionResult(
            $response['language'],
            $response['language_name'] ?? '',
            $response['confidence'] ?? 0.0,
            $alternatives
        );
    }
    
    /**
     * Get supported languages
     * 
     * @return array List of Language objects
     */
    public function getSupportedLanguages(): array
    {
        $response = $this->makeRequest('languages');
        
        $languages = [];
        if (isset($response['languages'])) {
            foreach ($response['languages'] as $lang) {
                $languages[] = new Language(
                    $lang['code'],
                    $lang['name'],
                    $lang['flag'] ?? ''
                );
            }
        }
        
        return $languages;
    }
    
    /**
     * Batch translate multiple texts
     * 
     * @param array $texts Array of texts to translate
     * @param string $targetLang Target language code
     * @param string|null $sourceLang Source language code (optional)
     * @return BatchTranslationResult Batch translation result
     */
    public function batchTranslate(
        array $texts,
        string $targetLang,
        ?string $sourceLang = null
    ): BatchTranslationResult {
        $data = [
            'texts' => $texts,
            'target_lang' => $targetLang
        ];
        
        if ($sourceLang !== null) {
            $data['source_lang'] = $sourceLang;
        }
        
        $response = $this->makeRequest('translate/batch', 'POST', $data);
        
        return new BatchTranslationResult(
            $response['translations'] ?? [],
            $response['characters_used'] ?? 0,
            $response['processing_time_ms'] ?? 0
        );
    }
    
    /**
     * Get account information
     * 
     * @return AccountInfo Account information
     */
    public function getAccountInfo(): AccountInfo
    {
        $response = $this->makeRequest('account');
        
        $account = $response['account'] ?? [];
        $planLimits = $account['plan_limits'] ?? [];
        $balance = $account['balance'] ?? [];
        $statistics = $account['statistics'] ?? [];
        
        return new AccountInfo(
            $account['email'] ?? '',
            $account['name'] ?? '',
            $account['plan'] ?? 'free',
            $account['status'] ?? 'active',
            $planLimits['daily_translations'] ?? 0,
            $planLimits['today_used'] ?? 0,
            $planLimits['remaining_today'] ?? 0,
            $balance['available'] ?? 0.0,
            $balance['total_spent'] ?? 0.0,
            $statistics['total_translations'] ?? 0,
            $statistics['total_characters'] ?? 0
        );
    }
}
