import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#hero-contactus | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$hero = $current_component = $this->_component['herocontactus']?? [];
    // echo '<pre>';
	// print_r($hero);
	// echo '</pre>';
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

[data-v-component-herocontactus] [data-v-herocontactus-component-link] a | href = <?php echo isset($hero['component_link']) ? $hero['component_link'] : ''; ?>
[data-v-herocontactus-component-link] | if_exists = $is_admin


[data-v-herocontactus-waypoints] | deleteAllButFirst
[data-v-herocontactus-waypoints] | prepend = <?php if(isset($hero['banner_way_points'])){ foreach ($hero['banner_way_points'] as $item) { ?>
div[data-v-herocontactus-waypoint] | id = <?php echo isset($item["id"]) ? 'way-point-'. $item["id"] : ''; ?>
div[data-v-herocontactus-waypoint] | style = <?php echo isset($item["leftPercent"]) && isset($item["topPercent"]) ? "left: ".$item["leftPercent"]."%; top: ".$item["topPercent"]."%;" : ''; ?>
a[data-v-herocontactus-waypoint-link] | innerText = <?php echo isset($item["label"]) ? $item["label"] : ''; ?>
a[data-v-herocontactus-waypoint-link] | href = <?php echo isset($item["href"]) ? trim($item["href"]) : ''; ?>
a[data-v-herocontactus-waypoint-link] | id = <?php echo isset($item["id"]) ? $item["id"] : ''; ?>
[data-v-herocontactus-waypoints] | append = <?php }} ?>





[data-v-component-herocontactus] [data-v-herocontactus-*]|innerText = $hero['@@__data-v-herocontactus-(*)__@@']
section[data-v-herocontactus-image] | data-bg-src = <?php echo $hero['image']??""; ?>
h1[data-v-herocontactus-title] | innerText = <?php echo $hero['title']??""; ?>
span[data-v-herocontactus-subtitle] | innerText = <?php echo $hero['subtitle']??""; ?>
<!-- a[data-v-herocontactus-button_link] | href = <?php echo $hero['button_link']??""; ?> -->
    <!-- span[data-v-herocontactus-button_label_white] | innerText = <?php echo $hero['button_label_white']??""; ?>
    i[data-v-herocontactus-button_icon] | class = <?php echo $hero['button_icon']??""; ?> -->


[data-v-herocontactus-hero_button_group] | prepend = <?php if(isset($hero['buttons'])){ foreach ($hero['buttons'] as $key => $button) { ?>
    [data-v-herocontactus-button_label] | innerText = <?php echo $button['title']; ?>
    [data-v-herocontactus-button_link] | class = <?php echo $key == 0 ? 'th-btn text-capitalize th-btn-text' : 'th-btn-outline text-capitalize th-btn-text'; ?>
    [data-v-herocontactus-button_link] | href = <?php echo $button['url']; ?>
[data-v-herocontactus-hero_button_group] | append = <?php }} ?>

[data-v-component-herocontactus] [data-v-herocontactus-component-link] | if_exists = $is_admin


import(components/showrooms.tpl, [data-v-component-showrooms])


#th-explore-virtually | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$explorevirtually = $current_component = $this->_component['explorevirtually']?? [];

    // echo '<pre>';
    // print_r($explorevirtually);
    // echo '</pre>';
?>
[data-v-component-explorevirtually] [data-v-explorevirtually-*]|innerText = $explorevirtually['@@__data-v-explorevirtually-(*)__@@']

