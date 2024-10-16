<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3';
import { useClientFormat } from "@/useClientFormat";
import { RiLoginBoxLine as LoginIcon } from "vue-remix-icons";
import { CheckIcon, CircleIcon, DotIcon } from '@radix-icons/vue'
import { useForm } from 'vee-validate'
import validator from 'validator'
import { toTypedSchema } from '@vee-validate/zod'
import GuestLayout from '@/Layouts/GuestLayout.vue';
import * as z from 'zod'
import { Form } from '@/components/ui/form'
import { Stepper, StepperDescription, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper'
import Button from '@/components/Button.vue';
import PersonalDetails from '../components/personalDetails.vue';
import axios from 'axios';
import AdressDetails from '../components/adressDetails.vue';
import ConfirmClient from '../components/confirmClient.vue';


defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const formSchema = [
    z.object({
        nome: z.string({ required_error: 'Informar seu nome é obrigatório' }).min(4, { message: 'Nome muito curto' }),
        telefone: z.string({ required_error: 'Número de telefone obrigatório' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
        senha: z.string({ required_error: 'Senha obrigatória' }).refine(value => validator.isStrongPassword(value,
            { minLength: 5, minLowercase: 1, minUppercase: 1, minNumbers: 1, minSymbols: 0, returnScore: false, pointsPerUnique: 1, pointsPerRepeat: 0.5, pointsForContainingLower: 10, pointsForContainingUpper: 10, pointsForContainingNumber: 10, pointsForContainingSymbol: 10 }
        ), {
            message: 'Senha fraca: precisa conter 5 caractéres, letra maiúscula e minúscula um número.'
        }),
        confirmSenha: z.string({ required_error: 'Confirme sua senha' }).refine(value => validator.isStrongPassword(value,
            { minLength: 5, minLowercase: 1, minUppercase: 1, minNumbers: 1, minSymbols: 0, returnScore: false, pointsPerUnique: 1, pointsPerRepeat: 0.5, pointsForContainingLower: 10, pointsForContainingUpper: 10, pointsForContainingNumber: 10, pointsForContainingSymbol: 10 }
        ), {
            message: 'Senha fraca: precisa conter 5 caractéres, letra maiúscula e minúscula um número.'
        }),
        sexo: z.enum(['1', '2']).nullable().optional(),
        dataNascimento: z.string().nullable().optional(),
        tipoPessoa: z.string().nullable().optional(),
        email: z.string({ required_error: "e-mail obrigatório" }).refine(validator.isEmail, { message: 'e-mail inválido' }),
    }).superRefine(({ confirmSenha, senha }, ctx) => {
        if (confirmSenha !== senha) {
            ctx.addIssue({
                code: "custom",
                message: "As senhas devem ser iguais",
                path: ['confirmSenha']
            });
        }
    }),
    z.object({
        search: z.string().nullable().optional(),
        cep: z.string().nullable().optional(),
        cidade: z.string(),
        estado: z.string(),
        apelido: z.string().nullable().optional(),
        logradouro: z.string(),
        numero: z.string(),
        bairro: z.string(),
        complemento: z.string().nullable().optional(),
        referencia: z.string().nullable().optional(),
        observacao: z.string().nullable().optional(),
    }),
    z.object({
        validateInformations: z.boolean().refine(
            (value) => value === true,
            {
                message: 'Confirme os dados para prosseguir',
            },
        ),
    }),
];

const stepIndex = ref(1)

const steps = [
    {
        step: 1,
        title: 'Seus dados',
    },
    {
        step: 2,
        title: 'Seu endereço',
    },
    {
        step: 3,
        title: 'Revisão',
    },
]

const { getTipoPessoaPayload } = useClientFormat();

/*

nome: $("#formCliente_Nome").val(),
sexo: $("#formCliente_Sexo").val(),
dataNascimento: $("#formCliente_DataNascimento").val(),
tipoPessoa: $("#formCliente_TipoPessoa").val(),
cnpj: $("#formCliente_Cnpj").val().replace(/\D+/g, ''),
cpf: $("#formCliente_Cpf").val().replace(/\D+/g, ''),
logradouro: $("#formCliente_Logradouro").val(),
numero: $("#formCliente_Numero").val(),
bairro: $("#formCliente_Bairro").val(),
complemento: $("#formCliente_Complemento").val(),
cep: $("#formCliente_Cep").val(),
cidade: $("#formCliente_Cidade").val(),
estado: $("#formCliente_Estado").val(),
referencia: $("#formCliente_Referencia").val(),
apelido: $("#formCliente_Apelido").val(),
observacao: $("#formCliente_Observacao").val(),
telefone: $("#formCliente_Telefone").val(),
outrosContatos: $("#formCliente_Outros").val(),
email: $("#formCliente_Email").val(),
senha: $("#formCliente_Senha").val()

*/

const onSubmit = (values) => {
    const phoneRaw = values.telefone.replace(/\D/g, '')

    const ddd = phoneRaw.slice(0, 2);
    const telefone = phoneRaw.slice(2);

    const { tipoPessoa, documento } = getTipoPessoaPayload(values.tipoPessoa);


    const payload = {
        nome: values.nome,
        sexo: values.nome,
        dataNascimento: values.nome,
        tipoPessoa,
        cpf: documento['CPF'],
        cnpj: documento['CNPJ'],
        logradouro: values.logradouro,
        numero: values.numero,
        bairro: values.bairro,
        complemento: values.complemento,
        cep: values.cep,
        cidade: values.cidade,
        estado: values.estado,
        referencia: values.referencia,
        apelido: values.apelido,
        observacao: values.observacao,
        telefone: values.telefone,
        email: values.email,
        senha: values.senha,
    }
    console.log(payload)

    // axios.post(route('cliente.register'), payload)
    //     .then((response) => {
    //         console.log('response')
    //         console.log(response)
    //         location.reload();
    //     }).catch((error) => {
    //         console.error(error);
    //         resetField('senha')
    //     })

}


const handleUpdateDatePicker = (dataValue, setFieldValue) => {
    if (!dataValue) return
    setFieldValue('dataNascimento', dataValue.toString())
}


const handleUpdateAddress = (addressValue, setValues) => setValues({
    'search': addressValue.formattedAddress && addressValue.formattedAddress,
    'cep': addressValue.cep && addressValue.cep,
    'cidade': addressValue.cidade && addressValue.cidade,
    'estado': addressValue.estado && addressValue.estado,
    'logradouro': addressValue.logradouro && addressValue.logradouro,
    'numero': addressValue.numero && addressValue.numero,
    'bairro': addressValue.bairro && addressValue.bairro
})

</script>

<template>
    <GuestLayout>

        <Head title="Registre-se" />

        <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
        </div>

        <Form v-slot="{ meta, validate, setFieldValue, values, setValues }" as="" keep-values
            :validation-schema="toTypedSchema(formSchema[stepIndex - 1])">

            <Stepper v-slot="{ isNextDisabled, isPrevDisabled, nextStep, prevStep }" v-model="stepIndex"
                class="block w-full">
                <form form @submit="(event) => {
                    event.preventDefault()
                    validate()

                    if (stepIndex === steps.length && meta.valid) onSubmit(values)
                }">
                    <div class="flex w-full flex-start gap-2">
                        <StepperItem v-for="step in steps" :key="step.step" v-slot="{ state }"
                            class="relative flex w-full flex-col items-center justify-center" :step="step.step">
                            <StepperSeparator v-if="step.step !== steps[steps.length - 1].step"
                                class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-primary" />

                            <StepperTrigger as-child>
                                <Button :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                                    size="icon" class="z-10 rounded-full shrink-0"
                                    :class="[state === 'active' && 'ring-2 ring-ring ring-offset-2 ring-offset-background']"
                                    :disabled="state !== 'completed' && !meta.valid">
                                    <CheckIcon v-if="state === 'completed'" class="size-5" />
                                    <CircleIcon v-if="state === 'active'" />
                                    <DotIcon v-if="state === 'inactive'" />
                                </Button>
                            </StepperTrigger>

                            <div class="mt-5 flex flex-col items-center text-center">
                                <StepperTitle :class="[state === 'active' && 'text-primary']"
                                    class="text-sm font-semibold transition lg:text-base">
                                    {{ step.title }}
                                </StepperTitle>
                                <StepperDescription :class="[state === 'active' && 'text-primary']"
                                    class="sr-only text-xs text-muted-foreground transition md:not-sr-only lg:text-sm">
                                    {{ step.description }}
                                </StepperDescription>
                            </div>
                        </StepperItem>
                    </div>
                    <div>
                        <PersonalDetails v-if="stepIndex === 1" :values="values"
                            @update:birth-date-picker="(dataValue) => handleUpdateDatePicker(dataValue, setFieldValue)" />

                        <AdressDetails v-if="stepIndex === 2"
                            @update:address-value="(addressValue) => handleUpdateAddress(addressValue, setValues)" />

                        <ConfirmClient v-if="stepIndex === 3" :values="values" />

                    </div>
                    <div class="flex items-center justify-between mt-4">
                        <Button :disabled="isPrevDisabled" variant="outline" size="sm" @click="prevStep()">
                            Back
                        </Button>
                        <div class="flex items-center gap-3">
                            <Button v-if="stepIndex !== 3" :type="meta.valid ? 'button' : 'submit'"
                                :disabled="isNextDisabled" size="sm" @click="meta.valid && nextStep()">
                                Next
                            </Button>
                            <Button v-if="stepIndex === 3" size="sm" type="submit">
                                Submit
                            </Button>
                        </div>
                    </div>
                </form>
            </Stepper>
        </Form>
    </GuestLayout>
</template>
