// ClipboardUtil.js
import { toast } from "vue-sonner";
import { MoneyUtil, StringUtil, OrderUtil, DateUtil } from "@/util";
import { PAYMENT_METHODS } from "./Constants";
import { usePixQrGenerator, generatePixPayload } from "@/composables/usePixQrGenerator";
import QRCode from "qrcode";

/**
 * Converts a Data URL to a Blob.
 *
 * @param {string} dataUrl - The Data URL to convert.
 * @returns {Blob} - The resulting Blob.
 */
export function dataURLToBlob(dataUrl) {
    const parts = dataUrl.split(",");
    const mime = parts[0].match(/:(.*?);/)[1];
    const bstr = atob(parts[1]);
    let n = bstr.length;
    const u8arr = new Uint8Array(n);
    while (n--) {
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], { type: mime });
}

/**
 * Converts a Blob to a Data URL.
 *
 * @param {Blob} blob - The Blob to convert.
 * @returns {Promise<string>} - A promise that resolves with the Data URL.
 */
export function blobToDataURL(blob) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onloadend = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
    });
}

/**
 * Escapes HTML special characters in a string.
 *
 * @param {string} str - The string to escape.
 * @returns {string} - The escaped string.
 */
export function escapeHtml(str) {
    return str.replace(/[&<>"']/g, (m) => {
        switch (m) {
            case "&":
                return "&amp;";
            case "<":
                return "&lt;";
            case ">":
                return "&gt;";
            case '"':
                return "&quot;";
            case "'":
                return "&#039;";
            default:
                return m;
        }
    });
}

/**
 * Copies both plain text and HTML content (with an embedded PNG image) to the clipboard.
 * The HTML content includes a <pre> block with the text and an <img> tag for the image.
 *
 * @param {string} textContent - The plain text to copy.
 * @param {Blob} imageBlob - The PNG image Blob for the QR Code.
 * @returns {Promise<void>}
 */
export async function copyTextAndImageToClipboard(textContent, imageBlob) {
    try {
        let imageDataUrl = "";
        if (imageBlob) {
            imageDataUrl = await blobToDataURL(imageBlob);
        }
        // Build HTML content with text and embedded image (if available)
        const htmlContent = `
      <div>
        <pre>${escapeHtml(textContent)}</pre>
        ${imageDataUrl ? `<img src="${imageDataUrl}" alt="QR Code" style="max-width:100%;height:auto;" />` : ""}
      </div>
    `;
        // Prepare clipboard items for plain text and HTML content
        const clipboardItems = {
            "text/plain": new Blob([textContent], { type: "text/plain" }),
            "text/html": new Blob([htmlContent], { type: "text/html" }),
        };
        if (imageBlob) {
            clipboardItems["image/png"] = imageBlob;
        }
        const clipboardItem = new ClipboardItem(clipboardItems);
        await navigator.clipboard.write([clipboardItem]);
        console.info("Conteúdo copiado para a área de transferência.");
    } catch (error) {
        console.error("Erro ao copiar para a área de transferência:", error);
        throw error;
    }
}

/**
 * Formats product details for clipboard display.
 *
 * @param {Object} product - The product object.
 * @returns {string} - A formatted string with product details (in Portuguese).
 */
export function formatProductForClipboard(product) {
    const { toCurrency } = MoneyUtil.formatMoney();
    const { nome, preco, precoEspecial, quantidade } = product;
    const value =
        precoEspecial && precoEspecial.length
            ? precoEspecial[precoEspecial.length - 1].val
            : preco[preco.length - 1].val;
    const subtotal = quantidade ? quantidade * value : 0;
    return `${StringUtil.utf8Decode(nome)} un ${toCurrency(value)}${subtotal !== 0 ? ` subtotal: ${toCurrency(subtotal)}` : ""
        }`;
}

/**
 * Copies order details (as plain text) to the clipboard.
 * The order text remains in Brazilian Portuguese.
 *
 * @param {Object} order - The order object.
 */
