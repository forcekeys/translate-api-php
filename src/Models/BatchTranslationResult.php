<?php
/**
 * TranslateAPI PHP SDK - Batch Translation Result Model
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI\Models;

/**
 * Batch translation result
 */
class BatchTranslationResult
{
    public array $translations;
    public int $charactersUsed;
    public int $processingTimeMs;
    
    public function __construct(
        array $translations,
        int $charactersUsed = 0,
        int $processingTimeMs = 0
    ) {
        $this->translations = $translations;
        $this->charactersUsed = $charactersUsed;
        $this->processingTimeMs = $processingTimeMs;
    }
}