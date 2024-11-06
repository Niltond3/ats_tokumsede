
import { clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'
import { ref } from 'vue';
import { format } from 'v-money3';

export function cn(...inputs) {
    return twMerge(clsx(inputs))
}

export function valueUpdater(updaterOrValue, ref) {
    ref.value = typeof updaterOrValue === 'function'
        ? updaterOrValue(ref.value)
        : updaterOrValue
}

export function utf8Decode(utf8String) {
    if (typeof utf8String != 'string')
        throw new TypeError('parameter ‘utf8String’ is not a string');

    const unicodeString = utf8String
        .replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, (c) => {
            return String.fromCharCode(((c.charCodeAt(0) & 0x0f) << 12)
                | ((c.charCodeAt(1) & 0x3f) << 6)
                | (c.charCodeAt(2) & 0x3f));
        })
        .replace(/[\u00c0-\u00df][\u0080-\u00bf]/g, (c) => {
            return String.fromCharCode((c.charCodeAt(0) & 0x1f) << 6
                | c.charCodeAt(1) & 0x3f);
        });
    return unicodeString;
}

// A data passada deve estar no padrão:
// DD/MM/YYYY HH:mm
export function dateToISOFormat(dateTimeString) {
    // Primeiro, dividimos a data completa em duas partes:
    const [date, time] = dateTimeString.split(' ');

    // Dividimos a data em dia, mês e ano:
    const [DD, MM, YYYY] = date.split('/');

    // Dividimos o tempo em hora e minutos:
    const [HH, mm] = time.split(':');

    // Retornamos a data formatada em um padrão compatível com ISO:
    const formattedDate = `${YYYY}-${MM}-${DD}T${HH}:${mm}`;;
    return new Date(formattedDate)
}

export const removeEmptyValues = array => {
    const filtered = array.filter(Boolean);

    return filtered;
};

export const formatMoney = () => {

    const config = {
        debug: false,
        masked: false,
        prefix: 'R$ ',
        suffix: '',
        thousands: '.',
        decimal: ',',
        precision: 2,
        disableNegative: false,
        disabled: false,
        min: null,
        max: null,
        allowBlank: false,
        minimumNumberOfCharacters: 0,
        modelModifiers: {
            number: false,
        },
        shouldRound: true,
        focusOnRight: false,
    };

    const toCurrency = value => format(value, config);

    return { toCurrency, config };
};
