#th-showroom-section-products | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$showroomsectionproducts = $current_component = $this->_component['showroomsectionproducts']?? [];
//   Here $this is the View class

	// echo '<pre>';
	// print_r($showroomsectionproducts);
	// echo '</pre>';
?>


<!-- div[data-v-showroomsection-products] div[data-v-showroomsection-product] | deleteAllButFirst -->

div#th-showroom-section-products > div.th-showroom-details-body div.th-showroom-product-item| deleteAllButFirst

div[data-v-showroomsection-products] | prepend = <?php if(isset($showroomsectionproducts['products'])){ foreach ($showroomsectionproducts['products'] as $item) { ?>

	img[data-v-showroomsection-product-image] | src = <?php echo $item['image']??''; ?>
	h3[data-v-showroomsection-product-name] | innerHTML = <?php echo $item['name']??''; ?>

	div[data-v-showroomsection-product-tags] a[data-v-showroomsection-product-tag] | deleteAllButFirst
	div[data-v-showroomsection-product-tags] | prepend = <?php if(isset($item['tags'])){ foreach ($item['tags'] as $tag) { ?>
		a[data-v-showroomsection-product-tag] innerHTML = <?php echo $tag??''; ?>
	div[data-v-showroomsection-product-tags] | append = <?php }} ?>

	span[data-v-showroomsection-product-description] | innerHTML = <?php echo $item['description']??''; ?>
	img[data-v-showroomsection-product-fabric-img] | src = <?php echo $item['fabric_image']??''; ?>

div[data-v-showroomsection-products] | append = <?php }} ?>

