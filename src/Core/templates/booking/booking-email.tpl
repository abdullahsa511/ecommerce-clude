import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#booking-email | before = <?php 
if(isset($current_component)) $previous_component = $current_component;

$bookingemail = $current_component = $this->_component['bookingemail'] ?? [];
$pinboard_items = $bookingemail['pinboard_items'] ?? [];
$existing_service_request = $pinboard_items['service_request'] ?? [];
$note_content       = $existing_service_request['content'] ?? '';
$comment_attachment = $existing_service_request['comment_attachment'] ?? [];
$comment_photos     = $pinboard_items['comment_photos'] ?? [];

$uuid = $this->parameters['uuid'] ?? '';

// echo '<pre>';
// print_r($bookingemail);
// echo '</pre>';
?>

span[data-v-bookingemail-item-count] | innerText = <?php echo isset($pinboard_items['count_items']) ? $pinboard_items['count_items'] . ' items' : ''; ?>
span[data-v-bookingemail-total-price] | innerText = <?php echo isset($pinboard_items['total_amount']) ? $pinboard_items['total_amount'] : ''; ?>
span[data-v-bookingemail-project-name] | innerText = <?php echo isset($pinboard_items['project_name']) ? $pinboard_items['project_name'] : ''; ?>

div[data-v-bookingemail-existing] | data-comment-attachment = <?php echo htmlspecialchars(json_encode($comment_attachment)); ?>
div[data-v-bookingemail-existing] | data-service-request-id = <?php echo $existing_service_request['service_request_id'] ?? ''; ?>

input[data-v-bookingemail-pinboard-input] | data-pinboard-id = <?php echo isset($pinboard_items['pinboard_id']) ? $pinboard_items['pinboard_id'] : ''; ?>
input[data-v-bookingemail-pinboard-input] | value = <?php echo $pinboard_items['customer_email'] ?? ''; ?>

div[data-v-bookingemail-note-content] | innerHTML = <?php echo $note_content; ?>

select[data-v-bookingemail-attachments] > option[data-v-bookingemail-attachments-option] | deleteAllButFirst

select[data-v-bookingemail-attachments] | foreach = <?php foreach ($comment_photos as $photo) { ?>
option[data-v-bookingemail-attachments-option] | value = <?php echo $photo['photo_url'] ?? ''; ?>
option[data-v-bookingemail-attachments-option] | innerText = <?php echo $photo['photo_name'] ?? ''; ?>
select[data-v-bookingemail-attachments] | append = <?php } ?>
