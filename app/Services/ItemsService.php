<?php

namespace App\Services;

use App\Models\Items;
use SimpleSoftwareIO\QrCode\Facades\QrCode; // Import the QR Code package
use Illuminate\Support\Facades\Storage;
use Spatie\Browsershot\Browsershot;

/**
 * Class ItemsService.
 */
class ItemsService
{
    public function getItems()
    {
        $items = Items::with('category')->orderBy('created_at', 'desc')->get();
        return $items;
    }

    public function getTotalItems()
    {
        $totalItems = Items::count();
        return $totalItems;
    }

    public function storeItem(array $itemData)
    {
        if (isset($itemData['image'])) {
            $itemData['image'] = $this->storeImage($itemData['image']);
        }

        $itemData['item_code'] = $this->generateUniqueItemCode();

        // Generate the QR code based on the item code
        $qrCodePath = $this->generateAndStoreQRCode($itemData['item_code']);
        $itemData['qrcode'] = $qrCodePath;

        $item = Items::create($itemData);

        return $item;
    }

    private function generateAndStoreQRCode($itemCode)
    {
        $qrCodeImage = QrCode::format('svg')->size(100)->generate($itemCode);
        $path = 'qrcodes/' . $itemCode . '.svg';
        Storage::disk('public')->put($path, $qrCodeImage);

        return $path;
    }

    private function generateUniqueItemCode()
    {
        $count = Items::count() + 1;
        $formattedNumber = str_pad($count, 5, '0', STR_PAD_LEFT);
        return 'MLD-' . $formattedNumber;
    }

    private function storeImage($image)
    {
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $imagePath = $image->storeAs('images/items', $imageName, 'public');
        return $imagePath;
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
        $item->barcode = $itemData['barcode'] ?? $item->barcode;
        $item->quantity = $itemData['quantity'] ?? $item->quantity;

        if (isset($itemData['image'])) {
            $itemData['image'] = $this->updateImage($item, $itemData['image']);
        }

        $item->save();
        return $item;
    }

    private function updateImage(Items $item, $image)
    {
        if ($image && $image->isValid()) {
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('images/items', $imageName, 'public');

            if ($item->image) {
                Storage::disk('public')->delete($item->image);
            }

            $item->image = $imagePath;
        }
    }

    public function deleteItem($id)
    {
        $item = Items::findOrFail($id);
        $this->deleteImage($item->image);
        $item->delete();
    }

    private function deleteImage($imagePath)
    {
        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }
    }

    public function generateItemsPDF()
    {
        $items = $this->getItems();

        $html = view('items_pdf', compact('items'))->render();

        $pdfPath = storage_path('app/public/reports/items_report.pdf');

        Browsershot::html($html)
            ->setChromePath('/usr/bin/chromium-browser')
            ->noSandbox()
            ->newHeadless()
            ->setOption('format', 'A4')
            ->margins(10, 10, 10, 10)
            ->waitUntilNetworkIdle()
            ->setTimeout(3000)
            ->showBackground()
            ->noSandbox()
            ->enableImages()
            ->enableDebugging()
            ->showBrowserHeaderAndFooter()
            ->writeOptionsToFile()
            ->save($pdfPath);

        return $pdfPath;
    }
}
