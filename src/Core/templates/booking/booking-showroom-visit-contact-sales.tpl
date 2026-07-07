import(components/head.tpl, [data-v-component-head])
import(components/header.tpl, [data-v-component-header])
#booking-showroom-visit-contact-sales | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$rawComponent = $this->_component['bookingshowroomvisitcontactsales'] ?? [];

	// echo '<pre>';
	// print_r($rawComponent);
	// echo '</pre>';

	if (is_object($rawComponent) || is_array($rawComponent)) {
		$bookingshowroomvisit = json_decode(json_encode($rawComponent), true);
	} else {
		$bookingshowroomvisit = [];
	}
	
	$current_component = $bookingshowroomvisit;

	// $pinboard = isset($bookingshowroomvisit['visit_showroom']) ? $bookingshowroomvisit['visit_showroom'] : [];
	// $pinboard = is_array($pinboard) ? $pinboard : [];
	
	$showroomVisit = isset($bookingshowroomvisit['visit_showroom']) ? $bookingshowroomvisit['visit_showroom'] : [];
	$showroomVisit = is_array($showroomVisit) ? $showroomVisit : [];

	// echo '<pre>';
	// print_r($showroomVisit);
	// echo '</pre>';

	$pinboardId = isset($showroomVisit['pinboard_id']) ? $showroomVisit['pinboard_id'] : '';
	$showroomVisitId = isset($showroomVisit['visit_showroom_id']) ? $showroomVisit['visit_showroom_id'] : '';
	$uuid = isset($showroomVisit['uuid']) ? $showroomVisit['uuid'] : '';
	$showroomId = isset($showroomVisit['showroom_id']) ? $showroomVisit['showroom_id'] : '';
	$showroomContactId = isset($showroomVisit['showroom_contact_id']) ? $showroomVisit['showroom_contact_id'] : '';
	// echo '<pre>';
	// print_r($showroomContactId);
	// echo '</pre>';
	$customerId = isset($showroomVisit['customer_id']) ? $showroomVisit['customer_id'] : '';
	// customer details
	$customerEmail  = isset($showroomVisit['customer_email']) ? $showroomVisit['customer_email'] : '';

	// echo '<pre>';
	// print_r($showroomVisit);
	// echo '</pre>';
	// exit;


	$customerName   = isset($showroomVisit['customer_name']) ? $showroomVisit['customer_name'] : '';
	$customerPhone  = isset($showroomVisit['customer_phone']) ? $showroomVisit['customer_phone'] : '';
	$showroomGoogleMapLink = isset($showroomVisit['google_map_link']) ? $showroomVisit['google_map_link'] : '';
	// showroom details
	$showroomTitle  = isset($showroomVisit['title']) ? $showroomVisit['title'] : (isset($showroomVisit['showroom_id']) && $showroomVisit['showroom_id'] == 4 ? 'Online' : '');
	$showroomAddr   = isset($showroomVisit['address']) ? $showroomVisit['address'] : '';
	$showroomDate   = isset($showroomVisit['date']) ? $showroomVisit['date'] : '';
	$timeZone   = isset($showroomVisit['time_zone']) ? $showroomVisit['time_zone'] : '';
	$showroomMeetingTime   = isset($showroomVisit['meeting_time']) ? $showroomVisit['meeting_time'] : '';
	$showroomMeetingTimeLabel   = isset($showroomVisit['meeting_time_label']) ? $showroomVisit['meeting_time_label'] : '';
	$tourType       = isset($showroomVisit['tour_type']) ? $showroomVisit['tour_type'] : '';
	
	// consultant details
	$consultantName = isset($showroomVisit['name']) ? $showroomVisit['name'] : '';
	$consultantDesignation = isset($showroomVisit['designation']) ? $showroomVisit['designation'] : '';
	$consultantPhone = isset($showroomVisit['phone']) ? $showroomVisit['phone'] : '';
	$consultantEmail = isset($showroomVisit['email']) ? $showroomVisit['email'] : '';
	$showroomPhoto = isset($showroomVisit['image']) ? $showroomVisit['image'] : '';
	$showroomPhone = isset($showroomVisit['showroom_phone']) ? $showroomVisit['showroom_phone'] : '';

	$itemCount = isset($showroomVisit['pinboard_item_count']) ? $showroomVisit['pinboard_item_count'] : '';
	$projectName = isset($showroomVisit['pinboard_name']) ? $showroomVisit['pinboard_name'] : '';

	$isExistVirtualAppointment = false;
	$isExistPhysicalTour = false;
	$tourTypeLabel = '';
	$pageTitle = '';
	$isExistProjectMeta = false;
	if ($tourType === 'virtualTour') {
		$isExistVirtualAppointment = true;
		$tourTypeLabel = 'Online Appointment';
		$pageTitle = 'Booking Confirmed';
	} elseif ($tourType === 'physicalTour') {
		$isExistPhysicalTour = true;
		$tourTypeLabel = 'Physical Tour';
		$pageTitle = 'Booking Confirmed';
	}elseif ($tourType === 'virtualMeeting') {
		$isExistVirtualAppointment = true;
		$isExistProjectMeta = true;
		$tourTypeLabel = 'Online Appointment';
		$pageTitle = 'Virtual Meeting';
	}

	if ($itemCount > 0) {
		$isExistProjectMeta = true;
	}