export function orderToClipboard(order) {
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
    const status =
        typeof order.status === "object"
            ? order.status
            : OrderUtil.getStatusString(order.agendado, order.dataAgendada, horaInicio, order.status);

    const nome = StringUtil.utf8Decode(order.cliente.nome);
    const telefoneFormatted = phoneMatch
        ? `(${phoneMatch[1]}) 9 ${phoneMatch[2]}-${phoneMatch[3]}`
        : order.cliente.telefone;
    const total = toCurrency(order.total);
    const troco = toCurrency(order.troco);
    const trocoPara = toCurrency(order.trocoPara);
    const responseEndereco = order.endereco;
    const cepFormatted = cepMatch.length > 1 ? `${cepMatch[1]}-${cepMatch[2]}` : null;
    const formaPagamento = PAYMENT_METHODS[order.formaPagamento == 0 ? 1 : order.formaPagamento];

    const endereco = {
        ...responseEndereco,
        cep: cepFormatted,
        logradouro: StringUtil.utf8Decode(responseEndereco.logradouro || ""),
        bairro: StringUtil.utf8Decode(responseEndereco.bairro || ""),
        complemento: StringUtil.utf8Decode(responseEndereco.complemento || ""),
        referencia: StringUtil.utf8Decode(responseEndereco.referencia || ""),
        cidade: StringUtil.utf8Decode(responseEndereco.cidade || ""),
        apelido: StringUtil.utf8Decode(responseEndereco.apelido || ""),
        observacao: StringUtil.utf8Decode(responseEndereco.observacao || ""),
        cliente: { ...responseEndereco.cliente, nome, telefone: telefoneFormatted },
    };

    const clipboardText = `
---------- Pedido nº ${order.id} ----------
cliente: ${nome}, Telefone: ${telefoneFormatted}
criado: ${(() => {
            const date = DateUtil.dateToISOFormat(order.horarioPedido);
            const minute = date.getMinutes().toString().padStart(2, "0");
            const hour = date.getHours().toString().padStart(2, "0");
            const options = { weekday: "long", year: "numeric", month: "short", day: "numeric" };
            return `${date.toLocaleDateString("pt-BR", options)} as ${hour}:${minute}`;
        })()}
status: ${status.label} ${order.dataAgendada ? `- para ${order.dataAgendada} às ${horaInicio}` : ""}
Endereço: ${endereco.logradouro}, nº ${endereco.numero} - ${endereco.complemento}, ${endereco.bairro} - ${endereco.cidade} ${endereco.estado} - ${endereco.referencia}
distribuidor: ${order.distribuidor.nome}
forma de pagamento: ${formaPagamento} ${trocoPara !== "R$ 0,00" ? `- troco: ${troco}` : ""}
${order.itensPedido
            .map((item) => `${item.qtd} ${item.produto.nome} un ${item.preco} subtotal ${item.subtotal}`)
            .join("\n")}
total: ${total}
${order.obs ? `obs: ${order.obs}` : ""}
---------------------------------------`.trim();

    try {
        navigator.clipboard.writeText(clipboardText);
        toast.info("Copiado para a área de transferência", { position: "top-center" });
    } catch (error) {
        console.error("Erro ao copiar para a área de transferência", error);
    }
}

/**
 * Copia os detalhes dos produtos do pedido para a área de transferência como texto.
 * Esta função formata uma lista detalhada dos produtos e informações de pagamento,
 * incluindo o código Pix "copia e cola" quando uma chave Pix é fornecida, mas
 * sem incluir a imagem do QR Code.
 *
 * @param {Object} payload - O payload do pedido.
 * @param {Array<Object>} products - A lista de objetos de produtos.
 * @param {string} pix_key - A chave Pix para gerar o código "copia e cola".
 * @returns {Promise<boolean>} - Resolve para true se for bem-sucedido.
 */
export async function orderProductsToClipboard(payload, products, pix_key) {
    try {
        const { toCurrency } = MoneyUtil.formatMoney();

        // Mapeia os produtos com detalhes
        const orderItems = payload.itens.map((item, index) => {
            const product = products.find((p) => p.id === item.idProduto);
            if (!product) return null;
            return {
                index: index + 1,
                quantidade: item.quantidade,
                nome: StringUtil.utf8Decode(product.nome),
                preco: toCurrency(item.preco),
                subtotal: toCurrency(item.subtotal),
            };
        }).filter(Boolean);

        const header = "*Pedido* ----------\n";
        const productsText = orderItems
            .map((item) => `*${item.quantidade}* ${item.nome} un ${item.preco} subtotal: ${item.subtotal}`)
            .join("\n");
        const paymentMethod = PAYMENT_METHODS[payload.formaPagamento] || "Não especificado";
        const paymentInfo = `\nForma de pagamento: *${paymentMethod}*`;
        const total = `\nValor total: *${toCurrency(payload.totalProdutos)}*`;
        const footer = "\n--------------------";

        // Base do texto para clipboard
        let clipboardText = `${header}${productsText}${paymentInfo}${total}${footer}`;

        // Se tiver chave Pix, adiciona o código "copia e cola"
        if (pix_key) {
            // Gera o payload Pix "código copia e cola" usando generatePixPayload
            const formatAmount = (amountStr) => {
                const cleaned = amountStr.replace(/[^\d,]/g, "").replace(",", ".");
                return parseFloat(cleaned).toFixed(2);
            };
            const formattedAmount = formatAmount(toCurrency(payload.totalProdutos));
            const pixPayload = generatePixPayload({
                pixKey: pix_key,
                amount: formattedAmount,
                merchantName: "ALCALINA COMERCIO DE AGUA MINERAL EIRELI",
                merchantCity: "JERICO",
                txid: "*",
            });

            // Adiciona a mensagem do QR Code e o payload Pix ao texto do clipboard
            clipboardText = `${header}${productsText}${paymentInfo}${total}\n\n*Código para pagamento, copia e cola*\n${pixPayload}\n${footer}`;
        }

        // Copia apenas o texto para a área de transferência
        await navigator.clipboard.writeText(clipboardText);
        toast.info("Detalhes do pedido copiados para a área de transferência");
        console.log("clipboardText", clipboardText);
        return true;
    } catch (error) {
        console.error("Erro ao copiar detalhes do pedido para a área de transferência:", error);
        toast.error("Erro ao copiar os detalhes do pedido");
        return false;
    }
}

