<?php
/**
 * TranslateAPI PHP SDK - Language Detection Result Model
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI\Models;

/**
 * Language detection result
 */
class LanguageDetectionResult
{
    public string $language;
    public string $languageName;
    public float $confidence;
    public array $alternatives;
    
    public function __construct(
        string $language,
        string $languageName,
        float $confidence = 0.0,
        array $alternatives = []
    ) {
        $this->language = $language;
        $this->languageName = $languageName;
        $this->confidence = $confidence;
        $this->alternatives = $alternatives;
    }
}