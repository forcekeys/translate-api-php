<?php
/**
 * TranslateAPI PHP SDK - Language Model
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI\Models;

/**
 * Language information
 */
class Language
{
    public string $code;
    public string $name;
    public string $flag;
    
    public function __construct(
        string $code,
        string $name,
        string $flag = ''
    ) {
        $this->code = $code;
        $this->name = $name;
        $this->flag = $flag;
    }
}
