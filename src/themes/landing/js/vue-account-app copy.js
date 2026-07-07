// ---------------- show error function ----------------
function showError(input, message) {
  input.classList.add('is-invalid');
  // input.style.border = '1px solid red';
  let feedback = input.parentNode.querySelector('.invalid-feedback');
  if (!feedback) {
    feedback = document.createElement('div');
    feedback.className = 'invalid-feedback';
    input.parentNode.appendChild(feedback);
  }
  feedback.textContent = message;
}
// ---------------- end show error function ----------------

// ---------------- clear error function ----------------
function clearError(input) {
  input.classList.remove('is-invalid');
  // input.style.border = '1px solid #ced4da';
  const feedback = input.parentNode.querySelector('.invalid-feedback');
  if (feedback) feedback.remove();
}
// ---------------- end clear error function ----------------

// ---------------- validation rules function ----------------
function validateInput(input) {
  if (!input) return false;
  const val = input.value.trim();
  if (!val) {
    showError(input, 'This field is required');
    return false;
  }
  if (input.type === 'email') {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!re.test(val)) {
      showError(input, 'Enter a valid email address');
      return false;
    }
  }
  if (input.id === 'phone') {
    const numeric = val.replace(/[\s\-().]/g, '');
    if (!/^\+?\d{7,15}$/.test(numeric)) {
      showError(input, 'Enter a valid phone number');
      return false;
    }
  }
  return true;
}
// ---------------- end validation rules function ----------------
let dateTime = new Date().toISOString().replace(/[-:]/g, '').replace('.000Z','Z');
//###################### Order Tracking Start here ########################
document.addEventListener("DOMContentLoaded", async function () {
    console.log('Recent Orders App Loaded');
    const module = await import('/js/vue/account.js');
    const accountApp = module.default;
    let appContainer = document.getElementById('tracking-orders-list');

    // ---------------- track order button function ----------------
    const trackOrderButton = document.getElementById('trackOrderButton');
    trackOrderButton?.addEventListener('click', async function (event) {
      event.preventDefault();
      const orderNumberInput = document.getElementById('orderNumber');
      const orderNumber = orderNumberInput ? orderNumberInput.value : '';

      [orderNumberInput].forEach(function (inp) {
        if (!inp) return;
        inp.addEventListener('input', function () {
          clearError(inp);
        });
      });
      const fields = [orderNumberInput];
      let firstInvalid = null;
      const allValid = fields.every(function (f) {
        const ok = validateInput(f);
        if (!ok && !firstInvalid) firstInvalid = f;
        return ok;
      });

      if (!allValid) {
        if (firstInvalid && typeof firstInvalid.focus === 'function') firstInvalid.focus();
        return;
      }

      renderTrackingOrders(orderNumber);
    });
    // ---------------- end order track button function ----------------

    // ---------------- order track button function ----------------
    const orderTrackButton = document.querySelectorAll('.order-track-btn');
    orderTrackButton?.forEach(function (btn) {
      btn.addEventListener('click', async function () {
        const orderNumber = btn.getAttribute('data-order-id');
        // console.log('Track order for order:', orderNumber);
        const orderNumberInput = document.getElementById('orderNumber');
        orderNumberInput.value = orderNumber;
        renderTrackingOrders(orderNumber);

      });
    });
    // ---------------- end order track button function ----------------
    async function renderTrackingOrders(orderNumber) {

        // ---------------- call get tracking orders function ----------------
        if (!accountApp || typeof accountApp.getOrderTracking !== 'function') {
        throw new Error('accountApp or its getOrderTracking method is not available');
        }
        const response = await accountApp.getOrderTracking(appContainer, { orderNumber: orderNumber });
    
        if (response && response.error) {
        console.error('Error from accountApp:', response.error);
        }
    
    }

  });
//###################### Order Tracking End here ##########################


//###################### Accept Quote Start here ########################
document.addEventListener("DOMContentLoaded", async function () {
  const quoteTrackButton = document.querySelectorAll('.accept-quote-btn');
  quoteTrackButton?.forEach(function (btn) {
    btn.addEventListener('click', async function () {
      const quoteId = btn.getAttribute('data-quote-id');
      const acceptQuoteText = btn.querySelector('.accept-quote-text');
      if(!quoteId){
        return;
      }

    // ---------------- call get tracking orders function ----------------
    if (!accountApp || typeof accountApp.getQuoteAcceptance !== 'function') {
        throw new Error('accountApp or its getOrderTracking method is not available');
      }

      const response = await accountApp.getQuoteAcceptance({ quoteId: quoteId });

      if (response && response.error) {
        console.error('Error from accountApp:', response.error);
      }

      if(response && response.success){
        btn.setAttribute('data-quote-id', '');
        acceptQuoteText.innerText = 'Accepted';
        btn.disabled = true;
        btn.classList.remove('accept-quote-btn');
        btn.classList.remove('th-btn-primary');
        btn.classList.add('th-btn-disabled');
      }

    });
  });
});
//###################### Accept Quote End here ########################

