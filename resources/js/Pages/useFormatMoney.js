import { ref } from 'vue';
import { format } from 'v-money3';
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

    return [toCurrency, config];
};
