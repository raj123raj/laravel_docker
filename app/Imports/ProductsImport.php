<?php
namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProductsImport implements ToCollection
{
    public array $badRows = [];

    public function collection(Collection $rows)
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $index => $row) {
                // Skip header row
                if ($index === 0) continue;
                
                $sku = trim($row[0]);
                $stock = (int) $row[2];
                
                // Test invalid rows
                if (!is_numeric($stock) || $stock < 0) {
                    $this->badRows[] = "Row " . ($index+1) . ": Invalid stock '{$row[2]}'";
                    continue;
                }
                
                // Update or create (handles duplicate SKU gracefully)
                Product::updateOrCreate(
                    ['sku' => $sku],
                    [
                        'name' => $row[1],
                        'stock' => $stock,
                        'price' => (float) $row[3]
                    ]
                );
            }
        });
    }
}
