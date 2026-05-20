const expressionInput = document.querySelector('#expression');
const keys = document.querySelector('.keys');

function insertAtCursor(value) {
    const start = expressionInput.selectionStart ?? expressionInput.value.length;
    const end = expressionInput.selectionEnd ?? expressionInput.value.length;
    const before = expressionInput.value.slice(0, start);
    const after = expressionInput.value.slice(end);

    expressionInput.value = `${before}${value}${after}`;
    expressionInput.focus();
    expressionInput.setSelectionRange(start + value.length, start + value.length);
}

function removeBeforeCursor() {
    const start = expressionInput.selectionStart ?? expressionInput.value.length;
    const end = expressionInput.selectionEnd ?? expressionInput.value.length;

    if (start !== end) {
        expressionInput.value = expressionInput.value.slice(0, start) + expressionInput.value.slice(end);
        expressionInput.setSelectionRange(start, start);
        return;
    }

    if (start === 0) {
        return;
    }

    expressionInput.value = expressionInput.value.slice(0, start - 1) + expressionInput.value.slice(start);
    expressionInput.setSelectionRange(start - 1, start - 1);
}

keys.addEventListener('click', (event) => {
    const button = event.target.closest('button');

    if (!button) {
        return;
    }

    if (button.hasAttribute('data-clear')) {
        expressionInput.value = '';
        expressionInput.focus();
        return;
    }

    if (button.hasAttribute('data-backspace')) {
        removeBeforeCursor();
        expressionInput.focus();
        return;
    }

    if (button.dataset.value) {
        insertAtCursor(button.dataset.value);
    }
});

expressionInput.addEventListener('keydown', (event) => {
    const serviceKeys = [
        'Backspace',
        'Delete',
        'ArrowLeft',
        'ArrowRight',
        'ArrowUp',
        'ArrowDown',
        'Home',
        'End',
        'Tab',
        'Enter',
    ];

    if (event.ctrlKey || event.metaKey || serviceKeys.includes(event.key)) {
        return;
    }

    if (/^[0-9+\-*/^().!,]$/.test(event.key)) {
        return;
    }

    if (/^[a-zA-Z]$/.test(event.key)) {
        return;
    }

    event.preventDefault();
});
