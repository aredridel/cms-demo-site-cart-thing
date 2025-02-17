export interface Item {
  sku: string;
  amount: number;
  name: string;
}

function initialState(): Item[] {
  const stored = localStorage.getItem("cart");
  return stored ? JSON.parse(stored): []; 
}

export const items = $state(initialState());

$effect.root(() => {
  $effect(() => {
    localStorage.setItem("cart", JSON.stringify(items));
  })
});

export let ui = $state({
  open: false
});
