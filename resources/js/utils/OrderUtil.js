import { DateUtil, MoneyUtil, StringUtil, OrderUtil } from "@/util";

export default {
    getStatusString: (agendado, dataAgendada, horaInicio, status) => {
        const dateIso = DateUtil.dateToISOFormat(`${dataAgendada} ${horaInicio}`);
        const currentDate = new Date();
        const scheduleDate = new Date(dateIso);
        const timeDiff = (scheduleDate - currentDate) / (1000 * 60);
        const statusKey =
            agendado == 1 && currentDate < scheduleDate && timeDiff > 30
                ? 9
                : [2, 3, 4, 5].includes(status)
                    ? 2
                    : status;


        const statusString = {
            1: { label: "Pendente", classes: { bg: "bg-warning", text: "text-warning", icon: "ri-error-warning-fill" } },
            2: { label: "Cancelado", classes: { bg: "bg-danger", text: "text-danger", icon: "ri-close-circle-fill" } },
            3: { label: "Não Localizado", classes: { bg: "bg-danger", text: "text-danger", icon: "ri-close-circle-fill" } },
            4: { label: "Trote", classes: { bg: "bg-danger", text: "text-danger", icon: "ri-close-circle-fill" } },
            5: { label: "Recusado", classes: { bg: "bg-danger", text: "text-danger", icon: "ri-close-circle-fill" } },
            6: { label: "Despachado", classes: { bg: "bg-dispatched", text: "text-dispatched", icon: "ri-e-bike-2-fill" } },
            7: { label: "Entregue", classes: { bg: "bg-info", text: "text-info", icon: "ri-check-double-fill" } },
            8: { label: "Aceito", classes: { bg: "bg-accepted", text: "text-accepted", icon: "ri-check-double-fill" } },
            9: { label: "Agendado", classes: { bg: "bg-muted", text: "text-gray-400", icon: "ri-calendar-schedule-fill" } },
        };

        return statusString[statusKey];
    },

    formatOrder: (order) => {
        const { toCurrency } = MoneyUtil.formatMoney();
        const phoneRegex = /^\s*(\d{2}|\d{0})[-. ]?(\d{5}|\d{4})[-. ]?(\d{4})[-. ]?\s*$/;
        const cepRegex = /(\d{5})-?(\d{3})/;
        const phone = `${order.cliente.dddTelefone}${order.cliente.telefone}`;
        const postalCode = order.endereco?.cep;
        const phoneMatch = phone.replace(/\D/g, "").match(phoneRegex);
        const cepMatch = (postalCode && postalCode.replace(/\D/g, "").match(cepRegex)) || [];
        const removeSeconds = (horario) =>
            /^\d{2}:\d{2}:\d{2}$/.test(horario) ? horario.slice(0, 5) : horario;
        const horaInicio = removeSeconds(order.horaInicio);
        const status = OrderUtil.getStatusString(
            order.agendado,
            order.dataAgendada,
            horaInicio,
            order.status
        );


        const details = [
            {
                classColor: "",
                classIcon: "ri-calendar-fill",
                label: { short: "Criado", long: "Horário Criado" },
                data: DateUtil.dateToDayMonthYearFormat(order.horarioPedido)?.dateTime,
                author: order.administrador,
            },
            {
                classColor: status.classes.text,
                classIcon: status.classes.icon,
                label: { short: "Status", long: "Status" },
                data: status.label,
                author: "",
                reason: order.retorno,
            },
            {
                classColor: "text-accepted",
                classIcon: "ri-timer-fill",
                label: { short: "Aceito", long: "Horário Aceito" },
                data: DateUtil.dateToDayMonthYearFormat(order.horarioAceito)?.dateTime,
                author: order.aceitoPor,
            },
            {
                classColor: "text-dispatched",
                classIcon: "ri-timer-fill",
                label: { short: "Despachado", long: "Horário Despachado" },
                data: DateUtil.dateToDayMonthYearFormat(order.horarioDespache)?.dateTime,
                author: order.despachadoPor,
            },
            {
                classColor: "text-info",
                classIcon: "ri-timer-fill",
                label: { short: "Entregue", long: "Horário Entregue" },
                data: DateUtil.dateToDayMonthYearFormat(order.horarioEntrega)?.dateTime,
                author: order.entreguePor,
            },
            {
                classColor: "text-danger",
                classIcon: "ri-timer-fill",
                label: { short: "Cancelado", long: "Horário Cancelado" },
                data: DateUtil.dateToDayMonthYearFormat(order.horarioCancelado)?.dateTime,
                author: order.canceladoPor,
            },
            {
                classColor: "",
                classIcon: "ri-e-bike-fill",
                label: { short: "entregador", long: "entregador" },
                data: StringUtil.utf8Decode(order.entregador?.nome || ""),
                author: "",
            },
            {
                classColor: "",
                classIcon: "ri-sticky-note-fill",
                label: { short: "Observação", long: "Observação" },
                data: StringUtil.utf8Decode(order.obs || ""),
                author: "",
            },
        ].filter((item) => item.data);

        const paymentFormToString = {
            1: "Dinheiro",
            2: "Cartão",
            3: "Pix",
            4: "Transferência",
            5: "Ifood",
        };
        const originToString = {
            1: "App Android",
            2: "App IOS",
            3: "Plataforma",
            4: "Auto atendimento Web",
        };

        const nome = StringUtil.utf8Decode(order.cliente.nome);
        const telefone = phoneMatch
            ? `(${phoneMatch[1]}) 9 ${phoneMatch[2]}-${phoneMatch[3]}`
            : order.cliente.telefone;

        const total = toCurrency(order.total);
        const troco = order.trocoPara > 0 ? toCurrency(order.trocoPara - order.total) : "";
        const trocoPara = toCurrency(order.trocoPara);
        const responseEndereco = order.endereco;
        const cep =
            cepMatch.length > 1 ? `${cepMatch[1]}-${cepMatch[2]}` : null;
        const formaPagamento =
            paymentFormToString[order.formaPagamento == 0 ? 1 : order.formaPagamento];
        const origem = originToString[order.origem];

        const endereco = {
            ...responseEndereco,
            cep,
            logradouro: StringUtil.utf8Decode(responseEndereco.logradouro || ""),
            bairro: StringUtil.utf8Decode(responseEndereco.bairro || ""),
            complemento: StringUtil.utf8Decode(responseEndereco.complemento || ""),
            referencia: StringUtil.utf8Decode(responseEndereco.referencia || ""),
            cidade: StringUtil.utf8Decode(responseEndereco.cidade || ""),
            apelido: StringUtil.utf8Decode(responseEndereco.apelido || ""),
            observacao: StringUtil.utf8Decode(responseEndereco.observacao || ""),
            cliente: { ...responseEndereco.cliente, nome, telefone },
        };

        return {
            ...order,
            distribuidor: {
                ...order.distribuidor,
                nome: StringUtil.utf8Decode(order.distribuidor.nome),
            },
            cliente: { ...order.cliente, telefone, nome },
            endereco,
            status,
            statusId: order.status,
            details,
            total,
            troco,
            trocoPara,
            formaPagamento,
            origem,
            horaInicio,
        };
    },
};
