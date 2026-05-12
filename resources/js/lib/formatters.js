export function formatMoney(value) {
    return new Intl.NumberFormat('ru-RU', {
        style: 'currency',
        currency: 'RUB',
        maximumFractionDigits: 0,
    }).format(Number(value || 0));
}

export function formatQuantity(value) {
    return new Intl.NumberFormat('ru-RU', {
        maximumFractionDigits: 3,
    }).format(Number(value || 0));
}

export function formatDate(value) {
    if (!value) return '—';
    return new Intl.DateTimeFormat('ru-RU').format(new Date(value));
}

export function formatDateTime(value) {
    if (!value) return '—';
    return new Intl.DateTimeFormat('ru-RU', {
        dateStyle: 'short',
        timeStyle: 'short',
    }).format(new Date(value));
}
