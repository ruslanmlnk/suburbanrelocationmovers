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
}());
