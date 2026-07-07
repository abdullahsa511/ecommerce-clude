import(components/header.tpl, [data-v-component-header])


#th-catalogue-confirmation | before = <?php 
    if(isset($current_component)) $previous_component = $current_component;
	$confirmation = $current_component = $this->_component['catalogueconfirmation']?? [];
   
    // echo "<pre>";
    // print_r($confirmation);
    // echo "</pre>";

    $show_contact_us_get_touch_section = isset($confirmation['form_type']) && $confirmation['form_type'] === 'contact-us' ? true : false;
   
    $show_physical_catalogue_request_section = isset($confirmation['form_type']) && $confirmation['form_type'] === 'physical_catalogue' ? true : false;

    $show_online_catalogue_request_section = isset($confirmation['form_type']) && $confirmation['form_type'] === 'online_catalogue' ? true : false;

    $catalogue_format_title = isset($confirmation['catalogue_format']) && $confirmation['catalogue_format'] ? $confirmation['catalogue_format'] : '';

    // Properly initialize to avoid undefined warnings


   
    // echo "<pre>";
    // print_r($show_digital_catalogue_section);
    // echo "</pre>";
    // echo "<pre>";
    // print_r($show_catalogue_request_section);
    // echo "</pre>";

?>


div[data-v-catalogueconfirmation-media] | innerHTML = <?php 
    $mediaUrl = isset($confirmation['image']) && !empty($confirmation['image']) ? $confirmation['image'] : '/media/catalogue/catalogue-confirmation/confirm1.png';
    $mediaUrlEscaped = htmlspecialchars($mediaUrl, ENT_QUOTES, 'UTF-8');
    $ext = strtolower(pathinfo($mediaUrl, PATHINFO_EXTENSION));
    if ($ext === 'pdf') {
        echo '<div class="catalogue-confirmation-pdf-wrapper" style="height: 300px;">';
        echo '<img src="/media/design-resource/icons/pdf.png" alt="Catalogue Confirmation">';
        echo '</div>';
        // echo '<iframe src="' . $mediaUrlEscaped . '" class="catalogue-confirmation-pdf" style="width:100%;height:100%;border:none;" title="Catalogue PDF"></iframe>';
    // }else if($ext === 'zip'){
    //     echo '<div class="catalogue-confirmation-pdf-wrapper" style="height: 300px;">';
    //     echo '<img src="/media/design-resource/icons/zip.png" alt="Catalogue Confirmation">';
    //     echo '</div>';
    // }else if($ext === 'docx'){
    //     echo '<div class="catalogue-confirmation-pdf-wrapper" style="height: 300px;">';
    //     echo '<img src="/media/design-resource/icons/docx.png" alt="Catalogue Confirmation">';
    //     echo '</div>';
    } else {
        // echo '<img src="' . $mediaUrlEscaped . '" alt="Catalogue Confirmation">';
        if(!empty($confirmation['confirm_left_image'])){
            echo '<img src="' . $confirmation['confirm_left_image'] . '" alt="Catalogue Confirmation">';
        }else{
            echo '<img src="/media/catalogue/catalogue-confirmation/confirm1.png" alt="Catalogue Confirmation">';
        }
    }
    $is_admin = isset($this->parameters['is_admin']) ? !!$this->parameters['is_admin'] : false;
?>

div[data-v-catalogueconfirmation-component-link] > a | href = <?php echo isset($confirmation['component_link']) ? $confirmation['component_link'] : ''; ?>

div[data-v-catalogueconfirmation-title] | innerText = <?php echo isset($confirmation['request_type']) ? $confirmation['request_type'] : "Catalogue Request"; ?>
h3[data-v-catalogueconfirmation-catalogue-title] | innerText = <?php echo $catalogue_format_title; ?>
span[data-v-catalogueconfirmation-first_name_last_name] | innerHTML = <?php echo isset($confirmation['first_name']) || isset($confirmation['last_name']) ? $confirmation['first_name'] . " " . $confirmation['last_name'] : ""; ?>
span[data-v-catalogueconfirmation-company_name] | innerHTML = <?php echo isset($confirmation['company']) ? $confirmation['company'] : ""; ?>
span[data-v-catalogueconfirmation-email_address] | innerHTML = <?php echo isset($confirmation['email']) ? $confirmation['email'] : ""; ?>
span[data-v-catalogueconfirmation-source] | innerHTML = <?php echo isset($confirmation['source']) ? $confirmation['source'] : ""; ?>
span[data-v-catalogueconfirmation-digital-first_name] | innerHTML = <?php echo isset($confirmation['first_name']) ? $confirmation['first_name'] : ""; ?>
span[data-v-catalogueconfirmation-digital-email_address] | innerHTML = <?php echo isset($confirmation['email']) ? $confirmation['email'] : ""; ?>
div[data-v-catalogueconfirmation-notes_text] | innerHTML = <?php echo isset($confirmation['content']) ? $confirmation['content'] : ""; ?>
span[data-v-catalogueconfirmation-project_details] | innerHTML = <?php echo isset($confirmation['project_details']) ? $confirmation['project_details'] : ""; ?>
span[data-v-catalogueconfirmation-state] | innerHTML = <?php echo isset($confirmation['state']) ? $confirmation['state'] : ""; ?>

div[data-v-catalogueconfirmation-contact-us-get-touch-section] | if_exists = $show_contact_us_get_touch_section

div[data-v-catalogueconfirmation-catalogue-request-section] | if_exists = $show_physical_catalogue_request_section 
div[data-v-catalogueconfirmation-digital-catalogue-section] | if_exists = $show_online_catalogue_request_section 
div[data-v-catalogueconfirmation-component-link] | if_exists = $is_admin

import(components/needhelp.tpl, [data-v-component-needhelp])
import(components/footer.tpl, [data-v-component-footer])