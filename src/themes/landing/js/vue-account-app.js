import {
  executeRecaptcha,
  getRecaptchaActionFromPage,
  getRecaptchaSiteKeyFromPage,
  preloadRecaptcha,
} from './recaptcha-v3.js';

//###################### Project List Start here ########################
document.addEventListener("DOMContentLoaded", async function () {
  let appContainer = document.getElementById('account-pinboard-list');
  if (appContainer) {
    const module = await import('/js/vue/accountProjectList.js');
    const accountProjectList = module.default;
    const response = await accountProjectList.getProjectList(appContainer);
  }
});
//###################### Project List End here ########################

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
let dateTime = new Date().toISOString().replace(/[-:]/g, '').replace('.000Z', 'Z');
//###################### Order Tracking Start here ########################
document.addEventListener("DOMContentLoaded", async function () {
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
      if (!quoteId) {
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

      if (response && response.success) {
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
  const form = document.getElementById('createRequestForm');
  const submitRequestButton = document.getElementById('submitRequestButton');
  if (!form || !submitRequestButton) return;

  const RECAPTCHA_ACTION = getRecaptchaActionFromPage('service_request', form);
  const recaptchaSiteKey = getRecaptchaSiteKeyFromPage(form);
  const recaptchaEnabled = recaptchaSiteKey !== '';
  const recaptchaTokenInput = document.getElementById('g-recaptcha-response-create-request');
  const recaptchaFeedback = document.getElementById('recaptcha-feedback-create-request');

  if (!recaptchaEnabled) {
    const disclaimer = form.querySelector('.recaptcha-disclaimer');
    if (disclaimer) {
      disclaimer.style.display = 'none';
    }
  } else {
    preloadRecaptcha(recaptchaSiteKey);
  }

  function showRecaptchaError(message) {
    if (!recaptchaFeedback) {
      return;
    }
    recaptchaFeedback.textContent = message;
    recaptchaFeedback.style.display = message ? 'block' : 'none';
  }

  const nameInput = document.getElementById('name');
  const descriptionInput = document.getElementById('description');
  const fileInput = document.getElementById('attachments');
  const dropzone = document.getElementById('attachments-dropzone');
  const fileListEl = document.getElementById('attachments-list');
  const uploadErrorEl = document.getElementById('upload-error');
  const successMessage = document.getElementById('success-message');
  const errorMessage = document.getElementById('error-message');

  const MAX_FILES = 3;
  const MAX_FILE_SIZE_BYTES = 15 * 1024 * 1024; // 15 MB per file

  const formatLimit = function () {
    return Math.round(MAX_FILE_SIZE_BYTES / (1024 * 1024)) + ' MB';
  };
  const formatSize = function (size) {
    if (size < 1024 * 1024) return Math.max(1, Math.round(size / 1024)) + ' KB';
    return (size / (1024 * 1024)).toFixed(1) + ' MB';
  };
  const getExt = function (name) {
    const parts = String(name || '').split('.');
    if (parts.length < 2) return 'FILE';
    return parts[parts.length - 1].toUpperCase().slice(0, 4);
  };

  let uploadedFiles = []; // [{ id, name, size, extension, raw }]

  // ---------------- live-clear validation feedback ----------------
  [nameInput, descriptionInput].forEach(function (inp) {
    if (!inp) return;
    inp.addEventListener('input', function () { clearError(inp); });
  });

  // ---------------- file picker / dropzone wiring ----------------
  if (dropzone) {
    dropzone.addEventListener('click', function () { fileInput && fileInput.click(); });
    dropzone.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        fileInput && fileInput.click();
      }
    });
    dropzone.addEventListener('dragover', function (e) {
      e.preventDefault();
      dropzone.classList.add('is-drag-over');
    });
    dropzone.addEventListener('dragleave', function (e) {
      e.preventDefault();
      dropzone.classList.remove('is-drag-over');
    });
    dropzone.addEventListener('drop', function (e) {
      e.preventDefault();
      dropzone.classList.remove('is-drag-over');
      handleSelectedFiles(e.dataTransfer ? e.dataTransfer.files : null);
    });
  }
  if (fileInput) {
    fileInput.addEventListener('change', function (e) {
      handleSelectedFiles(e.target.files);
      e.target.value = '';
    });
  }

  function setUploadError(msg) {
    if (!uploadErrorEl) return;
    uploadErrorEl.style.display = msg ? 'block' : 'none';
    uploadErrorEl.innerText = msg || '';
  }

  function renderFileList() {
    if (!fileListEl) return;
    if (!uploadedFiles.length) {
      fileListEl.style.display = 'none';
      fileListEl.innerHTML = '';
      return;
    }
    fileListEl.style.display = 'block';
    fileListEl.innerHTML = uploadedFiles.map(function (f) {
      return ''
        + '<li>'
        + '  <span class="th-file-type">' + f.extension + '</span>'
        + '  <div class="th-file-content">'
        + '    <strong>' + f.name + '</strong>'
        + '    <small>' + formatSize(f.size) + ' · uploaded just now</small>'
        + '  </div>'
        + '  <button type="button" aria-label="Remove file" data-remove-id="' + f.id + '">×</button>'
        + '</li>';
    }).join('');
    fileListEl.querySelectorAll('[data-remove-id]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        const id = btn.getAttribute('data-remove-id');
        uploadedFiles = uploadedFiles.filter(function (f) { return f.id !== id; });
        if (uploadedFiles.length < MAX_FILES) setUploadError('');
        renderFileList();
      });
    });
  }

  function handleSelectedFiles(fileList) {
    if (!fileList || !fileList.length) return;

    const remaining = MAX_FILES - uploadedFiles.length;
    if (remaining <= 0) {
      setUploadError('You can upload up to ' + MAX_FILES + ' files.');
      return;
    }

    const incoming = Array.from(fileList);
    const toAdd = [];
    let oversized = 0, dupes = 0;

    incoming.forEach(function (file) {
      if (toAdd.length >= remaining) return;
      if (file.size > MAX_FILE_SIZE_BYTES) { oversized += 1; return; }
      const dup = uploadedFiles.some(function (u) { return u.name === file.name && u.size === file.size; });
      if (dup) { dupes += 1; return; }

      toAdd.push({
        id: Date.now() + '-' + Math.random().toString(36).slice(2, 8),
        name: file.name,
        size: file.size,
        extension: getExt(file.name),
        raw: file,
      });
    });

    uploadedFiles = uploadedFiles.concat(toAdd);
    renderFileList();

    const ignoredForLimit = Math.max(0, incoming.length - toAdd.length - oversized - dupes);
    const messages = [];
    if (oversized) messages.push('Each file must be ' + formatLimit() + ' or less (' + oversized + ' skipped).');
    if (ignoredForLimit) messages.push('Only ' + MAX_FILES + ' files are allowed.');
    if (dupes) messages.push(dupes + ' duplicate file skipped.');
    setUploadError(messages.join(' '));
  }

  function validateUploadsBeforeSubmit() {
    if (uploadedFiles.length > MAX_FILES) {
      setUploadError('You can upload up to ' + MAX_FILES + ' files.');
      return false;
    }
    const oversizedFile = uploadedFiles.find(function (f) {
      return Number((f && f.size) || 0) > MAX_FILE_SIZE_BYTES;
    });
    if (oversizedFile) {
      setUploadError('Each file must be ' + formatLimit() + ' or less.');
      return false;
    }
    return true;
  }

  // ---------------- submit ----------------
  form.addEventListener('submit', async function (event) {
    event.preventDefault();

    if (successMessage) successMessage.style.display = 'none';
    if (errorMessage) { errorMessage.style.display = 'none'; errorMessage.innerText = ''; }
    showRecaptchaError('');

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
    if (!validateUploadsBeforeSubmit()) return;

    const originalHtml = submitRequestButton.innerHTML;
    submitRequestButton.disabled = true;
    submitRequestButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> REQUESTING...';

    try {
      let recaptchaToken = '';
      if (recaptchaEnabled) {
        try {
          recaptchaToken = await executeRecaptcha(recaptchaSiteKey, RECAPTCHA_ACTION);
          if (recaptchaTokenInput) {
            recaptchaTokenInput.value = recaptchaToken;
          }
        } catch (recaptchaErr) {
          console.error('reCAPTCHA execute failed', recaptchaErr);
          showRecaptchaError('reCAPTCHA verification failed. Please refresh the page and try again.');
          return;
        }
      } else if (recaptchaTokenInput && recaptchaTokenInput.value) {
        recaptchaToken = recaptchaTokenInput.value;
      }

      const authServiceModule = await import('./vue/services/authService.js');
      const userAuthDetails = await authServiceModule.default.getUserAuthentication();
      const customerId = userAuthDetails && userAuthDetails.customer ? userAuthDetails.customer.customer_id : null;
      const email = (userAuthDetails && userAuthDetails.customer && (userAuthDetails.customer.email || userAuthDetails.customer.customer_email))
                 || (userAuthDetails && userAuthDetails.user && userAuthDetails.user.email)
                 || '';

      const formData = new FormData();
      formData.append('name', nameInput.value.trim());          // order number
      formData.append('note', descriptionInput.value.trim());   // issue description
      if (customerId) formData.append('user_id', userAuthDetails.user.user_id);
      if (customerId) formData.append('customer_id', customerId);
      if (email) formData.append('email', email);
      if (recaptchaEnabled) {
        formData.append('g-recaptcha-response', recaptchaToken);
      }
      uploadedFiles.forEach(function (f) {
        if (f && f.raw) formData.append('files[]', f.raw);
      });

      const response = await fetch('/api/account/create-request', {
        method: 'POST',
        body: formData,
        credentials: 'include',
      });

      let result = null;
      try {
        const text = await response.text();
        if (text) result = JSON.parse(text);
      } catch (parseErr) {
        result = null;
      }

      if (!response.ok || !result || result.success === false || result.error) {
        const msg = (result && (result.message || result.error))
                 || (response.status === 413 ? ('Each file must be ' + formatLimit() + ' or less.') : 'Request failed');
        // if (errorMessage) {
        //   errorMessage.style.display = 'block';
        //   errorMessage.innerText = msg;
        // }
        if (successMessage) {
          successMessage.style.display = 'block';
          successMessage.classList.remove('text-success');
          successMessage.classList.add('text-danger');
          successMessage.innerText = msg;
        }
        return;
      }

      // success
      if (nameInput) nameInput.value = '';
      if (descriptionInput) descriptionInput.value = '';
      uploadedFiles = [];
      renderFileList();
      setUploadError('');
      if (successMessage) {
        successMessage.classList.remove('text-danger');
        successMessage.classList.add('text-success');
        successMessage.style.display = 'block';
        successMessage.innerText = result.message || 'Request submitted successfully';
      }
    } catch (err) {
      console.error('Create request failed', err);
      const msg = 'Request failed';
      if (errorMessage) { errorMessage.style.display = 'block'; errorMessage.innerText = msg; }
      if (successMessage) {
        successMessage.style.display = 'block';
        successMessage.classList.remove('text-success');
        successMessage.classList.add('text-danger');
        successMessage.innerText = msg;
      }
    } finally {
      submitRequestButton.disabled = false;
      submitRequestButton.innerHTML = originalHtml;
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
    if (!/^\d+$/.test(phoneNumber)) {
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
    if (response && response.success) {
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

      const pinboardProcessed = { pinboard_id: pinboardId, processed_method: 'phone-call', date_time: dateTime };
      // console.log('pinboardProcessed=', pinboardProcessed);
      // localStorage.setItem('pinboard_processed', JSON.stringify(pinboardProcessed));
      // empty local storage for pinboard_processed
      localStorage.removeItem('pinboard_processed');
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
      if (!/^\d+$/.test(customerPhoneNumber)) {
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
      const contactName = icon.getAttribute('data-name');
      const contactCompany = 'Krost';
      if (!email) return; // exit if no email

      // Predefine subject and body
      const subject = encodeURIComponent('Booking Enquiry');
      const body = encodeURIComponent(
        `Hi Krost Team,
        I am interested in booking a consultation to discuss an upcoming project and would like to learn more about Krost's solutions.
        Please let me know your availability for a brief consultation (either at your showroom or via a video call).
        You can reach me at this email address, or I have included my contact details below.

        My Contact Info (Optional):
        Name: 
        Company: 

        Looking forward to hearing from you.

        Best regards, 
        ${contactName}
        `
      );

      // Construct mailto link
      const mailtoUrl = `mailto:${email}?subject=${subject}&body=${body}`;

      // Open default email client in new message window
      // window.open(mailtoUrl, '_blank');
      window.open(mailtoUrl, '_blank', 'noopener noreferrer');
      return false;
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
      const eventTitle = encodeURIComponent(icon.getAttribute('data-title') || 'Your Phone Call Consultation with Krost');
      const eventLocation = encodeURIComponent(icon.getAttribute('data-location') || 'Showroom Location');
      const contactName = icon.getAttribute('data-name');
      const contactEmail = icon.getAttribute('data-email');
      const contactPhone = icon.getAttribute('data-phone');
      const contactCompany = 'Krost';

      const eventDescription = encodeURIComponent(`
Hi Krost Team,

I am interested in booking a consultation to discuss an upcoming project and would like to learn more about Krost's solutions.
Please let me know your availability for a brief consultation (either at your showroom or via a video call).
You can reach me at this email address, or I have included my contact details below.

My Contact Info (Optional):
Name: ${contactName}
Company: ${contactCompany}
Looking forward to hearing from you.

Best regards, 
${contactName}`);

      // const eventDescription = encodeURIComponent('I am ' + contactName + 'my email is ' + contactEmail + ' and my phone number is ' + contactPhone + '. Click to join your scheduled call.');

      let eventDateStart = new Date().toISOString().replace(/[-:]/g, '').replace('.000Z', 'Z');
      let eventDateEnd = new Date(new Date().getTime() + 1 * 60 * 60 * 1000).toISOString().replace(/[-:]/g, '').replace('.000Z', 'Z');

      // Format date for Google Calendar link
      function formatGcalDate(dateStr) {
        // Ensure UTC and remove separator for Google
        return dateStr.replace(/[-:]/g, '').replace('.000Z', 'Z');
      }
      const formattedStart = formatGcalDate(eventDateStart);
      const formattedEnd = formatGcalDate(eventDateEnd);

      // Construct Google Calendar link with dynamic data
      const calendarUrl = `https://calendar.google.com/calendar/u/0/r/eventedit?text=${eventTitle}` +
        `&dates=${formattedStart}/${formattedEnd}` +
        `&location=${eventLocation}` +
        `&details=${eventDescription}` +
        `&add=${encodeURIComponent(contactEmail)}` +
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

    const pinboardProcessed = { pinboard_id: pinboardId, processed_method: 'email', date_time: dateTime };
    // localStorage.setItem('pinboard_processed', JSON.stringify(pinboardProcessed));
    // empty local storage for pinboard_processed
    localStorage.removeItem('pinboard_processed');
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
    // const attachmentsSelect = document.getElementById('attachments');
    // const attachmentsValue = attachmentsSelect ? attachmentsSelect?.value || '' : '';
    const imageInput = document.getElementById('image-upload');
    // console.log('attachmentsValue=', imageInput.files);
    

    // validate email field
    if (!validateInput(emailInput)) {
      if (emailInput && typeof emailInput.focus === 'function') emailInput.focus();
      return;
    }

    // Build payload or FormData if files are present
    // let useFormData = false;
    let formData = new FormData();

    // Always use FormData
    formData.append('pinboard_id', pinboardId);
    formData.append('email', email);
    formData.append('note', note);

    // Send each selected file individually in multipart payload
    if (imageInput && imageInput.files && imageInput.files.length > 0) {
      Array.from(imageInput.files).forEach(function (file) {
        // formData.append('files', file);
        formData.append('files[]', file);
      });
    }


    // const payload = {
    //   pinboard_id: pinboardId,
    //   email: email,
    //   note: note,
    //   attachments: imageInput.files
    // };

    // disable button and show spinner
    const originalBtnHtml = bookingServiceRequestSubmitButton.innerHTML;
    bookingServiceRequestSubmitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';

    try {
      // console.log('payload=', payload);
      console.log('formData=', formData);
      
      // call store and update pinboard email (supports FormData or JSON payload)
      // const response = await pinboardApp.bookingEmail(useFormData ? formData : payload);
      // const response = await pinboardApp.bookingEmail(payload);

      // Do NOT set 'Content-Type': fetch with FormData auto-sets headers, or it will break boundary and fail
      const responseRaw = await fetch("/api/booking-email-service-requests", {
        method: 'POST',
        body: formData
      });

      const response = await responseRaw.json();

      if (response && response.error) {
        console.error('Error from pinboardApp:', response.error);
        // alert('Failed to send booking email: ' + (response.error || 'Unknown error'));
      } else {
        // success - give feedback and reset optional fields
        const bookingEmailAlertMessage = document.getElementById('booking-email-alert-message');
        bookingEmailAlertMessage.style.display = 'block';
        bookingEmailAlertMessage.innerText = response.message || 'Booking email sent successfully';
        const pinboardProcessed = { pinboard_id: pinboardId, processed_method: 'email', date_time: dateTime };
        // localStorage.setItem('pinboard_processed', JSON.stringify(pinboardProcessed));
        // empty local storage for pinboard_processed
        localStorage.removeItem('pinboard_processed');
        bookingServiceRequestSubmitButton.disabled = true;
      }
    } catch (err) {
      console.error('bookingEmail failed', err);
      // alert('Booking email failed: ' + (err && err.message ? err.message : 'Unknown error'));
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
  const showroomVisitInfo = document.getElementById('get-showroom-visit-data');
  const pinboardId = showroomVisitInfo?.getAttribute('data-pinboard-id');
  const projectName = showroomVisitInfo?.getAttribute('data-project-name');
  const customerEmail = showroomVisitInfo?.getAttribute('data-customer-email');
  const meetingTime = showroomVisitInfo?.getAttribute('data-meeting-time');
  const timeZone = showroomVisitInfo?.getAttribute('data-time-zone');
  const visitShowroomDate = showroomVisitInfo?.getAttribute('data-visit-showroom-date');
  const showroom = showroomVisitInfo?.getAttribute('data-showroom');
  const tourType = showroomVisitInfo?.getAttribute('data-tour-type');
  const guestEmail = showroomVisitInfo?.getAttribute('data-guest-email');
  const mapLink = showroomVisitInfo?.getAttribute('data-map-link');
  const location = showroomVisitInfo?.getAttribute('data-location');

  radioInputs?.forEach(function (input) {
    input.addEventListener('change', function () {
      if (!input.checked) return;

      const platformValue = input.closest('.virtual-meeting-platform').getAttribute('data');

      const timestamp = Date.now();
      let meetingLink = '';

      const eventTitle = encodeURIComponent(`Virtual Meeting - ${projectName || 'Project'}`);
      const dateTimeString = `${visitShowroomDate} ${meetingTime}`;
      const startDate = new Date(dateTimeString);
      const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);
      const formatDate = (date) => {
        const pad = (n) => String(n).padStart(2, '0');
        return (
          date.getUTCFullYear() +
          pad(date.getUTCMonth() + 1) +
          pad(date.getUTCDate()) +
          'T' +
          pad(date.getUTCHours()) +
          pad(date.getUTCMinutes()) +
          pad(date.getUTCSeconds()) +
          'Z'
        );
      };
      const eventInfoQuery =
        `?text=${eventTitle}` +
        `&dates=${formatDate(startDate)}/${formatDate(endDate)}` +
        `&location=${encodeURIComponent(location || '')}` +
        `&add=${encodeURIComponent(customerEmail || '')}`;

      switch (platformValue) {
        case 'google-meet':
          // Official Google Calendar schedule entry point
          meetingLink = `https://calendar.google.com/calendar/render?action=TEMPLATE${eventInfoQuery.replace('?', '&')}`;
          break;
        case 'zoom':
          // Reliable Zoom start page for signed-in users
          meetingLink = `https://zoom.us/start/videomeeting${eventInfoQuery}`;
          break;
        case 'microsoft-teams': 
          // Official Teams "new meeting" entry point
          meetingLink =
            `https://teams.microsoft.com/l/meeting/new?subject=${encodeURIComponent(projectName || '')}` +
            `&content=${eventTitle}` +
            `&startTime=${encodeURIComponent(startDate.toISOString())}` +
            `&endTime=${encodeURIComponent(endDate.toISOString())}` +
            `&location=${encodeURIComponent(location || '')}` +
            `&attendees=${encodeURIComponent(customerEmail || '')}`;
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

      linkContainer.innerHTML = `<span><i class="fa fa-copy" style="cursor: pointer; margin-right: 6px;" onclick="copyToClipboard('${meetingLink}', this)"></i>Meeting Link: </span><a class="underline" style="cursor: pointer; text-decoration: underline; color: blue;" href="${meetingLink}" target="_blank">Online Meeting Link</a>`;
      const dateTime = new Date().toISOString().replace(/[-:]/g, '').replace('.000Z', 'Z');
      const pinboardProcessed = { pinboard_id: pinboardId, processed_method: 'virtual-meeting', date_time: dateTime };
      // localStorage.setItem('pinboard_processed', JSON.stringify(pinboardProcessed));
      // empty local storage for pinboard_processed
      localStorage.removeItem('pinboard_processed');
    });
  });
});

window.copyToClipboard = function (text, triggerEl) {
  const fallbackCopy = () => {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.setAttribute('readonly', '');
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    document.body.appendChild(textArea);
    textArea.select();

    let copied = false;
    try {
      copied = document.execCommand('copy');
    } catch (error) {
      copied = false;
    }

    document.body.removeChild(textArea);
    showCopyToast(copied ? 'Meeting link copied' : 'Failed to copy meeting link', triggerEl);
  };

  if (navigator.clipboard && window.isSecureContext) {
    navigator.clipboard.writeText(text)
      .then(() => {
        showCopyToast('Meeting link copied', triggerEl);
      })
      .catch(() => {
        fallbackCopy();
      });
    return;
  }

  fallbackCopy();
}

function showCopyToast(message, triggerEl) {
  const existingToast = document.getElementById('copy-link-toast');
  if (existingToast) {
    existingToast.remove();
  }

  const toast = document.createElement('div');
  toast.id = 'copy-link-toast';
  toast.textContent = message;
  toast.style.position = 'fixed';
  if (triggerEl && typeof triggerEl.getBoundingClientRect === 'function') {
    const rect = triggerEl.getBoundingClientRect();
    const top = Math.max(8, rect.top - 6);
    const left = Math.min(window.innerWidth - 180, rect.right + 10);
    toast.style.top = `${top}px`;
    toast.style.left = `${left}px`;
  } else {
    toast.style.top = '20px';
    toast.style.right = '20px';
  }
  toast.style.background = '#111';
  toast.style.color = '#fff';
  toast.style.padding = '10px 14px';
  toast.style.borderRadius = '6px';
  toast.style.fontSize = '13px';
  toast.style.zIndex = '9999';
  toast.style.boxShadow = '0 6px 16px rgba(0, 0, 0, 0.25)';
  toast.style.opacity = '0';
  toast.style.transition = 'opacity 0.2s ease';
  document.body.appendChild(toast);

  requestAnimationFrame(() => {
    toast.style.opacity = '1';
  });

  setTimeout(() => {
    toast.style.opacity = '0';
    setTimeout(() => {
      toast.remove();
    }, 200);
  }, 1800);
}

//###################### Booking Virtual Meeting End here ########################

//###################### Booking Showroom Visit Start here ########################
document.addEventListener("DOMContentLoaded", async function () {
  window.openBookingCalendar = function (el) {
    const pinboardId = el.dataset.pinboardId;
    const projectName = el.dataset.projectName;
    const customerEmail = el.dataset.customerEmail;
    const customerName = el.dataset.customerName;
    const customerPhone = el.dataset.customerPhone;
    const meetingTime = el.dataset.meetingTime; // e.g., "6:10 PM"
    const timeZone = el.dataset.timeZone || 'Asia/Dhaka'; // default
    const visitShowroomDate = el.dataset.visitShowroomDate; // e.g., "Mar 20, 2026"
    const showroom = el.dataset.showroom;
    const tourType = el.dataset.tourType;
    const guestEmail = el.dataset.guestEmail;
    const mapLink = el.dataset.mapLink ? el.dataset.mapLink : showroom;
    const location = el.dataset.location ? el.dataset.location : showroom;
    // Fix: Corrected virtualTour typo and JavaScript variable for guestEmail
    const title = tourType === 'virtualTour' 
      ? 'Krost: Online Meeting'
      : `Krost: ${showroom} Visit`;

    // Event title and description
    const eventTitle = encodeURIComponent(title);

    // const eventDescription = encodeURIComponent(
    //   `I am ${customerName}, my email is ${customerEmail} and my phone number is ${customerPhone}.`
    // );

    // Combine date + time
    const dateTimeString = `${visitShowroomDate} ${meetingTime}`; // "Mar 20, 2026 6:10 PM"
    const startDate = new Date(dateTimeString);

    // End time 1 hour later
    const endDate = new Date(startDate.getTime() + 60 * 60 * 1000);

    // Google Calendar expects format: YYYYMMDDTHHMMSSZ (UTC)
    const formatDate = (date) => {
      const pad = (n) => String(n).padStart(2, '0');
      return (
        date.getUTCFullYear() +
        pad(date.getUTCMonth() + 1) +
        pad(date.getUTCDate()) +
        'T' +
        pad(date.getUTCHours()) +
        pad(date.getUTCMinutes()) +
        pad(date.getUTCSeconds()) +
        'Z'
      );
    };

    const calendarUrl =
      `https://calendar.google.com/calendar/u/0/r/eventedit` +
      `?text=${eventTitle}` +
      `&dates=${formatDate(startDate)}/${formatDate(endDate)}` +
      `&location=${location}` +
      `&add=${encodeURIComponent(customerEmail)}`;

      // empty local storage for pinboard_process
      localStorage.removeItem('pinboard_processed');

    window.open(calendarUrl, '_blank');
  };
});

//###################### Contact Sales Get in Touch Start here ########################
document.addEventListener("DOMContentLoaded", async function () {


  // Image upload frontend start here

  const serviceRequestUploadZone = document.getElementById('upload-zone');
  const serviceRequestFileInput = document.getElementById('attachments');
  const serviceRequestRemoveBtn = document.getElementById('remove-image');

  if(serviceRequestUploadZone && serviceRequestFileInput && serviceRequestRemoveBtn){
    // Click anywhere to open file
    serviceRequestUploadZone.addEventListener('click', (e) => { if (e.target !== serviceRequestRemoveBtn) serviceRequestFileInput.click(); });
    // When image selected
    serviceRequestFileInput.addEventListener('change', function () {
      const file = this.files[0];
      if (!file) return;

      if (!file.type.startsWith('image/')) {
        alert('Please select a valid image file.');
        serviceRequestFileInput.value = '';
        return;
      }

      const reader = new FileReader();
      reader.onload = function (e) {
        serviceRequestUploadZone.style.backgroundImage = `url(${e.target.result})`;
        serviceRequestUploadZone.classList.add('has-image');
        serviceRequestRemoveBtn.style.display = 'block';
      };
      reader.readAsDataURL(file);
    });
  }

  // Remove image
  serviceRequestRemoveBtn?.addEventListener('click', function (e) {
    e.stopPropagation();
    fileInput.value = '';
    serviceRequestUploadZone.style.backgroundImage = '';
    serviceRequestUploadZone.classList.remove('has-image');
    serviceRequestRemoveBtn.style.display = 'none';
  });
// Image upload frontend end here

//Service Request Submit Button Start here

  const contactTouchSubmitButton = document.getElementById('contact-touch-submit');
  contactTouchSubmitButton?.addEventListener('click', async function (event) {
    event.preventDefault();
    const emailInput = document.getElementById('email');
    // const companyInput = document.getElementById('company');
    const firstNameInput = document.getElementById('first-name');
    const lastNameInput = document.getElementById('last-name');
    const typeInput = document.getElementById('type');
    const attachmentsInput = document.getElementById('attachments');
    const addTextInput = document.getElementById('add-text');

    // validate email field
    if (!validateInput(emailInput)) {
      if (emailInput && typeof emailInput.focus === 'function') emailInput.focus();
      return;
    }
    // if (!validateInput(companyInput)) {
    //   if (companyInput && typeof companyInput.focus === 'function') companyInput.focus();
    //   return;
    // }
    if (!validateInput(firstNameInput) && !validateInput(lastNameInput)) {
      if (firstNameInput && typeof firstNameInput.focus === 'function') firstNameInput.focus();
      if (lastNameInput && typeof lastNameInput.focus === 'function') lastNameInput.focus();
      return;
    }

    // Build payload or FormData if files are present
    let useFormData = false;
    let formData = null;
    if (attachmentsInput && attachmentsInput.files && attachmentsInput.files.length) {
      useFormData = true;
      formData = new FormData();
      formData.append('email', emailInput.value);
      // formData.append('company', companyInput.value);
      formData.append('first_name', firstNameInput.value);
      formData.append('last_name', lastNameInput.value);
      formData.append('type', typeInput.value);
      formData.append('attachments', Array.from(attachmentsInput.files));
      formData.append('add_text', addTextInput.value);
      Array.from(attachmentsInput.files).forEach(function (file) {
        formData.append('files', file);
      });
    }

    const payload = {
      email: emailInput.value,
      // company: companyInput.value,
      first_name: firstNameInput.value,
      last_name: lastNameInput.value,
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
      const reqEmail = emailInput.value;
      const response = await accountApp.contactSalesGetInTouch(useFormData ? formData : payload);
    //  const response = { success: true, message: 'Contact sales get in touch sent successfully' };
      console.log('response accountApp contactSalesGetInTouch =', response);
      if (!response.success) {
        console.error('Error from accountApp:', response.message);
        // alert('Failed to send contact sales get in touch: ' + (response.message || 'Unknown error'));
      } else {
        // success - give feedback and clear form
        // const contactSalesGetInTouchAlertMessage = document.getElementById('contact-sales-get-in-touch-alert-message');
        // contactSalesGetInTouchAlertMessage.style.display = 'block';
        // contactSalesGetInTouchAlertMessage.innerText = response.success.message || 'Contact sales get in touch sent successfully';

        // Clear form fields
        if (firstNameInput) firstNameInput.value = '';
        if (lastNameInput) lastNameInput.value = '';
        if (emailInput) emailInput.value = '';
        if (typeInput) typeInput.selectedIndex = 0;
        if (addTextInput) addTextInput.value = '';
        if (attachmentsInput) attachmentsInput.value = '';

        // Reset upload zone UI
        const uploadZone = document.getElementById('upload-zone');
        const removeImageBtn = document.getElementById('remove-image');
        if (uploadZone) {
          uploadZone.style.backgroundImage = '';
          uploadZone.classList.remove('has-image');
        }

        if (removeImageBtn) removeImageBtn.style.display = 'none';

        // Re-enable submit button for new submission
        contactTouchSubmitButton.disabled = false;
        contactTouchSubmitButton.classList.remove('th-btn-disabled');
    
        window.location.href = '/contact-get-in-touch/' + response.data;
       
      }
    } catch (err) {
      console.error('contactSalesGetInTouch failed', err);
      // alert('Contact sales get in touch failed: ' + (err && err.message ? err.message : 'Unknown error'));
      contactTouchSubmitButton.disabled = false;
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
      let eventDateStart = new Date().toISOString().replace(/[-:]/g, '').replace('.000Z', 'Z');
      let eventDateEnd = new Date(new Date().getTime() + 1 * 60 * 60 * 1000).toISOString().replace(/[-:]/g, '').replace('.000Z', 'Z');

      // Format date for Google Calendar link
      function formatGcalDate(dateStr) {
        // Ensure UTC and remove separator for Google
        return dateStr.replace(/[-:]/g, '').replace('.000Z', 'Z');
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

//###################### Contact Sales Mailto Links Start here ########################
const SALESPERSON_MAIL_CC = 'sales@krost.com.au';
const SALESPERSON_MAIL_SUBJECT = 'Meeting Enquiry with Krost';
const SALESPERSON_MAIL_BODY = (
  `Hi Krost Team,

I am interested in booking a consultation to discuss an upcoming project and would like to learn more about Krost's solutions.

Please let me know your availability for a brief consultation (either at your showroom or via a video call).

You can reach me at this email address, or I have included my contact details below.

My Contact Info (Optional):
Name: 
Company: 

Looking forward to hearing from you.

Best regards,
`
);

function buildSalespersonMailtoLink(email) {
  // const query = new URLSearchParams({
  //   to: email,
  //   cc: SALESPERSON_MAIL_CC,
  //   subject: SALESPERSON_MAIL_SUBJECT,
  //   body: SALESPERSON_MAIL_BODY,
  // });
  // return `mailto:${email}?${query.toString()}`;
  const params = [
    `cc=${encodeURIComponent(SALESPERSON_MAIL_CC)}`,
    `subject=${encodeURIComponent(SALESPERSON_MAIL_SUBJECT)}`,
    `body=${encodeURIComponent(SALESPERSON_MAIL_BODY)}`,
  ];
  return `mailto:${email}?${params.join('&')}`;
}

function pushSalespersonEmailClickEvent(email) {
  window.dataLayer = window.dataLayer || [];
  window.dataLayer.push({
    event: 'email_click_salesperson',
    salesperson_email: email,
  });
}

  // member email icon click function (mailto links + GA4/GTM tracking)
  const memberEmailIcon = document.querySelectorAll('.th-email-icon');
  memberEmailIcon?.forEach(function (icon) {
    const email = icon.dataset.memberEmail;
    if (!email) return;
    
    // console.log('email =', email);
    const mailtoLink = buildSalespersonMailtoLink(email);
    // console.log('mailtoLink =', mailtoLink);
    if (icon.tagName === 'A') {
      icon.href = mailtoLink;
    }

    icon.addEventListener('click', function (e) {
      const salespersonEmail = this.dataset.memberEmail;
      console.log('salespersonEmail =', salespersonEmail);
      if (!salespersonEmail) return;

      pushSalespersonEmailClickEvent(salespersonEmail);

      if (this.tagName === 'A') return;

      e.preventDefault();
      console.log('buildSalespersonMailtoLink(salespersonEmail) =', buildSalespersonMailtoLink(salespersonEmail));
      window.location.href = buildSalespersonMailtoLink(salespersonEmail);
    });
  });
});
//###################### Contact Sales Mailto Links End here ########################


// ---------------- Booking duration dropdown ----------
(function () {
  var dd = document.querySelector('.th-duration-dropdown');
  if (!dd) return;
  var sel = dd.querySelector('#ts-duration-select');
  var val = dd.querySelector('.th-duration-value');
  var opt = dd.querySelector('.th-duration-options');
  var txt = { '30': '30 mins', '60': '60 mins', '90': '90 mins' };

  function defaultDurationForTourType(tourType) {
    if (tourType === 'virtualTour' || tourType === 'virutalTour') return 30;
    return 60; // physicalTour / Showroom
  }

  function applyDurationByTourType() {
    var tourTypeEl = document.getElementById('choose-tour-type');
    if (!tourTypeEl) return;
    var tourType = tourTypeEl.value;
    var defaultMins = defaultDurationForTourType(tourType);
    sel.value = String(defaultMins);
    val.textContent = txt[defaultMins] || defaultMins + ' mins';
  }

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

  // Initial and dynamic duration based on tour type
  applyDurationByTourType();
  var tourTypeEl = document.getElementById('choose-tour-type');
  if (tourTypeEl) {
    tourTypeEl.addEventListener('change', applyDurationByTourType);
  }
})();
// ---------------- end booking slots additions ----------


$(document).ready(function () {
  function resetAllTimeSlots() {
    document.querySelectorAll('.th-time-slot').forEach((slot) => {
      slot.classList.remove('active', 'disabled', 'no-hover');
      slot.removeAttribute('disabled');

      const checkbox = slot.querySelector('input[type="checkbox"]');
      const icon = slot.querySelector('i');

      if (checkbox) {
        checkbox.checked = false;
        checkbox.disabled = false;
        checkbox.classList.remove('d-none', 'hover');
      }
      if (icon) icon.classList.add('d-none');
    });
  }

  function markBookedTimeSlots_new(bookedTimes) {
    const bookedSet = new Set(
      (Array.isArray(bookedTimes) ? bookedTimes : [])
        .map(time => String(time).trim())
        .filter(Boolean)
    );
  
    document.querySelectorAll('.th-time-slot').forEach((slot) => {
      const checkbox = slot.querySelector('input[type="checkbox"]');
      if (!checkbox) return;
  
      const timeValue = String(checkbox.value || '').trim();
  
      if (bookedSet.has(timeValue)) {
        // booked → hide
        slot.classList.add('d-none');
        checkbox.checked = false;
        checkbox.disabled = true;
      } else {
        // available → show
        slot.classList.remove('d-none');
        checkbox.disabled = false;
      }
    });
  }

  function markBookedTimeSlots(bookedTimes) {
    // console.log("Booked bookedTimes:", bookedTimes);
  
    const slots = document.querySelectorAll('.th-time-slot');
  
    // STEP 1: Reset all slots
    slots.forEach((slot) => {
      const checkbox = slot.querySelector('input[type="checkbox"]');
      const icon = slot.querySelector('i');
  
      slot.classList.remove('active', 'disabled', 'time-slot-disabled');
      slot.removeAttribute('disabled');
      slot.classList.add('no-hover');
  
      if (checkbox) {
        checkbox.disabled = false;
        checkbox.classList.remove('d-none', 'time-slot-disabled');
        checkbox.checked = false;
      }
  
      if (icon) {
        icon.classList.add('d-none');
      }
    });
  
    // STEP 2: prepare booked set
    const bookedSet = new Set(
      (Array.isArray(bookedTimes) ? bookedTimes : [])
        .map((time) => String(time || '').trim())
        .filter(Boolean)
    );
  
    if (bookedSet.size === 0) return;
  
    // STEP 3: apply booked state
    slots.forEach((slot) => {
      const checkbox = slot.querySelector('input[type="checkbox"]');
      const icon = slot.querySelector('i');
  
      if (!checkbox) return;
  
      const value = String(checkbox.value || '').trim();
  
      if (!bookedSet.has(value)) return;
  
      slot.classList.add('active', 'disabled', 'time-slot-disabled', 'th-booked-time-slot');
      slot.setAttribute('disabled', 'disabled');
      slot.classList.remove('no-hover');
  
      checkbox.checked = false;
      checkbox.disabled = true;
      checkbox.classList.add('d-none', 'time-slot-disabled');
  
      if (icon) icon.classList.remove('d-none');
    });
  }
 
  let timezoneReqId = 0;

  async function showTimeZones(showroom_id) {
    const reqId = ++timezoneReqId;
  
    const res = await fetch('/api/visit-showroom/timezone');
    const data = await res.json();
  
    // If a newer call started, abort this older one
    if (reqId !== timezoneReqId) return;
  
    const selectEl = document.getElementById('choose-timezone-dropdown');
    if (!selectEl) return;
  
    const choicesData = showroom_id
      ? data.map(t => {
        if(String(t.showroom_id) === String(showroom_id)){
          t.selected = true;
        }else t.selected = false;
        return t;
      })
      : data;
  
    const inst = selectEl._choicesInstance;
    if (inst && typeof inst.destroy === 'function') {
      try { inst.destroy(); } catch (e) {}
      selectEl._choicesInstance = null;
    }
  
    // Bind to exact element, not '#id' string
    selectEl._choicesInstance = new Choices(selectEl, {
      allowHTML: true,
      choices: []
    });
    selectEl._choicesInstance.setChoices(choicesData, 'showroom_id', 'label', true);
  }

  // flatpickr-days click function
  const calendarContainer = document.querySelector(".flatpickr-days");
  if (calendarContainer) {
    calendarContainer.addEventListener("click", async function (e) {
      const day = e.target.closest(".flatpickr-day");
      const member = document.getElementById("choose-members").value;
      if (!member) {
        // alert('Please select a member to book a tour');
        return;
      }

      // time zone 
      const tourType = document.getElementById("choose-tour-type").value;
      const chooseLocationSelect = document.getElementById('choose-location');
      const showroom_id = chooseLocationSelect?.value;
      await showTimeZones(showroom_id);  
      const opt = chooseLocationSelect?.selectedOptions?.[0]; // the chosen <option>
      const locationAddress = opt?.getAttribute('data-address') || '';
      const locationMapLink = opt?.getAttribute('data-map-link') || '';
      // console.log('locationMapLink =', locationMapLink);
      const tsLocationContainer = document.getElementById("ts-location-container");
      const timeSlotsModalLabel = document.getElementById("timeSlotsModalLabel");
      const tsLocationName = document.getElementById("ts-location-name");
      const tsLocationLink = document.getElementById("ts-location-link");
      // console.log('tourType =', tourType);
      if (tourType === 'virutalTour') {
        timeSlotsModalLabel.textContent = "Booking for Online Tour";
        // now hide id="ts-location-container"
        if (tsLocationContainer) {
          tsLocationContainer.classList.add('d-none');
        }
        if (tsLocationName) {
          tsLocationName.textContent = '';
        }
        if (tsLocationLink) {
          tsLocationLink.setAttribute('href', '');
        }
      } else {
        timeSlotsModalLabel.textContent = "Booking for Showroom Tour";
        // now show id="ts-location-container"
        if (tsLocationContainer) {
          tsLocationContainer.classList.remove('d-none');
        }
        if (tsLocationName) {
          tsLocationName.textContent = locationAddress;
        }
        if (tsLocationLink) {
          tsLocationLink.setAttribute('href', locationMapLink);
        }
      }
      // Only proceed if a valid day is clicked
      if (day &&
        !day.classList.contains("disabled") &&
        !day.classList.contains("prevMonthDay") &&
        !day.classList.contains("nextMonthDay")) {
        resetAllTimeSlots();

        const selectedDate = day.getAttribute("aria-label");
        const formattedDate = new Date(selectedDate).toLocaleDateString('en-GB');

        if (formattedDate && /^\d{2}\/\d{2}\/\d{4}$/.test(formattedDate)) {
          // Convert dd/mm/yyyy to yyyy-mm-dd
          const [day, month, year] = formattedDate.split('/');
          const isoDate = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
          document.getElementById("ts-selected-date").value = isoDate;
        } else {
          document.getElementById("ts-selected-date").value = '';
        }

        // Open Bootstrap modal (if using bootstrap.js)
        const modalEl = document.getElementById("timeSlotsModal");
        if (modalEl && window.bootstrap && typeof window.bootstrap.Modal === "function") {
          const modal = new bootstrap.Modal(modalEl);
          const [day, month, year] = formattedDate.split('/');
          const date = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

          try {
            const response = await fetch(`/api/fetch-booked-data/${showroom_id}/${date}`, {
              method: 'GET',
            });

            if (!response.ok) {
              throw new Error(`Failed to fetch booked data for ${showroom_id} and ${date}`);
            }

            const bookedDataResponse = await response.json();
            const bookedRowsRaw = bookedDataResponse && bookedDataResponse.data ? bookedDataResponse.data : [];
            const bookedRows = Array.isArray(bookedRowsRaw) ? bookedRowsRaw : Object.values(bookedRowsRaw);
            const bookedTimes = bookedRows
              .map((row) => (row && row.meeting_time ? row.meeting_time : ''))
              .filter(Boolean);

            markBookedTimeSlots(bookedTimes);
          } catch (error) {
            console.error('Error fetching booked showroom data:', error);
          }

          modal.show();

        }
      }
    });
  }

  
  function to24HourWithSeconds(time12h) {
    if (!time12h) return "";
    const [time, modifier] = time12h.trim().split(" ");
    let [hours, minutes] = time.split(":").map(Number);
  
    if (modifier === "PM" && hours !== 12) hours += 12;
    if (modifier === "AM" && hours === 12) hours = 0;
  
    return `${String(hours).padStart(2, "0")}:${String(minutes).padStart(2, "0")}:00`;
  }
  
  function markExistingMeetingSlot(meetingTime12h, selectedDate, changeDate) {
    const normalized = to24HourWithSeconds(meetingTime12h);
  
    document.querySelectorAll(".th-time-slot").forEach(slot => {
      slot.classList.remove("existing-booking-slot");
      const input = slot.querySelector('input[type="checkbox"]');
      if (input && input.value === normalized && selectedDate === changeDate) {
        slot.classList.add("existing-booking-slot");
      }else{
        slot.classList.remove("existing-booking-slot");
      }
    });
  }

  /** Reschedule modal (`#timeSlotsModalContactSales`): weekends + showroom public holidays */
  const HOLIDAYS = {
    SYDNEY: ["2026-01-01", "2026-01-26", "2026-04-03", "2026-04-04", "2026-04-05", "2026-04-06", "2026-04-25", "2026-04-27", "2026-06-08", "2026-10-05", "2026-12-25", "2026-12-26", "2026-12-28"],
    MELBOURNE: ["2026-01-01", "2026-01-26", "2026-03-09", "2026-04-03", "2026-04-04", "2026-04-05", "2026-04-06", "2026-04-25", "2026-06-08", "2026-11-03", "2026-12-25", "2026-12-26", "2026-12-28"],
    BRISBANE: ["2026-01-01", "2026-01-26", "2026-04-03", "2026-04-04", "2026-04-05", "2026-04-06", "2026-04-25", "2026-05-04", "2026-08-12", "2026-10-05", "2026-12-24", "2026-12-25", "2026-12-26", "2026-12-28"],
  };

  function contactSalesHolidayList(showroomId) {
    const w = window.HOLIDAYS_2026 || {};
    if (String(showroomId) === "1") return w.SYDNEY || [];
    if (String(showroomId) === "2") return w.MELBOURNE || [];
    if (String(showroomId) === "3") return w.BRISBANE || [];
    return [];
  }

  function contactSalesHolidayListResolved(showroomId) {
    let list = contactSalesHolidayList(showroomId);
    if (list.length) return list;
    const sid = String(showroomId || "");
    if (sid === "2") return HOLIDAYS.MELBOURNE;
    if (sid === "3") return HOLIDAYS.BRISBANE;
    return HOLIDAYS.SYDNEY;
  }

  function contactSalesFormatIso(d) {
    return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, "0")}-${String(d.getDate()).padStart(2, "0")}`;
  }

  function contactSalesParseIso(iso) {
    const p = String(iso || "").split("-");
    if (p.length !== 3) return new Date();
    return new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
  }

  function contactSalesIsDisabledDay(dateObj, showroomId) {
    const wd = dateObj.getDay();
    if (wd === 0 || wd === 6) return true;
    const iso = contactSalesFormatIso(dateObj);
    return contactSalesHolidayListResolved(showroomId).includes(iso);
  }

  function contactSalesNextAllowedIso(fromIso, showroomId) {
    let d = contactSalesParseIso(fromIso || contactSalesFormatIso(new Date()));
    for (let i = 0; i < 400; i++) {
      if (!contactSalesIsDisabledDay(d, showroomId)) return contactSalesFormatIso(d);
      d.setDate(d.getDate() + 1);
    }
    return contactSalesFormatIso(d);
  }

  function contactSalesDisableRules(showroomId) {
    const set = new Set(contactSalesHolidayListResolved(showroomId));
    return [
      function (date) {
        const wd = date.getDay();
        if (wd === 0 || wd === 6) return true;
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");
        return set.has(`${y}-${m}-${day}`);
      },
    ];
  }

  function destroyContactSalesDateFlatpickr() {
    const el = document.getElementById("ts-selected-date");
    if (el && el._flatpickr) {
      try {
        el._flatpickr.destroy();
      } catch (e) {}
    }
  }

  function initContactSalesDateFlatpickr(showroomId, dateIso) {
    if (typeof window.jQuery === "undefined" || !window.jQuery.fn.flatpickr) return;
    const el = document.getElementById("ts-selected-date");
    if (!el || el.type === "date") return;
    destroyContactSalesDateFlatpickr();
    const allowed = contactSalesNextAllowedIso(dateIso, showroomId);
    el.value = allowed;
    window.jQuery(el).flatpickr({
      dateFormat: "Y-m-d",
      minDate: "today",
      disable: contactSalesDisableRules(showroomId),
      defaultDate: allowed,
      allowInput: false,
      onChange(_d, dateStr) {
        if (!dateStr) return;
        el.value = dateStr;
        handleDateChange({ target: el });
      },
    });
  }

  async function showTimeSlotsModal(showroom_id, selectedDate, modalEl, visitShowroomId) {
      let modal = null;
      if (modalEl && window.bootstrap && typeof window.bootstrap.Modal === "function") {
        modal = new bootstrap.Modal(modalEl);
      }

      try {
        const bookedTimes = await fetchBookedTimes(showroom_id, selectedDate, visitShowroomId);
        markBookedTimeSlots(bookedTimes);
      } catch (error) {
        console.error('Error fetching booked showroom data:', error);
      }

      if (modal) {
        modal.show();
      }
  }

  // reschedule-booking-button id click function
  $(document).on('click', '#reschedule-booking-button', async function () {
    const modalEl = document.getElementById("timeSlotsModalContactSales");
    if (modalEl && window.bootstrap && typeof window.bootstrap.Modal === "function") {
    const getData = document.getElementById("get-showroom-visit-data");
    const showroom_id = getData?.dataset?.showroomId;
    const visit_showroom_id = getData?.dataset?.visitShowroomId;
    const selectedDate = getData?.dataset?.date;
    const email = getData?.dataset?.guestEmail;
    const locationAddress = getData?.dataset?.location;
    const meetingTime = getData?.dataset?.meetingTime;
    const timeZone = getData?.dataset?.timeZone;

    const tsSelectedDate = document.getElementById("ts-selected-date");
    const tsEmail = document.getElementById("ts-email-not-logged-in-email");
    const tsTimeZone = document.getElementById("choose-timezone-reschedule");
    const tsLocationName = document.getElementById("ts-location-name");
    const allowedDate = contactSalesNextAllowedIso(selectedDate, showroom_id);
    if (tsSelectedDate) tsSelectedDate.value = allowedDate;
    if (tsEmail) tsEmail.value = email;
    if (tsLocationName) tsLocationName.textContent = locationAddress;
    if (tsTimeZone) tsTimeZone.value = timeZone;
    await showTimeSlotsModal(showroom_id, allowedDate, modalEl, visit_showroom_id);
    markExistingMeetingSlot(meetingTime, selectedDate, allowedDate);
    setTimeout(function () {
      initContactSalesDateFlatpickr(showroom_id, allowedDate);
    }, 150);
    }
  });

  $(document).on("hidden.bs.modal", "#timeSlotsModalContactSales", function () {
    destroyContactSalesDateFlatpickr();
  });

  const dateInput = document.getElementById("ts-selected-date");

  if (dateInput && dateInput.type === "date") {
    dateInput.addEventListener("change", handleDateChange);
  }

  async function handleDateChange(event) {
    const selectedDate = event.target.value;
    if (!selectedDate) return;

    const { showroomId, visitShowroomId, existingDate, existingMeetingTime } = getVisitData();
    if (!showroomId) {
      console.warn("Showroom ID not found");
      return;
    }

    try {
      const bookedTimes = await fetchBookedTimes(showroomId, selectedDate, visitShowroomId);
      markBookedTimeSlots(bookedTimes);
      markExistingMeetingSlot(existingMeetingTime, existingDate, selectedDate);
    } catch (error) {
      console.error("Error fetching showroom data:", error);
    }
  }

  /**
   * Get showroom ID from DOM
   */
  function getVisitData() {
    const locationSelect = document.getElementById("choose-location");

    if (locationSelect && locationSelect.value) {
      return { showroomId: locationSelect.value, visitShowroomId: null, existingDate: null, existingMeetingTime: null };
    }

    const dataElement = document.getElementById("get-showroom-visit-data");
    const visitShowroomId = dataElement?.dataset?.visitShowroomId || null;
    const existingDate = dataElement?.dataset?.date || null;
    const existingMeetingTime = dataElement?.dataset?.meetingTime || null;
    return { showroomId: dataElement?.dataset?.showroomId, 
      visitShowroomId: visitShowroomId || null, 
      existingDate: existingDate || null,
      existingMeetingTime: existingMeetingTime || null };
  }

  /**
   * Fetch booked time slots from API
   */
  async function fetchBookedTimes(showroomId, selectedDate, visitShowroomId) {
    const url = `/api/fetch-booked-data/${showroomId}/${selectedDate}?id=${visitShowroomId}`;

    const response = await fetch(url, { method: "GET" });

    if (!response.ok) {
      throw new Error(`API Error: ${response.status}`);
    }

    const result = await response.json();

    const rows = Array.isArray(result?.data)
      ? result.data
      : Object.values(result?.data || {});

    return rows
      .map(row => row?.meeting_time || "")
      .filter(Boolean);
  }

  // Time slot checkbox: when checked -> add active, hide arrow, show checked; single selection
  $(document).on('click', '.th-time-slot', function (event) {
    const $slot = this;
    const $icon = $slot.querySelector('i');
    const $input = $slot.querySelector('input[type="checkbox"]');

    if (!$input || $input.disabled) return;

    const isCurrentlyChecked = $input.checked;

    if (!isCurrentlyChecked) {
      // alert('checked');
      // Uncheck all other active slots
      document.querySelectorAll('.th-time-slot.active').forEach(function (slot) {
        if (slot === $slot || slot.classList.contains('disabled')) return;
        const input = slot.querySelector('input[type="checkbox"]');
        slot.classList.remove('active');
        slot.classList.remove('no-hover');
        slot.querySelector('i')?.classList.add('d-none');
        if (input) {
          input.checked = false;
          input.classList.remove('d-none', 'hover');
        }
      });

      // Activate the clicked slot
      $input.checked = true;
      $slot.classList.add('active');
      $slot.classList.remove('no-hover');
      $input.classList.add('d-none');
      // $icon && $icon.classList.remove('d-none');
    } else {
      // Deactivate the clicked/active slot
      $slot.classList.remove('active');
      $slot.classList.add('no-hover');
      // $icon && $icon.classList.add('d-none');
      $input.classList.remove('hover', 'd-none');
      $input.checked = false;
      event.stopPropagation();
    }
  });


  // time slot hover function
  $('.th-time-slot').hover(function () {
    if ($(this).hasClass('active')) return;
    // $(this).find('i').removeClass('d-none');
    $(this).find('input').addClass('hover');
  }, function () {
    if ($(this).hasClass('active')) return;
    // $(this).find('i').addClass('d-none');
    $(this).find('input').removeClass('hover');
  });

  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  async function postData(url, payload) {
    const res = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    });
    return res.json();
}

function buildMeetingDates(date, meetingTime, duration) {
  const start = new Date(`${date}T${meetingTime}`);

  const end = new Date(start.getTime() + duration * 60000);

  return { start, end };
}

function makeMeetingLink({ start, end, title, location }) {
  const format = (date) =>
    date.toISOString().replace(/[-:]/g, '').split('.')[0] + 'Z';

  const startStr = format(start);
  const endStr = format(end);

  console.log('startStr:', startStr);
  console.log('endStr:', endStr);

  const add = 'c_18895tnlfoecignhmo94g3jqjc402@resource.calendar.google.com';

  const url = `https://calendar.google.com/calendar/u/0/r/eventedit?text=${encodeURIComponent(title)}&dates=${startStr}/${endStr}&location=${encodeURIComponent(location)}&add=${encodeURIComponent(add)}`;

  return url;
}


let otpTimerRef = null; // global variable to store the timer reference

function startOtpTimer(duration = 120) {
  const resendOtpButton = document.getElementById("resend-otp-button");
  const otpTimerText = document.getElementById("otp-timer-text");

  if (!otpTimerText) return;

  // 1. Clear old timer FIRST
  if (otpTimerRef) {
    clearInterval(otpTimerRef);
    otpTimerRef = null;
  }

  // 2. Disable button
  if (resendOtpButton) {
    resendOtpButton.disabled = true;
  }

  let timeLeft = duration;

  // 3. Start new timer
  otpTimerRef = setInterval(() => {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;

    otpTimerText.textContent =
      `Resend in ${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;

    if (timeLeft <= 0) {
      clearInterval(otpTimerRef);
      otpTimerRef = null;

      otpTimerText.textContent = "";

      if (resendOtpButton) {
        resendOtpButton.disabled = false;
      }
    }

    timeLeft--;
  }, 1000);
}

  //book button click function
  // $(document).on('click', '#th-book-time-btn', async function () {
  //   let userAuthDetails = {};
  //   try {
  //     userAuthDetails = JSON.parse(localStorage.getItem('userAuthDetails') || '{}');
  //   } catch (e) {
  //     userAuthDetails = {};
  //   }
  //   // if (!userAuthDetails) {
  //     const messageContainer = document.getElementById('show-message-container');
  //     const emailInput = document.getElementById('ts-email-not-logged-in-email');
  //     const nameInput = document.getElementById('ts-name');
  //     const emailNotLoggedInEmail = emailInput.value.trim();
  //     const nameNotLoggedIn = nameInput.value.trim();
  //     const emailNotLoggedInEmailContainer = document.getElementById('ts-email-not-logged-in-email-container');
  //     const nameInputContainer = document.getElementById('ts-name-container');
  
  //     // Reset previous error state
  //     emailNotLoggedInEmailContainer.classList.remove('invalid-email');
  
  //     if (!nameNotLoggedIn) {
  //       nameInputContainer.classList.add('invalid-email');
  //       nameInput.focus();
  //       return false;
  //     }
  
  //     // Empty check
  //     if (!emailNotLoggedInEmail) {
  //       emailNotLoggedInEmailContainer.classList.add('invalid-email');
  //       emailInput.focus();
  //       return false;
  //     }

  //     // Email format check using your function
  //     if (!validateEmail(emailNotLoggedInEmail)) {
  //       emailNotLoggedInEmailContainer.classList.add('invalid-email');
  //       emailInput.focus();
  //       return false;
  //     }
  
  //     // valid email
  //     emailNotLoggedInEmailContainer.classList.remove('invalid-email');
  //   // }

  //   const $bookBtn = $(this);
  //   const originalBtnHtml = $bookBtn.html();
  //   const loadingHtml = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending OTP...';

  //   $bookBtn.prop('disabled', true).html(loadingHtml);

  //   const timeSlots = document.querySelector('.th-time-slot.active:not(.disabled)');
  //   if (!timeSlots) {
  //     messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">Please select a time slot</div>`;
  //     $bookBtn.prop('disabled', false).html(originalBtnHtml);
  //     return;
  //   }

  //   messageContainer.innerHTML = '';

  //   const timeSlotValue = timeSlots.querySelector('input[type="checkbox"]').value;
  //   // date 
  //   const selectedDate = document.getElementById("ts-selected-date").value;
  //   // const [day, month, year] = selectedDate.split('/');
  //   // const formattedDate = `${year}-${month}-${day}`;
  //   const member = document.getElementById("choose-members").value;
  //   const chooseLocationSelect = document.getElementById('choose-location');
  //   const locationAddress = chooseLocationSelect?.selectedOptions?.[0]?.getAttribute('data-address') || '';
  //   const showroomId = chooseLocationSelect?.value;
  //   const tourType = document.getElementById("choose-tour-type").value;
  //   const timeZone = document.getElementById("choose-timezone").value;
    
  //   // Set duration based on tourType
  //   const duration = 30;
  //   if (tourType === 'physicalTour') {
  //   } else if (tourType === 'virtualTour') {

  //   } 

  //   const customerId = (userAuthDetails) ? userAuthDetails.customer_id : '';
  //   // const email = (userAuthDetails) ? userAuthDetails.email : document.getElementById('ts-email-not-logged-in-email').value;
  //   const name = document.getElementById('ts-name').value;
  //   const email = document.getElementById('ts-email-not-logged-in-email').value;

  //   const { start, end } = buildMeetingDates(selectedDate, timeSlotValue, duration);
  //   const meetingLink = makeMeetingLink({ start, end, title: 'Meeting', location: locationAddress });
  //   console.log('meetingLink:', meetingLink);

  //   const bookingData = {
  //       showroom_contact_id: member,
  //       customer_id: customerId,
  //       customer_name: name,
  //       email: email,
  //       label: 'Meeting',
  //       showroom_id: showroomId,
  //       tour_type: tourType,
  //       date: selectedDate,
  //       meeting_time: timeSlotValue,
  //       duration: duration,
  //       time_zone: timeZone,
  //       location: locationAddress,
  //       meeting_link: meetingLink,
  //   };

  // // console.log('meetingLink:', bookingData);

  //   try {
  //     // check existing booking
  //     const checkExistingBooking = await postData('/api/check-existing-booking', bookingData);
  //     if (!checkExistingBooking || !checkExistingBooking.success) {
  //       messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${checkExistingBooking.message}</div>`;
  //       return;
  //     }

  //     messageContainer.innerHTML = '';
  //     // send email verification
  //     const verifySendResponse = await postData('/api/send-email-verification', { email: email, customer_name: name, subject: 'Booking Verification Code with Krost' });
  //     console.log("verifySendResponse", verifySendResponse);
  //     if (!verifySendResponse || !verifySendResponse.success) {
  //       messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${verifySendResponse.message}</div>`;
  //       return;
  //     }

  //     messageContainer.innerHTML = '';

  //     // add otp timer
  //     startOtpTimer(); // 1 minute is 60 seconds

  //     // add customer id from verifySendResponse.customer.customer_id
  //     if (verifySendResponse.customer.customer_id) {
  //       bookingData.customer_id = verifySendResponse.customer.customer_id;
  //     }

  //     // Show verify form first
  //     const bookingFormContainer = document.getElementById("booking-form-container");
  //     if (bookingFormContainer) {
  //       bookingFormContainer.classList.add('d-none');
  //     }

  //     const bookNowVerifyEmailFormContainer = document.getElementById("book-now-verify-email-form-container");
  //     if (bookNowVerifyEmailFormContainer) {
  //       bookNowVerifyEmailFormContainer.classList.remove('d-none');
  //     }

  //     const customerEmailEl = document.getElementById("verify-email-display");
  //     if (customerEmailEl) {
  //       customerEmailEl.textContent = email;
  //     }

  //     const verifyEmailButton = document.getElementById("verify-email-button");
  //     if (verifyEmailButton) {
  //       // Replace previous handler to avoid duplicate booking calls.
  //       verifyEmailButton.onclick = async function () {
  //         const otpInputs = document.querySelectorAll('.otp-input');
  //         const otp = [...otpInputs].map(i => i.value).join('');
  //         if (otp.length !== 6 || isNaN(otp)) {
  //           messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">Please enter the 6-digit code</div>`;
  //           return;
  //         }

  //         messageContainer.innerHTML = '';
  //         const otpResponse = await postData('/api/verify-email', { email: email, otp: otp, customer_name: name });
  //         if (!otpResponse || !otpResponse.success) {
  //           messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${otpResponse?.message || 'OTP verification failed'}</div>`;
  //           return;
  //         }

  //         messageContainer.innerHTML = '';
  //         verifyEmailButton.disabled = true;
  //         const originalVerifyBtnHtml = verifyEmailButton.innerHTML;
  //         verifyEmailButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Booking...';

  //         try {
  //           const response = await pinboardApp.bookNow(bookingData);
  //           if (response && response.success) {
  //             const encodedId = btoa(response.data.visit_showroom_id);
    
  //             window.open(`/booking/showroom-visit/${encodedId}`, '_blank');
  //             if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
  //               window.location.reload();
  //             }
  //           } else {
  //             messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${response?.message || 'Booking failed'}</div>`;
  //             verifyEmailButton.disabled = false;
  //             verifyEmailButton.innerHTML = originalVerifyBtnHtml;
  //             // redirect to home page
  //             window.location.href = '/contact-sales';
  //           }
  //         } catch (bookError) {
  //           messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${bookError?.message || 'Booking failed'}</div>`;
  //           verifyEmailButton.disabled = false;
  //           verifyEmailButton.innerHTML = originalVerifyBtnHtml;
  //           // redirect to home page
  //           window.location.href = '/contact-sales';
  //         }
  //       };
  //     }
  //   } catch (error) {
  //     messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${error?.message || 'Booking failed'}</div>`;
  //     // console.error('Book time slot error:', error);
  //     // alert('Booking failed');
  //   } finally {
  //     $bookBtn.prop('disabled', false).html(originalBtnHtml);
  //     // messageContainer.innerHTML = '';
  //   }
  // });

  // th-book-time-btn-contact-sales id click function
  $(document).on('click', '#th-book-time-btn-contact-sales', async function () {
      
      const messageContainer = document.getElementById('show-message-container');
      const emailInput = document.getElementById('ts-email-not-logged-in-email');
      const emailNotLoggedInEmail = emailInput.value.trim();
      const emailNotLoggedInEmailContainer = document.getElementById('ts-email-not-logged-in-email-container');
  
      // Reset previous error state
      emailNotLoggedInEmailContainer.classList.remove('invalid-email');

      // const nameInput = document.getElementById('ts-name');
      // const nameNotLoggedIn = nameInput.value.trim();
      // if (!nameNotLoggedIn) {
      //   nameInputContainer.classList.add('invalid-email');
      //   nameInput.focus();
      //   return false;
      // }
  
      // Empty check
      if (!emailNotLoggedInEmail) {
        emailNotLoggedInEmailContainer.classList.add('invalid-email');
        emailInput.focus();
        return false;
      }
  
      // Email format check using your function
      if (!validateEmail(emailNotLoggedInEmail)) {
        emailNotLoggedInEmailContainer.classList.add('invalid-email');
        emailInput.focus();
        return false;
      }

      emailNotLoggedInEmailContainer.classList.remove('invalid-email');

    const $bookBtn = $(this);
    const originalBtnHtml = $bookBtn.html();
    const loadingHtml = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Sending OTP...';

    $bookBtn.prop('disabled', true).html(loadingHtml);

    const timeSlots = document.querySelector('.th-time-slot.active:not(.disabled)');
    if (!timeSlots) {
      messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">Please select a time slot</div>`;
      $bookBtn.prop('disabled', false).html(originalBtnHtml);
      return;
    }

    messageContainer.innerHTML = '';

    const timeSlotValue = timeSlots.querySelector('input[type="checkbox"]').value;
    // date 
    const selectedDate = document.getElementById("ts-selected-date").value;
    const getData = document.getElementById("get-showroom-visit-data");
    const visitShowroomId = getData?.dataset?.visitShowroomId;
    const locationAddress = getData?.dataset?.location;
    const tourType = getData?.dataset?.tourType;
    const guestEmail = getData?.dataset?.guestEmail;
    const existingMeetingTime = getData?.dataset?.meetingTime;
    const showroomId = getData?.dataset?.showroomId;
    const showroomContactId = getData?.dataset?.showroomContactId;
    const customerId = getData?.dataset?.customerId;
    const timeZone = getData?.dataset?.timeZone;
    const guestName = getData?.dataset?.guestName;
    // data-map-link
    const googleMapLink = getData?.dataset?.mapLink;
    console.log('guestName:', guestName);
    const duration = 30;
    
    // const email = (userAuthDetails) ? userAuthDetails.email : document.getElementById('ts-email-not-logged-in-email').value;
    const email = document.getElementById('ts-email-not-logged-in-email').value;
    // const name = document.getElementById('ts-name').value;
    const { start, end } = buildMeetingDates(selectedDate, timeSlotValue, duration);
    const meetingLink = makeMeetingLink({ start, end, title: 'Meeting', location: locationAddress });

    const bookingData = {
        visit_showroom_id: visitShowroomId,
        showroom_contact_id: showroomContactId,
        customer_id: customerId,
        email: email,
        customer_name: guestName,
        showroom_id: showroomId,
        tour_type: tourType,
        date: selectedDate,
        meeting_time: timeSlotValue,
        duration: duration,
        time_zone: timeZone,
        label: 'Meeting',
        location: locationAddress,
        meeting_link: meetingLink,
        google_map_link: googleMapLink,
    };

    try {
      const verifySendResponse = await postData('/api/send-email-verification', { email: email, customer_name: guestName, subject: 'Booking Verification Code with Krost' });
      console.log("verifySendResponse", verifySendResponse);
      if (!verifySendResponse || !verifySendResponse.success) {
        messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${verifySendResponse?.message || 'Unable to send verification code'}</div>`;
        return;
      }
      messageContainer.innerHTML = '';
      startOtpTimer();
      // add customer id from verifySendResponse.customer.customer_id
      if (verifySendResponse.customer.customer_id) {
        bookingData.customer_id = verifySendResponse.customer.customer_id;
      }

      // Show verify form first
      const bookingFormContainer = document.getElementById("booking-form-container");
      if (bookingFormContainer) {
        bookingFormContainer.classList.add('d-none');
      }

      const bookNowVerifyEmailFormContainer = document.getElementById("book-now-verify-email-form-container");
      if (bookNowVerifyEmailFormContainer) {
        bookNowVerifyEmailFormContainer.classList.remove('d-none');
      }

      const customerEmailEl = document.getElementById("verify-email-display");
      if (customerEmailEl) {
        customerEmailEl.textContent = email;
      }

      const verifyEmailButton = document.getElementById("verify-email-button");
      if (verifyEmailButton) {
        // Replace previous handler to avoid duplicate booking calls.
        verifyEmailButton.onclick = async function () {
          const otpInputs = document.querySelectorAll('.otp-input');
          const otp = [...otpInputs].map(i => i.value).join('');
          if (otp.length !== 6 || isNaN(otp)) {
            messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">Please enter the 6-digit code</div>`;
            return;
          }

          messageContainer.innerHTML = '';
          const otpResponse = await postData('/api/verify-email', { email: email, otp: otp });
          if (!otpResponse || !otpResponse.success) {
            messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${otpResponse?.message || 'OTP verification failed'}</div>`;
            return;
          }

          verifyEmailButton.disabled = true;
          const originalVerifyBtnHtml = verifyEmailButton.innerHTML;
          verifyEmailButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Booking...';

          try {
            // const response = await pinboardApp.bookNow(bookingData);
            const response = await postData('/api/reschedule-booking', bookingData);
            if (response && response.success) {
              const encodedId = btoa(response.data.visit_showroom_id);
    
              window.open(`/booking/showroom-visit/${encodedId}`, '_blank');
              if (window.bootstrap && typeof window.bootstrap.Modal === 'function') {
                window.location.reload();
              }
            } else {
              messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${response?.message || 'Booking failed'}</div>`;
              verifyEmailButton.disabled = false;
              verifyEmailButton.innerHTML = originalVerifyBtnHtml;
            }
          } catch (bookError) {
            messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${bookError?.message || 'Booking failed'}</div>`;
            verifyEmailButton.disabled = false;
            verifyEmailButton.innerHTML = originalVerifyBtnHtml;
          }
        };
      }
    } catch (error) {
      messageContainer.innerHTML = `<div class="alert alert-danger" role="alert" id="success-message" aria-live="polite">${error?.message || 'Booking failed'}</div>`;
      // console.error('Book time slot error:', error);
      // alert('Booking failed');
    } finally {
      $bookBtn.prop('disabled', false).html(originalBtnHtml);
    }
  });

  // cancel booking button click function
  $(document).on('click', '#cancel-booking-button', async function () {
    const $btn = $(this);
    if ($btn.prop('disabled') || $btn.attr('aria-busy') === 'true') {
      return;
    }

    const originalHtml = $btn.html();

    // show loader + text and prevent double submit; stay disabled until redirect or recover on error
    $btn
      .prop('disabled', true)
      .attr('aria-busy', 'true')
      .css({ cursor: 'not-allowed', pointerEvents: 'none' })
      .html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Cancelling...');

    try {
      const getData = document.getElementById("get-showroom-visit-data");
      const visitShowroomId = getData?.dataset?.visitShowroomId;
      const tourType = getData?.dataset?.tourType;
      const email = getData?.dataset?.guestEmail;
      const date = getData?.dataset?.date;
      const meetingTime = getData?.dataset?.meetingTime;
      const guestName = getData?.dataset?.guestName;
      const location = getData?.dataset?.location;
      const timeZone = getData?.dataset?.timeZone;
      const uuid = getData?.dataset?.uuid;
      const pinboardId = getData?.dataset?.pinboardId;

      const response = await postData('/api/cancel-booking', { 
        visit_showroom_id: visitShowroomId, 
        email: email, 
        date: date, 
        meeting_time: meetingTime, 
        location: location, 
        tour_type: tourType,
        customer_name: guestName,
        time_zone: timeZone,
        pinboard_id: pinboardId,
      });

      let cancelUrl = `/booking-cancel/${uuid}${visitShowroomId}`;
      if (pinboardId) {
        if (tourType === "physicalTour") {
          cancelUrl = `/pinboards/cancelled-showroom-visit/${uuid}${visitShowroomId}`;
        }else{
          cancelUrl = `/pinboards/cancelled-virtual-meeting/${uuid}${visitShowroomId}`;
        }
      }else{
        if (tourType === "physicalTour") {
          cancelUrl = `/contact-us/cancelled-physical-showroom-visit/${uuid}${visitShowroomId}`;
        }else{
          cancelUrl = `/contact-us/cancelled-virtual-meeting-booking/${uuid}${visitShowroomId}`;
        }
      }

      if (response && response.success) {
        window.location.href = cancelUrl;
      }
    } catch (err) {
      $btn
        .prop('disabled', false)
        .removeAttr('aria-busy')
        .css({ cursor: '', pointerEvents: '' })
        .html(originalHtml);
    }
  });

  // close-time-slots-modal-btn
  $(document).on('click', '#close-time-slots-modal-btn', async function () {
     // add d-none class from book-now-verify-email-form-container id 
     const bookNowVerifyEmailFormContainer = document.getElementById("book-now-verify-email-form-container");
     if (bookNowVerifyEmailFormContainer) {
      bookNowVerifyEmailFormContainer.classList.add('d-none');
     }

     // remove d-none class from booking-form-container id 
     const bookingFormContainer = document.getElementById("booking-form-container");
     if (bookingFormContainer) {
      bookingFormContainer.classList.remove('d-none');
     }
  });

  const today = new Date().toISOString().split('T')[0];
  const tsSelectedDate = document.getElementById("ts-selected-date");
  if (tsSelectedDate && tsSelectedDate.type === "date") {
    tsSelectedDate.setAttribute("min", today);
  }


  $(document).on('click', '#resend-otp-button', async function () {
    const resendOtpButton = document.getElementById("resend-otp-button");
    const email = document.getElementById('ts-email-not-logged-in-email').value;
    const name = document.getElementById('ts-name').value;
    if (!resendOtpButton) return;
  
    resendOtpButton.disabled = true;
    resendOtpButton.innerHTML = `
      <span class="spinner-border spinner-border-sm me-2" role="status"></span>
      Resending...
    `;
  
    try {
      await new Promise(resolve => setTimeout(resolve, 50));
  
      const response = await postData('/api/send-email-verification', { email: email, customer_name: name, subject: 'Resend Booking OTP with Krost' });
  
      if (response && response.success) {
        // alert(response.message);
        startOtpTimer();
      } else {
        // alert(response?.message || 'Something went wrong');
      }
  
    } catch (error) {
      console.error(error);
      // alert('Request failed');
    }
  
    resendOtpButton.disabled = false;
    resendOtpButton.innerHTML = 'Resend OTP';
  });

});

// ---------------- OTP inputs behavior ----------------
document.addEventListener('DOMContentLoaded', function () {
  const otpInputs = Array.from(document.querySelectorAll('.otp-input'));
  if (!otpInputs || otpInputs.length === 0) return;

  otpInputs.forEach((input, idx) => {
    input.addEventListener('input', (e) => {
      const v = e.target.value;
      // allow only single digit numbers
      if (!/^\d$/.test(v)) {
        e.target.value = '';
        return;
      }
      // focus next
      const next = otpInputs[idx + 1];
      if (next) next.focus();
      updateOtpText();
    });

    input.addEventListener('keydown', (e) => {
      if (e.key === 'Backspace' && !e.target.value) {
        const prev = otpInputs[idx - 1];
        if (prev) {
          prev.focus();
          prev.value = '';
          updateOtpText();
        }
      }
      if (e.key === 'ArrowLeft') {
        const prev = otpInputs[idx - 1];
        if (prev) prev.focus();
      }
      if (e.key === 'ArrowRight') {
        const next = otpInputs[idx + 1];
        if (next) next.focus();
      }
    });
  });

  function updateOtpText() {
    const val = otpInputs.map(i => i.value || '').join('');
    const otpTextEl = document.getElementById('otp-text') || document.getElementById('verify-email-display');
    // if (otpTextEl) otpTextEl.textContent = val;
  }

  // copy-paste behavior for OTP inputs (fills all 6 boxes)
    otpInputs.forEach((input, idx) => {
        input.addEventListener('paste', (e) => {
          e.preventDefault();

          const raw = (e.clipboardData || window.clipboardData).getData('text') || '';
          const digits = raw.replace(/\D/g, '').slice(0, otpInputs.length);
          if (!digits) return;

          digits.split('').forEach((digit, i) => {
            if (otpInputs[i]) otpInputs[i].value = digit;
          });

          // focus next empty input, or last one if complete
          const firstEmpty = otpInputs.find((el) => !el.value);
          (firstEmpty || otpInputs[otpInputs.length - 1])?.focus();

          updateOtpText();
        });
    });
});