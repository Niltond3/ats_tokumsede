<script setup>
import { RiWallet3Line as WalletIcon } from 'vue-remix-icons';
</script>

<script>
export default {
    data() {
        return {
            total: ''
        }
    },
    methods: {
        getHomepage: function () {
            var url = '/homepage';
            axios.get(url).then(response => {
                this.total = response.data,
                    this.total.faturamentoAno > this.total.faturamentoAnoPassado ? $('#setaAno').addClass('ti-arrow-up') : $('#setaAno').addClass('ti-arrow-down'),
                    this.total.faturamentoMes > this.total.faturamentoMesPassado ? $('#setaMes').addClass('ti-arrow-up') : $('#setaMes').addClass('ti-arrow-down'),
                    this.total.faturamentoSemana > this.total.faturamentoSemanaPassada ? $('#setaSemana').addClass('ti-arrow-up') : $('#setaSemana').addClass('ti-arrow-down'),
                    this.total.faturamentoDia > this.total.faturamentoDiaPassado ? $('#setaDia').addClass('ti-arrow-up') : $('#setaDia').addClass('ti-arrow-down'),
                    this.total.faturamentoAno = this.formataDinheiro(this.total.faturamentoAno),
                    this.total.faturamentoMes = this.formataDinheiro(this.total.faturamentoMes),
                    this.total.faturamentoSemana = this.formataDinheiro(this.total.faturamentoSemana),
                    this.total.faturamentoDia = this.formataDinheiro(this.total.faturamentoDia),
                    this.total.faturamentoAnoPassado = this.formataDinheiro(this.total.faturamentoAnoPassado),
                    this.total.faturamentoMesPassado = this.formataDinheiro(this.total.faturamentoMesPassado),
                    this.total.faturamentoSemanaPassada = this.formataDinheiro(this.total.faturamentoSemanaPassada),
                    this.total.faturamentoDiaPassado = this.formataDinheiro(this.total.faturamentoDiaPassado)
                // //this.clima = response.data[4],
                // //moment.defineLocale('pt-BR', {months : 'janeiro_fevereiro_março_abril_maio_junho_julho_agosto_setembro_outubro_novembro_dezembro'.split('_'),weekdays : 'domingo_segunda-feira_terça-feira_quarta-feira_quinta-feira_sexta-feira_sábado'.split('_'),}),
                // //this.clima.data.date = moment(this.clima.data.date).format('LLLL'),
            }).catch(error => {
                console.log(error)
                this.errors = error.response.data;
                console.log(error)
                Swal.fire({
                    title: "Aviso!",
                    text: this.errors,
                    type: 'warning',
                    confirmButtonText: 'Fazer Login!'
                }).then((result) => {
                    window.location.href = '/login';
                });
            });
        },
        formataDinheiro: function (n) {
            return "R$" + n.toFixed(2).replace('.', ',').replace(/(\d)(?=(\d{3})+\,)/g, "$1.");
        },
    },
    mounted: function () {
        // window.location.href = '/#/';
        // $("#linkbreadcrumb li").remove();
        // $("#linkicon").html("<i class='fas fa-home'></i>");
        // $("#linklocal").text("Página Inicial");
        // $("#linkbreadcrumb").append('<li class="breadcrumb-item active"><a href="/#/">Home</a></li>');
        this.$root.refresh = false;
        this.getHomepage();
    }

}
</script>

