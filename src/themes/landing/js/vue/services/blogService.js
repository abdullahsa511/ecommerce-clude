/**
 * Simple Blog Service (mock)
 * Returns paginated mock posts for the blog post list.
 */
const BlogService = {
    // Simulate a total of 3 pages of data
    async fetchPosts(page = 1, perPage = 6) {
        // simulate network delay
        await new Promise((r) => setTimeout(r, 300));

        const totalItems = 18;
        const start = (page - 1) * perPage + 1;
        const end = Math.min(start + perPage - 1, totalItems);

        if (start > totalItems) return [];

        const posts = [];
        for (let i = start; i <= end; i++) {
            posts.push({
                id: i,
                title: `Blog Post Title ${i}`,
                excerpt: `This is a short excerpt for blog post ${i}.`,
                image: `/img/blog-page/News ${((i - 1) % 6) + 1}.png`,
                slug: `blog-post-${i}`
            });
        }

        return posts;
    }
};

export default BlogService;
