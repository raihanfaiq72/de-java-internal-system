document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-ui-open]');
    if (!btn) return;

    openModal({
        contentId: btn.dataset.content,
        action: btn.dataset.action,
        data: btn.dataset.data ? JSON.parse(btn.dataset.data) : null
    });
});

function openModal({ contentId, action, data }) {
    const modalEl = document.getElementById('uiModal');
    const body = modalEl.querySelector('[data-ui-body]');
    const title = modalEl.querySelector('[data-ui-title]');
    const footer = modalEl.querySelector('[data-ui-footer]');

    const template = document.getElementById(contentId);
    if (!template) {
        console.error('Template not found:', contentId);
        return;
    }

    body.innerHTML = template.innerHTML;

    const form = body.querySelector('[data-ui-form]');
    if (!form) {
        console.error('data-ui-form not found');
        return;
    }

    const mode = action || form.dataset.mode;

    title.innerText = form.dataset[`title${capitalize(mode)}`] ?? '';
    footer.innerHTML = `
        <button class="btn btn-primary w-100">
            ${form.dataset[`submit${capitalize(mode)}`] ?? 'Submit'}
        </button>
    `;

    if (data) fillForm(form, data);

    new bootstrap.Modal(modalEl).show();
}

function fillForm(form, data) {
    Object.keys(data).forEach(key => {
        const field = form.querySelector(`[data-field="${key}"]`);
        if (field) field.value = data[key];
    });
}

function capitalize(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}
