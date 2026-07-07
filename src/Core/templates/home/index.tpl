import(components/head.tpl, [data-v-component-head])

import(components/header.tpl, [data-v-component-header])

import(components/hero-home.tpl, [data-v-component-herohome])

#home-categories | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $categories = $current_component = $this->_component['categoriesslidernav']?? [];
		// echo '<pre>';
		// print_r($categories);
		// echo '</pre>';
      $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;  
?>

// Section header data binding
[data-v-categoriesslidernav-component-link] a | href = <?php echo isset($categories['component_link']) ? $categories['component_link'] : ''; ?>
[data-v-categoriesslidernav-section_title] | innerHTML = <?php echo isset($categories["section_title"]) ? $categories["section_title"] : ''; ?>
[data-v-categoriesslidernav-section_subtitle] | innerHTML = <?php echo isset($categories["section_subtitle"]) ? $categories["section_subtitle"] : ''; ?>
[data-v-categoriesslidernav-section_link_text] | innerHTML = <?php echo isset($categories["section_link_text"]) ? $categories["section_link_text"] : ''; ?>

// Navigation menu items
ul[data-v-categoriesslidernav-collapse-items] > li | deleteAllButFirst
ul[data-v-categoriesslidernav-collapse-items]| prepend = <?php if(isset($categories['collapseItems'])){ foreach ($categories['collapseItems'] as $index => $menuItem) { ?>
span[data-v-categoriesslidernav-collapse-items-title] | innerHTML = <?php echo isset($menuItem["menuTitle"]) ? $menuItem["menuTitle"] : ''; ?>
a[data-v-categoriesslidernav-collapse-item] | data-slide-index = <?php echo (int)$index; ?>
[data-v-categoriesslidernav-collapse-items-link] | href = <?php echo isset($menuItem["link"]) ? $menuItem["link"] : ''; ?>

// Sub-menu items
ul[data-v-categoriesslidernav-sub-menu-items] > li | deleteAllButFirst
ul[data-v-categoriesslidernav-sub-menu-items] | prepend = <?php if(isset($menuItem['subMenuItems'])){ foreach ($menuItem['subMenuItems'] as $subItem) { ?>
    a[data-v-categoriesslidernav-collapse-items-sub-menu-items-link] | href = <?php echo isset($subItem["link"]) ? $subItem["link"] : ''; ?>
    h6[data-v-categoriesslidernav-collapse-items-sub-menu-items-title] | innerHTML = <?php echo isset($subItem["title"]) ? $subItem["title"] : ''; ?>
ul[data-v-categoriesslidernav-sub-menu-items] | append = <?php }}; ?>


ul[data-v-categoriesslidernav-sub-menu-items] | append = <?php 
$readMore = "<li><div >";
$readMore .= "<a class='th-link classPadding'href='{$menuItem['link']}'>";
$readMore .= "<div class='th-link-text pr-5'>";
$readMore .= "View All";
$readMore .= "</div>";
$readMore .= "</a></div></li>";
echo $readMore;
?>
ul[data-v-categoriesslidernav-collapse-items] | append = <?php }}; ?>
// Slider items
div[data-v-categoriesslidernav-slider-items] > div.swiper-slide | deleteAllButFirst
div[data-v-categoriesslidernav-slider-items] | prepend = <?php if(isset($categories['items'])){ foreach ($categories['items'] as $item) { ?>
    [data-v-categoriesslidernav-slider-item-image] | src = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
    [data-v-categoriesslidernav-slider-item-image] | alt = <?php echo isset($item["title"]) ? htmlspecialchars($item['title']) : ''; ?>
    [data-v-categoriesslidernav-slider-item-title] | innerHTML = <?php echo isset($item["title"]) ? $item["title"] : ''; ?>
    [data-v-categoriesslidernav-subtext] | innerHTML = <?php echo isset($item["subTitle"]) ? $item["subTitle"] : ''; ?>
    [data-v-categoriesslidernav-slider-item-link] | href = <?php echo isset($item["link"]) ? $item["link"] : ''; ?>
    [data-v-categoriesslidernav-slider-item-buttonText] | innerHTML = <?php echo isset($item["buttonText"]) ? $item["buttonText"] : ''; ?>
div[data-v-categoriesslidernav-slider-items] | append = <?php }} ?>

div[data-v-categoriesslidernav-component-link] | if_exists = $is_admin


import(components/project_slider.tpl, [data-v-component-featuredprojectslider])


#th-block-quote | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $blockquote = $current_component = $this->_component['blockquote']?? [];
    // echo '<pre>';
    // print_r($blockquote);
    // echo '</pre>';

    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

