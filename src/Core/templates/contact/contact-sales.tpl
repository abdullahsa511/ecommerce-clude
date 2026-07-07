import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#hero-contactsales | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$hero = $current_component = $this->_component['herocontactsales']?? [];
    // echo '<pre>';
	// print_r($hero);
	// echo '</pre>';

	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

section[data-v-herocontactsales-image] | data-bg-src = <?php echo $hero['image']??""; ?>
div[data-v-contactsales-component-link] > a | href = <?php echo isset($hero['component_link']) ? $hero['component_link'] : ''; ?>
h1[data-v-herocontactsales-title] | innerText = <?php echo $hero['title']??""; ?>



[data-v-herocontactsales-hero_button_group] | prepend = <?php if(isset($hero['buttons'])){ foreach ($hero['buttons'] as $key => $button) { ?>
    [data-v-herocontactsales-button_label] | innerText = <?php echo $button['title']; ?>

    [data-v-herocontactsales-button_link] | class = <?php echo $key == 0 ? 'th-btn th-btn-text th-btn-text-preserve' : 'th-btn-outline th-btn-text th-btn-text-preserve'; ?>
    [data-v-herocontactsales-button_link] | href = <?php echo $button['url']; ?>
[data-v-herocontactsales-hero_button_group] | append = <?php }} ?>

div[data-v-contactsales-component-link] | if_exists = $is_admin



import(components/showrooms.tpl, [data-v-component-showrooms])


#th-contact-members | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$sales_team_sydney = $current_component = $this->_component['salesteamsydneys']?? [];
	$sales_team_sydney_items = isset($sales_team_sydney['items']) ? $sales_team_sydney['items'] : [];
	// echo '<pre>';
	// print_r($sales_team_sydney['items']);
	// echo '</pre>';

	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-salesteamsydneys-component-link] a | href = <?php echo isset($sales_team_sydney['component_link']) ? $sales_team_sydney['component_link'] : ''; ?>
[data-v-component-salesteamsydneys] [data-v-salesteamsydneys-*]|innerText = $sales_team_sydney['@@__data-v-salesteamsydneys-(*)__@@']

[data-v-salesteamsydneys-items] > [data-v-salesteamsydneys-item] | deleteAllButFirst
[data-v-salesteamsydneys-items] | prepend = <?php foreach ($sales_team_sydney_items as $key => $item) { ?>
	[data-v-salesteamsydneys-item] | deleteAllButFirst
	<!-- member email -->
	[data-v-salesteamsydneyitem-member_email] | data-location = 'Sydney Showroom';
	[data-v-salesteamsydneyitem-member_email] | data-member-name = <?php echo isset($item["name"]) ? $item["name"] : 'Jane Doe'; ?>
	[data-v-salesteamsydneyitem-member_email] | data-member-email = <?php echo isset($item["email"]) ? $item["email"] : 'sales@krost.com.au'; ?>
	[data-v-salesteamsydneyitem-member_email] | data-member-phone = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>

	<!-- member phone -->
	[data-v-salesteamsydneyitem-member_phone] | data-location = 'Sydney Showroom';
	[data-v-salesteamsydneyitem-member_phone] | data-member-name = <?php echo isset($item["name"]) ? $item["name"] : 'Jane Doe'; ?>
	[data-v-salesteamsydneyitem-member_phone] | data-member-email = <?php echo isset($item["email"]) ? $item["email"] : 'sales@krost.com.au'; ?>
	[data-v-salesteamsydneyitem-member_phone] | data-member-phone = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>
	[data-v-salesteamsydneyitem-member_phone] | title = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>

	[data-v-salesteamsydneyitem-member_image] | data-bg-src = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
	[data-v-salesteamsydneyitem-member_name] | innerHTML = <?php echo isset($item["name"]) ? $item["name"] : ''; ?>
	[data-v-salesteamsydneyitem-member_position] | innerHTML = <?php echo isset($item["designation"]) ? $item["designation"] : ''; ?>
[data-v-salesteamsydneys-items] | append = <?php } ?>

div[data-v-salesteamsydneys-component-link] | if_exists = $is_admin

#sales-team-melbourne | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$sales_team_melbourne = $current_component = $this->_component['salesteammelbourne']?? [];
	$sales_team_melbourne_items = isset($sales_team_melbourne['items']) ? $sales_team_melbourne['items'] : [];
	// echo '<pre>';
	// print_r($sales_team_melbourne);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-component-salesteammelbourne] [data-v-salesteammelbourne-*]|innerText = $sales_team_melbourne['@@__data-v-salesteammelbourne-(*)__@@']

