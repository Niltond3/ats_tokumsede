// utils.js - Arquivo unificado para funções utilitárias com aplicação dos princípios SOLID

// ==================
// Importações
// ==================
import { clsx } from "clsx";
import { twMerge } from "tailwind-merge";
import { format, unformat } from "v-money3";
import { ref } from "vue";
import { toast } from "vue-sonner";

// ==================
// 1. ClassNames Utility (Responsabilidade: Gerenciar classes CSS)
// ==================
export const ClassNamesUtil = {
    merge: (...inputs) => twMerge(clsx(inputs)),
};

// ==================
// 2. State Utility (Responsabilidade: Atualização de estados reativos)
// ==================
export function updateValue(updaterOrValue, refValue) {
    // Verifica se o parâmetro é uma função (atualizador) ou um valor simples.
    refValue.value =
        typeof updaterOrValue === "function"
            ? updaterOrValue(refValue.value)
            : updaterOrValue;
}

// ==================
// 3. Error Utility (Responsabilidade: Tratamento e padronização de erros)
// ==================
export const ErrorUtil = {
    getError: (error) => {
        let e = error;
        if (error.response) {
            e = error.response.data;
            // Usa optional chaining para simplificar
            if (error.response.data?.error) e = error.response.data.error;
            if (error.response.data?.message) e = error.response.data.message;
        } else if (error.message) {
            e = error.message;
        } else {
            e = "Ocorreu um erro inesperado";
        }
        if (e === "Unauthenticated") {
            console.error(e);
            window.location.reload();
        }
        return e;
    },
};

// ==================
// 4. Array e Object Utilities (Responsabilidade: Operações em arrays e objetos)
// ==================
export const ArrayUtil = {
    removeEmptyValues: (array) => array.filter(Boolean),
};

export const ObjectUtil = {
    isEmpty: (obj) => Object.keys(obj).length === 0,
};

// ==================
// 5. String Utilities (Responsabilidade: Manipulação e decodificação de strings)
// ==================
export const StringUtil = {
    utf8Decode: (utf8String) => {
        if (typeof utf8String !== "string")
            throw new TypeError("parameter 'utf8String' is not a string");
        return utf8String
            .replace(/[\u00e0-\u00ef][\u0080-\u00bf][\u0080-\u00bf]/g, (c) =>
                String.fromCharCode(
                    ((c.charCodeAt(0) & 0x0f) << 12) |
                    ((c.charCodeAt(1) & 0x3f) << 6) |
                    (c.charCodeAt(2) & 0x3f)
                )
            )
            .replace(/[\u00c0-\u00df][\u0080-\u00bf]/g, (c) =>
                String.fromCharCode(
                    ((c.charCodeAt(0) & 0x1f) << 6) | (c.charCodeAt(1) & 0x3f)
                )
            );
    },
};

// ==================
// 6. Date Utilities (Responsabilidade: Manipulação e formatação de datas)
// ==================
export const DateUtil = {
    isValidDateFormat: (dateString) => {
        const isoRegex = /^\d{4}-\d{2}-\d{2}$/;
        const brRegex = /^\d{2}\/\d{2}\/\d{4}$/;
        if (isoRegex.test(dateString)) return { isValid: true, format: "YYYY-MM-DD" };
        if (brRegex.test(dateString)) return { isValid: true, format: "DD/MM/YYYY" };
        return { isValid: false, format: null };
    },

    getDateComponents: (dateString) => {
        const { isValid, format } = DateUtil.isValidDateFormat(dateString);
        if (!isValid) return null;
        return format === "YYYY-MM-DD"
            ? (([YYYY, MM, DD]) => ({ DD, MM, YYYY }))(dateString.split("-"))
            : (([DD, MM, YYYY]) => ({ DD, MM, YYYY }))(dateString.split("/"));
    },

    // Converte data para ISO. Entrada esperada: "DD/MM/YYYY HH:mm"
    dateToISOFormat: (dateTimeString) => {
        const parts = dateTimeString.split(" ");
        if (parts.length < 2) return null; // Tratamento de casos extremos
        const [date, time] = parts;
        const components = DateUtil.getDateComponents(date);
        if (!components) return null;
        const { DD, MM, YYYY } = components;
        const timeParts = time.split(":");
        if (timeParts.length < 2) return null;
        const [HH, mm] = timeParts;
        return new Date(`${YYYY}-${MM}-${DD}T${HH}:${mm}`);
    },

    dateToDayMonthYearFormat: (rawDate) => {
        if (!rawDate) return rawDate;
        try {
            const date = new Date(rawDate);
            const YYYY = date.getFullYear();
            const unformattedMonth = date.getMonth() + 1;
            const unformattedDay = date.getDate();
            const unformattedHour = date.getHours();
            const unformattedMinutes = date.getMinutes();

            const dd = unformattedDay < 10 ? `0${unformattedDay}` : unformattedDay;
            const MM = unformattedMonth < 10 ? `0${unformattedMonth}` : unformattedMonth;
            const hh = unformattedHour < 10 ? `0${unformattedHour}` : unformattedHour;
            const mm = unformattedMinutes < 10 ? `0${unformattedMinutes}` : unformattedMinutes;

            const extenseDate = DateUtil.checkDate(`${dd}/${MM}/${YYYY} ${hh}:${mm}`);
            return { date: `${dd}/${MM}/${YYYY}`, time: `${hh}:${mm}`, dateTime: `${extenseDate} às ${hh}:${mm}` };
        } catch (err) {
            console.error(err);
            return rawDate;
        }
    },

    checkDate: (dateStr) => {
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const tomorrow = new Date(today);
        tomorrow.setDate(today.getDate() + 1);
        const yesterday = new Date(today);
        yesterday.setDate(today.getDate() - 1);

        const checkDateObj = DateUtil.dateToISOFormat(dateStr);
        if (!checkDateObj) return dateStr.split(" ")[0];
        checkDateObj.setHours(0, 0, 0, 0);

        if (checkDateObj.getTime() === today.getTime()) return "Hoje";
        if (checkDateObj.getTime() === tomorrow.getTime()) return "Amanhã";
        if (checkDateObj.getTime() === yesterday.getTime()) return "Ontem";
        return dateStr.split(" ")[0];
    },
};

