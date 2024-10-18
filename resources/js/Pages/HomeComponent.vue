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
        <div v-if="total.faturamento" class="row">
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card m-0">
                    <div class="d-flex flex-row">
                        <div class="p-10 bg-info">
                            <h3 class="text-white box m-b-0"><i class="ti-wallet"></i></h3>
                        </div>
                        <div class="align-self-center m-l-20">
                            <h3 class="m-b-0 text-info">{{ total.faturamentoAnoPassado }}</h3>
                            <h5 class="text-muted m-b-0">Ano Passado</h5>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Faturamento Anual</h4>
                        <div class="text-right"><i id="setaAno" class="text-info"></i><span class="text-muted">Neste
                                Ano</span>
                            <h2 class="font-light"><sup></sup>{{ total.faturamentoAno }}</h2>
                        </div>
                        <span class="text-info">{{ total.percentualAno }}%</span>
                        <div class="progress">
                            <div class="progress-bar bg-info" role="progressbar"
                                :style="{ width: total.percentualAno + '%', height: '6px' }" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card m-0">
                    <div class="d-flex flex-row">
                        <div class="p-10 bg-success">
                            <h3 class="text-white box m-b-0"><i class="ti-wallet"></i></h3>
                        </div>
                        <div class="align-self-center m-l-20">
                            <h3 class="m-b-0 text-success">{{ total.faturamentoMesPassado }}</h3>
                            <h5 class="text-muted m-b-0">Mês Passado</h5>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Faturamento Mensal</h4>
                        <div class="text-right"><i id="setaMes" class="text-success"></i><span class="text-muted">Neste
                                Mês</span>
                            <h2 class="font-light"><sup></sup>{{ total.faturamentoMes }}</h2>
                        </div>
                        <span class="text-success">{{ total.percentualMes }}%</span>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar"
                                :style="{ width: total.percentualMes + '%', height: '6px' }" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card m-0">
                    <div class="d-flex flex-row">
                        <div class="p-10 bg-inverse">
                            <h3 class="text-white box m-b-0"><i class="ti-wallet"></i></h3>
                        </div>
                        <div class="align-self-center m-l-20">
                            <h3 class="m-b-0">{{ total.faturamentoSemanaPassada }}</h3>
                            <h5 class="text-muted m-b-0">Semana Passada</h5>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Faturamento Semanal</h4>
                        <div class="text-right"><i id="setaSemana" class="text-inverse"></i><span
                                class="text-muted">Nesta Semana</span>
                            <h2 class="font-light"><sup></sup>{{ total.faturamentoSemana }}</h2>
                        </div>
                        <span class="text-inverse">{{ total.percentualSemana }}%</span>
                        <div class="progress">
                            <div class="progress-bar bg-inverse" role="progressbar"
                                :style="{ width: total.percentualSemana + '%', height: '6px' }" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card m-0">
                    <div class="d-flex flex-row">
                        <div class="p-10 bg-warning">
                            <h3 class="text-white box m-b-0"><i class="ti-wallet"></i></h3>
                        </div>
                        <div class="align-self-center m-l-20">
                            <h3 class="m-b-0 text-warning">{{ total.faturamentoDiaPassado }}</h3>
                            <h5 class="text-muted m-b-0">Ontem</h5>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Faturamento Diário</h4>
                        <div class="text-right"><i id="setaDia" class="text-warning"></i><span
                                class="text-muted">Hoje</span>
                            <h2 class="font-light"><sup></sup>{{ total.faturamentoDia }}</h2>
                        </div>
                        <span class="text-warning">{{ total.percentualDia }}%</span>
                        <div class="progress">
                            <div class="progress-bar bg-warning" role="progressbar"
                                :style="{ width: total.percentualDia + '%', height: '6px' }" aria-valuenow="25"
                                aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
        </div>
        <!-- Row -->
        <div class="row">
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-info"><i
                                    class="mdi mdi-account-star"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-light" id="totalClientes">{{ total.clientesAtivos }}</h3>
                                <h5 class="text-muted m-b-0">Clientes Ativos</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-info"><i class="mdi mdi-motorbike"></i>
                            </div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">{{ total.entregadoresAtivos }}</h3>
                                <h5 class="text-muted m-b-0">Entregadores Ativos</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-primary"><i
                                    class="fas fa-clipboard-check"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">{{ total.pedidosEntregues }}</h3>
                                <h5 class="text-muted m-b-0">Pedidos Entregues</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-danger"><i
                                    class="fas fa-exclamation-triangle"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">{{ total.pedidosAceitos }}</h3>
                                <h5 class="text-muted m-b-0">Pedidos à Entregar</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-info"><i
                                    class="fas fa-clipboard-list"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">{{ total.pedidosAno }}</h3>
                                <h5 class="text-muted m-b-0">Pedidos no Ano</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-success"><i
                                    class="fas fa-clipboard-list"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">{{ total.pedidosMes }}</h3>
                                <h5 class="text-muted m-b-0">Pedidos no Mês</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center bg-inverse"><i
                                    class="fas fa-clipboard-list"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">{{ total.pedidosSemana }}</h3>
                                <h5 class="text-muted m-b-0">Pedidos na Semana</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-row">
                            <div class="round round-lg align-self-center round-warning"><i
                                    class="fas fa-clipboard-list"></i></div>
                            <div class="m-l-10 align-self-center">
                                <h3 class="m-b-0 font-lgiht">{{ total.pedidosDia }}</h3>
                                <h5 class="text-muted m-b-0">Pedidos Hoje</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- <div class="col-lg-6 col-md-12">
            <div class="card">
                <img class="" src="/vendor/wrappixel/material-pro/4.1.0/assets/images/background/weatherbg.jpg" alt="Card image cap">
                <div class="card-img-overlay" style="height:110px;">
                    <h3 class="card-title text-white m-b-0 dl">{{clima.name}}-{{clima.state}}</h3>
                    <div class="clearfix"></div>
                    <small class="card-text text-white font-light" id="data">{{clima.data.date}}</small>
                </div>
                <div class="card-body weather-small">
                    <div class="row">
                        <div class="col-8 b-r align-self-center">
                            <div class="d-flex">
                                <div class="display-6 text-info"><i class="wi wi-day-rain-wind"></i></div>
                                <div class="m-l-20">
                                    <h1 class="font-light text-info m-b-0">{{clima.data.temperature}}<sup>0</sup></h1>
                                    <small>{{clima.data.condition}}</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4 text-center">
                            <h1 class="font-light m-b-0">25<sup>0</sup></h1>
                            <small>Tonight</small>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        </div>
        <!-- Row -->
        <div class="row">
            <!-- Column -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row p-t-10 p-b-10">
                            <!-- Column -->
                            <div class="col p-r-0">
                                <h1 class="font-light">{{ total.pedidosPlataforma }}</h1>
                                <h6 class="text-muted">Pedidos Através do Gerenciador</h6>
                            </div>
                            <!-- Column -->
                            <div class="col text-right align-self-center">
                                <div :data-label="total.percentualPlataforma + '%'"
                                    :class="'css-bar m-b-0 css-bar-info css-bar-' + total.percentualPlataforma"><i
                                        class="fab fa-chrome"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row p-t-10 p-b-10">
                            <!-- Column -->
                            <div class="col p-r-0">
                                <h1 class="font-light">{{ total.pedidosAndroid }}</h1>
                                <h6 class="text-muted">Pedidos Através do App Android</h6>
                            </div>
                            <!-- Column -->
                            <div class="col text-right align-self-center">
                                <div :data-label="total.percentualAndroid + '%'"
                                    :class="'css-bar m-b-0 css-bar-success css-bar-' + total.percentualAndroid"><i
                                        class="mdi mdi-android"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Column -->
            <!-- Column -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row p-t-10 p-b-10">
                            <!-- Column -->
                            <div class="col p-r-0">
                                <h1 class="font-light">{{ total.pedidosIos }}</h1>
                                <h6 class="text-muted">Pedidos Através do App IOS</h6>
                            </div>
                            <!-- Column -->
                            <div class="col text-right align-self-center">
                                <div :data-label="total.percentualIos + '%'"
                                    :class="'css-bar m-b-0 css-bar-inverse css-bar-' + total.percentualIos"><i
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
