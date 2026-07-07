import(components/header.tpl, [data-v-component-header])

#th-products-list | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $productlist = $current_component = $this->_component['productlist']?? [];

    // echo '<pre>';
	// print_r($productlist);
	// echo '</pre>';
?>

[data-v-component-productlist] [data-v-productlist-*]|innerText = $productlist['@@__data-v-productlist-(*)__@@']

div.th-products-list-wrapper > div.th-item-product | deleteAllButFirst
div.th-products-list-wrapper | prepend = <?php if(isset($productlist['items'])){ foreach ($productlist['items'] as $item) { ?>
    img[data-v-productlistitem-image] | src = <?php echo $item['image']??""; ?>
    h3[data-v-productlistitem-name] | innerText = <?php echo $item['title']??""; ?>
    p[data-v-productlistitem-description] | innerHTML = <?php echo $item['description']??""; ?>

    .th-tag-name > .th-tag | deleteAllButFirst
    .th-tag-name | prepend = <?php if(isset($item['tags'])){ foreach ($item['tags'] as $tag) { ?>
        div[data-v-productlistitemtag-name] | innerHTML = <?php echo $tag['name']??""; ?>
    .th-tag-name | append = <?php }} ?>

    .th-item-finish-circle > .th-circle | deleteAllButFirst
    .th-item-finish-circle | prepend = <?php if(isset($item['finishes'])){ foreach ($item['finishes'] as $finish) { ?>
        div[data-v-productlistitemfinish-name] | innerHTML = <?php echo $finish['name']??""; ?>
        div[data-v-productlistitemfinish-color] | class = <?php echo $finish['color']??""; ?>
        div[data-v-productlistitemfinish-img] | data-bg-src = <?php echo $finish['img']??""; ?>
    .th-item-finish-circle | append = <?php }} ?>

div.th-products-list-wrapper | append = <?php }} ?>

import(components/footer.tpl, [data-v-component-footer])