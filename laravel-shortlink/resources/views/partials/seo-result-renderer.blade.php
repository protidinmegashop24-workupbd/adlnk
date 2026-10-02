<script>
// Shared by the SEO tools that fetch another site's HTML (Meta Tag Checker,
// Canonical Checker, Open Graph Checker). The checked page's own title,
// description, canonical URL etc. end up in these results, so everything
// here is built with createElement/textContent — never innerHTML — so a
// malicious page can't inject a script into klikwit's own origin.
function renderSeoResults(container, checks) {
  container.textContent = '';
  var symbols = { pass: '✓', warning: '!', missing: '×', problem: '×' };

  checks.forEach(function (check) {
    var item = document.createElement('div');
    item.className = 'result-item';

    var head = document.createElement('div');
    head.className = 'r-head';

    var badge = document.createElement('span');
    badge.className = 'result-badge ' + check.status;
    badge.setAttribute('aria-hidden', 'true');
    badge.textContent = symbols[check.status] || '?';
    head.appendChild(badge);

    var label = document.createElement('span');
    label.textContent = check.label + ' — ' + check.status.toUpperCase();
    head.appendChild(label);

    item.appendChild(head);

    var finding = document.createElement('div');
    finding.className = 'r-finding';
    finding.textContent = check.finding;
    item.appendChild(finding);

    var explain = document.createElement('div');
    explain.className = 'r-explain';
    explain.textContent = check.explanation;
    item.appendChild(explain);

    if (check.action) {
      var action = document.createElement('div');
      action.className = 'r-action';
      action.textContent = 'Suggested action: ' + check.action;
      item.appendChild(action);
    }

    container.appendChild(item);
  });
}
</script>
