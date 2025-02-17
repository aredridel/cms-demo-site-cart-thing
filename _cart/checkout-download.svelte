<script lang="ts">
    import { items } from "./cart.svelte.ts";
    const params = new URLSearchParams(location.search);
    const key = params.get("s");
    const downloads = JSON.parse(atob(params.get("d")));

    for (const [sku,] of downloads) {
        const idx = items.findIndex(i => i.sku == sku);
        if (idx >= 0) {
            items.splice(idx, 1);
        }
    }
</script>

<svelte:options customElement="checkout-download" />

<p>Your downloads: </p>

<ul>
    {#each downloads as item}
        {@const [sku, name] = item}
        <li><a href="checkout.phar.php?d={sku}&key={key}" download>{name}</a></li>
    {/each}
</ul>
