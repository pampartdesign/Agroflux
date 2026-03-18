<?php

namespace App\Services;

use App\Models\LanguageLine;
use Illuminate\Translation\FileLoader;

/**
 * Extends Laravel's FileLoader so that __() / trans() reads from the
 * language_lines database table, with PHP lang-files as fallback.
 *
 * DB values always win over file values, so translators can edit in the
 * admin UI without touching any source files.
 */
class DatabaseTranslationLoader extends FileLoader
{
    public function load($locale, $group, $namespace = null): array
    {
        // Let the standard file loader handle vendor/package namespaces untouched
        if ($namespace && $namespace !== '*') {
            return parent::load($locale, $group, $namespace);
        }

        // File-based translations as base (may be empty if no file exists)
        try {
            $fileLines = parent::load($locale, $group, $namespace);
        } catch (\Throwable) {
            $fileLines = [];
        }

        // DB translations override file-based ones
        $dbLines = $this->loadFromDatabase($locale, $group);

        // EN: DB wins → admin UI edits take effect immediately
        // Other locales: file wins → uploaded translation files take effect immediately
        if ($locale === 'en') {
            return array_replace_recursive($fileLines, $dbLines);
        }
        return array_replace_recursive($dbLines, $fileLines);
    }

    private function loadFromDatabase(string $locale, string $group): array
    {
        try {
            $rows = LanguageLine::query()
                ->where('group', $group)
                ->get(['key', 'en', 'el', 'extra']);
        } catch (\Throwable) {
            // DB not ready (e.g. during artisan migrate or testing without DB)
            return [];
        }

        $result = [];
        foreach ($rows as $row) {
            $value = $row->getTranslation($locale);
            if ($value !== null && $value !== '') {
                // Supports dot-notation keys: "some.nested.key" → nested array
                data_set($result, $row->key, $value);
            }
        }

        return $result;
    }
}