//###################### Create Request Start here ########################
document.addEventListener("DOMContentLoaded", async function () {
     const submitRequestButton = document.getElementById('submitRequestButton');
     submitRequestButton?.addEventListener('click', async function (event) {
       event.preventDefault();
       const nameInput = document.getElementById('name');
       const name = nameInput ? nameInput.value.trim() : '';
       const descriptionInput = document.getElementById('description');
       const description = descriptionInput ? descriptionInput.value.trim() : '';
       const attachmentsInput = document.getElementById('attachments');
 
       [nameInput, descriptionInput].forEach(function (inp) {
         if (!inp) return;
         inp.addEventListener('input', function () {
           clearError(inp);
         });
       });
 
       const fields = [nameInput, descriptionInput];
       let firstInvalid = null;
       const allValid = fields.every(function (f) {
         const ok = validateInput(f);
         if (!ok && !firstInvalid) firstInvalid = f;
         return ok;
       });
 
       if (!allValid) {
         if (firstInvalid && typeof firstInvalid.focus === 'function') firstInvalid.focus();
         return;
       }
 
       // Build payload - if files are attached, use FormData to support uploads
       let payload = { name: name, description: description };
       let useFormData = false;
       let formData = null;
       if (attachmentsInput && attachmentsInput.files && attachmentsInput.files.length) {
         useFormData = true;
         formData = new FormData();
         formData.append('name', name);
         formData.append('description', description);
         Array.from(attachmentsInput.files).forEach(function (file, idx) {
           formData.append('attachments[]', file);
         });
       }
 
       try {
         const response = await accountApp.createRequest(useFormData ? formData : payload);
         // Try to show a friendly success message
         const Swal = typeof window !== 'undefined' ? window.Swal : null;
         if (response && response.error) {
           if (Swal) {
             Swal.fire({ icon: 'error', title: 'Request failed', text: response.error || 'Create request failed' });
           } else {
             alert('Create request failed: ' + (response.error || 'Unknown error'));
           }
         } else {
           if (Swal) {
             Swal.fire({ icon: 'success', title: 'Request submitted', text: 'Your request has been submitted.' });
           } else {
             alert('Request submitted successfully');
           }
           // Optionally reset the form
           if (nameInput) nameInput.value = '';
           if (descriptionInput) descriptionInput.value = '';
           if (attachmentsInput) attachmentsInput.value = '';
         }
       } catch (err) {
         console.error('Create request failed', err);
         alert('Create request failed: ' + (err && err.message ? err.message : 'Unknown error'));
       }
     });
});
//###################### Create Request End here ########################

