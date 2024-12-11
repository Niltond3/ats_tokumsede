// Separate text formatting utilities
const TextFormatter = {
    removeAccents: (text) => {
        const accents = "áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ´`^¨~";
        const noAccents = "aaaaaeeeeiiiiooooouuuucAAAAAEEEEIIIIOOOOOUUUUC     ";
        let formatted = text;

        for (let i = 0; i < accents.length; i++) {
            formatted = formatted.replace(
                new RegExp(accents[i], "g"),
                noAccents[i]
            );
        }

        // Remove emojis
        const emojiRanges = [
            "\ud83c[\udf00-\udfff]",
            "\ud83d[\udc00-\ude4f]",
            "\ud83d[\ude80-\udeff]",
        ];
        return formatted.replace(new RegExp(emojiRanges.join("|"), "g"), "");
    },
    pad: (pad, str, padLeft) =>
        padLeft
            ? (pad + str).slice(-pad.length)
            : (str + pad).substring(0, pad.length),
};

// Printer configuration class
class PrinterConfig {
    constructor(printerType) {
        this.characters = this.getCharacterWidth(printerType);
    }

    getCharacterWidth(type) {
        const widths = {
            tks80: 48,
            tks58: 42,
            default: 38,
        };
        return widths[type] || widths.default;
    }
}

// Order formatter function
const OrderFormatter = () => {
    function addAddressComponent(currentLine, component) {
        const combined = `${currentLine}, ${component}`;
        return combined.length > 37 ? `\x0A${component}` : combined;
    }
    function formatAddress(endereco) {
        const address = [];
        let line = `Endereco: ${endereco.logradouro}`;

        // Format address lines
        if (line.length > 37) {
            address.push(line.substring(0, 38));
            line = line.substring(38);
        }

        // Add number
        line = addAddressComponent(line, `n ${endereco.numero}`);

        // Add other components
        if (endereco.complemento) {
            line = addAddressComponent(line, endereco.complemento);
        }

        line = addAddressComponent(line, endereco.bairro);
        address.push(`${line}, ${endereco.cidade} - ${endereco.estado}`);

        if (endereco.referencia) {
            address.push(`\x0A${endereco.referencia}`);
        }

        return address;
    }
    function formatSchedule(pedido) {
        const { pad } = TextFormatter;
        let dt = pedido.dataAgendada + " (" + pedido.horaInicio + ")";
        let padding = " ".repeat(25);
        return pad(padding, dt, true);
    }
    function formatClient(cliente) {
        const { pad, removeAccents } = TextFormatter;
        var fone = " " + cliente.telefone;
        var nome = removeAccents(cliente.nome);
        if (nome.length > 29 - fone.length) {
            nome = nome.substring(0, 29) + "\x0A";
            return (
                nome +
                "\x1B" +
                "\x45" +
                "\x0D" +
                "Telefone:" +
                "\x1B" +
                "\x45" +
                "\x0A" +
                fone
            );
        }
        var padSize = 29 - nome.length;
        var padding = Array(padSize + 1).join(" ");
        return nome + pad(padding, fone, true);
    }
    function formatProductL1(pedido, pos) {
        const { pad, removeAccents } = TextFormatter;
        var padding1 = Array(5).join("0");
        var padding2 = Array(7).join("0");
        var produto = pad(padding1, pos + 1, true);
        produto += " " + pad(padding2, pedido.itensPedido[pos].idProduto, true);
        produto +=
            " " +
            removeAccents(
                pedido.itensPedido[pos].produto.length > 36
                    ? pedido.itensPedido[pos].produto.substring(0, 36)
                    : pedido.itensPedido[pos].produto.nome
            ).toUpperCase();
        return produto;
    }
    function formatProductL2(pedido, pos) {
        const { pad } = TextFormatter;
        var padding1 = Array(7).join(" ");
        var padding2 = Array(11).join(" ");
        var produto = pedido.itensPedido[pos].qtd + "  x  R$";
        produto += pad(
            padding1,
            pedido.itensPedido[pos].preco.substring(
                2,
                pedido.itensPedido[pos].preco.length
            ),
            true
        );
        produto +=
            "  =  R$" +
            pad(
                padding2,
                pedido.itensPedido[pos].subtotal.substring(
                    2,
                    pedido.itensPedido[pos].subtotal.length
                ),
                true
            );
        return produto;
    }
    return {
        formatAddress,
        formatSchedule,
        formatClient,
        formatProductL1,
        formatProductL2,
    };
};

