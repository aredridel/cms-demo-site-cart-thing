- Make a cart that stores all the items in the add to cart button in a CMS
- keep all state client side, in local storage
- Make checkout verify the cart against a spreadsheet of products and
  prices
- Look up tax also in a spreadsheet or maybe a SQLite db
- Checkout with Stripe I guess, so the total server-side private state is
  an AWS key or something for sending the ePub files, and a Stripe key for
  collecting payments. Actual record of sales goes in email and Stripe.
