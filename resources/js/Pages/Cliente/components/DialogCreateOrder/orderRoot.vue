<script setup>
import { ref, markRaw, defineComponent, h, onMounted, watch } from 'vue';
import axios from 'axios';
import { useForwardPropsEmits } from "radix-vue";
import { Button } from '@/components/ui/button'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog'
import { Card, CardContent } from '@/components/ui/card'
import { Carousel, CarouselContent, CarouselItem, CarouselNext, CarouselPrevious } from '@/components/ui/carousel'
import { CommandItem } from '@/components/ui/command'
import { dateToDayMonthYearFormat, dateToISOFormat, formatMoney, utf8Decode } from '@/util'
import { toast } from 'vue-sonner'
import { cn } from '@/lib/utils'
import { dialogState } from '@/hooks/useToggleDialog'
import NumberField from './components/NumberField.vue';
import { payloadPedido } from '@/hooks/usePayloadPedido'
import Separator from '@/components/ui/separator/Separator.vue';
import SelectPayment from '@/components/orderComponents/SelectPayment.vue'
import ExchangeInput from '@/components/orderComponents/ExchangeInput.vue'
import DateTimePicker from '@/components/orderComponents/DateTimePicker.vue'
import DialogCreateOrderNote from '../DialogCreateOrderNote.vue';

const { isOpen, toggleDialog } = dialogState()

const [payload, setPayload] = payloadPedido()

const { toCurrency, toFloat } = formatMoney()

const props = defineProps({
    setTab: { type: Function, required: false },
    address: { type: Object, required: true },
    value: { type: String, required: true },
    commandOpen: { type: Boolean, required: true },
});


const emits = defineEmits(["update:modelValue", "update:commandOpen"]);

const createOrderData = ref()

const numberFieldProps = ref({
    min: 0,
    value: 0
})

const disabledButton = ref(true)


const forwarded = useForwardPropsEmits(props, emits);

const CustomDiv = (title, description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex flex-col' }, title, h('span', { class: 'text-xs opacity-80' }, description))
    }
})


const renderToast = (promise) => {
    toast.promise(promise, {
        loading: 'Aguarde...',

        success: (data) => {
            toggleDialog()
            return markRaw(CustomDiv('sucesso', `O pedido foi cadastrado com sucesso!`));
        },
        error: (data) => markRaw(CustomDiv('Error', data.response)),
    });
}

const whenDialogOpen = async () => {
    const { id } = props.address
    const url = `produtos/${id}`
    const responseOrder = await axios.get(url)
    const { data: orderData } = responseOrder

    const products = orderData[0].map(product => { return { ...product, nome: utf8Decode(product.nome) } }).filter(product => product.id != 3 && product.id != 334).sort();
    const responseDistributor = orderData[1];
    const responseAddress = orderData[2];
    const address = {
        ...responseAddress,
        "logradouro": utf8Decode(responseAddress.logradouro),
        "bairro": utf8Decode(responseAddress.bairro),
        "complemento": utf8Decode(responseAddress.complemento || ''),
        "cidade": utf8Decode(responseAddress.cidade),
        "referencia": utf8Decode(responseAddress.referencia || ''),
        "apelido": utf8Decode(responseAddress.apelido || ''),
        "observacao": utf8Decode(responseAddress.observacao || ''),
    }

    const distributor = {
        ...responseDistributor,
        nome: utf8Decode(responseDistributor.nome),
    }

    createOrderData.value = {
        products,
        distributor,
        address,
        distributorExpedient: orderData[6],
        distributorTaxes: orderData[4],
    }

    const { distributorTaxes: { taxaUnica: taxaEntrega }, distributor: { id: idDistribuidor, observacao }, address: { id: idEndereco } } = createOrderData.value

    setPayload({ ...payload.value, taxaEntrega, idDistribuidor, idEndereco, observacao })

}

const handleDialogOpen = () => {
    isOpen.value && whenDialogOpen()
    return isOpen.value
}

onMounted(() => whenDialogOpen())

const handleToggleOpenDialog = (op) => {
    !op && emits('update:commandOpen', false)
    toggleDialog()
}
const updateData = (rowIndex, columnId, value) => {
    const newData = columnId !== 'quantidade' ? [...createOrderData.value.products.map((row, index) => {
        if (index == rowIndex) {
            const oldRow = createOrderData.value.products[rowIndex]
            return {
                ...oldRow,
                [columnId]: [{ qtd: oldRow[columnId][0].qtd, val: toFloat(value) }]
            }
        }
        return row;
    })] : [...createOrderData.value.products.map((row, index) => {
        if (index == rowIndex) {
            const oldRow = createOrderData.value.products[rowIndex]
            return {
                ...oldRow,
                [columnId]: value
            }
        }
        return row;
    })]
    createOrderData.value = { ...createOrderData.value, products: newData }

    const itens = newData.map(product => {
        if (product.quantidade > 0) {
            const { id, preco, quantidade } = product
            return {
                idProduto: id,
                quantidade: quantidade,
                preco: preco[0].val,
                subtotal: quantidade * preco[0].val,
                precoAcertado: null,
            }
        }
        return null
    }).filter(x => x)

    const taxaEntrega = payload.value.taxaEntrega
    try {
        const totalProdutos = itens.map(product => product.subtotal).reduce((curr, prev) => curr + prev);
        const total = totalProdutos + taxaEntrega

        setPayload({ ...payload.value, totalProdutos, total, itens, origem: 4 })
    } catch (error) {
        disabledButton.value = true
        toast.error('Adicione ao menos um produto')
    }
}

