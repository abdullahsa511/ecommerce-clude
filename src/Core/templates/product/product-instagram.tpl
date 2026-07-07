#th-product-instagram | before = <?php
    if (isset($current_component)) {
        $previous_component = $current_component;
    }
    $productInstagram = $current_component = $this->_component['productinstagram'] ?? [];
    $showProductInstagram = isset($productInstagram['items']) && is_array($productInstagram['items']) && count($productInstagram['items']) > 0;
    $productInstagramPostsJson = htmlspecialchars(
        json_encode(array_values($productInstagram['items'] ?? []), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ENT_QUOTES,
        'UTF-8'
    );
?>
section[data-v-component-productinstagram] | data-product-instagram-posts = <?php echo $productInstagramPostsJson; ?>
[data-v-component-productinstagram] h2[data-v-productinstagram-title] | innerText = <?php echo isset($productInstagram['title']) ? '# ' . ucfirst($productInstagram['title']) : '# Instagram'; ?>
div[data-v-productinstagram-items] > div.swiper-slide | deleteAllButFirst
[data-v-productinstagram-items] | prepend = <?php if (!empty($productInstagram['items']) && is_array($productInstagram['items'])) { foreach (array_values($productInstagram['items']) as $index => $item) { if (!is_array($item)) { continue; } ?>
    button[data-v-productinstagram-item-trigger] | data-productinstagram-index = <?php echo (int) $index; ?>
    div[data-v-productinstagram-item-image] | data-bg-src = <?php echo isset($item['thumbnail']) ? htmlspecialchars($item['thumbnail'], ENT_QUOTES, 'UTF-8') : ''; ?>
[data-v-productinstagram-items] | append = <?php } } ?>

section[data-v-component-productinstagram] | if_exists = $showProductInstagram
