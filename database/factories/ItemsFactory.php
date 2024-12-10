<?php

namespace Database\Factories;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Items>
 */
class ItemsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Generate a unique item code
        $count = $this->faker->unique()->numberBetween(1, 1000); // Adjust range as needed
        $formattedNumber = str_pad($count, 5, '0', STR_PAD_LEFT);
        $itemCode = 'MLD-' . $formattedNumber;

        // Generate and store a QR code based on the item code
        $qrCodePath = $this->generateAndStoreQRCode($itemCode);

        return [
            'item_code' => $itemCode,
            'name' => $this->faker->word(),
            'category_id' => $this->faker->numberBetween(1, 3), // Assuming categories exist
            'description' => $this->faker->sentence(),
            'size' => $this->faker->numberBetween(40, 43),
            'image' => $this->generateDummyImage(),
            'qrcode' => $qrCodePath,
            'quantity' => $this->faker->numberBetween(1, 100),
        ];
    }

    private function generateAndStoreQRCode($itemCode)
    {
        // Generate QR code content
        $qrCodeImage = QrCode::format('svg')->size(100)->generate($itemCode);

        // Save the QR code to the storage
        $path = 'qrcodes/' . $itemCode . '.svg';
        Storage::disk('public')->put($path, $qrCodeImage);

        return $path;
    }

    private function generateDummyImage()
    {
        // Generate a unique image name using time and a random word from Faker
        $imageName = time() . '-' . $this->faker->unique()->word() . '.jpeg';

        // Define the path where the image will be stored
        $imagePath = 'images/items/' . $imageName;

        // Use Storage to save a placeholder image from a URL
        $imageUrl = 'https://picsum.photos/150'; // Placeholder image URL
        $imageContent = file_get_contents($imageUrl); // Fetch image content

        // Store the image in the 'public' disk
        Storage::disk('public')->put($imagePath, $imageContent);

        // Return the path to the stored image
        return $imagePath;
    }
}
