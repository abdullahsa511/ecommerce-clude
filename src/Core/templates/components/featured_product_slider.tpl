#th-product-slider | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$featuredproductslider = $current_component = $this->_component['featuredproductslider']?? [];

	// echo '<pre>';
	// print_r($featuredproductslider);
	// echo '</pre>';
?>
[data-v-component-featuredproductslider] [data-v-featuredproductslider-*]|innerText = $featuredproductslider['@@__data-v-featuredproductslider-(*)__@@']

.th-featured-products-slider > div.swiper-wrapper > .swiper-slide | deleteAllButFirst
.th-featured-products-slider > div.swiper-wrapper | prepend = <?php if(isset($featuredproductslider['items'])){ foreach ($featuredproductslider['items'] as $item) { ?>
h3[data-v-featuredproductslideritem-name] | innerText = <?php echo $item['name']??""; ?>
img[data-v-featuredproductslideritem-image] | src = <?php echo $item['image']??""; ?>

[data-v-component-featuredproductslider] .th-tag-name > .th-tag | deleteAllButFirst
[data-v-component-featuredproductslider] .th-tag-name | prepend = <?php if(isset($item['tags'])){ foreach ($item['tags']as $tag) { ?>
div[data-v-featuredproductslideritemtag-name] | innerHTML = <?php echo $tag['name']??""; ?>
[data-v-component-featuredproductslider] .th-tag-name | append = <?php }} ?>


.th-featured-products-slider > div.swiper-wrapper | append = <?php }} ?>