/* SportsPlay Admin UI interactions (no frameworks) */

(function () {
  const sidebar = document.querySelector('.sp-sidebar');
  const burger = document.querySelector('[data-burger]');

  if (burger && sidebar) {
    burger.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });

    // close sidebar when clicking outside on small screens
    document.addEventListener('click', (e) => {
      const isSmall = window.matchMedia('(max-width: 860px)').matches;
      if (!isSmall) return;
      const clickedInside = sidebar.contains(e.target) || burger.contains(e.target);
      if (!clickedInside) sidebar.classList.remove('open');
    });
  }

  // Generic client-side table filter
  // Usage:
  //   <input data-table-search="#tableId" ...>
  //   <select data-table-filter="#tableId" data-col="3"> ...</select>
  const applyFilters = (tableEl) => {
    const id = '#' + tableEl.id;

    const search = document.querySelector(`[data-table-search="${id}"]`);
    const filters = [...document.querySelectorAll(`[data-table-filter="${id}"]`)];

    const query = (search?.value || '').trim().toLowerCase();

    const filterRules = filters.map((el) => {
      const col = Number(el.getAttribute('data-col'));
      const val = (el.value || '').trim().toLowerCase();
      return { col, val };
    });

    const rows = [...tableEl.querySelectorAll('tbody tr')];
    rows.forEach((tr) => {
      const cells = [...tr.children].map(td => (td.textContent || '').trim().toLowerCase());

      const matchesSearch = !query || cells.some(t => t.includes(query));
      const matchesFilters = filterRules.every((r) => {
        if (!r.val) return true;
        const c = cells[r.col] || '';
        return c.includes(r.val);
      });

      tr.style.display = (matchesSearch && matchesFilters) ? '' : 'none';
    });
  };

  document.querySelectorAll('table[id]').forEach((tbl) => {
    const id = '#' + tbl.id;
    const search = document.querySelector(`[data-table-search="${id}"]`);
    const filters = [...document.querySelectorAll(`[data-table-filter="${id}"]`)];

    const handler = () => applyFilters(tbl);

    if (search) search.addEventListener('input', handler);
    filters.forEach(f => f.addEventListener('change', handler));
  });

  // Dialog open/close
  document.querySelectorAll('[data-dialog-open]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-dialog-open');
      const dlg = document.querySelector(id);
      if (dlg && typeof dlg.showModal === 'function') dlg.showModal();
    });
  });

  document.querySelectorAll('[data-dialog-close]').forEach((btn) => {
    btn.addEventListener('click', () => {
      const dlg = btn.closest('dialog');
      if (dlg && typeof dlg.close === 'function') dlg.close();
    });
  });

  // Simple view/tabs switcher
  // Usage:
  //   <div class="sp-viewtabs" data-viewtabs>
  //     <button data-view="list" class="active">List</button>
  //     <button data-view="calendar">Calendar</button>
  //   </div>
  //   <section data-viewpane="list">...</section>
  //   <section data-viewpane="calendar" hidden>...</section>
  document.querySelectorAll('[data-viewtabs]').forEach((tabs) => {
    const btns = [...tabs.querySelectorAll('[data-view]')];
    if (!btns.length) return;

    const root = tabs.closest('section, main, .sp-card') || document;
    const panes = [...root.querySelectorAll('[data-viewpane]')];
    if (!panes.length) return;

    const activate = (key) => {
      btns.forEach((b) => b.classList.toggle('active', b.getAttribute('data-view') === key));
      panes.forEach((p) => {
        const show = p.getAttribute('data-viewpane') === key;
        p.hidden = !show;
      });
    };

    btns.forEach((b) => b.addEventListener('click', () => activate(b.getAttribute('data-view'))));

    // initial
    const initial = btns.find(b => b.classList.contains('active'))?.getAttribute('data-view') || btns[0].getAttribute('data-view');
    activate(initial);
  });
})();
