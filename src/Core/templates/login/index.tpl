import(components/header.tpl, [data-v-component-header])


#login-page | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $login = $current_component = $this->_component['login']?? [];
    // echo '<pre>';
	// print_r($this->parameters);
	// echo '</pre>';

    $nonce = $this->parameters['nonce'] ?? '';
    $errors = $this->parameters['errors'] ?? [];
    $data = $this->parameters['data'] ?? [];


    $email_error = isset($errors['email']) ? true : false;
    $nonce_error = isset($errors['nonce']) ? true : false;

    // echo "<pre>";
    // print_r($errors);
    // echo "</pre>";
?>

[data-v-nonce] | value = <?php echo $nonce; ?>

#csrf-error-message | innerText = <?php echo $errors['nonce'] ?? ''; ?>
#csrf-error-message | if_exists = $nonce_error

#login-form | class = <?php echo count($errors) > 0 ? "th-form  needs-validation was-validated invalid" : "th-form  needs-validation"; ?>

#email-feedback | innerText = <?php echo $errors['email'] ?? ''; ?>

#email | value = <?php echo $data['email'] ?? ""; ?>
#email | class = <?php echo $email_error ? "is-invalid form-control" : "form-control"; ?>
#signup-email-value | value = <?php echo $data['email']??""; ?>


<!-- #email-feedback | innerText = <?php echo $this->parameters['errors']['email']??""; ?>
#email-feedback | if_exits = <?php $this->parameters['errors']['email'] ?? false;?>    
#login-form | class = <?php echo isset($this->parameters['errors']) && count($this->parameters['errors']) > 0 ? "th-form  needs-validation was-validated" : "th-form  needs-validation"; ?>
#email | value = <?php echo $this->parameters['email']??""; ?>
#signup-email-value | value = <?php echo $this->parameters['email']??""; ?> -->



[data-v-component-login] [data-v-login-*]|innerText = $login['@@__data-v-login-(*)__@@']
img[data-v-login-image] | src = <?php echo $login['image']??""; ?>
h2[data-v-login-section_title] | innerText = <?php echo $login['section_title']??""; ?>
p[data-v-login-section_description] | innerText = <?php echo $login['section_description']??""; ?>






#virtual-pinboard-item | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $virtualpinboard = $current_component = $this->_component['virtualpinboard']?? [];

    // echo '<pre>';
	// print_r($virtualpinboard);
	// echo '</pre>';
?>

div[data-v-virtualpinboard-list] > div[data-v-virtualpinboard-item] | deleteAllButFirst
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