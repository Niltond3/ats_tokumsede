import { dateToDayMonthYearFormat } from "@/util";
import { toast } from "vue-sonner";

export const useEventHandlers = (
    tableProductsState,
    emit,
    addressNote,
    disabledButton
) => ({
    handleCallbackPedido: () => {
        tableProductsState.payload = {
            ...tableProductsState.payload,
            observacao: addressNote.value,
        };
        disabledButton.value = true;
        console.log(tableProductsState.payload);
        emit("callback:payloadPedido", tableProductsState.payload);
    },
    handlePayForm: (value) =>
        (tableProductsState.payload = {
            ...tableProductsState.payload,
            formaPagamento: value,
        }),
    handleExchange: ({ value }) =>
        (tableProductsState.payload = {
            ...tableProductsState.payload,
            trocoPara: parseFloat(value.split(" ")[1]),
        }),
    handleScheduling: (date) => {
        if (date) {
            const { date: formattedDate, time } =
                dateToDayMonthYearFormat(date);

            const dataAgendada = formattedDate;

            const horaInicio = time;

            return (tableProductsState.payload = {
                ...tableProductsState.payload,
                agendado: 1,
                dataAgendada,
                horaInicio,
            });
        }
        return (tableProductsState.payload = {
            ...tableProductsState.payload,
            agendado: 0,
            dataAgendada: "",
            horaInicio: "",
        });
    },
    handleDistributor: (value) =>
        (tableProductsState.payload = {
            ...tableProductsState.payload,
            idDistribuidor: value.toString(),
        }),
    handleUpdateOrderNote: (value) => {
        console.log(value);
        tableProductsState.payload = {
            ...tableProductsState.payload,
            obs: value,
        };
    },
    handleUpdateStatus: (value) => {
        console.log(value);
        const { info, payload, status } = value;
        tableProductsState.status = status;
        tableProductsState.payload = {
            ...tableProductsState.payload,
            status: payload,
        };
        return toast.info(info);
    },
});