/**
 * Gera e copia apenas a imagem do QR Code Pix para a área de transferência.
 * Esta função utiliza os valores do pedido para gerar um QR Code Pix e
 * copiá-lo para a área de transferência como uma imagem.
 *
 * @param {Object} payload - O payload do pedido, contendo pelo menos o valor total.
 * @param {string} pix_key - A chave Pix para gerar o QR Code.
 * @param {Object} [options] - Opções adicionais para a geração do QR Code.
 * @param {string} [options.merchantName="COMERCIO TESTE"] - O nome do estabelecimento.
 * @param {string} [options.merchantCity="BRASILIA"] - A cidade do estabelecimento.
 * @param {string} [options.txid="*"] - O identificador da transação.
 * @returns {Promise<boolean>} - Resolve para true se for bem-sucedido.
 */
export async function qrCodeToClipboard(payload, pix_key, options = {}) {
    try {
        if (!pix_key) {
            toast.error("Chave Pix não fornecida. Impossível gerar QR Code.");
            return false;
        }

        const { toCurrency } = MoneyUtil.formatMoney();
        const defaultOptions = {
            merchantName: "COMERCIO TESTE",
            merchantCity: "BRASILIA",
            txid: "*",
            ...options
        };

        // Use o gerador de QR Code Pix para gerar o Blob PNG do QR Code
        const pixQr = usePixQrGenerator(pix_key, {
            amount: toCurrency(payload.totalProdutos), // ex: "R$ 26,00"
            merchantName: defaultOptions.merchantName,
            merchantCity: defaultOptions.merchantCity,
            txid: defaultOptions.txid,
        });

        const qrCodeBlob = await pixQr.getPreparedQrCodePng();

        if (!qrCodeBlob) {
            throw new Error("Falha ao gerar o QR Code");
        }

        // Copia apenas a imagem para a área de transferência
        const clipboardItem = new ClipboardItem({
            [qrCodeBlob.type]: qrCodeBlob
        });

        await navigator.clipboard.write([clipboardItem]);
        toast.info("QR Code Pix copiado para a área de transferência");
        return true;
    } catch (error) {
        console.error("Erro ao copiar QR Code para a área de transferência:", error);
        toast.error("Erro ao copiar o QR Code");
        return false;
    }
}

/**
 * Função auxiliar que permite copiar tanto os detalhes do pedido quanto o QR Code Pix
 * em uma sequência, orientando o usuário através de notificações toast.
 *
 * Esta função executa orderProductsToClipboard e qrCodeToClipboard em sequência,
 * fornecendo instruções para o usuário sobre como usar ambos.
 *
 * @param {Object} payload - O payload do pedido.
 * @param {Array<Object>} products - A lista de objetos de produtos.
 * @param {string} pix_key - A chave Pix para gerar o código e QR Code.
 * @returns {Promise<boolean>} - Resolve para true se todo o processo for bem-sucedido.
 */
export async function copyOrderWithQrCode(payload, products, pix_key) {
    try {
        // Primeiro copiamos o QR Code
        const qrResult = await qrCodeToClipboard(payload, pix_key);

        if (qrResult) {
            toast.info("QR Code copiado! Cole-o primeiro onde desejar.", {
                position: "top-center",
                duration: 4000
            });

            // Depois de um breve intervalo, copiamos o texto e notificamos o usuário
            setTimeout(async () => {
                const textResult = await orderProductsToClipboard(payload, products, pix_key);
                if (textResult) {
                    toast.info("Agora os detalhes do pedido foram copiados! Cole-os após o QR Code.", {
                        position: "top-center",
                        duration: 5000
                    });
                }
            }, 2000);

            return true;
        } else {
            // Se não conseguir copiar o QR Code, tenta pelo menos copiar o texto
            return await orderProductsToClipboard(payload, products, pix_key);
        }
    } catch (error) {
        console.error("Erro ao processar o pedido com QR Code:", error);
        toast.error("Erro ao processar o pedido completo");
        return false;
    }
}