<template>
    <div class="container p-0">
        <!-- Row -->
        <div v-if="total.faturamento" class="flex flex-wrap -mx-4">
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px">
                    <div class="flex flex-row">
                        <div class="p-3 bg-info">
                            <h3 class="text-white rounded p-3 mb-0">
                                <WalletIcon class="text-base w-6" />
                            </h3>
                        </div>
                        <div class="self-center ml-5">
                            <h3 class="mb-0 text-info">{{ total.faturamentoAnoPassado }}</h3>
                            <h5 class="text-[#99abb4] mb-0">Ano Passado</h5>
                        </div>
                    </div>
                </div>
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <h4 class="mb-3">Faturamento Anual</h4>
                        <div class="text-right"><i id="setaAno" class="text-info"></i><span class="text-[#99abb4]">Neste
                                Ano</span>
                            <h2 class="font-light"><sup></sup>{{ total.faturamentoAno }}</h2>
                        </div>
                        <span class="text-info">{{ total.percentualAno }}%</span>
                        <div class="h-auto progress">
                            <div class="flex flex-col justify-center text-white text-center whitespace-nowrap bg-info"
                                role="progressbar" :style="{ width: total.percentualAno + '%', height: '6px' }"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px">
                    <div class="flex flex-row">
                        <div class="p-3 bg-success">
                            <h3 class="text-white rounded p-3 mb-0">
                                <WalletIcon class="text-base w-6" />
                            </h3>
                        </div>
                        <div class="align-self-center m-l-20">
                            <h3 class="mb-0 text-success">{{ total.faturamentoMesPassado }}</h3>
                            <h5 class="text-[#99abb4] mb-0">Mês Passado</h5>
                        </div>
                    </div>
                </div>
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <h4 class="mb-3">Faturamento Mensal</h4>
                        <div class="text-right"><i id="setaMes" class="text-success"></i><span
                                class="text-[#99abb4]">Neste
                                Mês</span>
                            <h2 class="font-light"><sup></sup>{{ total.faturamentoMes }}</h2>
                        </div>
                        <span class="text-success">{{ total.percentualMes }}%</span>
                        <div class="h-auto progress">
                            <div class="flex flex-col justify-center text-white text-center whitespace-nowrap bg-success"
                                role="progressbar" :style="{ width: total.percentualMes + '%', height: '6px' }"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px">
                    <div class="flex flex-row">
                        <div class="p-3 bg-inverse">
                            <h3 class="text-white rounded p-3 mb-0">
                                <WalletIcon class="text-base w-6" />
                            </h3>
                        </div>
                        <div class="align-self-center m-l-20">
                            <h3 class="mb-0">{{ total.faturamentoSemanaPassada }}</h3>
                            <h5 class="text-[#99abb4] mb-0">Semana Passada</h5>
                        </div>
                    </div>
                </div>
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <h4 class="mb-3">Faturamento Semanal</h4>
                        <div class="text-right"><i id="setaSemana" class="text-inverse"></i><span
                                class="text-[#99abb4]">Nesta Semana</span>
                            <h2 class="font-light"><sup></sup>{{ total.faturamentoSemana }}</h2>
                        </div>
                        <span class="text-inverse">{{ total.percentualSemana }}%</span>
                        <div class="h-auto progress">
                            <div class="flex flex-col justify-center text-white text-center whitespace-nowrap bg-inverse"
                                role="progressbar" :style="{ width: total.percentualSemana + '%', height: '6px' }"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px">
                    <div class="flex flex-row">
                        <div class="p-3 bg-warning">
                            <h3 class="text-white rounded p-3 mb-0">
                                <WalletIcon class="text-base w-6" />
                            </h3>
                        </div>
                        <div class="align-self-center m-l-20">
                            <h3 class="mb-0 text-warning">{{ total.faturamentoDiaPassado }}</h3>
                            <h5 class="text-[#99abb4] mb-0">Ontem</h5>
                        </div>
                    </div>
                </div>
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <h4 class="mb-3">Faturamento Diário</h4>
                        <div class="text-right"><i id="setaDia" class="text-warning"></i><span
                                class="text-[#99abb4]">Hoje</span>
                            <h2 class="font-light"><sup></sup>{{ total.faturamentoDia }}</h2>
                        </div>
                        <span class="text-warning">{{ total.percentualDia }}%</span>
                        <div class="h-auto progress">
                            <div class="flex flex-col justify-center text-white text-center whitespace-nowrap bg-warning"
                                role="progressbar" :style="{ width: total.percentualDia + '%', height: '6px' }"
                                aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
        </div>
        <!-- Row -->
        <div class="flex flex-wrap -mx-4">
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-row">
                            <div
                                class="text-white font-normal inline-block text-center rounded-full leading-[65px] w-16 h-16 text-3xl round-lg align-self-center bg-info">
                                <i class="mdi mdi-account-star"></i>
                            </div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="mb-0 font-light" id="totalClientes">{{ total.clientesAtivos }}</h3>
                                <h5 class="text-[#99abb4] mb-0">Clientes Ativos</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-row">
                            <div
                                class="text-white font-normal inline-block text-center rounded-full w-16 h-16 text-3xl round-lg align-self-center bg-info">
                                <i class="mdi mdi-motorbike"></i>
                            </div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="mb-0 font-lgiht">{{ total.entregadoresAtivos }}</h3>
                                <h5 class="text-[#99abb4] mb-0">Entregadores Ativos</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-row">
                            <div
                                class="text-white font-normal inline-block text-center rounded-full leading-[65px] w-16 h-16 text-3xl round-lg align-self-center bg-primary">
                                <i class="fas fa-clipboard-check"></i>
                            </div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="mb-0 font-lgiht">{{ total.pedidosEntregues }}</h3>
                                <h5 class="text-[#99abb4] mb-0">Pedidos Entregues</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-row">
                            <div
                                class="text-white font-normal inline-block text-center rounded-full leading-[65px] w-16 h-16 text-3xl round-lg align-self-center bg-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="mb-0 font-lgiht">{{ total.pedidosAceitos }}</h3>
                                <h5 class="text-[#99abb4] mb-0">Pedidos à Entregar</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-row">
                            <div
                                class="text-white font-normal inline-block text-center rounded-full leading-[65px] w-16 h-16 text-3xl round-lg align-self-center bg-info">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="mb-0 font-lgiht">{{ total.pedidosAno }}</h3>
                                <h5 class="text-[#99abb4] mb-0">Pedidos no Ano</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-row">
                            <div
                                class="text-white font-normal inline-block text-center rounded-full leading-[65px] w-16 h-16 text-3xl round-lg align-self-center bg-success">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="mb-0 font-lgiht">{{ total.pedidosMes }}</h3>
                                <h5 class="text-[#99abb4] mb-0">Pedidos no Mês</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-row">
                            <div
                                class="text-white font-normal inline-block text-center rounded-full leading-[65px] w-16 h-16 text-3xl round-lg align-self-center bg-inverse">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="mb-0 font-lgiht">{{ total.pedidosSemana }}</h3>
                                <h5 class="text-[#99abb4] mb-0">Pedidos na Semana</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div
                class="lg:flex-[0_0_25%] lg:max-w-[25%] md:flex-[0_0_50%] md:max-w-[50%] px-3 pb-6 bg-gray-100 bg-background">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-row">
                            <div
                                class="text-white font-normal inline-block text-center rounded-full leading-[65px] w-16 h-16 text-3xl round-lg align-self-center bg-warning">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="mb-0 font-lgiht">{{ total.pedidosDia }}</h3>
                                <h5 class="text-[#99abb4] mb-0">Pedidos Hoje</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- <div class="col-lg-6 col-md-12">
            <div class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                <img class="" src="/vendor/wrappixel/material-pro/4.1.0/assets/images/background/weatherbg.jpg" alt="Card image cap">
                <div class="card-img-overlay" style="height:110px;">
                    <h3 class="mb-3 text-white mb-0 dl">{{clima.name}}-{{clima.state}}</h3>
                    <div class="clearfix"></div>
                    <small class="card-text text-white font-light" id="data">{{clima.data.date}}</small>
                </div>
                <div class="flex-auto p-5 weather-small">
                    <div class="flex flex-wrap -mx-4">
                        <div class="col-8 b-r align-self-center">
                            <div class="flex">
                                <div class="display-6 text-info"><i class="wi wi-day-rain-wind"></i></div>
                                <div class="m-l-20">
                                    <h1 class="font-light text-info mb-0">{{clima.data.temperature}}<sup>0</sup></h1>
                                    <small>{{clima.data.condition}}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h1 class="font-light mb-0">25<sup>0</sup></h1>
                            <small>Tonight</small>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        </div>
        <!-- Row -->
        <div class="flex flex-wrap -mx-4">
            <!-- Column -->
            <div class="col-lg-4">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-wrap -mx-4 p-t-10 p-b-10">
                            <!-- Column -->
                            <div class="col p-r-0">
                                <h1 class="font-light">{{ total.pedidosPlataforma }}</h1>
                                <h6 class="text-[#99abb4]">Pedidos Através do Gerenciador</h6>
                            </div>
                            <!-- Column -->
                            <div class="col text-right align-self-center">
                                <div :data-label="total.percentualPlataforma + '%'"
                                    :class="'css-bar mb-0 css-bar-info css-bar-' + total.percentualPlataforma"><i
                                        class="fab fa-chrome"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-4">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-wrap -mx-4 p-t-10 p-b-10">
                            <!-- Column -->
                            <div class="col p-r-0">
                                <h1 class="font-light">{{ total.pedidosAndroid }}</h1>
                                <h6 class="text-[#99abb4]">Pedidos Através do App Android</h6>
                            </div>
                            <!-- Column -->
                            <div class="col text-right align-self-center">
                                <div :data-label="total.percentualAndroid + '%'"
                                    :class="'css-bar mb-0 css-bar-success css-bar-' + total.percentualAndroid"><i
                                        class="mdi mdi-android"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-4">
                <div
                    class="relative flex flex-col min-w-0 break-words bg-white bg-clip-border border-solid border border-black/15 rounded w-full min-h-px px-4">
                    <div class="flex-auto p-5">
                        <div class="flex flex-wrap -mx-4 p-t-10 p-b-10">
                            <!-- Column -->
                            <div class="col p-r-0">
                                <h1 class="font-light">{{ total.pedidosIos }}</h1>
                                <h6 class="text-[#99abb4]">Pedidos Através do App IOS</h6>
                            </div>
                            <!-- Column -->
                            <div class="col text-right align-self-center">
                                <div :data-label="total.percentualIos + '%'"
                                    :class="'css-bar mb-0 css-bar-inverse css-bar-' + total.percentualIos"><i
                                        class="mdi mdi-apple"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
        </div>
        <!-- Row -->
    </div>
</template>
