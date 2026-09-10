(() => {
  const root = document.querySelector('.srs-home-admin');
  if (!root) return;
  const form = root.querySelector('form');
  const sections = [...root.querySelectorAll('.srs-admin-sections > section')];
  const nav = root.querySelector('.srs-section-nav');
  const names = ['Hero', 'Quote form', 'Trust strip', 'Services', 'Process', 'Locations', 'Reviews', 'FAQ', 'Final CTA'];
  const ids = ['hero', 'form', 'trust', 'services', 'process', 'locations', 'reviews', 'faq', 'cta'];
  const select = (id) => {
    const index = Math.max(0, ids.indexOf(id));
    sections.forEach((section, i) => { section.hidden = i !== index; });
    [...nav.children].forEach((link, i) => {
      if (i === index) link.setAttribute('aria-current', 'true');
      else link.removeAttribute('aria-current');
    });
  };
  sections.forEach((section, i) => {
    section.id = ids[i];
    const link = document.createElement('a');
    link.href = '#' + ids[i];
    link.textContent = names[i];
    link.addEventListener('click', () => select(ids[i]));
    nav.append(link);
  });
  window.addEventListener('hashchange', () => select(location.hash.slice(1)));
  select(location.hash.slice(1));
  let dirty = false;
  const markDirty = () => {
    dirty = true;
    root.querySelector('.srs-save-status').textContent = 'Есть несохранённые изменения';
  };
  form.addEventListener('input', markDirty);
  form.addEventListener('change', markDirty);
  form.addEventListener('submit', () => { dirty = false; });
  window.addEventListener('beforeunload', (event) => {
    if (dirty) { event.preventDefault(); event.returnValue = ''; }
  });
})();