// Main print order function
const imprimirPedido = (pedido, printer) => {
    const { characters } = new PrinterConfig(printer);
    const {
        formatAddress,
        formatSchedule,
        formatClient,
        formatProductL1,
        formatProductL2,
    } = OrderFormatter();
    const { pad, removeAccents } = TextFormatter;
    const printData = [];

    printData.push({
        type: "raw",
        format: "image",
        data:
            characters == 48
                ? "/images/printer_logo2.png"
                : "/images/printer_logo.png",
        options: { language: "ESC/POS", dotDensity: "double" },
    });
    printData.push("\x1B" + "\x40"); // init
    printData.push("\x1B" + "\x61" + "\x31"); // center align
    characters != 38
        ? printData.push("\x1B" + "\x4D" + "\x00")
        : printData.push("\x1B" + "\x21" + "\x01"); // font B
    printData.push("\x1D" + "\x21" + "\x00"); //Altura
    characters != 38
        ? printData.push("\x1B" + "\x33" + "\x28")
        : printData.push("\x1B" + "\x33" + "\x00"); //Spacing
    if (pedido.distribuidor?.nome)
        printData.push(removeAccents(pedido.distribuidor.nome) + "\x0A");
    printData.push("tks.tokumsede.com.br" + "\x0A");
    printData.push("Central de atendimento:" + "\x0A");
    printData.push("Whatsapp - (83) 9.9882-1242" + "\x0A");
    printData.push("(83) 9.9882-1342" + "\x0A");
    printData.push("\x0A" + "\x0A"); // line break (2x)
    printData.push("\x1B" + "\x61" + "\x30"); // left align
    printData.push("\x1B" + "\x45" + "\x0D"); // bold on
    printData.push("\x1D" + "\x21" + "\x11"); // double font size
    printData.push("Pedido " + pedido.id + "\x0A");
    printData.push("\x1B" + "\x45" + "\x0A"); // bold off
    printData.push("\x1D" + "\x21" + "\x00"); // standard font size
    printData.push("\x1D" + "\x21" + "\x00"); //Altura
    characters != 38
        ? printData.push("\x1B" + "\x33" + "\x28")
        : printData.push("\x1B" + "\x33" + "\x00"); //Spacing
    printData.push(
        "Cadastrado por " +
            (pedido.administrador
                ? pedido.administrador
                : "Aplicativo Cliente") +
            "\x0A"
    );
    printData.push("\x1B" + "\x61" + "\x32"); // right align
    printData.push(pedido.horarioPedido + "\x0A");
    printData.push("\x1B" + "\x61" + "\x30"); // left align
    printData.push("\x1D" + "\x21" + "\x00"); // standard font size
    printData.push("\x1D" + "\x21" + "\x00"); //Altura
    characters != 38
        ? printData.push("\x1B" + "\x33" + "\x28")
        : printData.push("\x1B" + "\x33" + "\x00"); //Spacing
    printData.push("\x1B" + "\x61" + "\x32"); // right align
    printData.push(Array(characters + 1).join("-") + "\x0A");
    if (pedido.entregador.nome) {
        printData.push(
            "Entregador " + removeAccents(pedido.entregador.nome) + "\x0A"
        );
    }
    if (pedido.horarioAceito) {
        printData.push("Saiu para entrega: " + pedido.horarioAceito + "\x0A");
        printData.push("\x1B" + "\x61" + "\x30"); // left align
        printData.push("\x1D" + "\x21" + "\x00"); // standard font size
        printData.push("\x1D\x21\x00"); //Altura
        printData.push("\x1B\x33\x00"); //Spacing
        printData.push(Array(characters + 1).join("=") + "\x0A");
    }
    if (pedido.agendado != "0") {
        printData.push("Data Entrega: " + formatSchedule(pedido) + "\x0A");
        printData.push(Array(characters + 1).join("-") + "\x0A");
    }
    printData.push("\x1B" + "\x45" + "\x0D"); // bold on
    printData.push("Cliente: ");
    printData.push("\x1B" + "\x45" + "\x0A"); // bold off
    printData.push(formatClient(pedido.cliente) + "\x0A");
    var end = "";
    var endereco = formatAddress(pedido.endereco);
    endereco.forEach(function (entry) {
        end += entry;
    });
    printData.push(removeAccents(end));
    printData.push("\x0A" + Array(characters + 1).join("=") + "\x0A");
    printData.push("\x1B" + "\x45" + "\x0D"); // bold on
    printData.push("\x1B" + "\x61" + "\x31"); // center align
    printData.push("PRODUTOS" + "\x0A");
    printData.push("\x1B" + "\x45" + "\x0A"); // bold off
    printData.push("\x1B" + "\x61" + "\x30"); // left align
    printData.push(
        characters == 38
            ? "ITEM CODIGO NOME    QTD PRECO SUBTOTAL" + "\x0A"
            : "ITEM CODIGO NOME " +
                  Array(characters - 41).join(" ") +
                  "QUANTIDADE PRECO SUBTOTAL" +
                  "\x0A"
    );
    printData.push(Array(characters + 1).join("-") + "\x0A");
    for (var i = 0; i < pedido.itensPedido.length; i++) {
        printData.push("\x1B" + "\x61" + "\x30"); // left align
        printData.push(formatProductL1(pedido, i) + "\x0A");
        printData.push("\x1B" + "\x61" + "\x32"); // right align
        printData.push(formatProductL2(pedido, i) + "\x0A");
    }
    printData.push(Array(characters + 1).join("-") + "\x0A");
    printData.push("\x1B" + "\x61" + "\x30"); // left align
    if (
        pedido.cliente.outrosContatos != null &&
        pedido.cliente.outrosContatos.length > 0
    ) {
        printData.push(
            "Outros Contatos: " +
                removeAccents(pedido.cliente.outrosContatos) +
                "\x0A"
        );
        printData.push(Array(characters + 1).join("-") + "\x0A");
    }
    var padding = Array(11).join(" ");
    printData.push("\x1B" + "\x45" + "\x0D"); // bold on
    printData.push(
        "TOTAL  R$" +
            pad(padding, pedido.total.substring(2, pedido.total.length), true) +
            "\x0A"
    );
    printData.push("\x1B" + "\x45" + "\x0A"); // bold off
    printData.push(Array(characters + 1).join("=") + "\x0A");
    printData.push("\x1B" + "\x61" + "\x30"); // left align
    printData.push("Forma de Pagamento: " + pedido.formaPagamento + "\x0A");
    if (parseFloat(pedido.troco.replace(/,/g, ".")) > 0) {
        printData.push(
            "Troco para: " +
                pedido.trocoPara +
                " => R$ " +
                pedido.troco +
                "\x0A"
        );
    }
    printData.push(Array(characters + 1).join("-") + "\x0A");
    if (pedido.obs != null && pedido.obs != "") {
        printData.push("Observacao: " + removeAccents(pedido.obs) + "\x0A");
        printData.push(Array(characters + 1).join("-") + "\x0A");
    }
    printData.push("\x0A"); // line break
    printData.push("\x1B" + "\x61" + "\x31"); // center align
    printData.push("ATENCAO: NAO VALIDO COMO CUPOM" + "\x0A");
    printData.push("* GRATOS PELA PREFERENCIA! *" + "\x0A" + "\x0A");
    printData.push("Acesse: tks.tokumsede.com.br" + "\x0A");
    printData.push(
        characters < 48
            ? "E descubra a importancia de tomar agua" + "\x0A" + "alcalina!"
            : "E descubra a importancia de tomar agua alcalina!"
    );
    printData.push("\x0A" + "\x0A" + "\x0A" + "\x0A");
    printData.push("\x1B" + "\x69"); //cut paper
    printData.push("\x10" + "\x14" + "\x01" + "\x00" + "\x05"); // Generate Pulse to kick-out cash drawer**

    return printData;
};

export default imprimirPedido;