[data-v-blockquote-component-link] a | href = <?php echo isset($blockquote['component_link']) ? $blockquote['component_link'] : ''; ?>
div[data-v-blockquote-content] p | innerHTML = <?php echo isset($blockquote["description"]) ? $blockquote["description"] : 'Our Legacy Isn’t Only About Refined And Functional Furniture, Fit For Australian Organizations, It’s About The Trust We’ve Fostered For Decades'; ?>
span[data-v-blockquote-image] img | src = <?php echo isset($blockquote["image"]) ? $blockquote["image"] : '/img/logo_black.png'; ?>

div[data-v-blockquote-component-link] | if_exists = $is_admin

import(components/design_resources.tpl, [data-v-component-designresources])

#feature-products | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $featured_products = $current_component = $this->_component['featureproductsmasonry']?? [];
    // echo '<pre>';
    // print_r($featured_products);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

[data-v-featureproductsmasonry-section_title] | innerHTML = <?php echo isset($featured_products["section_title"]) ? $featured_products["section_title"] : ''; ?>
[data-v-featureproductsmasonry-section_subtitle] | innerHTML = <?php echo isset($featured_products["section_subtitle"]) ? $featured_products["section_subtitle"] : ''; ?>
[data-v-featureproductsmasonry-component-link] a | href = <?php echo isset($featured_products['component_link']) ? $featured_products['component_link'] : ''; ?>

div[data-v-featureproductsmasonry-grid] > div.th-masonry-grid-item | deleteAllButFirst

div[data-v-featureproductsmasonry-grid] | prepend = <?php if(isset($featured_products['items'])){ foreach ($featured_products['items'] as $product) { ?>
    div[data-v-featureproductsmasonryitem-img-wrapper] a[data-v-featureproductsmasonryitem-link] | href = <?php echo isset($product["link"]) ? $product["link"] : ''; ?>
    div[data-v-featureproductsmasonryitem-img-wrapper] img[data-v-featureproductsmasonryitem-img] | src = <?php echo isset($product["img"]) ? $product["img"] : ''; ?>
    div[data-v-featureproductsmasonryitem-img-wrapper] img[data-v-featureproductsmasonryitem-img] | alt = <?php echo isset($product["heading"]) ? htmlspecialchars($product['heading']) : ''; ?>
div[data-v-featureproductsmasonryitem-class] | class = <?php echo isset($product["class"]) ? $product["class"] : ''; ?>
h6[data-v-featureproductsmasonryitem-heading] a | innerHTML = <?php echo isset($product["heading"]) ? $product["heading"] : ''; ?>
h6[data-v-featureproductsmasonryitem-heading] a[data-v-featureproductsmasonryitem-link] | href = <?php echo isset($product["link"]) ? $product["link"] : ''; ?>
p[data-v-featureproductsmasonryitem-des] | innerHTML = <?php echo isset($product["des"]) ? $product["des"] : ''; ?>
a[data-v-featureproductsmasonryitem-link] | href = <?php echo isset($product["link"]) ? $product["link"] : ''; ?>
<!-- a[data-v-featureproductsmasonryitem-link] div | innerHTML = <?php echo isset($product["link"]) ? 'View '. $product['heading'] : ''; ?> -->
div[data-v-featureproductsmasonryitem-product-link] a | innerHTML = <?php echo isset($product["link"]) ? 'View '. $product['heading'] : ''; ?>
div[data-v-featuredproductitem-add-to-pinboard] | data-id = <?php echo isset($product["product_id"]) ? $product["product_id"] : ''; ?>
div[data-v-featuredproductitem-add-to-pinboard] | data-product-url = <?php echo isset($product["link"]) ? $product["link"] : ''; ?>
div[data-v-featuredproductitem-add-to-pinboard] | data-title = <?php echo isset($product["heading"]) ? $product["heading"] : ''; ?>
div[data-v-featuredproductitem-add-to-pinboard] | data-description = <?php echo isset($product["tag_line"]) ? $product["tag_line"] : ''; ?>
div[data-v-featuredproductitem-add-to-pinboard] | data-image = <?php echo isset($product["img"]) ? $product["img"] : ''; ?>
div[data-v-featureproductsmasonry-grid] | append = <?php }} ?>

div[data-v-featureproductsmasonry-component-link] | if_exists = $is_admin

import(components/blogs.tpl, [data-v-component-blogslider])

import(components/needhelp.tpl, [data-v-component-needhelp])

import(components/footer.tpl, [data-v-component-footer])
