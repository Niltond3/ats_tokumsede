import { toast } from "vue-sonner";
import { MoneyUtil, StringUtil, OrderUtil, DateUtil } from '@/util'


export default {
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

        // const originToString = {
        //     1: "App Android",
        //     2: "App IOS",
        //     3: "Plataforma",
        //     4: "Auto atendimento Web",
        // };

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
        // const origem = originToString[order.origem];

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
