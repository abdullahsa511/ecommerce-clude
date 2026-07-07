import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])

#category-hero | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$category_hero = $current_component = $this->_component['categoryhero']?? [];
	// echo '<pre>';
	// print_r($category_hero);
	// echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-category-hero-edit-link] a | href = <?php echo isset($category_hero['edit_link']) ? $category_hero['edit_link'] : ''; ?>
[data-v-component-categoryhero] [data-v-categoryhero-component-link] a | href = <?php echo isset($category_hero['component_link']) ? $category_hero['component_link'] : ''; ?>

ol[data-v-category-hero-breadcrumbs] > li[data-v-category-hero-breadcrumb-list] | deleteAllButFirst
ol[data-v-category-hero-breadcrumbs] | prepend = <?php if(isset($category_hero['breadcrumbs'])){ foreach ($category_hero['breadcrumbs'] as $key => $breadcrumb) { ?>
    li[data-v-category-hero-breadcrumb-list] > a[data-v-category-hero-breadcrumb-link] | innerText = <?php echo isset($breadcrumb['name']) ? $breadcrumb['name'] : ''; ?>
    li[data-v-category-hero-breadcrumb-list] > a[data-v-category-hero-breadcrumb-link] | href = <?php echo isset($breadcrumb['link']) ? $breadcrumb['link'] : ''; ?>
ol[data-v-category-hero-breadcrumbs] | append = <?php } } ?>

span[data-v-category-hero-title] | innerText = <?php echo $category_hero['title']??""; ?>
div[data-v-category-hero-image] | data-bg-src = <?php echo $category_hero['image']??""; ?>
span[data-v-category-hero-subtitle] | innerText = <?php echo $category_hero['subtitle']??""; ?>
a[data-v-category-hero-categories-link] | href = <?php echo $category_hero['link']??""; ?>
h4[data-v-category-hero-categories-title] | class = <?php echo isset($category_hero['active']) && $category_hero['active']?"active th-hero-category th-link":"th-hero-category th-link"; ?>
h4[data-v-category-hero-categories-title] | innerText = <?php echo ucwords(strtolower($category_hero['title']??"")); ?>
span[data-v-category-hero-categories-title-mobile] | innerText = <?php echo ucwords(strtolower($category_hero['title']??"")); ?>

ul[data-v-category-hero-subcategories-mobile] > li | deleteAllButFirst
ul[data-v-category-hero-subcategories-mobile] | prepend = <?php if(isset($category_hero['categories'])){ foreach ($category_hero['categories'] as $category) { ?>
ul[data-v-category-hero-subcategories-mobile] > li | class = <?php echo isset($category['active']) && $category['active']?"active th-hero-subcategory th-link":"th-hero-subcategory th-link"; ?>
ul[data-v-category-hero-subcategories-mobile] > li > a | innerText = <?php echo $category['name']??""; ?>
ul[data-v-category-hero-subcategories-mobile] > li > a | href = <?php echo $category['link']??""; ?>
ul[data-v-category-hero-subcategories-mobile] | append = <?php }} ?>


ul[data-v-category-hero-subcategories] > li | deleteAllButFirst
ul[data-v-category-hero-subcategories] | prepend = <?php if(isset($category_hero['categories'])){ foreach ($category_hero['categories'] as $category) { ?>
ul[data-v-category-hero-subcategories] > li | class = <?php echo isset($category['active']) && $category['active']?"active th-hero-subcategory th-link":"th-hero-subcategory th-link"; ?>
ul[data-v-category-hero-subcategories] > li > a | innerText = <?php echo $category['name']??""; ?>
ul[data-v-category-hero-subcategories] > li > a | href = <?php echo $category['link']??""; ?>
ul[data-v-category-hero-subcategories] | append = <?php }} ?>

[data-v-category-hero-waypoints] | deleteAllButFirst
[data-v-category-hero-waypoints] | prepend = <?php if(isset($category_hero['way_points'])){ foreach ($category_hero['way_points'] as $item) { ?>
div[data-v-category-hero-waypoint] | id = <?php echo isset($item["id"]) ? 'way-point-'. $item["id"] : ''; ?>
div[data-v-category-hero-waypoint] | style = <?php echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : ''; ?>
a[data-v-category-hero-waypoint-link] | innerText = <?php echo isset($item["label"]) ? $item["label"] : ''; ?>
a[data-v-category-hero-waypoint-link] | href = <?php echo isset($item["href"]) ? trim($item["href"]) : ''; ?>
a[data-v-category-hero-waypoint-link] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
[data-v-category-hero-waypoints] | append = <?php }} ?>

div[data-v-category-hero-edit-link] | if_exists = $is_admin
div[data-v-categoryhero-component-link] | if_exists = $is_admin


#th-products-list | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $productlist = $current_component = $this->_component['productlist']?? [];

    // echo '<pre>';
	// print_r($productlist);
	// echo '</pre>';
?>

[data-v-component-productlist] [data-v-productlist-*]|innerText = $productlist['@@__data-v-productlist-(*)__@@']

h2[data-v-productlist-section_title] | innerText = <?php echo $productlist['section_title']??""; ?>
div[data-v-productlist-section_subtitle] | innerText = <?php echo $productlist['section_subtitle']??""; ?>
div.th-products-list-wrapper > div.th-item-product | deleteAllButFirst
div.th-products-list-wrapper | prepend = <?php if(isset($productlist['items'])){ foreach ($productlist['items'] as $item) { ?>
    img[data-v-productlistitem-image] | src = <?php echo $item['image']??""; ?>
    div[data-v-productlistitem-add-to-pinboard] | data-id = <?php echo $item['id']??""; ?>
    div[data-v-productlistitem-add-to-pinboard] | data-model = product
    div[data-v-productlistitem-add-to-pinboard] | data-title = <?php echo $item['name']??""; ?>
    div[data-v-productlistitem-add-to-pinboard] | data-description = <?php echo $item['description']??""; ?>
    div[data-v-productlistitem-add-to-pinboard] | data-image = <?php echo $item['image']??""; ?>
    div[data-v-productlistitem-add-to-pinboard] | data-product-url = <?php echo isset($item['product_url']) ? $item['product_url'] : ''; ?>
    h3[data-v-productlistitem-name] | innerText = <?php echo $item['name']??""; ?>
    a[data-v-productlistitem-link] | href = <?php echo $item['href']??""; ?>
    p[data-v-productlistitem-description] | innerHTML = <?php echo $item['description']??""; ?>

    .th-tag-name > .th-tag | deleteAllButFirst
    .th-tag-name | prepend = <?php if(isset($item['tags'])){ foreach ($item['tags'] as $tag) { ?>
        div[data-v-productlistitemtag-name] | innerHTML = <?php echo $tag??""; ?>
    .th-tag-name | append = <?php }} ?>

    .th-item-finish-circle > .th-circle | deleteAllButFirst
    .th-item-finish-circle | prepend = <?php if(isset($item['finishes'])){ foreach ($item['finishes'] as $finish) { ?>
        div[data-v-productlistitemfinish-name] | innerHTML = <?php echo $finish['name']??""; ?>
        div[data-v-productlistitemfinish-color] | class = <?php echo $finish['color']??""; ?>
        div[data-v-productlistitemfinish-img] | data-bg-src = <?php echo $finish['img']??""; ?>
    .th-item-finish-circle | append = <?php }} ?>

div.th-products-list-wrapper | append = <?php }} ?>

import(components/footer.tpl, [data-v-component-footer])