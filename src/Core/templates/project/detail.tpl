import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#th-blog-main | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$blogmain = $current_component = $this->_component['blogmain']?? [];

	// echo '<pre>';
	// print_r($blogmain);
	// echo '</pre>';
?>
[data-v-component-blogmain] [data-v-blogmain-*]|innerText = $blogmain['@@__data-v-blogmain-(*)__@@']
[data-v-component-blogmain] [data-v-blogmain-img]|src = <?php echo $blogmain['img']; ?>


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

div[data-v-featuredproductslideritem-tags] > .th-tag | deleteAllButFirst
div[data-v-featuredproductslideritem-tags] | prepend = <?php if(isset($item['tags'])){ foreach ($item['tags']as $tag) { ?>
div[data-v-featuredproductslideritem-tags] > .th-tag | innerText = <?php echo $tag['name']??""; ?>
div[data-v-featuredproductslideritem-tags] | append = <?php }} ?>

div[data-v-featuredproductslideritem-finishes] > .th-circle | deleteAllButFirst
div[data-v-featuredproductslideritem-finishes] | prepend = <?php if(isset($item['finishes'])){ foreach ($item['finishes'] as $finish) { ?>
div[data-v-featuredproductslideritem-finishes] > .th-circle | innerText = <?php echo $finish['name']??""; ?>
div[data-v-featuredproductslideritem-finishes] > .th-circle | class = <?php echo "th-circle "; echo $finish['color']??""; ?>
div[data-v-featuredproductslideritem-finishes] | append = <?php }} ?>

.th-featured-products-slider > div.swiper-wrapper | append = <?php }} ?>


#th-featured-material | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$featuredmaterialslider = $current_component = $this->_component['featuredmaterialslider']?? [];

	// echo '<pre>';
	// print_r($featuredmaterialslider);
	// echo '</pre>';
?>
[data-v-component-featuredmaterialslider] [data-v-featuredmaterialslider-*]|innerText = $featuredmaterialslider['@@__data-v-featuredmaterialslider-(*)__@@']

.th-featured-material-slider > .swiper-wrapper > div.swiper-slide | deleteAllButFirst
.th-featured-material-slider > .swiper-wrapper| prepend = <?php if(isset($featuredmaterialslider['items'])){ foreach ($featuredmaterialslider['items'] as $materialItem) { ?>
img[data-v-featuredmaterialslideritem-image] | src = <?php echo isset($materialItem["image"]) ? $materialItem["image"] : ''; ?>
p[data-v-featuredmaterialslideritem-category] | innerHTML = <?php echo isset($materialItem["category"]) ? $materialItem["category"] : ''; ?>
h3[data-v-featuredmaterialslideritem-name] | innerHTML = <?php echo isset($materialItem["name"]) ? $materialItem["name"] : ''; ?>
span[data-v-featuredmaterialslideritem-description] | innerHTML = <?php echo isset($materialItem["description"]) ? $materialItem["description"] : ''; ?>
.th-featured-material-slider > .swiper-wrapper | append = <?php }} ?>



import(components/footer.tpl, [data-v-component-footer])