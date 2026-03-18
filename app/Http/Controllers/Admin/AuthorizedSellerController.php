<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedSeller;
use App\Models\AuthorizedSellerProduct;
use Illuminate\Http\Request;

class AuthorizedSellerController extends Controller
{
    public function index()
    {
        $sellers = AuthorizedSeller::withCount('products')
            ->orderByRaw('CASE WHEN sort_order = 0 THEN 1 ELSE 0 END')
            ->orderBy('sort_order')
            ->orderBy('company_name')
            ->get();

        return view('admin.authorized-sellers.index', compact('sellers'));
    }

    public function create()
    {
        return view('admin.authorized-sellers.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'company_name'      => ['required', 'string', 'max:200'],
            'category'          => ['nullable', 'string', 'max:100'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'address'           => ['nullable', 'string', 'max:500'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'email'             => ['nullable', 'email', 'max:200'],
            'website_url'       => ['nullable', 'url', 'max:500'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
            'is_active'         => ['nullable', 'boolean'],
            'featured_image'    => ['nullable', 'image', 'max:5120'],
            'products'          => ['nullable', 'array'],
            'products.*'        => ['nullable', 'string', 'max:200'],
        ]);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $this->uploadImage($request->file('featured_image'));
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = $request->boolean('is_active', true);

        $seller = AuthorizedSeller::create($data);

        $this->syncProducts($seller, $request->input('products', []));

        return redirect()->route('admin.authorized-sellers.index')
            ->with('status', 'Authorized seller added.');
    }

    public function edit(AuthorizedSeller $authorizedSeller)
    {
        $authorizedSeller->load('products');
        return view('admin.authorized-sellers.edit', ['seller' => $authorizedSeller]);
    }

    public function update(Request $request, AuthorizedSeller $authorizedSeller)
    {
        $data = $request->validate([
            'company_name'      => ['required', 'string', 'max:200'],
            'category'          => ['nullable', 'string', 'max:100'],
            'short_description' => ['nullable', 'string', 'max:1000'],
            'address'           => ['nullable', 'string', 'max:500'],
            'phone'             => ['nullable', 'string', 'max:50'],
            'email'             => ['nullable', 'email', 'max:200'],
            'website_url'       => ['nullable', 'url', 'max:500'],
            'sort_order'        => ['nullable', 'integer', 'min:0'],
            'is_active'         => ['nullable', 'boolean'],
            'featured_image'    => ['nullable', 'image', 'max:5120'],
            'products'          => ['nullable', 'array'],
            'products.*'        => ['nullable', 'string', 'max:200'],
        ]);

        if ($request->hasFile('featured_image')) {
            // Delete old image file if exists
            if ($authorizedSeller->featured_image) {
                $oldPath = public_path('uploads/' . $authorizedSeller->featured_image);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $data['featured_image'] = $this->uploadImage($request->file('featured_image'));
        } else {
            unset($data['featured_image']);
        }

        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active']  = $request->boolean('is_active', true);

        $authorizedSeller->update($data);

        $this->syncProducts($authorizedSeller, $request->input('products', []));

        return redirect()->route('admin.authorized-sellers.index')
            ->with('status', '"' . $authorizedSeller->company_name . '" updated.');
    }

    public function destroy(AuthorizedSeller $authorizedSeller)
    {
        if ($authorizedSeller->featured_image) {
            $path = public_path('uploads/' . $authorizedSeller->featured_image);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $authorizedSeller->delete();

        return redirect()->route('admin.authorized-sellers.index')
            ->with('status', 'Seller removed.');
    }

    public function toggle(AuthorizedSeller $authorizedSeller)
    {
        $authorizedSeller->update(['is_active' => !$authorizedSeller->is_active]);
        return back()->with('status', $authorizedSeller->is_active ? 'Seller published.' : 'Seller unpublished.');
    }

    // ── Private ─────────────────────────────────────────────────────────────────

    private function uploadImage($file): string
    {
        $dir = public_path('uploads/authorized-sellers');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = time() . '_' . uniqid() . '.jpg';
        $destPath = $dir . '/' . $filename;

        // Load source image via GD
        $mime = $file->getMimeType();
        $src  = match (true) {
            str_contains($mime, 'png')  => imagecreatefrompng($file->getRealPath()),
            str_contains($mime, 'gif')  => imagecreatefromgif($file->getRealPath()),
            str_contains($mime, 'webp') => imagecreatefromwebp($file->getRealPath()),
            default                     => imagecreatefromjpeg($file->getRealPath()),
        };

        $origW = imagesx($src);
        $origH = imagesy($src);
        $maxSize = 300;

        // Calculate new dimensions keeping aspect ratio
        if ($origW > $maxSize || $origH > $maxSize) {
            $ratio = min($maxSize / $origW, $maxSize / $origH);
            $newW  = (int) round($origW * $ratio);
            $newH  = (int) round($origH * $ratio);
        } else {
            $newW = $origW;
            $newH = $origH;
        }

        $dst = imagecreatetruecolor($newW, $newH);

        // Preserve transparency for PNG/WEBP
        if (str_contains($mime, 'png') || str_contains($mime, 'webp')) {
            imagealphablending($dst, false);
            imagesavealpha($dst, true);
            $transparent = imagecolorallocatealpha($dst, 0, 0, 0, 127);
            imagefilledrectangle($dst, 0, 0, $newW, $newH, $transparent);
        }

        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newW, $newH, $origW, $origH);
        imagejpeg($dst, $destPath, 88);

        imagedestroy($src);
        imagedestroy($dst);

        return 'authorized-sellers/' . $filename;
    }

    private function syncProducts(AuthorizedSeller $seller, array $names): void
    {
        $seller->products()->delete();

        $order = 1;
        foreach ($names as $name) {
            $name = trim($name ?? '');
            if ($name === '') continue;
            AuthorizedSellerProduct::create([
                'authorized_seller_id' => $seller->id,
                'name'                 => $name,
                'sort_order'           => $order++,
            ]);
        }
    }
}