?>
h1[data-v-bookingshowroomvisitcontactsales-title] | innerText = <?php echo htmlspecialchars($pageTitle); ?>

span[data-v-bookingshowroomvisitcontactsales-item-count] | innerText = <?php echo htmlspecialchars($itemCount); ?>
span[data-v-bookingshowroomvisitcontactsales-project-name] | innerText = <?php echo htmlspecialchars($projectName); ?>

div[data-v-bookingshowroomvisitcontactsales-consultant-name] | innerText = <?php echo htmlspecialchars($consultantName); ?>
a[data-v-bookingshowroomvisitcontactsales-consultant-designation] | innerText = <?php echo htmlspecialchars($consultantDesignation); ?>
a[data-v-bookingshowroomvisitcontactsales-consultant-phone] | innerText = <?php echo htmlspecialchars($showroomPhone); ?>
a[data-v-bookingshowroomvisitcontactsales-consultant-email] | innerText = <?php echo htmlspecialchars($consultantEmail); ?>

a[data-v-bookingshowroomvisitcontactsales-consultant-email] | data-member-email = <?php echo htmlspecialchars($consultantEmail); ?>
a[data-v-bookingshowroomvisitcontactsales-consultant-email] | data-member-name = <?php echo htmlspecialchars($consultantName); ?>
a[data-v-bookingshowroomvisitcontactsales-consultant-email] | data-member-phone = <?php echo htmlspecialchars($consultantPhone); ?>
a[data-v-bookingshowroomvisitcontactsales-consultant-email] | data-location = <?php echo htmlspecialchars($showroomTitle); ?>
a[data-v-bookingshowroomvisitcontactsales-consultant-email] | href = <?php echo 'mailto:' . htmlspecialchars($consultantEmail); ?>

img[data-v-bookingshowroomvisitcontactsales-consultant-photo] | src = <?php echo htmlspecialchars($showroomPhoto); ?>

p[data-v-bookingshowroomvisitcontactsales-showroom-name] | innerHTML = <?php echo htmlspecialchars($showroomTitle); ?>
a[data-v-bookingshowroomvisitcontactsales-showroom-address] | innerHTML = <?php echo htmlspecialchars($showroomAddr); ?>
a[data-v-bookingshowroomvisitcontactsales-showroom-address] | href = <?php echo htmlspecialchars($showroomGoogleMapLink); ?>
p[data-v-bookingshowroomvisitcontactsales-tour-type] | innerHTML = <?php echo htmlspecialchars($tourTypeLabel); ?>
p[data-v-bookingshowroomvisitcontactsales-meeting-time] | innerHTML = <?php echo htmlspecialchars($showroomMeetingTimeLabel); ?>
p[data-v-bookingshowroomvisitcontactsales-showroom-date] | innerHTML = <?php echo htmlspecialchars($showroomDate); ?>
p[data-v-bookingshowroomvisitcontactsales-time-zone] | innerHTML = <?php echo htmlspecialchars($timeZone); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-meeting-time = <?php echo htmlspecialchars($showroomMeetingTime); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-time-zone = <?php echo htmlspecialchars($timeZone); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-visit-showroom-date = <?php echo htmlspecialchars(date('M d,Y', strtotime($showroomDate))); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-map-link = <?php echo htmlspecialchars($showroomGoogleMapLink); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-tour-type = <?php echo htmlspecialchars($tourType); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-guest-email = <?php echo htmlspecialchars($customerEmail); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-guest-name = <?php echo htmlspecialchars($customerName); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-location = <?php echo htmlspecialchars($showroomAddr); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-showroom = <?php echo htmlspecialchars($showroomTitle); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-customer-email = <?php echo htmlspecialchars($consultantEmail); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-customer-name = <?php echo htmlspecialchars($consultantName); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-customer-phone = <?php echo htmlspecialchars($consultantPhone); ?>
a[data-v-bookingshowroomvisitcontactsales-note-id] | data-showroom-visit-id = <?php echo htmlspecialchars($showroomVisitId); ?>
textarea[data-v-bookingshowroomvisitcontactsales-note] | innerText = <?php echo htmlspecialchars($showroomVisit['note'] ?? ''); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-showroom-contact-id = <?php echo htmlspecialchars($showroomContactId); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-date = <?php echo htmlspecialchars(date('Y-m-d', strtotime($showroomDate))); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-showroom-id = <?php echo htmlspecialchars($showroomId); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-customer-id = <?php echo htmlspecialchars($customerId); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-visit-showroom-id = <?php echo htmlspecialchars($showroomVisitId); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-pinboard-id = <?php echo htmlspecialchars($pinboardId); ?>
a[data-v-bookingshowroomvisitcontactsales-calendar] | data-uuid = <?php echo htmlspecialchars($uuid); ?>
input[data-v-bookingshowroomvisitcontactsales-showroom-id] | value = <?php echo htmlspecialchars($showroomId); ?>
#physical-location-info | if_exists = $isExistPhysicalTour
#virtual-meeting-info | if_exists = $isExistVirtualAppointment
#project-meta-container | if_exists = $isExistProjectMeta