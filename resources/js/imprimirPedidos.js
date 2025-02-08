import qz from 'qz-tray'

export default {
    connect: function () {
        qz.websocket.connect().then(function () {
            qz.printers.find().then(function (printers) {
                for (let i = 0; i < this.impressoras.length; i++) {
                    for (let p = 0; p < printers.length; p++) {
                        if (this.impressoras[i] == printers[p]) {
                            this.printer = printers[p];
                        }
                        if (this.printer) { break; }
                    }
                    if (this.printer) { break; }
                }
                this.config = qz.configs.create(this.printer);
            }).catch(function (error) {
                Toast.fire({
                    type: 'warning',
                    title: "Impressora não localizada",
                    text: error
                });
            });
        });
    },
    imprimirPedido: function () {
        var formaPagamento;
        switch (this.pedido.formaPagamento) {
            case 1: formaPagamento = 'DINHEIRO'
                break;
            case 2: formaPagamento = 'CARTAO'
                break;
            case 3: formaPagamento = 'PIX'
                break;
            case 4: formaPagamento = 'TRANSFERENCIA'
                break;
            case 5: formaPagamento = 'IFOOD'
                break;
            default: formaPagamento = 'OUTROS'
                break;
        }
        if (/mobile|android/i.test(navigator.userAgent)) {
            if (this.pedido.status == 8) {
                var printData = '';
                printData += '\x1B' + '\x40'; 				// init
                printData += '\x1B' + '\x21' + '\x01';         // font B
                printData += '\x1D\x21\x00';                  //Altura
                printData += '\x1B\x33\x00';                  //Spacing
                printData += '\x1B' + '\x61' + '\x31';		// center align
                printData += 'ROTEIRO DE DISTRIBUICAO DE PEDIDOS' + '\x0A';
                printData += '\x1B' + '\x45' + '\x0D';		// bold on
                printData += '\x1D' + '\x21' + '\x11';		// double font size
                printData += 'Pedido ' + this.pedido.id + '\x0A';
                printData += '\x1B' + '\x45' + '\x0A';		// bold off
                printData += '\x1D' + '\x21' + '\x00';		// standard font size
                printData += '\x1D\x21\x00';                  //Altura
                printData += '\x1B\x33\x00';                  //Spacing
                printData += "Cadastrado por " + (this.pedido.administrador ? this.pedido.administrador : 'Aplicativo Cliente') + '\x0A';
                printData += "Horario do pedido: " + this.pedido.horarioPedido + '\x0A';
                printData += "Entregador " + this.retiraAcento(this.pedido.entregador.nome) + '\x0A';
                printData += "Saiu para entrega: " + this.pedido.horarioAceito + '\x0A';
                printData += '\x1B' + '\x61' + '\x30'; 		// left align
                printData += '\x1D' + '\x21' + '\x00';		// standard font size
                printData += '\x1D\x21\x00';                  //Altura
                printData += '\x1B\x33\x00';                  //Spacing
                printData += '======================================' + '\x0A';
                if (this.pedido.agendado != '0') {
                    printData += 'Data Entrega: ' + this.formatAgendado() + '\x0A';
                    printData += '--------------------------------------' + '\x0A';
                }
                printData += '\x1B' + '\x45' + '\x0D';		// bold on
                printData += 'Cliente: ';
                printData += '\x1B' + '\x45' + '\x0A';		// bold off
                printData += this.formatCliente() + '\x0A';
                var end = '';
                var endereco = this.formatAddress();
                endereco.forEach(function (entry) {
                    end += entry;
                });
                printData += this.retiraAcento(end);
                printData += '\x0A' + '--------------------------------------' + '\x0A';
                printData += '\x1B' + '\x61' + '\x30'; 		// left align
                for (var i = 0; i < this.itensPedido.length; i++) {
                    printData += '\x1B' + '\x61' + '\x30'; 	// left align
                    printData += this.itensPedido[i].qtd + "x " + this.retiraAcento(this.itensPedido[i].produto.nome) + '\x0A';
                }
                printData += '======================================' + '\x0A';
                printData += '\x1B' + '\x61' + '\x30'; 		// left align
                if (this.cliente.outrosContatos != null && this.cliente.outrosContatos.length > 0) {
                    printData += 'Outros Contatos: ' + this.retiraAcento(this.cliente.outrosContatos) + '\x0A';
                    printData += '--------------------------------------' + '\x0A';
                }
                printData += 'TOTAL ' + this.pedido.total + ' - ' + formaPagamento + '\x0A';
                printData += '\x1B' + '\x45' + '\x0A';		// bold off
                printData += '\x1B' + '\x61' + '\x30'; 		// left align
                if (parseFloat(this.pedido.troco.replace(/,/g, '.')) > 0) {
                    printData += 'Troco para: ' + this.pedido.trocoPara + ' => R$ ' + this.pedido.troco + '\x0A';
                }
                if (this.pedido.obs != null && this.pedido.obs != '') {
                    printData += 'Obs: ' + this.retiraAcento(this.pedido.obs) + '\x0A';
                }
                printData += '======================================' + '\x0A';
                printData += '\x1B' + '\x61' + '\x31';		// center align
                printData += 'ATENCAO: NAO VALIDO COMO CUPOM' + '\x0A';
                printData += '\x0A' + '\x0A' + '\x0A' + '\x0A';
                printData += '\x1B' + '\x69'; //cut paper
                printData += '\x10' + '\x14' + '\x01' + '\x00' + '\x05'; // Generate Pulse to kick-out cash drawer**

                document.location = 'intent:#Intent;scheme=rawbt;component=ru.a402d.rawbtprinter.activity.PrintDownloadActivity;package=ru.a402d.rawbtprinter;end;';                //Abre download caso app não estiver instalado ou abre o app para configurar caso não estiver como serviço, mas deixa espaço entre logo e nome distribuição
                document.location = 'rawbt:base64,' + window.btoa(printData);
            } else {
                var wprintData = '';
                printData += '\x1B' + '\x40'; 				// init
                printData += '\x1B' + '\x61' + '\x31'; 		// center align
                printData += '\x1B' + '\x21' + '\x01';         // font B
                printData += '\x1D\x21\x00';                  //Altura
                printData += '\x1B\x33\x00';                  //Spacing
                //printData+=this.retiraAcento(this.pedido.distribuidor.nome) + '\x0A';
                printData += 'tks.tokumsede.com.br' + '\x0A';
                printData += 'Central de atendimento:' + '\x0A';
                printData += 'Whatsapp - (83) 9.9882-1242' + '\x0A';
                printData += '(83) 9.9882-1342' + '\x0A';
                //printData+=this.formatPhone(this.pedido.distribuidor.dddTelefone, this.pedido.distribuidor.telefonePrincipal) + '\x0A';
                printData += '\x0A' + '\x0A'; 				// line break (2x)
                printData += '\x1B' + '\x61' + '\x30'; 		// left align
                printData += '\x1B' + '\x45' + '\x0D';		// bold on
                printData += '\x1D' + '\x21' + '\x11';		// double font size
                printData += 'Pedido ' + this.pedido.id + '\x0A';
                printData += '\x1B' + '\x45' + '\x0A';		// bold off
                printData += '\x1D' + '\x21' + '\x00';		// standard font size
                printData += '\x1D\x21\x00';                  //Altura
                printData += '\x1B\x33\x00';                  //Spacing
                //printData+=this.formatDate(String('Pedido '+this.pedido.id).length) + '\x1D' + '\x21' + '\x11'+' '+'\x0A';// double font size
                printData += '\x1B' + '\x61' + '\x32'; 	// right align
                printData += this.pedido.horarioPedido + '\x0A';
                printData += '\x1B' + '\x61' + '\x30'; 		// left align
                printData += '\x1D' + '\x21' + '\x00';		// standard font size
                printData += '\x1D\x21\x00';                  //Altura
                printData += '\x1B\x33\x00';                  //Spacing
                printData += '--------------------------------------' + '\x0A';
                if (this.pedido.agendado != '0') {
                    printData += 'Data Entrega: ' + this.formatAgendado() + '\x0A';
                    printData += '--------------------------------------' + '\x0A';
                }
                printData += '\x1B' + '\x45' + '\x0D';		// bold on
                printData += 'Cliente: ';
                printData += '\x1B' + '\x45' + '\x0A';		// bold off
                printData += this.formatCliente() + '\x0A';
                var end = '';
                var endereco = this.formatAddress();
                endereco.forEach(function (entry) {
                    end += entry;
                });
                printData += this.retiraAcento(end);
                printData += '\x0A' + '======================================' + '\x0A';
                printData += '\x1B' + '\x45' + '\x0D';		// bold on
                printData += '\x1B' + '\x61' + '\x31'; 		// center align
                printData += 'PRODUTOS' + '\x0A';
                printData += '\x1B' + '\x45' + '\x0A';		// bold off
                printData += '\x1B' + '\x61' + '\x30'; 		// left align
                printData += 'ITEM CODIGO NOME    QNT PRECO SUBTOTAL' + '\x0A';
                printData += '--------------------------------------' + '\x0A';
                for (var i = 0; i < this.itensPedido.length; i++) {
                    printData += '\x1B' + '\x61' + '\x30'; 	// left align
                    printData += this.produtoL1(i) + '\x0A';
                    printData += '\x1B' + '\x61' + '\x32'; 	// right align
                    printData += this.produtoL2(i) + '\x0A';
                }
                printData += '--------------------------------------' + '\x0A';
                var padding = Array(11).join(' ');
                printData += 'PRODUTOS  R$' + this.pad(padding, (this.pedido.totalProdutos.substring(2, this.pedido.totalProdutos.length)), true) + '\x0A';
                printData += 'TAXA DE ENTREGA  R$' + this.pad(padding, (this.pedido.taxaEntrega.substring(2, this.pedido.taxaEntrega.length)), true) + '\x0A';
                printData += '\x1B' + '\x45' + '\x0D';		// bold on
                //PREMIAÇÕES
                // if(premiacoes && this.cliente.tipoPessoa==1){
                //     printData+='DESCONTO PREMIACAO*  R$' + this.pad(padding,(this.pedido.descontoPremiacao.substring(2, this.pedido.descontoPremiacao.length)),true) + '\x0A';
                // }
                //******* */
                printData += 'TOTAL  R$' + this.pad(padding, (this.pedido.total.substring(2, this.pedido.total.length)), true) + '\x0A';
                printData += '\x1B' + '\x45' + '\x0A';		// bold off
                printData += '======================================' + '\x0A';
                printData += '\x1B' + '\x61' + '\x30'; 		// left align
                printData += 'Forma de Pagamento: ' + formaPagamento + '\x0A';
                if (parseFloat(this.pedido.troco.replace(/,/g, '.')) > 0) {
                    printData += 'Troco para: ' + this.pedido.trocoPara + ' => R$ ' + this.pedido.troco + '\x0A';
                }
                printData += '--------------------------------------' + '\x0A';
                if (this.pedido.obs != null && this.pedido.obs.length > 0) {
                    printData += 'Observacao: ' + this.retiraAcento(this.pedido.obs) + '\x0A';
                    printData += '--------------------------------------' + '\x0A';
                }
                printData += '\x0A';							// line break
                printData += '\x1B' + '\x61' + '\x31';		// center align
                printData += '* GRATOS PELA PREFERENCIA! *' + '\x0A' + '\x0A';
                printData += 'Acesse: tks.tokumsede.com.br' + '\x0A';
                printData += 'E descubra a importancia de tomar agua' + '\x0A' + 'alcalina!';

                //PREMIAÇÕES
                // if(premiacoes && this.cliente.tipoPessoa==1){
                //     printData+='\x0A' + '\x0A' + '**************************************' + '\x0A';
                //     printData+='**** PROMOCAO FIDELIDADE PREMIADA ****' + '\x0A';
                //     printData+='A cada 10 aguas, 1 gratis!' + '\x0A' + 'Exija o cupom para' + '\x0A' + 'acompanhar seus pedidos.' + '\x0A' + 'Apenas para pedidos feitos pela' + '\x0A' + 'central telefonica ou' + '\x0A' + 'pelo aplicativo ToKumSede.' + '\x0A';
                //     printData+='--------------------------------------' + '\x0A';
                //     printData+='Pontuacao acumulada: '+this.pedido.pontuacaoAcumulada+' agua'+(this.pedido.pontuacaoAcumulada>1?'s.':'.')+ '\x0A';
                //     if(this.pedido.pontuacaoAcumulada>=10){
                //         printData+='--------------------------------------' + '\x0A';
                //         printData+='************** PARABENS **************' + '\x0A';
                //         printData+=parseInt(this.pedido.pontuacaoAcumulada/10, 10)>1?'VOCE GANHOU DESCONTO DE '+parseInt(this.pedido.pontuacaoAcumulada/10, 10)+' AGUAS':'VOCE GANHOU DESCONTO DE 1 AGUA' + '\x0A';
                //     }
                //     printData+='**************************************' + '\x0A';
                // }
                //****************//

                printData += '\x0A' + '\x0A' + '\x0A' + '\x0A';
                printData += '\x1B' + '\x69'; //cut paper
                printData += '\x10' + '\x14' + '\x01' + '\x00' + '\x05'; // Generate Pulse to kick-out cash drawer**

                document.location = 'intent:#Intent;scheme=rawbt;component=ru.a402d.rawbtprinter.activity.PrintDownloadActivity;package=ru.a402d.rawbtprinter;end;';                //Abre download caso app não estiver instalado ou abre o app para configurar caso não estiver como serviço, mas deixa espaço entre logo e nome distribuição
                document.location = 'rawbt:data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAkQAAACTAgMAAAASKu53AAAACVBMVEUAAAAAAABQUFFJCPtGAAAAAXRSTlMAQObYZgAABMlJREFUeNrs2T0O2zAMBeDHItwzSPdhhu4cxPtfpRAlm0gCuEMBk0X7gJCJpw+JfiwHf55uMhubGUqEzMbsBnRFhXSAZb3QUSEDgK4GqvAlkQBoh2UgP+zFW5GfzSkk7fiUnwboEnUTkCA9T7AJVPd6VGBoK8x0irqZSRXRwFPIbNKeSI/QFPUlGgVEdIi4iqivEc11RGOJIGQDFUSkLlo2UIG5RrJEOwXWI0aIeFTYah/Ys95lo8DG1tBtRoDVWJCbEK3Zn7/VNphHp6zEVvs8RLsLGlITItq9johXH+mT7RR1W0kXNfQPESEzIRLbkezp/wA7BGVEDMRU83fIF7GPHmdVEJGESDBF6Yu2hgjoZiNd1MqJWFzkBVxBRP9FF2HTEFEFkb1MpqjMyGYB6bdI80Rtvt5Egtw1WwHwFFXZRUi8HKICez971VqiDjxPUT9FSMrD16N2ivLvsxu6jVMkyD+L+CEEjy0aoEPUkJAQ8RYZkH6CdJFMEVkMpMwH2kpLhC3KfzbyJfKmFUSKJUL3xkhJiMxOEfp2AX7h1oRIWCdiYOeHgkefBXcmRMdhRIFIH7MI7k2IGPj4jSjhP+QQXW0z17lfxLjO7aKbF6YQsQJkADB5rGBTrKK4MSHyHe1E2KA4mDTclxDRUg2gxx2J9lmSRHyK1lHbk3DrFqK+z0TA9kWSRBYi90V+eyj5V0SvU0RfIk0R/bwSIUNkJUXy14jufrQVIryL/PIrUzTeRQN+LVOkn6IoSSJ5Fym8ZIpQTvSrnbvJrSOEAQBsS/G+i8d9OIIr2fe/Sjv+wW+UTFupkmER9IIc3oJPA4EZBnLeNeLjRHKcSL9F/ziLzFNE626E6RCRzV9+sQ4S6RLBAaJpkKNE40jRDJAcISJjnSH6YSIsUWAqaxfBGq/pQTShLZUoWLUlkiPrFdXmLF+kgehGl2NWhhtEEiLyRgNUfcu6RR+Ajpi+0C9rJVvFI4TeRA/FOnct1urT6TretKBN/ABVyVCgNQ14Eukm0cMlwhI17x3H+WdR/7saun74WdTftV8+BoUiaYNTRMrNf/7sk9mMzszgDP15iSzqPl3HMOpQhimcFvHw7xo7Es6oPh3zKlwi7N/yjxNvtVuzUYgy4lYRUN0TVVNlOPrf1VCJRjXfEm3YYE8F0aRhiiLqvUP6CFHVzm8iWspmEZNKicJRor6nkRLBKIe8icY20RVRrWPfRNB3TLNEfoedzeQlJkpgv0hdJEskDvRu1bvLjlwkKSrHPhHdhuflQIsyNYowRHyOKGZauYvmTlFU+yTq3z/KQH8RNY9HOMEtM0XyRT/iRhFd2U0EdxF3H9L+uLJ6EWrJItolegG9bRUJUU1pXvbqFQ0VE1E0Ft9nWvudm0R1ZtVFnuQm6uvZzyKFz5FsEeFxIlgOrGinCL4SwUaRAIzPNhj9oheMFNFnW5Zxq4i0VtXLFiLsfxYhgKi8TmWYQ8wGQesVxWAdT9cSNvb1bOo+F2FVkbUUe/UQtok6gfv/90g1x3h9sTIrGXSKEiI0s/aybVljDwgy8rKt75A3LGijeKPZp2xRKBl0psHhIoeofaKtiEPdmmzZYV4BR0ZyQa1B811gb1IH3ccj+R1n2YRtaWgGYm33n+kXghxg1lyi92gAAAAASUVORK5CYII=';
                setTimeout(function () {//Logo não estava sendo impressa no app do Chrome
                    document.location = 'rawbt:base64,' + window.btoa(printData);
                }, 500);
            }
        } else {
            if (this.printer == 'epson') {
                this.caracteres = 48;
                //alert('IMPRESSORA EPSON 80MM - 48 CARACTERES POR LINHA');
                /* IMPRESSORA EPSON 80MM - 48 CARACTERES POR LINHA */

            } else if (this.printer == 'epson58') {
                this.caracteres = 42;
                //alert('IMPRESSORA EPSON 58MM - 42 CARACTERES POR LINHA');
                /* IMPRESSORA EPSON 58MM - 42 CARACTERES POR LINHA */
            } else {
                this.caracteres = 38;
                //alert('IMPRESSORA PADRÃO 38 CARACTERES POR LINHA');
            }
            var printData = new Array();
            if (this.pedido.status == 6) {
                printData.push('\x1B' + '\x40'); 				// init
                printData.push('\x1B' + '\x21' + '\x01');         // font B
                printData.push('\x1D\x21\x00');                  //Altura
                printData.push('\x1B\x33\x00');                  //Spacing
                printData.push('\x1B' + '\x61' + '\x31');		// center align
                printData.push('ROTEIRO DE DISTRIBUICAO DE PEDIDOS' + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0D');		// bold on
                printData.push('\x1D' + '\x21' + '\x11');		// double font size
                printData.push('Pedido ' + this.pedido.id + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0A');		// bold off
                printData.push('\x1D' + '\x21' + '\x00');		// standard font size
                printData.push('\x1D\x21\x00');                  //Altura
                printData.push('\x1B\x33\x00');                  //Spacing
                printData.push("Cadastrado por " + (this.pedido.administrador ? this.pedido.administrador : 'Aplicativo Cliente') + '\x0A');
                printData.push("Horario do pedido: " + this.pedido.horarioPedido + '\x0A');
                printData.push("Entregador " + this.retiraAcento(this.pedido.entregador.nome) + '\x0A');
                printData.push("Saiu para entrega: " + this.pedido.horarioAceito + '\x0A');
                printData.push('\x1B' + '\x61' + '\x30'); 		// left align
                printData.push('\x1D' + '\x21' + '\x00');		// standard font size
                printData.push('\x1D\x21\x00');                  //Altura
                printData.push('\x1B\x33\x00');                  //Spacing
                printData.push('======================================' + '\x0A');
                if (this.pedido.agendado != '0') {
                    printData.push('Data Entrega: ' + this.formatAgendado() + '\x0A');
                    printData.push('--------------------------------------' + '\x0A');
                }
                printData.push('\x1B' + '\x45' + '\x0D');		// bold on
                printData.push('Cliente: ');
                printData.push('\x1B' + '\x45' + '\x0A');		// bold off
                printData.push(this.formatCliente() + '\x0A');
                var end = '';
                var endereco = this.formatAddress();
                endereco.forEach(function (entry) {
                    end += entry;
                });
                printData.push(this.retiraAcento(end));
                printData.push('\x0A' + '--------------------------------------' + '\x0A');
                printData.push('\x1B' + '\x61' + '\x30'); 		// left align
                for (var i = 0; i < this.itensPedido.length; i++) {
                    printData.push('\x1B' + '\x61' + '\x30'); 	// left align
                    printData.push(this.itensPedido[i].qtd + "x " + this.retiraAcento(this.itensPedido[i].produto.nome) + '\x0A');
                }
                printData.push('======================================' + '\x0A');
                printData.push('\x1B' + '\x61' + '\x30'); 		// left align
                if (this.cliente.outrosContatos != null && this.cliente.outrosContatos.length > 0) {
                    printData.push('Outros Contatos: ' + this.retiraAcento(this.cliente.outrosContatos) + '\x0A');
                    printData.push('--------------------------------------' + '\x0A');
                }
                printData.push('TOTAL ' + this.pedido.total + ' - ' + formaPagamento + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0A');		// bold off
                printData.push('\x1B' + '\x61' + '\x30'); 		// left align
                if (parseFloat(this.pedido.troco.replace(/,/g, '.')) > 0) {
                    printData.push('Troco para: ' + this.pedido.trocoPara + ' => R$ ' + this.pedido.troco + '\x0A');
                }
                if (this.pedido.obs != null && this.pedido.obs != '') {
                    printData.push('Obs: ' + this.retiraAcento(this.pedido.obs) + '\x0A');
                }
                printData.push('======================================' + '\x0A');
                printData.push('\x1B' + '\x61' + '\x31');		// center align
                printData.push('ATENCAO: NAO VALIDO COMO CUPOM' + '\x0A');
                printData.push('\x0A' + '\x0A' + '\x0A' + '\x0A');
                printData.push('\x1B' + '\x69'); //cut paper
                printData.push('\x10' + '\x14' + '\x01' + '\x00' + '\x05'); // Generate Pulse to kick-out cash drawer**
            } else {
                printData.push({ type: 'raw', format: 'image', data: this.caracteres == 48 ? '/images/printer_logo2.png' : '/images/printer_logo.png', options: { language: "ESC/POS", dotDensity: 'double' } });
                printData.push('\x1B' + '\x40'); 				// init
                printData.push('\x1B' + '\x61' + '\x31'); 		// center align
                this.caracteres != 38 ? printData.push('\x1B' + '\x4D' + '\x00') : printData.push('\x1B' + '\x21' + '\x01');// font B
                printData.push('\x1D' + '\x21' + '\x00');                  //Altura
                this.caracteres != 38 ? printData.push('\x1B' + '\x33' + '\x28') : printData.push('\x1B' + '\x33' + '\x00');//Spacing
                //printData.push(this.retiraAcento(this.pedido.distribuidor.nome) + '\x0A';
                printData.push('tks.tokumsede.com.br' + '\x0A');
                printData.push('Central de atendimento:' + '\x0A');
                printData.push('Whatsapp - (83) 9.9882-1242' + '\x0A');
                printData.push('(83) 9.9882-1342' + '\x0A');
                //printData.push(this.formatPhone(this.pedido.distribuidor.dddTelefone, this.pedido.distribuidor.telefonePrincipal) + '\x0A';
                printData.push('\x0A' + '\x0A'); 				// line break (2x)
                printData.push('\x1B' + '\x61' + '\x30'); 		// left align
                printData.push('\x1B' + '\x45' + '\x0D');		// bold on
                printData.push('\x1D' + '\x21' + '\x11');		// double font size
                printData.push('Pedido ' + this.pedido.id + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0A');		// bold off
                printData.push('\x1D' + '\x21' + '\x00');		// standard font size
                printData.push('\x1D' + '\x21' + '\x00');                  //Altura
                this.caracteres != 38 ? printData.push('\x1B' + '\x33' + '\x28') : printData.push('\x1B' + '\x33' + '\x00');//Spacing
                printData.push('\x1B' + '\x61' + '\x32'); 	// right align
                printData.push(this.pedido.horarioPedido + '\x0A');
                printData.push('\x1B' + '\x61' + '\x30'); 		// left align
                printData.push('\x1D' + '\x21' + '\x00');		// standard font size
                printData.push('\x1D' + '\x21' + '\x00');                  //Altura
                this.caracteres != 38 ? printData.push('\x1B' + '\x33' + '\x28') : printData.push('\x1B' + '\x33' + '\x00');//Spacing
                printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
                if (this.pedido.agendado != '0') {
                    printData.push('Data Entrega: ' + this.formatAgendado() + '\x0A');
                    printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
                }
                printData.push('\x1B' + '\x45' + '\x0D');		// bold on
                printData.push('Cliente: ');
                printData.push('\x1B' + '\x45' + '\x0A');		// bold off
                printData.push(this.formatCliente() + '\x0A');
                var end = '';
                var endereco = this.formatAddress();
                endereco.forEach(function (entry) {
                    end += entry;
                });
                printData.push(this.retiraAcento(end));
                printData.push('\x0A' + Array(this.caracteres + 1).join('=') + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0D');		// bold on
                printData.push('\x1B' + '\x61' + '\x31'); 		// center align
                printData.push('PRODUTOS' + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0A');		// bold off
                printData.push('\x1B' + '\x61' + '\x30'); 		// left align
                printData.push(this.caracteres == 38 ? 'ITEM CODIGO NOME    QTD PRECO SUBTOTAL' : 'ITEM CODIGO NOME ' + Array(this.caracteres - 41).join(' ') + 'QUANTIDADE PRECO SUBTOTAL' + '\x0A');
                printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
                for (var i = 0; i < this.itensPedido.length; i++) {
                    printData.push('\x1B' + '\x61' + '\x30'); 	// left align
                    printData.push(this.produtoL1(i) + '\x0A');
                    printData.push('\x1B' + '\x61' + '\x32'); 	// right align
                    printData.push(this.produtoL2(i) + '\x0A');
                }
                printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
                var padding = Array(11).join(' ');
                printData.push('PRODUTOS  R$' + this.pad(padding, (this.pedido.totalProdutos.substring(2, this.pedido.totalProdutos.length)), true) + '\x0A');
                printData.push('TAXA DE ENTREGA  R$' + this.pad(padding, (this.pedido.taxaEntrega.substring(2, this.pedido.taxaEntrega.length)), true) + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0D');		// bold on
                printData.push('TOTAL  R$' + this.pad(padding, (this.pedido.total.substring(2, this.pedido.total.length)), true) + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0A');		// bold off
                printData.push(Array(this.caracteres + 1).join('=') + '\x0A');
                printData.push('\x1B' + '\x61' + '\x30'); 		// left align
                printData.push('Forma de Pagamento: ' + formaPagamento + '\x0A');
                if (parseFloat(this.pedido.troco.replace(/,/g, '.')) > 0) {
                    printData.push('Troco para: ' + this.pedido.trocoPara + ' => R$ ' + this.pedido.troco + '\x0A');
                }
                printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
                if (this.pedido.obs != null && this.pedido.obs != '') {
                    printData.push('Observacao: ' + this.retiraAcento(this.pedido.obs) + '\x0A');
                    printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
                }
                printData.push('\x0A');							// line break
                printData.push('\x1B' + '\x61' + '\x31');		// center align
                printData.push('* GRATOS PELA PREFERENCIA! *' + '\x0A' + '\x0A');
                printData.push('Acesse: tks.tokumsede.com.br' + '\x0A');
                printData.push(this.caracteres < 48 ? 'E descubra a importancia de tomar agua' + '\x0A' + 'alcalina!' : 'E descubra a importancia de tomar agua alcalina!');
                printData.push('\x0A' + '\x0A' + '\x0A' + '\x0A' + '\x0A' + '\x0A' + '\x0A');
                printData.push('\x1B' + '\x69'); //cut paper
                printData.push('\x10' + '\x14' + '\x01' + '\x00' + '\x05'); // Generate Pulse to kick-out cash drawer**
            }
            if (this.config != null) {
                if (qz.websocket.isActive()) {
                    qz.print(this.config, printData).catch(function (e) { alert("Erro na impressão."); });
                } else {
                    this.connect();
                    alert("A impressora foi desconectada. Tente novamente! Caso não consiga, atualize a página.");
                }
            } else {
                if (!qz.websocket.isActive()) {
                    alert("Não foi possível localizar a impressora. Conecte-a e atualize a página.");
                } else {
                    qz.printers.find().then(function (printers) {
                        for (let i = 0; i < this.impressoras.length; i++) {
                            for (let p = 0; p < printers.length; p++) {
                                if (this.impressoras[i] == printers[p]) {
                                    this.printer = printers[p];
                                }
                                if (this.printer) { break; }
                            }
                            if (this.printer) { break; }
                        }
                        this.config = qz.configs.create(this.printer);
                        qz.print(this.config, printData).catch(function (e) { alert("Erro na impressão."); });
                    }).catch(function (error) {
                        Toast.fire({
                            type: 'warning',
                            title: "Impressora não localizada",
                            text: error
                        });
                    });
                }
            }
        }
    },
    produtoL1: function (pos) {
        var padding1 = Array(5).join('0');
        var padding2 = Array(7).join('0');
        var produto = this.pad(padding1, (pos + 1), true);
        produto += ' ' + this.pad(padding2, this.itensPedido[pos].idProduto, true);
        produto += ' ' + this.retiraAcento(this.itensPedido[pos].produto.length > 36 ? this.itensPedido[pos].produto.substring(0, 36) : this.itensPedido[pos].produto.nome).toUpperCase();
        return produto;
    },
    produtoL2: function (pos) {
        var padding1 = Array(7).join(' ');
        var padding2 = Array(11).join(' ');
        var produto = this.itensPedido[pos].qtd + '  x  R$';
        produto += this.pad(padding1, (this.itensPedido[pos].preco.substring(2, this.itensPedido[pos].preco.length)), true);
        produto += '  =  R$' + this.pad(padding2, (this.itensPedido[pos].subtotal.substring(2, this.itensPedido[pos].subtotal.length)), true);
        return produto;
    },
    formatAddress: function () {
        var address = new Array();
        var aux = 'Endereco: ' + this.pedido.endereco.logradouro;
        if (aux.length > 37) {
            address.push(aux.substring(0, 38));
            aux = aux.substring(38, aux.length);
        }
        if (aux == '' || String(aux + ', n ' + this.pedido.endereco.numero).length > 37) {
            address.push(aux);
            aux = '\x0A' + 'n ' + this.pedido.endereco.numero;
        } else {
            aux += ', n ' + this.pedido.endereco.numero;
        }
        if (this.pedido.endereco.complemento != null && this.pedido.endereco.complemento.length > 0) {
            if (String(aux + ', ' + this.pedido.endereco.complemento).length > 37) {
                address.push(aux);
                aux = '\x0A' + this.pedido.endereco.complemento;
            } else {
                aux += ', ' + this.pedido.endereco.complemento;
            }
        }
        if (String(aux + ', ' + this.pedido.endereco.bairro).length > 37) {
            address.push(aux);
            aux = '\x0A' + this.pedido.endereco.bairro;
        } else {
            aux += ', ' + this.pedido.endereco.bairro;
        }
        if (String(aux + ', ' + this.pedido.endereco.cidade + ' - ' + this.pedido.endereco.estado).length > 37) {
            address.push(aux);
            address.push('\x0A' + this.pedido.endereco.cidade + ' - ' + this.pedido.endereco.estado);
        } else {
            address.push(aux + ', ' + this.pedido.endereco.cidade + ' - ' + this.pedido.endereco.estado);
        }
        if (this.pedido.endereco.referencia != null && this.pedido.endereco.referencia.length > 0) {
            if (String(aux + ', ' + this.pedido.endereco.referencia).length > 37) {
                address.push('\x0A' + this.pedido.endereco.referencia);
            } else {
                address.push('\x0A' + this.pedido.endereco.referencia);
                //address.push(', ' + this.pedido.endereco.referencia);
            }
        }
        return address;
    },
    formatCliente: function () {
        var fone = ' ' + this.formatPhone(this.cliente.dddTelefone, this.cliente.telefone);
        var nome = this.retiraAcento(this.cliente.nome);
        if (nome.length > (29 - fone.length)) {
            nome = nome.substring(0, 29) + '\x0A';
            return nome + '\x1B' + '\x45' + '\x0D' + 'Telefone:' + '\x1B' + '\x45' + '\x0A' + fone;
        }
        var padSize = 29 - nome.length;
        var padding = Array(padSize + 1).join(' ');
        return nome + this.pad(padding, fone, true);
    },
    formatDate: function (pSize) {
        var padSize = 38 - (pSize * 2);
        var padding = Array(padSize + 1).join(' ');
        return this.pad(padding, this.pedido.horarioPedido.substring(0, 17), true);
    },
    formatAgendado: function () {
        var dt = this.pedido.dataAgendada + ' (' + this.pedido.horaInicio + '-' + this.pedido.horaFim + ')';
        var padding = Array(25).join(' ');
        return this.pad(padding, dt, true);
    },
    pad: function (pad, str, padLeft) {
        if (padLeft) { 	//pad left
            return (pad + str).slice(-pad.length);
        } else {       //pad right
            return (str + pad).substring(0, pad.length);
        }
    },
    formatPhone: function (ddd, tel) {
        var result = '(' + ddd + ') ';
        if (ddd.length == 2) {
            if (tel.length == 8) {
                result += tel.substring(0, 4) + '-' + tel.substring(4, 8);
                return result;
            } else if (tel.length == 9) {
                result += tel.substring(0, 1) + '.' + tel.substring(1, 5) + '-' + tel.substring(5, 9);
                return result;
            } else {
                return '';
            }
        } else {
            return '';
        }
    },
    retiraAcento: function (palavra) {
        var com_acento = 'áàãâäéèêëíìîïóòõôöúùûüçÁÀÃÂÄÉÈÊËÍÌÎÏÓÒÕÖÔÚÙÛÜÇ´`^¨~';
        var sem_acento = 'aaaaaeeeeiiiiooooouuuucAAAAAEEEEIIIIOOOOOUUUUC     ';
        for (var l in palavra) {
            for (var l2 in com_acento) {
                if (palavra[l] == com_acento[l2]) {
                    palavra = palavra.replace(palavra[l], sem_acento[l2]);
                }
            }
        }
        //Remover emojis
        var ranges = [
            '\ud83c[\udf00-\udfff]', // U+1F300 to U+1F3FF
            '\ud83d[\udc00-\ude4f]', // U+1F400 to U+1F64F
            '\ud83d[\ude80-\udeff]'  // U+1F680 to U+1F6FF
        ];
        palavra = palavra.replace(new RegExp(ranges.join('|'), 'g'), '');
        return palavra;
    }
}
