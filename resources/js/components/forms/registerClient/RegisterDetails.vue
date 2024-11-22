<script setup>
import { ref, markRaw, defineComponent, h, onMounted, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { Stepper, StepperItem, StepperSeparator, StepperTitle, StepperTrigger } from '@/components/ui/stepper'
import { Form } from '@/components/ui/form'
import Button from '@/components/Button.vue';
import PersonalDetails from './components/personalDetails.vue';
import ConfirmClient from './components/confirmClient.vue';
import * as z from 'zod'
import validator from 'validator'
import { toTypedSchema } from '@vee-validate/zod'
import { User, Check } from 'lucide-vue-next'
import { getClientFormat } from "@/Pages/clientes/utils";
import { toast } from 'vue-sonner';

const props = defineProps({
    clientDetails: { type: Object, required: false },
})

const emit = defineEmits(['create:success'])

const page = usePage()

const { tipoAdministrador } = page.props.auth.user

const disabledButton = ref(false)

const formSchema = [
    z.object({
        nome: z.string({ required_error: 'Informar seu nome é obrigatório' }).min(4, { message: 'Nome muito curto' }),
        telefone: z.string({ required_error: 'Número de telefone obrigatório' }).refine(validator.isMobilePhone, { message: 'Número de telefone inválido' }),
        senha: z.string().refine(value => validator.isStrongPassword(value,
            { minLength: 5, minLowercase: 1, minUppercase: 1, minNumbers: 1, minSymbols: 0, returnScore: false, pointsPerUnique: 1, pointsPerRepeat: 0.5, pointsForContainingLower: 10, pointsForContainingUpper: 10, pointsForContainingNumber: 10, pointsForContainingSymbol: 10 }
        ), {
            message: 'Senha fraca: precisa conter 5 caractéres, letra maiúscula e minúscula um número.'
        }).nullable().optional(),
        confirmSenha: z.string().refine(value => validator.isStrongPassword(value,
            { minLength: 5, minLowercase: 1, minUppercase: 1, minNumbers: 1, minSymbols: 0, returnScore: false, pointsPerUnique: 1, pointsPerRepeat: 0.5, pointsForContainingLower: 10, pointsForContainingUpper: 10, pointsForContainingNumber: 10, pointsForContainingSymbol: 10 }
        ), {
            message: 'Senha fraca: precisa conter 5 caractéres, letra maiúscula e minúscula um número.'
        }).nullable().optional(),
        sexo: z.enum([undefined, '1', '2']).nullable().optional(),
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
        title: 'Detalhes',
        icon: User
    },
    {
        step: 2,
        title: 'Revisão',
        icon: Check
    },
]

const { getTipoPessoaPayload } = getClientFormat();

const CustomDiv = (title, description) => defineComponent({
    setup() {
        return () => h('div', { class: 'flex flex-col' }, title, h('span', { class: 'text-xs opacity-80' }, description))
    }
})


const renderToast = (promise) => {
    disabledButton.value = true
    toast.promise(promise, {
        loading: 'Aguarde...',

        success: (data) => {
            emit('create:success')
            return markRaw(CustomDiv('sucesso', `O Cliente foi alterado com sucesso!`));
        },
        error: (data) => markRaw(CustomDiv('Error', data.response)),
    });
}

const onSubmit = (values) => {

    const { tipoPessoa, documento } = getTipoPessoaPayload(values.tipoPessoa);
    const { id } = props.clientDetails
    const payload = {
        nome: values.nome,
        sexo: values.sexo,
        dataNascimento: values.dataNascimento,
        tipoPessoa,
        cpf: documento['CPF'],
        cnpj: documento['CNPJ'],
        telefone: values.telefone,
        email: values.email,
        senha: values.senha,
    }

    const response = tipoAdministrador === 'Administrador' ? axios.put(`clientes/${id}`, payload) : axios.put(route('cliente.register'), payload);

    renderToast(response)

}


const handleUpdateDatePicker = (dataValue, setFieldValue) => {
    console.log(dataValue)
    if (!dataValue) return
    setFieldValue('dataNascimento', dataValue.toString())
}

const handleUpdatePassword = (password, setValues) => setValues({
    'senha': password,
    'confirmSenha': password
})

</script>

<template>
    <Form v-slot="{ meta, validate, setFieldValue, values, setValues }" as="" keep-values
        :validation-schema="toTypedSchema(formSchema[stepIndex - 1])" :initial-values="clientDetails">

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
                            class="absolute left-[calc(50%+20px)] right-[calc(-50%+10px)] top-5 block h-0.5 shrink-0 rounded-full bg-muted group-data-[state=completed]:bg-info" />

                        <StepperTrigger as-child>
                            <Button :variant="state === 'completed' || state === 'active' ? 'default' : 'outline'"
                                size="icon" class="z-10 rounded-full shrink-0 transition-all"
                                :class="[state === 'active' && 'hover:bg-info/90 bg-info ring-2 ring-offset-2 ring-offset-info']"
                                :disabled="state !== 'completed' && !meta.valid">

                                <component :is="step.icon" class="w-4 h-4" />
                            </Button>
                        </StepperTrigger>

                        <div class="pb-5 flex flex-col items-center text-center">
                            <StepperTitle :class="[state === 'active' && '!text-info translate-y-3 scale-125']"
                                class="text-slate-300 !text-[12px] font-semibold transition-all lg:text-base ">
                                {{ step.title }}
                            </StepperTitle>
                        </div>
                    </StepperItem>
                </div>
                <div>
                    <PersonalDetails v-if="stepIndex === 1" :values="values"
                        @update:birth-date-picker="(dataValue) => handleUpdateDatePicker(dataValue, setFieldValue)"
                        @update:generate-password="(password) => handleUpdatePassword(password, setValues)" />

                    <ConfirmClient v-if="stepIndex === 2" :values="values" />

                </div>
                <div class="flex items-center justify-between mt-4">
                    <Button class="disabled:cursor-not-allowed text-info/75 hover:text-info transition-colors"
                        :disabled="isPrevDisabled" variant="outline" size="sm" @click="prevStep()">
                        Voltar
                    </Button>
                    <div class="flex items-center gap-3">
                        <Button v-if="stepIndex !== 2" :type="meta.valid ? 'button' : 'submit'"
                            :disabled="isNextDisabled" size="sm" @click="meta.valid && nextStep()">
                            Próximo
                        </Button>
                        <Button v-if="stepIndex === 2" size="sm" type="submit" :disabled="disabledButton"
                            class="disabled:cursor-not-allowed">
                            Cadastrar
                        </Button>
                    </div>
                </div>
            </form>
        </Stepper>
    </Form>
</template>
