<!-- ======================================================== Start Page Header ====================================================== -->
#th-page-header | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$header = $current_component = $this->_component['pageheader']?? [];
   
    // echo '<pre>';
    // print_r($header);
    // echo '</pre>';
?>
?>

ul[data-v-header-desktop-menu-list] > li[data-v-header-desktop-menu-item] | deleteAllButFirst
ul[data-v-header-desktop-menu-list] | prepend = <?php if(isset($header['desktop_menu'])){ foreach ($header['desktop_menu'] as $menu) { ?>
    li[data-v-header-desktop-menu-item] | class = <?php echo isset($menu["class"]) ? $menu["class"] : ''; ?>
    span[data-v-header-desktop-menu-item-title] | innerText = <?php echo isset($menu["title"]) ? $menu["title"] : ''; ?>
    ul.mega-menu | if_exists = $menu['mega_menu']
    li[data-v-header-desktop-menu-item] > a | href = <?php echo isset($menu["href"]) ? $menu["href"] : ''; ?>
        div[data-v-header-mega-menu-rows] > div[data-v-header-mega-menu-row] | deleteAllButFirst
        div[data-v-header-mega-menu-rows] | prepend = <?php if(isset($menu['rows'])){ foreach ($menu['rows'] as $key => $row) { ?>
            div[data-v-header-mega-menu-row] | class = <?php if($key == 0){ echo 'row mega-menu-row category-list'; }else{ echo 'row mega-menu-row mt-50 category-list'; } ?>
            div[data-v-header-mega-menu-row] > div[data-v-header-mega-menu-item] | deleteAllButFirst
            div[data-v-header-mega-menu-row] | prepend = <?php if(isset($row)){ foreach ($row as $item) { ?>
            h5[data-v-header-mega-menu-item-title] | innerText = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
                span[data-v-header-mega-menu-item-links] > a[data-v-header-mega-menu-item-link] | deleteAllButFirst
                span[data-v-header-mega-menu-item-links] | prepend = <?php if(isset($item['links'])){ foreach ($item['links'] as $link) { ?>
                    a[data-v-header-mega-menu-item-link] | href = <?php echo isset($link['href']) ? $link['href'] : ''; ?>
                    a[data-v-header-mega-menu-item-link] > span | innerText = <?php echo isset($link['title']) ? $link['title'] : ''; ?>
                span[data-v-header-mega-menu-item-links] | append = <?php  }} ?>
            div[data-v-header-mega-menu-row] | append = <?php  }} ?>
        div[data-v-header-mega-menu-rows] | append = <?php }}; ?>
ul[data-v-header-desktop-menu-list] | append = <?php }}; ?>

ul[data-v-header-mobile-menu-list] > li | deleteAllButFirst
ul[data-v-header-mobile-menu-list] | prepend = <?php if(isset($header['mobile_menu'])){ foreach ($header['mobile_menu'] as $menu) { ?>
    li[data-v-header-mobile-menu-item] | class = <?php echo ($menu["class"]?? '').' mobile-menu-item'; ?>
    a[data-v-header-mobile-menu-item-href] | href = <?php echo ($menu["href"]?? ''); ?>
    span[data-v-header-mobile-menu-item-title] | innerText = <?php echo isset($menu["title"]) ? $menu["title"] : ''; ?>
    ul.sub-menu | if_exists = $menu['has_children']
    ul[data-v-header-mobilemenu-children] > li | deleteAllButFirst
    ul[data-v-header-mobilemenu-children] | prepend = <?php if(isset($menu['children'])){ foreach ($menu['children'] as $key => $child) { ?>
       a[data-v-header-mobilemenu-childitem] | href = <?php echo ($child["href"]?? ''); ?>
       a[data-v-header-mobilemenu-childitem] | innerText = <?php echo  $child["title"] ?? ''; ?>
    ul[data-v-header-mobilemenu-children] | append = <?php }}; ?>
ul[data-v-header-mobile-menu-list] | append = <?php }}; ?>

<!-- ======================================================== End Page Header ====================================================== -->




<!-- ======================================================== Start Page Body ====================================================== -->
#th-page-body | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$pabebody = $current_component = $this->_component['pagebody']?? [];
    
    // echo '<pre>';
    // print_r($pabebody);
    // echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

<!-- hero section -->
[data-v-component-pagebody] [data-v-pagehero-*]|innerText = $pabebody['@@__data-v-pagehero-(*)__@@']
[data-v-component-pagebody] [data-v-pagebody-hero-component-link] | prepend = <?php if(isset($pabebody['component_link'])){ ?>
	[data-v-component-pagebody] [data-v-pagebody-hero-component-link] a | href = <?php echo isset($pabebody['component_link']) ? $pabebody['component_link'] : ''; ?>
[data-v-component-pagebody] [data-v-pagebody-hero-component-link] | append = <?php } ?>

[data-v-pagehero-image]|data-bg-src = <?php echo $pabebody['image_banner']??'/img/project-detail/hero.png'; ?>
span[data-v-pagehero-date]|innerText = <?php echo isset($pabebody['created_at']) ? $pabebody['created_at'] : ''; ?>
<!-- h1[data-v-pagehero-heading]|innerText = <?php echo isset($pabebody['title']) ? $pabebody['title'] : ''; ?> -->
h1[data-v-pagehero-heading]|innerText = <?php echo isset($pabebody['postContent']) ? $pabebody['postContent']['name'] : ''; ?>
div[data-v-pagebody-hero-component-link] | if_exists = $is_admin



