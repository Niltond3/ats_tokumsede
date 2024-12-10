<script setup>
import { ref, onMounted, watch } from 'vue';
import {
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle
} from '@/components/ui/dialog'
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue, SelectLabel } from '@/components/ui/select'
import { Separator } from '@/components/ui/separator'
import { utf8Decode, formatMoney } from '@/util';
import { formatOrder, orderToClipboard } from '../utils';
import renderToast from '@/components/renderPromiseToast';
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import { useQzTray } from '@/composables/useQzTray'
import { connectPrinter, printData } from '@/services/MobilePrinterService';

const { checkConnection, selectedPrinter, connect, findPrinter, listPrinters, print } = useQzTray()

const props = defineProps({
    orderId: { type: Number, required: true },
    isOpen: { type: Boolean, required: false },
});

const isLoading = ref(true); // Estado de carregamento
const data = ref({})
const printerList = ref([]);

// Variável reativa para armazenar o tipo de dispositivo
const isMobile = ref(false);

// Função para detectar dispositivo
const detectDevice = () => {
    const userAgent = navigator.userAgent || navigator.vendor || window.opera;
    // Verifica dispositivos móveis comuns
    isMobile.value = /android|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(userAgent);
};

onMounted(() => detectDevice())

const { toCurrency } = formatMoney()

const fetchOrder = () => {
    var url = `pedidos/visualizar/${props.orderId}`
    const promise = axios.get(url)
    renderToast(promise, `carregando pedido ${props.orderId}`, 'Pedido carregado', (response) => {
        const formatedOrder = formatOrder(response.data)

        
        const itensPedido = response.data.itensPedido.map((order) => { return { ...order, preco: toCurrency(order.preco), subtotal: toCurrency(order.subtotal), produto: { ...order.produto, nome: utf8Decode(order.produto.nome) } } })

        data.value = { ...formatedOrder, itensPedido }

        isLoading.value = false
    })
}

watch(() => props.isOpen, async () => fetchOrder())

const handleCopyOrder = (order) => orderToClipboard(order)


