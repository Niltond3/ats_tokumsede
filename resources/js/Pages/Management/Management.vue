<script setup>
import { onMounted } from 'vue'
import qz from 'qz-tray';
import { TableClientes } from './DataTableClientes'
import { TablePedidos } from './DataTablePedidos'
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card'
import {
    Tabs,
    TabsContent,
    TabsList,
    TabsTrigger,
} from '@/components/ui/tabs'
import { useToggleTabs } from './useTabs'
import { usePage, } from '@inertiajs/vue3';
import { useQzTray } from '@/composables/useQzTray'
import useIsMobile from '@/composables/useIsMobile'

// const isAuth = computed(() => page.props.auth.user)

const { connect } = useQzTray()
const { detectDevice, isMobile } = useIsMobile()
const page = usePage()

const typeAdmin = page.props.auth.user.tipoAdministrador

const getTypeAdmin = {
    Distribuidor: {
        tab: 'estatisticas',
        tabName: 'Estatísticas',
        description: 'Vistualize as estatísticas das vendas'
    },
    Administrador: {
        tab: 'clientes',
        tabName: 'Clientes',
        description: 'Cadastre um novo cliente, edite um já existente ou realizar um pedido em nome de um cliente cadastrado'
    }
}

const { tab, tabName, description } = getTypeAdmin[typeAdmin]

const { activeTab, setActiveTab } = useToggleTabs(tab)

const handleSetActiveTab = (tab) => setActiveTab(tab)

// Função para conectar ao QZ Tray
const connectQZTray = () => {
    const promise = connect()
    renderToast(promise, 'Conectando ao QZ Tray', 'Conectado ao QZ Tray', () => {
    }, 'QZ não encontrado! Inicie-o, atualize a página e tente novamente')
}

onMounted(() => {
    detectDevice()
    !isMobile.value && connectQZTray()
})

</script>

<template>
    <!-- MODAL REALIZAR PEDIDOS -->
    <div class="row">
        <!-- Column -->
        <Tabs default-value="account" :default-value="tab" :model-value="activeTab">
            <TabsList class="grid w-full grid-cols-2">
                <TabsTrigger :value="tab" @Click="handleSetActiveTab(tab)">
                    {{ tabName }}
                </TabsTrigger>
                <TabsTrigger value="pedidos" @Click="handleSetActiveTab('pedidos')" class="*:overflow-visible">
                    <span class="relative ">
                        <div
                            class="absolute hidden items-center justify-center w-6 h-6 text-xs font-bold text-white bg-danger border-2 border-white rounded-full -top-3 -right-6 -end-2 dark:border-gray-900 animate-pulse m-auto transition-all [transition-behavior:allow-discrete]">
                            !
                        </div>
                        Pedidos
                    </span>

                </TabsTrigger>
            </TabsList>
            <TabsContent :value="tab">
                <Card>
                    <CardHeader>
                        <CardTitle>
                            <span class="sr-only">Clientes</span>
                        </CardTitle>
                        <CardDescription>
                            {{ description }}
                        </CardDescription>
                    </CardHeader>
                    <CardContent class="">
                        <TableClientes :set-tab="setActiveTab" v-if="typeAdmin === 'Administrador'" />
                        <Dashboard v-else />
                    </CardContent>
                </Card>
            </TabsContent>
            <TabsContent value="pedidos">
                <Card>
                    <CardHeader>
                        <CardTitle>
                            <span class="sr-only">Pedidos</span>
                        </CardTitle>
                        <CardDescription>
                            Visualize e gerencie todos os pedios feitos hoje
                        </CardDescription>
                    </CardHeader>
                    <CardContent
                        class=" [&_.dtsp-titleRow]:h-0 [&_.dtsp-nameColumn]:border-none [&_.dtsp-nameColumn>div]:flex [&_.dtsp-nameColumn>div]:items-center ">
                        <TablePedidos :set-tab="setActiveTab" />
                    </CardContent>
                </Card>
            </TabsContent>
        </Tabs>
    </div>