[data-v-salesteammelbourne-items] > [data-v-salesteammelbourne-item] | deleteAllButFirst
[data-v-salesteammelbourne-items] | prepend = <?php foreach ($sales_team_melbourne_items as $key => $item) { ?>
	[data-v-salesteammelbourne-item] | deleteAllButFirst

	<!-- member calendar -->
	[data-v-salesteammelbourneitem-member_calendar] | data-location = 'Melbourne Showroom';
	[data-v-salesteammelbourneitem-member_calendar] | data-member-name = <?php echo isset($item["name"]) ? $item["name"] : 'Jane Doe'; ?>
	[data-v-salesteammelbourneitem-member_calendar] | data-member-email = <?php echo isset($item["email"]) ? $item["email"] : 'sales@krost.com.au'; ?>
	[data-v-salesteammelbourneitem-member_calendar] | data-member-phone = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>

	<!-- member email -->
	[data-v-salesteammelbourneitem-member_email] | data-location = 'Melbourne Showroom';
	[data-v-salesteammelbourneitem-member_email] | data-member-name = <?php echo isset($item["name"]) ? $item["name"] : 'Jane Doe'; ?>
	[data-v-salesteammelbourneitem-member_email] | data-member-email = <?php echo isset($item["email"]) ? $item["email"] : 'sales@krost.com.au'; ?>
	[data-v-salesteammelbourneitem-member_email] | data-member-phone = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>

	<!-- member phone -->
	[data-v-salesteammelbourneitem-member_phone] | data-location = 'Melbourne Showroom';
	[data-v-salesteammelbourneitem-member_phone] | data-member-name = <?php echo isset($item["name"]) ? $item["name"] : 'Jane Doe'; ?>
	[data-v-salesteammelbourneitem-member_phone] | data-member-email = <?php echo isset($item["email"]) ? $item["email"] : 'sales@krost.com.au'; ?>
	[data-v-salesteammelbourneitem-member_phone] | data-member-phone = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>
	[data-v-salesteammelbourneitem-member_phone] | title = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>

	[data-v-salesteammelbourneitem-member_image] | data-bg-src = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
	[data-v-salesteammelbourneitem-member_name] | innerHTML = <?php echo isset($item["name"]) ? $item["name"] : ''; ?>
	[data-v-salesteammelbourneitem-member_position] | innerHTML = <?php echo isset($item["designation"]) ? $item["designation"] : ''; ?>
[data-v-salesteammelbourne-items] | append = <?php } ?>

div[data-v-salesteammelbourne-component-link] | if_exists = $is_admin

#sales-team-brisbane | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$sales_team_brisbane = $current_component = $this->_component['salesteambrisbane']?? [];
	$sales_team_brisbane_items = isset($sales_team_brisbane['items']) ? $sales_team_brisbane['items'] : [];
	// echo '<pre>';
	// print_r($sales_team_brisbane);
	// echo '</pre>';
	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-component-salesteambrisbane] [data-v-salesteambrisbane-*]|innerText = $sales_team_brisbane['@@__data-v-salesteambrisbane-(*)__@@']

[data-v-salesteambrisbane-items] > [data-v-salesteambrisbane-item] | deleteAllButFirst
[data-v-salesteambrisbane-items] | prepend = <?php foreach ($sales_team_brisbane_items as $key => $item) { ?>
	[data-v-salesteambrisbane-item] | deleteAllButFirst

	<!-- member calendar -->
	[data-v-salesteambrisbaneitem-member_calendar] | data-location = 'Brisbane Showroom';
	[data-v-salesteambrisbaneitem-member_calendar] | data-member-name = <?php echo isset($item["name"]) ? $item["name"] : 'Jane Doe'; ?>
	[data-v-salesteambrisbaneitem-member_calendar] | data-member-email = <?php echo isset($item["email"]) ? $item["email"] : 'sales@krost.com.au'; ?>
	[data-v-salesteambrisbaneitem-member_calendar] | data-member-phone = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>

	<!-- member email -->
	[data-v-salesteambrisbaneitem-member_email] | data-location = 'Brisbane Showroom';
	[data-v-salesteambrisbaneitem-member_email] | data-member-name = <?php echo isset($item["name"]) ? $item["name"] : 'Jane Doe'; ?>
	[data-v-salesteambrisbaneitem-member_email] | data-member-email = <?php echo isset($item["email"]) ? $item["email"] : 'sales@krost.com.au'; ?>
	[data-v-salesteambrisbaneitem-member_email] | data-member-phone = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>

	<!-- member phone -->
	[data-v-salesteambrisbaneitem-member_phone] | data-location = 'Brisbane Showroom';
	[data-v-salesteambrisbaneitem-member_phone] | data-member-name = <?php echo isset($item["name"]) ? $item["name"] : 'Jane Doe'; ?>
	[data-v-salesteambrisbaneitem-member_phone] | data-member-email = <?php echo isset($item["email"]) ? $item["email"] : 'sales@krost.com.au'; ?>
	[data-v-salesteambrisbaneitem-member_phone] | data-member-phone = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>
	[data-v-salesteambrisbaneitem-member_phone] | title = <?php echo isset($item["phone"]) ? $item["phone"] : '0412345678'; ?>
	
	[data-v-salesteambrisbaneitem-member_image] | data-bg-src = <?php echo isset($item["image"]) ? $item["image"] : ''; ?>
	[data-v-salesteambrisbaneitem-member_name] | innerHTML = <?php echo isset($item["name"]) ? $item["name"] : ''; ?>
	[data-v-salesteambrisbaneitem-member_position] | innerHTML = <?php echo isset($item["designation"]) ? $item["designation"] : ''; ?>
[data-v-salesteambrisbane-items] | append = <?php } ?>

