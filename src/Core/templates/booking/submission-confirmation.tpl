import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#booking-submissionconfirmation | before = <?php 
if(isset($current_component)) $previous_component = $current_component;

$submissionconfirmation = $current_component = $this->_component['submissionconfirmation'] ?? [];
// echo '<pre>';
// print_r($submissionconfirmation['pinboard_items']);
// echo '</pre>';
?>
h1[data-v-submissionconfirmation-title] | innerText = <?php echo isset($submissionconfirmation['section_title']) ? $submissionconfirmation['section_title'] : ''; ?>
p[data-v-submissionconfirmation-subtitle] | innerText = <?php echo isset($submissionconfirmation['section_subtitle']) ? $submissionconfirmation['section_subtitle'] : ''; ?>

h2[data-v-submissionconfirmation-pinboard-name] | innerText = <?php echo isset($submissionconfirmation['pinboard_name']) ? $submissionconfirmation['pinboard_name'] : ''; ?>
p[data-v-submissionconfirmation-pinboard-created-at] | innerText = <?php echo isset($submissionconfirmation['pinboard_created_at']) ? $submissionconfirmation['pinboard_created_at'] . " - " : ''; ?>
span[data-v-submissionconfirmation-pinboard-created-at-time] | innerText = <?php echo isset($submissionconfirmation['pinboard_created_at_time']) ? $submissionconfirmation['pinboard_created_at_time'] : ''; ?>
span[data-v-submissionconfirmation-pinboard-item-count] | innerText = <?php echo isset($submissionconfirmation['pinboard_item_count']) ? $submissionconfirmation['pinboard_item_count'] : ''; ?>

div[data-v-submissionconfirmation-pinboard-items] > div[data-v-submissionconfirmation-pinboard-item] | deleteAllButFirst
div[data-v-submissionconfirmation-pinboard-items] | prepend = <?php foreach (($submissionconfirmation['pinboard_items'] ?? []) as $key => $item) { ?>
div[data-v-submissionconfirmation-pinboard-item] img[data-v-submissionconfirmation-pinboard-item-image] | src = <?php echo isset($item['photo']) ? $item['photo'] : ''; ?>
a[data-v-submissionconfirmation-pinboard-item-name] | innerText = <?php echo isset($item['title']) && $item['title'] !== '' ? $item['title'] : ($item['description'] ?? ''); ?>
a[data-v-submissionconfirmation-pinboard-item-name] | href = <?php echo isset($item['product_url']) ? $item['product_url'] : ''; ?>
p[data-v-submissionconfirmation-pinboard-item-description] | innerText = <?php echo isset($item['model_type']) ? $item['model_type'] : ''; ?>
div[data-v-submissionconfirmation-pinboard-items] | append = <?php } ?>