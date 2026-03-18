<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanguageLine extends Model
{
    protected $fillable = ['group', 'key', 'en', 'el', 'extra'];

    protected $casts = ['extra' => 'array'];

    /**
     * Get the translation value for any locale.
     * 'en' and 'el' have dedicated columns; all others live in the extra JSON.
     */
    public function getTranslation(string $locale): ?string
    {
        if ($locale === 'en') return $this->en;
        if ($locale === 'el') return $this->el;

        return data_get($this->extra, $locale);
    }

    /**
     * Set the translation value for any locale (does not save — call save() after).
     */
    public function setTranslation(string $locale, ?string $value): static
    {
        if ($locale === 'en') {
            $this->en = $value;
        } elseif ($locale === 'el') {
            $this->el = $value;
        } else {
            $extra = $this->extra ?? [];
            $extra[$locale] = $value;
            $this->extra = $extra;
        }

        return $this;
    }

    /**
     * True if any configured locale is missing a translation for this line.
     */
    public function isMissingTranslation(): bool
    {
        foreach (array_keys(config('agroflux.locales')) as $locale) {
            if (empty($this->getTranslation($locale))) {
                return true;
            }
        }

        return false;
    }
}
