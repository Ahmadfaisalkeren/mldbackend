<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Items Stickers Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .sticker-box {
            width: 250px;
            height: 150px;
            border: 2px dashed #ccc;
        }
    </style>
</head>

<body class="p-4">
    <div class="grid grid-cols-3 gap-2">
        @foreach ($items as $item)
            <div class="sticker-box p-2 flex flex-col justify-between">
                <div class="text-base text-center font-semibold">
                    {{ $item->name }}
                </div>

                <div class="flex justify-between items-end">
                    <div class="text-sm leading-tight">
                        <p><strong>Code:</strong> {{ $item->item_code }}</p>
                        <p><strong>Size:</strong> {{ $item->size }}</p>
                        <p><strong>Stock:</strong> {{ $item->stock }}</p>
                    </div>
                    <div class="ml-2">
                        <img src="{{ public_path('storage/' . $item->qrcode) }}" alt="QR Code"
                            class="w-20 h-20 object-contain">
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</body>

</html>
