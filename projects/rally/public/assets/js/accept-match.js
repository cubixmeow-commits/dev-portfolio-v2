document.addEventListener('DOMContentLoaded', () => {
  const source = document.getElementById('accept-source');
  const ack = document.getElementById('baseline-acknowledged');
  if (!source) return;

  source.addEventListener('change', () => {
    if (ack) ack.checked = false;
    const id = source.value;
    const url = new URL(window.location.href);
    if (id) {
      url.searchParams.set('source_id', id);
    } else {
      url.searchParams.delete('source_id');
    }
    window.location.href = url.toString();
  });
});