<!-- excerpt section  -->
[data-v-component-pagebody] [data-v-pagedetailsexcerpt-component-link] | prepend = <?php if(isset($pabebody['component_link'])){ ?>
	[data-v-component-pagebody] [data-v-pagedetailsexcerpt-component-link] a | href = <?php echo isset($pabebody['component_link']) ? $pabebody['component_link'] : ''; ?>
[data-v-component-pagebody] [data-v-pagedetailsexcerpt-component-link] | append = <?php } ?>

h2[data-v-pagedetail-excerpt_title]|innerText = <?php echo isset($pabebody['excerpt_title']) ? $pabebody['excerpt_title'] : ''; ?>
p[data-v-pagedetail-description]|innerText = <?php echo isset($pabebody['description']) ? $pabebody['description'] : ''; ?>
div[data-v-pagedetailsexcerpt-component-link] | if_exists = $is_admin



<!-- main section  -->
[data-v-component-pagebody] [data-v-pagemain-component-link] | prepend = <?php if(isset($pabebody['component_link'])){ ?>
	[data-v-component-pagebody] [data-v-pagemain-component-link] a | href = <?php echo isset($pabebody['component_link']) ? $pabebody['component_link'] : ''; ?>
[data-v-component-pagebody] [data-v-pagemain-component-link] | append = <?php } ?>

h2[data-v-pagemain-section_title]|innerHTML = <?php echo isset($pabebody['section_title']) ? $pabebody['section_title'] : ''; ?>
div[data-v-pagemain-section_subtitle]|innerHTML = <?php echo isset($pabebody['description']) ? str_replace('&nbsp;', ' ', $pabebody['description']) : ''; ?>
img[data-v-pagemain-mainimage] | src = <?php echo isset($pabebody['feature_image']) ? $pabebody['feature_image'] : ''; ?>
div[data-v-pagemain-section_subtitle2]|innerHTML = <?php echo isset($pabebody['description_one']) ? str_replace('&nbsp;', ' ', $pabebody['description_one']) : ''; ?>
div[data-v-pagemain-section_subtitle3]|innerHTML = <?php echo isset($pabebody['description_two']) ? str_replace('&nbsp;', ' ', $pabebody['description_two']) : ''; ?>
div[data-v-pagemain-section_subtitle4]|innerHTML = <?php echo isset($pabebody['description_three']) ? str_replace('&nbsp;', ' ', $pabebody['description_three']) : ''; ?>
div[data-v-pagemain-component-link] | if_exists = $is_admin



<!-- gallery section  -->
[data-v-component-pagebody] [data-v-pagegallery-component-link] | prepend = <?php if(isset($pabebody['component_link'])){ ?>
	[data-v-component-pagebody] [data-v-pagegallery-component-link] a | href = <?php echo isset($pabebody['component_link']) ? $pabebody['component_link'] : ''; ?>
[data-v-component-pagebody] [data-v-pagegallery-component-link] | append = <?php } ?>

div[data-v-pageprojectgallery-thumbs] > button | deleteAllButFirst
div[data-v-pageprojectgallery-thumbs] | prepend = <?php if(isset($pabebody['galleryItems'])){ foreach ($pabebody['galleryItems'] as $item) { ?>
button[data-v-pageprojectgalleryitem-thumb] | class = <?php echo isset($item["thumb_class"]) ? $item["thumb_class"] : ''; ?>
img[data-v-pageprojectgalleryitem-thumb_image] | src = <?php echo isset($item["thumb_image"]) ? $item["thumb_image"] : ''; ?>
button[data-v-pageprojectgalleryitem-thumb] | id = <?php echo isset($item["thumb_id"]) ? $item["thumb_id"] : ''; ?>
button[data-v-pageprojectgalleryitem-thumb] | data-bs-target = <?php echo isset($item["target"]) ? $item["target"] : ''; ?>
div[data-v-pageprojectgallery-thumbs] | append = <?php }} ?>

div[data-v-pagegallery-content] > div | deleteAllButFirst
div[data-v-pagegallery-content] | prepend = <?php if(isset($pabebody['galleryItems'])){ foreach ($pabebody['galleryItems'] as $tabItem) { ?>
div[data-v-pagegallery-content-item] | class = <?php echo isset($tabItem["class"]) ? $tabItem["class"] : ''; ?>
img[data-v-pagegallery-content-item-img] | src = <?php echo isset($tabItem["image"]) ? $tabItem["image"] : ''; ?>
div[data-v-pagegallery-content-item] | id = <?php echo isset($tabItem["id"]) ? $tabItem["id"] : ''; ?>
div[data-v-pagegallery-content-item] | aria-labelledby = <?php echo isset($tabItem["thumb_id"]) ? $tabItem["thumb_id"] : ''; ?>
div[data-v-pagegallery-content] | append = <?php }} ?>

div[data-v-pagegallery-component-link] | if_exists = $is_admin





<!-- Footer -->
import(components/footer.tpl, [data-v-component-footer])
<!-- ======================================================== End Page Body ====================================================== -->



