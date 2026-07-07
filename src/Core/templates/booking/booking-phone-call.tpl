import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#booking-phone-call-app | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$bookingphonecall = $current_component = $this->_component['bookingphonecall']?? [];

	// echo '<pre>';
	// print_r($bookingphonecall);
	// echo '</pre>';
	
?>

h1[data-v-bookingphonecall-title] | innerText = <?php echo isset($bookingphonecall['section_title']) ? $bookingphonecall['section_title'] : ''; ?>
p[data-v-bookingphonecall-subtitle] | innerText = <?php echo isset($bookingphonecall['section_subtitle']) ? $bookingphonecall['section_subtitle'] : ''; ?>

h2[data-v-bookingphonecall-pinboard-name] | innerText = <?php echo isset($bookingphonecall['pinboard_name']) ? $bookingphonecall['pinboard_name'] : ''; ?>
p[data-v-bookingphonecall-pinboard-created-at] | innerText = <?php echo isset($bookingphonecall['pinboard_created_at']) ? $bookingphonecall['pinboard_created_at'] . " - " : ''; ?>
span[data-v-submissionconfirmation-pinboard-created-at-time] | innerText = <?php echo isset($bookingphonecall['pinboard_created_at_time']) ? $bookingphonecall['pinboard_created_at_time'] : ''; ?>
span[data-v-bookingphonecall-pinboard-item-count] | innerText = <?php echo isset($bookingphonecall['pinboard_item_count']) ? $bookingphonecall['pinboard_item_count'] : ''; ?>
input[data-v-bookingphonecall-pinboard-uuid] | value = <?php echo isset($bookingphonecall['pinboard_uuid']) ? $bookingphonecall['pinboard_uuid'] : ''; ?>
input[data-v-bookingphonecall-nonce] | value = <?php echo isset($bookingphonecall['nonce']) ? $bookingphonecall['nonce'] : ''; ?>
div[data-v-bookingphonecall-feedback] | innerText = <?php echo isset($bookingphonecall['feedback_text']) ? $bookingphonecall['feedback_text'] : ''; ?>

div[data-v-bookingphonecall-pinboard-items] > div[data-v-bookingphonecall-pinboard-item] | deleteAllButFirst
div[data-v-bookingphonecall-pinboard-items] | prepend = <?php foreach (($bookingphonecall['pinboard_items'] ?? []) as $key => $item) { ?>
div[data-v-bookingphonecall-pinboard-item] img[data-v-bookingphonecall-pinboard-item-image] | src = <?php echo isset($item['photo']) ? $item['photo'] : ''; ?>
a[data-v-bookingphonecall-pinboard-item-name] | innerText = <?php echo isset($item['title']) && $item['title'] !== '' ? $item['title'] : ($item['description'] ?? ''); ?>
a[data-v-item-link-url] | href = <?php echo isset($item['product_url']) ? $item['product_url'] : ''; ?>
p[data-v-bookingphonecall-pinboard-item-description] | innerText = <?php echo isset($item['model_type']) ? $item['model_type'] : ''; ?>
div[data-v-bookingphonecall-pinboard-items] | append = <?php } ?>
