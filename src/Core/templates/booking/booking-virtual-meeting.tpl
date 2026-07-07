import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#booking-virtual-meeting | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$bookingvirtualmeeting = $current_component = $this->_component['bookingvirtualmeeting']?? [];


	$rawComponent = $this->_component['bookingvirtualmeeting'] ?? [];

	if (is_object($rawComponent) || is_array($rawComponent)) {
		$bookingshowroomvisit = json_decode(json_encode($rawComponent), true);
	} else {
		$bookingshowroomvisit = [];
	}
	
	$current_component = $bookingshowroomvisit;

	$pinboard = $bookingshowroomvisit['pinboard'] ?? [];
	$pinboard = is_array($pinboard) ? $pinboard : [];
	
	$showroomVisit = isset($pinboard['showroom_visit']) ? $pinboard['showroom_visit'] : [];
	$showroomVisit = is_array($showroomVisit) ? $showroomVisit : [];

	$showroomVisitId = isset($showroomVisit['visit_showroom_id']) ? $showroomVisit['visit_showroom_id'] : '';
	$itemCount      = isset($pinboard['count_items']) ? $pinboard['count_items'] : '';
	$totalAmount    = isset($pinboard['total_amount']) ? $pinboard['total_amount'] : '';
	$projectName    = isset($pinboard['project_name']) ? $pinboard['project_name'] : '';
	$pinboardId     = isset($pinboard['pinboard_id']) ? $pinboard['pinboard_id'] : '';
	$customerEmail  = isset($pinboard['customer_email']) ? $pinboard['customer_email'] : '';
	$customerName   = isset($pinboard['customer_name']) ? $pinboard['customer_name'] : '';
	$customerPhone  = isset($pinboard['customer_phone']) ? $pinboard['customer_phone'] : '';
	$showroomGoogleMapLink = isset($showroomVisit['google_map_link']) ? $showroomVisit['google_map_link'] : '';
	$showroomTitle  = isset($showroomVisit['title']) ? $showroomVisit['title'] : (isset($showroomVisit['showroom_id']) && $showroomVisit['showroom_id'] == 4 ? 'Online' : '');
	$showroomAddr   = isset($showroomVisit['address']) ? $showroomVisit['address'] : '';
	$showroomDate   = isset($showroomVisit['date']) ? $showroomVisit['date'] : '';
	$timeZone   = isset($showroomVisit['time_zone']) ? $showroomVisit['time_zone'] : '';
	$showroomMeetingTime   = isset($showroomVisit['meeting_time']) ? $showroomVisit['meeting_time'] : '';
	$showroomMeetingTimeLabel   = isset($showroomVisit['meeting_time_label']) ? $showroomVisit['meeting_time_label'] : '';
	$tourType       = isset($showroomVisit['tour_type']) ? $showroomVisit['tour_type'] : '';

	$consultantName = isset($showroomVisit['name']) ? $showroomVisit['name'] : '';
	$consultantDesignation = isset($showroomVisit['designation']) ? $showroomVisit['designation'] : '';
	$consultantPhone = isset($showroomVisit['phone']) ? $showroomVisit['phone'] : '';
	$showroomPhone = isset($showroomVisit['showroom_phone']) ? $showroomVisit['showroom_phone'] : '';
	$consultantEmail = isset($showroomVisit['email']) ? $showroomVisit['email'] : '';
	$showroomPhoto = isset($showroomVisit['image']) ? $showroomVisit['image'] : '';

	// echo '<pre>';
	// print_r($showroomPhone);
	// echo '</pre>';

?>

a[data-v-bookingvirtualmeeting-consultant-name] | innerText = <?php echo htmlspecialchars($consultantName); ?>
a[data-v-bookingvirtualmeeting-consultant-designation] | innerText = <?php echo htmlspecialchars($consultantDesignation); ?>
a[data-v-bookingvirtualmeeting-consultant-phone] | innerText = <?php echo htmlspecialchars($showroomPhone); ?>
a[data-v-bookingvirtualmeeting-consultant-email] | innerText = <?php echo htmlspecialchars($consultantEmail); ?>
img[data-v-bookingvirtualmeeting-consultant-photo] | src = <?php echo htmlspecialchars($showroomPhoto); ?>

div[data-v-virtual-meeting-info] | data-pinboard-id = <?php echo $pinboardId??''; ?>
div[data-v-virtual-meeting-info] | data-project-name = <?php echo $projectName??''; ?>
div[data-v-virtual-meeting-info] | data-customer-email = <?php echo $customerEmail??''; ?>
div[data-v-virtual-meeting-info] | data-customer-name = <?php echo $customerName??''; ?>
div[data-v-virtual-meeting-info] | data-customer-phone = <?php echo $customerPhone??''; ?>

span[data-v-bookingvirtualmeeting-item-count] | innerText = <?php echo $itemCount??''; ?>
span[data-v-bookingvirtualmeeting-total-price] | innerText = <?php echo $totalAmount??''; ?>
span[data-v-bookingvirtualmeeting-project-name] | innerText = <?php echo $projectName??''; ?>

p[data-v-bookingvirtualmeeting-meeting-time] | innerHTML = <?php echo htmlspecialchars($showroomMeetingTimeLabel); ?>
p[data-v-bookingvirtualmeeting-showroom-date] | innerHTML = <?php echo htmlspecialchars($showroomDate); ?>
p[data-v-bookingvirtualmeeting-time-zone] | innerHTML = <?php echo htmlspecialchars($timeZone); ?>

a[data-v-bookingvirtualmeeting-calendar] | data-pinboard-id = <?php echo htmlspecialchars($pinboardId); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-visit-showroom-id = <?php echo htmlspecialchars($showroomVisitId); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-meeting-time = <?php echo htmlspecialchars($showroomMeetingTime); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-time-zone = <?php echo htmlspecialchars($timeZone); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-visit-showroom-date = <?php echo htmlspecialchars(date('M d,Y', strtotime($showroomDate))); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-project-name = <?php echo htmlspecialchars($projectName); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-customer-email = <?php echo htmlspecialchars($consultantEmail); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-customer-name = <?php echo htmlspecialchars($consultantName); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-customer-phone = <?php echo htmlspecialchars($consultantPhone); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-map-link = <?php echo htmlspecialchars($showroomGoogleMapLink); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-tour-type = <?php echo htmlspecialchars($tourType); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-guest-email = <?php echo htmlspecialchars($customerEmail); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-location = <?php echo htmlspecialchars($showroomAddr); ?>
a[data-v-bookingvirtualmeeting-calendar] | data-showroom = <?php echo htmlspecialchars($showroomTitle); ?>
textarea[data-v-bookingvirtualmeeting-note] | innerText = <?php echo htmlspecialchars($showroomVisit['note'] ?? ''); ?>