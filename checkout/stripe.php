<?php

function fetch_stripe($url, $data = null) {
    $response = fetch($url, [
        "method" => $data ? "POST" : "GET",
        "headers" => [
            "Authorization" => "Basic " . base64_encode(STRIPE_KEY . ":")
        ],
        "body" => $data ? http_build_query($data) : null
    ]);

    if (!$response->ok()) throw new Error("Not ok: " . $response->text());

    return $response->json();
}

