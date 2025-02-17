<?php

namespace Checkout;

function load_catalogue($file) {
    // Load catalogue
    $catalogue = [];
    $csv = fopen($file, "r");
    $header = null;
    while ($row = fgetcsv($csv, 0, ",", '"' ,"")) {
        $addRow = [];
        if ($header) {
            foreach ($header as $n => $h) {
                $addRow[strtolower($h)] = @$row[$n] ?: "";
            }
            $catalogue[] = $addRow;
        } else {
            $header = $row;
        }
    }
    return $catalogue;
}

function load_cart_from_catalogue($catalogue, $cart) {
    $items = [];
    foreach ($cart as $product) {
        [$sku, $price] = $product;
        $found = false;
        foreach ($catalogue as $entry) {
            if ($sku == $entry['sku']) {
                if ($price == $entry['price']) {
                    $items[] = [
                        'price_data' => [
                            'currency' => 'USD',
                            'product_data' => [
                                'metadata' => ['sku' => $sku],
                                'tax_code' => 'txcd_10302000', # ebook, permanent rights
                                'name' => $entry['title']
                            ],
                            'unit_amount' => $price * 100
                        ],
                        'quantity' => 1
                    ];
                    $found = true;
                } else {
                    print("Price in cart ($price) does not match catalogue ({$entry['price']}) for $sku");
                    exit;
                }
            }
        }
        if (!$found) {
            print("Item '$sku' not in catalogue");
            exit;
        }
    }

    return $items;
}
