<svelte:options customElement="add-to-cart" />

<script lang="ts">
  import { items, ui } from "./cart.svelte.js";
  let { name, amount, sku } = $props();

  let item_in_cart = $derived(items.find((e) => e.sku == sku));

  function add_to_cart() {
    if (item_in_cart) {
    ui.open = true;
    } else {
    items.push({ sku, name, amount });
    }
  }
</script>

<button onclick={add_to_cart}>
  {#if item_in_cart}
    In Your Cart
  {:else}
    Add to Cart
  {/if}
</button>
