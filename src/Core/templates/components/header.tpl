script#is-logged-in | before = <?php

    $is_logged_in = !empty($this->parameters['is_logged_in']);
    $is_logged_in_js = $is_logged_in ? 'true' : 'false';
?>
script#is-logged-in | innerText = <?php echo "window.__AUTH_PRESENT__ = " . $is_logged_in_js . ";"; ?>

.th-header | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$header = $current_component = $this->_component['header']?? [];

    // echo '<pre>';
    // print_r($header);
    // echo '</pre>';


    // echo '<pre>';
    // print_r($header['desktop_menu']);
    // echo '</pre>';

    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
    // echo '<pre>';
    // print_r($header['account_menu']);
    // echo '</pre>';

    // echo '<pre>';
    // print_r($this->parameters);
    // echo '</pre>';
?>
a[data-v-header-top_header_middle_text] | innerText = <?php echo isset($header['top_header_middle_text']) ? $header['top_header_middle_text'] : ''; ?>
a[data-v-header-top_header_middle_text] | href = <?php echo isset($header['top_header_middle_text_link']) ? $header['top_header_middle_text_link'] : ''; ?>
a[data-v-header-top_header_right_text] | innerText = <?php echo isset($header['top_header_right_text']) ? $header['top_header_right_text'] : ''; ?>
a[data-v-header-top_header_right_text] | href = <?php echo isset($header['Top_header_right_text_link']) ? $header['Top_header_right_text_link'] : ''; ?>
div[data-v-header-component-link] > a | href = <?php echo isset($header['component_link']) ? $header['component_link'] : ''; ?>

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
                a[data-v-header-mega-submenu-item-link] | href = <?php echo isset($item['href']) ? $item['href'] : ''; ?>
                h5[data-v-header-mega-menu-item-title] | innerText = <?php echo isset($item['title']) ? $item['title'] : ''; ?>
                div[data-v-header-mega-menu-item] | style = <?php echo isset($item['style']) ? $item['style'] : ''; ?>
                
                span[data-v-header-mega-menu-item-links] > a[data-v-header-mega-menu-item-link] | deleteAllButFirst
                span[data-v-header-mega-menu-item-links] | prepend = <?php if(isset($item['links'])){ foreach ($item['links'] as $link) { ?>
                    a[data-v-header-mega-menu-item-link] | href = <?php echo isset($link['href']) ? $link['href'] : ''; ?>
                    a[data-v-header-mega-menu-item-link] > span | innerText = <?php echo isset($link['label_name']) ? $link['label_name'] : ''; ?>
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
       a[data-v-header-mobilemenu-childitem] | innerText = <?php echo  $child["label_name"] ?? ''; ?>
       a[data-v-header-mobilemenu-childitem] | class = <?php echo  $child["class"] ?? ''; ?>
    ul[data-v-header-mobilemenu-children] | append = <?php }}; ?>
ul[data-v-header-mobile-menu-list] | append = <?php }}; ?>

ul[data-v-header-navigation-menus] > li | deleteAllButFirst
ul[data-v-header-navigation-menus] | prepend = <?php if(isset($header['account_menu'])){ foreach ($header['account_menu'] as $menu) { ?>
    li[data-v-header-navigation-menu-item] > a | href = <?php echo isset($menu["href"]) ? $menu["href"] : ''; ?>
    li[data-v-header-navigation-menu-item] > a | innerText = <?php echo isset($menu["title"]) ? $menu["title"] : ''; ?>
    li[data-v-header-navigation-menu-item] > a | id = <?php echo isset($menu["id"]) ? $menu["id"] : ''; ?>
ul[data-v-header-navigation-menus] | append = <?php }}; ?>

#krost-recaptcha-config | data-recaptcha-site-key = <?php echo htmlspecialchars((string) App\Core\System\utils\env('RECAPTCHA_SITE_KEY', ''), ENT_QUOTES, 'UTF-8'); ?>

#krost-recaptcha-config | data-recaptcha-action-contact = <?php echo htmlspecialchars((string) App\Core\System\utils\env('RECAPTCHA_ACTION', 'contact_submit'), ENT_QUOTES, 'UTF-8'); ?>

#krost-recaptcha-config | data-recaptcha-action-service = <?php echo htmlspecialchars((string) App\Core\System\utils\env('RECAPTCHA_ACTION_SERVICE', 'service_request'), ENT_QUOTES, 'UTF-8'); ?>

#krost-recaptcha-config | data-recaptcha-action-project = <?php echo htmlspecialchars((string) App\Core\System\utils\env('RECAPTCHA_ACTION_PROJECT', 'project_submission'), ENT_QUOTES, 'UTF-8'); ?>
div[data-v-header-component-link] | if_exists = $is_admin