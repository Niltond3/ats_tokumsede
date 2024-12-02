<script setup>
import { TableClientes } from './DataTableClientes'
import { TablePedidos } from './DataTablePedidos'
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
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
import { Link, usePage, } from '@inertiajs/vue3';

const page = usePage()
// const isAuth = computed(() => page.props.auth.user)

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
        //$('#datatable-enderecosAtualizacao').on('click', 'tbody td', function (e) {
        //    editor.inline(this, {
        //        submit: 'allIfChanged'
        //    });
        //});
        ////ENVIAR ATUALIZÇÃO DE CLIENTE
        //$('#atualizarCliente').on('click', function () {
        //    //envia dados do formulário
        //    var url = "clientes/" + this.value;
        //    axios.put(url, {
        //        nome: $("#formClienteAtualizar_Nome").val(),
        //        sexo: $("#formClienteAtualizar_Sexo").val(),
        //        dataNascimento: $("#formClienteAtualizar_DataNascimento").val(),
        //        tipoPessoa: $("#formClienteAtualizar_Tipo").val(),
        //        cnpj: $("#formClienteAtualizar_Cnpj").val(),
        //        cpf: $("#formClienteAtualizar_Cpf").val(),
        //        precoAcertado: $('#formClienteAtualizar_PrecoAcertado').val(),
        //        telefone: $("#formClienteAtualizar_Telefone").val(),
        //        outrosContatos: $("#formClienteAtualizar_Outros").val(),
        //        email: $("#formClienteAtualizar_Email").val()
        //    }).then(response => {
        //        if (response.data === 'Email já cadastrado.') {
        //            Swal.fire("Atenção!", "Email já cadastrado.", "info");
        //        } else if (response.data === 'Erro ao cadastrar o cliente. Tente novamente ou contate o cliente.') {
        //            Swal.fire("Atenção!", response.data, "error");
        //        } else {
        //            Toast.fire({
        //                type: 'success',
        //                title: response.data + " foi Atualizado com Sucesso!",
        //            })
        //            table.ajax.reload(null, false); // a paginação do usuário não é redefinida ao recarregar
        //        }
        //    }).catch(error => {
        //        this.errors = error.response.data;
        //        Swal.fire("Erro!", this.errors, "error");
        //    });
        //});
        ////VISUALIZAR
        //$('#datatable-clientes').on('click', '.visualizar', function () {
        //    $("#datatable-enderecos").dataTable().fnDestroy();
        //    $("#datatable-pedidos").dataTable().fnDestroy();
        //    $("#datatable-enderecosAtualizacao").dataTable().fnDestroy();
        //    este.getCliente(this.id);
        //});
        ////VISUALIZAR
        //$('#datatable-clientes').on('click', '.enviarMsg', function () {
        //    try {
        //        // const msg = "https://api.whatsapp.com/send?phone=55"+this.id+"&text=Olá Cliente ";
        //        const msg = "https://api.whatsapp.com/send?phone=55" + telefone + "&text=%E2%9A%A0%EF%B8%8F%20*COMUNICADO%20AOS%20CLIENTES*%20%E2%9A%A0%EF%B8%8F%0A%20Seu%20servi%C3%A7o%20est%C3%A1%20perto%20de%20se%20vencer!%0A%20%0A%20Vencimento:%20*" + vencimento + "*%0A%20Valor:%20*" + valor + "*%0A%20%0A%20%E2%9A%A0%EF%B8%8F%20*ATEN%C3%87%C3%83O*%20%E2%9A%A0%EF%B8%8F%0A%20*N%C3%83O*%20aceitamos%20deposito%20por%20envelope!%0A%20*N%C3%83O*%20identifique%20a%20transfer%C3%AAncia!"
        //        window.open(msg);
        //    } catch (err) {
        //        Toast.fire({
        //            type: 'warning',
        //            title: "Erro ao tentar enviar menssagem!",
        //            text: "Entre em contato com o suporte."
        //        });
        //    }
        //});
        ////ABRIR ATUALIZAR
        //$('#datatable-clientes').on('click', '.atualizar', function () {
        //    $("#datatable-enderecos").dataTable().fnDestroy();
        //    $("#datatable-pedidos").dataTable().fnDestroy();
        //    $("#datatable-enderecosAtualizacao").dataTable().fnDestroy();
        //    este.getClienteAtualizar(this.id);
        //});
        ////ATIVAR
        //$('#datatable-clientes').on('click', '.ativar', function () {
        //    //envia dados para ativação
        //    var url = "clientes/" + this.id;
        //    axios.put(url, { status: 1 }).then(response => {
        //        Toast.fire({
        //            type: 'success',
        //            title: response.data + " foi Ativado com Sucesso!",
        //        })
        //        table.ajax.reload(null, false); // a paginação do usuário não é redefinida ao recarregar
        //    }).catch(error => {
        //        this.errors = error.response.data;
        //        Swal.fire("Erro!", this.errors, "error");
        //    });
        //});
        ////INATIVAR
        //$('#datatable-clientes').on('click', '.inativar', function () {
        //    Swal.fire({
        //        title: "Confirma a inativação deste cliente?",
        //        text: "Ao realizar a inativação deste cliente, ele não estará mais disponível. Tem certeza que deseja inativar?",
        //        type: "warning",
        //        showCancelButton: true,
        //        confirmButtonColor: "#DD6B55",
        //        confirmButtonText: "Sim, confirmar!",
        //        cancelButtonText: "Não, cancelar!"
        //    }).then((result) => {
        //        if (result.value) {
        //            //envia dados para inativação
        //            var url = "clientes/" + this.id;
        //            axios.put(url, { status: 2 }).then(response => {
        //                Toast.fire({
        //                    type: 'success',
        //                    title: response.data + " foi Inativado com Sucesso!",
        //                })
        //                table.ajax.reload(null, false); // a paginação do usuário não é redefinida ao recarregar
        //            }).catch(error => {
        //                this.errors = error.response.data;
        //                Swal.fire("Erro!", this.errors, "error");
        //            });
        //        }
        //    })
        //});
        ////EXCLUIR
        //$('#datatable-clientes').on('click', '.excluir', function () {
        //    Swal.fire({
        //        title: "Confirma a exclusão deste cliente?",
        //        text: "Ao realizar a exclusão deste cliente, ele não estará mais disponível. Tem certeza que deseja excluir?",
        //        type: "warning",
        //        showCancelButton: true,
        //        confirmButtonColor: "#DD6B55",
        //        confirmButtonText: "Sim, confirmar!",
        //        cancelButtonText: "Não, cancelar!"
        //    }).then((result) => {
        //        if (result.value) {
        //            //envia dados para exclusão
        //            var url = "clientes/" + this.id;
        //            axios.put(url, { status: 3 }).then(response => {
        //                Toast.fire({
        //                    type: 'success',
        //                    title: response.data + " foi Excluído com Sucesso!",
        //                })
        //                table.ajax.reload(null, false); // a paginação do usuário não é redefinida ao recarregar
        //            }).catch(error => {
        //                this.errors = error.response.data;
        //                Swal.fire("Erro!", this.errors, "error");
        //            });
        //        }
        //    })
        //});
        ////EXCLUIR ENDERECO
        //$('#enderecoAtualizacao').on("click", ".excluir", function () {
        //    Swal.fire({
        //        title: "Confirma a exclusão deste endereço?",
        //        text: "Ao realizar a exclusão deste endereço, ele não estará mais disponível. Tem certeza que deseja excluir?",
        //        type: "warning",
        //        showCancelButton: true,
        //        confirmButtonColor: "#DD6B55",
        //        confirmButtonText: "Sim, confirmar!",
        //        cancelButtonText: "Não, cancelar!"
        //    }).then((result) => {
        //        if (result.value) {
        //            //envia dados para exclusão
        //            var url = "enderecos/" + this.id;
        //            axios.put(url, { status: 3 }).then(response => {
        //                Toast.fire({
        //                    type: 'success',
        //                    title: response.data + " foi Excluído com Sucesso!",
        //                })
        //                este.$root.tableEnderecosAtualizacao.row($(this).parents('tr')).remove().draw(false);//remove linha excluída
        //            }).catch(error => {
        //                this.errors = error.response.data;
        //                Swal.fire("Erro!", this.errors, "error");
        //            });
        //        }
        //    })
        //});
        ////EDITAR ENDERECO
        //$('#enderecoAtualizacao').on("click", ".editarEndereco", function () {
        //    console.log(this.id);
        //    var url = "enderecos/" + this.id;
        //    axios.get(url).then(response => {
        //        este.enderecoCliente = response.data[2],
        //            $("#modalAtualizacao").modal("hide"),
        //            setTimeout(function () {
        //                $(".bs-editarEndereco-modal-lg").modal("show")
        //            }, 500);
        //    }).catch(error => {
        //        this.errors = error.response.data;
        //        Swal.fire("Erro!", this.errors, "error");
        //    });
        //});
        //$('#tab-enderecosAtualizacao').on('click', function () {
        //    setTimeout(function () {
        //        este.ajustarTabelas();
        //    }, 1);
        //});
        //$('#tab-enderecos').on('click', function () {
        //    setTimeout(function () {
        //        este.ajustarTabelas();
        //    }, 1);
        //});
        //$('#tab-pedidos').on('click', function () {
        //    setTimeout(function () {
        //        este.ajustarTabelas();
        //    }, 1);
        //});
        //$('#closeModalPedido').on('click', function () {
        //    $("#modalVisualizarPedido").modal("hide"),
        //        setTimeout(function () {
        //            $("#modalVisualizacao").modal("show"),
        //                setTimeout(function () {
        //                    este.ajustarTabelas();
        //                }, 500);
        //        }, 500);
        //});
        //$('#closeModalEndereco').on('click', function () {
        //    $(".bs-editarEndereco-modal-lg").modal("hide"),
        //        setTimeout(function () {
        //            $("#modalAtualizacao").modal("show"),
        //                setTimeout(function () {
        //                    este.ajustarTabelas();
        //                }, 500);
        //        }, 500);
        //});
        //$("#linkbreadcrumb li").remove();
        //$("#linkicon").html("<i class='fas fa-clipboard-list'></i>");
        //$("#linklocal").text("LISTA DE CLIENTES");
        //$("#linkbreadcrumb").append('<li class="breadcrumb-item"><a href="/#/">Home</a></li>')
        //$("#linkbreadcrumb").append('<li class="breadcrumb-item">Clientes</li>')
        //$("#linkbreadcrumb").append('<li class="breadcrumb-item"><a href="/#/listapedidos">Listar</a></li>');
        //
        //if (!/mobile|android/i.test(navigator.userAgent)) {
        //    var este = this;
        //    /// Authentication setup ///
        //    qz.security.setCertificatePromise(function (resolve, reject) {
        //        //Alternate method 2 - direct
        //        resolve("-----BEGIN CERTIFICATE-----\n" +
        //            "MIIEBjCCAu6gAwIBAgIJAOJrQsHMj2TaMA0GCSqGSIb3DQEBCwUAMIGXMQswCQYD\n" +
        //            "VQQGEwJCUjEQMA4GA1UECAwHUGFyYWliYTEPMA0GA1UEBwwGSmVyaWNvMRIwEAYD\n" +
        //            "VQQKDAlUb2t1bXNlZGUxCzAJBgNVBAsMAlRJMRswGQYDVQQDDBIqLnRva3Vtc2Vk\n" +
        //            "ZS5jb20uYnIxJzAlBgkqhkiG9w0BCQEWGHR1bGlvZ2FsdmFvLnBiQGdtYWlsLmNv\n" +
        //            "bTAeFw0xNzA5MjQxNzM3NTZaFw00OTAzMTkxNzM3NTZaMIGXMQswCQYDVQQGEwJC\n" +
        //            "UjEQMA4GA1UECAwHUGFyYWliYTEPMA0GA1UEBwwGSmVyaWNvMRIwEAYDVQQKDAlU\n" +
        //            "b2t1bXNlZGUxCzAJBgNVBAsMAlRJMRswGQYDVQQDDBIqLnRva3Vtc2VkZS5jb20u\n" +
        //            "YnIxJzAlBgkqhkiG9w0BCQEWGHR1bGlvZ2FsdmFvLnBiQGdtYWlsLmNvbTCCASIw\n" +
        //            "DQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBALsZvyHqUsupDlATczH99sPo5dak\n" +
        //            "s1CwdqrAbBRG24HiTuk51ianCvA4RYacApJsoVQKQQhqEitMCcJ1LMW/sFfCT2aG\n" +
        //            "bc6FXo8Kpgg383jnmrxcDZGou4qDOOS1Kksbz8CJYlD/UVucayfXRqYRi7DHSDQC\n" +
        //            "ix5bfiumZFxqPGd3i6N09/nm37Zb9zKpTJFpsY6fw1WPKZFZ6x4UOnZ7NDcNHse/\n" +
        //            "7qQl4M84KM5RV8c9Bq17C8N+OMMfIRJNpAzUo+KOv/9kvXQl7YffUANB5Br4QWEi\n" +
        //            "bET+Etac8OeT6Qy31K9gERKTE6olS3HzuLUIY2Jq4Tyja83v0WvU/M8x0zUCAwEA\n" +
        //            "AaNTMFEwHQYDVR0OBBYEFNQ1pt/TYpH24TPPmBjZXL03KfN3MB8GA1UdIwQYMBaA\n" +
        //            "FNQ1pt/TYpH24TPPmBjZXL03KfN3MA8GA1UdEwEB/wQFMAMBAf8wDQYJKoZIhvcN\n" +
        //            "AQELBQADggEBABnoCtapmJ4dj6MW9Uy6vTYsXy4RtKqoPTxRsNVaBE0BQgYeocVu\n" +
        //            "f08vRoXBEMKQVJruY/G8yhKbNYBPmqohgONsvhI5Rcf6ZvS1NqD3OQ8sBiwI2yCg\n" +
        //            "YBpSu7JcuJuaiXiU9pv90YdUf4WI/jUIOKiAmGQpcFHdexVFW0mb2VfxOooahdBP\n" +
        //            "elApY3vYRlTtjcmU4JaQ+dt5H+ebjUs4sJsroHfxJuQLxExDdvy/0M264hnqm1ad\n" +
        //            "iW0Tb2wlgBb7r3xgISxGIFeI7V1tqB90qidrFu8F/pUXwl6c02qr/zwjS5m9QnaP\n" +
        //            "FEFhTIKoFft56SNdx3U143h/NvnAccxVa0s=\n" +
        //            "-----END CERTIFICATE-----");
        //    });
        //    qz.security.setSignaturePromise(function (toSign) {
        //        return function (resolve, reject) {
        //            //$.ajax("{{$BASE_PATH}}api/certificadoImpressora?request=" + toSign).then(resolve, reject);
        //            resolve();
        //        };
        //    });
        //    qz.websocket.connect().then(function () {
        //        qz.printers.find().then(function (printers) {
        //            for (let i = 0; i < este.impressoras.length; i++) {
        //                for (let p = 0; p < printers.length; p++) {
        //                    if (este.impressoras[i] == printers[p]) {
        //                        este.printer = printers[p];
        //                    }
        //                    if (este.printer) { break; }
        //                }
        //                if (este.printer) { break; }
        //            }
        //            este.config = qz.configs.create(este.printer);
        //        }).catch(function (error) {
        //            Toast.fire({
        //                type: 'warning',
        //                title: "Impressora não localizada",
        //                text: error
        //            });
        //        });
        //    });
        //}
        ////PESQUISA ENDEREÇO MAPA
        //var componentForm = {
        //    route: 'long_name',                         //Rua
        //    street_number: 'short_name',                //Numero
        //    sublocality_level_1: 'long_name',           //Bairro
        //    political: 'long_name',                     //Bairro
        //    administrative_area_level_2: 'long_name',   //Cidade
        //    administrative_area_level_1: 'short_name',  //Estado
        //    postal_code: 'short_name'                   //Cep
        //};
        //var map = new google.maps.Map(document.getElementById('map'), {
        //    center: { lat: -6.911047, lng: -36.480329 },
        //    zoom: 7
        //});
        //var options = {
        //    componentRestrictions: {
        //        country: 'br'
        //    },
        //    strictBounds: true
        //};
        //var autocomplete = new google.maps.places.Autocomplete(document.getElementById('pac-input'), options);
        //autocomplete.setFields(['place_id', 'geometry', 'address_component', 'formatted_address']);
        //var infowindow = new google.maps.InfoWindow();
        //var marker = new google.maps.Marker({ map: map });
        //marker.addListener('click', function () {
        //    infowindow.open(map, marker);
        //});
        //var kmlLayer = new google.maps.KmlLayer();
        //var src = "https://www.google.com/maps/d/u/0/kml?forcekml=1&mid=1XtIOAREIy_1FYBuJ5cxGJW5IZYN4Ybk7&lid=CV1tkbC6Bm8";
        //var kmlLayer = new google.maps.KmlLayer(src, {
        //    suppressInfoWindows: true,
        //    preserveViewport: true,
        //    clickable: false,
        //    map: map
        //});
        //autocomplete.addListener('place_changed', function () {
        //    infowindow.close();
        //    var place = autocomplete.getPlace();
        //    if (!place.geometry) {
        //        return;
        //    }
        //    if (map.getZoom() < 17) {
        //        if (place.geometry.viewport) {
        //            map.fitBounds(place.geometry.viewport);
        //        } else {
        //            map.setCenter(place.geometry.location);
        //            map.setZoom(17);
        //        }
        //    }
        //    marker.setPlace({
        //        placeId: place.place_id,
        //        location: place.geometry.location
        //    });
        //    infowindow.setContent(place.formatted_address);
        //    infowindow.open(map, marker);
        //    for (var component in componentForm) {
        //        $("." + component).val('');
        //    }
        //    for (var i = 0; i < place.address_components.length; i++) {
        //        var addressType = place.address_components[i].types[0];
        //        if (componentForm[addressType]) {
        //            var val = place.address_components[i][componentForm[addressType]];
        //            $("." + addressType).val(val);
        //        }
        //    }
        //});
        //map.addListener("click", function (event) {
        //    var geocoder = new google.maps.Geocoder;
        //    geocoder.geocode({ 'location': event.latLng }, function (results, status) {
        //        console.log(results);
        //        if (status === 'OK') {
        //            if (results[0]) {
        //                marker.setPlace({
        //                    placeId: results[0].place_id,
        //                    location: results[0].geometry.location
        //                });
        //                if (map.getZoom() < 17) {
        //                    map.setZoom(17);
        //                }
        //                $('#pac-input').val(results[0].formatted_address);
        //                infowindow.setContent(results[0].formatted_address);
        //                infowindow.open(map, marker);
        //                for (var component in componentForm) {
        //                    $("." + component).val('');
        //                }
        //                for (var i = 0; i < results[0].address_components.length; i++) {
        //                    var addressType = results[0].address_components[i].types[0];
        //                    if (componentForm[addressType]) {
        //                        var val = results[0].address_components[i][componentForm[addressType]];
        //                        $("." + addressType).val(val);
        //                    }
        //                }
        //            } else {
        //                window.alert('Nenhum resultado encontrado');
        //            }
        //        } else {
        //            window.alert('Falha no geocoder devido a: ' + status);
        //        }
        //    });
        //});
    },
    // beforeDestroy() {
    //     qz.websocket.disconnect();
    // },
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
                this.pedido = response.data,
                    // this.pedido.horarioPedido = moment(this.pedido.horarioPedido).format("DD/MM/YYYY, HH:mm"),
                    // this.pedido.dataAgendada = this.pedido.dataAgendada != null ? moment(this.pedido.dataAgendada).format("DD/MM/YYYY"):"",
                    this.pedido.horaFim = this.pedido.horaFim != null ? moment("01-01-19, " + this.pedido.horaFim).format("HH:mm") : "",
                    this.pedido.horaInicio = this.pedido.horaInicio != null ? moment("01-01-19, " + this.pedido.horaInicio).format("HH:mm") : "",
                    this.pedido.totalProdutos = this.pedido.total - this.pedido.taxaEntrega,
                    this.pedido.total = this.pedido.total.toFixed(2).split('.'), this.pedido.total[0] = "R$ " + this.pedido.total[0].split(/(?=(?:...)*$)/).join('.'), this.pedido.total = this.pedido.total.join(','),
                    this.pedido.trocoPara = this.pedido.trocoPara.toFixed(2).split('.'), this.pedido.trocoPara[0] = "R$ " + this.pedido.trocoPara[0].split(/(?=(?:...)*$)/).join('.'), this.pedido.trocoPara = this.pedido.trocoPara.join(','),
                    this.pedido.taxaEntrega = this.pedido.taxaEntrega.toFixed(2).split('.'), this.pedido.taxaEntrega[0] = "R$ " + this.pedido.taxaEntrega[0].split(/(?=(?:...)*$)/).join('.'), this.pedido.taxaEntrega = this.pedido.taxaEntrega.join(','),
                    this.pedido.totalProdutos = this.pedido.totalProdutos.toFixed(2).split('.'), this.pedido.totalProdutos[0] = "R$ " + this.pedido.totalProdutos[0].split(/(?=(?:...)*$)/).join('.'), this.pedido.totalProdutos = this.pedido.totalProdutos.join(','),
                    this.endereco = this.pedido.endereco,
                    this.cliente = this.pedido.clientePedido,
                    this.itensPedido = this.pedido.itensPedido,
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
                    var printData = '';
                    printData += '\x1B' + '\x40'; 				// init
                    printData += '\x1B' + '\x61' + '\x31'; 		// center align
                    printData += '\x1B' + '\x21' + '\x01';         // font B
                    printData += '\x1D\x21\x00';                  //Altura
                    printData += '\x1B\x33\x00';                  //Spacing
                    //printData+=this.retiraAcento(this.pedido.distribuidor.nome) + '\x0A';
                    printData += 'www.tokumsede.com.br' + '\x0A';
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
                    printData += 'Acesse: www.aguasterrasanta.com.br' + '\x0A';
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
                    printData.push('www.tokumsede.com.br' + '\x0A');
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
        },
    }
}
</script>