[data-v-component-explorevirtually] .explore-virtually-list .explore-virtually-item | deleteAllButFirst
[data-v-component-explorevirtually] .explore-virtually-list | prepend = <?php foreach ($explorevirtually['items'] as $explorevirtually) { ?>
	img[data-v-explorevirtuallyitem-image] | src = <?php echo isset($explorevirtually["image"]) ? $explorevirtually["image"] : ''; ?>
	h3[data-v-explorevirtuallyitem-title] | innerHTML = <?php echo isset($explorevirtually["title"]) ? $explorevirtually["title"] : ''; ?>
	div[data-v-explorevirtuallyitem-book_btn] | innerHTML = <?php echo isset($explorevirtually["book_btn"]) ? $explorevirtually["book_btn"] : ''; ?>
	div[data-v-explorevirtuallyitem-view_btn] | innerHTML = <?php echo isset($explorevirtually["view_btn"]) ? $explorevirtually["view_btn"] : ''; ?>
    a[data-v-explorevirtuallyitem-book_link] | href = <?php echo isset($explorevirtually["book_link"]) ? $explorevirtually["book_link"] : ''; ?>
	a[data-v-explorevirtuallyitem-view_link] | href = <?php echo isset($explorevirtually["view_link"]) ? $explorevirtually["view_link"] : ''; ?>
[data-v-component-explorevirtually] .explore-virtually-list | append = <?php } ?>



#design-process | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $designprocess = $current_component = $this->_component['ourdesignprocess']?? [];
    // echo '<pre>';
	// print_r($designprocess);
	// echo '</pre>';
?>
[data-v-component-ourdesignprocess] [data-v-ourdesignprocess-*]|innerText = $designprocess['@@__data-v-ourdesignprocess-(*)__@@']
[data-v-component-ourdesignprocess] [data-v-ourdesignprocess-section_title] | innerHTML = <?php echo isset($designprocess['section_title']) ? $designprocess['section_title'] : ''; ?>
[data-v-component-ourdesignprocess] [data-v-ourdesignprocess-section_subtitle] | innerHTML = <?php echo isset($designprocess['section_subtitle']) ? $designprocess['section_subtitle'] : ''; ?>


div[data-v-ourdesignprocess-items] > div.design-resource-item | deleteAllButFirst

div[data-v-ourdesignprocess-items] | prepend = <?php if(isset($designprocess['items'])){ foreach ($designprocess['items'] as $designItem) { ?>

[data-v-ourdesignprocessitem-class] | class = <?php echo $designItem['class'] ?? ''; ?>
[data-v-ourdesignprocessitem-image] | src = <?php echo $designItem['image'] ?? ''; ?>
[data-v-ourdesignprocessitem-step_number] | innerText = <?php echo $designItem['step_number'] ?? ''; ?>
[data-v-ourdesignprocessitem-step_title] | innerHTML = <?php echo $designItem['step_title'] ?? ''; ?>
[data-v-ourdesignprocessitem-step_description] | innerHTML = <?php echo $designItem['step_description'] ?? ''; ?>

div[data-v-ourdesignprocess-items] | append = <?php }} ?>



#contact-getin-touch | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$get_in_touch = $current_component = $this->_component['getintouch']?? [];
    // echo '<pre>';
	// print_r($get_in_touch);
	// echo '</pre>';
	$nonce = $this->parameters['nonce'] ?? '';
	$recaptcha_site_key = $this->parameters['recaptcha_site_key'] ?? '';
	$recaptcha_action = $this->parameters['recaptcha_action'] ?? '';
	// echo '<pre>';
	// print_r($nonce);
	// echo '</pre>';
    $errors = $this->parameters['errors'] ?? [];
	$data = $this->parameters['data'] ?? [];

    $first_name_error = isset($errors['first_name']) ? true : false;
    $last_name_error = isset($errors['last_name']) ? true : false;
    $email_error = isset($errors['email']) ? true : false;
    $files_error = isset($errors['files']) ? true : false;
    $recaptcha_error = isset($errors['recaptcha']) ? true : false;

	// echo "<pre>";
	// print_r($errors);
	// echo "</pre>";

    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
h1[data-v-getintouch-section_title] | innerText = <?php echo isset($get_in_touch['section_title']) ? $get_in_touch['section_title'] : ''; ?>
h1[data-v-getintouch-section_subtitle] | innerText = <?php echo isset($get_in_touch['section_subtitle']) ? $get_in_touch['section_subtitle'] : ''; ?>
img[data-v-getintouch-image] | src = <?php echo isset($get_in_touch['image']) ? $get_in_touch['image'] : ''; ?>


div[data-v-getintouch-component-link] | if_exists = $is_admin

[data-v-component-getintouch] [data-v-getintouch-component-link] a | href = <?php echo isset($get_in_touch['component_link']) ? $get_in_touch['component_link'] : ''; ?>


