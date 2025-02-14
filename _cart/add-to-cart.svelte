<script lang="ts">
    import {items} from "./cart.svelte.js"
    let { name, amount } = $props();

    function add_to_cart() {
    items.push({name, amount})
    }
</script>

<svelte:options customElement="add-to-cart"/>

<button onclick={add_to_cart}>Add to Cart</button>
