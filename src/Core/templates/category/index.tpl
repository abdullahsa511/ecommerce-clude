import(components/head.tpl, [data-v-component-head])

import(components/header.tpl, [data-v-component-header])

#categories-masonry-section | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$categories = $current_component = $this->_component['categoriesmasonry']?? [];
	// echo '<pre>';
	// print_r($categories);
	// echo '</pre>';

	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

[data-v-component-categoriesmasonry] [data-v-categoriesmasonry-*]|innerText = $categories['@@__data-v-categoriesmasonry-(*)__@@']

div[data-v-categoriesmasonry-component-link] > a | href = <?php echo isset($categories["component_link"]) ? $categories["component_link"] : '';?>

.categories-masonry-grid > div.th-masonry-grid-item | deleteAllButFirst

.categories-masonry-grid | prepend = <?php if(isset($categories['items'])){ foreach ($categories['items'] as $category) { ?>
div[data-v-categoriesmasonryitem-class] | class = <?php echo isset($category["class"]) ? $category["class"] : '';?>
img[data-v-categoriesmasonryitem-image] | src = <?php echo isset($category["img"]) ? $category["img"] : '';?>
h3[data-v-categoriesmasonryitem-heading] > a | innerText = <?php echo isset($category["heading"]) ? $category["heading"] : '';?>
h3[data-v-categoriesmasonryitem-heading] > a | href = <?php echo isset($category["link"]["url"]) ? $category["link"]["url"] : '';?>
p[data-v-categoriesmasonryitem-description] | innerText = <?php echo isset($category["des"]) ? $category["des"] : '';?>

div[data-v-subcategory-items] > h4[data-v-subcategory-item] | deleteAllButFirst

div[data-v-subcategory-items] | prepend = <?php if(isset($category["subcategories"])){ foreach ($category["subcategories"] as $subcategory) { ?>
 a[data-v-subcategory-link] | innerText = <?php echo key($subcategory)??'';?>
 a[data-v-subcategory-link] | href = <?php echo current($subcategory)??'';?>
div[data-v-subcategory-items] | append = <?php }} ?>

a[data-v-categoriesmasonryitem-link] | innerText = <?php echo isset($category["link"]["text"]) ? $category["link"]["text"] : '';?>
a[data-v-categoriesmasonryitem-link] | href = <?php echo isset($category["link"]["url"]) ? $category["link"]["url"] : '';?>
i[data-v-categoriesmasonryitem-linkicon] | class = <?php echo isset($category["link"]["icon"]) ? $category["link"]["icon"] : '';?>

.categories-masonry-grid | append = <?php }} ?>

div[data-v-categoriesmasonry-component-link] | if_exists = $is_admin

import(components/footer.tpl, [data-v-component-footer])
