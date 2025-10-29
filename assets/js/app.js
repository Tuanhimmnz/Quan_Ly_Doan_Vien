// Sidebar toggle
(function () {
  var btn = document.getElementById('sidebarToggle');
  if (btn) {
    btn.addEventListener('click', function () {
      document.body.classList.toggle('sidebar-open');
    });
  }
})();

// Auto-hide alerts and close buttons
(function () {
  var alerts = document.querySelectorAll('.alert');
  if (alerts.length) {
    setTimeout(function () {
      alerts.forEach(function (a) { a.classList.add('hide'); });
    }, 3000);
  }
  document.addEventListener('click', function (e) {
    if (e.target.classList.contains('btn-close')) {
      var p = e.target.closest('.alert');
      if (p) p.classList.add('hide');
    }
  });
})();

