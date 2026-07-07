[data-v-component-site]|prepend = <?php
	if (isset($_site_idx)) $_site_idx++; else $_site_idx = 0;
	$previous_component = isset($component)?$component:null;
	$site = $component = $this->_component['site']?? [];
?>

[data-v-component-site] [data-v-site-*]|innerText = $site['@@__data-v-site-(*)__@@']
h1[data-v-title]|innerText = $title
div[data-v-message]|innerText = $message

// example <div data-v-copy-from="index.html,#element">
[data-v-copy-from]|outerHTML = from(@@__data-v-copy-from:([^\,]+)__@@|@@__data-v-copy-from:[^\,]+\,([^\,]+)__@@)


[data-v-if-showmessage]|before = <?php 
$showmessage = isset($message) && !empty($message) && (!isset($errors) || (isset($errors) && empty($errors)));
if(isset($showmessage) && $showmessage){ 
?>
[data-v-if-showmessage]|after = <?php } ?>
[data-v-if-showerrors]|before = <?php 
$showerrors = isset($errors) && !empty($errors);
if(isset($showerrors) && $showerrors){ 
?>
[data-v-if-showerrors]|after = <?php } ?>


input[name="csrf_token"] | value = $csrf_token

div[data-v-errors]|innerText = <?php echo implode(', ', $errors); ?>