// ==================
// 7. Money Utilities (Responsabilidade: Formatação monetária)
// ==================
export const MoneyUtil = {
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

// ==================
// 8. Password Utilities (Responsabilidade: Geração e embaralhamento de senhas)
// ==================
export const PasswordUtil = {
    generatePassword: (options) => {
        const groups = options?.groups ?? ["ABCDEFGHIJKLMNOPQRSTUVWXYZ", "abcdefghijklmnopqrstuvwxyz", "1234567890"];
        const length = options?.length ?? 8;
        // Gera um caractere aleatório para cada grupo inicialmente
        let pass = groups.map((group) => group.charAt(Math.floor(Math.random() * group.length))).join("");
        const str = groups.join("");
        for (let i = pass.length; i < length; i++) {
            pass += str.charAt(Math.floor(Math.random() * str.length));
        }
        // Embaralha usando o algoritmo Fisher-Yates
        const shuffle = (array) => {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
            return array;
        };
        return shuffle(pass.split("")).join("");
    },
};

// ==================
// 9. Clipboard Utilities (Responsabilidade: Formatação para área de transferência)
// ==================
export const ClipboardUtil = {
    formatProductForClipboard: (product) => {
        const { toCurrency } = MoneyUtil.formatMoney();
        const { nome, preco, precoEspecial, quantidade } = product;
        const value = precoEspecial
            ? precoEspecial[precoEspecial.length - 1].val
            : preco[preco.length - 1].val;
        const subtotal = quantidade ? quantidade * value : 0;

        return `${StringUtil.utf8Decode(nome)} un ${toCurrency(value)} ${subtotal !== 0 ? `subtotal: ${toCurrency(subtotal)}` : ""
            }`;
    },

    orderToClipboard: (order) => {
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
        const status = typeof order.status === 'object' ? order.status : OrderUtil.getStatusString(
            order.agendado,
            order.dataAgendada,
            horaInicio,
            order.status
        );
        // const details = [
        //     {
        //         classColor: "",
        //         classIcon: "ri-calendar-fill",
        //         label: { short: "Criado", long: "Horário Criado" },
        //         data: order.horarioPedido,
        //         author: order.administrador,
        //     },
        //     {
        //         classColor: status.classes.text,
        //         classIcon: status.classes.icon,
        //         label: { short: "Status", long: "Status" },
        //         data: status.label,
        //         author: "",
        //         reason: order.retorno,
        //     },
        //     {
        //         classColor: "text-accepted",
        //         classIcon: "ri-timer-fill",
        //         label: { short: "Aceito", long: "Horário Aceito" },
        //         data: order.horarioAceito,
        //         author: order.aceitoPor,
        //     },
        //     {
        //         classColor: "text-dispatched",
        //         classIcon: "ri-timer-fill",
        //         label: { short: "Despachado", long: "Horário Despachado" },
        //         data: order.horarioDespache,
        //         author: order.despachadoPor,
        //     },
        //     {
        //         classColor: "text-info",
        //         classIcon: "ri-timer-fill",
        //         label: { short: "Entregue", long: "Horário Entregue" },
        //         data: order.horarioEntrega,
        //         author: order.entreguePor,
        //     },
        //     {
        //         classColor: "text-danger",
        //         classIcon: "ri-timer-fill",
        //         label: { short: "Cancelado", long: "Horário Cancelado" },
        //         data: order.horarioCancelado,
        //         author: order.canceladoPor,
        //     },
        //     {
        //         classColor: "",
        //         classIcon: "ri-e-bike-fill",
        //         label: { short: "entregador", long: "entregador" },
        //         data: StringUtil.utf8Decode(order.entregador?.nome || ""),
        //         author: "",
        //     },
        //     {
        //         classColor: "",
        //         classIcon: "ri-sticky-note-fill",
        //         label: { short: "Observação", long: "Observação" },
        //         data: StringUtil.utf8Decode(order.obs || ""),
        //         author: "",
        //     },
        // ].filter((item) => item.data);

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
        const troco = toCurrency(order.troco);
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

        try {
            // Formata os dados do pedido para a área de transferência
            navigator.clipboard.writeText(`
---------- Pedido nº ${order.id} ----------
cliente: ${nome}, Telefone: ${telefone}
criado: ${(() => {
                    const date = DateUtil.dateToISOFormat(order.horarioPedido);
                    const minute = date.getMinutes().toString().padStart(2, "0");
                    const hour = date.getHours().toString().padStart(2, "0");
                    const options = { weekday: "long", year: "numeric", month: "short", day: "numeric" };
                    return `${date.toLocaleDateString("pt-BR", options)} as HH:mm: ${hour}:${minute}`;
                })()}
status: ${status.label} ${order.dataAgendada ? `- para ${order.dataAgendada} às ${horaInicio}` : ""
                }
Endereço: ${endereco.logradouro}, nº ${endereco.numero} - ${endereco.complemento}, ${endereco.bairro} - ${endereco.cidade} ${endereco.estado} - ${endereco.referencia}
distribuidor: ${order.distribuidor.nome}
forma de pagamento: ${formaPagamento} ${trocoPara !== "R$ 0,00" ? `- troco: ${troco}` : ""
                }
${order.itensPedido
                    .map(
                        (item) =>
                            `${item.qtd} ${item.produto.nome} un ${item.preco} subtotal ${item.subtotal}`
                    )
                    .join("\n")}
total: ${total}
${order.obs ? `obs: ${order.obs}` : ""}
---------------------------------------`);
            toast.info("Copiado para a área de transferência", { position: "top-center" });
        } catch (error) {
            console.error("Erro ao copiar para a área de transferência", error);
        }
    },
    orderProductsToClipboard: (payload, products) => {
        try {
            // Get money formatter
            const { toCurrency } = MoneyUtil.formatMoney();

            // Map items with product details
            const orderItems = payload.itens.map((item, index) => {
                // Find corresponding product details
                const product = products.find(p => p.id === item.idProduto);
                if (!product) return null;

                return {
                    index: index + 1,
                    quantidade: item.quantidade,
                    nome: StringUtil.utf8Decode(product.nome),
                    preco: toCurrency(item.preco),
                    subtotal: toCurrency(item.subtotal)
                };
            }).filter(Boolean); // Remove null entries

            // Format header
            const header = "*Pedido* ----------------------------------------\n";

            // Format each product line
            const productsText = orderItems
                .map(item =>
                    `*${item.quantidade}* ${item.nome} un ${item.preco} subtotal: ${item.subtotal}`
                )
                .join('\n');

            // Format total
            const total = `\nValor total: *${toCurrency(payload.totalProdutos)}*`;

            // Format footer
            const footer = "\n---------------------------------------------------";

            // Combine all parts
            const clipboardText = `${header}${productsText}${total}${footer}`;

            // Copy to clipboard
            navigator.clipboard.writeText(clipboardText);
            toast.info('Produtos copiados para a área de transferência');

            return true;
        } catch (error) {
            console.error('Erro ao copiar produtos para área de transferência:', error);
            toast.error('Erro ao copiar produtos');
            return false;
        }
    },
    // Add this new method inside ClipboardUtil
    productsToClipboard: (products) => {
        try {
            // Get money formatter
            const { toCurrency } = MoneyUtil.formatMoney();

            // Helper function to check product name patterns
            const hasPattern = (name, pattern) => name.toLowerCase().includes(pattern.toLowerCase());

            // Custom sorting function based on requirements
            const sortProducts = (a, b) => {
                const nameA = StringUtil.utf8Decode(a.nome);
                const nameB = StringUtil.utf8Decode(b.nome);

                // 3.1 Alkalina first
                if (hasPattern(nameA, 'Alkalina') && !hasPattern(nameB, 'Alkalina')) return -1;
                if (!hasPattern(nameA, 'Alkalina') && hasPattern(nameB, 'Alkalina')) return 1;

                // 3.2 20L second
                if (hasPattern(nameA, '20L') && !hasPattern(nameB, '20L')) return -1;
                if (!hasPattern(nameA, '20L') && hasPattern(nameB, '20L')) return 1;

                // 3.3 10L third
                if (hasPattern(nameA, '10L') && !hasPattern(nameB, '10L')) return -1;
                if (!hasPattern(nameA, '10L') && hasPattern(nameB, '10L')) return 1;

                // 3.4 5L fourth
                if (hasPattern(nameA, '5L') && !hasPattern(nameB, '5L')) return -1;
                if (!hasPattern(nameA, '5L') && hasPattern(nameB, '5L')) return 1;

                // 3.5 Alphabetical order
                if (nameA < nameB) return -1;
                if (nameA > nameB) return 1;

                // 3.6 Product ID
                return a.id - b.id;
            };

            // Validate input
            if (!Array.isArray(products) || products.length === 0) {
                throw new Error('Invalid products array');
            }

            // Format header
            const header = "*Temos:*\n__________________________________________________\n\n";

            // Format products
            const formattedProducts = products
                .sort(sortProducts)
                .map(product => {
                    // Validate product structure
                    if (!product.nome || !product.preco || !Array.isArray(product.preco)) {
                        return null;
                    }

                    // Get lowest price from price array
                    const price = product.preco[product.preco.length - 1].val

                    // Format pH in description
                    let description = product.descricao || '';
                    const pHMatch = description.match(/pH\s*(\d+\.?\d*)/i);
                    if (pHMatch) {
                        description = `pH ${pHMatch[1]}`;
                    }

                    return `• *${StringUtil.utf8Decode(product.nome)}* ${StringUtil.utf8Decode(description)
                        } por *${toCurrency(price)}*;\n`;
                })
                .filter(Boolean) // Remove null entries
                .join('\n');

            // Format footer
            const footer = "__________________________________________________";

            // Combine all parts
            const clipboardText = `${header}${formattedProducts}${footer}`;

            // Copy to clipboard
            navigator.clipboard.writeText(clipboardText);
            toast.info('Lista de produtos copiada para a área de transferência');

            return true;
        } catch (error) {
            console.error('Erro ao copiar produtos para área de transferência:', error);
            toast.error('Erro ao copiar lista de produtos');
            return false;
        }
    },

};

// ==================
// 10. Order Utilities (Responsabilidade: Formatação e status de pedidos)
// ==================
export const OrderUtil = {
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

// ==================
// 11. Client Utilities (Responsabilidade: Formatação e obtenção de dados do cliente)
// ==================
export const ClientUtil = {
    getClientFormat: () => {
        const getSexo = {
            mobile: { 1: "Masculino", 2: "Feminino" },
            desktop: { 1: "M", 2: "F" },
        };

        const getTipoPessoaPayload = (documentValue) => {
            if (!documentValue)
                return { tipoPessoa: "1", documento: { CPF: null, CNPJ: null } };
            if (documentValue.length < 14)
                return {
                    tipoPessoa: "1",
                    documento: { CPF: documentValue.replace(/[^a-zA-Z0-9]/g, ""), CNPJ: null },
                };
            return {
                tipoPessoa: "2",
                documento: { CPF: null, CNPJ: documentValue.replace(/[^a-zA-Z0-9]/g, "") },
            };
        };

        return { getSexo, getTipoPessoaPayload };
    },
};

// ==================
// 12. Google Autocomplete Utility (Responsabilidade: Integração com Google Places)
// ==================
export const GoogleAutocompleteUtil = {
    useGoogleAutocomplete: (inputId) => {
        const place = ref();
        const options = {
            componentRestrictions: { country: "br" },
            strictBounds: true,
        };
        const element = document.getElementById(inputId);
        if (!element) {
            console.error(`Elemento com id ${inputId} não foi encontrado`);
            return { place };
        }
        const autocomplete = new google.maps.places.Autocomplete(element, options);
        autocomplete.setFields(["place_id", "geometry", "address_component", "formatted_address"]);
        const infowindow = new google.maps.InfoWindow();
        autocomplete.addListener("place_changed", function () {
            infowindow.close();
            place.value = autocomplete.getPlace();
        });
        // Garante que a propriedade pointerEvents seja redefinida sem causar efeitos colaterais inesperados
        setTimeout(() => (document.body.style.pointerEvents = ""), 0);
        return { place };
    },
};
