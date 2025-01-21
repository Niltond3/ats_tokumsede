<script setup>
import { ref } from 'vue';
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Form } from '@/components/ui/form';
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css';
import { Select } from '@/components/ui/select';
import { Button } from '@/components/ui/button';

const isOpen = ref(false);
const dateRange = ref([null, null]);
const selectedDistributors = ref([]);

async function fetchSalesReport() {
  const [startDate, endDate] = dateRange.value;
  const response = await axios.post('/relatorio/vendas', {
    dataInicial: startDate?.toLocaleDateString('pt-BR'),
    dataFinal: endDate?.toLocaleDateString('pt-BR'),
    idDistribuidores: selectedDistributors.value.join(','),
  });
  console.log(response);
  // Handle response data
}
</script>

<template>
  <Dialog :open="isOpen">
    <slot name="trigger" @click="isOpen = true" />
    <DialogContent>
      <DialogHeader>
        <DialogTitle>Relatório de Vendas</DialogTitle>
      </DialogHeader>
      <Form @submit="fetchSalesReport">
        <VueDatePicker
          v-model="dateRange"
          range
          locale="pt-BR"
          format="dd/MM/yyyy"
          :enable-time-picker="false"
          auto-apply
        />
        <Select v-model="selectedDistributors" multiple />
        <Button type="submit">Gerar Relatório</Button>
      </Form>
    </DialogContent>
  </Dialog>
</template>
