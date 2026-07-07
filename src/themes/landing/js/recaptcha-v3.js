/**
 * Google reCAPTCHA v3 helpers (score-based, no user challenge).
 */

export function loadRecaptchaScript(siteKey) {
  return new Promise(function (resolve, reject) {
    if (window.grecaptcha && typeof window.grecaptcha.execute === 'function') {
      resolve();
      return;
    }

    const existing = document.querySelector('script[data-recaptcha-v3]');
    if (existing) {
      if (existing.getAttribute('data-recaptcha-ready') === '1') {
        resolve();
        return;
      }
      existing.addEventListener('load', function () {
        existing.setAttribute('data-recaptcha-ready', '1');
        resolve();
      });
      existing.addEventListener('error', function () {
        reject(new Error('reCAPTCHA failed to load'));
      });
      return;
    }

    const script = document.createElement('script');
    script.src =
      'https://www.google.com/recaptcha/api.js?render=' + encodeURIComponent(siteKey);
    script.async = true;
    script.defer = true;
    script.setAttribute('data-recaptcha-v3', '1');
    script.onload = function () {
      script.setAttribute('data-recaptcha-ready', '1');
      resolve();
    };
    script.onerror = function () {
      reject(new Error('reCAPTCHA failed to load'));
    };
    document.head.appendChild(script);
  });
}

export function executeRecaptcha(siteKey, action) {
  return loadRecaptchaScript(siteKey).then(function () {
    return new Promise(function (resolve, reject) {
      window.grecaptcha.ready(function () {
        window.grecaptcha
          .execute(siteKey, { action: action })
          .then(resolve)
          .catch(reject);
      });
    });
  });
}

export function preloadRecaptcha(siteKey) {
  const key = String(siteKey || '').trim();
  if (!key) {
    return Promise.resolve();
  }
  return loadRecaptchaScript(key).catch(function () {
    // Preload failure is non-fatal; submit will retry.
  });
}

export function getRecaptchaConfig() {
  const el = document.getElementById('krost-recaptcha-config');
  if (!el) {
    return {
      siteKey: '',
      actions: {
        contact: 'contact_submit',
        service: 'service_request',
        project: 'project_submission',
        booking: 'showroom_booking',
      },
    };
  }

  return {
    siteKey: String(el.getAttribute('data-recaptcha-site-key') || '').trim(),
    actions: {
      contact:
        String(el.getAttribute('data-recaptcha-action-contact') || '').trim() ||
        'contact_submit',
      service:
        String(el.getAttribute('data-recaptcha-action-service') || '').trim() ||
        'service_request',
      project:
        String(el.getAttribute('data-recaptcha-action-project') || '').trim() ||
        'project_submission',
      booking:
        String(el.getAttribute('data-recaptcha-action-booking') || '').trim() ||
        'showroom_booking',
    },
  };
}

export function getRecaptchaSiteKeyFromPage(root) {
  const globalKey = getRecaptchaConfig().siteKey;
  if (!root && globalKey) {
    return globalKey;
  }

  if (root && typeof root.getAttribute === 'function') {
    const fromRoot = String(root.getAttribute('data-recaptcha-site-key') || '').trim();
    if (fromRoot) {
      return fromRoot;
    }
  }

  const scope = root && typeof root.querySelector === 'function' ? root : document;
  const input = scope.querySelector('#recaptcha-site-key, [data-v-recaptcha_site_key]');
  const scopedKey = input ? String(input.value || '').trim() : '';
  return scopedKey || globalKey;
}

export function getRecaptchaActionFromPage(fallback, root) {
  const globalActions = getRecaptchaConfig().actions;

  if (root && typeof root.getAttribute === 'function') {
    const fromRoot = String(root.getAttribute('data-recaptcha-action') || '').trim();
    if (fromRoot) {
      return fromRoot;
    }
  }

  const scope = root && typeof root.querySelector === 'function' ? root : document;
  const input = scope.querySelector('#recaptcha-action, [data-v-recaptcha_action]');
  const value = input ? String(input.value || '').trim() : '';
  return value || fallback || globalActions.service || 'service_request';
}

export function getRecaptchaProjectAction() {
  return getRecaptchaConfig().actions.project || 'project_submission';
}

export function getRecaptchaBookingAction() {
  return getRecaptchaConfig().actions.booking || 'showroom_booking';
}

export async function attachBookingRecaptcha(payload) {
  const siteKey = getRecaptchaConfig().siteKey;
  if (!siteKey) {
    return payload;
  }

  const token = await executeRecaptcha(siteKey, getRecaptchaBookingAction());
  return {
    ...payload,
    'g-recaptcha-response': token,
  };
}
