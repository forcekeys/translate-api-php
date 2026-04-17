# TranslateAPI PHP SDK

[![Packagist Version](https://img.shields.io/packagist/v/forcekeys/translate-api.svg)](https://packagist.org/packages/forcekeys/translate-api)
[![PHP Version](https://img.shields.io/packagist/php-v/forcekeys/translate-api.svg)](https://packagist.org/packages/forcekeys/translate-api)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Documentation](https://img.shields.io/badge/docs-forcekeys.com-blue.svg)](https://translate.forcekeys.com/docs)

Official PHP client library for the TranslateAPI translation service. Translate text, documents, and images between 70+ languages with a simple, intuitive interface.

## Features

- **Text Translation**: Translate text between 70+ languages
- **Document Translation**: Support for PDF, DOCX, TXT files
- **Image OCR**: Extract and translate text from images
- **Language Detection**: Automatically detect language of text
- **Batch Translation**: Translate multiple texts in a single request
- **Account Management**: Check usage, credits, and account info
- **PSR-7/PSR-18 Compatible**: Works with any HTTP client
- **Guzzle Integration**: Built-in Guzzle support

## Installation

### Using Composer (Recommended)

```bash
composer require forcekeys/translate-api
```

### Manual Installation

```bash
git clone https://github.com/forcekeys/translate-api-php.git
cd translate-api-php
composer install
```

## Quick Start

### 1. Get Your API Key

First, sign up at [translate.forcekeys.com](https://translate.forcekeys.com) to get your free API key.

### 2. Basic Usage

```php
<?php
require_once 'vendor/autoload.php';

use ForceKeys\TranslateAPI\TranslateAPI;

// Initialize with your API key
$api = new TranslateAPI('your_api_key_here');

// Translate text
$result = $api->translate('Hello, world!', 'en', 'fr');
echo "Translated: " . $result->translatedText . "\n";
echo "Characters used: " . $result->charactersUsed . "\n";
echo "Processing time: " . $result->processingTimeMs . "ms\n";

// Auto-detect source language
$result = $api->translate('Bonjour le monde', null, 'en');
echo "Detected language: " . $result->sourceLang . "\n";
echo "Translated: " . $result->translatedText . "\n";
```

## Comprehensive Examples

### Text Translation

```php
<?php
use ForceKeys\TranslateAPI\TranslateAPI;

$api = new TranslateAPI('your_api_key_here');

// Basic translation
$result = $api->translate(
    'Hello, how are you?',
    'en',
    'es'
);

// With formality control
$result = $api->translate(
    'Hello, how are you?',
    'en',
    'de',
    ['formality' => 'formal']  // or 'informal'
);

// Translation with context
$result = $api->translate(
    'The bank is closed on Sunday.',
    'en',
    'fr',
    ['context' => 'financial']  // Helps with ambiguous words
);
```

### Document Translation

```php
// Translate a document file
$result = $api->translateDocument(
    'document.pdf',
    'en',
    'es'
);

// Save translated text to file
file_put_contents('translated_document.txt', $result->translatedText);

echo "Translated " . $result->pages . " pages\n";
echo "Used " . $result->charactersUsed . " characters\n";
```

### Image OCR and Translation

```php
// Extract text from image and translate
$result = $api->ocrAndTranslate(
    'receipt.png',
    'en',
    'fr'
);

echo "Extracted text: " . $result->extractedText . "\n";
echo "Translated text: " . $result->translatedText . "\n";
echo "Confidence: " . $result->confidence . "%\n";
```

### Language Detection

```php
// Detect language of text
$detection = $api->detect('Bonjour le monde');

echo "Detected language: " . $detection->language . "\n";
echo "Language name: " . $detection->languageName . "\n";
echo "Confidence: " . $detection->confidence . "%\n";

// Show alternative possibilities
foreach ($detection->alternatives as $alt) {
    echo "  - " . $alt->language . ": " . $alt->confidence . "%\n";
}
```

### Batch Translation

```php
// Translate multiple texts at once
$texts = [
    'Hello',
    'Goodbye',
    'Thank you',
    'Please'
];

$results = $api->batchTranslate(
    $texts,
    'en',
    'de'
);

foreach ($results->translations as $item) {
    echo $item->original . " => " . $item->translated . "\n";
}
```

### Account Information

```php
// Get account details
$account = $api->account();

echo "Email: " . $account->email . "\n";
echo "Plan: " . $account->plan . "\n";
echo "Status: " . $account->status . "\n";

// Usage statistics
$limits = $account->planLimits;
echo "Daily translations: " . $limits->todayUsed . "/" . $limits->dailyTranslations . "\n";
echo "Remaining today: " . $limits->remainingToday . "\n";

// Balance information
$balance = $account->balance;
echo "Available balance: $" . number_format($balance->available, 2) . "\n";
echo "Total spent: $" . number_format($balance->totalSpent, 2) . "\n";
```

### Supported Languages

```php
// Get all supported languages
$languages = $api->languages();

echo "Total languages: " . $languages->count . "\n";
foreach ($languages->languages as $lang) {
    echo $lang->flag . " " . $lang->code . ": " . $lang->name . "\n";
}
```

## Advanced Configuration

### Custom HTTP Client

```php
<?php
use ForceKeys\TranslateAPI\TranslateAPI;
use GuzzleHttp\Client;

// Use custom Guzzle client
$httpClient = new Client([
    'timeout' => 30,
    'verify' => false, // For development only
]);

$api = new TranslateAPI('your_api_key', [
    'http_client' => $httpClient,
    'base_url' => 'https://api.translate.forcekeys.com/api/v1',
]);
```

### Environment Variables

```php
<?php
use ForceKeys\TranslateAPI\TranslateAPI;

// Read API key from environment variable
$apiKey = getenv('FORCEKEYS_API_KEY');
$api = new TranslateAPI($apiKey);
```

### Error Handling

```php
<?php
use ForceKeys\TranslateAPI\TranslateAPI;
use ForceKeys\TranslateAPI\Exception\APIException;

$api = new TranslateAPI('your_api_key');

try {
    $result = $api->translate('Hello', 'en', 'fr');
} catch (APIException $e) {
    echo "API Error: " . $e->getCode() . " - " . $e->getMessage() . "\n";
    echo "Status Code: " . $e->getStatusCode() . "\n";
    
    if ($e->getCode() === 'rate_limit_exceeded') {
        echo "Retry after: " . $e->getRetryAfter() . " seconds\n";
    } elseif ($e->getCode() === 'insufficient_credits') {
        echo "Please add credits to your account\n";
    }
} catch (Exception $e) {
    echo "Unexpected error: " . $e->getMessage() . "\n";
}
```

## API Reference

### TranslateAPI Class

```php
new TranslateAPI(string $apiKey, array $options = [])
```

#### Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `base_url` | string | `https://api.translate.forcekeys.com/api/v1` | API base URL |
| `http_client` | Psr\Http\Client\ClientInterface | GuzzleHttp\Client | HTTP client instance |
| `timeout` | int | 30 | Request timeout in seconds |
| `retries` | int | 3 | Number of retry attempts |

#### Methods

| Method | Description | Parameters |
|--------|-------------|------------|
| `translate(string $text, ?string $source, string $target, array $options = [])` | Translate text | `$text`: Text to translate<br>`$source`: Source language code (null for auto-detect)<br>`$target`: Target language code<br>`$options`: Additional options (formality, context) |
| `translateDocument(string $filePath, ?string $source, string $target)` | Translate document file | `$filePath`: Path to document (PDF, DOCX, TXT)<br>`$source`: Source language code<br>`$target`: Target language code |
| `ocrAndTranslate(string $imagePath, ?string $source, string $target, bool $enhance = false)` | Extract text from image and translate | `$imagePath`: Path to image file<br>`$source`: Source language code<br>`$target`: Target language code<br>`$enhance`: Apply image enhancement |
| `detect(string $text)` | Detect language of text | `$text`: Text to analyze |
| `batchTranslate(array $texts, ?string $source, string $target)` | Translate multiple texts | `$texts`: Array of texts to translate<br>`$source`: Source language code<br>`$target`: Target language code |
| `languages()` | Get supported languages | |
| `account()` | Get account information | |

### Response Objects

All methods return typed response objects with the following common properties:

- `status`: "success" or "error"
- `processingTimeMs`: Processing time in milliseconds
- `charactersUsed`: Number of characters used

#### Translation Response
- `translatedText`: Translated text
- `sourceLang`: Source language code
- `targetLang`: Target language code

#### Document Translation Response
- `translatedText`: Translated text
- `pages`: Number of pages processed
- `charactersUsed`: Characters used

#### OCR Response
- `extractedText`: Text extracted from image
- `translatedText`: Translated text (if translation requested)
- `confidence`: OCR confidence percentage
- `languageDetected`: Detected language in image

#### Detection Response
- `language`: Detected language code
- `languageName`: Full language name
- `confidence`: Detection confidence percentage
- `alternatives`: Array of alternative possibilities

#### Account Response
- `email`: User email
- `plan`: Subscription plan
- `status`: Account status
- `planLimits`: Array with usage limits
- `balance`: Array with balance information
- `statistics`: Usage statistics

## Error Codes

The SDK throws `APIException` for API errors:

| Code | Description | HTTP Status |
|------|-------------|-------------|
| `invalid_request` | Missing or malformed parameters | 400 |
| `unauthorized` | Invalid or missing API key | 401 |
| `forbidden` | Feature not available on your plan | 403 |
| `payload_too_large` | File or text exceeds size limit | 413 |
| `unsupported_language` | Language code not supported | 422 |
| `rate_limit_exceeded` | Too many requests | 429 |
| `insufficient_credits` | Not enough credits | 402 |
| `internal_error` | Server error | 500 |

## Rate Limits

Rate limits vary by plan:

| Plan | Requests/Minute | Monthly Requests | Max Characters/Request |
|------|----------------|------------------|------------------------|
| Free | 10 | 500/day | 2,000 |
| Starter | 60 | 50,000 | 5,000 |
| Professional | 300 | 1,000,000 | 10,000 |
| Enterprise | Unlimited | Unlimited | Unlimited |

## Framework Integration

### Laravel

```php
// config/services.php
return [
    'forcekeys' => [
        'api_key' => env('FORCEKEYS_API_KEY'),
        'base_url' => env('FORCEKEYS_BASE_URL', 'https://api.translate.forcekeys.com/api/v1'),
    ],
];

// AppServiceProvider.php
use ForceKeys\TranslateAPI\TranslateAPI;

public function register()
{
    $this->app->singleton(TranslateAPI::class, function ($app) {
        return new TranslateAPI(
            config('services.forcekeys.api_key'),
            ['base_url' => config('services.forcekeys.base_url')]
        );
    });
}

// Usage in controller
use ForceKeys\TranslateAPI\TranslateAPI;

public function translate(Request $request, TranslateAPI $api)
{
    $result = $api->translate($request->text, 'en', 'fr');
    return response()->json($result);
}
```

### Symfony

```yaml
# config/services.yaml
services:
    ForceKeys\TranslateAPI\TranslateAPI:
        arguments:
            $apiKey: '%env(FORCEKEYS_API_KEY)%'
            $options:
                base_url: '%env(FORCEKEYS_BASE_URL)%'
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Support

- **Documentation**: [translate.forcekeys.com/docs](https://translate.forcekeys.com/docs)
- **Issues**: [GitHub Issues](https://github.com/forcekeys/translate-api-php/issues)
- **Email**: support@forcekeys.com
- **Discord**: [Join our Discord](https://discord.gg/forcekeys)

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Related Projects

- [TranslateAPI Python SDK](https://github.com/forcekeys/translate-api-python)
- [TranslateAPI JavaScript SDK](https://github.com/forcekeys/translate-api-js)
- [TranslateAPI Java SDK](https://github.com/forcekeys/translate-api-java)
- [TranslateAPI .NET SDK](https://github.com/forcekeys/translate-api-dotnet)
- [TranslateAPI Shell](https://github.com/forcekeys/translate-api-shell)
