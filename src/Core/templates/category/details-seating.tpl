import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#category-seating-hero | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$category_hero = $current_component = $this->_component['categoryhero']?? [];
	$titleClass = "text-decoration-none d-flex justify-content-between align-items-center h6 ";

	$isSubCategoriesExist = count($category_hero['categories']) > 0 ? true : false; 
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

[data-v-category-hero-edit-link] a | href = <?php echo isset($category_hero['edit_link']) ? $category_hero['edit_link'] : ''; ?>
[data-v-component-categoryhero] [data-v-categoryhero-component-link] a | href = <?php echo isset($category_hero['component_link']) ? $category_hero['component_link'] : ''; ?>


ol[data-v-category-hero-breadcrumbs] > li[data-v-category-hero-breadcrumb-list] | deleteAllButFirst
ol[data-v-category-hero-breadcrumbs] | prepend = <?php if(isset($category_hero['breadcrumbs'])){ foreach ($category_hero['breadcrumbs'] as $breadcrumb) { ?>
    li[data-v-category-hero-breadcrumb-list] > a[data-v-category-hero-breadcrumb-link] | innerText = <?php echo isset($breadcrumb['name']) ? $breadcrumb['name'] : ''; ?>
    li[data-v-category-hero-breadcrumb-list] > a[data-v-category-hero-breadcrumb-link] | href = <?php echo isset($breadcrumb['link']) ? $breadcrumb['link'] : ''; ?>
ol[data-v-category-hero-breadcrumbs] | append = <?php } } ?>


[data-v-category-hero-waypoints] | deleteAllButFirst
[data-v-category-hero-waypoints] | prepend = <?php if(isset($category_hero['way_points'])){ foreach ($category_hero['way_points'] as $item) { ?>
div[data-v-category-hero-waypoint] | id = <?php echo isset($item["id"]) ? 'way-point-'. $item["id"] : ''; ?>
div[data-v-category-hero-waypoint] | style = <?php echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : ''; ?>
a[data-v-category-hero-waypoint-link] | innerText = <?php echo isset($item["label"]) ? $item["label"] : ''; ?>
a[data-v-category-hero-waypoint-link] | href = <?php echo isset($item["href"]) ? trim($item["href"]) : ''; ?>
a[data-v-category-hero-waypoint-link] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
[data-v-category-hero-waypoints] | append = <?php }} ?>



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

div.th-hero-categories-list | if_exists = $isSubCategoriesExist

div[data-v-category-hero-edit-link] | if_exists = $is_admin
div[data-v-categoryhero-component-link] | if_exists = $is_admin


#category-seating-details | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$categories = $current_component = $this->_component['categoryseatingdetails']?? [];

	// echo '<pre>';
	// print_r($categories);
	// echo '</pre>';

?>

#category-seating-details > .th-section | deleteAllButFirst
#category-seating-details | prepend = <?php if(isset($categories['sections'])){ foreach ($categories['sections'] as $key => $section) { ?>
div[data-v-category-id] | id = <?php echo "th-product-slider-".$key; ?>
h2[data-v-category-title] | innerText = <?php echo $section['title']??""; ?>
div[data-v-category-subtitle] | innerText = <?php echo $section['subtitle']??""; ?>
a[data-v-category-link] | href = <?php echo $section['link']??""; ?>
div.swiper-wrapper > .swiper-slide | deleteAllButFirst
div.swiper-wrapper | prepend = <?php if(isset($section['items'])){ foreach ($section['items'] as $item) { ?>
h3[data-v-category-product-name] | innerText = <?php echo $item['name']??""; ?>
p[data-v-category-product-description] | innerText = <?php echo $item['description']??""; ?>
div[data-v-category-product-add-to-pinboard] | data-id = <?php echo $item['id']??""; ?>
div[data-v-category-product-add-to-pinboard] | data-model = <?php echo $item['model']??""; ?>
div[data-v-category-product-add-to-pinboard] | data-title = <?php echo $item['title']??""; ?>
div[data-v-category-product-add-to-pinboard] | data-description = <?php echo $item['description']??""; ?>
div[data-v-category-product-add-to-pinboard] | data-image = <?php echo $item['image']??""; ?>
div[data-v-category-product-add-to-pinboard] | data-product-url = <?php echo isset($item['product_url']) ? $item['product_url'] : ''; ?>
a[data-v-category-product-url] | href = <?php echo $item['url']??""; ?>
img[data-v-category-product-image] | src = <?php echo $item['image']??""; ?>
div[data-v-category-product-tags] > .th-tag | deleteAllButFirst
div[data-v-category-product-tags] | prepend = <?php if(isset($item['tags'])){ foreach ($item['tags'] as $tag) { ?>
div[data-v-category-product-tags] > .th-tag | innerText = <?php echo $tag??""; ?>
div[data-v-category-product-tags] | append = <?php }} ?>
div.swiper-wrapper | append = <?php }} ?>
div[data-v-category-product-finishes] > .th-circle | deleteAllButFirst
div[data-v-category-product-finishes] | prepend = <?php if(isset($item['finishes'])){ foreach ($item['finishes'] as $finish) { ?>
div[data-v-category-product-finishes] > .th-circle | innerText = <?php echo $finish['name']??""; ?>
div[data-v-category-product-finishes] > .th-circle | class = <?php echo $finish['color'] ? $finish['color'] : 'th-circle black-fabric'; ?>
div[data-v-category-product-finishes] | append = <?php }} ?>
#category-seating-details | append = <?php }} ?>

import(components/footer.tpl, [data-v-component-footer])