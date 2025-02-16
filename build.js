import esbuild from "esbuild";
import esbuildSvelte from "esbuild-svelte";
import { sveltePreprocess } from "svelte-preprocess";

const ctx = await esbuild.context({
  entryPoints: ["_cart/cart.js"],
  mainFields: ["svelte", "browser", "module", "main"],
  conditions: ["svelte", "browser"],
  bundle: true,
  outfile: "_site/cart.js",
  plugins: [
    esbuildSvelte({
      compilerOptions: {
        customElement: true,
      },
      preprocess: sveltePreprocess(),
    }),
  ],
});

await ctx.watch();
