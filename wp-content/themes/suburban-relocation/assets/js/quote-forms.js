(() => {
  'use strict';
  document.querySelectorAll('.srs-quote-form[data-ajax-url]').forEach(form => {
    const button = form.querySelector('[type="submit"]');
    const feedback = form.querySelector('.srs-quote-feedback');
    const date = form.querySelector('[name="move_date"]');
    if (date) {
      const now = new Date();
      date.min = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}`;
    }
    let busy = false;
    form.addEventListener('submit', async event => {
      event.preventDefault();
      if (busy || !form.reportValidity()) return;
      busy = true;
      button.disabled = true;
      form.setAttribute('aria-busy', 'true');
      feedback.hidden = false;
      feedback.classList.remove('srs-form-error');
      feedback.textContent = 'Sending your request…';
      try {
        // Refresh the nonce at submission time: cached landing pages can contain an expired one.
        const nonceResponse = await fetch(form.dataset.ajaxUrl, {
          method: 'POST', credentials: 'same-origin', cache: 'no-store',
          body: new URLSearchParams({action: 'srs_quote_nonce'})
        });
        const nonce = await nonceResponse.json();
        if (!nonceResponse.ok || !nonce.success || !nonce.data.nonce) throw new Error();
        const payload = new FormData(form);
        payload.set('srs_quote_nonce', nonce.data.nonce);
        const response = await fetch(form.dataset.ajaxUrl, {
          method: 'POST', credentials: 'same-origin', body: payload
        });
        const result = await response.json();
        if (!response.ok || !result.success) {
          feedback.classList.add('srs-form-error');
          feedback.textContent = result.data?.message || 'We could not submit your request. Please try again or call us.';
        } else {
          feedback.textContent = result.data.message;
          // Keep confirmation local to the submitted form; no page reload or loss on errors.
          form.querySelector('.srs-form-grid').hidden = true;
          form.querySelector('.srs-form-grid').style.display = 'none';
          button.hidden = true;
          button.style.display = 'none';
        }
        feedback.focus();
      } catch (_) {
        feedback.classList.add('srs-form-error');
        feedback.textContent = 'We could not confirm your request. Your details are still here. Please try again or call us.';
        feedback.focus();
      } finally {
        busy = false;
        button.disabled = false;
        form.removeAttribute('aria-busy');
      }
    });
  });
})();
