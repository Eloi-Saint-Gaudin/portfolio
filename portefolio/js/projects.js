(function () {
  function escapeHtml(value) {
    return String(value)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }

  function normalize(value) {
    return String(value || '').toLowerCase().trim();
  }

  function matches(project, query) {
    if (!query) return true;
    var q = normalize(query);

    var hay = [project.title, project.summary, project.date]
      .concat(project.technologies || [])
      .join(' ');

    return normalize(hay).indexOf(q) !== -1;
  }

  function renderProject(project) {
    var title = escapeHtml(project.title || 'Projet');
    var date = project.date ? '<div class="muted">' + escapeHtml(project.date) + '</div>' : '';
    var summary = project.summary ? '<p class="muted">' + escapeHtml(project.summary) + '</p>' : '';

    var actionButtons = [];
    if (project.page) {
      actionButtons.push('<a class="btn primary" href="' + escapeHtml(project.page) + '">Détails</a>');
    }

    var tags = Array.isArray(project.technologies) && project.technologies.length
      ? '<div class="tags">' + project.technologies.map(function (t) {
          return '<span class="tag">' + escapeHtml(t) + '</span>';
        }).join('') + '</div>'
      : '';

    if (Array.isArray(project.links) && project.links.length) {
      project.links.forEach(function (l) {
        if (!l || !l.url) return;
        var label = escapeHtml(l.label || 'Lien');
        var url = escapeHtml(l.url);
        actionButtons.push('<a class="btn" href="' + url + '" target="_blank" rel="noopener noreferrer">' + label + '</a>');
      });
    }

    var actions = actionButtons.length
      ? '<div class="actions">' + actionButtons.join('') + '</div>'
      : '';

    return [
      '<article class="project">',
      '<h3>' + title + '</h3>',
      date,
      summary,
      actions,
      tags,
      '</article>'
    ].join('');
  }

  function setStatus(statusEl, text) {
    if (!statusEl) return;
    statusEl.textContent = text;
  }

  async function loadProjects() {
    var grid = document.getElementById('projectsGrid');
    var statusEl = document.getElementById('projectsStatus');
    var searchEl = document.getElementById('projectSearch');

    if (!grid) return;

    setStatus(statusEl, 'Chargement des projets…');

    var projects = [];
    try {
      var res = await fetch('data/projects.json', { cache: 'no-store' });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      projects = await res.json();
      if (!Array.isArray(projects)) projects = [];
    } catch (e) {
      setStatus(statusEl, 'Impossible de charger data/projects.json (ouvre le site via un petit serveur local).');
      grid.innerHTML = '';
      return;
    }

    function render(query) {
      var filtered = projects.filter(function (p) { return matches(p, query); });
      grid.innerHTML = filtered.map(renderProject).join('');
      setStatus(statusEl, filtered.length + ' projet(s)');
    }

    render('');

    if (searchEl) {
      searchEl.addEventListener('input', function () {
        render(searchEl.value);
      });
    }
  }

  loadProjects();
})();
