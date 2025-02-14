import esbuild from "esbuild";
import esbuildSvelte from "esbuild-svelte";
import {sveltePreprocess} from "svelte-preprocess";

esbuild
  .build({
    entryPoints: ["_cart/cart.js"],
    mainFields: ["svelte", "browser", "module", "main"],
    conditions: ["svelte", "browser"],
    bundle: true,
    outfile: "cart/cart.js",
    plugins: [
      esbuildSvelte({
        compilerOptions: {
          customElement: true
        },
        preprocess: sveltePreprocess(),
      }),
    ],
  })
  .catch(() => process.exit(1));
