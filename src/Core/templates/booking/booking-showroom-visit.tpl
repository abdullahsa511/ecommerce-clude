import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#booking-showroom-visit | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$rawComponent = $this->_component['bookingshowroomvisit'] ?? [];

	 // echo '<pre>';
	 // print_r($rawComponent);
	 // echo '</pre>';

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
	$consultantEmail = isset($showroomVisit['email']) ? $showroomVisit['email'] : '';
	$showroomPhoto = isset($showroomVisit['image']) ? $showroomVisit['image'] : '';
	$showroomPhone = isset($showroomVisit['showroom_phone']) ? $showroomVisit['showroom_phone'] : '';
	
	$isExistVirtualAppointment = false;
	$isExistPhysicalTour = false;
	$tourTypeLabel = '';
	if ($tourType === 'virtualTour') {
		$isExistVirtualAppointment = true;
		$tourTypeLabel = 'Online Appointment';
	} elseif ($tourType === 'physicalTour') {
		$isExistPhysicalTour = true;
		$tourTypeLabel = 'Physical Tour';
	}

	// echo '<pre>';
	// print_r($tourTypeLabel);
	// echo '</pre>';

?>
a[data-v-bookingshowroomvisit-consultant-name] | innerText = <?php echo htmlspecialchars($consultantName); ?>
a[data-v-bookingshowroomvisit-consultant-designation] | innerText = <?php echo htmlspecialchars($consultantDesignation); ?>
a[data-v-bookingshowroomvisit-consultant-phone] | innerText = <?php echo htmlspecialchars($showroomPhone); ?>
a[data-v-bookingshowroomvisit-consultant-email] | innerText = <?php echo htmlspecialchars($consultantEmail); ?>
img[data-v-bookingshowroomvisit-consultant-photo] | src = <?php echo htmlspecialchars($showroomPhoto); ?>

span[data-v-bookingshowroomvisit-item-count] | innerText = <?php echo htmlspecialchars($itemCount); ?>
span[data-v-bookingshowroomvisit-total-price] | innerText = <?php echo htmlspecialchars($totalAmount); ?>
span[data-v-bookingshowroomvisit-project-name] | innerText = <?php echo htmlspecialchars($projectName); ?>
p[data-v-bookingshowroomvisit-showroom-name] | innerHTML = <?php echo htmlspecialchars($showroomTitle); ?>
a[data-v-bookingshowroomvisit-showroom-address] | innerHTML = <?php echo htmlspecialchars($showroomAddr); ?>
p[data-v-bookingshowroomvisit-tour-type] | innerHTML = <?php echo htmlspecialchars($tourTypeLabel); ?>
p[data-v-bookingshowroomvisit-meeting-time] | innerHTML = <?php echo htmlspecialchars($showroomMeetingTimeLabel); ?>
p[data-v-bookingshowroomvisit-showroom-date] | innerHTML = <?php echo htmlspecialchars($showroomDate); ?>
p[data-v-bookingshowroomvisit-time-zone] | innerHTML = <?php echo htmlspecialchars($timeZone); ?>
a[data-v-bookingshowroomvisit-calendar] | data-pinboard-id = <?php echo htmlspecialchars($pinboardId); ?>
a[data-v-bookingshowroomvisit-calendar] | data-visit-showroom-id = <?php echo htmlspecialchars($showroomVisitId); ?>
a[data-v-bookingshowroomvisit-calendar] | data-meeting-time = <?php echo htmlspecialchars($showroomMeetingTime); ?>
a[data-v-bookingshowroomvisit-calendar] | data-time-zone = <?php echo htmlspecialchars($timeZone); ?>
a[data-v-bookingshowroomvisit-calendar] | data-visit-showroom-date = <?php echo htmlspecialchars(date('M d,Y', strtotime($showroomDate))); ?>
a[data-v-bookingshowroomvisit-calendar] | data-project-name = <?php echo htmlspecialchars($projectName); ?>
a[data-v-bookingshowroomvisit-calendar] | data-customer-email = <?php echo htmlspecialchars($consultantEmail); ?>
a[data-v-bookingshowroomvisit-calendar] | data-customer-name = <?php echo htmlspecialchars($consultantName); ?>
a[data-v-bookingshowroomvisit-calendar] | data-customer-phone = <?php echo htmlspecialchars($consultantPhone); ?>
a[data-v-bookingshowroomvisit-calendar] | data-map-link = <?php echo htmlspecialchars($showroomGoogleMapLink); ?>
a[data-v-bookingshowroomvisit-calendar] | data-tour-type = <?php echo htmlspecialchars($tourType); ?>
a[data-v-bookingshowroomvisit-calendar] | data-guest-email = <?php echo htmlspecialchars($customerEmail); ?>
a[data-v-bookingshowroomvisit-calendar] | data-location = <?php echo htmlspecialchars($showroomAddr); ?>
a[data-v-bookingshowroomvisit-calendar] | data-showroom = <?php echo htmlspecialchars($showroomTitle); ?>
a[data-v-bookingshowroomvisit-note-id] | data-showroom-visit-id = <?php echo htmlspecialchars($showroomVisitId); ?>
textarea[data-v-bookingshowroomvisit-note] | innerText = <?php echo htmlspecialchars($showroomVisit['note'] ?? ''); ?>
#physical-location-info | if_exists = $isExistPhysicalTour
#virtual-meeting-info | if_exists = $isExistVirtualAppointment
