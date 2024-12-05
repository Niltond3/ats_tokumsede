<script setup>
import { ref, markRaw, defineComponent, h, onMounted, watch, reactive } from 'vue';
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
    DialogFooter
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
import { Check } from 'lucide-vue-next'
import renderToast from '@/components/renderPromiseToast';


const { isOpen, toggleDialog } = dialogState()

const [payload, setPayload] = payloadPedido()

const { toCurrency, toFloat } = formatMoney()

const props = defineProps({
    setTab: { type: Function, required: false },
    address: { type: Object, required: true },
    value: { type: String, required: true },
});

const emits = defineEmits(["update:modelValue", "update:commandOpen"]);

const readbleOrderData = reactive({ value: {} })

const numberFieldProps = ref({
    min: 0,
    value: 0
})

const disabledButton = ref(true)

const interableProducts = ref([])

const forwarded = useForwardPropsEmits(props, emits);

const whenDialogOpen = async () => {
    const { id } = props.address
    const url = `produtos/${id}`
    const responseOrder = await axios.get(url)
    console.log(responseOrder)
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

    const values = {
        products,
        distributor,
        address,
        distributorExpedient: orderData[6],
        distributorTaxes: orderData[4],
    }
    console.log(products)
    readbleOrderData.value = values

    const { distributorTaxes: { taxaUnica: taxaEntrega }, distributor: { id: idDistribuidor, observacao }, address: { id: idEndereco } } = readbleOrderData.value


    setPayload({ ...payload.value, taxaEntrega, idDistribuidor, idEndereco, observacao })

}

watch(() => isOpen.value, () => {
    whenDialogOpen()
})


watch(() => readbleOrderData.value.products, (newValue) => {
    console.log(newValue)
    interableProducts.value = newValue
})


const handleToggleOpenDialog = (op) => {
    !op && emits('update:commandOpen', false)
    toggleDialog()
}
const updateData = (rowIndex, columnId, value) => {
    const newData = columnId !== 'quantidade' ? [...readbleOrderData.value.products.map((row, index) => {
        if (index == rowIndex) {
            const oldRow = readbleOrderData.value.products[rowIndex]
            return {
                ...oldRow,
                [columnId]: [{ qtd: oldRow[columnId][0].qtd, val: toFloat(value) }]
            }
        }
        return row;
    })] : [...readbleOrderData.value.products.map((row, index) => {
        if (index == rowIndex) {
            const oldRow = readbleOrderData.value.products[rowIndex]
            return {
                ...oldRow,
                [columnId]: value
            }
        }
        return row;
    })]
    readbleOrderData.value = { ...readbleOrderData.value, products: newData }

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
    const promise = axios.post(url, payload.value)
    toggleDialog()
    renderToast(promise, 'Cadastrando pedido', 'o pedido foi cadastrado com sucesso', () => toggleDialog())
}

//class="flex flex-col items-start"

</script>

<template>
    <div>
        <Dialog v-bind="forwarded" :open="isOpen" @update:open="handleToggleOpenDialog">
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
                        <Suspense>
                            <template #default>
                                <CarouselItem v-for="(product, index) in interableProducts" :key="product.id"
                                    class="md:basis-1/2">
                                    <div class="p-1">
                                        <Card class="border-none bg-transparent">
                                            <CardContent
                                                class="flex aspect-square items-center justify-center p-6 gap-3 flex-col">
                                                <div class="flex flex-col items-center justify-center">
                                                    <h3 class="text-white/50 text-sm">{{ product.nome }}</h3>
                                                    <h2 class="text-white text-base mb-4">{{
                                                        toCurrency(parseFloat(product.preco[0].val)) }}
                                                    </h2>
                                                </div>
                                                <img :src="`public/images/uploads/${product.img}`" class="h-[227px]" />
                                                <div>
                                                    <NumberField v-bind="numberFieldProps"
                                                        @update:model-value="(val) => updateData(index, 'quantidade', val)">
                                                    </NumberField>
                                                </div>
                                            </CardContent>
                                        </Card>
                                    </div>
                                </CarouselItem>
                            </template>
                            <template #fallback>
                                carregando...
                            </template>
                        </Suspense>
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
    </div>
</template>
