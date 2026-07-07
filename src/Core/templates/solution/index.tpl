import(components/header.tpl, [data-v-component-header])

#hero-solution | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $herosolution = $current_component = $this->_component['herosolution']?? [];

    // echo '<pre>';
    // print_r($need_help);
    // echo '</pre>';
?>
[data-v-component-herosolution] [data-v-herosolution-*]|innerText = $herosolution['@@__data-v-herosolution-(*)__@@']


#solution-customizable | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
    $customizablesolution = $current_component = $this->_component['customizablesolution']?? [];

    // echo '<pre>';
    // print_r($customizablesolution);
    // echo '</pre>';
?>
[data-v-component-customizablesolution] [data-v-customizablesolution-*]|innerText = $customizablesolution['@@__data-v-customizablesolution-(*)__@@']


div[data-v-customizablesolution-list] > div[data-v-customizablesolution-item] | deleteAllButFirst
div[data-v-customizablesolution-list] | prepend = <?php if(isset($customizablesolution['items'])){ foreach ($customizablesolution['items'] as $item) { ?>
    img[data-v-customizablesolutionitem-image] | src = <?php echo $item['image']??""; ?>
    h6[data-v-customizablesolutionitem-title] | innerText = <?php echo $item['title']??""; ?>
    div[data-v-customizablesolutionitem-description] | innerHTML = <?php echo $item['description']??""; ?>
    a[data-v-customizablesolutionitem-link] | href = <?php echo isset($item["link"]) ? '/solutions' . $item["link"] : ''; ?>
div[data-v-customizablesolution-list] | append = <?php }} ?>

import(components/featured_product_masonry.tpl, [data-v-component-featureproductsmasonry])


#our-principle | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$ourprincipleicon = $current_component = $this->_component['ourprincipleicon']?? [];

	// echo '<pre>';
	// print_r($design_resources['items']);
	// echo '</pre>';

?>
[data-v-component-ourprincipleicon] [data-v-ourprincipleicon-*]|innerText = $ourprincipleicon['@@__data-v-ourprincipleicon-(*)__@@']
[data-v-component-ourprincipleicon] [data-v-ourprincipleicon-section_title]|innerHTML = <?php echo $ourprincipleicon['section_title']; ?>
[data-v-component-ourprincipleicon] [data-v-ourprincipleicon-section_subtitle]|innerHTML = <?php echo $ourprincipleicon['section_subtitle']; ?>

.principle-list .principle-item | deleteAllButFirst
.principle-list | prepend = <?php if(isset($ourprincipleicon['items'])){ foreach ($ourprincipleicon['items'] as $item) { ?>
img[data-v-ourprincipleiconitem-image] | src = <?php echo $item['image']; ?>
h5[data-v-ourprincipleiconitem-title] | innerHTML = <?php echo $item['title']; ?>
p[data-v-ourprincipleiconitem-description] | innerHTML = <?php echo $item['description']; ?>
.principle-list | append = <?php }} ?>


#about-who-you | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$videogallerywhoweare = $current_component = $this->_component['videogallerywhoweare']?? [];
	// echo '<pre>';
	// print_r($videogallerywhoweare);
	// echo '</pre>';
?>
[data-v-gallery-whoweare] | prepend = <?php echo 'let whoWeAreData = ' . json_encode($videogallerywhoweare); ?>


import(components/project_slider.tpl, [data-v-component-featuredprojectslider])

import(components/blogs.tpl, [data-v-component-blogslider])

import(components/needhelp.tpl, [data-v-component-needhelp])


import(components/footer.tpl, [data-v-component-footer])


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