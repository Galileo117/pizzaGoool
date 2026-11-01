import './bootstrap';

const confirmationDialog = document.getElementById('confirmation-dialog');
const confirmationTitle = document.getElementById('confirmation-dialog-title');
const confirmationMessage = document.getElementById('confirmation-dialog-message');
const confirmationIcon = document.getElementById('confirmation-dialog-icon');
const confirmButton = document.getElementById('confirmation-dialog-confirm');
const cancelButton = document.getElementById('confirmation-dialog-cancel');
const confirmedClicks = new WeakSet();
const confirmedForms = new WeakSet();
let pendingConfirmation = null;

function openConfirmation(action) {
    if (!confirmationDialog || !confirmationDialog.showModal) {
        return window.confirm(action.message);
    }

    pendingConfirmation = action;
    confirmationTitle.textContent = action.title;
    confirmationMessage.textContent = action.message;
    confirmButton.textContent = action.confirmLabel;
    confirmationIcon.classList.toggle('bg-green-50', action.variant === 'success');
    confirmationIcon.classList.toggle('text-goool-green', action.variant === 'success');
    confirmationIcon.classList.toggle('dark:bg-green-900/30', action.variant === 'success');
    confirmationIcon.classList.toggle('dark:text-green-300', action.variant === 'success');
    confirmationIcon.classList.toggle('bg-red-50', action.variant !== 'success');
    confirmationIcon.classList.toggle('text-red-600', action.variant !== 'success');
    confirmationIcon.classList.toggle('dark:bg-red-900/30', action.variant !== 'success');
    confirmationIcon.classList.toggle('dark:text-red-300', action.variant !== 'success');
    confirmationIcon.innerHTML = action.variant === 'success'
        ? '<i class="fa-solid fa-circle-check text-2xl" aria-hidden="true"></i>'
        : '<i class="fa-solid fa-triangle-exclamation text-2xl" aria-hidden="true"></i>';
    confirmButton.classList.toggle('bg-goool-green', action.variant === 'success');
    confirmButton.classList.toggle('hover:bg-goool-green-dark', action.variant === 'success');
    confirmButton.classList.toggle('focus:ring-goool-green', action.variant === 'success');
    confirmButton.classList.toggle('bg-red-600', action.variant !== 'success');
    confirmButton.classList.toggle('hover:bg-red-700', action.variant !== 'success');
    confirmButton.classList.toggle('focus:ring-red-500', action.variant !== 'success');
    confirmationDialog.showModal();

    return false;
}

function getConfirmationAction(element, kind, form = null, submitter = null) {
    return {
        kind,
        element,
        form,
        submitter,
        title: element.dataset.confirmTitle || 'Confirmar acción',
        message: element.dataset.confirmMessage || '¿Deseas continuar con esta acción?',
        confirmLabel: element.dataset.confirmConfirmLabel || 'Confirmar',
        variant: element.dataset.confirmVariant || 'danger',
    };
}

document.addEventListener('click', (event) => {
    const trigger = event.target instanceof Element
        ? event.target.closest('[data-confirm-click]')
        : null;

    if (!trigger) {
        return;
    }

    if (confirmedClicks.has(trigger)) {
        confirmedClicks.delete(trigger);

        return;
    }

    const action = getConfirmationAction(trigger, 'click');

    if (openConfirmation(action) === false) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);

document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement)) {
        return;
    }

    if (confirmedForms.has(form)) {
        confirmedForms.delete(form);

        return;
    }

    const submitter = event.submitter;
    const confirmationSource = submitter?.dataset.confirmMessage ? submitter : form;

    if (!confirmationSource.dataset.confirmMessage) {
        return;
    }

    const action = getConfirmationAction(confirmationSource, 'submit', form, submitter);

    if (openConfirmation(action) === false) {
        event.preventDefault();
        event.stopImmediatePropagation();
    }
}, true);

confirmButton?.addEventListener('click', () => {
    const action = pendingConfirmation;
    pendingConfirmation = null;
    confirmationDialog.close();

    if (!action) {
        return;
    }

    if (action.kind === 'click') {
        confirmedClicks.add(action.element);
        action.element.click();

        return;
    }

    confirmedForms.add(action.form);

    if (action.submitter) {
        action.form.requestSubmit(action.submitter);
    } else {
        action.form.requestSubmit();
    }

    setTimeout(() => confirmedForms.delete(action.form), 0);
});

cancelButton?.addEventListener('click', () => {
    pendingConfirmation = null;
    confirmationDialog.close();
});

confirmationDialog?.addEventListener('close', () => {
    pendingConfirmation = null;
});