</template>

<script>
import axios from 'axios'
import Dashboard from '../Dashboard.vue'
import renderToast from '@/components/renderPromiseToast';
import { toast } from 'vue-sonner';
import { errorUtils } from '@/util';
import useIsMobile from '@/composables/useIsMobile';
export default {
    data() {
        return {
            detalhesCliente: '',
            cliente: '',
            pedido: '',
            endereco: '',
            enderecoCliente: '',
            itensPedido: '',
            config: null,
            caracteres: 38,
            impressoras: ['epson', 'epson58', 'tks'],
            printer: false
        }
    },
    created() {
        if (this.$root.refresh == true) {
            window.location.href = '/#/';
        }
    },
    updated: function () {
        if (/mobile|android/i.test(navigator.userAgent)) {
            $('#detalhesPedido').css('font-size', '10px');
        }
        var este = this;
        $.fn.dataTable.isDataTable('#datatable-enderecosAtualizacao') ? '' : this.gerarDataTable(),
            $.fn.dataTable.isDataTable('#datatable-enderecos') ? '' : this.gerarDataTable(),
            $.fn.dataTable.isDataTable('#datatable-pedidos') ? '' : this.gerarDataTable(),
            setTimeout(function () {
                este.ajustarTabelas();
            }, 1500);
    },
    mounted: function () {
        var este = this;

    },

    methods: {
        setOpen: () => console.log('Open'),
        gerarDataTable: function () {
            this.$root.tableEnderecos = $('#datatable-enderecos').DataTable({
                "bLengthChange": false,
                "iDisplayLength": 5,
                "language": {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": '<span class="text-nowrap"><i class="fas fa-list-ol"></i>_MENU_<span class="hidden-lg-down">resultados por página</span><span class="hidden-xl-up text-nowrap">por pág.</span></span>',
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": "Nenhum registro encontrado",
                    "sSearch": '<span class="text-nowrap"><i class="fa fa-search"></i>_INPUT_</span>',
                    "sSearchPlaceholder": "Pesquisar...",
                    "oPaginate": {
                        "sNext": "<i class='fas fa-angle-double-right'></i>",
                        "sPrevious": "<i class='fas fa-angle-double-left'></i>",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    }
                }
            });
            this.$root.tablePedidos = $('#datatable-pedidos').DataTable({
                "order": [[0, "desc"]],
                "bLengthChange": false,
                "iDisplayLength": 5,
                'stateSave': true,
                "stateLoaded": function (settings, data) {
                    data.search.search = "12345";
                },
                "language": {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": '<span class="text-nowrap"><i class="fas fa-list-ol"></i>_MENU_<span class="hidden-lg-down">resultados por página</span><span class="hidden-xl-up text-nowrap">por pág.</span></span>',
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": "Nenhum registro encontrado",
                    "sSearch": '<span class="text-nowrap"><i class="fa fa-search"></i>_INPUT_</span>',
                    "sSearchPlaceholder": "Pesquisar...",
                    "oPaginate": {
                        "sNext": "<i class='fas fa-angle-double-right'></i>",
                        "sPrevious": "<i class='fas fa-angle-double-left'></i>",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    }
                }
            });
            this.$root.tableEnderecosAtualizacao = $('#datatable-enderecosAtualizacao').DataTable({
                "bLengthChange": false,
                "iDisplayLength": 5,
                "language": {
                    "sEmptyTable": "Nenhum registro encontrado",
                    "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                    "sInfoFiltered": "(Filtrados de _MAX_ registros)",
                    "sInfoPostFix": "",
                    "sInfoThousands": ".",
                    "sLengthMenu": '<span class="text-nowrap"><i class="fas fa-list-ol"></i>_MENU_<span class="hidden-lg-down">resultados por página</span><span class="hidden-xl-up text-nowrap">por pág.</span></span>',
                    "sLoadingRecords": "Carregando...",
                    "sProcessing": "Processando...",
                    "sZeroRecords": "Nenhum registro encontrado",
                    "sSearch": '<span class="text-nowrap"><i class="fa fa-search"></i>_INPUT_</span>',
                    "sSearchPlaceholder": "Pesquisar...",
                    "oPaginate": {
                        "sNext": "<i class='fas fa-angle-double-right'></i>",
                        "sPrevious": "<i class='fas fa-angle-double-left'></i>",
                        "sFirst": "Primeiro",
                        "sLast": "Último"
                    },
                    "oAria": {
                        "sSortAscending": ": Ordenar colunas de forma ascendente",
                        "sSortDescending": ": Ordenar colunas de forma descendente"
                    }
                }
            });
        },
        getCliente: function (id) {
            this.detalhesCliente = '';
            $("#modalVisualizacao").modal("show");
            //envia id para visualizar Cliente
            var url = "clientes/" + id;
            axios.get(url).then(response => {
                $("#datatable-enderecos").dataTable().fnDestroy();
                $("#datatable-pedidos").dataTable().fnDestroy();
                $("#datatable-enderecosAtualizacao").dataTable().fnDestroy();
                this.detalhesCliente = response.data
            }).catch(error => {
                this.errors = error.response.data;
                Swal.fire("Erro!", this.errors, "error");
            });
        },
        getClienteAtualizar: function (id) {
            $("#modalAtualizacao").modal("show");
            this.detalhesCliente = '';
            //envia id para visualizar Cliente
            var url = "clientes/" + id;
            axios.get(url).then(response => {
                $("#datatable-enderecos").dataTable().fnDestroy();
                $("#datatable-pedidos").dataTable().fnDestroy();
                $("#datatable-enderecosAtualizacao").dataTable().fnDestroy();
                this.detalhesCliente = response.data
            }).catch(error => {
                this.errors = error.response.data;
                Swal.fire("Erro!", this.errors, "error");
            });
        },
        ajustarTabelas: function () {
            this.$root.tablePedidos.draw().columns.adjust().responsive.recalc();
            this.$root.tableEnderecos.draw().columns.adjust().responsive.recalc();
            this.$root.tableEnderecosAtualizacao.draw().columns.adjust().responsive.recalc();
        },
        visualizar: function (id) {
            //envia id para visualizar pedido
            var url = "pedidos/visualizar/" + id;
            axios.get(url).then(response => {
                pedido = response.data,
                    // pedido.horarioPedido = moment(pedido.horarioPedido).format("DD/MM/YYYY, HH:mm"),
                    // pedido.dataAgendada = pedido.dataAgendada != null ? moment(pedido.dataAgendada).format("DD/MM/YYYY"):"",
                    pedido.horaFim = pedido.horaFim != null ? moment("01-01-19, " + pedido.horaFim).format("HH:mm") : "",
                    pedido.horaInicio = pedido.horaInicio != null ? moment("01-01-19, " + pedido.horaInicio).format("HH:mm") : "",
                    pedido.totalProdutos = pedido.total - pedido.taxaEntrega,
                    pedido.total = pedido.total.toFixed(2).split('.'), pedido.total[0] = "R$ " + pedido.total[0].split(/(?=(?:...)*$)/).join('.'), pedido.total = pedido.total.join(','),
                    pedido.trocoPara = pedido.trocoPara.toFixed(2).split('.'), pedido.trocoPara[0] = "R$ " + pedido.trocoPara[0].split(/(?=(?:...)*$)/).join('.'), pedido.trocoPara = pedido.trocoPara.join(','),
                    pedido.taxaEntrega = pedido.taxaEntrega.toFixed(2).split('.'), pedido.taxaEntrega[0] = "R$ " + pedido.taxaEntrega[0].split(/(?=(?:...)*$)/).join('.'), pedido.taxaEntrega = pedido.taxaEntrega.join(','),
                    pedido.totalProdutos = pedido.totalProdutos.toFixed(2).split('.'), pedido.totalProdutos[0] = "R$ " + pedido.totalProdutos[0].split(/(?=(?:...)*$)/).join('.'), pedido.totalProdutos = pedido.totalProdutos.join(','),
                    this.endereco = pedido.endereco,
                    this.cliente = pedido.clientePedido,
                    this.itensPedido = pedido.itensPedido,
                    this.itensPedido.forEach(item => {
                        item.subtotal = item.subtotal.toFixed(2).split('.'), item.subtotal[0] = "R$ " + item.subtotal[0].split(/(?=(?:...)*$)/).join('.'), item.subtotal = item.subtotal.join(',');
                        item.preco = item.preco.toFixed(2).split('.'), item.preco[0] = "R$ " + item.preco[0].split(/(?=(?:...)*$)/).join('.'), item.preco = item.preco.join(',');
                    }),
                    $("#modalVisualizacao").modal("hide"),
                    setTimeout(function () {
                        $("#modalVisualizarPedido").modal("show")
                    }, 500);
            }).catch(error => {
                this.errors = error.response.data;
                Toast.fire({
                    type: 'warning',
                    title: "Acesso Negado",
                    text: "Você não tem permissão para ver o pedido de outra distribuição ou ocorreu algum erro!"
                });
            });
        },
        alterarEnderecoCliente: function () {
            //envia dados para alteração do endereço
            var url = "enderecos/" + this.enderecoCliente.id;
            axios.put(url, {
                logradouro: $("#editformDistribuidor_Logradouro").val(), //this.enderecoCliente.logradouro,
                numero: $("#editformDistribuidor_Numero").val(), //this.enderecoCliente.numero,
                bairro: $("#editformDistribuidor_Bairro").val(), //this.enderecoCliente.bairro,
                complemento: $("#editformDistribuidor_Complemento").val(), //this.enderecoCliente.complemento,
                referencia: $("#editformDistribuidor_Referencia").val(), //this.enderecoCliente.referencia,
                cep: $("#editformDistribuidor_Cep").val(), //this.enderecoCliente.cep,
                cidade: $("#editformDistribuidor_Cidade").val(), //this.enderecoCliente.cidade,
                estado: $("#editformDistribuidor_Estado").val(), //this.enderecoCliente.estado,
                observacao: $("#editformDistribuidor_Observacao").val() //this.enderecoCliente.observacao
            }).then(response => {
                Toast.fire({
                    type: 'success',
                    title: response.data + " foi Alterado com Sucesso!",
                });
                this.getClienteAtualizar(this.enderecoCliente.idCliente);
            }).catch(error => {
                this.errors = error.response.data;
                Swal.fire("Erro!", this.errors, "error");
            });
        },
        excluirEndereco: function (id) {
            Swal.fire({
                title: "Confirma a exclusão deste endereço?",
                text: "Ao realizar a exclusão deste endereço, ele não estará mais disponível. Tem certeza que deseja excluir?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Sim, confirmar!",
                cancelButtonText: "Não, cancelar!"
            }).then((result) => {
                if (result.value) {
                    //envia dados para exclusão
                    var url = "enderecos/" + id;
                    axios.put(url, { status: 3 }).then(response => {
                        Toast.fire({
                            type: 'success',
                            title: response.data + " foi Excluído com Sucesso!",
                        })
                        this.$root.tableEnderecosAtualizacao.row($(this).parents('tr')).remove().draw();//remove linha excluída
                    }).catch(error => {
                        this.errors = error.response.data;
                        Swal.fire("Erro!", this.errors, "error");
                    });
                }
            })
        },
        connect: function () {
            qz.websocket.connect().then(function () {
                qz.printers.find().then(function (printers) {
                    for (let i = 0; i < este.impressoras.length; i++) {
                        for (let p = 0; p < printers.length; p++) {
                            if (este.impressoras[i] == printers[p]) {
                                este.printer = printers[p];
                            }
                            if (este.printer) { break; }
                        }
                        if (este.printer) { break; }
                    }
                    este.config = qz.configs.create(este.printer);
                }).catch(function (error) {
                    Toast.fire({
                        type: 'warning',
                        title: "Impressora não localizada",
                        text: error
                    });
                });
            });
        },
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
            if (pedido.status == 6) {
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
                printData.push('TOTAL ' + pedido.total + ' - ' + formaPagamento + '\x0A');
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
                printData.push('PRODUTOS  R$' + this.pad(padding, (pedido.totalProdutos.substring(2, pedido.totalProdutos.length)), true) + '\x0A');
                printData.push('TAXA DE ENTREGA  R$' + this.pad(padding, (pedido.taxaEntrega.substring(2, pedido.taxaEntrega.length)), true) + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0D');		// bold on
                printData.push('TOTAL  R$' + this.pad(padding, (pedido.total.substring(2, pedido.total.length)), true) + '\x0A');
                printData.push('\x1B' + '\x45' + '\x0A');		// bold off
                printData.push(Array(this.caracteres + 1).join('=') + '\x0A');
                printData.push('\x1B' + '\x61' + '\x30'); 		// left align
                printData.push('Forma de Pagamento: ' + formaPagamento + '\x0A');
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
                printData.push('Acesse: www.aguasterrasanta.com.br' + '\x0A');
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
                    var este = this;
                    qz.printers.find().then(function (printers) {
                        for (let i = 0; i < este.impressoras.length; i++) {
                            for (let p = 0; p < printers.length; p++) {
                                if (este.impressoras[i] == printers[p]) {
                                    este.printer = printers[p];
                                }
                                if (este.printer) { break; }
                            }
                            if (este.printer) { break; }
                        }
                        este.config = qz.configs.create(este.printer);
                        qz.print(este.config, printData).catch(function (e) { alert("Erro na impressão."); });
                    }).catch(function (error) {
                        Toast.fire({
                            type: 'warning',
                            title: "Impressora não localizada",
                            text: error
                        });
                    });
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
            var aux = 'Endereco: ' + pedido.endereco.logradouro;
            if (aux.length > 37) {
                address.push(aux.substring(0, 38));
                aux = aux.substring(38, aux.length);
            }
            if (aux == '' || String(aux + ', n ' + pedido.endereco.numero).length > 37) {
                address.push(aux);
                aux = '\x0A' + 'n ' + pedido.endereco.numero;
            } else {
                aux += ', n ' + pedido.endereco.numero;
            }
            if (pedido.endereco.complemento != null && pedido.endereco.complemento.length > 0) {
                if (String(aux + ', ' + pedido.endereco.complemento).length > 37) {
                    address.push(aux);
                    aux = '\x0A' + pedido.endereco.complemento;
                } else {
                    aux += ', ' + pedido.endereco.complemento;
                }
            }
            if (String(aux + ', ' + pedido.endereco.bairro).length > 37) {
                address.push(aux);
                aux = '\x0A' + pedido.endereco.bairro;
            } else {
                aux += ', ' + pedido.endereco.bairro;
            }
            if (String(aux + ', ' + pedido.endereco.cidade + ' - ' + pedido.endereco.estado).length > 37) {
                address.push(aux);
                address.push('\x0A' + pedido.endereco.cidade + ' - ' + pedido.endereco.estado);
            } else {
                address.push(aux + ', ' + pedido.endereco.cidade + ' - ' + pedido.endereco.estado);
            }
            if (pedido.endereco.referencia != null && pedido.endereco.referencia.length > 0) {
                if (String(aux + ', ' + pedido.endereco.referencia).length > 37) {
                    address.push('\x0A' + pedido.endereco.referencia);
                } else {
                    address.push('\x0A' + pedido.endereco.referencia);
                    //address.push(', ' + pedido.endereco.referencia);
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
            return this.pad(padding, pedido.horarioPedido.substring(0, 17), true);
        },
        formatAgendado: function () {
            var dt = pedido.dataAgendada + ' (' + pedido.horaInicio + '-' + pedido.horaFim + ')';
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
        },
    }
}
</script>