/**
 * Copies the formatted list of products to the clipboard.
 *
 * @param {Array<Object>} products - The array of product objects.
 * @returns {boolean} - Returns true if successful.
 */
export function productsToClipboard(products) {
    try {
        const { toCurrency } = MoneyUtil.formatMoney();

        // Helper to check if a product name contains a given pattern
        const hasPattern = (name, pattern) =>
            name.toLowerCase().includes(pattern.toLowerCase());

        // Custom sorting function based on specific rules
        const sortProducts = (a, b) => {
            const nameA = StringUtil.utf8Decode(a.nome);
            const nameB = StringUtil.utf8Decode(b.nome);

            if (hasPattern(nameA, "Alkalina") && !hasPattern(nameB, "Alkalina"))
                return -1;
            if (!hasPattern(nameA, "Alkalina") && hasPattern(nameB, "Alkalina"))
                return 1;
            if (hasPattern(nameA, "20L") && !hasPattern(nameB, "20L")) return -1;
            if (!hasPattern(nameA, "20L") && hasPattern(nameB, "20L")) return 1;
            if (hasPattern(nameA, "10L") && !hasPattern(nameB, "10L")) return -1;
            if (!hasPattern(nameA, "10L") && hasPattern(nameB, "10L")) return 1;
            if (hasPattern(nameA, "5L") && !hasPattern(nameB, "5L")) return -1;
            if (!hasPattern(nameA, "5L") && hasPattern(nameB, "5L")) return 1;
            if (nameA < nameB) return -1;
            if (nameA > nameB) return 1;
            return a.id - b.id;
        };

        if (!Array.isArray(products) || products.length === 0) {
            throw new Error("Array de produtos inválido");
        }

        const header = "*Temos:*\n__________________________________________________\n\n";
        const formattedProducts = products
            .sort(sortProducts)
            .map((product) => {
                if (!product.nome || !product.preco || !Array.isArray(product.preco))
                    return null;
                const price = product.preco[product.preco.length - 1].val;
                let description = product.descricao || "";
                const pHMatch = description.match(/pH\s*(\d+\.?\d*)/i);
                if (pHMatch) {
                    description = `pH ${pHMatch[1]}`;
                }
                return `• *${StringUtil.utf8Decode(product.nome)}* ${StringUtil.utf8Decode(description)} por *${toCurrency(price)}*;\n`;
            })
            .filter(Boolean)
            .join("\n");
        const footer = "__________________________________________________";
        const clipboardText = `${header}${formattedProducts}${footer}`;

        navigator.clipboard.writeText(clipboardText);
        toast.info("Lista de produtos copiada para a área de transferência");
        return true;
    } catch (error) {
        console.error("Erro ao copiar lista de produtos para a área de transferência:", error);
        toast.error("Erro ao copiar a lista de produtos");
        return false;
    }
}

/**
 * Copies the Pix "código copia e cola" to the clipboard.
 * This function generates the Pix payload string according to the official guidelines
 * and copies it as plain text. The payload remains in Brazilian Portuguese.
 *
 * @param {Object} params - The parameters for generating the Pix code.
 * @param {string} params.pixKey - A chave Pix (CPF, CNPJ, email, telefone ou UUID).
 * @param {string} params.amount - O valor da transação (ex: "R$ 26,00").
 * @param {string} [params.merchantName="COMERCIO TESTE"] - O nome do estabelecimento.
 * @param {string} [params.merchantCity="BRASILIA"] - A cidade do estabelecimento.
 * @param {string} [params.txid="*"] - O identificador da transação.
 * @returns {Promise<boolean>} - Resolves to true if the Pix code was copied successfully.
 */
export async function copyPixCodeToClipboard({ pixKey, amount, merchantName = "COMERCIO TESTE", merchantCity = "BRASILIA", txid = "*" }) {
    try {
        const formatAmount = (amountStr) => {
            const cleaned = amountStr.replace(/[^\d,]/g, "").replace(",", ".");
            return parseFloat(cleaned).toFixed(2);
        };
        const formattedAmount = formatAmount(amount);
        const pixPayload = generatePixPayload({ pixKey, amount: formattedAmount, merchantName, merchantCity, txid });
        await navigator.clipboard.writeText(pixPayload);
        toast.info("Código Pix copia e cola copiado para a área de transferência", { position: "top-center" });
        return true;
    } catch (error) {
        console.error("Erro ao copiar o código Pix para a área de transferência", error);
        toast.error("Erro ao copiar o código Pix");
        return false;
    }
}

// Default export object for backward compatibility
export default {
    dataURLToBlob,
    blobToDataURL,
    escapeHtml,
    copyTextAndImageToClipboard,
    formatProductForClipboard,
    orderToClipboard,
    orderProductsToClipboard,
    qrCodeToClipboard,
    copyOrderWithQrCode,
    productsToClipboard,
    copyPixCodeToClipboard,
};
