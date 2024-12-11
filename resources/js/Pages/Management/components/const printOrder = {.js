const printOrder = {
    imprimirPedido: function (pedido, printer) {
        if (printer == "epson") {
            this.caracteres = 48;
            //alert('IMPRESSORA EPSON 80MM - 48 CARACTERES POR LINHA');
            /* IMPRESSORA EPSON 80MM - 48 CARACTERES POR LINHA */
        } else if (printer == "epson58") {
            this.caracteres = 42;
            //alert('IMPRESSORA EPSON 58MM - 42 CARACTERES POR LINHA');
            /* IMPRESSORA EPSON 58MM - 42 CARACTERES POR LINHA */
        } else {
            this.caracteres = 38;
            //alert('IMPRESSORA PADRÃO 38 CARACTERES POR LINHA');
        }
        var printData = new Array();
        if (pedido.status.statusId == 6) {
            printData.push("\x1B" + "\x40"); // init
            printData.push("\x1B" + "\x21" + "\x01"); // font B
            printData.push("\x1D\x21\x00"); //Altura
            printData.push("\x1B\x33\x00"); //Spacing
            printData.push("\x1B" + "\x61" + "\x31"); // center align
            printData.push("ROTEIRO DE DISTRIBUICAO DE PEDIDOS" + "\x0A");
            printData.push("\x1B" + "\x45" + "\x0D"); // bold on
            printData.push("\x1D" + "\x21" + "\x11"); // double font size
            printData.push("Pedido " + pedido.id + "\x0A");
            printData.push("\x1B" + "\x45" + "\x0A"); // bold off
            printData.push("\x1D" + "\x21" + "\x00"); // standard font size
            printData.push("\x1D\x21\x00"); //Altura
            printData.push("\x1B\x33\x00"); //Spacing
            printData.push(
                "Cadastrado por " +
                    (pedido.administrador
                        ? pedido.administrador
                        : "Aplicativo Cliente") +
                    "\x0A"
            );
            printData.push(
                "Horario do pedido: " + pedido.horarioPedido + "\x0A"
            );
            printData.push(
                "Entregador " +
                    this.retiraAcento(pedido.entregador.nome) +
                    "\x0A"
            );
            printData.push(
                "Saiu para entrega: " + pedido.horarioAceito + "\x0A"
            );
            printData.push("\x1B" + "\x61" + "\x30"); // left align
            printData.push("\x1D" + "\x21" + "\x00"); // standard font size
            printData.push("\x1D\x21\x00"); //Altura
            printData.push("\x1B\x33\x00"); //Spacing
            printData.push("======================================" + "\x0A");
            if (pedido.agendado != "0") {
                printData.push(
                    "Data Entrega: " + this.formatAgendado(pedido) + "\x0A"
                );
                printData.push(
                    "--------------------------------------" + "\x0A"
                );
            }
            printData.push("\x1B" + "\x45" + "\x0D"); // bold on
            printData.push("Cliente: ");
            printData.push("\x1B" + "\x45" + "\x0A"); // bold off
            printData.push(this.formatCliente(pedido.cliente) + "\x0A");
            var end = "";
            var endereco = this.formatAddress(pedido.endereco);
            endereco.forEach(function (entry) {
                end += entry;
            });
            printData.push(this.retiraAcento(end));
            printData.push(
                "\x0A" + "--------------------------------------" + "\x0A"
            );
            printData.push("\x1B" + "\x61" + "\x30"); // left align
            for (var i = 0; i < pedido.itensPedido.length; i++) {
                printData.push("\x1B" + "\x61" + "\x30"); // left align
                printData.push(
                    pedido.itensPedido[i].qtd +
                        "x " +
                        this.retiraAcento(pedido.itensPedido[i].produto.nome) +
                        "\x0A"
                );
            }
            printData.push("======================================" + "\x0A");
            printData.push("\x1B" + "\x61" + "\x30"); // left align
            if (
                pedido.cliente.outrosContatos != null &&
                pedido.cliente.outrosContatos.length > 0
            ) {
                printData.push(
                    "Outros Contatos: " +
                        this.retiraAcento(cliente.outrosContatos) +
                        "\x0A"
                );
                printData.push(
                    "--------------------------------------" + "\x0A"
                );
            }
            printData.push(
                "TOTAL " + pedido.total + " - " + pedido.formaPagamento + "\x0A"
            );
            printData.push("\x1B" + "\x45" + "\x0A"); // bold off
            printData.push("\x1B" + "\x61" + "\x30"); // left align
            if (parseFloat(pedido.troco.replace(/,/g, ".")) > 0) {
                printData.push(
                    "Troco para: " +
                        pedido.trocoPara +
                        " => R$ " +
                        pedido.troco +
                        "\x0A"
                );
            }
            if (pedido.obs != null && pedido.obs != "") {
                printData.push(
                    "Obs: " + this.retiraAcento(pedido.obs) + "\x0A"
                );
            }
            printData.push("======================================" + "\x0A");
            printData.push("\x1B" + "\x61" + "\x31"); // center align
            printData.push("ATENCAO: NAO VALIDO COMO CUPOM" + "\x0A");
            printData.push("\x0A" + "\x0A" + "\x0A" + "\x0A");
            printData.push("\x1B" + "\x69"); //cut paper
            printData.push("\x10" + "\x14" + "\x01" + "\x00" + "\x05"); // Generate Pulse to kick-out cash drawer**
        } else {
            printData.push({
                type: "raw",
                format: "image",
                data:
                    this.caracteres == 48
                        ? "/images/printer_logo2.png"
                        : "/images/printer_logo.png",
                options: { language: "ESC/POS", dotDensity: "double" },
            });
            printData.push("\x1B" + "\x40"); // init
            printData.push("\x1B" + "\x61" + "\x31"); // center align
            this.caracteres != 38
                ? printData.push("\x1B" + "\x4D" + "\x00")
                : printData.push("\x1B" + "\x21" + "\x01"); // font B
            printData.push("\x1D" + "\x21" + "\x00"); //Altura
            this.caracteres != 38
                ? printData.push("\x1B" + "\x33" + "\x28")
                : printData.push("\x1B" + "\x33" + "\x00"); //Spacing
            //printData.push(this.retiraAcento(pedido.distribuidor.nome) + '\x0A';
            printData.push("www.tokumsede.com.br" + "\x0A");
            printData.push("Central de atendimento:" + "\x0A");
            printData.push("Whatsapp - (83) 9.9882-1242" + "\x0A");
            printData.push("(83) 9.9882-1342" + "\x0A");
            //printData.push(this.formatPhone(pedido.distribuidor.dddTelefone, pedido.distribuidor.telefonePrincipal) + '\x0A';
            printData.push("\x0A" + "\x0A"); // line break (2x)
            printData.push("\x1B" + "\x61" + "\x30"); // left align
            printData.push("\x1B" + "\x45" + "\x0D"); // bold on
            printData.push("\x1D" + "\x21" + "\x11"); // double font size
            printData.push("Pedido " + pedido.id + "\x0A");
            printData.push("\x1B" + "\x45" + "\x0A"); // bold off
            printData.push("\x1D" + "\x21" + "\x00"); // standard font size
            printData.push("\x1D" + "\x21" + "\x00"); //Altura
            this.caracteres != 38
                ? printData.push("\x1B" + "\x33" + "\x28")
                : printData.push("\x1B" + "\x33" + "\x00"); //Spacing
            printData.push("\x1B" + "\x61" + "\x32"); // right align
            printData.push(pedido.horarioPedido + "\x0A");
            printData.push("\x1B" + "\x61" + "\x30"); // left align
            printData.push("\x1D" + "\x21" + "\x00"); // standard font size
            printData.push("\x1D" + "\x21" + "\x00"); //Altura
            this.caracteres != 38
                ? printData.push("\x1B" + "\x33" + "\x28")
                : printData.push("\x1B" + "\x33" + "\x00"); //Spacing
            printData.push(Array(this.caracteres + 1).join("-") + "\x0A");
            if (pedido.agendado != "0") {
                printData.push(
                    "Data Entrega: " + this.formatAgendado(pedido) + "\x0A"
                );
                printData.push(Array(this.caracteres + 1).join("-") + "\x0A");
            }
            printData.push("\x1B" + "\x45" + "\x0D"); // bold on
            printData.push("Cliente: ");
            printData.push("\x1B" + "\x45" + "\x0A"); // bold off
            printData.push(this.formatCliente(pedido.cliente) + "\x0A");
            var end = "";
            var endereco = this.formatAddress(pedido.endereco);
            endereco.forEach(function (entry) {
                end += entry;
            });
            printData.push(this.retiraAcento(end));
            printData.push(
                "\x0A" + Array(this.caracteres + 1).join("=") + "\x0A"
            );
            printData.push("\x1B" + "\x45" + "\x0D"); // bold on
            printData.push("\x1B" + "\x61" + "\x31"); // center align
            printData.push("PRODUTOS" + "\x0A");
            printData.push("\x1B" + "\x45" + "\x0A"); // bold off
            printData.push("\x1B" + "\x61" + "\x30"); // left align
            printData.push(
                this.caracteres == 38
                    ? "ITEM CODIGO NOME    QTD PRECO SUBTOTAL"
                    : "ITEM CODIGO NOME " +
                          Array(this.caracteres - 41).join(" ") +
                          "QUANTIDADE PRECO SUBTOTAL" +
                          "\x0A"
            );
            printData.push(Array(this.caracteres + 1).join("-") + "\x0A");
            for (var i = 0; i < pedido.itensPedido.length; i++) {
                printData.push("\x1B" + "\x61" + "\x30"); // left align
                printData.push(this.produtoL1(pedido, i) + "\x0A");
                printData.push("\x1B" + "\x61" + "\x32"); // right align
                printData.push(this.produtoL2(pedido, i) + "\x0A");
            }
            printData.push(Array(this.caracteres + 1).join("-") + "\x0A");
            var padding = Array(11).join(" ");
            printData.push("\x1B" + "\x45" + "\x0D"); // bold on
            printData.push(
                "TOTAL  R$" +
                    this.pad(
                        padding,
                        pedido.total.substring(2, pedido.total.length),
                        true
                    ) +
                    "\x0A"
            );
            printData.push("\x1B" + "\x45" + "\x0A"); // bold off
            printData.push(Array(this.caracteres + 1).join("=") + "\x0A");
            printData.push("\x1B" + "\x61" + "\x30"); // left align
            printData.push(
                "Forma de Pagamento: " + pedido.formaPagamento + "\x0A"
            );
            if (parseFloat(pedido.troco.replace(/,/g, ".")) > 0) {
                printData.push(
                    "Troco para: " +
                        pedido.trocoPara +
                        " => R$ " +
                        pedido.troco +
                        "\x0A"
                );
            }
            printData.push(Array(this.caracteres + 1).join("-") + "\x0A");
            if (pedido.obs != null && pedido.obs != "") {
                printData.push(
                    "Observacao: " + this.retiraAcento(pedido.obs) + "\x0A"
                );
                printData.push(Array(this.caracteres + 1).join("-") + "\x0A");
            }
            printData.push("\x0A"); // line break
            printData.push("\x1B" + "\x61" + "\x31"); // center align
            printData.push("* GRATOS PELA PREFERENCIA! *" + "\x0A" + "\x0A");
            printData.push("Acesse: www.tokumsede.com.br" + "\x0A");
            printData.push(
                this.caracteres < 48
                    ? "E descubra a importancia de tomar agua" +
                          "\x0A" +
                          "alcalina!"
                    : "E descubra a importancia de tomar agua alcalina!"
            );
            printData.push(
                "\x0A" + "\x0A" + "\x0A" + "\x0A" + "\x0A" + "\x0A" + "\x0A"
            );
            printData.push("\x1B" + "\x69"); //cut paper
            printData.push("\x10" + "\x14" + "\x01" + "\x00" + "\x05"); // Generate Pulse to kick-out cash drawer**
        }
        return printData;
    },
    produtoL1: function (pedido, pos) {
        var padding1 = Array(5).join("0");
        var padding2 = Array(7).join("0");
        var produto = this.pad(padding1, pos + 1, true);
        produto +=
            " " + this.pad(padding2, pedido.itensPedido[pos].idProduto, true);
        produto +=
            " " +
            this.retiraAcento(
                pedido.itensPedido[pos].produto.length > 36
                    ? pedido.itensPedido[pos].produto.substring(0, 36)
                    : pedido.itensPedido[pos].produto.nome
            ).toUpperCase();
        return produto;
    },
    produtoL2: function (pedido, pos) {
        var padding1 = Array(7).join(" ");
        var padding2 = Array(11).join(" ");
        var produto = pedido.itensPedido[pos].qtd + "  x  R$";
        produto += this.pad(
            padding1,
            pedido.itensPedido[pos].preco.substring(
                2,
                pedido.itensPedido[pos].preco.length
            ),
            true
        );
        produto +=
            "  =  R$" +
            this.pad(
                padding2,
                pedido.itensPedido[pos].subtotal.substring(
                    2,
                    pedido.itensPedido[pos].subtotal.length
                ),
                true
            );
        return produto;
    },
    formatAddress: function (endereco) {
        var address = new Array();
        var aux = "Endereco: " + endereco.logradouro;
        if (aux.length > 37) {
            address.push(aux.substring(0, 38));
            aux = aux.substring(38, aux.length);
        }
        if (aux == "" || String(aux + ", n " + endereco.numero).length > 37) {
            address.push(aux);
            aux = "\x0A" + "n " + endereco.numero;
        } else {
            aux += ", n " + endereco.numero;
        }
        if (endereco.complemento != null && endereco.complemento.length > 0) {
            if (String(aux + ", " + endereco.complemento).length > 37) {
                address.push(aux);
                aux = "\x0A" + endereco.complemento;
            } else {
                aux += ", " + endereco.complemento;
            }
        }
        if (String(aux + ", " + endereco.bairro).length > 37) {
            address.push(aux);
            aux = "\x0A" + endereco.bairro;
        } else {
            aux += ", " + endereco.bairro;
        }
        if (
            String(aux + ", " + endereco.cidade + " - " + endereco.estado)
                .length > 37
        ) {
            address.push(aux);
            address.push("\x0A" + endereco.cidade + " - " + endereco.estado);
        } else {
            address.push(
                aux + ", " + endereco.cidade + " - " + endereco.estado
            );
        }
        if (endereco.referencia != null && endereco.referencia.length > 0) {
            if (String(aux + ", " + endereco.referencia).length > 37) {
                address.push("\x0A" + endereco.referencia);
            } else {
                address.push("\x0A" + endereco.referencia);
                //address.push(', ' + endereco.referencia);
            }
        }
        return address;
    },
    formatCliente: function (cliente) {
        var fone = " " + cliente.telefone;
        var nome = this.retiraAcento(cliente.nome);
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
        return nome + this.pad(padding, fone, true);
    },
    formatDate: function (pSize) {
        var padSize = 38 - pSize * 2;
        var padding = Array(padSize + 1).join(" ");
        return this.pad(padding, pedido.horarioPedido.substring(0, 17), true);
    },
    formatAgendado: function (pedido) {
        var dt = pedido.dataAgendada + " (" + pedido.horaInicio + ")";
        var padding = Array(25).join(" ");
        return this.pad(padding, dt, true);
    },
    pad: function (pad, str, padLeft) {
        if (padLeft) {
            //pad left
            return (pad + str).slice(-pad.length);
        } else {
            //pad right
            return (str + pad).substring(0, pad.length);
        }
    },
    retiraAcento: function (palavra) {
        var com_acento = "áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ´`^¨~";
        var sem_acento = "aaaaaeeeeiiiiooooouuuucAAAAAEEEEIIIIOOOOOUUUUC     ";
        for (var l in palavra) {
            for (var l2 in com_acento) {
                if (palavra[l] == com_acento[l2]) {
                    palavra = palavra.replace(palavra[l], sem_acento[l2]);
                }
            }
        }
        //Remover emojis
        var ranges = [
            "\ud83c[\udf00-\udfff]", // U+1F300 to U+1F3FF
            "\ud83d[\udc00-\ude4f]", // U+1F400 to U+1F64F
            "\ud83d[\ude80-\udeff]", // U+1F680 to U+1F6FF
        ];
        palavra = palavra.replace(new RegExp(ranges.join("|"), "g"), "");
        return palavra;
    },
};