//###################### Booking Phone Call Start here ########################
document.addEventListener("DOMContentLoaded", async function () {

  const bookingPhoneCallButton = document.getElementById('bookingPhoneCallButton');
  bookingPhoneCallButton?.addEventListener('click', async function () {
    const pinboardId = bookingPhoneCallButton.getAttribute('data-pinboard-id');
    const phoneNumberInput = document.getElementById('bookingPhoneNumber');
    const phoneNumber = phoneNumberInput ? phoneNumberInput.value : '';
    phoneNumberInput.classList.remove('is-warning');
    phoneNumberInput.setAttribute('placeholder', 'Enter your Phone Number');

    // only number is allowed in phone number input
    if(!/^\d+$/.test(phoneNumber)){
      phoneNumberInput.classList.add('is-invalid');
      phoneNumberInput.focus();
      return;
    }
    // remove class from phoneNumberInput
    phoneNumberInput.classList.remove('is-invalid');
    // call store and update pinboard contact number
    const payload = {
      pinboard_id: pinboardId,
      phone_number: phoneNumber
    };

    // add spnier loader in bookingPhoneCallButton i icon tag
    const bookingPhoneCallButtonIcon = bookingPhoneCallButton.querySelector('i');
    // i class remove fa-phone
    bookingPhoneCallButtonIcon.classList.remove('fa-phone');
    bookingPhoneCallButtonIcon.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    bookingPhoneCallButton.disabled = true;
    const response = await pinboardApp.bookingPhoneCall(payload);

    console.log('response account app=', response);
    if(response && response.success){
      // get phone use html 
      const phoneNumbers = response.data;
      const phoneNumbersContainer = document.getElementById('phone-numbers-container');

      if (!phoneNumbersContainer) {
        console.error("phone-numbers-container not found");
        return;
      }
      
      let phoneNumbersHtml = `
        <ul>
      `;
      for (const phone of phoneNumbers) {
        phoneNumbersHtml += `
          <li>
            <i class="fa-solid fa-phone"></i>
            <span>${phone.contact_number}</span>
          </li>
        `;
      }
      
      phoneNumbersHtml += `</ul>`;      
      // replace full inner content
      phoneNumbersContainer.innerHTML = phoneNumbersHtml;
      
      const pinboardProcessed = {pinboard_id: pinboardId, processed_method: 'phone-call', date_time: dateTime};
      // console.log('pinboardProcessed=', pinboardProcessed);
      localStorage.setItem('pinboard_processed', JSON.stringify(pinboardProcessed));
      // show success message
      const successMessage = document.getElementById('success-message');
      successMessage.style.display = 'block';
      successMessage.innerText = (response.success ? response.message : 'Phone call booking failed');
      setTimeout(() => {
        successMessage.style.display = 'none';
      }, 3000);
      bookingPhoneCallButtonIcon.innerHTML = '';
      bookingPhoneCallButtonIcon.classList.add('fa-phone');
      bookingPhoneCallButton.disabled = false;
    }
  });

  // final-booking-phone click function to book a phone call
  const bookingPhone = document.querySelectorAll('.final-booking-phone');
  bookingPhone?.forEach(function (icon) {
    icon.addEventListener('click', async function () {
      const customerPhoneNumberInput = document.getElementById('bookingPhoneNumber');
      const customerPhoneNumber = customerPhoneNumberInput ? customerPhoneNumberInput.value : '';
      if(!/^\d+$/.test(customerPhoneNumber)){
        // showError(customerPhoneNumberInput, 'Please enter a valid phone number');
        // add class to customerPhoneNumberInput
        customerPhoneNumberInput.classList.add('is-warning');
        customerPhoneNumberInput.setAttribute('placeholder', 'Please enter a valid phone number');
        customerPhoneNumberInput.focus();
        return;
      }
      // remove class from customerPhoneNumberInput
      customerPhoneNumberInput.classList.remove('is-warning');
      customerPhoneNumberInput.setAttribute('placeholder', 'Enter your Phone Number');
      const phoneNumber = icon.getAttribute('data-contact');
      if (!phoneNumber) {
        return;
      }

      window.location.href = 'tel:' + phoneNumber;
    });
  });

  const bookingEmail = document.querySelectorAll('.final-booking-email');
  bookingEmail?.forEach(function (icon) {
    icon.addEventListener('click', () => {
      // Get dynamic email from data-contact
      const email = icon.getAttribute('data-contact');
      if (!email) return; // exit if no email

      // Predefine subject and body
      const subject = encodeURIComponent('Booking Inquiry');
      const body = encodeURIComponent(
        'Hello,\n\nI would like to book an appointment.\n\nThanks.'
      );

      // Construct mailto link
      const mailtoUrl = `mailto:${email}?subject=${subject}&body=${body}`;

      // Open default email client in new message window
      window.open(mailtoUrl, '_blank');
    });
  });

  // final-booking-calendar click function to book a calendar
  const bookingCalendar = document.querySelectorAll('.final-booking-calendar');
  bookingCalendar?.forEach(function (icon) {
    icon.addEventListener('click', async function () {
      const phoneNumber = icon.getAttribute('data-phone');
      if (!phoneNumber) {
        return;
      }
      // Get dynamic description and date values from data attributes
      const eventTitle = encodeURIComponent(icon.getAttribute('data-title') || 'Booking Call');
      const eventLocation = encodeURIComponent(icon.getAttribute('data-location') || 'Showroom Location');
      const contactName = icon.getAttribute('data-name');
      const contactEmail = icon.getAttribute('data-email');
      const contactPhone = icon.getAttribute('data-phone');
      const eventDescription = encodeURIComponent('I am ' + contactName + 'my email is ' + contactEmail + ' and my phone number is ' + contactPhone + '. Click to join your scheduled call.');
      let eventDateStart =  new Date().toISOString().replace(/[-:]/g, '').replace('.000Z','Z');
      let eventDateEnd = new Date(new Date().getTime() + 1 * 60 * 60 * 1000).toISOString().replace(/[-:]/g, '').replace('.000Z','Z');

      // Format date for Google Calendar link
      function formatGcalDate(dateStr) {
        // Ensure UTC and remove separator for Google
        return dateStr.replace(/[-:]/g, '').replace('.000Z','Z');
      }
      const formattedStart = formatGcalDate(eventDateStart);
      const formattedEnd = formatGcalDate(eventDateEnd);

      // Construct Google Calendar link with dynamic data
      const calendarUrl = `https://calendar.google.com/calendar/u/0/r/eventedit?text=${eventTitle}` +
        `&dates=${formattedStart}/${formattedEnd}` +
        `&location=${eventLocation}` +
        `&details=${eventDescription}` +
        `&pli=1`;

      window.open(calendarUrl, '_blank');
    });
  });
});
//###################### Booking Phone Call End here ########################
//###################### Booking Email Start here ########################
document.addEventListener("DOMContentLoaded", async function () {

  const bookingEmailButton = document.getElementById('booking-email-button');
  bookingEmailButton?.addEventListener('click', async function () {
    const emailInput = document.getElementById('booking-customer-email');
    const pinboardId = emailInput ? emailInput.getAttribute('data-pinboard-id') : '';

    const pinboardProcessed = {pinboard_id: pinboardId, processed_method: 'email', date_time: dateTime};
    localStorage.setItem('pinboard_processed', JSON.stringify(pinboardProcessed));
  });

  const bookingServiceRequestSubmitButton = document.getElementById('booking-service-request-submit');
  bookingServiceRequestSubmitButton?.addEventListener('click', async function (event) {
    event.preventDefault();
    const emailInput = document.getElementById('booking-customer-email');
    const pinboardId = emailInput ? emailInput.getAttribute('data-pinboard-id') : '';
    const email = emailInput ? emailInput.value.trim() : '';

    if (!window.quill) {
        console.error('Quill editor not initialized');
        return false;
    }
    // Get HTML content
    const note = window.quill.root.innerHTML.trim();
    const attachmentsSelect = document.getElementById('attachments');
    const attachmentsValue = attachmentsSelect ? attachmentsSelect?.value || '' : '';
    const imageInput = document.getElementById('image-upload');

    // validate email field
    if (!validateInput(emailInput)) {
      if (emailInput && typeof emailInput.focus === 'function') emailInput.focus();
      return;
    }

    // Build payload or FormData if files are present
    let useFormData = false;
    let formData = new FormData();
    
    // Always use FormData
    formData.append('pinboard_id', pinboardId);
    formData.append('email', email);
    formData.append('note', note);
    formData.append('attachments', attachmentsValue);
    
    // If image selected append
    if (imageInput && imageInput.files && imageInput.files.length > 0) {
        formData.append('files', imageInput.files[0]); 
        useFormData = true;
    }
    

    const payload = {
      pinboard_id: pinboardId,
      email: email,
      note: note,
      attachments: attachmentsValue
    };

    // disable button and show spinner
    bookingServiceRequestSubmitButton.disabled = true;
    const originalBtnHtml = bookingServiceRequestSubmitButton.innerHTML;
    bookingServiceRequestSubmitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

    try {
      // call store and update pinboard email (supports FormData or JSON payload)
      const response = await pinboardApp.bookingEmail(useFormData ? formData : payload);
      if (response && response.error) {
        console.error('Error from pinboardApp:', response.error);
        alert('Failed to send booking email: ' + (response.error || 'Unknown error'));
      } else {
        // success - give feedback and reset optional fields
        const bookingEmailAlertMessage = document.getElementById('booking-email-alert-message');
        bookingEmailAlertMessage.style.display = 'block';
        bookingEmailAlertMessage.innerText = response.message || 'Booking email sent successfully';
        const pinboardProcessed = {pinboard_id: pinboardId, processed_method: 'email', date_time: dateTime};
        localStorage.setItem('pinboard_processed', JSON.stringify(pinboardProcessed));
      }
    } catch (err) {
      console.error('bookingEmail failed', err);
      alert('Booking email failed: ' + (err && err.message ? err.message : 'Unknown error'));
    } finally {
      // bookingServiceRequestSubmitButton.disabled = false;
      bookingServiceRequestSubmitButton.innerHTML = originalBtnHtml;
    }
  });
});
//###################### Booking Email End here ########################

