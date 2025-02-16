<?php

define("STRIPE_KEY", "X0BcFWl8qK5BlHQul6mwtchVs6DsyDi4");

if (!is_string(@$_GET['p'])) {
    print("Nothing in your cart");
    exit;
}

// Program to display URL of current page.
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $link = "https";
else
    $link = "http";

// Here append the common URL characters.
$link .= "://";

// Append the host(domain name, ip) to the URL.
$link .= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL
$link .= $_SERVER['REQUEST_URI'];

// Load catalogue
// Look up $GET['p'] each in it, make line items
//
$catalogue = [];
$csv = fopen("ebooks.csv", "r");
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

$items = [];
foreach (preg_split("/@@/", $_GET['p']) as $product) {
    [$sku, $price] = preg_split("@//@", $product); 
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
        print("Item $sku not in catalogue");
        exit;
    }
}

require_once './vendor/autoload.php';

\Stripe\Stripe::setApiKey(STRIPE_KEY);

$checkout_session = \Stripe\Checkout\Session::create([
    'automatic_tax' => [
        'enabled' => true
    ],
    'line_items' => $items,
    'mode' => 'payment',
    'success_url' => $link . "?success",
    'cancel_url' => $link . "?cancel"
]);

header("HTTP/1.1 303 See Other");
header("Location: " . $checkout_session->url);
