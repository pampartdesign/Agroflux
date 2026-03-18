<?php

namespace App\Http\Controllers;

use App\Models\MediaAsset;
use App\Services\CurrentTenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MediaLibraryController extends Controller
{
    public function index(Request $request, CurrentTenant $currentTenant)
    {
        $tenant = $currentTenant->model();
        $query = MediaAsset::query()->orderByDesc('id');

        // tenant library + global curated
        $query->where(function($q) use ($tenant) {
            $q->whereNull('tenant_id');
            if ($tenant) {
                $q->orWhere('tenant_id', $tenant->id);
            }
        });

        if ($mime = $request->get('mime')) {
            $query->where('mime', $mime);
        }

        if ($s = trim((string)$request->get('q'))) {
            $query->where('filename', 'like', '%'.$s.'%');
        }

        $assets = $query->paginate(24)->withQueryString();

        return view('media.index', compact('assets','tenant'));
    }

    public function upload(Request $request, CurrentTenant $currentTenant)
    {
        $tenant = $currentTenant->model();

        $maxMb = (int) config('agroflux.media.max_upload_mb', 8);
        $allowedMimes = config('agroflux.media.allowed_mimes', ['image/jpeg','image/png','image/webp']);

        $data = $request->validate([
            'file' => ['required','file','max:'.($maxMb * 1024), Rule::mimetypes($allowedMimes)],
            'alt_text' => ['nullable','string','max:190'],
            'scope' => ['nullable', Rule::in(['tenant','global'])],
        ]);

        $scope = $data['scope'] ?? 'tenant';
        $isGlobal = $scope === 'global' && $request->user()?->is_super_admin;

        $disk = config('agroflux.media.disk', 'public');

        $file = $data['file'];
        $ext = $file->getClientOriginalExtension() ?: 'jpg';
        $safeName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $filename = $safeName.'-'.Str::random(8).'.'.$ext;

        $folder = $isGlobal ? 'media/global' : ('media/tenant/'.($tenant?->id ?? 'unknown'));

        $path = $file->storeAs($folder, $filename, $disk);

        $asset = MediaAsset::create([
            'tenant_id' => $isGlobal ? null : ($tenant?->id),
            'uploaded_by' => $request->user()?->id,
            'disk' => $disk,
            'path' => $path,
            'filename' => $filename,
            'mime' => $file->getClientMimeType() ?: 'application/octet-stream',
            'size' => (int) $file->getSize(),
            'alt_text' => $data['alt_text'] ?? null,
        ]);

        return redirect()->route('media.index')->with('status', 'Uploaded.');
    }
}
