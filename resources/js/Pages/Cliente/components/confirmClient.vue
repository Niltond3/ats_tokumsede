<script setup>
import { useClientFormat } from '@/useClientFormat';
import { Checkbox } from '@/components/ui/checkbox'
import { FormLabel, FormControl, FormMessage, FormItem, FormField, FormDescription } from '@/components/ui/form'
import { Separator } from '@/components/ui/separator'


const { values } = defineProps({ values: Object })

const emit = defineEmits(['update:CheckboxToggle'])

const { getSexo, getTipoPessoaPayload } = useClientFormat();

const { CNPJ } = getTipoPessoaPayload(values.tipoPessoa).documento;

const newDetailsValues = JSON.parse(JSON.stringify({
    nome: values.nome,
    telefone: values.telefone,
    senha: values.senha,
    sexo: getSexo.mobile[values.sexo],
    email: values.email,
}))

const newAddressValues = JSON.parse(JSON.stringify({
    cep: values.cep,
    cidade: values.cidade,
    estado: values.estado,
    apelido: values.apelido,
    logradouro: values.logradouro,
    numero: values.numero,
    bairro: values.bairro,
    complemento: values.complemento,
    referencia: values.referencia,
    observacao: values.observacao,
}))


const interatedValues = !values.tipoPessoa ? newDetailsValues : CNPJ ? { ...newDetailsValues, CNPJ: values.tipoPessoa } : { ...newDetailsValues, CPF: values.tipoPessoa }


const handleToggle = (val) => {
    console.log(val)
    ////////////////////
    console.log(values.validateInformations)
    emit('update:CheckboxToggle', val)
}
console.log(interatedValues)
</script>

<template>
    <section>
        <div id="v-for-object" class="gap-3 flex flex-col p-4 text-sm capitalize  ">
            <div v-for="(value, key) in interatedValues"
                class="flex items-center gap-4 cursor-pointer opacity-70 hover:opacity-100">
                <span class="flex h-2 w-2 rounded-full bg-sky-500 "></span>
                <span class="text-slate-700 font-semibold"> {{ key }}:</span> <span>{{ value }}</span>
            </div>
            <Separator label="Endereço" />
            <div v-for="(value, key) in newAddressValues"
                class="flex items-center gap-4 cursor-pointer opacity-70 hover:opacity-100">
                <span class="flex h-2 w-2 rounded-full bg-sky-500 " />
                <span class="text-slate-700 font-semibold"> {{ key }}:</span> <span>{{ value }}</span>
            </div>
        </div>
        <FormField v-slot="{ value, handleChange }" type="checkbox" name="validateInformations">
            <FormItem class="flex flex-row items-start gap-x-3 space-y-0 rounded-md border p-4">
                <FormControl>
                    <Checkbox :checked="value" @update:checked="(val) => {
                        handleChange(val)
                        handleToggle(val)
                    }" />
                </FormControl>
                <div class="space-y-1 leading-none">
                    <FormLabel>Todos os dados estão corretos!</FormLabel>
                    <FormDescription>
                        Após confirmar você poderá alterar todos os seus dados no menú configurações ou entrando em
                        contato com nossos atendentes pelo <a href="/examples/forms">whatsapp</a>.
                    </FormDescription>
                    <FormMessage />
                </div>
            </FormItem>
        </FormField>
    </section>
</template>
