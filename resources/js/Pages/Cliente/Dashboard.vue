<script setup>
import { onMounted, ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { utf8Decode } from '@/util';
import { Button } from '@/components/ui/button';
import {
  Command,
  CommandEmpty,
  CommandGroup,
  CommandInput,
  CommandItem,
  CommandList,
} from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { ChevronsUpDown } from 'lucide-vue-next';
import { DialogCreateOrder } from './components/DialogCreateOrder';
import { TablePedidos } from '../Management/DataTablePedidos';
import renderToast from '@/components/renderPromiseToast';
import { cn } from '@/lib/utils';
import { dialogState } from '@/hooks/useToggleDialog';
import { Check } from 'lucide-vue-next';

const { isOpen, toggleDialog } = dialogState();

const page = usePage();

const client = page.props.auth.user;
const clientIdAddress = ref(null);
const updateDataTable = ref(false);
const welcome = ref(
  client.sexo == 1
    ? 'Bem vindo senhor ' + utf8Decode(client.nome)
    : 'Bem vinda senhora ' + utf8Decode(client.nome),
);

const addresses = ref([]);

const open = ref(false);

const value = ref('');

onMounted(async () => {
  const promise = axios.get(`enderecos/${client.id}`);

  renderToast(
    promise,
    'Carregando endereços...',
    'Endereços carregados com sucesso!',
    'Erro ao carregar endereços!',
    (response) => {
      const formatAddresses = response.data[1].map((address) => {
        const { logradouro, numero, bairro, cidade, estado, complemento, referencia, id } = address;

        return {
          id,
          value: `${utf8Decode(logradouro)}, ${numero} - ${utf8Decode(bairro)}`,
          city: `${utf8Decode(cidade)} - ${utf8Decode(estado)}`,
          complement: utf8Decode(complemento) || '',
          referency: utf8Decode(referencia) || '',
        };
      });

      addresses.value = formatAddresses;
    },
  );
});

watch(
  () => value.value,
  (newValue) => {
    if (!newValue) return;
    clientIdAddress.value = addresses.value.find((address) => address.value === newValue).id;
    toggleDialog();
  },
);
watch(
  () => isOpen.value,
  (newValue) => {
    if (!newValue) {
      value.value = '';
      open.value = false;
    }
  },
);
</script>

<template>
  <div>
    <Head title="Cliente Dashboard" />
    <AuthenticatedLayout>
      <template #header>
        <h2 class="text-xl leading-tight">{{ welcome }}</h2>
        <h4>selecione seu endereço para realizar um novo pedido</h4>
        <div class="w-full relative items-center justify-center flex">
          <Popover v-model:open="open">
            <PopoverTrigger as-child>
              <Button
                variant="outline"
                role="combobox"
                :aria-expanded="open"
                class="justify-between rounded-md h-9 text-info/80 py-1 pl-9 pr-3 hover:text-info transition-colors"
              >
                <i class="ri-truck-fill"></i>
                {{
                  value
                    ? addresses.find((address) => address.value === value)?.value
                    : 'Realizar Pedido'
                }}
                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-[285px] p-0 border-none">
              <Command v-model="value">
                <CommandInput placeholder="Endereços..." class="!ring-0 border-0 text-info/80" />
                <CommandList>
                  <CommandEmpty class="p-2">Nenhum endereço encontrado.</CommandEmpty>
                  <CommandGroup>
                    <div v-for="address in addresses" v-bind:key="address.id">
                      <CommandItem
                        :key="address.value"
                        :value="address.value"
                        class="flex flex-col items-start"
                      >
                        <Check
                          :class="
                            cn(
                              'mr-2 h-4 w-4',
                              value === address.value ? 'opacity-100' : 'opacity-0',
                            )
                          "
                        />
                        <span>{{ address.value }}</span>
                        <span class="text-xs text-muted-foreground">{{ address.city }}</span>
                        <span v-if="address.complement" class="text-xs text-info/70">{{
                          address.complement
                        }}</span>
                        <span v-if="address.referency" class="text-xs text-info/70">{{
                          address.referency
                        }}</span>
                      </CommandItem>
                    </div>
                  </CommandGroup>
                </CommandList>
              </Command>
            </PopoverContent>
          </Popover>
        </div>
      </template>
      <DialogCreateOrder
        :open="isOpen"
        :toggleDialog="toggleDialog"
        :id-cliente-address="clientIdAddress"
        :client-name="utf8Decode(client.nome)"
        :value="value"
        @update:dataTable="(date) => (updateDataTable = date)"
      />
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg">
            <TablePedidos
              ajustClass="min-[768px]:!top-[90px]"
              :updatedTable="updateDataTable"
              @tableDataLoaded="() => (updateDataTable = false)"
            ></TablePedidos>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  </div>
</template>
