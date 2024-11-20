<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Items Report</title>
    @vite('resources/css/app.css')
</head>

<body>
    <h1 class="text-2xl font-bold text-center">Items Report</h1>
    <table class="table-auto w-full mt-4">
        <thead>
            <tr>
                <th class="px-4 py-2">Item Code</th>
                <th class="px-4 py-2">Name</th>
                <th class="px-4 py-2">Category</th>
                <th class="px-4 py-2">Image</th>
                <th class="px-4 py-2">QR Code</th>
                <th class="px-4 py-2">Quantity</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($items as $item)
                <tr>
                    <td class="border px-4 py-2">{{ $item->item_code }}</td>
                    <td class="border px-4 py-2">{{ $item->name }}</td>
                    <td class="border px-4 py-2">{{ $item->category->category_name }}</td>
                    <td class="border px-4 py-2">
                        <img src="{{ public_path('storage/' . $item->image) }}" alt="Item Image"
                            class="w-20 h-20 object-cover">
                    </td>
                    <td class="border px-4 py-2">
                        <img src="{{ public_path('storage/' . $item->qrcode) }}" alt="Item Image"
                            class="w-20 h-20 object-cover">
                    </td>
                    <td class="border px-4 py-2">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
