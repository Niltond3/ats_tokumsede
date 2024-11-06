<script setup>
import { ref, onMounted, defineComponent, h, markRaw, defineProps, onBeforeMount } from 'vue';
import axios from 'axios'
import DataTable from 'datatables.net-vue3';
import DataTablesCore from 'datatables.net';
import 'datatables.net-autofill-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.colVis.mjs';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import 'datatables.net-responsive-dt';
import 'datatables.net-scroller-dt';
import 'datatables.net-searchbuilder-dt';
import 'datatables.net-searchpanes-dt';
import 'datatables.net-select-dt';
import 'datatables.net-staterestore-dt';
import DialogCreateOrder from './DialogCreateOrder.vue';
import { utf8Decode } from '../../../util';
import { dialogState } from '../useToggleDialog';
import languagePtBR from './dataTablePtBR.mjs';
import DropdownActionCliet from './DropdownActionCliet.vue';
import rowChildtable from './rowChildTable';
import Button from '@/components/Button.vue';

DataTable.use(DataTablesCore);

const props = defineProps({
    setTab: { type: Function, required: true },
})

const [isOpen, toggleDialog] = dialogState();
const idClienteAddress = ref('')
let dt;
const table = ref();


onMounted(() => {
    dt = table.value.dt;
    const format = (d) => {
        // `d` is the original data object for the row
        return (rowChildtable(d));
    }

    $('#datatable-clientes tbody').on('click', 'td.dt-control', function () {
        console.log('click')
        var tr = $(this).closest('tr');
        var row = dt.row(tr);

        if (row.child.isShown()) {
            return row.child.hide(); // This row is already open - close it
        }
        row.child(format(row.data())).show();// Open this row
    });
    $('#datatable-clientes').on("click", '.iniciarPedido', function () {
        idClienteAddress.value = this.id
        toggleDialog()
    })
});

const columns = [
    {
        className: 'dt-control',
        orderable: false,
        data: null,
        defaultContent: ''
    },
    { data: 'nome', title: 'Nome' },
    { data: 'tipoPessoa', title: 'CPF/CNPJ', searchable: false },
    { data: 'telefone', title: 'Telefone' },
    { data: 'outrosContatos', title: 'Outros Contatos' },
    { data: 'rating', title: 'Rating', searchable: false },
    { data: 'opcoes', title: 'Opções', render: '#actions', searchable: false },
    { data: 'enderecos[].logradouro', name: 'enderecos.logradouro', visible: false },
    { data: 'enderecos[].bairro', name: 'enderecos.bairro', visible: false },
    { data: 'enderecos[].numero', name: 'enderecos.numero', visible: false },
    { data: 'enderecos[].cidade', name: 'enderecos.cidade', visible: false },
    { data: 'enderecos[].estado', name: 'enderecos.estado', visible: false },
    { data: 'dddTelefone', visible: false }
]

const options = {
    language: languagePtBR,
    serverSide: true,
    processing: true,
}

const ajax = {
                url: 'clientes', dataFilter: function (data) {
                    const obj = JSON.parse(data)
                    const newData = obj.data.map((client) => {

                        const nome = utf8Decode(client.nome)
                        const enderecos = client.enderecos.map((address) => {
                            return {
                                ...address,
                                logradouro: utf8Decode(address.logradouro),
                                bairro: utf8Decode(address.bairro),
                                cidade: utf8Decode(address.cidade),
                                observacao: utf8Decode(address.observacao || ''),
                                referencia: utf8Decode(address.referencia || ''),
                                apelido: utf8Decode(address.apelido || ''),
                            }
                        })
                        const newClient = { ...client, nome, enderecos }

                        return newClient
                    })
                    const newObj = { ...obj, data: newData };

                    return JSON.stringify(newObj);
                },
                error: function (err) {
                    console.log(err)
                }
            }

</script>

<template>
    <div>
        <DialogCreateOrder :open="isOpen" :toggleDialog="toggleDialog" :idClienteAddress="idClienteAddress"
            :set-tab="props.setTab" />
        <Button class="rounded-md py-2 px-4 bg-info/70 hover:bg-info/100 transition-all text-base shadow-lg hover:shadow-sm"> <i class="ri-user-add-fill"></i>Novo Cliente </Button>
        <DataTable id="datatable-clientes" class="display [&_thead]:bg-info [&_thead]:text-[#F3F9FD]" :columns="columns"
            :ajax="ajax" :options="options" ref="table" language="language">
            <template #actions="data">
                <DropdownActionCliet :payloadData="data" :data-table="dt"/>
            </template>
        </DataTable>
    </div>
