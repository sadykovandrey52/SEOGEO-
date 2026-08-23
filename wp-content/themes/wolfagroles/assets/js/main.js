(function () {
  'use strict';

  var toggle = document.querySelector('[data-menu-toggle]');
  var menu = document.querySelector('[data-menu]');

  if (toggle && menu) {
    toggle.addEventListener('click', function () {
      var open = toggle.getAttribute('aria-expanded') === 'true';
      toggle.setAttribute('aria-expanded', String(!open));
      menu.classList.toggle('is-open', !open);
      document.body.classList.toggle('menu-open', !open);
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
        toggle.click();
        toggle.focus();
      }
    });
  }

  function pushEvent(name, parameters) {
    window.dataLayer = window.dataLayer || [];
    window.dataLayer.push(Object.assign({ event: name }, parameters || {}));
  }

  document.addEventListener('click', function (event) {
    var target = event.target.closest('[data-analytics-event]');
    if (target) {
      pushEvent(target.getAttribute('data-analytics-event'), {
        destination: target.getAttribute('href') || ''
      });
    }
  });

  document.querySelectorAll('input[type="file"][data-quote-files]').forEach(function (input) {
    input.addEventListener('change', function () {
      if (input.files && input.files.length) {
        pushEvent('lead_file_attached', { file_count: input.files.length });
      }
    });
  });

  var params = new URLSearchParams(window.location.search);
  var leadId = params.get('lead_id');
  if (params.get('lead') === 'success' && leadId) {
    var eventKey = 'wg_lead_success_' + leadId;
    if (!window.sessionStorage.getItem(eventKey)) {
      pushEvent('lead_form_success', { lead_id: leadId });
      window.sessionStorage.setItem(eventKey, '1');
    }
  }

  document.querySelectorAll('[data-quote-form]').forEach(function (form) {
    var storageKey = 'wg_quote_draft';
    var fields = form.querySelectorAll('input:not([type="file"]):not([type="hidden"]):not([type="checkbox"]), textarea');

    try {
      var draft = JSON.parse(window.sessionStorage.getItem(storageKey) || '{}');
      fields.forEach(function (field) {
        if (!field.value && draft[field.name]) field.value = draft[field.name];
      });
      fields.forEach(function (field) {
        field.addEventListener('input', function () {
          var current = {};
          fields.forEach(function (item) { current[item.name] = item.value; });
          window.sessionStorage.setItem(storageKey, JSON.stringify(current));
        });
      });
      if (params.get('lead') === 'success') window.sessionStorage.removeItem(storageKey);
    } catch (error) {
      // sessionStorage may be unavailable; the form remains fully server-functional.
    }

    var sourceField = form.querySelector('[name="source_url"]');
    var referrerField = form.querySelector('[name="referrer"]');
    if (sourceField) sourceField.value = window.location.href;
    if (referrerField) referrerField.value = document.referrer;

    ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term'].forEach(function (name) {
      var field = form.querySelector('[name="' + name + '"]');
      if (field) field.value = params.get(name) || '';
    });
  });
})();
