<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\LanguageLine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

class LanguageController extends Controller
{
    // ── Language CRUD ─────────────────────────────────────────────────────────

    public function index()
    {
        $languages     = Language::query()->orderByDesc('is_default')->orderBy('name')->get();
        $configLocales = config('agroflux.locales');

        return view('admin.languages.index', compact('languages', 'configLocales'));
    }

    public function create()
    {
        return view('admin.languages.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code'       => ['required', 'string', 'max:10', 'unique:languages,code'],
            'name'       => ['required', 'string', 'max:255'],
            'is_active'  => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_active']  = (bool) ($data['is_active']  ?? false);
        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        if ($data['is_default']) {
            Language::query()->update(['is_default' => false]);
        }

        Language::create($data);

        return redirect()->route('admin.languages.index')
            ->with('status', "Language \"{$data['name']}\" added. To activate it in the app UI, add '{$data['code']}' to the locales array in config/agroflux.php.");
    }

    public function edit(Language $language)
    {
        return view('admin.languages.edit', compact('language'));
    }

    public function update(Request $request, Language $language): RedirectResponse
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:255'],
            'is_active'  => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
        ]);

        $data['is_active']  = (bool) ($data['is_active']  ?? false);
        $data['is_default'] = (bool) ($data['is_default'] ?? false);

        if ($data['is_default']) {
            Language::query()->update(['is_default' => false]);
        }

        $language->update($data);

        return redirect()->route('admin.languages.index')->with('status', 'Language updated.');
    }

    // ── Translation Lines ─────────────────────────────────────────────────────

    public function lines(Request $request)
    {
        $locales = config('agroflux.locales');
        $group   = $request->get('group', 'app');
        $search  = $request->get('search');
        $filter  = $request->get('filter');

        $query = LanguageLine::query()->where('group', $group)->orderBy('key');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('key', 'like', "%{$search}%")
                  ->orWhere('en', 'like', "%{$search}%")
                  ->orWhere('el', 'like', "%{$search}%")
                  ->orWhere('extra', 'like', "%{$search}%");
            });
        }

        $allLines = $query->get();

        if ($filter === 'missing') {
            $allLines = $allLines->filter(fn($l) => $l->isMissingTranslation())->values();
        }

        // Progress stats over the whole group (ignoring search/filter)
        $allGroupLines = LanguageLine::where('group', $group)->get();
        $groupTotal    = $allGroupLines->count();
        $progress      = [];
        foreach (array_keys($locales) as $locale) {
            $filled            = $allGroupLines->filter(fn($l) => !empty($l->getTranslation($locale)))->count();
            $pct               = $groupTotal > 0 ? (int) round($filled / $groupTotal * 100) : 0;
            $progress[$locale] = ['filled' => $filled, 'total' => $groupTotal, 'pct' => $pct];
        }

        // PHP pagination
        $page  = (int) $request->get('page', 1);
        $perPage = 30;
        $lines = new LengthAwarePaginator(
            $allLines->forPage($page, $perPage)->values(),
            $allLines->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $groups = LanguageLine::query()
            ->select('group')->distinct()->orderBy('group')->pluck('group');

        return view('admin.languages.lines',
            compact('lines', 'group', 'groups', 'locales', 'search', 'filter', 'progress'));
    }

    public function updateLine(Request $request, LanguageLine $line): RedirectResponse
    {
        $localeCodes = array_keys(config('agroflux.locales'));
        $rules       = array_fill_keys($localeCodes, ['nullable', 'string']);
        $data        = $request->validate($rules);

        foreach ($localeCodes as $locale) {
            if (array_key_exists($locale, $data)) {
                $line->setTranslation($locale, $data[$locale] ?: null);
            }
        }
        $line->save();

        return back()->with('status', 'Translation updated.');
    }

    public function createLine(Request $request)
    {
        $locales      = config('agroflux.locales');
        $groups       = LanguageLine::query()->select('group')->distinct()->orderBy('group')->pluck('group');
        $currentGroup = $request->get('group', 'app');

        return view('admin.languages.create-line', compact('locales', 'groups', 'currentGroup'));
    }

    public function storeLine(Request $request): RedirectResponse
    {
        $localeCodes = array_keys(config('agroflux.locales'));
        $rules = [
            'group' => ['required', 'string', 'max:50', 'regex:/^[a-z_]+$/'],
            'key'   => ['required', 'string', 'max:255'],
        ];
        foreach ($localeCodes as $locale) {
            $rules[$locale] = ['nullable', 'string'];
        }

        $data = $request->validate($rules);

        $line = LanguageLine::firstOrNew(['group' => $data['group'], 'key' => $data['key']]);
        foreach ($localeCodes as $locale) {
            $line->setTranslation($locale, $data[$locale] ?? null);
        }
        $line->save();

        return redirect()
            ->route('admin.languages.lines', ['group' => $data['group']])
            ->with('status', "Key \"{$data['group']}.{$data['key']}\" saved.");
    }

    // ── Export CSV ────────────────────────────────────────────────────────────

    public function export(Request $request): Response
    {
        $locales = array_keys(config('agroflux.locales'));
        $group   = $request->get('group'); // null = all groups

        $query = LanguageLine::query()->orderBy('group')->orderBy('key');
        if ($group) {
            $query->where('group', $group);
        }
        $lines = $query->get();

        $filename = $group ? "translations_{$group}.csv" : 'translations_all.csv';

        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_merge(['group', 'key'], $locales));

        foreach ($lines as $line) {
            $row = [$line->group, $line->key];
            foreach ($locales as $locale) {
                $row[] = (string) ($line->getTranslation($locale) ?? '');
            }
            fputcsv($handle, $row);
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    // ── Import CSV ────────────────────────────────────────────────────────────

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:4096'],
        ]);

        $locales  = array_keys(config('agroflux.locales'));
        $path     = $request->file('csv_file')->getRealPath();
        $handle   = fopen($path, 'r');
        $headers  = fgetcsv($handle); // first row = headers

        if (!$headers || !in_array('group', $headers) || !in_array('key', $headers)) {
            fclose($handle);
            return back()->withErrors(['csv_file' => 'Invalid CSV: must have "group" and "key" columns.']);
        }

        $groupIdx = array_search('group', $headers);
        $keyIdx   = array_search('key',   $headers);

        // Map locale columns by position
        $localeIdxMap = [];
        foreach ($locales as $locale) {
            $pos = array_search($locale, $headers);
            if ($pos !== false) {
                $localeIdxMap[$locale] = $pos;
            }
        }

        $imported = 0;
        $skipped  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            $group = trim($row[$groupIdx] ?? '');
            $key   = trim($row[$keyIdx]   ?? '');

            if ($group === '' || $key === '') {
                $skipped++;
                continue;
            }

            $line = LanguageLine::firstOrNew(['group' => $group, 'key' => $key]);

            foreach ($localeIdxMap as $locale => $idx) {
                $value = isset($row[$idx]) ? trim($row[$idx]) : '';
                if ($value !== '') {
                    $line->setTranslation($locale, $value);
                }
            }

            $line->save();
            $imported++;
        }

        fclose($handle);

        app('translator')->setLoaded([]);

        return redirect()
            ->route('admin.languages.lines', ['group' => $request->get('group', 'app')])
            ->with('status', "Imported {$imported} rows. Skipped {$skipped} empty rows.");
    }

    // ── Sync from PHP files ───────────────────────────────────────────────────

    public function syncFromFiles(): RedirectResponse
    {
        $locales  = array_keys(config('agroflux.locales'));
        $langPath = lang_path();
        $synced   = 0;

        foreach ($locales as $locale) {
            $localePath = $langPath . DIRECTORY_SEPARATOR . $locale;
            if (! is_dir($localePath)) {
                continue;
            }

            foreach (glob($localePath . DIRECTORY_SEPARATOR . '*.php') as $file) {
                $group        = pathinfo($file, PATHINFO_FILENAME);
                $translations = include $file;

                if (! is_array($translations)) {
                    continue;
                }

                foreach ($this->flattenArray($translations) as $key => $value) {
                    $line = LanguageLine::firstOrCreate(['group' => $group, 'key' => $key]);

                    // Always overwrite from file — file is the source of truth
                    $line->setTranslation($locale, $value)->save();
                    $synced++;
                }
            }
        }

        // Automatically export back to files so __() picks up all changes immediately
        $this->doSyncToFiles();

        // Clear cached translations
        app('translator')->setLoaded([]);

        return redirect()
            ->route('admin.languages.lines')
            ->with('status', "Synced {$synced} translation entries and exported to lang files. Language switching is now active.");
    }

    // ── Sync DB → lang PHP files (makes __() actually use DB translations) ────

    public function syncToFiles(): RedirectResponse
    {
        $written = $this->doSyncToFiles();

        return redirect()
            ->route('admin.languages.lines')
            ->with('status', "✅ Exported translations to {$written} lang files. Language switching is now active.");
    }

    private function doSyncToFiles(): int
    {
        $locales  = array_keys(config('agroflux.locales'));
        $langPath = lang_path();
        $written  = 0;

        // Group all lines by their 'group' field
        $allLines = LanguageLine::all()->groupBy('group');

        foreach ($locales as $locale) {
            $localePath = $langPath . DIRECTORY_SEPARATOR . $locale;

            if (! is_dir($localePath)) {
                mkdir($localePath, 0755, true);
            }

            foreach ($allLines as $group => $lines) {
                $translations = [];

                foreach ($lines as $line) {
                    $value = $line->getTranslation($locale);
                    // Fall back to English if locale translation is empty
                    if (empty($value)) {
                        $value = $line->getTranslation('en') ?? '';
                    }
                    $translations[$line->key] = $value;
                }

                // Write lang/{locale}/{group}.php
                $filePath = $localePath . DIRECTORY_SEPARATOR . $group . '.php';
                $export   = "<?php\n\nreturn " . var_export($translations, true) . ";\n";
                file_put_contents($filePath, $export);
                $written++;
            }
        }

        // Clear Laravel's translation cache so new files are picked up immediately
        app('translator')->setLoaded([]);
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        return $written;
    }

    private function flattenArray(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $fullKey = $prefix ? "{$prefix}.{$key}" : (string) $key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $fullKey));
            } else {
                $result[$fullKey] = (string) $value;
            }
        }

        return $result;
    }
}
