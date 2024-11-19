<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Items\StoreItemRequest;
use App\Http\Requests\Items\UpdateItemRequest;
use App\Models\Items;
use App\Services\ItemsService;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    protected $itemsService;

    public function __construct(ItemsService $itemsService)
    {
        $this->itemsService = $itemsService;
    }

    public function index()
    {
        $items = $this->itemsService->getItems();

        return response()->json([
            'status' => 200,
            'message' => 'Items Fetched Successfully',
            'items' => $items,
        ], 200);
    }

    public function totalItems()
    {
        $totalItems = $this->itemsService->getTotalItems();

        return response()->json([
            'status' => 200,
            'message' => 'Total Items Fetched Successfully',
            'totalItems' => $totalItems,
        ], 200);
    }


    public function store(StoreItemRequest $request)
    {
        $item = $this->itemsService->storeItem($request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Store Item Successfully',
            'item' => $item,
        ], 200);
    }

    public function edit($id)
    {
        $item = $this->itemsService->getItemById($id);

        return response()->json([
            'status' => 200,
            'message' => 'Item Fetched Successfully',
            'item' => $item,
        ], 200);
    }

    public function update(UpdateItemRequest $request, $id)
    {
        $item = $this->itemsService->getItemById($id);

        $updateItem = $this->itemsService->updateItem($item, $request->validated());

        return response()->json([
            'status' => 200,
            'message' => 'Update Item Successfully',
            'item' => $updateItem,
        ], 200);
    }

    public function destroy($id)
    {
        $this->itemsService->deleteItem($id);

        return response()->json([
            'status' => 200,
            'message' => 'Item Deleted Successfully',
        ], 200);
    }

    public function generateItemsPDF()
    {
        $pdfPath = $this->itemsService->generateItemsPDF();

        return response()->file($pdfPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="items_report.pdf"',
        ]);
    }
}
