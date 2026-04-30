<?php
/**
 * TranslateAPI PHP SDK - Account Information Model
 * https://github.com/forcekeys/translate-api-php
 */

namespace ForceKeys\TranslateAPI\Models;

/**
 * Account information
 */
class AccountInfo
{
    public string $email;
    public string $name;
    public string $plan;
    public string $status;
    public int $dailyTranslations;
    public int $todayUsed;
    public int $remainingToday;
    public float $availableBalance;
    public float $totalSpent;
    public int $totalTranslations;
    public int $totalCharacters;
    
    public function __construct(
        string $email,
        string $name = '',
        string $plan = 'free',
        string $status = 'active',
        int $dailyTranslations = 0,
        int $todayUsed = 0,
        int $remainingToday = 0,
        float $availableBalance = 0.0,
        float $totalSpent = 0.0,
        int $totalTranslations = 0,
        int $totalCharacters = 0
    ) {
        $this->email = $email;
        $this->name = $name;
        $this->plan = $plan;
        $this->status = $status;
        $this->dailyTranslations = $dailyTranslations;
        $this->todayUsed = $todayUsed;
        $this->remainingToday = $remainingToday;
        $this->availableBalance = $availableBalance;
        $this->totalSpent = $totalSpent;
        $this->totalTranslations = $totalTranslations;
        $this->totalCharacters = $totalCharacters;
    }
}
