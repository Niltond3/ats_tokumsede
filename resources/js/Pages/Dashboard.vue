<script setup>
import {
  DashboardCard,
  DashboardColumn,
  DashboardProgressBar,
  DashboardAvatar,
  DashboardDonutChart,
} from '@/components/dashboardColumn';

</script>

<script>
import { MoneyUtil } from '@/util';

const { toCurrency } = MoneyUtil.formatMoney();

export default {
  data() {
    return {
      total: '',
    };
  },
  mounted: function () {
    this.$root.refresh = false;
    this.getHomepage();
  },
  methods: {
    getHomepage: function () {
      var url = '/homepage';
      axios
        .get(url)
        .then((response) => {
          this.total = response.data;
          const totalPedidos =
            this.total.pedidosPlataforma + this.total.pedidosAndroid + this.total.pedidosIos;

          this.total.faturamentoAno = this.formataDinheiro(this.total.faturamentoAno);
          this.total.faturamentoMes = this.formataDinheiro(this.total.faturamentoMes);
          this.total.faturamentoSemana = this.formataDinheiro(this.total.faturamentoSemana);
          this.total.faturamentoDia = this.formataDinheiro(this.total.faturamentoDia);
          this.total.faturamentoAnoPassado = this.formataDinheiro(this.total.faturamentoAnoPassado);
          this.total.faturamentoMesPassado = this.formataDinheiro(this.total.faturamentoMesPassado);
          this.total.faturamentoSemanaPassada = this.formataDinheiro(
            this.total.faturamentoSemanaPassada,
          );
          this.total.faturamentoDiaPassado = this.formataDinheiro(this.total.faturamentoDiaPassado);

          this.total.percentualPlataforma =
            ((this.total.pedidosPlataforma * 100) / totalPedidos).toFixed(2) + '%';
          this.total.percentualAndroid =
            ((this.total.pedidosAndroid * 100) / totalPedidos).toFixed(2) + '%';
          this.total.percentualIOS =
            ((this.total.pedidosIos * 100) / totalPedidos).toFixed(2) + '%';

          this.total.chartDataPlatform = [
            {
              name: 'Outros',
              total: totalPedidos - this.total.pedidosPlataforma,
              predicted: totalPedidos,
            },
            {
              name: 'Pedidos Gerenciador',
              total: this.total.pedidosPlataforma,
              predicted: this.total.pedidosPlataforma,
            },
          ];

          this.total.chartDataAndroid = [
            {
              name: 'Outros',
              total: totalPedidos - this.total.pedidosAndroid,
              predicted: totalPedidos,
            },
            {
              name: 'Pedidos Android',
              total: this.total.pedidosAndroid,
              predicted: this.total.pedidosAndroid,
            },
          ];

          this.total.chartDataIos = [
            {
              name: 'Outros',
              total: totalPedidos - this.total.pedidosIos,
              predicted: totalPedidos,
            },
            { name: 'Pedidos Ios', total: this.total.pedidosIos, predicted: this.total.pedidosIos },
          ];
        })
        .catch((error) => {
          this.errors = error.response.data;
          Swal.fire({
            title: 'Aviso!',
            text: this.errors,
            type: 'warning',
            confirmButtonText: 'Fazer Login!',
          }).then((result) => {
            window.location.href = '/login';
          });
        });
    },
    formataDinheiro: (value) => toCurrency(value),
  },
};
</script>

