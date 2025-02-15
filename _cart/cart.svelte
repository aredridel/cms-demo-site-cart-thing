<svelte:options customElement="shopping-cart" />

<script lang="ts">
  import { items, ui } from "./cart.svelte.js";

  function remove(item) {
    return function () {
      items.splice(items.indexOf(item), 1);
    };
  }
</script>

<div class="cart">
  <button onclick={() => (ui.open = !ui.open)}> CART! </button>
</div>

{#if ui.open}
  <div class="cart-open">
    <div class="header">
      Shopping Cart
      <button onclick={() => (ui.open = false)}>X</button>
    </div>
    {#if items.length}
      <table>
        <tbody>
          {#each items as item}
            <tr>
              <td>{item.name}</td>
              <td>{item.amount}</td>
              <td><button onclick={remove(item)}>X</button></td>
            </tr>
          {/each}
        </tbody>
      </table>
      <button>Checkout</button>
    {/if}
  </div>
{/if}

<style>
  .cart-open {
    position: fixed;
    top: 0;
    right: 0;
    min-width: 150px;
    max-width: min(70%, 450px);
    background-color: lightgrey;
    height: 100vh;
    height: 100svh;
    padding: 1em;
  }
  .header {
    display: flex;
    flex-direction: row;
    align-items: space-between;
    button {
      margin-inline-start: auto;
    }
  }
</style>
