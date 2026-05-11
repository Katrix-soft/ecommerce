<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$family = App\Models\Family::find(1);
$product = App\Models\Product::whereHas('subcategory.category', function($q) use($family) { 
    $q->where('family_id', $family->id); 
})->first();

if($product){ 
    // Attach some features. 
    // Wait, the pivot table is option_product. 
    // BUT the Option model says: ->withPivot('feature_id').
    // Let's attach option_id 1 (Talla) and option_id 2 (Color)
    // Wait, pivot table option_product needs option_id, product_id, and feature_id.
    // Let's fetch some features
    $features = App\Models\Feature::whereIn('option_id', [1, 2])->get();
    
    foreach ($features as $feature) {
        $product->options()->attach($feature->option_id, [
            'feature_id' => $feature->id
        ]);
    }
    
    echo "Options attached successfully to product " . $product->id; 
} else { 
    echo "No product found for family 1"; 
}