div[data-v-salesteambrisbane-component-link] | if_exists = $is_admin

#book-now | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$book_now = $current_component = $this->_component['booknow']?? [];
	// echo '<pre>';
	// print_r($book_now);
	// echo '</pre>';
    // $members = array_merge($sales_team_brisbane['items'], $sales_team_melbourne['items'], $sales_team_sydney['items']);

	$members = isset($book_now['members']['members']) ? $book_now['members']['members'] : [];
	$locations = isset($book_now['members']['locations']) ? $book_now['members']['locations']: [];
	$sales_teams = isset($book_now['members']['sales_teams']) ? $book_now['members']['sales_teams']: [];
	// echo '<pre>';
	// print_r($sales_teams);
	// echo '</pre>';
	$first_location = isset($locations[0]) ? $locations[0] : [];
	// echo '<pre>';
	// print_r($locations);
	// echo '</pre>';
 
?>

[data-v-component-booknow] [data-v-booknow-*]|innerText = $book_now['@@__data-v-booknow-(*)__@@']
[data-v-booknow-component-link] a | href = <?php echo isset($book_now['component_link']) ? $book_now['component_link'] : ''; ?>
[data-v-booknow-section_title]|innerHTML = <?php echo isset($book_now['section_title']) ? $book_now['section_title'] : ''; ?>
img[data-v-booknow-member_image] | src = <?php echo isset($first_location['image']) ? $first_location['image'] : ''; ?>
p[data-v-booknow-name] | innerHTML = <?php echo isset($first_location['showroom_title']) ? $first_location['showroom_title'] : ''; ?>

#choose-members > option | deleteAllButFirst
#choose-members | prepend = <?php foreach ($sales_teams as $key => $member) { ?>
    option[data-v-booknow-member] | innerHTML = <?php echo isset($member["name"]) ? $member["name"] : ''; ?>
	option[data-v-booknow-member] | value = <?php echo isset($member["showroom_contact_id"]) ? $member["showroom_contact_id"] : ''; ?>
	option[data-v-booknow-member] | data-member-name = <?php echo isset($member["name"]) ? $member["name"] : ''; ?>
	option[data-v-booknow-member] | data-member-image = <?php echo isset($member["image"]) ? $member["image"] : ''; ?>
#choose-members | append = <?php } ?>

#choose-location > option | deleteAllButFirst
#choose-location | prepend = <?php foreach ($locations as $key => $location) { ?>
    option[data-v-booknow-location] | innerHTML = <?php echo isset($location['showroom_title']) ? $location['showroom_title'] : ''; ?>
	option[data-v-booknow-location] | value = <?php echo isset($location['showroom_id']) ? $location['showroom_id'] : ''; ?>
	option[data-v-booknow-location] | data-map-link = <?php echo isset($location['google_map_link']) ? $location['google_map_link'] : ''; ?>
	option[data-v-booknow-location] | data-image = <?php echo isset($location['image']) ? $location['image'] : ''; ?>
	option[data-v-booknow-location] | data-address = <?php echo isset($location['showroom_address']) ? $location['showroom_address'] : ''; ?>
#choose-location | append = <?php } ?>



#contact-getin-touch | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$get_in_touch = $current_component = $this->_component['getintouch']?? [];
	$nonce = $this->parameters['nonce'] ?? '';
	// echo '<pre>';
	// print_r($sales_team_sydney);
	// echo '</pre>';
    $errors = $this->parameters['errors'] ?? [];
	$data = $this->parameters['data'] ?? [];

    $first_name_error = isset($errors['first_name']) ? true : false;
    $last_name_error = isset($errors['last_name']) ? true : false;
    $email_error = isset($errors['email']) ? true : false;
    $files_error = isset($errors['files']) ? true : false;

	// echo "<pre>";
	// print_r($errors);
	// echo "</pre>";
?>
[data-v-nonce] | value = <?php echo isset($nonce) ? $nonce : ''; ?>


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