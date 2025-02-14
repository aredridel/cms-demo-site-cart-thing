<script lang="ts">
    import {items} from "./cart.svelte.js"
	let open = $state(false);
</script>

<svelte:options customElement="shopping-cart" />

<style>
	.cart-open {
		position: fixed;
		top: 0;
		right: 0; 
		width: 150px;
		background-color: lightgrey;
		height: 100vh;
		height: 100svh;
	}
</style>

<div class='cart'>
	<button onclick={() => open = !open}>
	CART!
	</button>
</div>

{#if open}
	<div class="cart-open">
		<button onclick={() => open = false}>X</button>
		Cart
		{#if items.length}
			<table>
				<tbody>
					{#each items as item}
						<tr><td>{item.name}</td><td>{item.amount}</td></tr>	
					{/each}
				</tbody>
			</table>
			<button>Checkout</button>
		{/if}
	</div>
{/if}
