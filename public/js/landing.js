document.addEventListener('DOMContentLoaded', function () {
  // Accordion submenu sidebar. Klik salah satu toggle -> buka; grup lain otomatis tertutup.
  document.querySelectorAll('[data-nav-toggle]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var group = btn.closest('[data-nav-group]');
      var isOpen = group.classList.contains('open');

      document.querySelectorAll('[data-nav-group].open').forEach(function (openGroup) {
        if (openGroup !== group) {
          openGroup.classList.remove('open');
          openGroup.querySelector('[data-nav-toggle]').setAttribute('aria-expanded', 'false');
        }
      });

      group.classList.toggle('open', !isOpen);
      btn.setAttribute('aria-expanded', String(!isOpen));
    });
  });

  // Auto-buka submenu yang mengandung link aktif (current URL)
  var current = window.location.pathname;
  document.querySelectorAll('.submenu a').forEach(function (a) {
    if (a.getAttribute('href') === current) {
      a.classList.add('active');
      var group = a.closest('[data-nav-group]');
      if (group) group.classList.add('open');
    }
  });
});