//###################### Booking Virtual Meeting Start here ########################
document.addEventListener("DOMContentLoaded", function () {
  const virtualMeetingInfo = document.getElementById('virtual-meeting-info');
  const radioInputs = virtualMeetingInfo?.querySelectorAll('.virtual-meeting-platform input[type="radio"]');

  radioInputs?.forEach(function (input) {
      input.addEventListener('change', function () {
          if (!input.checked) return;

          const platformValue = input.closest('.virtual-meeting-platform').getAttribute('data');

          const pinboardId = virtualMeetingInfo?.getAttribute('data-pinboard-id');
          const projectName = virtualMeetingInfo?.getAttribute('data-project-name');

          const timestamp = Date.now();
          let meetingLink = '';

          switch (platformValue) {
              case 'google-meet':
                  meetingLink = `https://meet.google.com/${projectName.replace(/\s+/g, '-')}-${timestamp}`;
                  break;
              case 'zoom':
                  meetingLink = `https://zoom.us/j/${timestamp}?pwd=${btoa(projectName)}`;
                  break;
              case 'microsoft-teams':
                  meetingLink = `https://teams.microsoft.com/l/meetup-join/${timestamp}-${encodeURIComponent(projectName)}`;
                  break;
              default:
                  meetingLink = '#';
          }

          console.log('Generated Meeting Link:', meetingLink);

          // Optional: display in the UI
          let linkContainer = document.getElementById('generated-meeting-link');
          if (!linkContainer) {
              linkContainer = document.createElement('div');
              linkContainer.id = 'generated-meeting-link';
              linkContainer.style.marginTop = '10px';
              virtualMeetingInfo.appendChild(linkContainer);
          }

          linkContainer.innerHTML = `<span>Meeting Link: </span><a href="${meetingLink}" target="_blank">${meetingLink}</a>`;
          const dateTime = new Date().toISOString().replace(/[-:]/g, '').replace('.000Z','Z');
          const pinboardProcessed = {pinboard_id: pinboardId, processed_method: 'virtual-meeting', date_time: dateTime};
          localStorage.setItem('pinboard_processed', JSON.stringify(pinboardProcessed));
      });
  });
});

