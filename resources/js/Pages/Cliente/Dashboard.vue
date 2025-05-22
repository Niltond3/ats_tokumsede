<script setup>
import { onMounted, ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { StringUtil } from '@/util';
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
import { DialogCreateOrder } from '@/components/dialogs/DialogClientCreateOrder';
import { TablePedidos } from '@/components/dataTables/DataTablePedidos';
import renderToast from '@/components/renderPromiseToast';
import { dialogState } from '@/composables/useToggleDialog';
import DialogRegisterAddress from '@/components/dialogs/DialogRegisterAddress.vue';
import Separator from '@/components/ui/separator/Separator.vue';
import DialogConfirmAddressDelete from '@/components/dialogs/DialogConfirmAddressDelete.vue';

const { isOpen: isOpenCreateOrderDialog, toggleDialog: toggleCreateOrderDialog } =
  dialogState('CreateOrder');
const { isOpen: isOpenRegisterAddressDialog, toggleDialog: toggleDialogRegisterAddress } =
  dialogState('RegisterAddress');
const { isOpen: isOpenConfirmDeleAddressDialog, toggleDialog: toggleDialogConfirmDeleAddress } =
  dialogState('ConfirmAddressDelete');

const page = usePage();

const client = page.props.auth.user;
const addressDetails = ref(null);
const clientIdAddress = ref(null);
const updateDataTable = ref(false);
const addressTarget = ref({});
const welcome = ref(
  client.sexo == 1
    ? 'Bem vindo senhor ' + StringUtil.utf8Decode(client.nome)
    : 'Bem vinda senhora ' + StringUtil.utf8Decode(client.nome),
);

const formatAddresses = ref([]);

const open = ref(false);

const value = ref('');
const fetchAddresses = () => {
  const promise = axios.get(`enderecos/${client.id}`);

  renderToast(
    promise,
    'Carregando endereços...',
    'Endereços carregados com sucesso!',
    'Erro ao carregar endereços!',
    (response) => {
      formatAddresses.value = response.data[1].map((address) => {
        const { logradouro, numero, bairro, cidade, estado, complemento, referencia, id } = address;

        return {
          ...address,
          id,
          value: `${StringUtil.utf8Decode(logradouro)}, ${numero} - ${StringUtil.utf8Decode(bairro)}`,
          city: `${StringUtil.utf8Decode(cidade)} - ${StringUtil.utf8Decode(estado)}`,
          complement: StringUtil.utf8Decode(complemento) || '',
          referency: StringUtil.utf8Decode(referencia) || '',
        };
      });
    },
  );
};
onMounted(async () => fetchAddresses());

const setDefaultValues = () => {
  addressDetails.value = null;
  value.value = '';
  open.value = false;
};

const handleEditAddress = () => {
  setTimeout(() => {
    addressDetails.value && toggleDialogRegisterAddress();
  }, 50);
};

const handleDeleteAddress = () => {
  toggleDialogConfirmDeleAddress();
  fetchAddresses();
  setDefaultValues();
};

watch(
  () => value.value,
  (newValue) => {
    if (!newValue) return;
    addressDetails.value = formatAddresses.value.find((address) => address.value === newValue);
    clientIdAddress.value = addressDetails.value.id;
  },
);
watch(
  () => isOpenRegisterAddressDialog.value,
  (newValue) => {
    if (!newValue) setDefaultValues();
  },
);
watch(
  () => isOpenCreateOrderDialog.value,
  (newValue) => {
    if (!newValue) setDefaultValues();
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
                    ? formatAddresses.find((address) => address.value === value)?.value
                    : 'Realizar Pedido'
                }}
                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
              </Button>
            </PopoverTrigger>
            <PopoverContent class="w-[285px] p-0 border-none">
              <Command v-model="value">
                <CommandInput placeholder="Endereços..." class="!ring-0 border-0 text-info/80" />
                <CommandList>
                  <CommandEmpty class="p-1 hover:bg-slate-100"
                    ><button
                      class="group flex items-center text-info/80 transition-colors border-2 rounded-md py-1 px-3 border-white hover:bg-info hover:text-white w-full justify-center gap-3"
                      @click="toggleDialogRegisterAddress"
                    >
                      <i class="ri-map-pin-add-fill text-2xl"></i>
                      <div class="flex flex-col">
                        <p class="text-info/40">Nenhum endereço encontrado:</p>
                        <p
                          class="transition-all group-hover:scale-125 group-hover:translate-y-[-10px] group-hover:font-bold"
                        >
                          Cadastrar novo endereço
                        </p>
                      </div>
                    </button>
                  </CommandEmpty>
                  <CommandGroup>
                    <div v-for="address in formatAddresses" v-bind:key="address.id">
                      <CommandItem
                        :key="address.value"
                        :value="address.value"
                        class="flex flex-col items-start"
                        @click="(e) => (addressTarget.value = e.currentTarget)"
                      >
                        <div class="flex gap-2 items-center">
                          <button
                            class="group hover:text-white hover:bg-info/90 transition-colors border-white border-2 py-1 px-2 rounded-md text-info/80"
                            @click="toggleCreateOrderDialog"
                          >
                            <div
                              class="flex items-start flex-col justify-start pointer-events-none select-none"
                            >
                              <p class="text-start">{{ address.value }}</p>
                              <p class="text-xs text-muted-foreground group-hover:text-white/60">
                                {{ address.city }}
                              </p>
                              <p
                                v-if="address.complement"
                                class="text-xs text-info/70 group-hover:text-white/60"
                              >
                                {{ address.complement }}
                              </p>
                              <p
                                v-if="address.referency"
                                class="text-xs text-info/70 group-hover:text-white/60"
                              >
                                {{ address.referency }}
                              </p>
                            </div>
                          </button>
                          <Separator orientation="vertical" class="h-8" />
                          <div class="flex gap-2 ml-auto">
                            <button
                              class="size-8 text-lg bg-info rounded-md text-white transition-colors hover:text-warning"
                              @click="handleEditAddress"
                            >
                              <i class="ri-edit-2-fill"></i>
                            </button>
                            <button
                              class="size-8 text-lg bg-info rounded-md text-white transition-colors hover:text-danger"
                              @click="
                                (event) => {
                                  toggleDialogConfirmDeleAddress();
                                }
                              "
                            >
                              <i class="ri-delete-bin-6-fill"></i>
                            </button>
                          </div>
                        </div>
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
        :open="isOpenCreateOrderDialog"
        :toggleDialog="toggleCreateOrderDialog"
        :id-cliente-address="clientIdAddress"
        :client-name="StringUtil.utf8Decode(client.nome)"
        :value="value"
        @update:dataTable="(date) => (updateDataTable = date)"
      />
      <DialogRegisterAddress
        :open="isOpenRegisterAddressDialog"
        :toggle-dialog="toggleDialogRegisterAddress"
        :id-client="client.id"
        :address-details="addressDetails"
        @update:data-table="fetchAddresses"
      />
      <DialogConfirmAddressDelete
        variant="danger"
        dialog-title="Excluir Endereço"
        dialog-description="Tem certeza que deseja excluir o endereço?"
        :open="isOpenConfirmDeleAddressDialog"
        :id-address="clientIdAddress"
        @delete:confirm="handleDeleteAddress"
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