<template>
  <div class="container p-0">
    <!-- Row -->
    <div v-if="total.faturamento" class="flex flex-wrap -mx-4">
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard class="!m-0">
          <div class="flex flex-row">
            <div class="p-3 bg-info">
              <h3 class="text-white rounded-sm p-3 mb-0"><i class="ti-wallet"></i></h3>
            </div>
            <div class="self-center ml-5">
              <h3 class="mb-0 text-xl font-medium text-info">{{ total.faturamentoAnoPassado }}</h3>
              <h5 class="text-gray-400 font-medium mb-0">Ano Passado</h5>
            </div>
          </div>
        </DashboardCard>
        <DashboardCard>
          <div class="flex-auto p-5">
            <h4 class="mb-3 text-lg font-normal">Faturamento Anual</h4>
            <div class="text-right">
              <i id="setaAno" class="text-info"></i
              ><span class="text-gray-400 font-medium">Neste Ano</span>
              <h2 class="font-light text-2xl text-gray-700">
                <sup></sup>{{ total.faturamentoAno }}
              </h2>
            </div>
            <span class="text-info">{{ total.percentualAno }}%</span>
            <DashboardProgressBar :percentual="total.percentualAno" />
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard class="!m-0">
          <div class="flex flex-row">
            <div class="p-3 bg-success">
              <h3 class="text-white rounded-sm p-3 mb-0"><i class="ti-wallet"></i></h3>
            </div>
            <div class="self-center ml-5">
              <h3 class="mb-0 text-xl font-medium text-success">
                {{ total.faturamentoMesPassado }}
              </h3>
              <h5 class="text-gray-400 font-medium mb-0">Mês Passado</h5>
            </div>
          </div>
        </DashboardCard>
        <DashboardCard>
          <div class="flex-auto p-5">
            <h4 class="mb-3 text-lg font-normal">Faturamento Mensal</h4>
            <div class="text-right">
              <i id="setaMes" class="text-success"></i
              ><span class="text-gray-400 font-medium">Neste Mês</span>
              <h2 class="font-light text-2xl text-gray-700">
                <sup></sup>{{ total.faturamentoMes }}
              </h2>
            </div>
            <span class="text-success">{{ total.percentualMes }}%</span>
            <DashboardProgressBar :percentual="total.percentualMes" variant="success" />
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard class="!m-0">
          <div class="flex flex-row">
            <div class="p-3 bg-inverse">
              <h3 class="text-white rounded-sm p-3 mb-0"><i class="ti-wallet"></i></h3>
            </div>
            <div class="self-center ml-5">
              <h3 class="mb-0 text-xl font-medium">{{ total.faturamentoSemanaPassada }}</h3>
              <h5 class="text-gray-400 font-medium mb-0">Semana Passada</h5>
            </div>
          </div>
        </DashboardCard>
        <DashboardCard>
          <div class="flex-auto p-5">
            <h4 class="mb-3 text-lg font-normal">Faturamento Semanal</h4>
            <div class="text-right">
              <i id="setaSemana" class="text-inverse"></i
              ><span class="text-gray-400 font-medium">Nesta Semana</span>
              <h2 class="font-light text-2xl text-gray-700">
                <sup></sup>{{ total.faturamentoSemana }}
              </h2>
            </div>
            <span class="text-inverse">{{ total.percentualSemana }}%</span>
            <DashboardProgressBar :percentual="total.percentualSemana" variant="inverse" />
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard class="!m-0">
          <div class="flex flex-row">
            <div class="p-3 bg-warning">
              <h3 class="text-white rounded-sm p-3 mb-0"><i class="ti-wallet"></i></h3>
            </div>
            <div class="self-center ml-5">
              <h3 class="mb-0 text-xl font-medium text-warning">
                {{ total.faturamentoDiaPassado }}
              </h3>
              <h5 class="text-gray-400 font-medium mb-0">Ontem</h5>
            </div>
          </div>
        </DashboardCard>
        <DashboardCard>
          <div class="flex-auto p-5">
            <h4 class="mb-3 text-lg font-normal">Faturamento Diário</h4>
            <div class="text-right">
              <i id="setaDia" class="text-warning"></i
              ><span class="text-gray-400 font-medium">Hoje</span>
              <h2 class="font-light text-2xl text-gray-700">
                <sup></sup>{{ total.faturamentoDia }}
              </h2>
            </div>
            <span class="text-warning">{{ total.percentualDia }}%</span>
            <DashboardProgressBar :percentual="total.percentualDia" variant="warning" />
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
    </div>
    <!-- Row -->
    <div class="flex flex-wrap -mx-4">
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-row">
              <DashboardAvatar class="bg-info"
                ><i class="mdi mdi-account-star"></i>
              </DashboardAvatar>
              <div class="ml-3 self-center">
                <h3 id="totalClientes" class="mb-0 font-light text-xl">
                  {{ total.clientesAtivos }}
                </h3>
                <h5 class="text-gray-400 font-medium mb-0">Clientes Ativos</h5>
              </div>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-row">
              <DashboardAvatar class="bg-info"><i class="mdi mdi-motorbike"></i> </DashboardAvatar>
              <div class="ml-3 self-center">
                <h3 class="mb-0 font-light text-xl">{{ total.entregadoresAtivos }}</h3>
                <h5 class="text-gray-400 font-medium mb-0">Entregadores Ativos</h5>
              </div>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-row">
              <DashboardAvatar class="bg-primary"
                ><i class="fas fa-clipboard-check"></i>
              </DashboardAvatar>
              <div class="ml-3 self-center">
                <h3 class="mb-0 font-light text-xl">{{ total.pedidosEntregues }}</h3>
                <h5 class="text-gray-400 font-medium mb-0">Pedidos Entregues</h5>
              </div>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-row">
              <DashboardAvatar class="bg-danger"
                ><i class="fas fa-exclamation-triangle"></i>
              </DashboardAvatar>
              <div class="ml-3 self-center">
                <h3 class="mb-0 font-light text-xl">{{ total.pedidosAceitos }}</h3>
                <h5 class="text-gray-400 font-medium mb-0">Pedidos à Entregar</h5>
              </div>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-row">
              <DashboardAvatar class="bg-info"
                ><i class="fas fa-clipboard-list"></i>
              </DashboardAvatar>
              <div class="ml-3 self-center">
                <h3 class="mb-0 font-light text-xl">{{ total.pedidosAno }}</h3>
                <h5 class="text-gray-400 font-medium mb-0">Pedidos no Ano</h5>
              </div>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-row">
              <DashboardAvatar class="bg-success"
                ><i class="fas fa-clipboard-list"></i>
              </DashboardAvatar>
              <div class="ml-3 self-center">
                <h3 class="mb-0 font-light text-xl">{{ total.pedidosMes }}</h3>
                <h5 class="text-gray-400 font-medium mb-0">Pedidos no Mês</h5>
              </div>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-row">
              <DashboardAvatar class="bg-inverse"
                ><i class="fas fa-clipboard-list"></i>
              </DashboardAvatar>
              <div class="ml-3 self-center">
                <h3 class="mb-0 font-light text-xl">{{ total.pedidosSemana }}</h3>
                <h5 class="text-gray-400 font-medium mb-0">Pedidos na Semana</h5>
              </div>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="md">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-row">
              <DashboardAvatar class="bg-warning"
                ><i class="fas fa-clipboard-list"></i>
              </DashboardAvatar>
              <div class="ml-3 self-center">
                <h3 class="mb-0 font-light text-xl">{{ total.pedidosDia }}</h3>
                <h5 class="text-gray-400 font-medium mb-0">Pedidos Hoje</h5>
              </div>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
    </div>
    <!-- Row -->
    <div class="flex flex-wrap -mx-4">
      <!-- Column -->
      <DashboardColumn variant="lg">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-wrap -mx-4 py-3">
              <!-- Column -->
              <DashboardColumn variant="base" class="pr-0 !bg-white">
                <h1 class="font-light">{{ total.percentualPlataforma }}</h1>
                <h6 class="text-gray-400 font-medium">Pedidos Através do Gerenciador</h6>
              </DashboardColumn>
              <!-- Column -->
              <DashboardColumn variant="base" class="text-right self-center !bg-white">
                <DashboardDonutChart
                  :data="total.chartDataPlatform ? total.chartDataPlatform : []"
                />
              </DashboardColumn>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="lg">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-wrap -mx-4 py-3">
              <!-- Column -->
              <DashboardColumn variant="base" class="pr-0 !bg-white">
                <h1 class="font-light">{{ total.percentualAndroid }}</h1>
                <h6 class="text-gray-400 font-medium">Pedidos Através do App Android</h6>
              </DashboardColumn>
              <!-- Column -->
              <DashboardColumn variant="base" class="text-right self-center !bg-white">
                <DashboardDonutChart :data="total.chartDataAndroid ? total.chartDataAndroid : []" />
              </DashboardColumn>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
      <!-- Column -->
      <DashboardColumn variant="lg">
        <DashboardCard>
          <div class="flex-auto p-5">
            <div class="flex flex-wrap -mx-4 py-3">
              <DashboardColumn variant="base" class="pr-0 !bg-white">
                <h1 class="font-light">{{ total.percentualIOS }}</h1>
                <h6 class="text-gray-400 font-medium">Pedidos Através do App IOS</h6>
              </DashboardColumn>
              <DashboardColumn variant="base" class="text-right self-center !bg-white">
                <DashboardDonutChart :data="total.chartDataIos ? total.chartDataIos : []" />
              </DashboardColumn>
            </div>
          </div>
        </DashboardCard>
      </DashboardColumn>
      <!-- Column -->
    </div>
    <!-- Row -->
  </div>
</template>