[data-v-nonce] | value = <?php echo isset($nonce) ? $nonce : ''; ?>

[data-v-recaptcha_site_key] | value = <?php echo isset($recaptcha_site_key) ? htmlspecialchars((string) $recaptcha_site_key, ENT_QUOTES, 'UTF-8') : ''; ?>

[data-v-recaptcha_action] | value = <?php echo isset($recaptcha_action) ? htmlspecialchars((string) $recaptcha_action, ENT_QUOTES, 'UTF-8') : ''; ?>

#g-recaptcha-response | value = <?php echo isset($data['g-recaptcha-response']) ? htmlspecialchars((string) $data['g-recaptcha-response'], ENT_QUOTES, 'UTF-8') : ''; ?>

#recaptcha-feedback | innerText = <?php echo $errors['recaptcha']??""; ?>
#recaptcha-feedback | if_exists = $recaptcha_error


#getin-touch-form | class = <?php echo count($errors) > 0 ? "th-form needs-validation was-validated invalid" : "th-form needs-validation"; ?>


<!-- #email-feedback | innerText = <?php echo $errors['email']??""; ?> -->
#email-feedback | if_exists = $email_error  

#first-name-feedback | innerText = <?php echo $errors['first_name']??""; ?>
#first-name-feedback | if_exists = $first_name_error  

#last-name-feedback | innerText = <?php echo $errors['last_name']??""; ?>
#last-name-feedback | if_exists = $last_name_error  

#files-feedback | innerText = <?php echo $errors['files']??""; ?>
#files-feedback | if_exists = $files_error  





#first-name | value = <?php echo $data['first_name']??""; ?>
#last-name | value = <?php echo $data['last_name']??""; ?>
#email | value = <?php echo $data['email']??""; ?>
#attachments | value = <?php echo $data['files']??""; ?>

#first-name | class = <?php echo $first_name_error ? "is-invalid form-control" : "form-control"; ?>
#last-name | class = <?php echo $last_name_error ? "is-invalid form-control" : "form-control"; ?>
#email | class = <?php echo $email_error ? "is-invalid form-control" : "form-control"; ?>
#attachments | class = <?php echo $files_error ? "is-invalid form-control" : "form-control"; ?>




[data-v-component-getintouch] [data-v-getintouch-*]|innerText = $get_in_touch['@@__data-v-getintouch-(*)__@@']



import(components/needhelp.tpl, [data-v-component-needhelp])


import(components/footer.tpl, [data-v-component-footer])


#virtual-pinboard-item | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $virtualpinboard = $current_component = $this->_component['virtualpinboard']?? [];

    // echo '<pre>';
	// print_r($virtualpinboard);
	// echo '</pre>';
?>

div[data-v-component-virtualpinboard]  div[data-v-virtualpinboard-list] > div[data-v-virtualpinboard-item] | deleteAllButFirst

div[data-v-virtualpinboard-list] | prepend = <?php if(isset($virtualpinboard['items'])){ foreach ($virtualpinboard['items'] as $item) { ?>

    img[data-v-virtualpinboarditem-image] | src = <?php echo $item['image']??""; ?>
    h3[data-v-virtualpinboarditem-type] | innerText = <?php echo $item['type']??""; ?>
    p[data-v-virtualpinboarditem-name] | innerText = <?php echo $item['name']??""; ?>
    span[data-v-virtualpinboarditem-description] | innerText = <?php echo $item['description']??""; ?>

    div[data-v-virtualitemoptions] > img[data-v-virtualitemoption-image] | deleteAllButFirst
    div[data-v-virtualitemoptions] | prepend = <?php if(isset($item['options'])){ foreach ($item['options'] as $option) { ?>
        img[data-v-virtualitemoption-image] | src = <?php echo $option['src']??""; ?>
    div[data-v-virtualitemoptions] | append = <?php }} ?>

    
    input[data-v-virtualpinboarditem-comment_placeholder] | placeholder = <?php echo $item['comment_placeholder']??""; ?>

div[data-v-virtualpinboard-list] | append = <?php }} ?>