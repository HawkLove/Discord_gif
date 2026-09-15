document.addEventListener('click', async (event) => {
    const button = event.target.closest('[data-copy-url]');

    if (! (button instanceof HTMLElement)) {
        return;
    }

    const url = button.getAttribute('data-copy-url');

    if (! url) {
        return;
    }

    try {
        await navigator.clipboard.writeText(url);
    } catch {
        const input = document.createElement('textarea');
        input.value = url;
        input.setAttribute('readonly', '');
        input.style.position = 'fixed';
        input.style.left = '-9999px';
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        input.remove();
    }

    const original = button.dataset.label || button.textContent.trim();
    button.dataset.label = original;
    button.textContent = 'Copied!';
    button.classList.add('is-copied');

    window.clearTimeout(Number(button.dataset.copyTimer));
    button.dataset.copyTimer = String(window.setTimeout(() => {
        button.textContent = original;
        button.classList.remove('is-copied');
    }, 1600));
});
