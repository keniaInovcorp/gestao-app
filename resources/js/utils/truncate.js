export function truncate(text, maxLength = 24) {
    if (!text) return text;
    const str = String(text);
    if (str.length <= maxLength) return str;
    return str.substring(0, maxLength) + '...';
}

