// Load more for the activity feed. The button is a real link (works
// without JS); with JS it fetches the next page as a fragment and
// appends in place.
(function () {
  'use strict';

  var button = document.getElementById('feed-more-button');
  var list = document.getElementById('feed-list');
  if (!button || !list) return;

  button.addEventListener('click', function (event) {
    event.preventDefault();
    button.setAttribute('aria-disabled', 'true');
    button.textContent = 'Loading';

    var cursor = button.dataset.cursor;
    var url = button.dataset.fragmentUrl + '&before=' + encodeURIComponent(cursor);

    fetch(url, { headers: { Accept: 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (data) {
        list.insertAdjacentHTML('beforeend', data.html);
        if (data.nextCursor) {
          button.dataset.cursor = data.nextCursor;
          button.removeAttribute('aria-disabled');
          button.textContent = 'Load more';
        } else {
          button.parentElement.remove();
        }
      })
      .catch(function () {
        // Fall back to plain navigation on any hiccup.
        window.location.href = button.href;
      });
  });
})();