const printOrder = {
    imprimirPedido: function (pedido, printer) {
        if (printer == 'epson') {
            this.caracteres = 48;
            //alert('IMPRESSORA EPSON 80MM - 48 CARACTERES POR LINHA');
            /* IMPRESSORA EPSON 80MM - 48 CARACTERES POR LINHA */

        } else if (printer == 'epson58') {
            this.caracteres = 42;
            //alert('IMPRESSORA EPSON 58MM - 42 CARACTERES POR LINHA');
            /* IMPRESSORA EPSON 58MM - 42 CARACTERES POR LINHA */
        } else {
            this.caracteres = 38;
            //alert('IMPRESSORA PADRÃO 38 CARACTERES POR LINHA');
        }
        var printData = new Array();
        if (pedido.status.statusId == 6) {
            printData.push('\x1B' + '\x40'); 				// init
            printData.push('\x1B' + '\x21' + '\x01');         // font B
            printData.push('\x1D\x21\x00');                  //Altura
            printData.push('\x1B\x33\x00');                  //Spacing
            printData.push('\x1B' + '\x61' + '\x31');		// center align
            printData.push('ROTEIRO DE DISTRIBUICAO DE PEDIDOS' + '\x0A');
            printData.push('\x1B' + '\x45' + '\x0D');		// bold on
            printData.push('\x1D' + '\x21' + '\x11');		// double font size
            printData.push('Pedido ' + pedido.id + '\x0A');
            printData.push('\x1B' + '\x45' + '\x0A');		// bold off
            printData.push('\x1D' + '\x21' + '\x00');		// standard font size
            printData.push('\x1D\x21\x00');                  //Altura
            printData.push('\x1B\x33\x00');                  //Spacing
            printData.push("Cadastrado por " + (pedido.administrador ? pedido.administrador : 'Aplicativo Cliente') + '\x0A');
            printData.push("Horario do pedido: " + pedido.horarioPedido + '\x0A');
            printData.push("Entregador " + this.retiraAcento(pedido.entregador.nome) + '\x0A');
            printData.push("Saiu para entrega: " + pedido.horarioAceito + '\x0A');
            printData.push('\x1B' + '\x61' + '\x30'); 		// left align
            printData.push('\x1D' + '\x21' + '\x00');		// standard font size
            printData.push('\x1D\x21\x00');                  //Altura
            printData.push('\x1B\x33\x00');                  //Spacing
            printData.push('======================================' + '\x0A');
            if (pedido.agendado != '0') {
                printData.push('Data Entrega: ' + this.formatAgendado(pedido) + '\x0A');
                printData.push('--------------------------------------' + '\x0A');
            }
            printData.push('\x1B' + '\x45' + '\x0D');		// bold on
            printData.push('Cliente: ');
            printData.push('\x1B' + '\x45' + '\x0A');		// bold off
            printData.push(this.formatCliente(pedido.cliente) + '\x0A');
            var end = '';
            var endereco = this.formatAddress(pedido.endereco);
            endereco.forEach(function (entry) {
                end += entry;
            });
            printData.push(this.retiraAcento(end));
            printData.push('\x0A' + '--------------------------------------' + '\x0A');
            printData.push('\x1B' + '\x61' + '\x30'); 		// left align
            for (var i = 0; i < pedido.itensPedido.length; i++) {
                printData.push('\x1B' + '\x61' + '\x30'); 	// left align
                printData.push(pedido.itensPedido[i].qtd + "x " + this.retiraAcento(pedido.itensPedido[i].produto.nome) + '\x0A');
            }
            printData.push('======================================' + '\x0A');
            printData.push('\x1B' + '\x61' + '\x30'); 		// left align
            if (pedido.cliente.outrosContatos != null && pedido.cliente.outrosContatos.length > 0) {
                printData.push('Outros Contatos: ' + this.retiraAcento(cliente.outrosContatos) + '\x0A');
                printData.push('--------------------------------------' + '\x0A');
            }
            printData.push('TOTAL ' + pedido.total + ' - ' + pedido.formaPagamento + '\x0A');
            printData.push('\x1B' + '\x45' + '\x0A');		// bold off
            printData.push('\x1B' + '\x61' + '\x30'); 		// left align
            if (parseFloat(pedido.troco.replace(/,/g, '.')) > 0) {
                printData.push('Troco para: ' + pedido.trocoPara + ' => R$ ' + pedido.troco + '\x0A');
            }
            if (pedido.obs != null && pedido.obs != '') {
                printData.push('Obs: ' + this.retiraAcento(pedido.obs) + '\x0A');
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
            //printData.push(this.retiraAcento(pedido.distribuidor.nome) + '\x0A';
            printData.push('www.tokumsede.com.br' + '\x0A');
            printData.push('Central de atendimento:' + '\x0A');
            printData.push('Whatsapp - (83) 9.9882-1242' + '\x0A');
            printData.push('(83) 9.9882-1342' + '\x0A');
            //printData.push(this.formatPhone(pedido.distribuidor.dddTelefone, pedido.distribuidor.telefonePrincipal) + '\x0A';
            printData.push('\x0A' + '\x0A'); 				// line break (2x)
            printData.push('\x1B' + '\x61' + '\x30'); 		// left align
            printData.push('\x1B' + '\x45' + '\x0D');		// bold on
            printData.push('\x1D' + '\x21' + '\x11');		// double font size
            printData.push('Pedido ' + pedido.id + '\x0A');
            printData.push('\x1B' + '\x45' + '\x0A');		// bold off
            printData.push('\x1D' + '\x21' + '\x00');		// standard font size
            printData.push('\x1D' + '\x21' + '\x00');                  //Altura
            this.caracteres != 38 ? printData.push('\x1B' + '\x33' + '\x28') : printData.push('\x1B' + '\x33' + '\x00');//Spacing
            printData.push('\x1B' + '\x61' + '\x32'); 	// right align
            printData.push(pedido.horarioPedido + '\x0A');
            printData.push('\x1B' + '\x61' + '\x30'); 		// left align
            printData.push('\x1D' + '\x21' + '\x00');		// standard font size
            printData.push('\x1D' + '\x21' + '\x00');                  //Altura
            this.caracteres != 38 ? printData.push('\x1B' + '\x33' + '\x28') : printData.push('\x1B' + '\x33' + '\x00');//Spacing
            printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
            if (pedido.agendado != '0') {
                printData.push('Data Entrega: ' + this.formatAgendado(pedido) + '\x0A');
                printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
            }
            printData.push('\x1B' + '\x45' + '\x0D');		// bold on
            printData.push('Cliente: ');
            printData.push('\x1B' + '\x45' + '\x0A');		// bold off
            printData.push(this.formatCliente(pedido.cliente) + '\x0A');
            var end = '';
            var endereco = this.formatAddress(pedido.endereco);
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
            for (var i = 0; i < pedido.itensPedido.length; i++) {
                printData.push('\x1B' + '\x61' + '\x30'); 	// left align
                printData.push(this.produtoL1(pedido, i) + '\x0A');
                printData.push('\x1B' + '\x61' + '\x32'); 	// right align
                printData.push(this.produtoL2(pedido, i) + '\x0A');
            }
            printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
            var padding = Array(11).join(' ');
            printData.push('\x1B' + '\x45' + '\x0D');		// bold on
            printData.push('TOTAL  R$' + this.pad(padding, (pedido.total.substring(2, pedido.total.length)), true) + '\x0A');
            printData.push('\x1B' + '\x45' + '\x0A');		// bold off
            printData.push(Array(this.caracteres + 1).join('=') + '\x0A');
            printData.push('\x1B' + '\x61' + '\x30'); 		// left align
            printData.push('Forma de Pagamento: ' + pedido.formaPagamento + '\x0A');
            if (parseFloat(pedido.troco.replace(/,/g, '.')) > 0) {
                printData.push('Troco para: ' + pedido.trocoPara + ' => R$ ' + pedido.troco + '\x0A');
            }
            printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
            if (pedido.obs != null && pedido.obs != '') {
                printData.push('Observacao: ' + this.retiraAcento(pedido.obs) + '\x0A');
                printData.push(Array(this.caracteres + 1).join('-') + '\x0A');
            }
            printData.push('\x0A');							// line break
            printData.push('\x1B' + '\x61' + '\x31');		// center align
            printData.push('* GRATOS PELA PREFERENCIA! *' + '\x0A' + '\x0A');
            printData.push('Acesse: www.tokumsede.com.br' + '\x0A');
            printData.push(this.caracteres < 48 ? 'E descubra a importancia de tomar agua' + '\x0A' + 'alcalina!' : 'E descubra a importancia de tomar agua alcalina!');
            printData.push('\x0A' + '\x0A' + '\x0A' + '\x0A' + '\x0A' + '\x0A' + '\x0A');
            printData.push('\x1B' + '\x69'); //cut paper
            printData.push('\x10' + '\x14' + '\x01' + '\x00' + '\x05'); // Generate Pulse to kick-out cash drawer**
        }
        return printData;

    },
    produtoL1: function (pedido, pos) {
        var padding1 = Array(5).join('0');
        var padding2 = Array(7).join('0');
        var produto = this.pad(padding1, (pos + 1), true);
        produto += ' ' + this.pad(padding2, pedido.itensPedido[pos].idProduto, true);
        produto += ' ' + this.retiraAcento(pedido.itensPedido[pos].produto.length > 36 ? pedido.itensPedido[pos].produto.substring(0, 36) : pedido.itensPedido[pos].produto.nome).toUpperCase();
        return produto;
    },
    produtoL2: function (pedido, pos) {
        var padding1 = Array(7).join(' ');
        var padding2 = Array(11).join(' ');
        var produto = pedido.itensPedido[pos].qtd + '  x  R$';
        produto += this.pad(padding1, (pedido.itensPedido[pos].preco.substring(2, pedido.itensPedido[pos].preco.length)), true);
        produto += '  =  R$' + this.pad(padding2, (pedido.itensPedido[pos].subtotal.substring(2, pedido.itensPedido[pos].subtotal.length)), true);
        return produto;
    },
    formatAddress: function (endereco) {
        var address = new Array();
        var aux = 'Endereco: ' + endereco.logradouro;
        if (aux.length > 37) {
            address.push(aux.substring(0, 38));
            aux = aux.substring(38, aux.length);
        }
        if (aux == '' || String(aux + ', n ' + endereco.numero).length > 37) {
            address.push(aux);
            aux = '\x0A' + 'n ' + endereco.numero;
        } else {
            aux += ', n ' + endereco.numero;
        }
        if (endereco.complemento != null && endereco.complemento.length > 0) {
            if (String(aux + ', ' + endereco.complemento).length > 37) {
                address.push(aux);
                aux = '\x0A' + endereco.complemento;
            } else {
                aux += ', ' + endereco.complemento;
            }
        }
        if (String(aux + ', ' + endereco.bairro).length > 37) {
            address.push(aux);
            aux = '\x0A' + endereco.bairro;
        } else {
            aux += ', ' + endereco.bairro;
        }
        if (String(aux + ', ' + endereco.cidade + ' - ' + endereco.estado).length > 37) {
            address.push(aux);
            address.push('\x0A' + endereco.cidade + ' - ' + endereco.estado);
        } else {
            address.push(aux + ', ' + endereco.cidade + ' - ' + endereco.estado);
        }
        if (endereco.referencia != null && endereco.referencia.length > 0) {
            if (String(aux + ', ' + endereco.referencia).length > 37) {
                address.push('\x0A' + endereco.referencia);
            } else {
                address.push('\x0A' + endereco.referencia);
                //address.push(', ' + endereco.referencia);
            }
        }
        return address;
    },
    formatCliente: function (cliente) {
        var fone = ' ' + cliente.telefone;
        var nome = this.retiraAcento(cliente.nome);
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
        return this.pad(padding, pedido.horarioPedido.substring(0, 17), true);
    },
    formatAgendado: function (pedido) {
        var dt = pedido.dataAgendada + ' (' + pedido.horaInicio + ')';
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

const handlePrint = async () => {
    try {
        const printerData = printOrder.imprimirPedido(data.value, selectedPrinter.value)
        await print(printerData)
    } catch (error) {
        console.log(error)
    }
}
const handlePrintOrder = () => {
    if (isMobile.value) {
        renderToast(connectPrinter(), 'conectando-se a impressora', 'conectado', () => {
            renderToast(printData(printOrder.imprimirPedido(data.value, 'tks')), 'imprimindo pedido', 'pedido impresso')
        }, '', (err) => {
            console.log(err)
        })
        return
    }
    renderToast(checkConnection(), 'Verificando conexão', 'Conectando', () => {
        renderToast(findPrinter(selectedPrinter.value), 'Procurando impressora padrão', 'impressora encontrada', () => {
            handlePrint()
        }, 'Impressora não encontrada', () => {
            const promise = listPrinters()
            renderToast(promise, 'Listando impressoras', 'Lista Obtida', (response) => {
                printerList.value = response
            })
        })
    })
}

</script>

<template>
    <DialogContent
        class="flex flex-col text-sm max-w-[22rem] sm:max-w-[30rem] md:max-w-[40rem] [&_div]:w-full [&_div]:flex [&_div]:flex-wrap gap-4 max-h-[560px] overflow-scroll scrollbar !scrollbar-w-1.5 !scrollbar-h-1.5 !scrollbar-thumb-slate-200 !scrollbar-track-tr!scrollbar-thumb-rounded scrollbar-track-rounded dark:scrollbar-track:!bg-slate-500/[0.16] dark:scrollbar-thumb:!bg-slate-500/50 lg:supports-scrollbars:pr-2 text-slate-500">
        <DialogHeader>
            <DialogTitle class=" font-medium text-info mr-4">
                <div class="leading-none flex gap-3 justify-between" v-if="isLoading">
                    <Skeleton class="w-24 h-5" />
                    <Skeleton class="w-48 h-5" />
                </div>
                <div class="leading-none flex gap-3 justify-between" v-else>
                    <div class="flex gap-2 ">
                        <button class="group" @click="handleCopyOrder(data)">
                            #{{ data.id }} <i
                                class="ri-file-copy-fill opacity-75 group-hover:opacity-100 transition-all" />
                        </button>
                        <button
                            class="relative min-w-[32px] min-h-[32px] w-[32px] h-[32px] text-2xl shadow-md rounded-full hover:text-info/100 text-info/60 transition-all group-hover/line:bg-white group-aria-selected/line:!bg-white hover:shadow-lg flex justify-center items-center"
                            @click="handlePrintOrder">
                            <i class="ri-printer-fill"></i>
                        </button>
                        <Select v-if="printerList.length > 0" :default-value="selectedPrinter" @update:modelValue="(value) => {
                            selectedPrinter = value
                        }">
                            <SelectTrigger>
                                <SelectValue placeholder="Selecione uma impressora" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectGroup>
                                    <SelectLabel>Impressoras</SelectLabel>
                                    <SelectItem v-for="printer in printerList" :value="printer">
                                        {{ printer }}
                                    </SelectItem>
                                </SelectGroup>
                            </SelectContent>
                        </Select>
                    </div>
                    <span class="text-sm">{{ data.distribuidor?.nome }}</span>
                </div>
            </DialogTitle>
            <DialogDescription>Detalhes do pedido</DialogDescription>
        </DialogHeader>
        <Separator label="cliente" />
        <div class="flex justify-between" v-if="isLoading">
            <span class="flex gap-1 w-1/3">
                <i class="ri-contacts-fill" />
                <Skeleton class="w-48 h-5" />
            </span>
            <span class="flex gap-1 w-1/3">
                <i class="ri-phone-fill" />
                <Skeleton class="w-48 h-5" />
            </span>
        </div>
        <div class="justify-between" v-else>
            <span><i class="ri-contacts-fill" /> {{ data.cliente?.nome }}</span>
            <span><i class="ri-phone-fill" /> {{ data.cliente?.telefone }}</span>
        </div>
        <Separator label="endereço de entrega" />
        <div class="flex-col" v-if="isLoading">
            <Skeleton class="w-96 h-5" v-for="(_, index) in 3" :id="`address-skeleton-${index}`" />
        </div>
        <div class="flex-col" v-else>
            <span>{{ data.endereco?.logradouro }}, {{ `nº ${data.endereco?.numero}` || 'SN' }} - {{
                data.endereco?.bairro }}</span>
            <span v-if="data.endereco?.complemento">Complemento: {{ data.endereco.complemento }}</span>
            <span v-if="data.endereco?.referencia">Referência: {{ data.endereco.referencia }}</span>
            <span class="text-xs opacity-60">{{ data.endereco?.cidade }} - {{ data.endereco?.estado
                }} <span v-if="data.endereco?.cep">, {{ data.endereco?.cep }}</span></span>

            <span v-if="data.endereco?.apelido"
                class="bg-info text-white w-min py-px px-2 rounded-full font-semibold">{{ data.endereco.apelido
                }}</span>
        </div>
        <Separator label="outros detalhes" />
        <div class="flex-col relative gap-2 content-start" v-if="isLoading">
            <div class=" flex gap-1 items-center" v-for="(_, index) in 3" :id="`detail-skeleton-${index}`">
                <Skeleton class="w-96 h-5" />
            </div>
        </div>
        <div class="flex-col relative gap-2 content-start" v-else>
            <span
                class="relative bg-info text-white w-min flex-nowrap py-1 px-2 rounded-full font-semibold pointer-events-none whitespace-nowrap">
                {{ data.origem }}
                <span
                    class="absolute left-1/2 -translate-x-1/2 z-10 -top-3 text-xs rounded-md bg-white text-info px-1">origem</span>
            </span>
            <div v-if="data.agendado" class=" flex gap-1 items-center">
                <span class="w-[5.9rem] flex text-xs opacity-70 justify-start">
                    Horário Agendamento
                </span>
                <i class="ri-calendar-schedule-fill" />
                {{ data.dataAgendada }}
                <i class="ri-timer-fill" />
                {{ data.horaInicio }}
            </div>

            <div class=" flex gap-1 items-center" v-for="detail in data.details">
                <span class="w-[5.9rem] flex text-xs opacity-70 justify-start">
                    {{ detail.label.long }}
                </span>
                <i :class="[detail.classIcon, detail.classColor]" />
                {{ detail.data }}
                <span v-if='detail.author !== ""' class="flex gap-1">
                    <span class="text-xs opacity-70 justify-start w-[5.9rem] md:w-auto">responsável</span>
                    <span class="text-sm text-slate-500 justify-start">{{ detail.author }}</span>
                </span>
                <span v-if="detail.reason"> <span class="text-xs opacity-70 justify-start">motivo</span> {{
                    detail.reason }}</span>
            </div>
        </div>
        <Separator label="produtos " />
        <div v-if="isLoading">
            <div v-for="(_, index) in 3" :id="`products-skeleton-${index}`">
                <Skeleton class="w-72 h-5" />
            </div>
        </div>
        <div v-else>
            <div v-for="order in data.itensPedido">
                <p>
                    {{ order.qtd }} {{ utf8Decode(order.produto.nome) }}
                    <span class="text-xs opacity-70 justify-start">un</span>
                    {{ toCurrency(order.preco) }}
                    <span class="text-xs opacity-70 justify-start">subtotal</span>
                    {{ toCurrency(order.subtotal) }}
                </p>
            </div>
        </div>
        <p class="flex" v-if="isLoading">
            <span class="text-xs opacity-70 justify-start">total</span>
            <Skeleton class="w-44 h-5" />
        </p>
        <p class="" v-else>
            <span class="text-xs opacity-70 justify-start">total</span>
            <span
                class="relative bg-success text-white w-min flex-nowrap py-1 px-2 rounded-lg font-semibold pointer-events-none mx-2">
                {{ data.total }}
                <span
                    class="absolute left-1/2 -translate-x-1/2 z-10 -top-3 text-xs rounded-md bg-white text-success px-1">{{
                        data.formaPagamento }}</span>
            </span>
            <span v-if="data.trocoPara != 'R$ 0,00'">
                <span class="text-xs opacity-70 justify-start">troco para {{ data.trocoPara }}
                    <i class="ri-arrow-left-right-line"></i>
                </span>
                {{ data.troco }}
            </span>
        </p>

    </DialogContent>
</template>
