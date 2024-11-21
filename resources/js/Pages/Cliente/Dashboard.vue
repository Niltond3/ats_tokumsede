<script setup>
import { onMounted, ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/ClienteAuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { usePage, } from '@inertiajs/vue3';
import { utf8Decode } from '@/util';
import { Button } from '@/components/ui/button'
import {
    Command,
    CommandEmpty,
    CommandGroup,
    CommandInput,
    CommandList
} from '@/components/ui/command'
import {
    Popover,
    PopoverContent,
    PopoverTrigger,
} from '@/components/ui/popover'
import { cn } from '@/lib/utils'
import { Check, ChevronsUpDown } from 'lucide-vue-next'
import { DialogCreateOrder } from './components/DialogCreateOrder';
import { TablePedidos } from '../clientes/DataTablePedidos';

const page = usePage()


console.log(page.props.auth)
const client = page.props.auth.user

const welcome = ref(client.sexo == 1 ? 'Bem vindo senhor ' + utf8Decode(client.nome) : 'Bem vinda senhora ' + utf8Decode(client.nome))


const addresses = ref([])


onMounted(async () => {
    const url = `enderecos/${client.id}`
    const request = await axios.get(url)

    const formatAddresses = request.data[1].map((address) => {
        const { logradouro, numero, bairro, cidade, estado, complemento, referencia, id } = address

        return {
            id,
            value: `${utf8Decode(logradouro)}, ${numero} - ${utf8Decode(bairro)}`,
            city: `${utf8Decode(cidade)} - ${utf8Decode(estado)}`,
            complement: utf8Decode(complemento) || '',
            referency: utf8Decode(referencia) || ''
        }
    })
    addresses.value = formatAddresses
    console.log(request)
})

const open = ref(false)
const value = ref('')
</script>

<template>

    <Head title="Cliente Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl leading-tight">{{ welcome }}</h2>
            <h4>selecione seu endereço para realizar um novo pedido</h4>
            <div class="w-full relative ">
                <div>
                    <Popover v-model:open="open">
                        <PopoverTrigger as-child>
                            <Button variant="outline" role="combobox" :aria-expanded="open"
                                class="w-full justify-between rounded-md h-9 text-info/80 py-1 pl-9 pr-3 hover:text-info transition-colors">
                                {{ value ? addresses.find((address) => address.value === value)?.value :
                                    "Endereços..." }}
                                <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                            </Button>
                        </PopoverTrigger>
                        <PopoverContent class="w-[285px] p-0 border-none">
                            <Command v-model="value">
                                <CommandInput placeholder="Endereços..." class="!ring-0 border-0 text-info/80" />
                                <CommandList>
                                    <CommandEmpty class="p-2">Nenhum endereço encontrado.</CommandEmpty>
                                    <CommandGroup>
                                        <DialogCreateOrder v-for="address in addresses"
                                            class="flex flex-col items-start" :address="address" :value="value"
                                            @update:command-open="() => open = false">
                                        </DialogCreateOrder>
                                    </CommandGroup>
                                </CommandList>
                            </Command>
                        </PopoverContent>
                    </Popover>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 ">
                <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg">
                    <TablePedidos ajustClass="min-[768px]:!top-[90px]"></TablePedidos>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