//###################### Booking Virtual Meeting End here ########################

//###################### Booking Showroom Visit Start here ########################
document.addEventListener("DOMContentLoaded", async function () {
    window.openBookingCalendar = function (el) {
      const pinboardId = el.dataset.pinboardId;
      const projectName = el.dataset.projectName;
      const customerEmail = el.dataset.customerEmail;
      const customerName = el.dataset.customerName;
      const customerPhone = el.dataset.customerPhone;

      const eventTitle = encodeURIComponent(
          'Booking Virtual Meeting for Project: ' + projectName
      );

      const eventDescription = encodeURIComponent(
          `I am ${customerName}, my email is ${customerEmail} and my phone number is ${customerPhone}.`
      );

      const formatDate = (date) =>
          date.toISOString().replace(/[-:]/g, '').replace(/\.\d+Z$/, 'Z');

      const start = new Date();
      const end = new Date(start.getTime() + 60 * 60 * 1000);

      const calendarUrl =
          `https://calendar.google.com/calendar/u/0/r/eventedit` +
          `?text=${eventTitle}` +
          `&dates=${formatDate(start)}/${formatDate(end)}` +
          `&details=${eventDescription}`;

      window.open(calendarUrl, '_blank');
  };
});
//###################### Booking Showroom Visit End here ########################
//###################### Pinboard List Start here ########################
// document.addEventListener("DOMContentLoaded", function () {
//   const pinboardList = document.querySelectorAll('.nav-link');

//   pinboardList.forEach(function (link) {
//     link.addEventListener('click', function (e) {
//       const href = link.getAttribute('href');

