document.addEventListener('DOMContentLoaded', () => {
  const feedback = document.getElementById('share-feedback');
  const setFeedback = (msg) => {
    if (feedback) feedback.textContent = msg;
  };

  const shareBtn = document.getElementById('share-btn');
  shareBtn?.addEventListener('click', async () => {
    const url = shareBtn.dataset.shareUrl || window.location.href;
    const text = shareBtn.dataset.shareText || 'Rally result';
    if (navigator.share) {
      try {
        await navigator.share({ title: 'Rally', text, url });
        setFeedback('Shared.');
        return;
      } catch (err) {
        if (err && err.name === 'AbortError') return;
      }
    }
    try {
      await navigator.clipboard.writeText(url);
      setFeedback('Link copied.');
    } catch {
      setFeedback(url);
    }
  });

  const copyBtn = document.getElementById('copy-link-btn');
  copyBtn?.addEventListener('click', async () => {
    const url = copyBtn.dataset.shareUrl || window.location.href;
    try {
      await navigator.clipboard.writeText(url);
      setFeedback('Link copied.');
    } catch {
      setFeedback(url);
    }
  });

  // Card format switcher: tall (social) / square / compact (link preview)
  const card = document.getElementById('share-card');
  const variantBtns = document.querySelectorAll('[data-variant-btn]');
  variantBtns.forEach((btn) => {
    btn.addEventListener('click', () => {
      if (!card) return;
      card.dataset.variant = btn.dataset.variantBtn;
      variantBtns.forEach((b) => b.classList.toggle('is-active', b === btn));
    });
  });
});
