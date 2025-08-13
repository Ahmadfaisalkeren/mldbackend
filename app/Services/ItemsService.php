<?php

namespace App\Services;

use App\Models\Items;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

/**
 * Class ItemsService.
 */
class ItemsService
{
    public function getItems()
    {
        $items = Items::with('category')
            ->orderBy('created_at', 'desc')
            ->get();

        return $items;
    }

    public function storeItem(array $itemData)
    {
        if (isset($itemData['images'])) {
            $images = is_array($itemData['images']) ? $itemData['images'] : [$itemData['images']];
            $storedImages = $this->storeImages($images);
            $itemData['images'] = $storedImages;
        }

        $itemData['item_code'] = $this->generateUniqueItemCode();
        $qrCodePath = $this->generateAndStoreQRCode($itemData['item_code']);
        $itemData['qrcode'] = $qrCodePath;

        $item = Items::create($itemData);

        return $item;
    }

    private function generateAndStoreQRCode($itemCode)
    {
        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($itemCode)
            ->size(300)
            ->margin(0)
            ->build();

        $path = 'qrcodes/' . $itemCode . '.png';
        Storage::disk('public')->put($path, $result->getString());

        return $path;
    }

    private function generateUniqueItemCode()
    {
        $count = Items::count() + 1;
        $formattedNumber = str_pad($count, 5, '0', STR_PAD_LEFT);
        return 'MLD-' . $formattedNumber;
    }

    private function storeImages(array $images)
    {
        $storedPaths = [];

        foreach ($images as $image) {
            if ($image->isValid()) {
                $imageName = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs('images/items', $imageName, 'public');
                $storedPaths[] = $path;
            }
        }

        return $storedPaths;
    }

    public function getItemById($id)
    {
        $item = Items::findOrFail($id);
        return $item;
    }

    public function updateItem(Items $item, array $itemData)
    {
        $item->name = $itemData['name'] ?? $item->name;
        $item->category_id = $itemData['category_id'] ?? $item->category_id;
        $item->description = $itemData['description'] ?? $item->description;
        $item->size = $itemData['size'] ?? $item->size;
        $item->stock = $itemData['stock'] ?? $item->stock;

        $existingImages = $item->images ?? [];

        if (isset($itemData['existing_images'])) {
            $incoming = is_array($itemData['existing_images'])
                ? $itemData['existing_images']
                : json_decode($itemData['existing_images'], true);

            $remainingImages = array_values(array_intersect($existingImages, $incoming));

            $deletedImages = array_diff($existingImages, $remainingImages);
            foreach ($deletedImages as $path) {
                Storage::disk('public')->delete($path);
            }

            $existingImages = $remainingImages;
        } else {
            foreach ($existingImages as $path) {
                Storage::disk('public')->delete($path);
            }
            $existingImages = [];
        }

        $newImagePaths = [];

        if (!empty($itemData['images']) && is_iterable($itemData['images'])) {
            $newImagePaths = $this->storeImages($itemData['images']);
        }

        $item->images = array_merge($existingImages, $newImagePaths);

        $item->save();

        return $item;
    }


    public function deleteItem($id)
    {
        $item = Items::findOrFail($id);

        $imagePaths = json_decode($item->images, true);

        if (is_array($imagePaths)) {
            foreach ($imagePaths as $path) {
                $this->deleteImage($path);
            }
        }

        if ($item->qrcode) {
            Storage::disk('public')->delete($item->qrcode);
        }

        $item->delete();
    }

    private function deleteImage($imagePath)
    {
        if ($imagePath && Storage::disk('public')->exists($imagePath)) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    public function generateItemsPDF()
    {
        $items = Items::with('category')->latest()->get();

        $html = view('items_pdf', compact('items'))->render();

        collect(glob(storage_path('app/public/reports/items_report_*.pdf')))
            ->each(fn($file) => @unlink($file));

        $filename = 'items_report_' . now()->format('Ymd_His') . '.pdf';
        $pdfPath = storage_path("app/public/reports/{$filename}");

        Browsershot::html($html)
            ->setOption('executablePath', 'C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->addChromiumArguments([
                '--disable-dev-shm-usage',
                '--no-sandbox',
            ])
            ->format('A4')
            ->waitUntilNetworkIdle()
            ->showBackground()
            ->save($pdfPath);

        return $pdfPath;
    }

    public function generateItemStickers(array $selectedIds = [])
    {
        $items = Items::with('category')
            ->when($selectedIds, fn($query) => $query->whereIn('id', $selectedIds))
            ->latest()
            ->get();

        $html = view('items/item_stickers', compact('items'))->render();

        collect(glob(storage_path('app/public/reports/items_stickers_*.pdf')))
            ->each(fn($file) => @unlink($file));

        $filename = 'items_stickers_' . now()->format('Ymd_His') . '.pdf';
        $pdfPath = storage_path("app/public/reports/{$filename}");

        Browsershot::html($html)
            ->setOption('executablePath', 'C:\Program Files\Google\Chrome\Application\chrome.exe')
            ->margins(5, 5, 5, 5)
            ->format('A4')
            ->waitUntilNetworkIdle()
            ->showBackground()
            ->save($pdfPath);

        return $pdfPath;
    }
}
