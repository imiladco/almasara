document.addEventListener('DOMContentLoaded', () => {
  const menuItems = [
    { trigger: 'has-sub-menu-category', target: 'sub-menu-category' },
    { trigger: 'has-sub-menu-profile', target: 'sub-menu-profile' },
    { trigger: 'has-sub-menu-fragment', target: 'sub-menu-fragment' }
  ];

  menuItems.forEach(({ trigger, target }) => {
    const triggerElement = document.getElementById(trigger);
    const targetElement = document.getElementById(target);
    let timeoutId;

    if (triggerElement && targetElement) {
      triggerElement.addEventListener('mouseenter', () => {
        clearTimeout(timeoutId);
        targetElement.classList.replace('opacity-0', 'opacity-1');
        targetElement.classList.replace('invisible', 'visible');
      });

      triggerElement.addEventListener('mouseleave', () => {
        timeoutId = setTimeout(() => {
          targetElement.classList.replace('opacity-1', 'opacity-0');
          targetElement.classList.replace('visible', 'invisible');
        }, 300);
      });

      targetElement.addEventListener('mouseenter', () => {
        clearTimeout(timeoutId);
      });

      targetElement.addEventListener('mouseleave', () => {
        timeoutId = setTimeout(() => {
          targetElement.classList.replace('opacity-1', 'opacity-0');
          targetElement.classList.replace('visible', 'invisible');
        }, 300);
      });
    }
  });

  const searchTrigger = document.getElementById('has-sub-menu-search');
  const searchTarget = document.getElementById('sub-menu-search');
  const searchClose = document.getElementById('close-sub-menu-search');

  if (searchTrigger && searchTarget && searchClose) {
    searchTrigger.addEventListener('click', () => {
      searchTarget.classList.replace('opacity-0', 'opacity-1');
      searchTarget.classList.replace('invisible', 'visible');
    });

    searchClose.addEventListener('click', () => {
      searchTarget.classList.replace('opacity-1', 'opacity-0');
      searchTarget.classList.replace('visible', 'invisible');
    });
  }
});