//       if (href && href.includes('/account/pinboards')) {
//         e.preventDefault(); // stop default navigation
//         let customerId = '';
//         // customer get from local storage
//         const authDetails = JSON.parse(localStorage.getItem('userAuthDetails'));
//         if(!authDetails) {
//           alert('Please login to view your pinboards');
//           return;
//         }
//         customerId = authDetails.customer_id;
//         const url = new URL('/account/pinboards', window.location.origin);
//         url.searchParams.set('customer_id', customerId);
//         window.location.href = url.toString();
//       }
//     });
//   });
// });
//###################### Pinboard List End here ########################
//###################### Contact Sales Get in Touch Start here ########################
document.addEventListener("DOMContentLoaded", async function () {

  const contactTouchSubmitButton = document.getElementById('contact-touch-submit');
  contactTouchSubmitButton?.addEventListener('click', async function (event) {
    event.preventDefault();
    const emailInput = document.getElementById('email');
    const companyInput = document.getElementById('company');
    const fullNameInput = document.getElementById('full-name');
    const typeInput = document.getElementById('type');
    const attachmentsInput = document.getElementById('attachments');
    const addTextInput = document.getElementById('add-text');

    // validate email field
    if (!validateInput(emailInput)) {
      if (emailInput && typeof emailInput.focus === 'function') emailInput.focus();
      return;
    }
    if (!validateInput(companyInput)) {
      if (companyInput && typeof companyInput.focus === 'function') companyInput.focus();
      return;
    }
    if (!validateInput(fullNameInput)) {
      if (fullNameInput && typeof fullNameInput.focus === 'function') fullNameInput.focus();
      return;
    }

    // Build payload or FormData if files are present
    let useFormData = false;
    let formData = null;
    if (attachmentsInput && attachmentsInput.files && attachmentsInput.files.length) {
      useFormData = true;
      formData = new FormData();
      formData.append('email', emailInput.value);
      formData.append('company', companyInput.value);
      formData.append('full_name', fullNameInput.value);
      formData.append('type', typeInput.value);
      formData.append('attachments', Array.from(attachmentsInput.files));
      formData.append('add_text', addTextInput.value);
      Array.from(attachmentsInput.files).forEach(function (file) {
        formData.append('files', file);
      });
    }

    const payload = {
      email: emailInput.value,
      company: companyInput.value,
      full_name: fullNameInput.value,
      type: typeInput.value,
      attachments: Array.from(attachmentsInput.files),
      add_text: addTextInput.value
    };

    // disable button and show spinner
    contactTouchSubmitButton.disabled = true;
    // add class th-btn-disabled to contactTouchSubmitButton
    contactTouchSubmitButton.classList.add('th-btn-disabled');
    const originalBtnHtml = contactTouchSubmitButton.innerHTML;
    contactTouchSubmitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

    try {
      const response = await accountApp.contactSalesGetInTouch(useFormData ? formData : payload);
      console.log('response accountApp contactSalesGetInTouch =', response);
      if (!response.success) {
        console.error('Error from accountApp:', response.message);
        alert('Failed to send contact sales get in touch: ' + (response.message || 'Unknown error'));
      } else {
        // success - give feedback and reset optional fields
        const contactSalesGetInTouchAlertMessage = document.getElementById('contact-sales-get-in-touch-alert-message');
        contactSalesGetInTouchAlertMessage.style.display = 'block';
        contactSalesGetInTouchAlertMessage.innerText = response.success.message || 'Contact sales get in touch sent successfully';
      }
    } catch (err) {
      console.error('contactSalesGetInTouch failed', err);
      alert('Contact sales get in touch failed: ' + (err && err.message ? err.message : 'Unknown error'));
    } finally {
      // contactTouchSubmitButton.disabled = false;
      contactTouchSubmitButton.innerHTML = originalBtnHtml;
    }
  });

  // member calendar icon click function
  const memberCalendarIcon = document.querySelectorAll('.th-calendar-icon');
  memberCalendarIcon?.forEach(function (icon) {
    icon.addEventListener('click', function () {

      const location = icon.dataset.location;
      const memberEmail = icon.dataset.memberEmail;
      const memberPhone = icon.dataset.memberPhone;
      const memberName = icon.dataset.memberName;
      const eventTitle = 'Booking Call with ' + memberName;
      const eventLocation = encodeURIComponent(location || 'Sydney Showroom');   
      const eventDescription = 'I am ' + memberName + ' my email is ' + memberEmail + ' and my phone number is ' + memberPhone + '. Click to join your scheduled call.';
      let eventDateStart =  new Date().toISOString().replace(/[-:]/g, '').replace('.000Z','Z');
      let eventDateEnd = new Date(new Date().getTime() + 1 * 60 * 60 * 1000).toISOString().replace(/[-:]/g, '').replace('.000Z','Z');

      // Format date for Google Calendar link
      function formatGcalDate(dateStr) {
        // Ensure UTC and remove separator for Google
        return dateStr.replace(/[-:]/g, '').replace('.000Z','Z');
      }
      const formattedStart = formatGcalDate(eventDateStart);
      const formattedEnd = formatGcalDate(eventDateEnd);

      // Construct Google Calendar link with dynamic data
      const calendarUrl = `https://calendar.google.com/calendar/u/0/r/eventedit?text=${eventTitle}` +
        `&dates=${formattedStart}/${formattedEnd}` +
        `&location=${eventLocation}` +
        `&details=${eventDescription}` +
        `&pli=1`;

      window.open(calendarUrl, '_blank');

    });
  });

  // member email icon click function
  const memberEmailIcon = document.querySelectorAll('.th-email-icon');
  memberEmailIcon?.forEach(function (icon) {
    icon.addEventListener('click', function () {

      const email = this.dataset.memberEmail;  // use "this" instead of icon
      if (!email) return;

      const subject = encodeURIComponent('Booking Inquiry');
      const body = encodeURIComponent(
        `Hello, I would like to book an appointment. Thanks.`
      );

      const mailtoLink = `mailto:${email}?subject=${subject}&body=${body}`;

      // Open email client
      // window.location.href = mailtoLink;
      // open email client in new window
      window.open(mailtoLink, '_blank');
      return false;
    });
  });

  // member phone icon click function
  const memberPhoneIcon = document.querySelectorAll('.th-phone-icon');
  memberPhoneIcon?.forEach(function (icon) {
    icon.addEventListener('click', function () {

      const phoneNumber = icon.getAttribute('data-member-phone');
      if (!phoneNumber) {
        return;
      }
      window.location.href = 'tel:' + phoneNumber;
    });
  });

  // booking showroom visit button click function

  // click flatpickr-day class
  const flatpickrDay = document.querySelectorAll('.flatpickr-day');
  flatpickrDay?.forEach(function (day) {
    day.addEventListener('click', async function () {
      // const date = document.querySelector('.flatpickr-day.selected');
      //  let dateTime = '';
        // if (window.fp && Array.isArray(window.fp.selectedDates) && window.fp.selectedDates.length > 0) {
        //   const selectedDate = window.fp.selectedDates[0];            // JS Date
        //   dateTime = window.fp.formatDate(selectedDate, "Y-m-d");     // safe: selectedDate exists
        // } else if (date && date.getAttribute('aria-label')) {
        //   const ariaLabel = date.getAttribute('aria-label'); // e.g., "January 7, 2026"
        //   dateTime = dateFormat(ariaLabel);
        // } else {
        //   dateTime = new Date().toISOString().split('T')[0];
        // }
        // // selected timezone
        // let timezone = '';
        // const timezoneSelect = document.getElementById('choose-timezone');
        // if (timezoneSelect && timezoneSelect.value) {
        //   timezone = timezoneSelect.value;
        // }


      // const bookingData = {
      //   customer_id: 1,
      //   showroom_id: 1,
      //   tour_type: 'physicalTour',
      //   date: dateTime,
      //   time_zone: timezone
      // };
      // await pinboardApp.bookNow(bookingData);
    });
  });
  

});

