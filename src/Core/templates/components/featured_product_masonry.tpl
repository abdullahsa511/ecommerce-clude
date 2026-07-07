#feature-products | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $featured_products = $current_component = $this->_component['featureproductsmasonry']?? [];

    // echo '<pre>';
    // print_r($featured_products);
    // echo '</pre>';
?>
[data-v-component-featureproductsmasonry] [data-v-featureproductsmasonry-*]|innerText = $featured_products['@@__data-v-featureproductsmasonry-(*)__@@']

.th-masonry-grid > div.th-masonry-grid-item | deleteAllButFirst
.th-masonry-grid | prepend = <?php if(isset($featured_products['items'])){ foreach ($featured_products['items'] as $product) { ?>
[data-v-featureproductsmasonryitem-*] | innertHTML = $product['@@__data-v-featureproductsmasonryitem-(*)__@@']
img[data-v-featureproductsmasonryitem-img] | src = <?php echo isset($product["img"]) ? $product["img"] : ''; ?>
div[data-v-featureproductsmasonryitem-class] | class = <?php echo isset($product["class"]) ? $product["class"] : ''; ?>
h6[data-v-featureproductsmasonryitem-heading] | innerHTML = <?php echo isset($product["heading"]) ? $product["heading"] : ''; ?>
p[data-v-featureproductsmasonryitem-des] | innerHTML = <?php echo isset($product["des"]) ? $product["des"] : ''; ?>
a[data-v-featureproductsmasonryitem-link] | href = <?php echo isset($product["link"]) ? $product["link"] : ''; ?>
.th-masonry-grid | append = <?php }} ?>