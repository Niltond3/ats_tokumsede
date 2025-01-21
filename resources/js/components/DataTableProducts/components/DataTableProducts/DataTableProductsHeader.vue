<script setup>
import Skeleton from '@/components/ui/skeleton/Skeleton.vue';
import { SelectDistributor } from '..';
import DebouncedInput from '../DebouncedInput.vue';

const props = defineProps({
  distributors: { type: Array, required: false, default: [] },
  loadingDistributors: { type: Boolean, required: false, default: false },
  idDistribuidor: { type: [String, Number], required: false },
  tableIdentifier: { type: String, required: false, default: null },
  status: { type: Object, required: false },
  clientName: { type: String, required: false },
  globalFilter: { type: [String, null], required: true },
});
const emits = defineEmits(['update:distributor', 'update:globalFilter', 'update:status']);

const handleStatusChange = () => {
  const getAction = `${props.status.label}${!props.status.oldStatus}`;
  const getCurrentCalback = (info) =>
    emits('update:status', {
      status: props.status,
      payload: props.status.statusId,
      info,
    });
  const statusActions = {
    Agendado: () => getCurrentCalback('Pedido Agendado!'),
    Pendentetrue: () => getCurrentCalback('Pedido Pendente!'),
    Pendentefalse: () =>
      emits('update:status', {
        status: props.status.oldStatus,
        payload: props.status.statusId,
        info: 'Status Restaurado!',
      }),
    default: () => {
      const pendente = {
        label: 'Pendente',
        classes: {
          bg: 'bg-warning',
          text: 'text-warning',
          icon: 'ri-error-warning-fill',
        },
      };
      const oldStatus = {
        ...props.status,
        statusId: props.status,
      };
      return emits('update:status', {
        status: { ...pendente, oldStatus },
        payload: 1,
        info: 'Status Alterado!',
      });
    },
  };
  statusActions[getAction] ? statusActions[getAction]() : statusActions.default();
};
</script>
<template>
  <div class="relative flex flex-wrap items-center pb-1 justify-between gap-3 group">
    <div class="flex flex-col gap-1 w-full md:flex-row">
      <template v-if="globalFilter === null">
        <Skeleton class="w-full h-10" />
      </template>
      <template v-else>
        <DebouncedInput
          :modelValue="globalFilter"
          placeholder="Todos os produtos..."
          @update:modelValue="emits('update:globalFilter', $event)"
        />
      </template>

      <template v-if="distributors">
        <template v-if="loadingDistributors">
          <Skeleton class="w-full h-10" />
        </template>
        <template v-else>
          <SelectDistributor
            :distributors="distributors"
            :default="idDistribuidor ? `${idDistribuidor}` : null"
            @update:distributor="(value) => emits('update:distributor', value)"
          />
        </template>
      </template>
      <template v-else>
        <template v-if="!tableIdentifier">
          <Skeleton class="w-full h-10" />
        </template>
        <template v-else>
          <span class="font-medium flex items-center justify-center text-info py-1 px-2 w-full">
            {{ tableIdentifier }}
          </span>
        </template>
      </template>
    </div>
    <div class="flex flex-col gap-1 w-full md:flex-row pb-2">
      <button
        v-if="status"
        :class="[status.classes.bg, status.label == 'Agendado' ? 'text-slate-700' : 'text-white']"
        class="relative font-semibold px-2 py-1 rounded-lg opacity-80 hover:opacity-100"
        @click="handleStatusChange"
      >
        <i
          v-if="status.label != 'Agendado' && status.label != 'Pendente'"
          class="ri-edit-2-fill absolute bg-white rounded-full w-5 h-5 flex justify-center items-center -top-3 -right-1"
          :class="status.classes.text"
        ></i>
        <i
          v-if="status.oldStatus"
          class="ri-arrow-go-back-fill absolute bg-white rounded-full w-5 h-5 flex justify-center items-center -top-3 -right-1"
          :class="status.classes.text"
        ></i>
        {{ status.label }}
      </button>
      <p v-if="clientName" class="text-sm font-semibold px-2 py-1 rounded-lg text-info">
        Cliente:
        <span class="font-medium">
          {{ clientName }}
        </span>
      </p>
      <p
        v-else
        class="text-sm font-semibold px-2 py-1 rounded-lg text-info flex w-full items-center gap-2"
      >
        Cliente:
        <Skeleton class="w-full h-10" />
      </p>
    </div>
  </div>
</template>
