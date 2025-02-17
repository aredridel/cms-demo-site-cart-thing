<?php

namespace Checkout;

// Test stripe key
define("STRIPE_KEY", "X0BcFWl8qK5BlHQul6mwtchVs6DsyDi4");
// openssl rand -base64 32
define("JWT_KEY", "Dlm3TfvU8aOam3/Qf7U9H3UBx3vJ0eyuo+nkCmmFY2M=");
// Me!
define("OWNER_EMAIL", "aredridel@dinhe.net");
define("SUCCESS_PAGE", "/download");

require_once 'vendor/autoload.php';
require_once 'catalogue.php';
require_once 'stripe.php';
require_once 'util.php';

use DateTimeImmutable;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Parser;
use Lcobucci\JWT\Validation\Constraint\LooseValidAt;
use Lcobucci\JWT\Validation\Constraint\PermittedFor;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use Lcobucci\JWT\Validation\Validator;

$key = InMemory::base64Encoded(JWT_KEY);

$link = get_self_link();
$catalogue = load_catalogue("../ebooks.csv");
$clock = SystemClock::fromUTC();

if (is_string(@$_GET['success'])) {
    $parser = new Parser(new JoseEncoder());
    $token = $parser->parse($_GET['success']);
    $validator = new Validator();
    $validator->assert($token, new LooseValidAt($clock));
    $validator->assert($token, new PermittedFor("checkout"));

    $p = $token->claims()->get("p");
    $cart = json_decode(base64_decode($p));
    $id = $_GET["session_id"];
    $items = load_cart_from_catalogue($catalogue, $cart);

    $response = fetch_stripe("https://api.stripe.com/v1/checkout/sessions/$id");

    // Send email
    // FIXME
    $email = $response["customer_details"]["email"];

    $token = (new JwtFacade())->issue(
        new Sha256(),
        $key,
        static fn (
            Builder $builder,
            DateTimeImmutable $issuedAt
        ): Builder => $builder
            ->permittedFor('download')
            ->withClaim("p", $p)
            ->expiresAt($issuedAt->modify('+30 day'))
    );
    
    $d = [];
    foreach ($items as $item) {
        $pd = $item["price_data"]["product_data"];
        $d[] = [$pd["metadata"]["sku"], $pd["name"]];
    }
    $d = base64_encode(json_encode($d));

    // Show downloads
    header("HTTP/1.1 303 See Other");
    header ("Location: " . SUCCESS_PAGE . "?s={$token->toString()}&d=$d");
} else if (is_string(@$_GET["d"])) {
    // Downloads!
    $parser = new Parser(new JoseEncoder());
    $token = $parser->parse($_GET['key']);
    $validator = new Validator();
    $validator->assert($token, new LooseValidAt($clock));
    $validator->assert($token, new PermittedFor("download"));

    $p = $token->claims()->get("p");
    $d = $_GET["d"];

    $file = null;
    foreach ($catalogue as $book) {
        if ($book["sku"] == $d) {
            $file = $book["file"];
            break;
        }
    }
    if (!$file) {
        print "Book not found in catalogue";
        exit;
    }

    header("Content-Disposition: download; filename*={$file}");
    if (preg_match("/[.]epub$/", $file)) {
        header("Content-Type: application/epub+zip");
    } else if (preg_match("/[.]pdf$/", $file)) {
        header("Content-Type: application/pdf");
    } else {
        header("Content-Type: application/octet-stream");
    }

    // TODO: decrypt? Hidden URL?
    readfile($file);
} else if (is_string(@$_GET['p'])) {

    $p = $_GET['p'];

    $cart = json_decode(base64_decode($p));

    $items = load_cart_from_catalogue($catalogue, $cart);

    $token = (new JwtFacade())->issue(
        new Sha256(),
        $key,
        static fn (
            Builder $builder,
            DateTimeImmutable $issuedAt
        ): Builder => $builder
            ->permittedFor('checkout')
            ->withClaim("p", $p)
            ->expiresAt($issuedAt->modify('+1 day'))
    );

    $response = fetch_stripe("https://api.stripe.com/v1/checkout/sessions", [
        'mode' => 'payment',
        'automatic_tax' => [
            'enabled' => "true"
        ],
        'line_items' => $items,
        'success_url' => $link . "?success={$token->toString()}&session_id={CHECKOUT_SESSION_ID}",
        'cancel_url' => $link . "?cancel"
    ]);

    $id = $response['id'];
    $url = $response["url"];

    header("HTTP/1.1 303 See Other");
    header("Location: " . $url);
} else {
    print("Nothing in your cart");
    exit;
}