// ---------------- Booking slots: compute/render and rebind flatpickr handlers ----------
function computeTimeSlots(dateStr /* YYYY-MM-DD */, timezone) {
  const startHour = 9;
  const endHour = 17;
  const intervalMinutes = 30;
  const slots = [];

  for (let hour = startHour; hour < endHour; hour++) {
    for (let minute = 0; minute < 60; minute += intervalMinutes) {
      const d = new Date(`${dateStr}T${String(hour).padStart(2,'0')}:${String(minute).padStart(2,'0')}:00`);
      const display = d.toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
      slots.push({ iso: d.toISOString(), display: display });
    }
  }
  return slots;
}

// Render the modal with available slots and wire booking
function renderTimeSlotsModal(dateStr, timezone, slots) {
  const modalEl = document.getElementById('timeSlotsModal');
  if (!modalEl) return;

  const selectedDateEl = document.getElementById('ts-selected-date');
  const slotsGrid = document.getElementById('ts-slots-grid');
  const noSlotsEl = document.getElementById('ts-no-slots');
  const durationSelect = document.getElementById('ts-duration-select');

  selectedDateEl.innerText = dateStr;
  slotsGrid.innerHTML = '';
  noSlotsEl.style.display = 'none';

  if (!slots || slots.length === 0) {
    noSlotsEl.style.display = 'block';
  } else {
    slots.forEach(function (slot) {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-outline-primary btn-sm th-booking-slot-btn d-flex align-items-center justify-content-center';
      btn.style.minWidth = '110px';
      btn.style.whiteSpace = 'nowrap';
      btn.style.margin = '4px';
      btn.dataset.iso = slot.iso;

      // Create icon circle
      const iconCircle = document.createElement('span');
      iconCircle.className = 'booking-slot-icon-circle d-flex align-items-center justify-content-center me-2';
     
      // Add an icon - for example, a clock icon using font-awesome
      const icon = document.createElement('i');
      icon.className = 'fa-light fa-circle';
      icon.style.fontSize = '14px';
      icon.style.color = 'var(--theme-primary)';
      iconCircle.appendChild(icon);

      // Create the text node for display time
      const label = document.createElement('span');
      label.innerText = slot.display;

      // Assemble button content
      btn.appendChild(iconCircle);
      btn.appendChild(label);

      btn.addEventListener('click', async function () {
        const selectedDuration = durationSelect ? durationSelect.value : '30';
        const bookingData = {
          customer_id: 1,
          showroom_id: 1,
          tour_type: 'physicalTour',
          date: dateStr,
          time_zone: timezone,
          // time: slot.display,
          // duration: parseInt(selectedDuration, 10)
        };

        btn.disabled = true;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        // this button active set to bookButton
   
        // btn.classList.remove('btn-outline-primary');
        // remove spinner
        setTimeout(() => {
          btn.classList.add('th-btn-primary');
          btn.innerHTML = originalText;
        }, 1000);



        
      });
      slotsGrid.appendChild(btn);
    });
  }

  if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
    const bsModal = new bootstrap.Modal(modalEl);
    bsModal.show();
  } else {
    modalEl.classList.add('show');
    modalEl.style.display = 'block';
  }
}


