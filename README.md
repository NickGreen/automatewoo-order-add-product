# AutomateWoo Order Action Add Free Product

Extends the functionality of AutomateWoo with a custom action which allows you to add a free product to an order as a line item.

## Intended use

This action is intended for downstream use, such as adding a product to the order after it is created, but before it gets shipped, and the customer doesn't need to be aware of it while checking out.

This is because the product doesn't get added to the order until after it has been created, so therefore doesn't show on the checkout page or the order confirmation page. This means you can't use it as a replacement for Force Sells, for example, which adds products to the cart before checkout.

You will want to test that the product is added to your orders in a timely enough manner for your purposes.

Also, note that any product added will be added as a free product (price of $0), since it doesn't make sense to add a cost to the order _after_ it's been paid for.

## Support

This plugin is provided without any support or guarantees of functionality. If you'd like to contribute, feel free to open a PR on this repo.
