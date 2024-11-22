<script setup>
import { usePage } from '@inertiajs/vue3';
import { getClientFormat } from '@/Pages/clientes/utils';
import { RiEyeFill as EyeIcon, RiEyeCloseFill as EyeOffIcon } from 'vue-remix-icons';
import { Checkbox } from '@/components/ui/checkbox'
import { FormLabel, FormControl, FormMessage, FormItem, FormField, FormDescription } from '@/components/ui/form'
import { Separator } from '@/components/ui/separator'
import { Toggle } from '@/components/ui/toggle'
import { toast } from 'vue-sonner';
import { isEmptyObject } from 'jquery';

const page = usePage()
const { tipoAdministrador } = page.props.auth.user


const { values } = defineProps({ values: Object })

const emit = defineEmits(['update:CheckboxToggle'])

const { getSexo, getTipoPessoaPayload } = getClientFormat();

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

function handleCopyClient() {
    const payload = { ...newDetailsValues, ...newAddressValues }
    const clipboard = `
    ${!isEmptyObject(newDetailsValues) ? '------------- detalhes -------------' : ''}
    ${payload.nome ? 'Nome: ' + payload.nome : ''}
    ${payload.telefone ? 'Telefone: ' + payload.telefone : ''}
    ${payload.senha ? 'Senha: ' + payload.senha : ''}
    ${payload.sexo ? 'Sexo: ' + payload.sexo : ''}
    ${payload.email ? 'Email: ' + payload.email : ''}
    ------------- endereço -------------
    ${payload.cep ? 'CEP: ' + payload.cep : ''}
    Cidade: ${payload.cidade}
    ${payload.estado !== null ? 'Estado: ' + payload.estado : ''}
    ${payload.apelido ? 'Apelido: ' + payload.apelido : ''}
    Logradouro: ${payload.logradouro}
    Número: ${payload.numero}
    Bairro: ${payload.bairro}
    ${payload.complemento ? 'Complemento: ' + payload.complemento : ''}
    ${payload.referencia ? 'Referência: ' + payload.referencia : ''}
    ${payload.observacao ? 'Observação: ' + payload.observacao : ''}
    `.replace(/(^[ \t]*\n)/gm, "")

    navigator.clipboard.writeText(clipboard)
    toast.info('Copiado para a área de transferência')
}

</script>

<template>
    <section>
        <div id="v-for-object" class="relative gap-3 flex flex-col p-4 text-sm capitalize  ">
            <button class="group absolute right-0 -top-1" @click="handleCopyClient"><i
                    class="ri-file-copy-fill opacity-75 group-hover:opacity-100 transition-all text-xl text-info "></i></button>
            <div v-for="(value, key) in interatedValues"
                class="flex items-center gap-4 cursor-pointer opacity-70 hover:opacity-100 ">
                <span class="flex h-2 w-2 rounded-full bg-sky-500 " />
                <Toggle v-if="key === 'senha'" size="sm" aria-label="Toggle" class="gap-4 group p-0 h-auto">
                    <span class="text-slate-700 font-semibold"> {{ key }}:</span> <span
                        class="group-data-[state=off]:secure">{{ value
                        }}</span>
                    <EyeOffIcon class="w-4 h-4 group-data-[state=on]:block group-data-[state=off]:hidden" />
                    <EyeIcon class="w-4 h-4 block group-data-[state=on]:hidden group-data-[state=off]:block" />
                </Toggle>

                <div v-else="key !== 'senha'">
                    <span class="text-slate-700 font-semibold"> {{ key }}:</span> <span>{{ value }}</span>
                </div>

            </div>
            <Separator label="Endereço" />
            <div v-for="(value, key) in newAddressValues"
                class="flex items-center gap-4 cursor-pointer opacity-70 hover:opacity-100">
                <span class="flex h-2 w-2 rounded-full bg-sky-500 " />
                <span class="text-slate-700 font-semibold"> {{ key }}:</span> <span>{{ value }}</span>
            </div>
        </div>
        <FormField v-slot="{ value, handleChange }" type="checkbox" name="validateInformations">
            <FormItem class="flex flex-row items-start gap-x-3 space-y-0 rounded-md border p-4  border-input">
                <FormControl>
                    <Checkbox :checked="value" @update:checked="handleChange"
                        class="data-[state=checked]:bg-info border-info" />
                </FormControl>
                <div class="space-y-1 leading-none">
                    <FormLabel class="font-semibold text-info">Todos os dados estão corretos!</FormLabel>
                    <FormDescription>
                        Após confirmar você poderá alterar todos os dados no menu configurações <span
                            v-if="tipoAdministrador !== 'Administrador'"> ou entrando em contato com nossos atendentes
                            pelo <a href="/examples/forms">whatsapp</a></span>.
                    </FormDescription>
                    <FormMessage />
                </div>
            </FormItem>
        </FormField>
    </section>
</template>
