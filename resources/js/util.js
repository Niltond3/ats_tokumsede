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
            e = "Unknown error occured";
        }
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

export function dateToDayMonthYearFormat(date) {
    const YYYY = date.getFullYear();
    const unformattedMonth = date.getMonth() + 1;
    const unformattedDay = date.getDate();
    const unformattedHour = date.getHours();
    const unformattedMinutes = date.getMinutes();

    const dd = unformattedDay < 10 ? `0${unformattedDay}` : unformattedDay;
    const MM =
        unformattedMonth < 10 ? `0${unformattedMonth}` : unformattedMonth;
    const hh = unformattedHour < 10 ? `0${unformattedHour}` : unformattedHour;
    const mm =
        unformattedMinutes < 10 ? `0${unformattedMinutes}` : unformattedMinutes;

    return {
        date: `${dd}/${MM}/${YYYY}`,
        time: `${hh}:${mm}`,
    };
}

// A data passada deve estar no padrão:
// DD/MM/YYYY HH:mm
export function dateToISOFormat(dateTimeString) {
    // Primeiro, dividimos a data completa em duas partes:
    const [date, time] = dateTimeString.split(" ");

    // Dividimos a data em dia, mês e ano:
    const [DD, MM, YYYY] = date.split("/");

    // Dividimos o tempo em hora e minutos:
    const [HH, mm] = time.split(":");

    // Retornamos a data formatada em um padrão compatível com ISO:
    const formattedDate = `${YYYY}-${MM}-${DD}T${HH}:${mm}`;
    return new Date(formattedDate);
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
