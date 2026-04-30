<?php
/**
 * TranslateAPI PHP SDK - Document Translation Result Model
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI\Models;

/**
 * Document translation result
 */
class DocumentTranslationResult
{
    public string $translatedText;
    public string $sourceLang;
    public string $targetLang;
    public int $pages;
    public int $charactersUsed;
    public int $processingTimeMs;
    
    public function __construct(
        string $translatedText,
        string $sourceLang,
        string $targetLang,
        int $pages = 0,
        int $charactersUsed = 0,
        int $processingTimeMs = 0
    ) {
        $this->translatedText = $translatedText;
        $this->sourceLang = $sourceLang;
        $this->targetLang = $targetLang;
        $this->pages = $pages;
        $this->charactersUsed = $charactersUsed;
        $this->processingTimeMs = $processingTimeMs;
    }
}