</template>


<script>


// export default {
//     mounted: () => {
//         function format(d) {
//             // `d` is the original data object for the row
//             console.log(d)
//             const containerClasses = ``;
//             const containerAddressClasses = "max-h-32 overflow-y-scroll overflow-x-hidden flex flex-col px-3 py-2bg-slate-100 ";
//             const liClasses = 'p-2 rounded hover:bg-slate-200 transition-all text-base flex gap-2 items-center group';
//             const btClasses = 'w-6 h-6 text-2xl';
//             return (
//                 /*html*/`
//                 <div class="${containerClasses}">
//                     <dl>
//                         <dt>Tipo de Pessoa: ${d.tipoPessoa} </dt>
//                         <dt>Data de Nascimento: ${d.dataNascimento}</dt>
//                         <ul class="${containerAddressClasses}">
//                             ${d.enderecos.map(endereco => {
//                 console.log(endereco)
//                 return `
//                                 <li class="${liClasses}">
//                                     <span class="w-3/5 flex flex-col">
//                                         ${utf8Decode(endereco.logradouro)}, nº ${endereco.numero} - ${utf8Decode(endereco.bairro)}
//                                         <span class="text-xs text-gray-400">${utf8Decode(endereco.cidade)} - ${endereco.estado}</span>
//                                         ${(
//                         () => {
//                             if (endereco.complemento || endereco.referencia) return `
//                                                 <div class='overflow-hidden flex flex-col gap-2 mt-2 transition-max-height max-h-0 group-hover:max-h-40 ease-in-out delay-150'>
//                                                     ${(
//                                     () => {
//                                         if (endereco.complemento) return `
//                                                                 <span class="font-bold text-sm text-gray-500 border-t border-slate-300">
//                                                                     Complemento <span class="font-medium">${endereco.complemento}</span>
//                                                                 </span>
//                                                                 `
//                                         return ''
//                                     }
//                                 )()
//                                 }
//                                                     ${(
//                                     () => {
//                                         if (endereco.referencia) return `
//                                                                                                                                                                                         <span class="font-bold text-sm text-gray-500 border-t border-slate-300">
//                                                                     Referencia <span class="font-medium">${endereco.referencia}</span>
//                                                                 </span>
//                                                                 `
//                                         return ''
//                                     }
//                                 )()
//                                 }
//                                                 </div>
//                                                 `
//                             return ''
//                         }
//                     )()}
//                                     </span>
//                                      <div class="gap-3 flex">
//                                         <button class="${btClasses} editarEndereco" id="${endereco.id}"><i class="ri-edit-line"></i></button>
//                                         <button class="${btClasses} excluirEndereco text-danger" id="${endereco.id}"><i class="ri-delete-bin-6-line"></i></button>
//                                         <button class="${btClasses} iniciarPedido hover:text-cyan-600 text-cyan-800 transition-colors" id="${endereco.id}"><i class="ri-shopping-cart-line pointer-events-none"></i></button>
//                                     </div>
//                                 </li>
//                                 `
//             }).join('')}
//                         </ul>
//                     </dl>
//                 </div>
//                 `
//             );
//         }

//         var table = $('#datatable-clientes').DataTable({
//             serverSide: true,
//             processing: true,
//             ajax: {
//                 url: 'clientes', dataFilter: function (data) {
//                     const obj = JSON.parse(data)
//                     const newData = obj.data.map(function (item) {

//                         return { ...item, nome: utf8Decode(item.nome) }

//                     })
//                     const newObj = { ...obj, data: newData };
//                     return JSON.stringify(newObj);
//                 },
//                 error: function () {
//                     Swal.fire({
//                         title: "Aviso!",
//                         text: 'Erro de conexão com a internet!',
//                         type: 'warning',
//                         confirmButtonText: 'Fazer Login!'
//                     }).then((result) => {
//                         window.location.href = '/login';
//                     })
//                 }
//             },

