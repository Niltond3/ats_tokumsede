import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";
import { format, unformat } from "v-money3";

export function cn(...inputs) {
    return twMerge(clsx(inputs));
}

export function valueUpdater(updaterOrValue, ref) {
    ref.value =
        typeof updaterOrValue === "function"
            ? updaterOrValue(ref.value)
            : updaterOrValue;
}

export const errorUtils = {
    getError: (error) => {
        let e = error;
        if (error.response) {
            e = error.response.data; // data, status, headers
            if (error.response.data && error.response.data.error) {
                e = error.response.data.error; // my app specific keys override
            }
        } else if (error.message) {
            e = error.message;
        } else {
            e = "Ocorreu um erro inesperado";
        }
        console.error(e);
        return e;
    },
};

export function utf8Decode(utf8String) {
    if (typeof utf8String != "string")
        throw new TypeError("parameter ‘utf8String’ is not a string");

    const unicodeString = utf8String
        .replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, (c) => {
            return String.fromCharCode(
                ((c.charCodeAt(0) & 0x0f) << 12) |
                ((c.charCodeAt(1) & 0x3f) << 6) |
                (c.charCodeAt(2) & 0x3f)
            );
        })
        .replace(/[\u00c0-\u00df][\u0080-\u00bf]/g, (c) => {
            return String.fromCharCode(
                ((c.charCodeAt(0) & 0x1f) << 6) | (c.charCodeAt(1) & 0x3f)
            );
        });
    return unicodeString;
}

export function dateToDayMonthYearFormat(rawDate) {
    try {
        const date = new Date(rawDate);
        const YYYY = date.getFullYear();
        const unformattedMonth = date.getMonth() + 1;
        const unformattedDay = date.getDate();
        const unformattedHour = date.getHours();
        const unformattedMinutes = date.getMinutes();

        const dd = unformattedDay < 10 ? `0${unformattedDay}` : unformattedDay;
        const MM =
            unformattedMonth < 10 ? `0${unformattedMonth}` : unformattedMonth;
        const hh =
            unformattedHour < 10 ? `0${unformattedHour}` : unformattedHour;
        const mm =
            unformattedMinutes < 10
                ? `0${unformattedMinutes}`
                : unformattedMinutes;


        const extenseDate = checkDate(`${dd}/${MM}/${YYYY} ${hh}:${mm}`)
        return {
            date: `${dd}/${MM}/${YYYY}`,
            time: `${hh}:${mm}`,
            dateTime: `${extenseDate} às ${hh}:${mm}`,
        };
    } catch (err) {
        console.error(err);
        return rawDate;
    }
}

function isValidDateFormat(dateString) {
    // Check YYYY-MM-DD format
    const isoRegex = /^\d{4}-\d{2}-\d{2}$/;

    // Check DD/MM/YYYY format
    const brRegex = /^\d{2}\/\d{2}\/\d{4}$/;

    if (isoRegex.test(dateString)) {
        return {
            isValid: true,
            format: 'YYYY-MM-DD'
        };
    }

    if (brRegex.test(dateString)) {
        return {
            isValid: true,
            format: 'DD/MM/YYYY'
        };
    }

    return {
        isValid: false,
        format: null
    };
}

function getDateComponents(dateString) {
    const { isValid, format } = isValidDateFormat(dateString);

    if (!isValid) return ''

    const loadBaseFormat = {
        'YYYY-MM-DD': () => {
            const [YYYY, MM, DD] = dateString.split('-');
            return { DD, MM, YYYY };
        },
        'DD/MM/YYYY': () => {
            const [DD, MM, YYYY] = dateString.split('/');
            return { DD, MM, YYYY };
        }
    }
    return loadBaseFormat[format] ? loadBaseFormat[format]() : null;

}

// A data passada deve estar no padrão:
// DD/MM/YYYY HH:mm
export function dateToISOFormat(dateTimeString) {
    // Primeiro, dividimos a data completa em duas partes:
    const [date, time] = dateTimeString.split(" ");

    // Dividimos a data em dia, mês e ano:
    if (date == 'null' || !date || !time) return null

    const { DD, MM, YYYY } = getDateComponents(date)


    // Dividimos o tempo em hora e minutos:
    const [HH, mm] = time.split(":");

    // Retornamos a data formatada em um padrão compatível com ISO:
    const formattedDate = `${YYYY}-${MM}-${DD}T${HH}:${mm}`;

    return new Date(formattedDate);
}

export function checkDate(date) {
    // Get today's date
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Reset to start of day (00:00:00)

    // Get tomorrow's and yesterday's date
    const tomorrow = new Date(today);
    tomorrow.setDate(today.getDate() + 1);

    const yesterday = new Date(today);
    yesterday.setDate(today.getDate() - 1);

    // Check if the date is today, tomorrow, or yesterday
    const checkDate = dateToISOFormat(date);
    checkDate.setHours(0, 0, 0, 0); // Reset to start of day (00:00:00)

    if (checkDate.getTime() === today.getTime()) {
        return "Hoje";
    } else if (checkDate.getTime() === tomorrow.getTime()) {
        return "Amanhã";
    } else if (checkDate.getTime() === yesterday.getTime()) {
        return "Ontem";
    } else {
        const [rawDate, _] = date.split(" ");
        return rawDate;
    }
}

export const removeEmptyValues = (array) => {
    const filtered = array.filter(Boolean);

    return filtered;
};

export const formatMoney = () => {
    const config = {
        debug: false,
        masked: false,
        prefix: "R$ ",
        suffix: "",
        thousands: ".",
        decimal: ",",
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
        focusOnRight: true,
    };
    const toCurrency = (value) => format(value, config);

    const toFloat = (value) => unformat(value, config);

    return { toCurrency, toFloat, config };
};

function getRandomChar(str) {
    return str.charAt(Math.floor(Math.random() * str.length));
}

const shuffle = (array) => {
    for (let i = array.length - 1; i > 0; i--) {
        let j = Math.floor(Math.random() * (i + 1));
        let temp = array[i];
        let temp2 = array[j];

        [temp, temp2] = [temp2, temp];
    }
    return array;
};

export function generatePassword(options) {
    const groups = options?.groups ?? [
        "ABCDEFGHIJKLMNOPQRSTUVWXYZ",
        "abcdefghijklmnopqrstuvwxyz",
        "1234567890",
    ];
    const length = options?.length ?? 8;
    let pass = groups.map(getRandomChar).join("");

    const str = groups.join("");

    for (let i = pass.length; i <= length; i++) {
        pass += getRandomChar(str);
    }
    return shuffle(pass);
}

export const isObjectEmpty = (objectName) =>
    Object.keys(objectName).length === 0;