function rebindFlatpickrDayClicks() {
  const nodes = document.querySelectorAll('.flatpickr-day');

  nodes.forEach(function (node) {
    // Remove old node to remove existing listeners
    const clone = node.cloneNode(true);
    node.parentNode.replaceChild(clone, node);

    clone.addEventListener('click', function (e) {
      e.preventDefault();

      // Always use currentTarget for consistency
      const dateNode = e.currentTarget;
      // console.log('dateNode=', dateNode);

      // Convert aria-label to YYYY-MM-DD using local date to avoid UTC shift
      let dateTime = '';
      if (dateNode && dateNode.getAttribute('aria-label')) {
        const ariaLabel = dateNode.getAttribute('aria-label');
        const d = new Date(ariaLabel); // local timezone
        const year = d.getFullYear();
        const month = String(d.getMonth() + 1).padStart(2, '0'); // months are 0-indexed
        const day = String(d.getDate()).padStart(2, '0');
        dateTime = `${year}-${month}-${day}`;
      } else {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        dateTime = `${year}-${month}-${day}`;
      }

      // console.log('dateTime=', dateTime);

      // Get timezone
      const timezoneSelect = document.getElementById('choose-timezone');
      const timezone = (timezoneSelect && timezoneSelect.value)
        ? timezoneSelect.value
        : Intl.DateTimeFormat().resolvedOptions().timeZone;

      // Compute and render slots
      const slots = computeTimeSlots(dateTime, timezone);
      renderTimeSlotsModal(dateTime, timezone, slots);
    });
  });
}


// run immediately to ensure our handlers are active
rebindFlatpickrDayClicks();

// ---------------- Booking duration dropdown ----------
(function () {
  var dd = document.querySelector('.th-duration-dropdown');
  if (!dd) return;
  var sel = dd.querySelector('#ts-duration-select');
  var val = dd.querySelector('.th-duration-value');
  var opt = dd.querySelector('.th-duration-options');
  var txt = { '30': '30 mins', '60': '1 hour', '90': '90 mins' };
  dd.querySelector('.th-duration-trigger').onclick = function (e) { e.stopPropagation(); dd.classList.toggle('th-duration-open'); };
  opt.onclick = function (e) {
    var li = e.target.closest('li');
    if (!li) return;
    e.stopPropagation();
    sel.value = li.dataset.value;
    val.textContent = li.textContent;
    dd.classList.remove('th-duration-open');
  };
  document.addEventListener('click', function () { dd.classList.remove('th-duration-open'); });
  val.textContent = txt[sel.value] || '1 hour';
})();

// click book button 
const bookButton = document.getElementById('bookButton');
bookButton?.addEventListener('click', async function () {
  // console.log('bookButton clicked');
  const bookingData = {
    customer_id: 1,
    showroom_id: 1,
    tour_type: 'physicalTour',
    date: dateStr,
    time_zone: timezone,
    // time: slot.display,
    // duration: parseInt(selectedDuration, 10)
  };
  try {
    const response = await pinboardApp.bookNow(bookingData);
    console.log('response bookNow =', response);
    if (response && response.success) {
      if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        const bsModal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
        // window.location.href = `/pinboards/7/booking/showroom-visit`;
        // open new window
        // window.open(`/pinboards/7/booking/showroom-visit`, '_blank');
        // bsModal.hide();
      }
      const confirmEl = document.getElementById('bookingConfirmationModal');
      if (confirmEl && window.bootstrap && typeof window.bootstrap.Modal === 'function') {
        const confirmModal = new bootstrap.Modal(confirmEl);
        confirmModal.show();
      } else {
        alert(response.message || 'Booking confirmed');
      }
    } else {
      alert((response && response.message) ? response.message : 'Booking failed');
      btn.disabled = false;
      btn.innerHTML = originalText;
    }
  } catch (err) {
    console.error('Booking failed', err);
    alert('Booking failed: ' + (err && err.message ? err.message : 'Unknown error'));
    btn.disabled = false;
    btn.innerHTML = originalText;
  }



});





// ---------------- end booking slots additions ----------
//###################### Contact Sales Get in Touch End here ########################