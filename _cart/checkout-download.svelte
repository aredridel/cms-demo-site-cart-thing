<script lang="ts">
    const params = new URLSearchParams(location.search);
    const key = params.get("s");
    const items = JSON.parse(atob(params.get("d")));

    console.log(items);
</script>

<svelte:options customElement="checkout-download" />

<p>Your downloads: </p>

<ul>
    {#each items as item}
        {@const [sku, name] = item}
        <li><a href="checkout.phar.php?d={sku}&key={key}" download>{name}</a></li>
    {/each}
</ul>
