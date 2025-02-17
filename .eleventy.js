export default async function config(eleventyConfig) {
  // Merge data instead of overriding
  // https://www.11ty.dev/docs/data-deep-merge/
  eleventyConfig.setDataDeepMerge(true);

  // Add support for post authors
  eleventyConfig.addCollection("myAuthors", collection => {
    const blogs = collection.getFilteredByGlob("posts/*.md");
    return blogs.reduce((coll, post) => {
      const author = post.data.author;
      if (!author) {
        return coll;
      }
      if (!coll.hasOwnProperty(author)) {
        coll[author] = [];
      }
      coll[author].push(post);
      return coll;
    }, {});
  });

  // Don't process folders with static assets e.g. images
  eleventyConfig.addPassthroughCopy("assets/img"); 
  eleventyConfig.addPassthroughCopy("admin/");
  eleventyConfig.addPassthroughCopy("assets/style.css");
  eleventyConfig.addPassthroughCopy("checkout.phar.php");
  eleventyConfig.addPassthroughCopy("*.csv");

  eleventyConfig.ignores.add("_cart");
  eleventyConfig.ignores.add("checkout");
  eleventyConfig.ignores.add("*.epub");

  // Disable 11ty development server live reload when using the CMS locally.
  eleventyConfig.setServerOptions({
    liveReload: false
  });

  return {
    templateFormats: ["md", "njk", "liquid"],

    // If your site lives in a different sub-directory, change this.
    // Leading or trailing slashes are all normalized away, so don’t worry about it.
    // If you don’t have a sub-directory, use "" or "/" (they do the same thing)
    // This is only used for URLs (it does not affect your file structure)
    pathPrefix: "/",

    markdownTemplateEngine: "liquid",
    htmlTemplateEngine: "njk",
    dataTemplateEngine: "njk",
    dir: {
      input: ".",
      includes: "_includes",
      data: "_data",
      output: "_site"
    }
  };
};

