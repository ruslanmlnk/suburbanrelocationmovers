(function () {
  'use strict';

  document.documentElement.classList.add('srs-js');

  var menuButton = document.querySelector('.srs-menu-toggle');
  var navigation = document.querySelector('.srs-primary-nav');

  function closeMenu() {
    if (!menuButton || !navigation) return;
    menuButton.setAttribute('aria-expanded', 'false');
    navigation.classList.remove('is-open');
    document.body.classList.remove('srs-menu-open');
  }

  if (menuButton && navigation) {
    menuButton.addEventListener('click', function () {
      var open = menuButton.getAttribute('aria-expanded') === 'true';
      menuButton.setAttribute('aria-expanded', String(!open));
      navigation.classList.toggle('is-open', !open);
      document.body.classList.toggle('srs-menu-open', !open);
    });

    navigation.querySelectorAll('.menu-item-has-children').forEach(function (item, index) {
      var submenu = item.querySelector(':scope > .sub-menu');
      var link = item.querySelector(':scope > a');
      if (!submenu || !link) return;

      var submenuId = 'srs-submenu-' + index;
      submenu.id = submenuId;
      var toggle = document.createElement('button');
      toggle.type = 'button';
      toggle.className = 'srs-submenu-toggle';
      toggle.setAttribute('aria-expanded', 'false');
      toggle.setAttribute('aria-controls', submenuId);
      toggle.setAttribute('aria-label', 'Toggle ' + link.textContent.trim() + ' submenu');
      link.insertAdjacentElement('afterend', toggle);

      toggle.addEventListener('click', function () {
        var expanded = toggle.getAttribute('aria-expanded') === 'true';
        toggle.setAttribute('aria-expanded', String(!expanded));
        item.classList.toggle('is-submenu-open', !expanded);
      });
    });

    navigation.addEventListener('click', function (event) {
      if (event.target.closest('a') && !event.target.closest('.menu-item-has-children > a')) closeMenu();
    });

    document.addEventListener('keydown', function (event) {
      if (event.key === 'Escape') closeMenu();
    });

    window.addEventListener('resize', function () {
      if (window.innerWidth > 900) closeMenu();
    });
  }

  document.querySelectorAll('.srs-quote-form input[type="date"]').forEach(function (field) {
    if (!field.min) field.min = new Date().toISOString().split('T')[0];
  });

  document.querySelectorAll('[data-review-slider]').forEach(function (slider) {
    var slides = Array.prototype.slice.call(slider.querySelectorAll('[data-review-slide]'));
    var activeIndex = 0;
    if (!slides.length) return;

    function showSlide(index) {
      activeIndex = (index + slides.length) % slides.length;
      slides.forEach(function (slide, slideIndex) {
        slide.classList.toggle('is-active', slideIndex === activeIndex);
        slide.setAttribute('aria-hidden', slideIndex === activeIndex ? 'false' : 'true');
        var count = slide.querySelector('[data-review-count]');
        if (count) count.textContent = (activeIndex + 1) + ' / ' + slides.length;
      });
    }

    slider.addEventListener('click', function (event) {
      if (event.target.closest('[data-review-prev]')) showSlide(activeIndex - 1);
      if (event.target.closest('[data-review-next]')) showSlide(activeIndex + 1);
    });
    showSlide(0);
  });

  /* Subtle, one-time entrance motion. Elements remain fully visible without JS. */
  var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  if (!reduceMotion && 'IntersectionObserver' in window) {
    var revealItems = [];

    function prepare(selector, variant, delay) {
      document.querySelectorAll(selector).forEach(function (element, index) {
        if (element.classList.contains('srs-reveal')) return;
        element.classList.add('srs-reveal');
        if (variant) element.classList.add('srs-reveal--' + variant);
        element.style.setProperty('--srs-reveal-delay', Math.min(index * (delay || 0), 360) + 'ms');
        revealItems.push(element);
      });
    }

    prepare('.srs-hero-copy > *', '', 85);
    prepare('.srs-hero-form', 'scale', 0);
    prepare('.srs-trust-strip > div', '', 90);
    prepare('.srs-section-head > *, .srs-process-copy, .srs-location-copy, .srs-review-intro, .srs-faq-grid > div, .srs-final-cta-inner > div', '', 90);
    prepare('.srs-service-card, .srs-location-card, .srs-archive-card', '', 75);
    prepare('.srs-steps > li', 'right', 90);
    prepare('.srs-map-panel', 'scale', 0);
    prepare('.srs-review-slider, .srs-faq-list > details', '', 80);
    prepare('.srs-page-hero .srs-container > *, .srs-article-hero-inner > *, .srs-prose > *, .srs-article-content > *', '', 55);

    document.documentElement.classList.add('srs-motion-ready');

    var revealObserver = new IntersectionObserver(function (entries, observer) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -9% 0px', threshold: 0.08 });

    revealItems.forEach(function (element) {
      revealObserver.observe(element);
    });
  }
}());