//             columns: [
//                 {
//                     className: 'dt-control',
//                     orderable: false,
//                     data: null,
//                     defaultContent: ''
//                 },
//                 { data: 'nome' },
//                 { data: 'tipoPessoa', searchable: false },
//                 { data: 'telefone' },
//                 { data: 'outrosContatos' },
//                 { data: 'rating', searchable: false },
//                 { data: 'opcoes', searchable: false },
//                 { data: 'enderecos[].logradouro', name: 'enderecos.logradouro', visible: false },
//                 { data: 'enderecos[].bairro', name: 'enderecos.bairro', visible: false },
//                 { data: 'enderecos[].numero', name: 'enderecos.numero', visible: false },
//                 { data: 'enderecos[].estado', name: 'enderecos.estado', visible: false },
//                 { data: 'enderecos[].cidade', name: 'enderecos.cidade', visible: false },
//                 { data: 'dddTelefone', visible: false }
//             ],
//             "language": {
//                 "sEmptyTable": "Nenhum registro encontrado",
//                 "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
//                 "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
//                 "sInfoFiltered": "(Filtrados de _MAX_ registros)",
//                 "sInfoPostFix": "",
//                 "sInfoThousands": ".",
//                 "sLengthMenu": '<span class="text-nowrap"><i class="fas fa-list-ol"></i>_MENU_<span class="hidden-lg-down">resultados por página</span><span class="hidden-xl-up text-nowrap">por pág.</span></span>',
//                 "sLoadingRecords": "Carregando...",
//                 "sProcessing": "Processando...",
//                 "sZeroRecords": "Nenhum registro encontrado",
//                 "sSearch": '<span class="text-nowrap"><i class="fa fa-search"></i>_INPUT_</span>',
//                 "sSearchPlaceholder": "Pesquisar...",
//                 "oPaginate": {
//                     "sNext": "<i class='fas fa-angle-double-right'></i>",
//                     "sPrevious": "<i class='fas fa-angle-double-left'></i>",
//                     "sFirst": "Primeiro",
//                     "sLast": "Último"
//                 },
//                 "oAria": {
//                     "sSortAscending": ": Ordenar colunas de forma ascendente",
//                     "sSortDescending": ": Ordenar colunas de forma descendente"
//                 }
//             }
//         });
//         $('#datatable-clientes tbody').on('click', 'td.dt-control', function () {
//             var tr = $(this).closest('tr');
//             var row = table.row(tr);

//             if (row.child.isShown()) {
//                 return row.child.hide(); // This row is already open - close it
//             }
//             row.child(format(row.data())).show();// Open this row
//         });
//         $('#datatable-clientes').on('click', '.editarEndereco', function () {
//             console.log('this.id');
//             console.log(this.id);
//             var url = "enderecos/" + this.id;
//             // axios.get(url).then(response => {
//             //     este.enderecoCliente = response.data[2],
//             //         $("#modalAtualizacao").modal("hide"),
//             //         setTimeout(function () {
//             //             $(".bs-editarEndereco-modal-lg").modal("show")
//             //         }, 500);
//             // }).catch(error => {
//             //     this.errors = error.response.data;
//             //     Swal.fire("Erro!", this.errors, "error");
//             // });
//         });
//         //EXCLUIR ENDERECO
//         $('#datatable-clientes').on("click", ".excluirEndereco", function () {
//             Swal.fire({
//                 title: "Confirma a exclusão deste endereço?",
//                 text: "Ao realizar a exclusão deste endereço, ele não estará mais disponível. Tem certeza que deseja excluir?",
//                 type: "warning",
//                 showCancelButton: true,
//                 confirmButtonColor: "#DD6B55",
//                 confirmButtonText: "Sim, confirmar!",
//                 cancelButtonText: "Não, cancelar!"
//             }).then((result) => {
//                 if (result.value) {
//                     //envia dados para exclusão
//                     var url = "enderecos/" + this.id;
//                     axios.put(url, { status: 3 }).then(response => {
//                         Toast.fire({
//                             type: 'success',
//                             title: response.data + " foi Excluído com Sucesso!",
//                         })
//                         este.$root.tableEnderecosAtualizacao.row($(this).parents('tr')).remove().draw(false);//remove linha excluída
//                     }).catch(error => {
//                         this.errors = error.response.data;
//                         Swal.fire("Erro!", this.errors, "error");
//                     });
//                 }
//             })
//         });
//         $('#datatable-clientes').on("click", '.iniciarPedido', function () {
//             idClienteAddress.value = this.id
//             toggleDialog()
//             console.log('iniciarPedidos');
//         })
//     }
// }
</script>


<style>
@import 'datatables.net-dt';
</style>
