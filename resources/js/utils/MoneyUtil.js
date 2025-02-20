import { format, unformat } from "v-money3";

export default {
    formatMoney: () => {
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
            modelModifiers: { number: false },
            shouldRound: true,
            focusOnRight: true,
        };
        const toCurrency = (value) => format(value, config);
        const toFloat = (value) => unformat(value, config);
        return { toCurrency, toFloat, config };
    },
};