const handlePayForm = (value) => setPayload({ ...payload.value, formaPagamento: value })

const handleExchange = ({ value }) => setPayload({ ...payload.value, trocoPara: parseFloat(value.split(' ')[1]) })
const handleScheduling = (date) => {
    if (date) {
        const { date: formattedDate, time } = dateToDayMonthYearFormat(date)

        const dataAgendada = formattedDate

        const horaInicio = time

        return setPayload({ ...payload.value, agendado: 1, dataAgendada, horaInicio })
    }
    return setPayload({ ...payload.value, agendado: 0, dataAgendada: '', horaInicio: '' })
}

const handleOrderNote = (value) => setPayload({ ...payload.value, obs: value })

watch(() => payload.value.itens, (newVal) => disabledButton.value = newVal.map(product => product.quantidade).reduce((curr, prev) => curr + prev) < 1 ? true : false)

const handleCallbackPedido = () => {
    disabledButton.value = true
    var url = "pedidos";
    const response = axios.post(url, payload.value)
    renderToast(response)
}

</script>

<template>
    <Dialog :open="handleDialogOpen()" @update:open="handleToggleOpenDialog">
        <DialogTrigger as-child>
            <CommandItem :key="address.value" :value="address.value" @select="(e) => e.preventDefault()"
                class="flex flex-col items-start">
                <Check :class="cn(
                    'mr-2 h-4 w-4',
                    value === address.value ? 'opacity-100' : 'opacity-0',
                )" />
                <span>{{ address.value }}</span>
                <span class="text-xs text-muted-foreground">{{ address.city }}</span>
                <span v-if="address.complement" class="text-xs text-info/70">{{
                    address.complement }}</span>
                <span v-if="address.referency" class="text-xs text-info/70">{{
                    address.referency }}</span>
            </CommandItem>
        </DialogTrigger>
        <DialogContent
            class="bg-info/60 backdrop-blur-sm sm:max-w-[600px] grid-rows-[auto_minmax(0,1fr)_auto] p-0 h-full max-h-full">
            <DialogHeader class="p-6 pb-0 text-white">
                <DialogTitle class="">Nossos Produtos</DialogTitle>
                <DialogDescription class="text-white/50 text-sm flex flex-col gap-3 justify-between items-center">
                    Informações do pedido
                    <div class="flex justify-between items-center w-full">
                        <div class="flex flex-col">subtotal <p>{{ toCurrency(payload.totalProdutos) }}</p>
                        </div>
                        <Separator orientation="vertical" class="h-[32px] bg-white/20"></Separator>
                        <div class="flex flex-col">taxa de entrega <p>{{ toCurrency(payload.taxaEntrega) }}</p>
                        </div>
                        <Separator orientation="vertical" class="h-[32px] bg-white/20"></Separator>
                        <div class="flex flex-col">total <p>{{ toCurrency(payload.total) }}</p>
                        </div>
                    </div>
                </DialogDescription>
            </DialogHeader>
            <Carousel v-slot="{ canScrollNext }" class="relative w-full max-w-xs sm:max-w-full">
                <DialogCreateOrderNote @callback:order-note="handleOrderNote" :order-note="payload.obs"
                    class="top-[6%] right-[6%]">
                </DialogCreateOrderNote>
                <CarouselContent class="bg-transparent relative">
                    <CarouselItem v-for="(product, index) in createOrderData.products" :key="product.id"
                        class="md:basis-1/2">
                        <div class="p-1">
                            <Card class="border-none bg-transparent">
                                <CardContent class="flex aspect-square items-center justify-center p-6 gap-3 flex-col">
                                    <div class="flex flex-col items-center justify-center">
                                        <h3 class="text-white/50 text-sm">{{ product.nome }}</h3>
                                        <h2 class="text-white text-base mb-4">{{
                                            toCurrency(parseFloat(product.preco[0].val)) }}
                                        </h2>
                                    </div>
                                    <img :src=product.img class="h-[227px]" />
                                    <div>
                                        <NumberField v-bind="numberFieldProps"
                                            @update:model-value="(val) => updateData(index, 'quantidade', val)">
                                        </NumberField>
                                    </div>
                                </CardContent>
                            </Card>
                        </div>
                    </CarouselItem>
                </CarouselContent>
                <CarouselPrevious class="left-[1rem] bg-transparent text-white !border-input/30 ring-0" />
                <CarouselNext v-if="canScrollNext"
                    class="right-[1rem] bg-transparent text-white !border-input/30 ring-0" />
            </Carousel>
            <div class="flex flex-wrap gap-2 p-2 sm:h-14 justify-center mb-3">
                <SelectPayment @update:payment-form="handlePayForm" :default="payload.formaPagamento" />
                <Separator orientation="vertical" class="" />
                <ExchangeInput @update:exchange="handleExchange" :value="payload.trocoPara"
                    class="[&_input]:bg-white [&_label]:bg-transparent [&_label]:top-[-1rem] [&_label]:left-0 [&_label]:!text-input" />
                <Separator orientation="vertical" class="hidden sm:block" />
                <DateTimePicker @update:scheduling="handleScheduling"
                    :default:scheduling="dateToISOFormat(`${payload.dataAgendada} ${payload.horaInicio}`)" />
            </div>
            <DialogFooter>

                <Button :disabled="disabledButton" type="submit"
                    class="border-none w-full rounded-none px-4 py-2 text-base font-semibold bg-info/80 hover:bg-info/100 transition-all disabled:bg-info/20 disabled:hover:bg-info/60 disabled:cursor-not-allowed "
                    @click="handleCallbackPedido">
                    <span> Realizar Pedido </span>
                </Button>


            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
