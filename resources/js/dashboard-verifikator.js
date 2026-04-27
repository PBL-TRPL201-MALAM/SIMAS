document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const sidebarToggle = document.getElementById('sidebar-toggle');

  if (!sidebar || !sidebarToggle) {
    return;
  }

  sidebar.classList.add('xl:translate-x-0');

  sidebarToggle.addEventListener('click', () => {
    sidebar.classList.toggle('-translate-x-full');
  });

  document.addEventListener('click', (event) => {
    const outsideSidebar = !sidebar.contains(event.target) && !sidebarToggle.contains(event.target);

    if (outsideSidebar && window.innerWidth < 1280) {
      sidebar.classList.add('-translate-x-full');
    }
  });
});
