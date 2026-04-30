<?php
/**
 * TranslateAPI PHP SDK - Translation Result Model
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI\Models;

/**
 * Translation result
 */
class TranslationResult
{
    public string $translatedText;
    public string $sourceLang;
    public string $targetLang;
    public ?string $detectedLang;
    public int $charactersUsed;
    public int $processingTimeMs;
    
    public function __construct(
        string $translatedText,
        string $sourceLang,
        string $targetLang,
        ?string $detectedLang = null,
        int $charactersUsed = 0,
        int $processingTimeMs = 0
    ) {
        $this->translatedText = $translatedText;
        $this->sourceLang = $sourceLang;
        $this->targetLang = $targetLang;
        $this->detectedLang = $detectedLang;
        $this->charactersUsed = $charactersUsed;
        $this->processingTimeMs = $processingTimeMs;
    }
}
