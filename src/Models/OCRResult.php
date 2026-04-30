<?php
/**
 * TranslateAPI PHP SDK - OCR Result Model
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI\Models;

/**
 * OCR result
 */
class OCRResult
{
    public string $text;
    public float $confidence;
    public string $languageDetected;
    public int $processingTimeMs;
    
    public function __construct(
        string $text,
        float $confidence = 0.0,
        string $languageDetected = '',
        int $processingTimeMs = 0
    ) {
        $this->text = $text;
        $this->confidence = $confidence;
        $this->languageDetected = $languageDetected;
        $this->processingTimeMs = $processingTimeMs;
    }
}
