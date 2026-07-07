import(components/head.tpl, [data-v-component-head])
#signup-page | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $signup = $current_component = $this->_component['signup']?? [];
    // echo '<pre>';
	// print_r($signup);
	// echo '</pre>';

    $nonce = $this->parameters['nonce'] ?? '';
    $errors = $this->parameters['errors'] ?? [];
    $data = $this->parameters['data'] ?? [];

    $name_error = isset($errors['name']) ? true : false;
    $email_error = isset($errors['email']) ? true : false;
    $password_error = isset($errors['password']) ? true : false;
    $nonce_error = isset($errors['nonce']) ? true : false;

    // echo "<pre>";
    // print_r($errors);
    // echo "</pre>"
?>
[data-v-nonce] | value = <?php echo $nonce; ?>

#csrf-error-message | innerText = <?php echo $errors['nonce'] ?? ''; ?>
#csrf-error-message | if_exists = $nonce_error

#signup-form | class = <?php echo count($errors) > 0 ? "th-form  needs-validation was-validated invalid" : "th-form  needs-validation"; ?>

#name-feedback | innerText = <?php echo $errors['name']??""; ?>
#name-feedback | if_exists = $name_error

#email-feedback | innerText = <?php echo $errors['email']??""; ?>
#email-feedback | if_exists = $email_error

#password-feedback | innerText = <?php echo $errors['password']??""; ?>
#password-feedback | if_exists = $password_error

#name | value = <?php echo $data['name']??""; ?>
#email | value = <?php echo $data['email']??""; ?>
#password | value = <?php echo $data['password']??""; ?>


#name | class = <?php echo $name_error ? "is-invalid form-control" : "form-control"; ?>
#email | class = <?php echo $email_error ? "is-invalid form-control" : "form-control"; ?>
#password | class = <?php echo $password_error ? "is-invalid form-control" : "form-control"; ?>



img[data-v-signup-image] | src = <?php echo isset($signup['image']) ? $signup['image'] : ""; ?>
h2[data-v-signup-section_title] | innerText = <?php echo isset($signup['section_title'])? $signup['section_title']:""; ?>
p[data-v-signup-section_description] | innerText = <?php echo isset($signup['section_description'])? $signup['section_description']:""; ?>

<!-- import(components/footer.tpl, [data-v-component-footer]) -->

