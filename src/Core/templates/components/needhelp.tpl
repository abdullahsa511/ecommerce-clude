#need-help-section | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$need_help = $current_component = $this->_component['needhelp']?? [];

	// echo '<pre>';
	// print_r($need_help);
	// echo '</pre>';

	$is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>
[data-v-component-needhelp] [data-v-needhelp-section_title] | innerHTML = <?php echo isset($need_help['section_title']) ? $need_help['section_title'] : ''; ?>
[data-v-component-needhelp] [data-v-needhelp-section_subtitle] | innerHTML = <?php echo isset($need_help['section_subtitle']) ? $need_help['section_subtitle'] : ''; ?>
div[data-v-needhelp-wrapper] | class = <?php echo isset($need_help['section_class']) ? $need_help['section_class'] : ''; ?>
div[data-v-needhelp-component-link] > a | href = <?php echo isset($need_help['component_link']) ? $need_help['component_link'] : ''; ?>

[data-v-component-needhelp] [data-v-needhelp-wrapper] > div.th-item-help | deleteAllButFirst

[data-v-component-needhelp] [data-v-needhelp-wrapper] | prepend = <?php if(isset($need_help['items'])){ foreach ($need_help['items'] as $blog) { ?>

a[data-v-needhelpitem-icon-title-url] | href = <?php echo isset($blog["link"]) ? $blog["link"] : ''; ?>
i[data-v-needhelpitem-icon] | class = <?php echo isset($blog["icon"]) ? $blog["icon"] : ''; ?>
h6[data-v-needhelpitem-title] | innerHTML = <?php echo isset($blog["title"]) ? $blog["title"] : ''; ?>
p[data-v-needhelpitem-description] | innerHTML = <?php echo isset($blog["description"]) ? $blog["description"] : ''; ?>
a[data-v-needhelpitem-link] | innerHTML = <?php echo isset($blog["link_text"]) ? $blog["link_text"] : ''; ?>
a[data-v-needhelpitem-link] | href = <?php echo isset($blog["link"]) ? $blog["link"] : ''; ?>
a[data-v-needhelpitem-url] | href = <?php echo isset($blog["link"]) ? $blog["link"] : ''; ?>

[data-v-component-needhelp] [data-v-needhelp-wrapper] | append = <?php }} ?>

div[data-v-needhelp-component-link] | if_exists = $is_admin