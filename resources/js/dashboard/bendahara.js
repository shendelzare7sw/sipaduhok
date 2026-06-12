document.addEventListener('DOMContentLoaded', () => {
  const page = document.querySelector('.bendahara-dashboard-page');

  if (!page) {
    return;
  }

  const tabs = page.querySelectorAll('.action-tab');
  const panels = page.querySelectorAll('.tab-panel');

  tabs.forEach((tab) => {
    tab.addEventListener('click', () => {
      const targetId = tab.getAttribute('data-tab');
      const targetPanel = targetId ? page.querySelector(`#${targetId}`) : null;

      tabs.forEach((item) => item.classList.remove('active'));
      panels.forEach((panel) => panel.classList.remove('active'));

      tab.classList.add('active');

      if (targetPanel) {
        targetPanel.classList.add('active');
      }
    });
  });

  if (!window.bootstrap) {
    return;
  }

  page.querySelectorAll('[title]').forEach((tooltipTrigger) => {
    new window.bootstrap.Tooltip(tooltipTrigger);
  });
});
